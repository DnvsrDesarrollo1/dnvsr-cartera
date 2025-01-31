<?php

namespace App\Livewire;

use App\Models\Beneficiary;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Mostrar proyectos coincidentes')]
class MonthDashboard extends Component
{
    public $payments;
    public $paymentsTimeline;
    public $paymentsBudget;
    public $search;
    public $projects;
    public $selected;
    public $fechaInicio = '2020-01-01';
    public $fechaFin = '2024-12-31';
    public $total_capital;
    public $total_interes;
    public $total_seguro;

    protected $queryString = [
        'selected' => ['except' => ''],
        'fechaInicio' => ['except' => ''],
        'fechaFin' => ['except' => '']
    ];

    public function mount()
    {
        $this->fechaInicio = $this->fechaInicio ?? date('Y-01-01');
        $this->fechaFin = $this->fechaFin ?? date('Y-12-31');
        $this->loadPayments();
    }

    public function resetSearch()
    {
        $this->reset(['search', 'projects', 'selected', 'fechaInicio', 'fechaFin']);
        $this->loadPayments();
    }

    public function updatedSearch()
    {
        $this->projects = Beneficiary::query()
            ->select('proyecto')
            ->where('proyecto', 'like', "%{$this->search}%")
            ->groupBy('proyecto')
            ->orderBy('proyecto')
            ->get();
    }

    public function selectProject($proyecto)
    {
        $this->reset(['search', 'projects']);
        $this->selected = $proyecto;
        $this->loadPayments();
    }

    public function updatedFechaInicio()
    {
        $this->loadPayments();
    }

    public function updatedFechaFin()
    {
        $this->loadPayments();
    }

    public function loadPayments()
    {
        if (!$this->selected) return;

        $this->payments = $this->getPayments();

        $this->total_capital = $this->payments->filter(function($payment) {
            return stripos($payment->prtdtdesc, 'CAPI') !== false;
        })->sum('total_monto');
        $this->total_interes = $this->payments->filter(function($payment) {
            return stripos($payment->prtdtdesc, 'INTE') !== false;
        })->sum('total_monto');
        $this->total_seguro = $this->payments->filter(function($payment) {
            return stripos($payment->prtdtdesc, 'SEGU') !== false;
        })->sum('total_monto');

        $this->total_seguro += $this->payments->filter(function($payment) {
            return stripos($payment->prtdtdesc, 'PENA') !== false;
        })->sum('total_monto');

        $this->paymentsTimeline = $this->getPaymentsTimeline();
        $this->paymentsBudget = $this->getPaymentsBudget();
    }

    private function getPayments()
    {
        return DB::table('payments')
            ->select('payments.prtdtdesc', DB::raw('SUM(payments.montopago) as total_monto'))
            ->join('beneficiaries', 'payments.numprestamo', '=', 'beneficiaries.idepro')
            ->where('beneficiaries.proyecto', $this->selected)
            ->whereBetween('payments.fecha_pago', [$this->fechaInicio, $this->fechaFin])
            ->groupBy('payments.prtdtdesc')
            ->orderByDesc('total_monto')
            ->get();
    }

    private function getPaymentsTimeline()
    {
        return DB::table('payments')
            ->select(
                DB::raw("to_char(payments.fecha_pago, 'YYYY-MM') as mes"),
                DB::raw('SUM(payments.montopago) as total_monto')
            )
            ->join('beneficiaries', 'payments.numprestamo', '=', 'beneficiaries.idepro')
            ->where('payments.prtdtdesc', 'like', '%CAPI%')
            ->where('beneficiaries.proyecto', $this->selected)
            ->whereBetween('payments.fecha_pago', [$this->fechaInicio, $this->fechaFin])
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();
    }

    private function getPaymentsBudget()
    {
        return DB::table('plans')
            ->select(
                DB::raw("to_char(plans.fecha_ppg, 'YYYY-MM') as mes"),
                DB::raw('SUM(plans.prppgcapi) as total_monto')
            )
            ->join('beneficiaries', 'plans.idepro', '=', 'beneficiaries.idepro')
            ->where('plans.estado', 'ACTIVO')
            ->where('beneficiaries.proyecto', $this->selected)
            ->whereBetween('plans.fecha_ppg', [$this->fechaInicio, $this->fechaFin])
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();
    }

    public function render()
    {
        $this->fechaInicio = \App\Models\Payment::orderBy('fecha_pago', 'asc')->first()->fecha_pago ?? '2020-01-01';
        $this->fechaFin = \App\Models\Payment::orderBy('fecha_pago', 'desc')->first()->fecha_pago ?? '2024-12-31';
        return view('livewire.month-dashboard');
    }
}
