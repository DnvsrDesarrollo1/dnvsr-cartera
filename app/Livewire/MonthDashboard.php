<?php

namespace App\Livewire;

use App\Models\Beneficiary;
use Livewire\Component;
use App\Models\Payment;
use Carbon\Carbon;
use \App\Charts\PaymentsByMonth;

class MonthDashboard extends Component
{
    public $inicio = '2024-01-01', $fin = '2024-01-31';
    public $proyecto, $proyectos, $search = '';
    public $chart;

    public $desplegar;

    public function render()
    {
        if ($this->search != '') {
            $this->proyectos = Beneficiary::where('proyecto', 'LIKE', '%' . strtoupper($this->search) . '%')
                ->orderBy('proyecto')
                ->get(['proyecto'])
                ->unique('proyecto');
        }

        return view('livewire.month-dashboard');
    }

    public function setProject($proyecto)
    {
        $this->proyectos = null;
        $this->search = '';

        $rangeDates = [$this->inicio, $this->fin];
        $this->proyecto = $proyecto;

        $ideproList = Beneficiary::where('proyecto', $proyecto)->distinct('idepro')->pluck('idepro');

        $payments = Payment::whereIn('numprestamo', $ideproList)
            ->whereBetween('fecha_pago', $rangeDates)
            ->selectRaw("TO_CHAR(fecha_pago, 'MM-YYYY') as mes, SUM(montopago) as montopago")
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        $labels = $payments->pluck('mes')->toArray();
        $dataset = $payments->pluck('montopago')->toArray();

        $this->chart = new PaymentsByMonth;

        //$this->chart->labels($labels);
        //$this->chart->dataset('Pagos', 'line', $dataset);

        $this->chart->labels([1, 2, 3, 4]);
        $this->chart->dataset('Pagos', 'line', [100, 200, 100, 150]);
    }
}
