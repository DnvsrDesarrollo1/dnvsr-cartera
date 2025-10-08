<?php

namespace App\Livewire\Projects;

use Livewire\Component;

class ListByDepartament extends Component
{
    public $listOpen = false;

    public $departament;

    public $projectsFromDepartaments;

    public function mount($departament)
    {
        $this->departament = $departament;
        $this->projectsFromDepartaments = \App\Models\Beneficiary::where('departamento', $this->departament)->distinct('proyecto')->get();
    }

    public function render()
    {
        return view('livewire.projects.list-by-departament');
    }
}
