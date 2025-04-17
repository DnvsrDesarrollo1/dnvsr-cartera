<?php

namespace App\Livewire;

use App\Traits\FinanceTrait;
use Livewire\Component;

class VoucherRegister extends Component
{
    use FinanceTrait;

    public $idepro;
    public $beneficiario;
    public $cuota;

    public $numpago,
        $numtramite,
        $numprestamo,
        $fecha_pago,
        $descripcion,
        $montopago,
        $capital,
        $interes,
        $interes_devg,
        $seguro,
        $seguro_devg,
        $otros,
        $hora_pago,
        $prtdtfpro,
        $agencia_pago,
        $depto_pago,
        $obs_pago;

    public $totalpagado = 0;
    public $totalinicial = 0;

    public $showModal = false;
    public $confirmingSave = false;

    protected $rules = [
        'numpago' => 'required',
        'numtramite' => 'required',
        'numprestamo' => 'required',
        'fecha_pago' => 'required',
        'descripcion' => 'required',
        'capital' => 'required',
        'interes' => 'required',
        'interes_devg' => 'required',
        'seguro' => 'required',
        'seguro_devg' => 'required',
        'hora_pago' => 'required',
        'agencia_pago' => 'required',
        'depto_pago' => 'required',
        'obs_pago' => 'required'
    ];

    public function save()
    {
        $this->validate();

        #CAPITAL PAYMENT
        \App\Models\Payment::create([
            'numtramite' => $this->numtramite,
            'prtdtitem' => 1,
            'numprestamo' => $this->numprestamo,
            'prtdtpref' => 20,
            'prtdtccon' => 1,
            'fecha_pago' => now(),
            'prtdtdesc' => 'CAPITAL',
            'montopago' => $this->capital,
            'prtdtuser' => 'AEV-PVS VENTANILLA',
            'hora_pago' => $this->hora_pago,
            'prtdtfpro' => null,
            'prtdtnpag' => $this->numpago,
            'depto_pago' => $this->depto_pago,
            'obs_pago' => $this->obs_pago
        ]);

        \App\Models\Payment::create([
            'numtramite' => $this->numtramite,
            'prtdtitem' => 2,
            'numprestamo' => $this->numprestamo,
            'prtdtpref' => 20,
            'prtdtccon' => 2,
            'fecha_pago' => now(),
            'prtdtdesc' => 'INTERES',
            'montopago' => $this->interes,
            'prtdtuser' => 'AEV-PVS VENTANILLA',
            'hora_pago' => $this->hora_pago,
            'prtdtfpro' => null,
            'prtdtnpag' => $this->numpago,
            'depto_pago' => $this->depto_pago,
            'obs_pago' => $this->obs_pago
        ]);

        \App\Models\Payment::create([
            'numtramite' => $this->numtramite,
            'prtdtitem' => 3,
            'numprestamo' => $this->numprestamo,
            'prtdtpref' => 20,
            'prtdtccon' => 2,
            'fecha_pago' => now(),
            'prtdtdesc' => 'INTERES DEVG',
            'montopago' => $this->interes_devg,
            'prtdtuser' => 'AEV-PVS VENTANILLA',
            'hora_pago' => $this->hora_pago,
            'prtdtfpro' => null,
            'prtdtnpag' => $this->numpago,
            'depto_pago' => $this->depto_pago,
            'obs_pago' => $this->obs_pago
        ]);

        \App\Models\Payment::create([
            'numtramite' => $this->numtramite,
            'prtdtitem' => 4,
            'numprestamo' => $this->numprestamo,
            'prtdtpref' => 20,
            'prtdtccon' => 2,
            'fecha_pago' => now(),
            'prtdtdesc' => 'SEGURO DESGRV',
            'montopago' => $this->seguro,
            'prtdtuser' => 'AEV-PVS VENTANILLA',
            'hora_pago' => $this->hora_pago,
            'prtdtfpro' => null,
            'prtdtnpag' => $this->numpago,
            'depto_pago' => $this->depto_pago,
            'obs_pago' => $this->obs_pago
        ]);

        \App\Models\Payment::create([
            'numtramite' => $this->numtramite,
            'prtdtitem' => 5,
            'numprestamo' => $this->numprestamo,
            'prtdtpref' => 21,
            'prtdtccon' => 37,
            'fecha_pago' => now(),
            'prtdtdesc' => 'SEGURO DESGRV DEVG',
            'montopago' => $this->seguro_devg,
            'prtdtuser' => 'AEV-PVS VENTANILLA',
            'hora_pago' => $this->hora_pago,
            'prtdtfpro' => null,
            'prtdtnpag' => $this->numpago,
            'depto_pago' => $this->depto_pago,
            'obs_pago' => $this->obs_pago
        ]);

        \App\Models\Payment::create([
            'numtramite' => $this->numtramite,
            'prtdtitem' => 6,
            'numprestamo' => $this->numprestamo,
            'prtdtpref' => 21,
            'prtdtccon' => 37,
            'fecha_pago' => now(),
            'prtdtdesc' => 'OTROS',
            'montopago' => $this->otros,
            'prtdtuser' => 'AEV-PVS VENTANILLA',
            'hora_pago' => $this->hora_pago,
            'prtdtfpro' => null,
            'prtdtnpag' => $this->numpago,
            'depto_pago' => $this->depto_pago,
            'obs_pago' => $this->obs_pago
        ]);

        $this->montopago = $this->capital + $this->interes + $this->interes_devg + $this->seguro + $this->seguro_devg + $this->otros;

        #VOUCHER
        \App\Models\Voucher::create([
            'agencia_pago' => $this->agencia_pago . ' : (VENTANILLA) ' . \Illuminate\Support\Facades\Auth::user()->name,
            'descripcion' => $this->descripcion,
            'fecha_pago' => $this->fecha_pago,
            'hora_pago' => $this->hora_pago,
            'montopago' => $this->montopago,
            'numpago' => $this->numpago,
            'numprestamo' => $this->numprestamo,
            'numtramite' => $this->numtramite,
            'depto_pago' => $this->depto_pago,
            'obs_pago' => $this->obs_pago
        ]);

        $this->cuota->update([
            'prppgcapi' => $this->capital,
            'prppgtota' => $this->montopago,
            'estado' => 'CANCELADO',
            'user_id' => \Illuminate\Support\Facades\Auth::user()->id,
        ]);

        $this->beneficiario->update([
            'saldo_credito' => $this->beneficiario->saldo_credito - $this->capital,
        ]);

        if ($this->totalpagado != '' and $this->totalpagado > 0) {

            $planVigente = $this->beneficiario->getCurrentPlan('ACTIVO');

            $this->beneficiario = $this->beneficiario->refresh();

            $this->actualizarPlanActual($this->numprestamo, $this->beneficiario->saldo_credito, $planVigente);

        }

        $this->showModal = false;
        $this->confirmingSave = false;

        return redirect(request()->header('Referer'));
    }

