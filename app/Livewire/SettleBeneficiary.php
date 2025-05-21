<?php

namespace App\Livewire;

use App\Models\Beneficiary;
use App\Models\Settlement;
use App\Traits\FinanceTrait;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SettleBeneficiary extends Component
{
    use WithFileUploads, FinanceTrait;

    public $anexos = [];
    public $beneficiary;
    public $settlement;

    public $estados = [
        'pendiente',
        'aprobado',
        'ejecutado'
    ];

    protected $rules = [
        'anexos.*' => 'nullable|file|mimes:pdf,jpg,png,doc,docx,xlsx,xls,txt,csv|max:20480',
    ];

    public $settleModal = false;
    public $capSettle,
        $capDifSettle,
        $intSettle,
        $intDifSettle,
        $intDevSettle,
        $segSettle,
        $segDevSettle,
        $otrosSettle,
        $totalSettle = '',
        $numtramite,
        $comentarios,
        $observaciones,
        $plan_de_pagos,
        $estado,
        $descuento,
        $diasMora;

    public $comprobante, $fecha_comprobante;

    public function render()
    {
        if (
            $this->totalSettle != ''
        ) {
            $this->capSettle = $this->totalSettle - ($this->capDifSettle + $this->intSettle + $this->intDifSettle + $this->intDevSettle + $this->segSettle +
                $this->segDevSettle + $this->otrosSettle);
        }
        return view('livewire.settle-beneficiary');
    }

    public function mount(Beneficiary $beneficiary)
    {
        $this->beneficiary = $beneficiary;
        $this->settlement = $beneficiary->settlement ?? new Settlement();


        $this->anexos = $this->settlement->id != null ? json_decode($this->settlement->anexos, true) : [];

        if ($this->estado == 'aprobado') {
            $this->fill($this->settlement);
        }

        $plan = $this->beneficiary->getCurrentPlan('CANCELADO', '!=');

        $this->plan_de_pagos = $this->beneficiary->getCurrentPlan();

        $this->diasMora = $this->calcularDiasMora($this->beneficiary);

        $this->capSettle = round($this->settlement->id != null ? $this->settlement->capital_final : $this->beneficiary->saldo_credito, 2);
        $this->capDifSettle = round($this->settlement->id != null ? $this->settlement->capital_diferido : $this->beneficiary->helpers()->where('estado', 'ACTIVO')->sum('capital') ?? 0, 2);

        $this->intSettle = $this->settlement->id != null ? $this->settlement->interes : $this->calcularInteresAcumulado(
            $this->beneficiary->saldo_credito,
            $this->diasMora,
            ($this->beneficiary->tasa_interes / 100)
        );

        $this->intDifSettle = round($this->settlement->id != null ? $this->settlement->interes_diferido : $this->beneficiary->helpers()->where('estado', 'ACTIVO')->sum('interes') ?? 0, 2);
        $this->intDevSettle = round($this->settlement->id != null ? $this->settlement->interes_devengado : $plan->sum('prppggral'), 2);
        $this->segSettle = round($this->settlement->id != null ? $this->settlement->seguro : $plan->where('fecha_ppg', '<=', now())->sum('prppgsegu'), 2);
        $this->segDevSettle = round($this->settlement->id != null ? $this->settlement->seguro_devengado : $plan->sum('prppgcarg'), 2);
        $this->otrosSettle = round($this->settlement->id != null ? $this->settlement->otros : $plan->sum('prppgotro'), 2);
        $this->descuento = round($this->settlement->id != null ? $this->settlement->descuento : 0, 2);
        $this->estado =  $this->settlement->id != null ? $this->settlement->estado : 'pendiente';
        $this->comentarios =  $this->settlement->id != null ? $this->settlement->comentarios :  '';
        $this->observaciones =  $this->settlement->id != null ? $this->settlement->observaciones : '';
    }

    public function save()
    {
        // Filter out non-file items from anexos before validation
        $this->anexos = array_filter($this->anexos, function ($anexo) {
            return is_object($anexo) && method_exists($anexo, 'storeAs');
        });

        $this->validate();

        // Procesar anexos
        $anexosPaths = $this->processAnexos();

        // Crear o actualizar el settlement
        $this->settlement = Settlement::updateOrCreate(
            ['beneficiary_id' => $this->beneficiary->id],
            [
                'capital_inicial' => $this->beneficiary->monto_activado,
                'capital_final' => $this->capSettle,
                'capital_diferido' => $this->capDifSettle,
                'interes' => $this->intSettle,
                'interes_devengado' => $this->intDevSettle,
                'interes_diferido' => $this->intDifSettle,
                'seguro' => $this->segSettle,
                'seguro_devengado' => $this->segDevSettle,
                'gastos_judiciales' => $this->beneficiary->spends->where('criterio', 'LIKE', '%JUDICIAL%')->where('estado', 'ACTIVO')->sum('monto'),
                'gastos_administrativos' => $this->beneficiary->spends->where('criterio', 'LIKE', '%ADMINISTRA%')->where('estado', 'ACTIVO')->sum('monto'),
                'otros' => $this->otrosSettle,
                'descuento' => $this->descuento,
                'plan_de_pagos' => $this->plan_de_pagos ? $this->plan_de_pagos->pluck('id', 'estado') : [],
                'estado' => $this->estado,
                'comentarios' => $this->comentarios, // por parte del liquidador
                'observaciones' => $this->observaciones, // de la contraparte
                'anexos' => json_encode($anexosPaths),
                'user_id' => Auth::user()->id,
            ]
        );

        if ($this->comprobante != '' && $this->comprobante != null) {
            $this->createPayment(
                $this->comprobante,
                1,
                $this->beneficiary->idepro,
                20,
                1,
                $this->fecha_comprobante,
                null,
                'CAPITAL',
                $this->capSettle,
                'VENTANILLA: ' . Auth::user()->name,
                $this->beneficiary->getFirstQuote()->prppgnpag,
                null,
                'DEPOSITO DE LIQUIDACION'
            );

            $this->createPayment(
                $this->comprobante,
                2,
                $this->beneficiary->idepro,
                20,
                1,
                $this->fecha_comprobante,
                null,
                'CAPITAL DIFERIDO',
                $this->capDifSettle,
                'VENTANILLA: ' . Auth::user()->name,
                $this->beneficiary->getFirstQuote()->prppgnpag,
                null,
                'DEPOSITO DE LIQUIDACION'
            );

            $this->createPayment(
                $this->comprobante,
                3,
                $this->beneficiary->idepro,
                20,
                2,
                $this->fecha_comprobante,
                null,
                'INTERES',
                $this->intSettle,
                'VENTANILLA: ' . Auth::user()->name,
                $this->beneficiary->getFirstQuote()->prppgnpag,
                null,
                'DEPOSITO DE LIQUIDACION'
            );

            $this->createPayment(
                $this->comprobante,
                4,
                $this->beneficiary->idepro,
                20,
                2,
                $this->fecha_comprobante,
                null,
                'INTERES DIFERIDO',
                $this->intDifSettle,
                'VENTANILLA: ' . Auth::user()->name,
                $this->beneficiary->getFirstQuote()->prppgnpag,
                null,
                'DEPOSITO DE LIQUIDACION'
            );

            $this->createPayment(
                $this->comprobante,
                5,
                $this->beneficiary->idepro,
                20,
                2,
                $this->fecha_comprobante,
                null,
                'INTERES DEVENGADO',
                $this->intDevSettle,
                'VENTANILLA: ' . Auth::user()->name,
                $this->beneficiary->getFirstQuote()->prppgnpag,
                null,
                'DEPOSITO DE LIQUIDACION'
            );

            $this->createPayment(
                $this->comprobante,
                6,
                $this->beneficiary->idepro,
                21,
                37,
                $this->fecha_comprobante,
                null,
                'SEGURO',
                $this->segSettle,
                'VENTANILLA: ' . Auth::user()->name,
                $this->beneficiary->getFirstQuote()->prppgnpag,
                null,
                'DEPOSITO DE LIQUIDACION'
            );

            $this->createPayment(
                $this->comprobante,
                7,
                $this->beneficiary->idepro,
                21,
                37,
                $this->fecha_comprobante,
                null,
                'SEGURO DEVENGADO',
                $this->segDevSettle,
                'VENTANILLA: ' . Auth::user()->name,
                $this->beneficiary->getFirstQuote()->prppgnpag,
                null,
                'DEPOSITO DE LIQUIDACION'
            );

            $this->createPayment(
                $this->comprobante,
                8,
                $this->beneficiary->idepro,
                21,
                37,
                $this->fecha_comprobante,
                null,
                'OTROS',
                $this->otrosSettle,
                'VENTANILLA: ' . Auth::user()->name,
                $this->beneficiary->getFirstQuote()->prppgnpag,
                null,
                'DEPOSITO DE LIQUIDACION'
            );

            $this->createPayment(
                $this->comprobante,
                9,
                $this->beneficiary->idepro,
                21,
                37,
                $this->fecha_comprobante,
                null,
                'DESCUENTO AL GASTO ADMINISTRATIVO',
                $this->descuento,
                'VENTANILLA: ' . Auth::user()->name,
                $this->beneficiary->getFirstQuote()->prppgnpag,
                null,
                'DEPOSITO DE LIQUIDACION'
            );

            $this->createVoucher(
                null,
                'PAGO DE LIQUIDACION',
                $this->fecha_comprobante,
                null,
                ($this->capSettle) + ($this->capDifSettle) + ($this->intSettle) + ($this->intDevSettle) + ($this->intDifSettle) + ($this->segSettle) + ($this->segDevSettle) + ($this->otrosSettle),
                $this->beneficiary->getFirstQuote()->prppgnpag,
                $this->beneficiary->idepro,
                $this->comprobante,
                null,
                'DEPOSITO DE LIQUIDACION'
            );

            $this->beneficiary->update([
                'saldo_credito' => $this->beneficiary->saldo_credito - ($this->capSettle),
                'estado' => 'CANCELADO',
            ]);

            foreach ($this->beneficiary->helpers as $h) {
                $h->update([
                    'estado' => 'CANCELADO',
                    'user_id' => Auth::user()->id,
                ]);
            }

            foreach ($this->beneficiary->earns as $e) {
                $e->update([
                    'estado' => 'CANCELADO'
                ]);
            }

            foreach ($this->beneficiary->spends as $s) {
                $s->update([
                    'estado' => 'CANCELADO',
                ]);
            }

            foreach ($this->beneficiary->getCurrentPlan('CANCELADO', '!=') as $h) {
                $h->update([
                    'estado' => 'CANCELADO',
                    'user_id' => Auth::user()->id,
                ]);
            }

            $this->settlement->update([
                'estado' => 'ejecutado',
            ]);

        }

        if ($this->settlement->id != null && $this->settlement->estado == 'EJECUTADO') {
            $this->beneficiary->update([
                'saldo_credito' => 0,
                'estado' => 'CANCELADO',
            ]);

            $this->beneficiary->helpers()->update([
                'estado' => 'CANCELADO',
                'user_id' => Auth::user()->id,
            ]);

            foreach ($this->beneficiary->helpers as $helper) {
                $helper->update([
                    'estado' => 'CANCELADO',
                    'user_id' => Auth::user()->id,
                ]);
            }

            foreach ($this->beneficiary->earns as $earn) {
                $earn->update([
                    'estado' => 'CANCELADO',
                    'user_id' => Auth::user()->id,
                ]);
            }

            foreach ($this->beneficiary->spends as $sp) {
                $sp->update([
                    'estado' => 'CANCELADO',
                    'user_id' => Auth::user()->id,
                ]);
            }

            foreach ($this->beneficiary->getCurrentPlan() as $p) {
                $p->update([
                    'estado' => 'CANCELADO',
                    'prppgmpag' => 'L'
                ]);
            }
        }

        $this->settlement->refresh();

        $this->anexos = $this->settlement->id != null ? json_decode($this->settlement->anexos, true) : [];

        session()->flash('message', 'Liquidación guardada exitosamente');
    }

    public function delete()
    {
        $this->settlement->delete();
        //reload whole page
        return redirect()->back();
    }

    protected function processAnexos()
    {
        $paths = $this->settlement->anexos ? json_decode($this->settlement->anexos, true) : [];

        if ($this->anexos) {
            foreach ($this->anexos as $anexo) {
                if (is_object($anexo) && method_exists($anexo, 'storeAs')) {
                    // Es un archivo nuevo subido, almacenar con su nombre original
                    $originalName = $anexo->getClientOriginalName();
                    $paths[] = $anexo->storeAs('settlements', $originalName);
                } elseif (is_string($anexo)) {
                    // Es un path existente
                    $paths[] = $anexo;
                }
            }
        }

        return $paths;
    }

    public function removeAnexo($index)
    {
        if (isset($this->anexos[$index])) {
            $anexo = $this->anexos[$index];

            if (is_string($anexo)) {
                // Eliminar archivo del storage si existe
                Storage::delete($anexo);
            }

            unset($this->anexos[$index]);
            $this->anexos = array_values($this->anexos);
        }
    }

    private function calcularInteresAcumulado($capitalInicial, $dias, $tasaInteres)
    {
        $interesAcumulado = ($capitalInicial * $dias * $tasaInteres) / 360;
        return round($interesAcumulado, 2);
    }

    private function calcularDiasMora(Beneficiary $beneficiary)
    {
        $listaPlan = $beneficiary->getCurrentPlan('INACTIVO', '!=')->where('fecha_ppg', '<=', now())->sortBy('fecha_ppg');

        $arrayComparativo = array();

        foreach ($listaPlan as $key => $value) {
            $pago = $beneficiary->vouchers->where('numpago', $value->prppgnpag)->first();

            $arrayComparativo[] = array(
                (string)$value->prppgnpag,
                (string)$value->fecha_ppg,
                $pago ? (string)$pago->fecha_pago : (string)now()->format('Y-m-d'),
                $pago ? \Carbon\Carbon::parse($value->fecha_ppg)->diffInDays($pago->fecha_pago) : \Carbon\Carbon::parse($value->fecha_ppg)->diffInDays(now())
            );

            if ($arrayComparativo[$key][3] < 0) {
                unset($arrayComparativo[$key]);
            }
        }

        //dd($arrayComparativo);

        return floor(collect($arrayComparativo)->max(3) + collect($arrayComparativo)->min(3));
    }
}
