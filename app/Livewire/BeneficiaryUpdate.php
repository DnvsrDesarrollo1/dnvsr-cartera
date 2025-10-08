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
        'fecha_nacimiento' => 'required|date',
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
        $this->beneficiary = $beneficiary;
        $this->fill($beneficiary->toArray());
        $this->seguro = ($beneficiary->insurance()->exists()) ? $beneficiary->insurance->tasa_seguro : 0;
        if ($this->seguro == 0) {
            $this->seguro = ($this->beneficiary->hasPlan())
                ?
                ($this->beneficiary->getCurrentPlan('INACTIVO', '!=')->first()->prppgsegu > 0 ?: 0.0001 / $beneficiary->saldo_credito) * 100 : 0;
        }
        $this->seguro = number_format($this->seguro, 3);

        $this->cuota = ($this->beneficiary->hasPlan()) ? $this->beneficiary->getCurrentPlan()->first() : null;
    }

    public function update()
    {
        $this->validate();

        if ($this->idepro != $this->beneficiary->idepro) {

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

        // Backup and delete plans
        $plans = $this->beneficiary->getCurrentPlan('INACTIVO', '!=');
        $this->exportToCsv($plans, "plans_{$idepro}.csv");
        foreach ($plans as $plan) {
            $plan->delete();
        }

        // Backup and delete spends
        $spends = $this->beneficiary->spends()->get();
        $this->exportToCsv($spends, "spends_{$idepro}.csv");
        foreach ($spends as $spend) {
            $spend->delete();
        }

        // Backup and delete helpers
        $helpers = $this->beneficiary->helpers()->get();
        $this->exportToCsv($helpers, "helpers_{$idepro}.csv");
        foreach ($helpers as $helper) {
            $helper->delete();
        }

        // Backup and delete earns
        $earns = $this->beneficiary->earns()->get();
        $this->exportToCsv($earns, "earns_{$idepro}.csv");
        foreach ($earns as $earn) {
            $earn->delete();
        }

        // Backup and delete vouchers (payments will be cascade deleted)
        $vouchers = $this->beneficiary->vouchers()->get();
        $this->exportToCsv($vouchers, "vouchers_{$idepro}.csv");
        foreach ($vouchers as $voucher) {
            $voucher->delete();
        }

        // Backup and delete beneficiary
        $beneficiaryData = collect([$this->beneficiary]);
        $this->exportToCsv($beneficiaryData, "beneficiary_{$idepro}.csv");
        $this->beneficiary->delete();

        Log::info("[". now() ."] Usuario: " . \Illuminate\Support\Facades\Auth::user()->name . " - a eliminado a {$idepro}.");

        return redirect()->route('beneficiario.index');
    }

    /**
     * Export a collection to a CSV file in storage/app/exports
     */
    private function exportToCsv($collection, string $filename): void
    {
        if ($collection->isEmpty()) {
            return;
        }

        $headers = array_keys($collection->first()->toArray());
        $rows = $collection->map(fn ($item) => $item->toArray());

        // Build CSV content
        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, $headers);
        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        // Ensure directory exists
        $dir = storage_path('app/public/temp/');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Save file
        file_put_contents("{$dir}/{$filename}", $csv);
    }

    public function render()
    {
        return view('livewire.beneficiary-update');
    }
}
