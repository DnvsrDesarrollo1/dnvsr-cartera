<?php

namespace App\Livewire;

use App\Models\Beneficiary;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class BeneficiaryUpdate extends Component
{
    public $beneficiary;

    public $nombre;

    public $ci;

    public $complemento;

    public $expedido;

    public $estado;

    public $idepro;

    public $cod_fondesif;

    public $fecha_nacimiento;

    public $monto_credito;

    public $total_activado;

    public $monto_activado;

    public $gastos_judiciales;

    public $saldo_credito;

    public $monto_recuperado;

    public $fecha_activacion;

    public $plazo_credito;

    public $tasa_interes;

    public $departamento;

    public $seguro;

    public $cuota;

    public $benModal = false;

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'ci' => 'required|string|max:20',
        'complemento' => 'nullable|string|max:10',
        'expedido' => 'required|string|max:10',
        'estado' => 'required|string|max:50',
        'idepro' => 'required|string|max:50',
        'cod_fondesif' => '',
        'fecha_nacimiento' => 'nullable|date',
        'total_activado' => 'required|numeric',
        'monto_activado' => 'required|numeric',
        'monto_credito' => 'required|numeric',
        'gastos_judiciales' => 'required|numeric',
        'saldo_credito' => 'required|numeric',
        'monto_recuperado' => 'required|numeric',
        'fecha_activacion' => 'required|date',
        'plazo_credito' => 'required|integer',
        'tasa_interes' => 'required|numeric',
        'departamento' => 'required|string|max:50',
        'seguro' => 'required|numeric',
    ];

    public function mount(Beneficiary $beneficiary)
    {
        // Eager load todas las relaciones necesarias para evitar N+1
        $this->beneficiary = $beneficiary->load([
            'insurance',
            'plans' => function ($query) {
                $query->where('estado', '!=', 'INACTIVO')->orderBy('fecha_ppg', 'asc');
            },
            'readjustments' => function ($query) {
                $query->where('estado', '!=', 'INACTIVO')->orderBy('fecha_ppg', 'asc');
            }
        ]);

        $this->fill($beneficiary->toArray());

        // Calcular seguro usando la relación ya cargada
        $this->seguro = $beneficiary->insurance?->tasa_seguro ?? 0;

        if ($this->seguro == 0) {
            // Cachear el resultado de getCurrentPlan para evitar consultas duplicadas
            $currentPlan = $this->beneficiary->getCurrentPlan('INACTIVO', '!=');

            if ($currentPlan->isNotEmpty()) {
                $totalCapital = $currentPlan->sum('prppgcapi');
                if ($totalCapital > 0) {
                    $this->seguro = ($currentPlan->first()->prppgsegu / $totalCapital) * 100;
                }
            }
        }

        $this->seguro = number_format($this->seguro, 3);

        // Reutilizar el plan ya cargado para obtener la cuota
        $currentPlan = $currentPlan ?? $this->beneficiary->getCurrentPlan('INACTIVO', '!=');
        $this->cuota = $currentPlan->first()?->prppgcuota ?? 0;
    }

    public function update()
    {
        $this->validate();

        if ($this->idepro != $this->beneficiary->idepro) {
            // Eager load todas las relaciones que se van a actualizar
            $this->beneficiary->loadMissing([
                'plans',
                'helpers',
                'spends',
                'insurance',
                'earns',
                'vouchers',
                'payments'
            ]);

            // Actualizar planes activos
            foreach ($this->beneficiary->getCurrentPlan('INACTIVO', '!=') as $p) {
                $p->update([
                    'idepro' => $this->idepro,
                ]);
            }

            // Actualizar relaciones usando las ya cargadas
            $this->beneficiary->helpers()->update([
                'idepro' => $this->idepro,
            ]);

            $this->beneficiary->spends()->update([
                'idepro' => $this->idepro,
            ]);

            $this->beneficiary->insurance()->update([
                'idepro' => $this->idepro,
            ]);

            $this->beneficiary->earns()->update([
                'idepro' => $this->idepro,
            ]);

            $this->beneficiary->vouchers()->update([
                'numprestamo' => $this->idepro,
            ]);

            $this->beneficiary->payments()->update([
                'numprestamo' => $this->idepro,
            ]);
        }

        $this->beneficiary->update([
            'nombre' => $this->nombre,
            'ci' => $this->ci,
            'complemento' => $this->complemento,
            'expedido' => $this->expedido,
            'estado' => $this->estado,
            'idepro' => $this->idepro,
            'cod_fondesif' => $this->cod_fondesif,
            'fecha_nacimiento' => $this->fecha_nacimiento,
            'monto_credito' => $this->monto_credito,
            'monto_activado' => $this->monto_activado,
            'total_activado' => $this->total_activado,
            'gastos_judiciales' => $this->gastos_judiciales,
            'saldo_credito' => $this->saldo_credito,
            'monto_recuperado' => $this->monto_recuperado,
            'fecha_activacion' => $this->fecha_activacion,
            'plazo_credito' => $this->plazo_credito,
            'tasa_interes' => $this->tasa_interes,
            'departamento' => $this->departamento,
            'user_id' => \Illuminate\Support\Facades\Auth::user()->id,
        ]);

        \App\Models\Insurance::updateOrCreate(
            ['idepro' => $this->idepro],
            [
                'tasa_seguro' => $this->seguro,
            ]
        );

        return redirect()->route('beneficiario.show', ['cedula' => $this->beneficiary->ci]);
    }

    public function delete()
    {
        $idepro = $this->beneficiary->idepro;

        \Illuminate\Support\Facades\DB::transaction(function () use ($idepro) {
            \App\Models\BeneficiaryDeleted::create($this->beneficiary->toArray());

            \App\Models\Insurance::where('idepro', $idepro)->delete();
            \App\Models\Plan::where('idepro', $idepro)->delete();
            \App\Models\Readjustment::where('idepro', $idepro)->delete();
            \App\Models\Spend::where('idepro', $idepro)->delete();
            \App\Models\Earn::where('idepro', $idepro)->delete();
            \App\Models\Voucher::where('numprestamo', $idepro)->delete();
            \App\Models\Payment::where('numprestamo', $idepro)->delete();
            \App\Models\Helper::where('idepro', $idepro)->delete();

            $this->beneficiary->delete();
        });

        \Illuminate\Support\Facades\Log::info('Beneficiary with idepro ' . $idepro . ' has been deleted by ' . \Illuminate\Support\Facades\Auth::user()->name);

        return redirect()->route('beneficiario.index');
    }

    public function render()
    {
        return view('livewire.beneficiary-update');
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
