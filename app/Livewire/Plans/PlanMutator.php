<?php

namespace App\Livewire\Plans;

use Livewire\Attributes\Validate;
use App\Models\Beneficiary;
use Livewire\Component;

class PlanMutator extends Component
{
    public $mutatorModal = false;

    public $beneficiary;
    public $plan;

    #[Validate('required|numeric|min:1')]
    public $affectedQuotas = 1;

    #[Validate('required|numeric|min:100')]
    public $amountToInsert = 100;
    public $amountPerQuota;
    public $amountCriteria = 'GASTOS JUDICIALES';

    public $maxQuotas;

    public function mount(Beneficiary $beneficiary)
    {
        $this->beneficiary = $beneficiary->load('plans');
    }

    public function getNQuotas()
    {
        $this->plan = $this->beneficiary
            ->plans
            ->filter(function ($quota) {
                return $quota->estado != 'CANCELADO';
            })
            ->sortBy('fecha_ppg')
            ->take($this->affectedQuotas);
    }

    public function render()
    {
        if ($this->affectedQuotas == '' || $this->affectedQuotas < 1) {
            $this->affectedQuotas = 1;
        }

        if ($this->amountToInsert == '') {
            $this->amountToInsert = 100;
        }

        $this->maxQuotas = $this->plan = $this->beneficiary
            ->plans
            ->filter(function ($quota) {
                return $quota->estado != 'CANCELADO';
            })
            ->sortBy('fecha_ppg')
            ->count();

        $this->getNQuotas();

        $this->amountPerQuota = round($this->amountToInsert / $this->affectedQuotas, 4);

        return view('livewire.plans.plan-mutator');
    }

    public function mutate()
    {
        $this->validate();

        \App\Models\Spend::create([
            'idepro' => $this->beneficiary->idepro,
            'criterio' => $this->amountCriteria,
            'monto' => $this->amountToInsert,
            'estado' => 'ACTIVO',
        ]);

        $this->plan->each(function ($quota) {
            $quota->update([
                'prppgotro' => $quota->prppgotro + $this->amountPerQuota,
                'prppgtota' => $quota->prppgtota + $this->amountPerQuota,
            ]);
        });

        session()->flash('success', "Mutación aplicada exitosamente, verificar el nuevo plan de pagos.");
    }

    public function placeholder()
    {
        return <<<'HTML'
            <div class="flex items-center justify-center p-4">
                <div class="animate-spin h-6 w-6 border-2 border-blue-500 border-t-transparent rounded-full"></div>
                <span class="ml-3 text-gray-600 text-sm font-light">Cargando...</span>
            </div>
        HTML;
    }
}
