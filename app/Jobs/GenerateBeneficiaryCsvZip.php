<?php

namespace App\Jobs;

use App\Models\Beneficiary;
use App\Models\User;
use App\Models\Plan;
use App\Models\Readjustment;
use App\Models\Helper;
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

class GenerateBeneficiaryCsvZip implements ShouldQueue
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
            Log::error('User not found in GenerateBeneficiaryCsvsZip job.', ['userId' => $this->userId]);
            return;
        }

        try {
            $beneficiaries = Beneficiary::find($this->beneficiaryIds);

            if ($beneficiaries->isEmpty()) {
                Log::warning('No beneficiaries found for the given IDs in GenerateBeneficiaryCsvsZip job.', ['ids' => $this->beneficiaryIds]);
                return;
            }

            // Export XLSX logic
            $idepros = $beneficiaries->pluck('idepro');

            $plans = Plan::join('beneficiaries', 'beneficiaries.idepro', '=', 'plans.idepro')
                ->select(
                    'plans.id',
                    'plans.idepro',
                    'plans.fecha_ppg',
                    'plans.prppgnpag',
                    'plans.prppgcapi',
                    'plans.prppginte',
                    'plans.prppgsegu',
                    'plans.prppgotro',
                    'plans.prppgtota',
                    'plans.prppggral',
                    'plans.prppgcarg',
                    'plans.estado',
                    'beneficiaries.nombre',
                    'beneficiaries.ci',
                    'beneficiaries.complemento',
                    'beneficiaries.expedido'
                )
                ->whereIn('plans.idepro', $idepros)
                ->where('plans.estado', '<>', 'INACTIVO')
                ->orderBy('plans.fecha_ppg', 'asc')
                ->get();

            $readj = Readjustment::join('beneficiaries', 'beneficiaries.idepro', '=', 'readjustments.idepro')
                ->select(
                    'readjustments.id',
                    'readjustments.idepro',
                    'readjustments.fecha_ppg',
                    'readjustments.prppgnpag',
                    'readjustments.prppgcapi',
                    'readjustments.prppginte',
                    'readjustments.prppgsegu',
                    'readjustments.prppgotro',
                    'readjustments.prppgtota',
                    'readjustments.prppggral',
                    'readjustments.prppgcarg',
                    'readjustments.estado',
                    'beneficiaries.nombre',
                    'beneficiaries.ci',
                    'beneficiaries.complemento',
                    'beneficiaries.expedido'
                )
                ->whereIn('readjustments.idepro', $idepros)
                ->where('readjustments.estado', '<>', 'INACTIVO')
                ->orderBy('readjustments.fecha_ppg', 'asc')
                ->get();

            // XLSX export using PhpSpreadsheet
            $exportPath = storage_path('app/public/exports');
            if (!File::isDirectory($exportPath)) {
                File::makeDirectory($exportPath, 0755, true, true);
            }
            $fileName = 'exportacion_planes_' . uniqid() . '.xlsx';
            $filePath = $exportPath . '/' . $fileName;

            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $headers = [
                'NOMBRES', 'CI', 'COMPLEMENTO', 'EXPEDIDO', 'ID', 'IDEPRO', 'FECHA PPG',
                'NRO CUOTA', 'CAPITAL', 'INTERES', 'INTERES DEVG', 'SEGURO', 'SEGURO DEVG',
                'GASTOS ADM/JUD', 'TOTAL CUOTA', 'ESTADO'
            ];
            $sheet->fromArray($headers, null, 'A1');

            $row = 2;
            foreach ($plans as $plan) {
                $sheet->fromArray([
                    $plan->nombre,
                    $plan->ci,
                    $plan->complemento,
                    $plan->expedido,
                    $plan->id,
                    $plan->idepro,
                    $plan->fecha_ppg,
                    $plan->prppgnpag,
                    $plan->prppgcapi,
                    $plan->prppginte,
                    $plan->prppggral,
                    $plan->prppgsegu,
                    $plan->prppgcarg,
                    $plan->prppgotro,
                    $plan->prppgtota,
                    $plan->estado,
                ], null, 'A' . $row++);
            }
            foreach ($readj as $r) {
                $sheet->fromArray([
                    $r->nombre,
                    $r->ci,
                    $r->complemento,
                    $r->expedido,
                    $r->id,
                    $r->idepro,
                    $r->fecha_ppg,
                    $r->prppgnpag,
                    $r->prppgcapi,
                    $r->prppginte,
                    $r->prppggral,
                    $r->prppgsegu,
                    $r->prppgcarg,
                    $r->prppgotro,
                    $r->prppgtota,
                    $r->estado,
                ], null, 'A' . $row++);
            }

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save($filePath);

            $fileUrl = asset('storage/exports/' . $fileName);
            Log::info("XLSX file created for user {$user->id} at: {$fileUrl}");

            $user->notify(new ExportReadyNotification($fileUrl, $beneficiaries->count()));
        } catch (\Exception $e) {
            Log::error('Failed to generate beneficiary XLSX file for user ' . ($user ? $user->id : 'unknown'), [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    private function generatePDFs($beneficiaries)
    {
        $exportPath = storage_path('app/public/exports');
        if (!File::isDirectory($exportPath)) {
            File::makeDirectory($exportPath, 0755, true, true);
        }

        return $beneficiaries->map(function ($beneficiary) use ($exportPath) {
            $plans = $this->getActivePlans($beneficiary->idepro);
            $differs = $this->getActiveDiffers($beneficiary->idepro);

            $cleanName = preg_replace('/[^A-Za-z0-9 \-]/', '_', $beneficiary->nombre);
            $filePath = $exportPath . '/' . $cleanName . '-' . uniqid() . '.pdf';

            PDF::loadView('beneficiaries.pdf', compact('beneficiary', 'plans', 'differs'))->save($filePath);
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

    private function getActivePlans($idepro)
    {
        $plans = Plan::where('idepro', $idepro)
            ->where('estado', '<>', 'INACTIVO')
            ->orderBy('fecha_ppg', 'asc')
            ->get();
        return $plans->count() > 0 ? $plans : Readjustment::where('idepro', $idepro)
            ->where('estado', '<>', 'INACTIVO')
            ->orderBy('fecha_ppg', 'asc')
            ->get();
    }

    private function getActiveDiffers($idepro)
    {
        return Helper::where('idepro', $idepro)->where('estado', 'ACTIVO')->orderBy('indice', 'asc')->get();
    }
}
