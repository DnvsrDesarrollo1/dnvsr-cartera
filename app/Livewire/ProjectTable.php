<?php

namespace App\Livewire;

use App\Models\Project;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class ProjectTable extends PowerGridComponent
{
    public string $tableName = 'projects';

    public function setUp(): array
    {
        $this->showCheckBox('cod_proy_credito');

        return [
            PowerGrid::header(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount('short'),
        ];
    }

    public function datasource(): Builder
    {
        return Project::query();
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('proy_cod')
            ->add('cod_proy_credito')
            ->add('proy_nombre')
            ->add('proy_subprograma')
            ->add('proy_numActa')
            ->add('proy_fechaAprobacion')
            ->add('proy_numViviendas')
            ->add('proy_estado')
            ->add('proy_modalidad')
            ->add('entidad_inter_finan')
            ->add('proy_programa')
            ->add('fecha_ini_obra')
            ->add('fecha_fin_obra')
            ->add('proy_viv_concluidas')
            ->add('proy_viv_cartera')
            ->add('proy_componente')
            ->add('proy_depto')
            ->add('proy_provincia')
            ->add('proy_municipio')
            ->add('proy_ubicacion')
            ->add('proy_avance_finan')
            ->add('proy_avance_fis');
    }

    public function columns(): array
    {
        return [
            Column::make('Proyecto', 'proy_nombre')
                ->sortable(),

            Column::make('Acta', 'proy_numActa')
                ->hidden(),
            Column::make('Estado', 'proy_estado')
                ->hidden(),

            Column::make('Subprograma', 'proy_subprograma')
                ->sortable(),

            Column::make('Departamento', 'proy_depto'),

            Column::action('Opciones')
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('proy_nombre')->operators(['contains']),
            Filter::inputText('proy_numActa')->operators(['contains']),

            Filter::select('proy_subprograma', 'proy_subprograma')
                ->dataSource(Project::select('proy_subprograma')->distinct()->get())
                ->optionValue('proy_subprograma')
                ->optionLabel('proy_subprograma'),

            Filter::select('proy_depto', 'proy_depto')
                ->dataSource(Project::select('proy_depto')->distinct()->get())
                ->optionValue('proy_depto')
                ->optionLabel('proy_depto'),

            Filter::select('proy_estado', 'proy_estado')
                ->dataSource(Project::select('proy_estado')->distinct()->get())
                ->optionValue('proy_estado')
                ->optionLabel('proy_estado'),
            /*
            Filter::select('departamento', 'departamento')
                ->dataSource(Project::select('departamento')->distinct()->get())
                ->optionValue('departamento')
                ->optionLabel('departamento') */
        ];
    }

/*     #[\Livewire\Attributes\On('edit')]
    public function edit($rowId)
    {
        return redirect()->route('project.edit', ['cod' => $rowId]);
    } */

    public function actions($row): array
    {
        return [
            Button::add('show')
                ->slot(__('<svg fill="#000000" height="35px" width="35px" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="-47.2 -47.2 566.45 566.45" xml:space="preserve" stroke="#000000" stroke-width="0.00472047"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g> <path d="M452.842,431.982l-10.925-32.184c-19.086-56.186-68.4-95.763-126.107-103.326c-21.408,23.559-49.274,37.939-79.797,37.939 c-30.516,0-58.375-14.38-79.783-37.939c-57.7,7.563-107.014,47.14-126.1,103.326l-10.925,32.184 c-3.145,9.255-1.638,19.458,4.061,27.409c5.692,7.945,14.869,12.656,24.645,12.656h376.224c9.777,0,18.954-4.712,24.645-12.656 C454.48,451.44,455.987,441.237,452.842,431.982z"></path> <path d="M236.013,310.558c53.266,0,96.507-56.559,97.967-127.023H138.068C139.528,253.999,182.769,310.558,236.013,310.558z"></path> <path d="M116.133,152.296c3.184,1.57,6.709,2.532,10.498,2.532h217.396h1.398c3.781,0,7.315-0.962,10.491-2.532 c7.889-3.882,13.363-11.927,13.363-21.322c0-13.176-10.677-23.853-23.853-23.853h-1.878h-2.501 c-0.682-3.456-1.49-6.871-2.352-10.273c-6.507-25.631-18.256-49.476-34.778-67.32v17.324c0,8.781-7.121,15.902-15.902,15.902 c-8.783,0-15.902-7.121-15.902-15.902V6.079C265.782,3.238,259.004,1.272,251.921,0v64.191c0,8.781-7.119,15.902-15.902,15.902 c-8.781,0-15.902-7.121-15.902-15.902V0c-7.081,1.272-13.852,3.238-20.18,6.071v40.781c0,8.781-7.121,15.902-15.902,15.902 c-8.781,0-15.902-7.121-15.902-15.902V29.529c-16.516,17.836-28.257,41.665-34.762,67.282c-0.87,3.416-1.678,6.84-2.361,10.311 h-2.508h-1.871c-13.177,0-23.853,10.677-23.853,23.853C102.777,140.362,108.251,148.407,116.133,152.296z"></path> </g> </g></svg>'))
                ->route('proyecto.show', ['codigo' => $row->proy_cod], '_blank'),
        ];
    }

    /*
    public function actionRules($row): array
    {
       return [
            // Hide button edit for ID 1
            Rule::button('edit')
                ->when(fn($row) => $row->id === 1)
                ->hide(),
        ];
    }
    */
}
