<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateBeneficiaryExtractPdfsZip;
use App\Jobs\GenerateBeneficiaryPlansPdfsZip;
use App\Models\Beneficiary;
use App\Models\Helper;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Readjustment;
use Barryvdh\DomPDF\Facade\Pdf;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        if (! Auth::user()->can('write beneficiaries')) {
            abort(403);
        }

        try {
            // Update beneficiary status to blocked
            $beneficiary->update([
                'estado' => 'BLOQUEADO',
            ]);

            return redirect()->back()->with('success', 'Beneficiario bloqueado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al bloquear beneficiario: '.$e->getMessage());
        }
    }

    public function pdf($cedula)
    {
        $beneficiary = Beneficiary::where('ci', $cedula)->firstOrFail();
        $plans = $this->getActivePlans($beneficiary->idepro);
        $differs = $this->getActiveDiffers($beneficiary->idepro);

        $pdf = PDF::loadView('beneficiaries.pdf', compact('beneficiary', 'plans', 'differs'));

        return $pdf->stream("beneficiario_{$cedula}_".uniqid().'.pdf');
    }

    public function pdfExtract($cedula)
    {
        $beneficiary = Beneficiary::where('ci', $cedula)->firstOrFail();
        $payments = $this->getPayments($beneficiary->idepro);

        $noLegacy = $beneficiary->vouchers()
            ->where(function ($query) {
                $query->whereNull('obs_pago')
                    ->orWhere('obs_pago', '')
                    ->orWhere('obs_pago', '!=', 'LEGACY 22/24');
            })->orderBy('numpago', 'ASC')->get();

        $legacy = $beneficiary->vouchers()->where('obs_pago', 'LEGACY 22/24')->orderBy('fecha_pago')->orderBy('numpago')->get();

        $devengadoInt = \App\Models\Earn::where('idepro', $beneficiary->idepro)->sum('interes');
        $devengadoSeg = \App\Models\Earn::where('idepro', $beneficiary->idepro)->sum('seguro');

        $diferidoCap = $beneficiary->helpers()->sum('capital');
        $diferidoInt = $beneficiary->helpers()->sum('interes');

        $gastos = \App\Models\Spend::where('idepro', $beneficiary->idepro)->where('estado', 'ACTIVO')->sum('monto');

        $pdf = PDF::loadView('beneficiaries.pdf-extract', compact('beneficiary',
            'payments',
            'noLegacy',
            'legacy',
            'devengadoInt',
            'devengadoSeg',
            'diferidoCap',
            'diferidoInt',
            'gastos'));

        return $pdf->stream("beneficiario_{$cedula}_extracto_".uniqid().'.pdf');
    }

    public function pdfExtractBoosted($cedula)
    {
        $beneficiary = Beneficiary::where('ci', $cedula)->firstOrFail();

        // Precargamos las relaciones para evitar consultas N+1
        $noLegacy = $beneficiary->vouchers()
            /* ->with(['payments' => function ($query) {
                $query->select('prtdtnpag', 'prtdtdesc', 'montopago', 'numtramite', 'numprestamo');
            }]) */
            ->where(function ($query) {
                $query->whereNull('obs_pago')
                    ->orWhere('obs_pago', '')
                    ->orWhere('obs_pago', '!=', 'LEGACY 22/24');
            })->orderBy('numpago', 'ASC')->get();

        $legacy = $beneficiary->vouchers()
            /* ->with(['payments' => function ($query) {
                $query->select('prtdtnpag', 'prtdtdesc', 'montopago', 'numtramite', 'numprestamo');
            }]) */
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
            $payments = $voucher->payments()->where('prtdtnpag', $voucher->numpago)->get();
            $paymentsByVoucher[$voucher->numtramite][$voucher->numpago] = [
                'capital' => $payments->where('prtdtdesc', 'LIKE', 'CAPI%')
                    ->where('prtdtdesc', 'NOT LIKE', '%DIFER%')
                    ->sum('montopago'),
                'capital_diferido' => $payments->where('prtdtdesc', 'LIKE', '%DIFER%')
                    ->sum('montopago'),
                'amortizacion' => $payments->where('prtdtdesc', 'LIKE', 'AMR%')
                    ->sum('montopago'),
                'intereses' => $payments->where('prtdtdesc', 'LIKE', 'INTE%')
                    ->sum('montopago'),
                'seguros' => $payments->where('prtdtdesc', 'LIKE', 'SEGU%')
                    ->sum('montopago'),
                'otros' => $payments->where('prtdtdesc', 'LIKE', 'OTR%')
                    ->sum('montopago'),
            ];
        }

        // Hacemos lo mismo para los pagos legacy
        $paymentsByLegacyVoucher = [];
        foreach ($legacy as $voucher) {
            $payments = $voucher->payments()->where('prtdtnpag', $voucher->numpago)->get();
            $paymentsByLegacyVoucher[$voucher->numtramite][$voucher->numpago] = [
                'capital' => $payments->where('prtdtdesc', 'LIKE', 'CAPI%')
                    ->where('prtdtdesc', 'NOT LIKE', '%DIFER%')
                    ->sum('montopago'),
                'capital_diferido' => $payments->where('prtdtdesc', 'LIKE', '%DIFER%')
                    ->sum('montopago'),
                'amortizacion' => $payments->where('prtdtdesc', 'LIKE', 'AMR%')
                    ->sum('montopago'),
                'intereses' => $payments->where('prtdtdesc', 'LIKE', 'INTE%')
                    ->sum('montopago'),
                'seguros' => $payments->where('prtdtdesc', 'LIKE', 'SEGU%')
                    ->sum('montopago'),
                'otros' => $payments->where('prtdtdesc', 'LIKE', 'OTR%')
                    ->sum('montopago'),
            ];
        }

        // Calculamos el total de capital pagado una sola vez
        $capitalPagado = $beneficiary->payments()
            ->where('prtdtdesc', 'LIKE', 'CAPI%')
            ->where(function ($query) {
                $query->whereNull('observacion')
                    ->orWhere('observacion', '')
                    ->orWhere('observacion', '!=', 'LEGACY 22/24');
            })
            ->sum('montopago');

        // Calculamos el monto en diferimientos una sola vez
        $montoDiferimientos = $beneficiary->helpers->where('estado', 'ACTIVO')->sum('capital');

        $pdf = PDF::loadView('beneficiaries.pdf-extract-boosted', compact(
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
        ));

        return $pdf->stream("beneficiario_{$cedula}_extracto_".uniqid().'.pdf');
    }

    public function bulkPdf($data)
    {
        $decodedData = json_decode($data, true);
        $identificationNumbers = array_values($decodedData);
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para realizar esta acción.');
        }

        // Dispatch the job to the queue
        GenerateBeneficiaryPlansPdfsZip::dispatch($identificationNumbers, $user->id);

        // Redirect back immediately with a success message
        return back()
            ->with('success', 'La exportación de planes ha comenzado. Recibirás una notificación cuando el archivo esté listo para descargar.');
    }

    public function bulkExtractPdf($data)
    {
        $decodedData = json_decode($data, true);
        $identificationNumbers = array_values($decodedData);
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para realizar esta acción.');
        }

        // Dispatch the job to the queue
        GenerateBeneficiaryExtractPdfsZip::dispatch($identificationNumbers, $user->id);

        // Redirect back immediately with a success message
        return back()
            ->with('success', 'La exportación de extractos ha comenzado. Recibirás una notificación cuando el archivo esté listo para descargar.');
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
        $now = new DateTime;
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
}
