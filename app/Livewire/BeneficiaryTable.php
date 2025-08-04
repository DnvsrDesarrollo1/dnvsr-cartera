<?php

namespace App\Livewire;

use App\Models\Beneficiary;
use Illuminate\Database\Eloquent\Builder;

use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;

use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;

use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class BeneficiaryTable extends PowerGridComponent
{
    public string $tableName = 'beneficiaries';

    // ORDENAMIENTO
    public string $sortField = 'id';

    public array $estado;

    public string $sortDirection = 'desc';

    public bool $showErrorBag = true;

    public bool $showFilters = true;

    public function boot(): void
    {
        config(['livewire-powergrid.filter' => 'outside']);
    }

    public function setUp(): array
    {
        $this->showCheckBox();

        $this->persist(['columns', 'filters'], prefix: \Illuminate\Support\Facades\Auth::user()->id ?? '');

        return [
            PowerGrid::header(),

            PowerGrid::footer()
                ->showPerPage(perPage: 25, perPageValues: [25, 50, 100, 0])
                ->showRecordCount(),

            PowerGrid::detail()
                ->view('components.detail')
                ->showCollapseIcon(),
        ];
    }

    public function header(): array
    {
        return [
            \Illuminate\Support\Facades\Auth::user()->can('write plans') ? Button::add('bulk-activation')
                ->confirm('Confirma la creacion masiva automatica de planes para los beneficiarios seleccionados?')
                ->slot('Activación (<span x-text="window.pgBulkActions.count(\'' . $this->tableName . '\')"></span>)')
                ->class('border mr-2 px-2 py-1 rounded-md relative cursor-pointer')
                ->dispatch('bulkActivation.' . $this->tableName, []) : Button::add('-'),

            /* \Illuminate\Support\Facades\Auth::user()->can('write plans') ? Button::add('bulk-adjust')
                ->confirm('Confirma el reajuste masivo automatico de planes para los beneficiarios seleccionados?')
                ->slot('Reajuste (<span x-text="window.pgBulkActions.count(\'' . $this->tableName . '\')"></span>)')
                ->class('border px-2 py-1 rounded-md relative cursor-pointer')
                ->dispatch('bulkAdjust.' . $this->tableName, []) : Button::add('-'), */

            Button::add('bulk-export')
                ->confirm('Confirma la exportación masiva de los beneficiarios seleccionados?')
                ->slot('PDF (<span x-text="window.pgBulkActions.count(\'' . $this->tableName . '\')"></span>)')
                ->class('border ml-12 mr-2 px-2 py-1 rounded-md relative cursor-pointer')
                ->dispatch('bulkExportPDF.' . $this->tableName, []),

            Button::add('bulk-export')
                ->confirm('Confirma la exportación masiva de planes de los beneficiarios seleccionados?')
                ->slot('XLSX (<span x-text="window.pgBulkActions.count(\'' . $this->tableName . '\')"></span>)')
                ->class('border px-2 py-1 rounded-md relative cursor-pointer')
                ->dispatch('bulkExportXLSX.' . $this->tableName, []),
        ];
    }

    public function datasource(): Builder
    {
        return Beneficiary::query();
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('nombre')
            ->add('ci')
            ->add('complemento')
            ->add('expedido')
            ->add('mail')
            ->add('estado')
            ->add('entidad_financiera')
            ->add('idepro')
            ->add('proyecto')
            ->add('plan')
            ->add('genero')
            ->add('fecha_nacimiento')
            ->add('monto_activado', fn($ben) => number_format($ben->monto_activado ?? 0, 2))
            ->add('gastos_judiciales', fn($ben) => number_format($ben->gastos_judiciales ?? 0, 2))
            ->add('saldo_credito', fn($ben) => number_format($ben->getCurrentPlan('CANCELADO', '!=')->sum('prppgcapi') ?? 0, 2))
            ->add('fecha_activacion')
            ->add('departamento')
            ->add('cuotas_pendientes', fn($beneficiary) => e($beneficiary->getCurrentPlan('CANCELADO', '!=')->where('fecha_ppg', '<', now())->count()));
    }

    public function columns(): array
    {
        return [
            Column::make('Nombres', 'nombre')
                ->sortable(),

            Column::make('C.I.', 'ci')
                ->editOnClick(hasPermission: \Illuminate\Support\Facades\Auth::user()->can('write beneficiaries'))
                ->sortable(),

            Column::make('Prestamo', 'idepro')
                ->hidden(),

            Column::make('Proyecto', 'proyecto')
                ->hidden(),

            Column::make('Departamento', 'departamento')
                ->hidden(),

            Column::make('Estado', 'estado')
                ->editOnClick(hasPermission: \Illuminate\Support\Facades\Auth::user()->can('write beneficiaries'))
                ->contentClasses([
                    'BLOQUEADO' => 'text-red-500',
                ])
                ->sortable(),

            Column::make('EIF', 'entidad_financiera')
                ->hidden(),

            Column::make('M. Activado', 'monto_activado')
                ->sortable(),

            Column::make('Gastos', 'gastos_judiciales')
                ->sortable(),

            Column::make('Saldo <i>k</i>', 'saldo_credito')
                ->sortable(),

            Column::make('F. Activación', 'fecha_activacion')
                ->sortable(),

            Column::make('C. Pendientes', 'cuotas_pendientes'),

            Column::action('')
        ];
    }

    protected function rules()
    {
        return [
            'estado.*' => [
                'required',
                'in:VIGENTE,VENCIDO,CANCELADO,BLOQUEADO,EJECUCION',
            ]
        ];
    }

    protected function validationAttributes()
    {
        return [
            'estado.*' => 'Estado del beneficiario',
        ];
    }

    protected function messages()
    {
        return [
            'estado.*.in' => 'Estados validos: :values',
        ];
    }

    public function onUpdatedEditable(string|int $id, string $field, string $value): void
    {
        $this->withValidator(function (\Illuminate\Validation\Validator $validator) use ($id, $field) {
            if ($validator->errors()->isNotEmpty()) {
                $this->dispatch('toggle-' . $field . '-' . $id);
            }
        })->validate();

        Beneficiary::query()->find($id)->update([
            $field => e($value),
            'user_id' => \Illuminate\Support\Facades\Auth::user()->id,
            'updated_at' => \Carbon\Carbon::now()
        ]);
    }

    public function filters(): array
    {
        return [
            Filter::inputText('nombre')->operators(['contains']),
            Filter::inputText('ci')->operators(['contains']),
            Filter::inputText('idepro')->operators(['contains']),


            Filter::select('proyecto', 'proyecto')
                ->dataSource(Beneficiary::select('proyecto')->distinct()->orderBy('proyecto', 'asc')->get())
                ->optionValue('proyecto')
                ->optionLabel('proyecto'),

            Filter::select('estado', 'estado')
                ->dataSource(Beneficiary::select('estado')->distinct()->get())
                ->optionValue('estado')
                ->optionLabel('estado'),

            Filter::select('entidad_financiera', 'entidad_financiera')
                ->dataSource(Beneficiary::select('entidad_financiera')->distinct()->get())
                ->optionValue('entidad_financiera')
                ->optionLabel('entidad_financiera'),

            Filter::select('departamento', 'departamento')
                ->dataSource(Beneficiary::select('departamento')->distinct()->get())
                ->optionValue('departamento')
                ->optionLabel('departamento'),
        ];
    }

    public function actions($row): array
    {
        return [
            Button::add('show')
                ->slot(('<svg width="32px" height="32px" viewBox="-3.6 -3.6 31.20 31.20" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#000000" stroke-width="0.00024000000000000003"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.048"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M9.29289 1.29289C9.48043 1.10536 9.73478 1 10 1H18C19.6569 1 21 2.34315 21 4V8C21 8.55228 20.5523 9 20 9C19.4477 9 19 8.55228 19 8V4C19 3.44772 18.5523 3 18 3H11V8C11 8.55228 10.5523 9 10 9H5V20C5 20.5523 5.44772 21 6 21H9C9.55228 21 10 21.4477 10 22C10 22.5523 9.55228 23 9 23H6C4.34315 23 3 21.6569 3 20V8C3 7.73478 3.10536 7.48043 3.29289 7.29289L9.29289 1.29289ZM6.41421 7H9V4.41421L6.41421 7ZM18 19C15.2951 19 14 20.6758 14 22C14 22.5523 13.5523 23 13 23C12.4477 23 12 22.5523 12 22C12 20.1742 13.1429 18.5122 14.9952 17.6404C14.3757 16.936 14 16.0119 14 15C14 12.7909 15.7909 11 18 11C20.2091 11 22 12.7909 22 15C22 16.0119 21.6243 16.936 21.0048 17.6404C22.8571 18.5122 24 20.1742 24 22C24 22.5523 23.5523 23 23 23C22.4477 23 22 22.5523 22 22C22 20.6758 20.7049 19 18 19ZM18 17C19.1046 17 20 16.1046 20 15C20 13.8954 19.1046 13 18 13C16.8954 13 16 13.8954 16 15C16 16.1046 16.8954 17 18 17Z" fill="#000000"></path> </g></svg>'))
                ->route('beneficiario.show', ['cedula' => $row->ci ?? 0], '_blank'),
            Button::add('pdf')
                ->slot(('<svg width="32px" height="32px" viewBox="0 0 400 400" xmlns="http://www.w3.org/2000/svg" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <defs> <style>.cls-1{fill:#ff402f;}</style> </defs> <title></title> <g id="xxx-word"> <path class="cls-1" d="M325,105H250a5,5,0,0,1-5-5V25a5,5,0,0,1,10,0V95h70a5,5,0,0,1,0,10Z"></path> <path class="cls-1" d="M325,154.83a5,5,0,0,1-5-5V102.07L247.93,30H100A20,20,0,0,0,80,50v98.17a5,5,0,0,1-10,0V50a30,30,0,0,1,30-30H250a5,5,0,0,1,3.54,1.46l75,75A5,5,0,0,1,330,100v49.83A5,5,0,0,1,325,154.83Z"></path> <path class="cls-1" d="M300,380H100a30,30,0,0,1-30-30V275a5,5,0,0,1,10,0v75a20,20,0,0,0,20,20H300a20,20,0,0,0,20-20V275a5,5,0,0,1,10,0v75A30,30,0,0,1,300,380Z"></path> <path class="cls-1" d="M275,280H125a5,5,0,0,1,0-10H275a5,5,0,0,1,0,10Z"></path> <path class="cls-1" d="M200,330H125a5,5,0,0,1,0-10h75a5,5,0,0,1,0,10Z"></path> <path class="cls-1" d="M325,280H75a30,30,0,0,1-30-30V173.17a30,30,0,0,1,30-30h.2l250,1.66a30.09,30.09,0,0,1,29.81,30V250A30,30,0,0,1,325,280ZM75,153.17a20,20,0,0,0-20,20V250a20,20,0,0,0,20,20H325a20,20,0,0,0,20-20V174.83a20.06,20.06,0,0,0-19.88-20l-250-1.66Z"></path> <path class="cls-1" d="M145,236h-9.61V182.68h21.84q9.34,0,13.85,4.71a16.37,16.37,0,0,1-.37,22.95,17.49,17.49,0,0,1-12.38,4.53H145Zm0-29.37h11.37q4.45,0,6.8-2.19a7.58,7.58,0,0,0,2.34-5.82,8,8,0,0,0-2.17-5.62q-2.17-2.34-7.83-2.34H145Z"></path> <path class="cls-1" d="M183,236V182.68H202.7q10.9,0,17.5,7.71t6.6,19q0,11.33-6.8,18.95T200.55,236Zm9.88-7.85h8a14.36,14.36,0,0,0,10.94-4.84q4.49-4.84,4.49-14.41a21.91,21.91,0,0,0-3.93-13.22,12.22,12.22,0,0,0-10.37-5.41h-9.14Z"></path> <path class="cls-1" d="M245.59,236H235.7V182.68h33.71v8.24H245.59v14.57h18.75v8H245.59Z"></path> </g> </g></svg>'))
                ->route('beneficiario.pdf', ['cedula' => $row->ci ?? 0], '_blank'),
            Button::add('pdf')
                ->slot(('<svg width="32px" height="32px" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" stroke-width="2.56" stroke="#34d539" fill="none"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path d="M45.77,51a.09.09,0,0,1,.11,0l3.4,3.31s.12,0,.12-.07V9.7H14.6V54.16a.08.08,0,0,0,.13.07l4.38-3.29a.08.08,0,0,1,.1,0l4.53,3.33a.08.08,0,0,0,.11,0l4-3.33s.07,0,.11,0l4.52,3.32a.08.08,0,0,0,.11,0L36.79,51a.09.09,0,0,1,.12,0l4.51,3.31a.09.09,0,0,0,.12,0l4-3.3" stroke-linecap="round"></path><line x1="18.72" y1="31.11" x2="32.02" y2="31.11" stroke-linecap="round"></line><line x1="18.72" y1="25.77" x2="34.69" y2="25.77" stroke-linecap="round"></line><line x1="18.72" y1="15" x2="36.05" y2="15" stroke-linecap="round"></line><line x1="18.72" y1="20.11" x2="33.15" y2="20.11" stroke-linecap="round"></line><line x1="41.01" y1="30.95" x2="45.28" y2="30.95" stroke-linecap="round"></line><line x1="18.72" y1="36.81" x2="34.69" y2="36.81" stroke-linecap="round"></line><line x1="41.86" y1="36.66" x2="45.28" y2="36.66" stroke-linecap="round"></line><line x1="41.01" y1="42.56" x2="45.28" y2="42.56" stroke-linecap="round"></line><line x1="41.01" y1="25.61" x2="45.28" y2="25.61" stroke-linecap="round"></line><line x1="41.86" y1="20.11" x2="45.28" y2="20.11" stroke-linecap="round"></line><line x1="41.01" y1="15" x2="45.28" y2="15" stroke-linecap="round"></line></g></svg>'))
                ->route('beneficiario.pdf-extract', ['cedula' => $row->ci ?? 0], '_blank'),
        ];
    }

    #[\Livewire\Attributes\On('bulkActivation.{tableName}')]
    public function bulkActivation()
    {
        if ($this->checkboxValues) {
            $this->js('
        Swal.fire({
            title: "Ingrese la tasa de interés (-1 para usar dato del perfil): (0, 1, 2, 3...)",
            input: "number",
            inputAttributes: {
                min: -1,
                step: 0.01
            },
            showCancelButton: true,
            confirmButtonText: "Siguiente",
            cancelButtonText: "Cancelar",
            inputValidator: (value) => {
                if (!value || isNaN(value)) {
                    return "Por favor, ingrese un número válido.";
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: "Ingrese la tasa de seguro (-1 para usar dato del perfil): (0, 0.040, 0.076...)",
                    input: "number",
                    inputAttributes: {
                        min: -1,
                        step: 0.01
                    },
                    showCancelButton: true,
                    confirmButtonText: "Confirmar",
                    cancelButtonText: "Cancelar",
                    inputValidator: (value) => {
                        if (!value || isNaN(value)) {
                            return "Por favor, ingrese un número válido.";
                        }
                    }
                }).then((resultSeguro) => {
                    if (resultSeguro.isConfirmed) {
                        $wire.processBulkActivation(result.value, resultSeguro.value);
                    }
                });
            }
        });
    ');
        }
    }

    #[\Livewire\Attributes\On('processBulkActivation')]
    public function processBulkActivation($inputInterest, $inputInsurance)
    {
        if ($this->checkboxValues) {
            $data = $this->checkboxValues;
            $data["interes"] = strval($inputInterest);
            $data["seguro"] = strval($inputInsurance);

            $this->js('window.pgBulkActions.clearAll()');
            //show a alert with the data
            //dd($data);

            // Convertir el array a JSON y codificarlo para URL
            $encodedData = urlencode(json_encode($data));

            return redirect()->route('plan.bulk-activation', ['data' => $encodedData]);
        }
    }


    #[\Livewire\Attributes\On('bulkAdjust.{tableName}')]
    public function bulkAdjust()
    {
        if ($this->checkboxValues) {
            $data = collect($this->checkboxValues);
            $this->js('window.pgBulkActions.clearAll()');
            return redirect()->route('plan.bulk-adjust', ['data' => json_encode($data)]);
        }
    }

    #[\Livewire\Attributes\On('bulkExportPDF.{tableName}')]
    public function bulkExportPDF()
    {
        if ($this->checkboxValues) {
            $data = collect($this->checkboxValues);
            $this->js('window.pgBulkActions.clearAll()');
            return redirect()->route('beneficiario.bulk-pdf', ['data' => json_encode($data)]);
        }
    }

    #[\Livewire\Attributes\On('bulkExportXLSX.{tableName}')]
    public function bulkExportXLSX()
    {
        if ($this->checkboxValues) {
            $data = collect($this->checkboxValues);
            $this->js('window.pgBulkActions.clearAll()');
            return redirect()->route('plan.bulk-xlsx', ['data' => json_encode($data)]);
        }
    }
}
