<?php

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use App\Models\Voucher;
use App\Models\Payment;
use DateTime;
use Illuminate\Http\Request;

class BeneficiaryController extends Controller
{
    public function index()
    {
        $beneficiaries = Beneficiary::orderBy('estado', 'asc')->paginate(500);

        $cancelados = Beneficiary::where('estado', 'like', '%CANCELADO%')->count();
        $vigentes = Beneficiary::where('estado', 'like', '%VIGENTE%')->count();
        $ejecuciones = Beneficiary::where('estado', 'like', '%EJECUCION%')->count();
        $vencidos = Beneficiary::where('estado', 'like', '%VENCIDO%')->count();

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
        $beneficiary = Beneficiary::where('ci', $cedula)->first();
        $vouchers = Voucher::with('payments')
            ->where('numprestamo', $beneficiary->idepro)
            ->paginate(15);

        $totalVouchers = Voucher::where('numprestamo', $beneficiary->idepro)->sum('montopago');

        $paymentTotals = $this->calculatePaymentTotals($beneficiary->idepro);

        $mesesRestantes = $this->calculateRemainingMonths($beneficiary);

        return view('beneficiaries.show', compact(
            'beneficiary',
            'vouchers',
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
        return Payment::where('numprestamo', $idepro)
            ->where('prtdtdesc', 'like', "%$type%")
            ->where('montopago', '<', 0)
            ->sum('montopago');
    }

    private function calculateRemainingMonths(Beneficiary $beneficiary)
    {
        $now = new DateTime('now');
        $endDate = (new DateTime($beneficiary->fecha_activacion))
            ->modify("+{$beneficiary->plazo_credito} months");

        $dias = $endDate->diff($now)->format('%a');
        return round($dias / 30.5, 0);
    }
}
