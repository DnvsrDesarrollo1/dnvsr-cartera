<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::paginate(500);
        return view('plans.index', compact('plans'));
    }

    public function create()
    {
    }

    public function store(Request $request)
    {
    }

    public function show(Plan $plan)
    {
    }

    public function edit(Request $request)
    {
        return $request;
    }

    public function update(Request $request, Plan $plan)
    {
    }

    public function destroy(Plan $plan)
    {
    }

    public function reajuste(Request $request)
    {
        $validatedData = $request->validate([
            'capital_inicial' => 'required|numeric',
            'taza_interes' => 'required|numeric',
            'meses' => 'required|numeric',
            'fecha_inicio' => 'required|date',
        ]);

        $data = null;
        $diferimento = null;

        $data = $this->generarPlan(
            $validatedData['capital_inicial'],
            $validatedData['meses'],
            $validatedData['taza_interes'],
            $request->input('correlativo'),
            $request->input('plazo_credito'),
            $validatedData['fecha_inicio']
        );

        if ($request->input('diff_cuotas') AND
            $request->input('diff_capital') AND
            $request->input('diff_interes'))
        {
            $diferimento = $this->generarDiferimento($request->input('diff_cuotas'),
                                                    $request->input('diff_capital'),
                                                    $request->input('diff_interes'),
                                                    $request->input('plazo_credito'),
                                                    $data->last()->vencimiento);
        }

        return view('plans.show', compact(
                                            'data',
                                            'request',
                                            'diferimento'
                                            ));
    }
}
