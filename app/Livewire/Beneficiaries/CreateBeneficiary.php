<?php

namespace App\Livewire\Beneficiaries;

use Livewire\Component;

class CreateBeneficiary extends Component
{
    public $openNewBeneficiary = false;

    public $nombre;
    public $ci;
    public $complemento;
    public $expedido;
    public $mail;
    public $estado;
    public $entidad_financiera;
    public $cod_proy;
    public $idepro;
    public $cod_fondesif;
    public $proyecto;
    public $genero;
    public $fecha_nacimiento = '1900-01-01';
    public $monto_credito = 0;
    public $monto_activado = 0;
    public $total_activado = 0;
    public $saldo_credito = 0;
    public $monto_recuperado = 0;
    public $fecha_activacion;
    public $plazo_credito;
    public $tasa_interes;
    public $departamento;
    public $user_id;

    public $gastos_administrativos = 0;
    public $gastos_judiciales = 0;
    public $gastos_notariales = 0;

    public $rules = [
        'nombre' => 'required',
        'ci' => 'required',
        'complemento' => '',
        'expedido' => 'required',
        'mail' => '',
        'estado' => 'required',
        'entidad_financiera' => 'required',
        'cod_proy' => '',
        'idepro' => 'required',
        'cod_fondesif' => '',
        'proyecto' => 'required',
        'genero' => '',
        'fecha_nacimiento' => '',
        'monto_credito' => 'required',
        'monto_activado' => 'required',
        'total_activado' => 'required',
        'gastos_judiciales' => '',
        'saldo_credito' => 'required',
        'monto_recuperado' => '',
        'fecha_activacion' => 'required',
        'plazo_credito' => 'required',
        'tasa_interes' => 'required',
        'departamento' => 'required',
        'user_id' => 'required',
    ];

    private $departamentos = [
        '' => '',
        'QR' => 'QR',
        'LA PAZ' => 'LP',
        'COCHABAMBA' => 'CB',
        'SANTA CRUZ' => 'SC',
        'ORURO' => 'OR',
        'POTOSI' => 'PO',
        'CHUQUISACA' => 'CH',
        'TARIJA' => 'TJ',
        'BENI' => 'BE',
        'PANDO' => 'PA',
    ];

    public function render()
    {
        $this->idepro = $this->ci . $this->departamentos[$this->expedido];

        return view('livewire.beneficiaries.create-beneficiary');
    }

    public function mount()
    {
        $this->user_id = \Illuminate\Support\Facades\Auth::user()->id;
    }

    public function saveBeneficiary()
    {
        $this->validate();

        \App\Models\Beneficiary::create([
            'nombre' => strtoupper($this->nombre),
            'ci' => $this->ci,
            'complemento' => strtoupper($this->complemento),
            'expedido' => $this->departamentos[$this->expedido],
            'mail' => $this->mail,
            'estado' => $this->estado,
            'entidad_financiera' => strtoupper($this->entidad_financiera),
            'cod_proy' => $this->cod_proy,
            'idepro' => $this->idepro,
            'cod_fondesif' => $this->cod_fondesif,
            'proyecto' => $this->proyecto,
            'genero' => $this->genero,
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
            'user_id' => $this->user_id,
        ]);

        if ($this->gastos_administrativos > 0) {
            \App\Models\Spend::create([
                'idepro' => $this->idepro,
                'criterio' => 'GASTOS ADMINISTRATIVOS',
                'monto' => $this->gastos_administrativos,
                'estado' => 'ACTIVO',
            ]);
        }

        if ($this->gastos_judiciales > 0) {
            \App\Models\Spend::create([
                'idepro' => $this->idepro,
                'criterio' => 'GASTOS JUDICIALES',
                'monto' => $this->gastos_judiciales,
                'estado' => 'ACTIVO',
            ]);
        }

        if ($this->gastos_notariales > 0) {
            \App\Models\Spend::create([
                'idepro' => $this->idepro,
                'criterio' => 'GASTOS NOTARIALES',
                'monto' => $this->gastos_notariales,
                'estado' => 'ACTIVO',
            ]);
        }

        $this->reset();
        $this->openNewBeneficiary = false;

        session()->flash('message', 'Beneficiario creado exitosamente!');
        return redirect()->to(request()->header('Referer'));
    }

    public function placeholder()
    {
        return <<< 'HTML'
                        <div class="flex justify-center items-center h-12">
                            <svg class="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    HTML;
    }
}
