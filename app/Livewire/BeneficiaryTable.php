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

use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;

final class BeneficiaryTable extends PowerGridComponent
{
    use WithExport;

    public string $tableName = 'beneficiaries';

    // ORDENAMIENTO
    public string $sortField = 'fecha_activacion';
    public string $sortDirection = 'desc';

    public function setUp(): array
    {
        $this->showCheckBox('ci');

        return [
            PowerGrid::exportable('table_' . $this->tableName . '_' . uniqid())
                ->striped()
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),

            PowerGrid::header(),

            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount('short'),
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
            ->add('genero')
            ->add('fecha_nacimiento')
            ->add('total_activado', fn($ben) => \Illuminate\Support\Number::currency($ben->total_activado ?? 0, in: 'Bs.'))
            ->add('gastos_judiciales', fn($ben) => \Illuminate\Support\Number::currency($ben->gastos_judiciales ?? 0, in: 'Bs.'))
            ->add('saldo_credito', fn($ben) => \Illuminate\Support\Number::currency($ben->saldo_credito ?? 0, in: 'Bs.'))
            ->add('fecha_activacion')
            ->add('departamento');
    }

    public function columns(): array
    {
        return [
            Column::make('Nombres', 'nombre')
                ->sortable(),

            Column::make('CI', 'ci')
                ->sortable(),

            Column::make('Proyecto', 'proyecto')
                ->hidden(),

            Column::make('Departamento', 'departamento')
                ->hidden(),

            Column::make('Complemento', 'complemento'),

            Column::make('Estado', 'estado')
                ->sortable(),

            Column::make('EIF', 'entidad_financiera')
                ->hidden(),

            Column::make('Total Activado', 'total_activado')
                ->sortable(),

            Column::make('Gastos Judiciales', 'gastos_judiciales')
                ->sortable(),

            Column::make('Saldo Crédito', 'saldo_credito')
                ->sortable(),

            Column::make('Fecha Activación', 'fecha_activacion')
                ->sortable(),

            Column::action('Opciones')
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('nombre')->operators(['contains']),
            Filter::inputText('ci')->operators(['contains']),
            Filter::inputText('proyecto')->operators(['contains']),

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
                ->slot(__('<svg width="32px" height="32px" viewBox="-3.6 -3.6 31.20 31.20" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#000000" stroke-width="0.00024000000000000003"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.048"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M9.29289 1.29289C9.48043 1.10536 9.73478 1 10 1H18C19.6569 1 21 2.34315 21 4V8C21 8.55228 20.5523 9 20 9C19.4477 9 19 8.55228 19 8V4C19 3.44772 18.5523 3 18 3H11V8C11 8.55228 10.5523 9 10 9H5V20C5 20.5523 5.44772 21 6 21H9C9.55228 21 10 21.4477 10 22C10 22.5523 9.55228 23 9 23H6C4.34315 23 3 21.6569 3 20V8C3 7.73478 3.10536 7.48043 3.29289 7.29289L9.29289 1.29289ZM6.41421 7H9V4.41421L6.41421 7ZM18 19C15.2951 19 14 20.6758 14 22C14 22.5523 13.5523 23 13 23C12.4477 23 12 22.5523 12 22C12 20.1742 13.1429 18.5122 14.9952 17.6404C14.3757 16.936 14 16.0119 14 15C14 12.7909 15.7909 11 18 11C20.2091 11 22 12.7909 22 15C22 16.0119 21.6243 16.936 21.0048 17.6404C22.8571 18.5122 24 20.1742 24 22C24 22.5523 23.5523 23 23 23C22.4477 23 22 22.5523 22 22C22 20.6758 20.7049 19 18 19ZM18 17C19.1046 17 20 16.1046 20 15C20 13.8954 19.1046 13 18 13C16.8954 13 16 13.8954 16 15C16 16.1046 16.8954 17 18 17Z" fill="#000000"></path> </g></svg>'))
                ->route('beneficiario.show', ['cedula' => $row->ci ?? 0], '_blank'),
            Button::add('pdf')
                ->slot(__('<svg width="32px" height="32px" viewBox="0 0 400 400" xmlns="http://www.w3.org/2000/svg" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <defs> <style>.cls-1{fill:#ff402f;}</style> </defs> <title></title> <g id="xxx-word"> <path class="cls-1" d="M325,105H250a5,5,0,0,1-5-5V25a5,5,0,0,1,10,0V95h70a5,5,0,0,1,0,10Z"></path> <path class="cls-1" d="M325,154.83a5,5,0,0,1-5-5V102.07L247.93,30H100A20,20,0,0,0,80,50v98.17a5,5,0,0,1-10,0V50a30,30,0,0,1,30-30H250a5,5,0,0,1,3.54,1.46l75,75A5,5,0,0,1,330,100v49.83A5,5,0,0,1,325,154.83Z"></path> <path class="cls-1" d="M300,380H100a30,30,0,0,1-30-30V275a5,5,0,0,1,10,0v75a20,20,0,0,0,20,20H300a20,20,0,0,0,20-20V275a5,5,0,0,1,10,0v75A30,30,0,0,1,300,380Z"></path> <path class="cls-1" d="M275,280H125a5,5,0,0,1,0-10H275a5,5,0,0,1,0,10Z"></path> <path class="cls-1" d="M200,330H125a5,5,0,0,1,0-10h75a5,5,0,0,1,0,10Z"></path> <path class="cls-1" d="M325,280H75a30,30,0,0,1-30-30V173.17a30,30,0,0,1,30-30h.2l250,1.66a30.09,30.09,0,0,1,29.81,30V250A30,30,0,0,1,325,280ZM75,153.17a20,20,0,0,0-20,20V250a20,20,0,0,0,20,20H325a20,20,0,0,0,20-20V174.83a20.06,20.06,0,0,0-19.88-20l-250-1.66Z"></path> <path class="cls-1" d="M145,236h-9.61V182.68h21.84q9.34,0,13.85,4.71a16.37,16.37,0,0,1-.37,22.95,17.49,17.49,0,0,1-12.38,4.53H145Zm0-29.37h11.37q4.45,0,6.8-2.19a7.58,7.58,0,0,0,2.34-5.82,8,8,0,0,0-2.17-5.62q-2.17-2.34-7.83-2.34H145Z"></path> <path class="cls-1" d="M183,236V182.68H202.7q10.9,0,17.5,7.71t6.6,19q0,11.33-6.8,18.95T200.55,236Zm9.88-7.85h8a14.36,14.36,0,0,0,10.94-4.84q4.49-4.84,4.49-14.41a21.91,21.91,0,0,0-3.93-13.22,12.22,12.22,0,0,0-10.37-5.41h-9.14Z"></path> <path class="cls-1" d="M245.59,236H235.7V182.68h33.71v8.24H245.59v14.57h18.75v8H245.59Z"></path> </g> </g></svg>'))
                ->route('beneficiario.pdf', ['cedula' => $row->ci ?? 0], '_blank'),
        ];
    }

