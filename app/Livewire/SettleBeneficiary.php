<?php

namespace App\Livewire;

use App\Models\Beneficiary;
use App\Models\Settlement;
use App\Traits\FinanceTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class SettleBeneficiary extends Component
{
    use FinanceTrait, WithFileUploads;

    public $anexos = [];

    public $beneficiary;

    public $settlement;

    public $estados = [
        'pendiente',
        'aprobado',
        'ejecutado',
    ];

    protected $rules = [
        'anexos.*' => 'nullable|file|mimes:pdf,jpg,png,doc,docx,xlsx,xls,txt,csv|max:20480',
    ];

    public $settleModal = false;

    public $capSettle;

    public $capDifSettle;

    public $intSettle;

    public $intDifSettle;

    public $intDevSettle;

    public $segSettle;

    public $segDevSettle;

    public $gastosAdm;

    public $gastosJud;

    public $otrosSettle;

    public $totalSettle = '';

    public $numtramite;

    public $comentarios;

    public $observaciones;

    public $plan_de_pagos;

    public $estado;

    public $descuento;

    public $diasMora;

    public $comprobante = '';

    public $fecha_comprobante;

    public function render()
    {
        // Ensure totalSettle is numeric before calculations
        if (is_numeric($this->totalSettle) && $this->totalSettle !== '') {
            // Cast all values to float to ensure proper calculation
            $this->capSettle = (float) $this->totalSettle - (
                (float) ($this->capDifSettle ?? 0) +
                (float) ($this->intSettle ?? 0) +
                (float) ($this->intDifSettle ?? 0) +
                (float) ($this->intDevSettle ?? 0) +
                (float) ($this->segSettle ?? 0) +
                (float) ($this->segDevSettle ?? 0) +
                (float) ($this->gastosAdm ?? 0) +
                (float) ($this->gastosJud ?? 0) +
                (float) ($this->otrosSettle ?? 0)
            );

            // Round to 2 decimal places for currency
            $this->capSettle = round($this->capSettle, 2);
        }

        return view('livewire.settle-beneficiary');
    }

    public function mount(Beneficiary $beneficiary)
    {
        try {
            $this->beneficiary = $beneficiary;
            $this->settlement = $beneficiary->settlement ?? new Settlement;

            // Safely decode anexos with error handling
            try {
                $this->anexos = $this->settlement->id ? json_decode($this->settlement->anexos, true) ?? [] : [];
            } catch (\Exception $e) {
                $this->anexos = [];
            }

            if ($this->estado === 'aprobado') {
                $this->fill($this->settlement->toArray());
            }

            $plan = $this->beneficiary->getCurrentPlan('CANCELADO', '!=') ?? collect();
            $this->plan_de_pagos = $this->beneficiary->getCurrentPlan() ?? null;
            $this->diasMora = $this->calcularDiasMora($this->beneficiary);

            // Initialize settlement values with proper null checks and type casting
            $this->capSettle = round($this->settlement->id ?
                (float) $this->settlement->capital_final :
                (float) $this->beneficiary->saldo_credito, 2);

            $this->capDifSettle = round($this->settlement->id ?
                (float) $this->settlement->capital_diferido :
                (float) ($this->beneficiary->helpers()->where('estado', 'ACTIVO')->sum('capital') ?? 0), 2);

            $this->intSettle = $this->settlement->id ?
                (float) $this->settlement->interes :
                $this->calcularInteresAcumulado(
                    (float) $this->beneficiary->saldo_credito,
                    (int) $this->diasMora,
                    (float) ($this->beneficiary->tasa_interes / 100)
                );

            $this->intDifSettle = round($this->settlement->id ?
                (float) $this->settlement->interes_diferido :
                (float) ($this->beneficiary->helpers()->where('estado', 'ACTIVO')->sum('interes') ?? 0), 2);

            $this->intDevSettle = round($this->settlement->id ?
                (float) $this->settlement->interes_devengado :
                (float) ($plan->sum('prppggral') ?? 0), 2);

            $this->segSettle = round($this->settlement->id ?
                (float) $this->settlement->seguro :
                (float) ($plan->where('fecha_ppg', '<=', now())->sum('prppgsegu') ?? 0), 2);

            $this->segDevSettle = round($this->settlement->id ?
                (float) $this->settlement->seguro_devengado :
                (float) ($plan->sum('prppgcarg') ?? 0), 2);

            $this->gastosAdm = round($this->settlement->id ?
                (float) $this->settlement->gastos_administrativos :
                (float) ($this->beneficiary->spends()->where('criterio', 'LIKE', '%ADMIN%')->sum('monto') ?? 0), 2);

            $this->gastosJud = round($this->settlement->id ?
                (float) $this->settlement->gastos_judiciales :
                (float) ($this->beneficiary->spends()->where('criterio', 'LIKE', '%JUDIC%')->sum('monto') ?? 0), 2);

            $this->otrosSettle = round($this->settlement->id ?
                (float) $this->settlement->otros :
                (float) ($this->beneficiary->spends()->where('criterio', 'LIKE', '%NOTAR%')->sum('monto') ?? 0), 2);

            $this->descuento = round($this->settlement->id ?
                (float) $this->settlement->descuento : 0, 2);

            $this->estado = $this->settlement->id ?
                (string) $this->settlement->estado : 'pendiente';

            $this->comentarios = $this->settlement->id ?
                (string) $this->settlement->comentarios : '';

            $this->observaciones = $this->settlement->id ?
                (string) $this->settlement->observaciones : '';
        } catch (\Exception $e) {
            // Reset to default values on error
            $this->resetProperties();
            throw new \Exception('Error mounting SettleBeneficiary component: ' . $e->getMessage());
        }
    }

    // Helper method to reset properties to default values
    private function resetProperties()
    {
        $this->anexos = [];
        $this->capSettle = 0;
        $this->capDifSettle = 0;
        $this->intSettle = 0;
        $this->intDifSettle = 0;
        $this->intDevSettle = 0;
        $this->segSettle = 0;
        $this->segDevSettle = 0;
        $this->gastosAdm = 0;
        $this->gastosJud = 0;
        $this->otrosSettle = 0;
        $this->descuento = 0;
        $this->estado = 'pendiente';
        $this->comentarios = '';
        $this->observaciones = '';
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
                'capital_inicial' => $this->beneficiary->monto_activado ?? 0,
                'capital_final' => $this->capSettle ?? 0,
                'capital_diferido' => $this->capDifSettle ?? 0,
                'interes' => $this->intSettle ?? 0,
                'interes_devengado' => $this->intDevSettle ?? 0,
                'interes_diferido' => $this->intDifSettle ?? 0,
                'seguro' => $this->segSettle ?? 0,
                'seguro_devengado' => $this->segDevSettle ?? 0,
                'gastos_judiciales' => $this->gastosJud ?? 0,
                'gastos_administrativos' => $this->gastosAdm ?? 0,
                'otros' => $this->otrosSettle ?? 0,
                'descuento' => $this->descuento ?? 0,
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
                strpos(strtoupper($this->comentarios), 'CONTADO') !== false ? ('PAGO CONTADO: ' . Auth::user()->name) : ('LIQUIDACION TOTAL: ' . Auth::user()->name),
                $this->beneficiary->getFirstQuote()->prppgnpag ?? 0,
                null,
                'CANCELACION/LIQUIDACION'
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
                strpos(strtoupper($this->comentarios), 'CONTADO') !== false ? ('PAGO CONTADO: ' . Auth::user()->name) : ('LIQUIDACION TOTAL: ' . Auth::user()->name),
                $this->beneficiary->getFirstQuote()->prppgnpag ?? 0,
                null,
                'CANCELACION/LIQUIDACION'
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
                strpos(strtoupper($this->comentarios), 'CONTADO') !== false ? ('PAGO CONTADO: ' . Auth::user()->name) : ('LIQUIDACION TOTAL: ' . Auth::user()->name),
                $this->beneficiary->getFirstQuote()->prppgnpag ?? 0,
                null,
                'CANCELACION/LIQUIDACION'
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
                strpos(strtoupper($this->comentarios), 'CONTADO') !== false ? ('PAGO CONTADO: ' . Auth::user()->name) : ('LIQUIDACION TOTAL: ' . Auth::user()->name),
                $this->beneficiary->getFirstQuote()->prppgnpag ?? 0,
                null,
                'CANCELACION/LIQUIDACION'
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
                strpos(strtoupper($this->comentarios), 'CONTADO') !== false ? ('PAGO CONTADO: ' . Auth::user()->name) : ('LIQUIDACION TOTAL: ' . Auth::user()->name),
                $this->beneficiary->getFirstQuote()->prppgnpag ?? 0,
                null,
                'CANCELACION/LIQUIDACION'
            );

            $this->createPayment(
                $this->comprobante,
                6,
                $this->beneficiary->idepro,
                21,
                37,
                $this->fecha_comprobante,
                null,
                'SEGURO DESGRAVAMEN',
                $this->segSettle,
                strpos(strtoupper($this->comentarios), 'CONTADO') !== false ? ('PAGO CONTADO: ' . Auth::user()->name) : ('LIQUIDACION TOTAL: ' . Auth::user()->name),
                $this->beneficiary->getFirstQuote()->prppgnpag ?? 0,
                null,
                'CANCELACION/LIQUIDACION'
            );

            $this->createPayment(
                $this->comprobante,
                7,
                $this->beneficiary->idepro,
                21,
                37,
                $this->fecha_comprobante,
                null,
                'SEGURO DESGRAVAMEN DEVENGADO',
                $this->segDevSettle,
                strpos(strtoupper($this->comentarios), 'CONTADO') !== false ? ('PAGO CONTADO: ' . Auth::user()->name) : ('LIQUIDACION TOTAL: ' . Auth::user()->name),
                $this->beneficiary->getFirstQuote()->prppgnpag ?? 0,
                null,
                'CANCELACION/LIQUIDACION'
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
                strpos(strtoupper($this->comentarios), 'CONTADO') !== false ? ('PAGO CONTADO: ' . Auth::user()->name) : ('LIQUIDACION TOTAL: ' . Auth::user()->name),
                $this->beneficiary->getFirstQuote()->prppgnpag ?? 0,
                null,
                'CANCELACION/LIQUIDACION'
            );

            $this->createPayment(
                $this->comprobante,
                8,
                $this->beneficiary->idepro,
                21,
                37,
                $this->fecha_comprobante,
                null,
                'GASTOS ADMINISTRATIVOS',
                $this->gastosAdm,
                strpos(strtoupper($this->comentarios), 'CONTADO') !== false ? ('PAGO CONTADO: ' . Auth::user()->name) : ('LIQUIDACION TOTAL: ' . Auth::user()->name),
                $this->beneficiary->getFirstQuote()->prppgnpag ?? 0,
                null,
                'CANCELACION/LIQUIDACION'
            );

            $this->createPayment(
                $this->comprobante,
                8,
                $this->beneficiary->idepro,
                21,
                37,
                $this->fecha_comprobante,
                null,
                'GASTOS JUDICIALES',
                $this->gastosJud,
                strpos(strtoupper($this->comentarios), 'CONTADO') !== false ? ('PAGO CONTADO: ' . Auth::user()->name) : ('LIQUIDACION TOTAL: ' . Auth::user()->name),
                $this->beneficiary->getFirstQuote()->prppgnpag ?? 0,
                null,
                'CANCELACION/LIQUIDACION'
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
                strpos(strtoupper($this->comentarios), 'CONTADO') !== false ? ('PAGO CONTADO: ' . Auth::user()->name) : ('LIQUIDACION TOTAL: ' . Auth::user()->name),
                $this->beneficiary->getFirstQuote()->prppgnpag ?? 0,
                null,
                'CANCELACION/LIQUIDACION'
            );

            $this->createVoucher(
                strpos(strtoupper($this->comentarios), 'CONTADO') !== false ? ('PAGO CONTADO: ' . Auth::user()->name) : ('LIQUIDACION TOTAL: ' . Auth::user()->name),
                'CANCELACION/LIQUIDACION',
                $this->fecha_comprobante,
                null,
                ($this->capSettle) + ($this->capDifSettle) + ($this->intSettle) + ($this->intDevSettle) + ($this->intDifSettle) + ($this->segSettle) + ($this->segDevSettle) + ($this->otrosSettle) + ($this->gastosAdm) + ($this->gastosJud),
                $this->beneficiary->getFirstQuote()->prppgnpag ?? 0,
                $this->beneficiary->idepro,
                $this->comprobante,
                null,
                'DEPOSITO POR CANCELACION/LIQUIDACION'
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
                    'estado' => 'CANCELADO',
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
                    'prppgmpag' => 'L',
                    'user_id' => Auth::user()->id,
                ]);
            }

            $this->settlement->update([
                'estado' => 'ejecutado',
            ]);
        }

        if ($this->settlement->id != null && $this->settlement->estado == 'ejecutado') {
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
                    'prppgmpag' => 'L',
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
        if ($dias <= 0) {
            return 0;
        }

        $interesAcumulado = ($capitalInicial * $dias * $tasaInteres) / 360;

        return round($interesAcumulado, 2);
    }

    private function calcularDiasMora(Beneficiary $beneficiary)
    {
        $listaPlan = $beneficiary->getCurrentPlan('CANCELADO', '!=')->first() ?? null;

        if ($listaPlan == null) {
            return 0;
        }

        return round(\Carbon\Carbon::parse($listaPlan->fecha_ppg)->diffInDays(now()), 0);
    }

    public function placeholder()
    {
        return <<< 'HTML'
            <div class="flex items-center justify-center p-4">
                <div class="animate-pulse h-2 w-2 bg-blue-500 rounded-full"></div>
                <span class="ml-3 text-gray-600 text-sm font-light">Cargando</span>
            </div>
        HTML;
    }
}
