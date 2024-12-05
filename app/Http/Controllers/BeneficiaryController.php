<?php

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Readjustment;
use Barryvdh\DomPDF\Facade\Pdf;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use ZipArchive;

class BeneficiaryController extends Controller
{
    public function index()
    {
        return view('beneficiaries.index');
    }

    public function store(Request $request)
    {
        return $request;
    }

    public function show($cedula)
    {
        $beneficiary = Beneficiary::where('ci', $cedula)->firstOrFail();
        $paymentTotals = $this->calculatePaymentTotals($beneficiary->idepro);
        $mesesRestantes = $this->calculateRemainingMonths($beneficiary);

        return view('beneficiaries.show', compact('beneficiary', 'mesesRestantes', 'paymentTotals'));
    }

    public function pdf($cedula)
    {
        $beneficiary = Beneficiary::where('ci', $cedula)->firstOrFail();
        $plans = $this->getActivePlans($beneficiary->idepro);

        $pdf = PDF::loadView('beneficiaries.pdf', compact('beneficiary', 'plans'));
        return $pdf->stream("beneficiary_{$cedula}_" . uniqid() . '.pdf');
    }

    public function bulkPdf($data)
    {
        $beneficiaries = Beneficiary::whereIn('ci', json_decode($data, true))->get();

        try {
            $archivos = $this->generatePDFs($beneficiaries);
            $zipPath = $this->createZipFile($archivos);
            $this->cleanupPDFs($archivos);

            $zipUrl = asset('storage/exports/' . basename($zipPath));
            return redirect()->route('beneficiario.index')
                ->with('success', "La exportación masiva de {$beneficiaries->count()} beneficiarios fue realizada.")
                ->with('link', $zipUrl);
        } catch (\Exception $e) {
            return redirect()->route('beneficiario.index')
                ->with('error', "La exportación masiva de {$beneficiaries->count()} beneficiarios no fue realizada.");
        }
    }

    private function calculatePaymentTotals($idepro)
    {
        $types = ['CAPIT', 'SEG', 'INTE'];
        return collect($types)->mapWithKeys(function ($type) use ($idepro) {
            return [$type => $this->sumPaymentsByType($idepro, $type)];
        });
    }

    private function sumPaymentsByType($idepro, $type)
    {
        return Payment::where('numprestamo', $idepro)
            ->where('prtdtdesc', 'like', "%$type%")
            ->where('montopago', '<', 0)
            ->sum('montopago');
    }

    private function calculateRemainingMonths(Beneficiary $beneficiary)
    {
        $now = new DateTime();
        $endDate = (new DateTime($beneficiary->fecha_activacion))
            ->modify("+{$beneficiary->plazo_credito} months");

        $interval = $now->diff($endDate);
        return $interval->y * 12 + $interval->m;
    }

    private function getActivePlans($idepro)
    {
        $plans = Readjustment::where('idepro', $idepro)->where('estado', 'ACTIVO')->get();
        return $plans->count() > 0 ? $plans : Plan::where('idepro', $idepro)->where('estado', 'ACTIVO')->get();
    }

    private function generatePDFs($beneficiaries)
    {
        return $beneficiaries->map(function ($beneficiary) {
            $plans = $this->getActivePlans($beneficiary->idepro);
            $filePath = 'storage/exports/' . $beneficiary->ci . '_' . uniqid() . '.pdf';

            PDF::loadView('beneficiaries.pdf', compact('beneficiary', 'plans'))->save($filePath);
            return $filePath;
        })->toArray();
    }

    private function createZipFile($files)
    {
        $zipName = 'beneficiarios_' . uniqid() . '.zip';
        $zipPath = storage_path('app/public/exports/' . $zipName);

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE) !== TRUE) {
            throw new \Exception("No se pudo crear el archivo ZIP");
        }

        foreach ($files as $file) {
            $zip->addFile(public_path($file), basename($file));
        }
        $zip->close();

        return $zipPath;
    }

    private function cleanupPDFs($files)
    {
        foreach ($files as $file) {
            File::delete(public_path($file));
        }
    }
}
