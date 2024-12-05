<?php

namespace App\Livewire;

use App\Models\Beneficiary;
use Livewire\Component;

class BeneficiaryUpdate extends Component
{
    public $beneficiary;
    public $nombre, $ci, $complemento, $expedido, $estado, $idepro, $fecha_nacimiento, $total_activado, $gastos_judiciales, $saldo_credito, $monto_recuperado, $fecha_activacion, $plazo_credito, $tasa_interes, $departamento;
    public $showModal = false;
    public $confirmingSave = false;

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'ci' => 'required|string|max:20',
        'complemento' => 'nullable|string|max:10',
        'expedido' => 'required|string|max:10',
        'estado' => 'required|string|max:50',
        'idepro' => 'required|string|max:50',
        'fecha_nacimiento' => 'required|date',
        'total_activado' => 'required|numeric',
        'gastos_judiciales' => 'required|numeric',
        'saldo_credito' => 'required|numeric',
        'monto_recuperado' => 'required|numeric',
        'fecha_activacion' => 'required|date',
        'plazo_credito' => 'required|integer',
        'tasa_interes' => 'required|numeric',
        'departamento' => 'required|string|max:50',
    ];

    public function mount(Beneficiary $beneficiary)
    {
        $this->beneficiary = $beneficiary;
        $this->fill($beneficiary->toArray());
    }

    public function update()
    {
        $this->validate();

        $this->beneficiary->update([
            'nombre' => $this->nombre,
            'ci' => $this->ci,
            'complemento' => $this->complemento,
            'expedido' => $this->expedido,
            'estado' => $this->estado,
            'idepro' => $this->idepro,
            'fecha_nacimiento' => $this->fecha_nacimiento,
            'total_activado' => $this->total_activado,
            'gastos_judiciales' => $this->gastos_judiciales,
            'saldo_credito' => $this->saldo_credito,
            'monto_recuperado' => $this->monto_recuperado,
            'fecha_activacion' => $this->fecha_activacion,
            'plazo_credito' => $this->plazo_credito,
            'tasa_interes' => $this->tasa_interes,
            'departamento' => $this->departamento,
            'user_id' => auth()->id(),
        ]);

        $this->showModal = false;
        $this->confirmingSave = false;

        return redirect(request()->header('Referer'));
    }


    public function render()
    {
        return view('livewire.beneficiary-update');
    }
}
