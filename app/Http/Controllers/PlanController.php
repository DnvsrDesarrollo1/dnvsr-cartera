<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Foreach_;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::paginate(500);
        return view('plans.index', compact('plans'));
    }

    public function create() {}

    public function store(Request $request) {}

    public function show(Plan $plan) {}

    public function edit(Request $request)
    {
        return $request;
    }

    public function update(Request $request, Plan $plan) {}

    public function destroy(Plan $plan) {}

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

        if (
            $request->input('diff_cuotas') and
            $request->input('diff_capital') and
            $request->input('diff_interes')
        ) {
            $diferimento = $this->generarDiferimento(
                $request->input('diff_cuotas'),
                $request->input('diff_capital'),
                $request->input('diff_interes'),
                $request->input('plazo_credito'),
                $data->last()->vencimiento
            );
        }

        return view('plans.show', compact(
            'data',
            'request',
            'diferimento'
        ));
    }

    public function bulkAdjust($data)
    {
        $lista = json_decode($data, true);
        $l = [];

        for ($i = 0; $i < count($lista); $i++) {
            $l[] = $lista[$i];
        }

        $beneficiaries = \App\Models\Beneficiary::whereIn('ci', $l)
            ->where('estado', '<>', 'CANCELADO')
            ->get();

        try {
            foreach ($beneficiaries as $index => $value) {
                $capital_inicial = $value->total_activado - (\App\Models\Payment::where('numprestamo', $value->idepro)->sum('montopago') ?? 0);
                $meses = $this->calculateRemainingMonths($value);
                $taza_interes = 3;
                $correlativo = 'on';
                $plazo_credito = $value->plazo_credito;
                $fecha_inicio = now()->format('Y-m-d');
                $data = $this->generarPlan($capital_inicial, $meses, $taza_interes, $correlativo, $plazo_credito, $fecha_inicio);

                $helperData = \App\Models\Helper::where('idepro', $value->idepro)->get();
                foreach ($helperData as $h) {
                    $h->estado = 'INACTIVO';
                    $h->save();
                }

                $helperData = null;

                $planData = \App\Models\Plan::where('idepro', $value->idepro)->get();

                foreach ($planData as $plan) {
                    $plan->estado = 'INACTIVO';
                    $plan->save();
                }

                $planData = null;

                $readjustmentData = \App\Models\Readjustment::where('idepro', $value->idepro)->get();

                foreach ($readjustmentData as $readjustment) {
                    $readjustment->estado = 'INACTIVO';
                    $readjustment->save();
                }

                $readjustmentData = null;

                foreach ($data as $d) {
                    \App\Models\Readjustment::create([
                        'idepro' => $value->idepro,
                        'fecha_ppg' => $d->vencimiento,
                        'prppgnpag' => $d->nro_cuota,
                        'prppgcapi' => $d->abono_capital,
                        'prppginte' => $d->interes,
                        'prppgsegu' => $d->seguro,
                        'prppgtota' => $d->total_cuota,
                        'estado' => 'ACTIVO',
                    ]);
                }
            }

            return redirect()->route('beneficiario.index')
                ->with('success', 'El reajuste masivo-automatico de ' . $beneficiaries->count() . ' beneficiarios fue realizado');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('beneficiario.index')
                ->with('error', 'El reajuste masivo-automatico de ' . $beneficiaries->count() . ' beneficiarios no fue realizado');
        }
    }

    private function calculateRemainingMonths(\App\Models\Beneficiary $beneficiary)
    {
        $now = new \DateTime('now');
        $endDate = (new \DateTime($beneficiary->fecha_activacion))
            ->modify("+{$beneficiary->plazo_credito} months");

        $dias = $endDate->diff($now)->format('%a');
        return round($dias / 30.5, 0);
    }
}
