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

    public function setUp(): array
    {
        $this->showCheckBox('ci');

        return [
            PowerGrid::header(),

            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount('short')
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
            ->add('monto_credito')
            ->add('monto_activado')
            ->add('gastos_judiciales')
            ->add('saldo_credito')
            ->add('fecha_activacion');
    }

    public function columns(): array
    {
        return [
            Column::make('Nombres', 'nombre')
                ->sortable(),

            Column::make('CI', 'ci')
                ->sortable(),

            Column::make('Complemento', 'complemento'),

            Column::make('Género', 'genero')
                ->sortable(),

            Column::make('Fecha Nacimiento', 'fecha_nacimiento')
                ->sortable(),

            Column::make('Estado', 'estado')
                ->sortable(),

            Column::make('EIF', 'entidad_financiera')
                ->sortable(),

            Column::make('Total Activado', 'total_activado')
                ->sortable(),

            Column::make('Gastos Judiciales', 'gastos_judiciales'),

            Column::make('Saldo Crédito', 'saldo_credito')
                ->sortable(),

            Column::make('Fecha Activación', 'fecha_activacion')
                ->sortable(),

            Column::action('')
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('nombre')->operators(['contains']),
            Filter::inputText('ci')->operators(['contains']),
            Filter::select('estado', 'estado')
                ->dataSource(Beneficiary::select('estado')->distinct()->get())
                ->optionValue('estado')
                ->optionLabel('estado'),
            Filter::select('entidad_financiera', 'entidad_financiera')
                ->dataSource(Beneficiary::select('entidad_financiera')->distinct()->get())
                ->optionValue('entidad_financiera')
                ->optionLabel('entidad_financiera'),
        ];
    }

    public function actions($row): array
    {
        return [
            Button::add('show')
                ->slot('Revisar')
                ->class('py-2 px-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500')
                ->route('beneficiario.show', ['cedula' => $row->ci], '_blank'),
        ];
    }

    #[\Livewire\Attributes\On('bulkManage.{tableName}')]
    public function bulkManage()
    {
        if ($this->checkboxValues) {
            $data = collect($this->checkboxValues);
            return redirect()->route('plan.bulk-adjust', ['data' => json_encode($data)]);
        }
    }
    public function header(): array
    {
        return [
            Button::add('bulk-management')
                ->confirm('Confirma la creacion masiva automatica de planes para los beneficiarios seleccionados?')
                ->slot(__('<span x-text="window.pgBulkActions.count(\'' . $this->tableName . '\')"></span><span> seleccionados</span>'))
                ->class('border mt-2 border-green-600 text-green-600 px-2 py-1 rounded-md relative cursor-pointer')
                ->dispatch('bulkManage.' . $this->tableName, []),
        ];
    }
}