    #[\Livewire\Attributes\On('bulkActivation.{tableName}')]
    public function bulkActivation()
    {
        if ($this->checkboxValues) {
            $this->js('
        Swal.fire({
            title: "Ingrese la tasa de interés (sin símbolo):",
            input: "number",
            inputAttributes: {
                min: 0,
                step: 0.01
            },
            showCancelButton: true,
            confirmButtonText: "Siguiente",
            cancelButtonText: "Cancelar",
            inputValidator: (value) => {
                if (!value || isNaN(value) || value < 0) {
                    return "Por favor, ingrese un número válido mayor o igual a 0";
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: "Ingrese la tasa de seguro (sin símbolo):",
                    input: "number",
                    inputAttributes: {
                        min: 0,
                        step: 0.01
                    },
                    showCancelButton: true,
                    confirmButtonText: "Confirmar",
                    cancelButtonText: "Cancelar",
                    inputValidator: (value) => {
                        if (!value || isNaN(value) || value < 0) {
                            return "Por favor, ingrese un número válido mayor o igual a 0";
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

    #[\Livewire\Attributes\On('bulkExport.{tableName}')]
    public function bulkExport()
    {
        if ($this->checkboxValues) {
            $data = collect($this->checkboxValues);
            return redirect()->route('beneficiario.bulk-pdf', ['data' => json_encode($data)]);
            $this->js('window.pgBulkActions.clearAll()');
        }
    }

    public function header(): array
    {
        return [
            Button::add('bulk-activation')
                ->confirm('Confirma la creacion masiva automatica de planes para los beneficiarios seleccionados?')
                ->slot(__('<svg fill="#7cb518" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="-49.15 -49.15 589.82 589.82" xml:space="preserve" width="45px" height="45px" stroke="#7cb518" stroke-width="0.0049152"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g> <g> <path d="M342.165,0H40.96v491.52h409.6V108.4L342.165,0z M348.16,34.956l67.441,67.444H348.16V34.956z M430.08,471.04H61.44V20.48 h266.24v102.4h102.4V471.04z"></path> </g> </g> <g> <g> <polygon points="177.08,197.56 122.88,251.76 99.4,228.28 84.92,242.76 122.88,280.72 191.56,212.04 "></polygon> </g> </g> <g> <g> <rect x="215.04" y="215.04" width="81.92" height="20.48"></rect> </g> </g> <g> <g> <rect x="215.04" y="256" width="184.32" height="20.48"></rect> </g> </g> <g> <g> <polygon points="177.08,320.44 122.88,374.64 99.4,351.16 84.92,365.64 122.88,403.6 191.56,334.92 "></polygon> </g> </g> <g> <g> <rect x="215.04" y="337.92" width="81.92" height="20.48"></rect> </g> </g> <g> <g> <rect x="215.04" y="378.88" width="184.32" height="20.48"></rect> </g> </g> </g></svg>'))
                ->class('border mr-2 px-2 py-1 rounded-md relative cursor-pointer')
                ->dispatch('bulkActivation.' . $this->tableName, []),

            Button::add('bulk-adjust')
                ->confirm('Confirma el reajuste masivo automatico de planes para los beneficiarios seleccionados?')
                ->slot(__('<svg fill="#ffba08" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="-49.15 -49.15 589.82 589.82" xml:space="preserve" width="45px" height="45px" stroke="#ffba08" stroke-width="0.0049152"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g> <g> <path d="M342.165,0H40.96v491.52h409.6V108.4L342.165,0z M348.16,34.956l67.441,67.444H348.16V34.956z M430.08,471.04H61.44V20.48 h266.24v102.4h102.4V471.04z"></path> </g> </g> <g> <g> <polygon points="177.08,197.56 122.88,251.76 99.4,228.28 84.92,242.76 122.88,280.72 191.56,212.04 "></polygon> </g> </g> <g> <g> <rect x="215.04" y="215.04" width="81.92" height="20.48"></rect> </g> </g> <g> <g> <rect x="215.04" y="256" width="184.32" height="20.48"></rect> </g> </g> <g> <g> <polygon points="177.08,320.44 122.88,374.64 99.4,351.16 84.92,365.64 122.88,403.6 191.56,334.92 "></polygon> </g> </g> <g> <g> <rect x="215.04" y="337.92" width="81.92" height="20.48"></rect> </g> </g> <g> <g> <rect x="215.04" y="378.88" width="184.32" height="20.48"></rect> </g> </g> </g></svg>'))
                ->class('border px-2 py-1 rounded-md relative cursor-pointer')
                ->dispatch('bulkAdjust.' . $this->tableName, []),

            Button::add('bulk-export')
                ->confirm('Confirma la exportación masiva de los beneficiarios seleccionados?')
                ->slot(__('<svg fill="#ff0000" width="45px" height="45px" viewBox="0 0 64 64" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:serif="http://www.serif.com/" xmlns:xlink="http://www.w3.org/1999/xlink" stroke="#ff0000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g id="ICON"> <path d="M48.875,44.407l-0,-2.407c0,-0.376 0.21,-0.719 0.545,-0.89c0.334,-0.171 0.736,-0.141 1.04,0.079l10.125,7.313c0.261,0.188 0.415,0.489 0.415,0.811c-0,0.321 -0.154,0.622 -0.415,0.81l-10.125,7.313c-0.304,0.22 -0.706,0.25 -1.04,0.079c-0.335,-0.171 -0.545,-0.514 -0.545,-0.89c-0,-0 -0,-2.375 -0,-2.375c-0,0 -3.5,0 -3.501,0c-3.991,0 -7.594,2.381 -9.162,6.044c-0.037,0.134 -0.099,0.243 -0.173,0.33c-0.313,0.425 -0.789,0.376 -0.789,0.376c-0,-0 -1,-0.059 -1,-1.001c0,-4.144 1.646,-8.118 4.576,-11.048c2.694,-2.693 6.27,-4.302 10.049,-4.544Zm-11.939,11.325c0.661,-2.005 1.784,-3.847 3.304,-5.367c2.555,-2.555 6.021,-3.99 9.634,-3.99l0.001,-0c0.552,-0 1,-0.448 1,-1l-0,-1.419c0,-0 7.417,5.357 7.417,5.357c0,-0.001 -7.417,5.356 -7.417,5.356c-0,0 -0,-1.419 -0,-1.419c-0,-0.552 -0.448,-1 -1,-1l-4.501,0c-3.225,0 -6.238,1.294 -8.438,3.482Zm-23.936,-43.732l-7,0c-1.657,-0 -3,1.343 -3,3l-0,4c-0,0.552 0.448,1 1,1c0.552,0 1,-0.448 1,-1l0,-4c0,-0.552 0.448,-1 1,-1c5.455,0 20.545,0 26,0c0.552,-0 1,0.448 1,1c0,0 -0,12.5 -0,12.5c-0,0.552 -0.448,1 -1,1c-5.455,0 -20.545,0 -26,0c-0.265,0 -0.52,-0.105 -0.707,-0.293c-0.188,-0.187 -0.293,-0.442 -0.293,-0.707c-0,-0 -0,-5 -0,-5c-0,-0.552 -0.448,-1 -1,-1c-0.552,0 -1,0.448 -1,1l-0,5c0,0.796 0.316,1.559 0.879,2.121c0.562,0.563 1.325,0.879 2.121,0.879l7,0l0,17.5c0,2.761 2.239,5 5,5c5.706,-0 15.5,-0 15.5,-0c0.552,-0 1,-0.448 1,-1c-0,-0.552 -0.448,-1 -1,-1c-0,-0 -9.794,-0 -15.5,0c-1.657,-0 -3,-1.343 -3,-3l0,-17.5l17,0c1.657,0 3,-1.343 3,-3l-0,-12.5c-0,-1.657 -1.343,-3 -3,-3l-17,0l0,-4.621c0,-1.314 1.065,-2.379 2.379,-2.379l22.621,0l0,9c0,1.657 1.343,3 3,3l10,0l-0,24c-0,0.552 0.448,1 1,1c0.552,0 1,-0.448 1,-1l-0,-25.172c0,-0.281 -0.118,-0.548 -0.324,-0.738l-12.903,-11.827c-0.185,-0.169 -0.426,-0.263 -0.676,-0.263l-23.718,-0c-2.418,0 -4.379,1.961 -4.379,4.379l0,4.621Zm7,31l25.5,-0c0.552,-0 1,-0.448 1,-1c-0,-0.552 -0.448,-1 -1,-1l-25.5,-0c-0.552,-0 -1,0.448 -1,1c-0,0.552 0.448,1 1,1Zm-0,-4l25.5,-0c0.552,-0 1,-0.448 1,-1c-0,-0.552 -0.448,-1 -1,-1l-25.5,-0c-0.552,-0 -1,0.448 -1,1c-0,0.552 0.448,1 1,1Zm0,-4l25.5,-0c0.552,0 1,-0.448 1,-1c0,-0.552 -0.448,-1 -1,-1l-25.5,-0c-0.552,0 -1,0.448 -1,1c0,0.552 0.448,1 1,1Zm-10,-12.5l1,0c1.657,0 3,-1.343 3,-3c-0,-1.657 -1.343,-3 -3,-3l-2,0c-0.552,0 -1,0.448 -1,1l0,8c0,0.552 0.448,1 1,1c0.552,0 1,-0.448 1,-1l0,-3Zm6,3l-0,-8c-0,-1.075 1.024,-1 1.029,-1c1.319,0 2.583,0.524 3.515,1.456c0.932,0.932 1.456,2.197 1.456,3.515l0,0.058c0,1.318 -0.524,2.583 -1.456,3.515c-0.932,0.932 -2.197,1.456 -3.515,1.456l-0.029,0c-0.552,0 -1,-0.448 -1,-1Zm10,-3l3,0c0.552,-0 1,-0.448 1,-1c-0,-0.552 -0.448,-1 -1,-1l-3,0l0,-2c-0,0 3,0 3,0c0.552,-0 1,-0.448 1,-1c-0,-0.552 -0.448,-1 -1,-1l-4,0c-0.552,0 -1,0.448 -1,1l0,8c0,0.552 0.448,1 1,1c0.552,0 1,-0.448 1,-1l0,-3Zm-8,-3.837c0.421,0.146 0.809,0.386 1.13,0.707c0.557,0.557 0.87,1.313 0.87,2.101l0,0.058c0,0.788 -0.313,1.544 -0.87,2.101c-0.321,0.321 -0.709,0.561 -1.13,0.707l0,-5.674Zm-8,1.837l1,0c0.552,0 1,-0.448 1,-1c-0,-0.552 -0.448,-1 -1,-1c0,0 -1,0 -1,-0l0,2Zm32,-14.316l0,7.816c0,0.552 0.448,1 1,1l8.617,0l-9.617,-8.816Z"></path> </g> </g></svg>'))
                ->class('border ml-12 mr-2 px-2 py-1 rounded-md relative cursor-pointer')
                ->dispatch('bulkExport.' . $this->tableName, []),
        ];
    }
}
