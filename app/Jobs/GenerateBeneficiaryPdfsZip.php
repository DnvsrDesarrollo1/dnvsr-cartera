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

class GenerateBeneficiaryPdfsZip implements ShouldQueue
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
            Log::error('User not found in GenerateBeneficiaryPdfsZip job.', ['userId' => $this->userId]);
            return;
        }

        try {
            $beneficiaries = Beneficiary::find($this->beneficiaryIds);

            if ($beneficiaries->isEmpty()) {
                Log::warning('No beneficiaries found for the given IDs in GenerateBeneficiaryPdfsZip job.', ['ids' => $this->beneficiaryIds]);
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

        return $zipPath;
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
