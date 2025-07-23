<?php

namespace App\Livewire\Components;

use App\Models\Beneficiary;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\WithPagination;

class BeneficiaryTable extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'desc';
    public $perPage = 25;
    public $selected = [];
    public $expandedRows = [];
    public $showFilters = false;

    // Filtros avanzados
    public $filters = [
        'estado' => '',
        'entidad_financiera' => '',
        'departamento' => '',
        'genero' => '',
        'fecha_activacion_desde' => '',
        'fecha_activacion_hasta' => '',
        'monto_credito_min' => '',
        'monto_credito_max' => '',
        'saldo_credito_min' => '',
        'saldo_credito_max' => '',
        'plazo_credito' => ''
    ];

    protected $queryString = ['search', 'sortField', 'sortDirection', 'perPage', 'filters'];

    protected $listeners = ['refresh' => '$refresh'];

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function toggleRow($id)
    {
        if (in_array($id, $this->expandedRows)) {
            $this->expandedRows = array_diff($this->expandedRows, [$id]);
        } else {
            $this->expandedRows[] = $id;
        }
    }

    public function toggleSelect($id)
    {
        if (in_array($id, $this->selected)) {
            $this->selected = array_diff($this->selected, [$id]);
        } else {
            $this->selected[] = $id;
        }
    }

    public $selectAll = false;

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected = $this->getBeneficiaries()
                ->pluck('id')
                ->map(fn($id) => (string) $id)
                ->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function getSelectedCountProperty()
    {
        return count($this->selected);
    }

    public function getSelectAllProperty()
    {
        return count($this->selected) === $this->getBeneficiaries()->count();
    }

    public function selectAll()
    {
        $this->selected = $this->getBeneficiaries()->pluck('id')->toArray();
    }

    public function deselectAll()
    {
        $this->selected = [];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilters()
    {
        $this->resetPage();
    }

    // Agregar debounce para la búsqueda
    public function updatedSearch()
    {
        $this->resetPage();
    }

    // Optimizar la actualización de filtros
    public function updatedFilters()
    {
        $this->resetPage();
    }

    // Limpiar caché cuando sea necesario
    public function clearCache()
    {
        Cache::tags(['beneficiaries', 'filter_options'])->flush();
        $this->emit('refresh');
    }

    public function resetFilters()
    {
        $this->filters = [
            'estado' => '',
            'entidad_financiera' => '',
            'departamento' => '',
            'genero' => '',
            'fecha_activacion_desde' => '',
            'fecha_activacion_hasta' => '',
            'monto_credito_min' => '',
            'monto_credito_max' => '',
            'saldo_credito_min' => '',
            'saldo_credito_max' => '',
            'plazo_credito' => ''
        ];
    }

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    private function getBeneficiaries()
    {
        $query = Beneficiary::query()
            ->select([
                'id',
                'nombre',
                'ci',
                'idepro',
                'cod_proy',
                'estado',
                'entidad_financiera',
                'departamento',
                'genero',
                'fecha_activacion',
                'monto_credito',
                'saldo_credito',
                'plazo_credito'
            ])
            ->when($this->search, function ($query) {
                $query->where(function ($query) {
                    $query->where('nombre', 'like', '%' . $this->search . '%')
                        ->orWhere('ci', 'like', '%' . $this->search . '%')
                        ->orWhere('idepro', 'like', '%' . $this->search . '%')
                        ->orWhere('cod_proy', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filters['estado'], fn($query) => $query->where('estado', $this->filters['estado']))
            ->when($this->filters['entidad_financiera'], fn($query) => $query->where('entidad_financiera', $this->filters['entidad_financiera']))
            ->when($this->filters['departamento'], fn($query) => $query->where('departamento', $this->filters['departamento']))
            ->when($this->filters['genero'], fn($query) => $query->where('genero', $this->filters['genero']))
            ->when($this->filters['fecha_activacion_desde'], fn($query) => $query->whereDate('fecha_activacion', '>=', $this->filters['fecha_activacion_desde']))
            ->when($this->filters['fecha_activacion_hasta'], fn($query) => $query->whereDate('fecha_activacion', '<=', $this->filters['fecha_activacion_hasta']))
            ->when($this->filters['monto_credito_min'], fn($query) => $query->where('monto_credito', '>=', $this->filters['monto_credito_min']))
            ->when($this->filters['monto_credito_max'], fn($query) => $query->where('monto_credito', '<=', $this->filters['monto_credito_max']))
            ->when($this->filters['saldo_credito_min'], fn($query) => $query->where('saldo_credito', '>=', $this->filters['saldo_credito_min']))
            ->when($this->filters['saldo_credito_max'], fn($query) => $query->where('saldo_credito', '<=', $this->filters['saldo_credito_max']))
            ->when($this->filters['plazo_credito'], fn($query) => $query->where('plazo_credito', $this->filters['plazo_credito']))
            ->orderBy($this->sortField, $this->sortDirection);

        return $query;
    }

    public function render()
    {
        // Obtener valores únicos para los filtros usando colecciones en memoria
        $beneficiaries = $this->getBeneficiaries()->paginate($this->perPage);

        $allBeneficiaries = Beneficiary::select('estado', 'entidad_financiera', 'departamento', 'genero')
            ->distinct()
            ->get();

        $filterOptions = [
            'estados' => $allBeneficiaries->pluck('estado')->unique()->filter()->values(),
            'entidades' => $allBeneficiaries->pluck('entidad_financiera')->unique()->filter()->values(),
            'departamentos' => $allBeneficiaries->pluck('departamento')->unique()->filter()->values(),
            'generos' => $allBeneficiaries->pluck('genero')->unique()->filter()->values(),
        ];

        return view('livewire.components.beneficiary-table', [
            'beneficiaries' => $beneficiaries,
            'filterOptions' => $filterOptions
        ]);
    }
}
