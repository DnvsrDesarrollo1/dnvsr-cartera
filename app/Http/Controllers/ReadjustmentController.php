<?php

namespace App\Http\Controllers;

use App\Models\Readjustment;
use Illuminate\Http\Request;

class ReadjustmentController extends Controller
{
    public function index()
    {
        $readjustments = Readjustment::all();
        return view('readjustments.index', compact('readjustments'));
    }

    public function create()
    {
        return view('readjustments.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric',
            'reason' => 'required|string|max:255',
            'date' => 'required|date',
            // Add other validation rules as needed
        ]);

        Readjustment::create($validated);

        return redirect()->route('readjustments.index')->with('success', 'Readjustment created successfully.');
    }

    public function show(Readjustment $readjustment)
    {
        return view('readjustments.show', compact('readjustment'));
    }

    public function edit(Readjustment $readjustment)
    {
        return view('readjustments.edit', compact('readjustment'));
    }

    public function update(Request $request, Readjustment $readjustment)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric',
            'reason' => 'required|string|max:255',
            'date' => 'required|date',
            // Add other validation rules as needed
        ]);

        $readjustment->update($validated);

        return redirect()->route('readjustments.index')->with('success', 'Readjustment updated successfully.');
    }

    public function destroy(Readjustment $readjustment)
    {
        $readjustment->delete();

        return redirect()->route('readjustments.index')->with('success', 'Readjustment deleted successfully.');
    }
}
