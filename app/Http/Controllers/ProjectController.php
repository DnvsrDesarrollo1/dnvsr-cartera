<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Project;
use App\Models\Voucher;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::all();

        $procLegal = Project::where('proy_estado', 'like', '%PROCESO LEGAL%')->count();

        $paraCierre = Project::where('proy_estado', 'like', '%PARA CIERRE%')->count();

        $conCierre = Project::where('proy_estado', 'like', '%CON CIERRE%')->count();

        $ejecucion = Project::where('proy_estado', 'like', '%EJECUCION%')->count();

        return view('projects.index', compact(
            'projects',
            'procLegal',
            'paraCierre',
            'conCierre',
            'ejecucion'
        ));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            // Add other validation rules as needed
        ]);

        Project::create($validated);

        return redirect()->route('projects.index')->with('success', 'Project created successfully.');
    }

    public function show($codigo)
    {
        $project = Project::where('proy_cod', $codigo)->first();
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

        return redirect()->route('projects.index')->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
    }
}