    public function mount($idepro)
    {
        $p = \App\Models\Plan::where('idepro', $idepro)
            ->whereIn('estado', ['VENCIDO', 'ACTIVO'])
            ->orderBy('fecha_ppg', 'asc')
            ->first();

        if (!$p) {
            $p = \App\Models\Readjustment::where('idepro', $idepro)
                ->whereIn('estado', ['VENCIDO', 'ACTIVO'])
                ->orderBy('fecha_ppg', 'asc')
                ->first();
        }

        $this->beneficiario = \App\Models\Beneficiary::where('idepro', $idepro)
            ->first();

        $this->cuota = $p;

        if ($p != null) {
            $this->numpago = $p->prppgnpag;
            $this->numtramite = null;
            $this->numprestamo = $p->idepro;
            $this->fecha_pago = $p->fecha_ppg;
            $this->descripcion = null;
            $this->montopago = round($p->prppgtota, 2);
            $this->capital = round($p->prppgcapi, 2);
            $this->interes = round($p->prppginte, 2);
            $this->interes_devg = round($p->prppggral, 2);
            $this->seguro = round($p->prppgsegu, 2);
            $this->seguro_devg = round($p->prppgcarg, 2);
            $this->otros = round($p->prppgotro, 2);
            $this->hora_pago = now();
            $this->prtdtfpro = null;
            $this->agencia_pago = null;
            $this->depto_pago = null;
            $this->obs_pago = null;
        }
    }

    public function render()
    {
        if ($this->totalpagado != '' and $this->totalpagado > 0) {
            $this->totalinicial = $this->totalpagado;

            $this->totalpagado -= $this->interes;
            $this->totalpagado -= $this->interes_devg;
            $this->totalpagado -= $this->seguro;
            $this->totalpagado -= $this->seguro_devg;
            $this->totalpagado -= $this->otros;

            $this->capital = $this->totalpagado;

            $this->totalpagado = $this->totalinicial;
        }

        return view('livewire.voucher-register');
    }
}
