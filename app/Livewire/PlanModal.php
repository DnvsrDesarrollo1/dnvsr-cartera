<?php

namespace App\Livewire;

use App\Models\Beneficiary;
use Livewire\Component;

class PlanModal extends Component
{
    public $planModal = false;

    public Beneficiary $beneficiary;

    public $title = '';

    public function mount(Beneficiary $beneficiary, string $title = '')
    {
        $this->beneficiary = $beneficiary;
        $this->title = $title;
    }

    public function render()
    {
        return view('livewire.plan-modal');
    }

    public function placeholder()
    {
        return <<< 'HTML'
            <div class="flex items-center justify-center p-4">
                <div class="animate-pulse h-6 w-6 bg-blue-500 rounded-full"></div>
                <span class="ml-3 text-gray-600 text-sm font-light">Cargando</span>
            </div>
        HTML;
    }
}
