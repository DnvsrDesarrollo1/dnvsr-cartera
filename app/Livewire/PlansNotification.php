<?php

namespace App\Livewire;

use Livewire\Component;

class PlansNotification extends Component
{
    public $openMora = false;

    public $title = '';

    public $nVencidos = 0;

    public $lVencidos;

    public $lBeneficiarios;

    public $lProyectos;

    public $lSettlements;

    public function openModal()
    {
        $this->openMora = true;
    }

    public function closeModal()
    {
        $this->openMora = false;
    }

    public function render()
    {
        // Primero obtenemos solo los IDs de planes vencidos
        $this->lVencidos = \App\Models\Plan::where('estado', 'VENCIDO')
            ->where('prppgmpag', 'SI')
            ->whereNotNull('prppgmpag')
            ->distinct('idepro')
            ->pluck('idepro');

        // Obtenemos todos los beneficiarios vencidos en una sola consulta con los datos necesarios
        $this->lBeneficiarios = \App\Models\Beneficiary::whereIn('idepro', $this->lVencidos)
            ->where('estado', '!=', 'BLOQUEADO')
            ->where('estado', '!=', 'CANCELADO')
            ->orderBy('proyecto')
            ->get(['nombre', 'ci', 'proyecto']);

        // Obtenemos la lista única de proyectos directamente de los beneficiarios ya cargados
        $this->lProyectos = $this->lBeneficiarios->unique('proyecto')
            ->pluck('proyecto');

        // Pre-cargamos los contadores de beneficiarios por proyecto en una sola consulta
        $totalesPorProyecto = \App\Models\Beneficiary::whereIn('proyecto', $this->lProyectos)
            ->where('estado', '!=', 'BLOQUEADO')
            ->where('estado', '!=', 'CANCELADO')
            ->selectRaw('proyecto, COUNT(*) as total')
            ->groupBy('proyecto')
            ->pluck('total', 'proyecto');

        // Construimos la estructura final procesando los datos en memoria
        $this->lProyectos = $this->lBeneficiarios
            ->groupBy('proyecto')
            ->map(function ($group, $proyecto) use ($totalesPorProyecto) {
                $totalBeneficiarios = $totalesPorProyecto[$proyecto] ?? 0;
                $morosos = $group->count();
                $porcentajeMora = $totalBeneficiarios > 0 ? ($morosos / $totalBeneficiarios) * 100 : 0;

                return [
                    'morosos' => $morosos,
                    'total' => $totalBeneficiarios,
                    'porcentajeMora' => $porcentajeMora,
                    'listaBeneficiarios' => $group
                        ->map(fn ($item) => [
                            'nombre' => $item->nombre,
                            'ci' => $item->ci,
                        ])->toArray(),
                ];
            });

        $this->nVencidos = $this->lBeneficiarios->count();

        $this->lSettlements = \App\Models\Settlement::where('estado', 'pendiente')->orderBy('created_at', 'asc')->get();

        return view('livewire.plans-notification');
    }

    public function placeholder()
    {
        return <<< 'HTML'
                        <div class="flex justify-center items-center h-32">
                            <svg class="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    HTML;
    }
}
