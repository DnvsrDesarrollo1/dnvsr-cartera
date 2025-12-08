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

        $paymentStats = Payment::where('numprestamo', $beneficiary->idepro)
            ->toBase()
            ->selectRaw("
                SUM(CASE WHEN prtdtdesc LIKE '%CAP%' AND montopago < 0 THEN montopago ELSE 0 END) as total_cap,
                SUM(CASE WHEN prtdtdesc LIKE '%SEG%' AND montopago < 0 THEN montopago ELSE 0 END) as total_seg,
                SUM(CASE WHEN prtdtdesc LIKE '%INT%' AND montopago < 0 THEN montopago ELSE 0 END) as total_int,
                SUM(CASE WHEN prtdtdesc LIKE '%CAPI%' THEN montopago ELSE 0 END) as capital_cancelado,
                COUNT(*) as total_payments
            ")
            ->first();

        $plans = Plan::where('idepro', $beneficiary->idepro)
            ->where('estado', '<>', 'INACTIVO')
            ->orderBy('fecha_ppg', 'asc')
            ->get();

        if ($plans->isEmpty()) {
            $plans = Readjustment::where('idepro', $beneficiary->idepro)
                ->where('estado', '<>', 'INACTIVO')
                ->orderBy('fecha_ppg', 'asc')
                ->get();
        }

        $plansData = $plans->map(function ($plan) {
            return [
                'month' => substr($plan->fecha_ppg, 0, 7),
                'amount' => abs((float) $plan->prppgcapi)
            ];
        })->groupBy('month')->map->sum('amount');

        $paymentsData = Payment::where('numprestamo', $beneficiary->idepro)
            ->where('prtdtdesc', 'like', '%CAP%')
            ->toBase() // Raw query, faster
            ->selectRaw("TO_CHAR(fecha_pago, 'YYYY-MM') as month, SUM(ABS(montopago)) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $allMonths = $plansData->keys()->merge($paymentsData->keys())
            ->unique()
            ->sort()
            ->values();

        $chartData = [
            'categories' => [],
            'plans' => [],
            'payments' => [],
            'compliance' => []
        ];

        foreach ($allMonths as $month) {

            $dateObj = \Carbon\Carbon::createFromFormat('Y-m', $month);
            $chartData['categories'][] = ucfirst($dateObj->translatedFormat('M Y'));

            $planAmount = $plansData->get($month, 0);
            $payAmount = $paymentsData->get($month, 0);

            $chartData['plans'][] = round($planAmount, 2);
            $chartData['payments'][] = round($payAmount, 2);
            $chartData['compliance'][] = ($planAmount > 0)
                ? round(($payAmount / $planAmount) * 100, 2)
                : 0;
        }

        $gastosAdicionales = \App\Models\Spend::where('idepro', $beneficiary->idepro)->sum('monto');

        $firstUnpaidPlan = $plans->where('estado', '!=', 'CANCELADO')->first();
        $diasMora = 0;
        $showMora = false;

        if ($firstUnpaidPlan) {
            $fechaInicio = \Carbon\Carbon::parse($firstUnpaidPlan->fecha_ppg)->startOfDay();
            $diasMora = $fechaInicio->diffInDays(now()->startOfDay());
            $showMora = true;
        }

        $mesesRestantes = $this->calculateRemainingMonths($beneficiary);

        $paymentTotals = [
            'CAP' => $paymentStats->total_cap,
            'SEG' => $paymentStats->total_seg,
            'INT' => $paymentStats->total_int
        ];

        return view('beneficiaries.show', [
            'beneficiary' => $beneficiary,
            'mesesRestantes' => $mesesRestantes,
            'paymentTotals' => $paymentTotals,
            'capitalCancelado' => $paymentStats->capital_cancelado,
            'hasPayments' => $paymentStats->total_payments > 0,
            'gastosAdicionales' => $gastosAdicionales,
            'diasMora' => $diasMora,
            'showMora' => $showMora,
            'chartData' => $chartData
        ]);
    }

    public function update(Request $request, Beneficiary $beneficiary)
    {
        if (! Auth::user()->can('write beneficiaries')) {
            abort(403);
        }

        try {
            $beneficiary->update([
                'estado' => 'BLOQUEADO',
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

        return $pdf->stream("beneficiario_{$cedula}_" . uniqid() . '.pdf');
    }

    public function pdfExtract($cedula)
    {
        $beneficiary = Beneficiary::where('ci', $cedula)->firstOrFail();

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

        //return $noLegacy;

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

            // Avoid LIKE in eager-loaded collections; map once and filter in memory
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

            $payments = null;

            //echo $payments.'<br/>';
        }

        //return $paymentsByVoucher;

        // Hacemos lo mismo para los pagos legacy
        $paymentsByLegacyVoucher = [];
        foreach ($legacy as $voucher) {

            $payments = $voucher->payments->filter(fn($p) => $p->prtdtnpag === $voucher->numpago);

            // Avoid LIKE in eager-loaded collections; map once and filter in memory
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

            $payments = null;
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

        $pdf = PDF::loadView('beneficiaries.pdf-extract', compact(
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

        return $pdf->stream("beneficiario_{$cedula}_extracto_" . uniqid() . '.pdf');
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
