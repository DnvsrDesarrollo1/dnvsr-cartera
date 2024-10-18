<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Http\Request;

class BeneficiaryController extends Controller
{
    public function index()
    {
        $beneficiaries = \App\Models\Beneficiary::orderBy('estado', 'asc')->paginate(500);

        $cancelados = \App\Models\Beneficiary::where('estado', 'like', '%CANCELADO%')->count();
        $vigentes = \App\Models\Beneficiary::where('estado', 'like', '%VIGENTE%')->count();
        $ejecuciones = \App\Models\Beneficiary::where('estado', 'like', '%EJECUCION%')->count();
        $vencidos = \App\Models\Beneficiary::where('estado', 'like', '%VENCIDO%')->count();

        return view('beneficiaries.index', compact(
                                                            'beneficiaries',
                                                        'cancelados',
                                                        'vigentes',
                                                        'ejecuciones',
                                                        'vencidos'));
    }

    public function store(Request $request)
    {
        return $request;
    }

    public function show($cedula)
    {
        $beneficiary = \App\Models\Beneficiary::where('ci', $cedula)
                                ->first();

        $vouchers = \App\Models\Voucher::with('payments')
            ->where('numprestamo', $beneficiary->idepro)
            ->paginate(15);

        $plans = \App\Models\Readjustment::where('idepro', $beneficiary->idepro)
                                        ->where('estado', 'like', '%ACTIVO%')
                                        ->get();

        if ($plans->count() <= 0) {
            $plans = \App\Models\Plan::where('idepro', $beneficiary->idepro)
                    ->where('estado', 'like', '%ACTIVO%')
                    ->get();
        }

        $totalVouchers = \App\Models\Voucher::where('numprestamo', $beneficiary->idepro)->sum('montopago');

        $paymentTotals = $this->calculatePaymentTotals($beneficiary->idepro);

        $mesesRestantes = $this->calculateRemainingMonths($beneficiary);

        return view('beneficiaries.show', compact(
            'beneficiary',
            'vouchers',
            'plans',
            'totalVouchers',
            'mesesRestantes',
            'paymentTotals'
        ));
    }

    private function calculatePaymentTotals($idepro)
    {
        return [
            'capital' => $this->sumPaymentsByType($idepro, 'CAPIT'),
            'seguro' => $this->sumPaymentsByType($idepro, 'SEG'),
            'interes' => $this->sumPaymentsByType($idepro, 'INTE'),
        ];
    }

    private function sumPaymentsByType($idepro, $type)
    {
        return \App\Models\Payment::where('numprestamo', $idepro)
            ->where('prtdtdesc', 'like', "%$type%")
            ->where('montopago', '<', 0)
            ->sum('montopago');
    }

    private function calculateRemainingMonths(\App\Models\Beneficiary $beneficiary)
    {
        $now = new DateTime('now');
        $endDate = (new DateTime($beneficiary->fecha_activacion))
            ->modify("+{$beneficiary->plazo_credito} months");

        $dias = $endDate->diff($now)->format('%a');
        return round($dias / 30.5, 0);
    }
}
