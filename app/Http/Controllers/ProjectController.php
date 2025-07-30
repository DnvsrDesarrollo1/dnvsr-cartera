<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Auth::user()->projects;

        return view('projects.index', compact('projects'));
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
