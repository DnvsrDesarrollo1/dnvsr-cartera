<?php

namespace App\Livewire;

use Livewire\Component;


class VoucherRegister extends Component
{
    public $beneficiary;
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
            'agencia_pago' => $this->agencia_pago . ' : (VENTANILLA) ' . auth()->user()->name,
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

        $this->showModal = false;
        $this->confirmingSave = false;

        return redirect(request()->header('Referer'));
    }

    public function render()
    {
        $this->numprestamo = $this->beneficiary->idepro;

        return view('livewire.voucher-register');
    }
}
