<?php

namespace App\Livewire\Projects;

use App\Models\Beneficiary;
use App\Models\Project;
use Livewire\Component;

class BeneficiariesByStatus extends Component
{
    public $project;
    public $projectBeneficiaries;

    public $statusFilter = '';

    public function mount(Project $project)
    {
        $this->project = $project;
    }

    public function render()
    {
        $beneficiaries = Beneficiary::query()
            ->where('proyecto', $this->project->nombre_proyecto)
            ->when($this->statusFilter, function ($query) {
                return $query->where('estado', $this->statusFilter);
            })
            ->orderBy('nombre', 'asc')
            ->get();

        $statuses = Beneficiary::where('proyecto', $this->project->nombre_proyecto)
            ->select('estado')
            ->distinct()
            ->pluck('estado');

        return view('livewire.projects.beneficiaries-by-status', [
            'beneficiaries' => $beneficiaries,
            'statuses' => $statuses
        ]);
    }

    public function placeholder()
    {
        return <<<'HTML'
        <div class="flex items-center justify-center w-full h-48">
            <div class="flex justify-center items-center space-x-1 text-gray-700">
                <svg fill='none' class="w-6 h-6 animate-spin" viewBox="0 0 32 32" xmlns='http://www.w3.org/2000/svg'>
                    <path clip-rule='evenodd'
                        d='M15.165 8.53a.5.5 0 01-.404.58A7 7 0 1023 16a.5.5 0 011 0 8 8 0 11-9.416-7.874.5.5 0 01.58.404z'
                        fill='currentColor' fill-rule='evenodd' />
                </svg>
            </div>
        </div>
        HTML;
    }
}
