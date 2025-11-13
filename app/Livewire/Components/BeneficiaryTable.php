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

    public $showFilters = false;

    // Filtros avanzados
    public $filters = [
        'estado' => '',
        'entidad_financiera' => '',
        'departamento' => '',
        'genero' => '',
        'proyecto' => '',
        'fecha_activacion_desde' => '',
        'fecha_activacion_hasta' => '',
        'monto_activado_min' => '',
        'monto_activado_max' => '',
        'saldo_credito_min' => '',
        'saldo_credito_max' => '',
        'plazo_credito' => '',
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

    public $selected = [];

    public $selectAll = false;

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected = $this->getBeneficiaries()
                ->paginate($this->perPage)
                ->pluck('id')
                ->map(fn ($id) => (string) $id)
                ->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function getSelectedCountProperty()
    {
        return count($this->selected);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilters()
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
            'proyecto' => '',
            'fecha_activacion_desde' => '',
            'fecha_activacion_hasta' => '',
            'monto_activado_min' => '',
            'monto_activado_max' => '',
            'saldo_credito_min' => '',
            'saldo_credito_max' => '',
            'plazo_credito' => '',
        ];

        $this->search = '';
        $this->sortField = 'id';
        $this->sortDirection = 'desc';

        $this->reset();
    }

    public function toggleFilters()
    {
        $this->showFilters = ! $this->showFilters;
    }

    public $statusOptions = ['VIGENTE', 'VENCIDO', 'CANCELADO', 'BLOQUEADO', 'EJECUCION'];

    public function save($id, $field, $value)
    {
        Beneficiary::find($id)->update([
            $field => $value,
        ]);

        $this->dispatch('notify', 'Cambios guardados con éxito!');
        $this->skipRender();
    }

    private function getBeneficiaries()
    {
        $query = Beneficiary::query();

        $query->select([
            'id',
            'nombre',
            'ci',
            'idepro',
            'estado',
            'proyecto',
            'entidad_financiera',
            'departamento',
            'fecha_activacion',
            'monto_activado',
            'monto_credito',
            'saldo_credito',
        ])
            ->when($this->search != '', function ($query) {
                $query->where(function ($query) {
                    $query->where('nombre', 'like', '%'.$this->search.'%')
                        ->orWhere('ci', 'like', '%'.$this->search.'%')
                        ->orWhere('idepro', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->filters['estado'] != '', fn ($query) => $query->where('estado', $this->filters['estado']))
            ->when($this->filters['entidad_financiera'] != '', fn ($query) => $query->where('entidad_financiera', $this->filters['entidad_financiera']))
            ->when($this->filters['departamento'] != '', fn ($query) => $query->where('departamento', $this->filters['departamento']))
            ->when($this->filters['genero'] != '', fn ($query) => $query->where('genero', $this->filters['genero']))
            ->when($this->filters['proyecto'] != '', fn ($query) => $query->where('proyecto', $this->filters['proyecto']))
            ->when($this->filters['fecha_activacion_desde'] != '', fn ($query) => $query->whereDate('fecha_activacion', '>=', $this->filters['fecha_activacion_desde']))
            ->when($this->filters['fecha_activacion_hasta'] != '', fn ($query) => $query->whereDate('fecha_activacion', '<=', $this->filters['fecha_activacion_hasta']))
            ->when($this->filters['monto_activado_min'] != '', fn ($query) => $query->where('monto_activado', '>=', $this->filters['monto_activado_min']))
            ->when($this->filters['monto_activado_max'] != '', fn ($query) => $query->where('monto_activado', '<=', $this->filters['monto_activado_max']))
            ->when($this->filters['saldo_credito_min'] != '', fn ($query) => $query->where('saldo_credito', '>=', $this->filters['saldo_credito_min']))
            ->when($this->filters['saldo_credito_max'] != '', fn ($query) => $query->where('saldo_credito', '<=', $this->filters['saldo_credito_max']))
            ->when($this->filters['plazo_credito'] != '', fn ($query) => $query->where('plazo_credito', $this->filters['plazo_credito']));

        $query->orderBy($this->sortField, $this->sortDirection);

        return $query;
    }

    public function render()
    {
        $beneficiaries = $this->getBeneficiaries()
            ->paginate($this->perPage);

        $filterOptions = Cache::remember('beneficiary_filter_options', now()->addDay(), function () {
            return [
                'estados' => Beneficiary::query()->select('estado')->distinct()->pluck('estado')->filter()->values(),
                'proyectos' => Beneficiary::query()->select('proyecto')->orderBy('proyecto', 'asc')->distinct()->pluck('proyecto')->filter()->values(),
                'entidades' => Beneficiary::query()->select('entidad_financiera')->distinct()->pluck('entidad_financiera')->filter()->values(),
                'departamentos' => Beneficiary::query()->select('departamento')->distinct()->pluck('departamento')->filter()->values(),
                'generos' => Beneficiary::query()->select('genero')->distinct()->pluck('genero')->filter()->values(),
            ];
        });

        return view('livewire.components.beneficiary-table', [
            'beneficiaries' => $beneficiaries,
            'filterOptions' => $filterOptions,
        ]);
    }
}
