<?php

namespace App\Jobs;

use App\Models\Beneficiary;
use App\Models\User;
use App\Models\Payment;
use App\Notifications\ExportReadyNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use ZipArchive;

class GenerateBeneficiaryExtractPdfsZip implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $beneficiaryIds;
    protected $userId;

    /**
     * Create a new job instance.
     *
     * @param array $beneficiaryIds
     * @param int $userId
     */
    public function __construct(array $beneficiaryIds, int $userId)
    {
        $this->beneficiaryIds = $beneficiaryIds;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $user = User::find($this->userId);
        if (!$user) {
            Log::error('User not found in GenerateBeneficiaryExtractPdfsZip job.', ['userId' => $this->userId]);
            return;
        }

        try {
            $beneficiaries = Beneficiary::find($this->beneficiaryIds);

            if ($beneficiaries->isEmpty()) {
                Log::warning('No beneficiaries found for the given IDs in GenerateBeneficiaryExtractPdfsZip job.', ['ids' => $this->beneficiaryIds]);
                return;
            }

            $pdfFiles = $this->generatePDFs($beneficiaries);
            $zipPath = $this->createZipFile($pdfFiles);
            $this->cleanupPDFs($pdfFiles);

            $zipUrl = asset('storage/exports/' . basename($zipPath));
            Log::info("ZIP file created for user {$user->id} at: {$zipUrl}");

            // Notificar al usuario que el archivo está listo
            $user->notify(new ExportReadyNotification($zipUrl, count($beneficiaries)));
        } catch (\Exception $e) {
            Log::error('Failed to generate beneficiary PDF zip file for user ' . $user->id, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // Opcional: Notificar al usuario sobre el fallo
            // $user->notify(new ExportFailedNotification());
        }
    }

    private function generatePDFs($beneficiaries)
    {
        $exportPath = storage_path('app/public/exports');
        if (!File::isDirectory($exportPath)) {
            File::makeDirectory($exportPath, 0755, true, true);
        }

        return $beneficiaries->map(function ($beneficiary) use ($exportPath) {

            // Logic synchronized from BeneficiaryController::pdfExtract

            // Precargamos las relaciones para evitar consultas N+1
            $noLegacy = $beneficiary->vouchers()
                ->with('payments')
                ->where(function ($query) {
                    $query->whereNull('obs_pago')
                        ->orWhere('obs_pago', '')
                        ->orWhere('obs_pago', '!=', 'LEGACY 22/24');
                })->orderBy('numpago', 'ASC')->get();

            $legacy = $beneficiary->vouchers()
                ->with('payments')
                ->where('obs_pago', 'LEGACY 22/24')
                ->orderBy('fecha_pago')
                ->orderBy('numpago')
                ->get();

            // Precalculamos los totales para evitar consultas repetitivas
            $devengadoInt = \App\Models\Earn::where('idepro', $beneficiary->idepro)->sum('interes');
            $devengadoSeg = \App\Models\Earn::where('idepro', $beneficiary->idepro)->sum('seguro');
            $diferidoCap = $beneficiary->helpers()->sum('capital');
            $diferidoInt = $beneficiary->helpers()->sum('interes');
            $gastos = \App\Models\Spend::where('idepro', $beneficiary->idepro)->where('estado', 'ACTIVO')->sum('monto');

            // Precalculamos los pagos por tipo para cada voucher
            $paymentsByVoucher = [];
            foreach ($noLegacy as $voucher) {
                // Usamos la relación ya cargada con eager loading
                $payments = $voucher->payments->filter(fn($p) => $p->prtdtnpag === $voucher->numpago);

                $paymentsByVoucher[$voucher->numtramite][$voucher->numpago] = [
                    'capital' => $payments->filter(fn($p) => str_starts_with($p->prtdtdesc, 'CAPITAL') && ! str_contains($p->prtdtdesc, 'DIF'))
                        ->sum('montopago'),
                    'capital_diferido' => $payments->filter(fn($p) => str_contains($p->prtdtdesc, 'CAPITAL DIF'))
                        ->sum('montopago'),
                    'interes_diferido' => $payments->filter(fn($p) => str_contains($p->prtdtdesc, 'INTERES DIF'))
                        ->sum('montopago'),
                    'amortizacion' => $payments->filter(fn($p) => str_starts_with($p->prtdtdesc, 'AMR'))
                        ->sum('montopago'),
                    'intereses' => $payments->filter(fn($p) => str_starts_with($p->prtdtdesc, 'INTERES') && ! str_contains($p->prtdtdesc, 'DIF'))
                        ->sum('montopago'),
                    'seguros' => $payments->filter(fn($p) => str_starts_with($p->prtdtdesc, 'SEGU'))
                        ->sum('montopago'),
                    'otros' => $payments->filter(fn($p) => str_starts_with($p->prtdtdesc, 'OTR'))
                        ->sum('montopago') + $payments->filter(fn($p) => str_starts_with($p->prtdtdesc, 'GAS'))
                        ->sum('montopago')
                ];
            }

            // Hacemos lo mismo para los pagos legacy
            $paymentsByLegacyVoucher = [];
            foreach ($legacy as $voucher) {
                $payments = $voucher->payments->filter(fn($p) => $p->prtdtnpag === $voucher->numpago);

                $paymentsByLegacyVoucher[$voucher->numtramite][$voucher->numpago] = [
                    'capital' => $payments->filter(fn($p) => str_starts_with($p->prtdtdesc, 'CAPITAL'))
                        ->sum('montopago'),
                    'capital_diferido' => $payments->filter(fn($p) => str_contains($p->prtdtdesc, 'DIFER'))
                        ->sum('montopago'),
                    'amortizacion' => $payments->filter(fn($p) => str_starts_with($p->prtdtdesc, 'AMR'))
                        ->sum('montopago'),
                    'intereses' => $payments->filter(fn($p) => str_starts_with($p->prtdtdesc, 'INTE'))
                        ->sum('montopago'),
                    'seguros' => $payments->filter(fn($p) => str_starts_with($p->prtdtdesc, 'SEGU'))
                        ->sum('montopago'),
                    'otros' => $payments->filter(fn($p) => str_starts_with($p->prtdtdesc, 'OTR'))
                        ->sum('montopago'),
                ];
            }

            // Calculamos el total de capital pagado una sola vez
            $capitalPagado = $beneficiary->payments()
                ->where('prtdtdesc', 'LIKE', 'CAPI%')
                ->where('prtdtdesc', 'NOT LIKE', '%DIF%')
                ->where(function ($query) {
                    $query->whereNull('observacion')
                        ->orWhere('observacion', '')
                        ->orWhere('observacion', '!=', 'LEGACY 22/24');
                })
                ->sum('montopago');

            // Calculamos el monto en diferimientos una sola vez
            $montoDiferimientos = $beneficiary->helpers->where('estado', 'ACTIVO')->sum('capital');

            $cleanName = preg_replace('/[^A-Za-z0-9 \-]/', '_', $beneficiary->nombre);
            $filePath = $exportPath . '/' . $cleanName . '-' . uniqid() . '.pdf';

            // Pass all calculated variables strictly matching the controller
            PDF::loadView('beneficiaries.pdf-extract', compact(
                'beneficiary',
                'noLegacy',
                'legacy',
                'devengadoInt',
                'devengadoSeg',
                'diferidoCap',
                'diferidoInt',
                'gastos',
                'paymentsByVoucher',
                'paymentsByLegacyVoucher',
                'capitalPagado',
                'montoDiferimientos'
            ))->save($filePath);

            return $filePath;
        })->toArray();
    }

    private function createZipFile($files)
    {
        $zipName = 'beneficiarios_' . uniqid() . '.zip';
        $zipPath = storage_path('app/public/exports/' . $zipName);

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            throw new \Exception("No se pudo crear el archivo ZIP en {$zipPath}");
        }

        foreach ($files as $file) {
            if (File::exists($file)) {
                $zip->addFile($file, basename($file));
            }
        }
        $zip->close();

        return '/storage/exports/' . $zipName;
    }

    private function cleanupPDFs($files)
    {
        foreach ($files as $file) {
            if (File::exists($file)) {
                File::delete($file);
            }
        }
    }
}
