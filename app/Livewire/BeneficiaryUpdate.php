<?php

namespace App\Livewire;

use App\Models\Beneficiary;
use Livewire\Component;

class BeneficiaryUpdate extends Component
{
    public $beneficiary;
    public $nombre, $ci, $complemento, $expedido, $estado, $idepro, $fecha_nacimiento, $total_activado, $monto_activado,
        $gastos_judiciales, $saldo_credito, $monto_recuperado, $fecha_activacion, $plazo_credito, $tasa_interes,
        $departamento, $seguro;
    public $cuota;

    public $benModal = false;

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'ci' => 'required|string|max:20',
        'complemento' => 'nullable|string|max:10',
        'expedido' => 'required|string|max:10',
        'estado' => 'required|string|max:50',
        'idepro' => 'required|string|max:50',
        'fecha_nacimiento' => 'required|date',
        'total_activado' => 'required|numeric',
        'monto_activado' => 'required|numeric',
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
        $this->beneficiary = $beneficiary;
        $this->fill($beneficiary->toArray());
        $this->seguro = ($beneficiary->insurance()->exists()) ? $beneficiary->insurance->tasa_seguro : 0;
        if ($this->seguro == 0) {
            $this->seguro = ($this->beneficiary->hasPlan())
                ?
                ($this->beneficiary->getCurrentPlan('INACTIVO', '!=')->first()->prppgsegu  > 0 ?: 0.0001 / $beneficiary->saldo_credito) * 100 : 0;
        }
        $this->seguro = number_format($this->seguro, 3);

        $this->cuota = ($this->beneficiary->hasPlan()) ? $this->beneficiary->getCurrentPlan()->first() : null;
    }

    public function update()
    {
        $this->validate();

        if ($this->idepro != $this->beneficiary->idepro){

            foreach ($this->beneficiary->getCurrentPlan('INACTIVO', '!=') as $p) {
                $p->update([
                    'idepro' => $this->idepro,
                ]);
            }

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
            'fecha_nacimiento' => $this->fecha_nacimiento,
            'monto_activado' => $this->monto_activado,
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


    public function render()
    {
        return view('livewire.beneficiary-update');
    }
}
