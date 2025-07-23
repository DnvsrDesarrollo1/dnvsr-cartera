<?php

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use App\Models\Helper;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Readjustment;
use Barryvdh\DomPDF\Facade\Pdf;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class BeneficiaryController extends Controller
{
    public function index()
    {
        return view('beneficiaries.index');
    }

    public function indexAll()
    {
        return view('beneficiaries.index-all');
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

        $plansArray = $this->getActivePlans($beneficiary->idepro);
        $paymentsArray = $this->getPayments($beneficiary->idepro, 'CAP');

        return view('beneficiaries.show', compact(
            'beneficiary',
            'mesesRestantes',
            'paymentTotals',
            'plansArray',
            'paymentsArray'
        ));
    }

    public function update(Request $request, Beneficiary $beneficiary)
    {
        // Check if user has permission to update beneficiaries
        if (!Auth::user()->can('write beneficiaries')) {
            abort(403);
        }

        try {
            // Update beneficiary status to blocked
            $beneficiary->update([
                'estado' => 'BLOQUEADO'
            ]);

            return redirect()->back()->with('success', 'Beneficiario bloqueado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al bloquear beneficiario: ' . $e->getMessage());
        }
    }

    public function pdf($cedula)
    {
        $beneficiary = Beneficiary::where('ci', $cedula)->firstOrFail();
        $plans = $this->getActivePlans($beneficiary->idepro);
        $differs = $this->getActiveDiffers($beneficiary->idepro);

        $pdf = PDF::loadView('beneficiaries.pdf', compact('beneficiary', 'plans', 'differs'));
        return $pdf->stream("beneficiary_{$cedula}_" . uniqid() . '.pdf');
    }

    public function pdfExtract($cedula)
    {
        $beneficiary = Beneficiary::where('ci', $cedula)->firstOrFail();
        $payments = $this->getPayments($beneficiary->idepro);

        $pdf = PDF::loadView('beneficiaries.pdf-extract', compact('beneficiary', 'payments'));

        return $pdf->stream("beneficiary_{$cedula}_extracto_" . uniqid() . '.pdf');
    }

    public function bulkPdf($data)
    {
        $decodedData = json_decode($data, true);
        $identificationNumbers = array_diff(array_values($decodedData));

        $beneficiaries = Beneficiary::find($identificationNumbers);

        try {
            $zipUrl = null;

            $archivos = $this->generatePDFs($beneficiaries);
            $zipPath = $this->createZipFile($archivos);
            $this->cleanupPDFs($archivos);

            Log::info("ZIP file created at: {$zipUrl}");
            $zipUrl = asset('storage/exports/' . basename($zipPath));
            // Example usage: Log the URL or notify the user
            Log::info("ZIP file created at: {$zipUrl}");

            return redirect()->route('beneficiario.index')
                ->with('success', "La exportación masiva de {$beneficiaries->count()} beneficiarios fue realizada correctamente.")
                ->with('link', $zipUrl);
        } catch (\Exception $e) {
            return redirect()->route('beneficiario.index')
                ->with('error', "La exportación masiva de {$beneficiaries->count()} beneficiarios no fue realizada.")
                ->with('data', $e->getMessage());
        }
    }

    private function calculatePaymentTotals($idepro)
    {
        $types = ['CAP', 'SEG', 'INT'];
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
        $plans = Plan::where('idepro', $idepro)
            ->where('estado', '<>', 'INACTIVO')
            ->orderBy('fecha_ppg', 'asc')
            ->get();
        return $plans->count() > 0 ? $plans : Readjustment::where('idepro', $idepro)
            ->where('estado', '<>', 'INACTIVO')
            ->orderBy('fecha_ppg', 'asc')
            ->get();
    }

    private function getPayments($idepro, $type = null)
    {
        $query = Payment::where('numprestamo', $idepro)->orderBy('fecha_pago', 'DESC');

        if ($type) {
            $query->where('prtdtdesc', 'like', "%$type%");
        }

        return $query->get();
    }

    private function getActiveDiffers($idepro)
    {
        $differs = Helper::where('idepro', $idepro)->where('estado', 'ACTIVO')->orderBy('indice', 'asc')->get();
        return $differs;
    }

    private function generatePDFs($beneficiaries)
    {
        return $beneficiaries->map(function ($beneficiary) {
            $plans = $this->getActivePlans($beneficiary->idepro);
            $differs = $this->getActiveDiffers($beneficiary->idepro);

            $cleanName = preg_replace('/[^A-Za-z0-9 \-]/', '_', $beneficiary->nombre);
            $filePath = 'storage/exports/' . $cleanName . '.pdf';

            PDF::loadView('beneficiaries.pdf', compact('beneficiary', 'plans', 'differs'))->save($filePath);
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
