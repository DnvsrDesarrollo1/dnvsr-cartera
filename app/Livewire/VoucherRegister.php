<?php

namespace App\Livewire;

use App\Traits\FinanceTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
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

    public $capital_diff, $interes_diff, $cuota_diff;
    public $enableDiffFields = false;
    public $totalpagado = 0;
    public $totalinicial = 0;

    public $comprobanteDuplicado = false;

    public $voucherModal = false;

    protected $rules = [
        'numpago' => 'required',
        'numtramite' => 'required|unique:vouchers',
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
        // Generate temporary backup CSV of current payment plan
        $timestamp = now()->format('Y-m-d_H-i-s');
        $tempDir = storage_path('app/public/temp/');

        if (!File::isDirectory($tempDir)) {
            File::makeDirectory($tempDir, 0755, true, true);
        }

        $filename = "payment_plan_backup_{$this->numprestamo}_{$timestamp}.csv";
        $fullPath = "{$tempDir}/{$filename}";

        // Create temp directory if it doesn't exist
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        // Get current payment plan data
        $currentPlan = $this->beneficiario->getCurrentPlan('INACTIVO', '!=');

        // Create CSV file with payment plan data
        $file = fopen($fullPath, 'w');
        fputcsv($file, ['idepro', 'prppgnpag', 'fecha_ppg', 'prppgcapi', 'prppginte', 'prppggral', 'prppgsegu', 'prppgcarg', 'prppgotro', 'prppgtota', 'estado']);

        foreach ($currentPlan as $payment) {
            fputcsv($file, [
                $payment->idepro,
                $payment->prppgnpag,
                $payment->fecha_ppg,
                $payment->prppgcapi,
                $payment->prppginte,
                $payment->prppggral,
                $payment->prppgsegu,
                $payment->prppgcarg,
                $payment->prppgotro,
                $payment->prppgtota,
                $payment->estado
            ]);
        }
        fclose($file);

        // Schedule file deletion after 30 minutes
        dispatch(function () use ($fullPath) {
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        })->delay(now()->addMinutes(30));

        if ($this->enableDiffFields) {

            $helpers = $this->beneficiario->helpers()->where('estado', 'ACTIVO')->get();

            if ($this->capital_diff >= $helpers->sum('capital') && $this->interes_diff >= $helpers->sum('interes')) {

                #CAPITAL PAYMENT
                \App\Models\Payment::create([
                    'numtramite' => $this->numtramite,
                    'prtdtitem' => 1,
                    'numprestamo' => $this->numprestamo,
                    'prtdtpref' => 20,
                    'prtdtccon' => 1,
                    'fecha_pago' => $this->fecha_pago,
                    'prtdtdesc' => 'CAPITAL DIFERIDO',
                    'montopago' => $this->capital_diff,
                    'prtdtuser' => 'AEV-PVS VENTANILLA',
                    'hora_pago' => $this->hora_pago,
                    'prtdtfpro' => null,
                    'prtdtnpag' => $this->cuota_diff,
                    'depto_pago' => $this->depto_pago,
                    'obs_pago' => $this->obs_pago
                ]);

                \App\Models\Payment::create([
                    'numtramite' => $this->numtramite,
                    'prtdtitem' => 2,
                    'numprestamo' => $this->numprestamo,
                    'prtdtpref' => 20,
                    'prtdtccon' => 2,
                    'fecha_pago' => $this->fecha_pago,
                    'prtdtdesc' => 'INTERES DIFERIDO',
                    'montopago' => $this->interes_diff,
                    'prtdtuser' => 'AEV-PVS VENTANILLA',
                    'hora_pago' => $this->hora_pago,
                    'prtdtfpro' => null,
                    'prtdtnpag' => $this->cuota_diff,
                    'depto_pago' => $this->depto_pago,
                    'obs_pago' => $this->obs_pago
                ]);

                #VOUCHER
                \App\Models\Voucher::create([
                    'agencia_pago' => $this->agencia_pago . ' : (VENTANILLA) ' . Auth::user()->name,
                    'descripcion' => $this->descripcion,
                    'fecha_pago' => $this->fecha_pago,
                    'hora_pago' => $this->hora_pago,
                    'montopago' => $this->capital_diff + $this->interes_diff,
                    'numpago' => $this->cuota_diff,
                    'numprestamo' => $this->numprestamo,
                    'numtramite' => $this->numtramite,
                    'depto_pago' => $this->depto_pago,
                    'obs_pago' => $this->obs_pago
                ]);

                $this->beneficiario->helpers()->where('estado', 'ACTIVO')->where('indice', $this->cuota_diff)->first()->update([
                    'capital' => $this->capital_diff,
                    'interes' => $this->interes_diff,
                    'estado' => 'CANCELADO',
                    'user_id' => Auth::user()->id ?? 1,
                ]);

                $desabilitar = $this->beneficiario->helpers()->where('estado', 'ACTIVO')->where('indice', '>', $this->cuota_diff)->get();

                foreach ($desabilitar as $d) {
                    $d->update([
                        'capital' => 0,
                        'interes' => 0,
                        'estado' => 'CANCELADO',
                        'user_id' => Auth::user()->id ?? 1,
                    ]);
                }

                return redirect(request()->header('Referer'));
            } else {
                #CAPITAL PAYMENT
                \App\Models\Payment::create([
                    'numtramite' => $this->numtramite,
                    'prtdtitem' => 1,
                    'numprestamo' => $this->numprestamo,
                    'prtdtpref' => 20,
                    'prtdtccon' => 1,
                    'fecha_pago' => $this->fecha_pago,
                    'prtdtdesc' => 'CAPITAL DIFERIDO',
                    'montopago' => $this->capital_diff,
                    'prtdtuser' => 'AEV-PVS VENTANILLA',
                    'hora_pago' => $this->hora_pago,
                    'prtdtfpro' => null,
                    'prtdtnpag' => $this->cuota_diff,
                    'depto_pago' => $this->depto_pago,
                    'obs_pago' => $this->obs_pago
                ]);

                \App\Models\Payment::create([
                    'numtramite' => $this->numtramite,
                    'prtdtitem' => 2,
                    'numprestamo' => $this->numprestamo,
                    'prtdtpref' => 20,
                    'prtdtccon' => 2,
                    'fecha_pago' => $this->fecha_pago,
                    'prtdtdesc' => 'INTERES DIFERIDO',
                    'montopago' => $this->interes,
                    'prtdtuser' => 'AEV-PVS VENTANILLA',
                    'hora_pago' => $this->hora_pago,
                    'prtdtfpro' => null,
                    'prtdtnpag' => $this->cuota_diff,
                    'depto_pago' => $this->depto_pago,
                    'obs_pago' => $this->obs_pago
                ]);

                #VOUCHER
                \App\Models\Voucher::create([
                    'agencia_pago' => $this->agencia_pago . ' : (VENTANILLA) ' . Auth::user()->name,
                    'descripcion' => $this->descripcion,
                    'fecha_pago' => $this->fecha_pago,
                    'hora_pago' => $this->hora_pago,
                    'montopago' => $this->capital_diff + $this->interes_diff,
                    'numpago' => $this->cuota_diff,
                    'numprestamo' => $this->numprestamo,
                    'numtramite' => $this->numtramite,
                    'depto_pago' => $this->depto_pago,
                    'obs_pago' => $this->obs_pago
                ]);

                $this->beneficiario->helpers()->where('indice', $this->cuota_diff)->update([
                    'estado' => 'CANCELADO',
                    'user_id' => Auth::user()->id ?? 1,
                ]);

                $this->beneficiario->update([
                    'saldo_credito' => $this->beneficiario->saldo_credito - $this->capital_diff,
                    'user_id' => Auth::user()->id,
                    'updated_at' => now()
                ]);

                return redirect(request()->header('Referer'));
            }
        }

        if (!$this->enableDiffFields) {

            $this->validate();

            #CAPITAL PAYMENT
            \App\Models\Payment::create([
                'numtramite' => $this->numtramite,
                'prtdtitem' => 1,
                'numprestamo' => $this->numprestamo,
                'prtdtpref' => 20,
                'prtdtccon' => 1,
                'fecha_pago' => $this->fecha_pago,
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
                'fecha_pago' => $this->fecha_pago,
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
                'fecha_pago' => $this->fecha_pago,
                'prtdtdesc' => 'INTERES DEVENGADO',
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
                'fecha_pago' => $this->fecha_pago,
                'prtdtdesc' => 'SEGURO DESGRAVAMEN',
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
                'fecha_pago' => $this->fecha_pago,
                'prtdtdesc' => 'SEGURO DESGRAVAMEN DEVENGADO',
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
                'fecha_pago' => $this->fecha_pago,
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
                'agencia_pago' => $this->agencia_pago . ' : (VENTANILLA) ' . Auth::user()->name,
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
                'user_id' => Auth::user()->id,
            ]);

            $this->beneficiario->update([
                'saldo_credito' => $this->beneficiario->saldo_credito - $this->capital,
            ]);

            if (($this->totalpagado != '' || $this->totalpagado > 0) || $this->beneficiario->saldo_credito <= 0) {

                $planVigente = $this->beneficiario->getCurrentPlan('CANCELADO', '!=');

                $totalPlan = $this->beneficiario->getCurrentPlan('INACTIVO', '!=')->sum('prppgcapi');

                $this->beneficiario = $this->beneficiario->refresh();

                $this->actualizarPlanActual($this->numprestamo, $this->beneficiario->saldo_credito, $planVigente, $totalPlan);
            }

            if ($this->beneficiario->saldo_credito <= 0) {

                $planVigente = $this->beneficiario->getCurrentPlan('ACTIVO');

                $totalPlan = $this->beneficiario->getCurrentPlan('INACTIVO', '!=')->sum('prppgcapi');

                $this->beneficiario = $this->beneficiario->refresh();

                $this->actualizarPlanActual($this->numprestamo, $this->beneficiario->saldo_credito, $planVigente, $totalPlan);

                $this->beneficiario->update([
                    'saldo_credito' => 0,
                    'estado' => 'CANCELADO',
                    'user_id' => Auth::user()->id,
                    'updated_at' => now()
                ]);
            }

            return redirect(request()->header('Referer'));
        }
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
