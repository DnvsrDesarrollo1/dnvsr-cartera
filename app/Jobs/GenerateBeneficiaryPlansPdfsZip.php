<?php

namespace App\Jobs;

use App\Models\Beneficiary;
use App\Models\Helper;
use App\Models\Plan;
use App\Models\Readjustment;
use App\Models\User;
use App\Notifications\ExportReadyNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class GenerateBeneficiaryPlansPdfsZip implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $beneficiaryIds;

    protected $userId;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 1200;

    /**
     * Create a new job instance.
     */
    public function __construct(array $beneficiaryIds, int $userId)
    {
        $this->beneficiaryIds = $beneficiaryIds;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 1200);

        $user = User::find($this->userId);
        if (! $user) {
            Log::error('User not found in GenerateBeneficiaryPdfsZip job.', ['userId' => $this->userId]);

            return;
        }

        try {
            // Eager load relationships with constraints to avoid N+1 problem
            $beneficiaries = Beneficiary::with([
                'plans' => function ($query) {
                    $query->where('estado', '<>', 'INACTIVO')
                        ->orderBy('fecha_ppg', 'asc');
                },
                'readjustments' => function ($query) {
                    $query->where('estado', '<>', 'INACTIVO')
                        ->orderBy('fecha_ppg', 'asc');
                },
                'helpers' => function ($query) {
                    $query->where('estado', 'ACTIVO')
                        ->orderBy('indice', 'asc');
                },
            ])->whereIn('id', $this->beneficiaryIds)->get();

            if ($beneficiaries->isEmpty()) {
                Log::warning('No beneficiaries found for the given IDs in GenerateBeneficiaryPdfsZip job.', ['ids' => $this->beneficiaryIds]);

                return;
            }

            $pdfFiles = $this->generatePDFs($beneficiaries);
            $zipPath = $this->createZipFile($pdfFiles);
            $this->cleanupPDFs($pdfFiles);

            $zipUrl = ($zipPath);
            Log::info("ZIP file created for user {$user->id} at: {$zipUrl} : Check it out.");

            // Notificar al usuario que el archivo está listo
            $user->notify(new ExportReadyNotification($zipUrl, count($beneficiaries)));
        } catch (\Exception $e) {
            Log::error('Failed to generate beneficiary PDF zip file for user ' . $user->id, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    private function generatePDFs($beneficiaries)
    {
        $exportPath = storage_path('app/public/exports');
        if (! File::isDirectory($exportPath)) {
            File::makeDirectory($exportPath, 0755, true, true);
        }

        return $beneficiaries->map(function ($beneficiary) use ($exportPath) {
            // Use eager loaded relationships
            $plans = $beneficiary->plans->isNotEmpty() ? $beneficiary->plans : $beneficiary->readjustments;

            // Map 'helpers' relation to $differs variable as expected by the view
            $differs = $beneficiary->helpers;

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
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
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
