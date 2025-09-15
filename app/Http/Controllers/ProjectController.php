<?php

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    public function index()
    {
        if (Auth::user()->hasRole('admin')) {
            // 1. Obtener datos brutos agrupados por departamento y estado
            $rawData = Beneficiary::select('departamento', 'estado', DB::raw('count(*) as total'))
                ->groupBy('departamento', 'estado')
                ->get();

            // 2. Obtener listas únicas de departamentos y estados
            $departments = $rawData->pluck('departamento')->unique()->sort()->values();
            $statuses = $rawData->pluck('estado')->unique()->sort()->values();

            // 3. Mapeo de colores para cada estado
            $statusColors = [
                'Bloqueado' => '#3b82f6',
                'Cancelado' => '#6366f1',
                'Default' => '#6b7280',
                'Ejecucion' => '#ef4444',
                'Vencido' => '#eab308',
                'Vigente' => '#22c55e',
            ];

            // 4. Preparar la estructura de datos para el gráfico (series)
            $seriesData = $statuses->map(function ($status) use ($departments, $rawData, $statusColors) {
                $data = $departments->map(function ($department) use ($status, $rawData) {
                    return $rawData
                        ->where('departamento', $department)
                        ->where('estado', $status)
                        ->first()->total ?? 0;
                });
                return [
                    'name' => $status,
                    'data' => $data,
                    'color' => $statusColors[$status] ?? $statusColors['Default'],
                ];
            });

            // 5. Preparar datos para la tabla de resumen
            $tableData = $rawData->groupBy('departamento')->map(function ($departmentGroup) {
                return [
                    'total' => $departmentGroup->sum('total'),
                    'statuses' => $departmentGroup->pluck('total', 'estado'),
                ];
            });

            // 6. Pasar todos los datos a la vista
            return view('projects.index-admin', [
                'chartCategories' => $departments,
                'chartSeries' => $seriesData,
                'tableData' => $tableData,
                'allStatuses' => $statuses,
                'totalBeneficiaries' => $rawData->sum('total'),
            ]);
        } else {
            return view('projects.index');
        }
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([]);

        Project::create($validated);

        return redirect()->route('proyecto.index')->with('success', 'Project created successfully.');
    }

    public function show($id)
    {
        $project = Project::find($id)->first();

        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            // Add other validation rules as needed
        ]);

        $project->update($validated);

        return redirect()->route('proyecto.index')->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('proyecto.index')->with('success', 'Project deleted successfully.');
    }
}
