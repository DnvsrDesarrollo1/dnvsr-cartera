<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Foreach_;

class PlanController extends Controller
{
    public function index()
    {
        //$plans = Plan::paginate(500);
        //return view('plans.index', compact('plans'));
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
            $request->input('gastos_judiciales'),
            $validatedData['meses'],
            $validatedData['taza_interes'],
            $request->input('seguro'),
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

    public function bulkActivation($data)
    {
        $decodedData = json_decode($data, true);
        $interestRate = $decodedData['interes'] ?? 0;
        $secureRate = $decodedData['seguro']?? 0;
        $identificationNumbers = array_diff(array_values($decodedData), [$interestRate]);

        $beneficiaries = \App\Models\Beneficiary::whereIn('ci', $identificationNumbers)
            ->where('estado', '<>', 'CANCELADO')
            ->get();

        try {
            \DB::transaction(function () use ($beneficiaries, $interestRate, $secureRate) {
                foreach ($beneficiaries as $beneficiary) {
                    $this->activateBeneficiary($beneficiary, $interestRate, $secureRate);
                }
            });

            return redirect()->route('beneficiario.index')
                ->with('success', "La activación masivo-automática de {$beneficiaries->count()} beneficiarios fue realizada");
        } catch (\Exception $e) {
            return redirect()->route('beneficiario.index')
                ->with('error', "La activación masivo-automática de {$beneficiaries->count()} beneficiarios no fue realizada");
        }
    }

    private function activateBeneficiary(\App\Models\Beneficiary $beneficiary, $interestRate, $secureRate)
    {
        $initialCapital = $beneficiary->total_activado - ($beneficiary->payments()->sum('montopago') ?? 0);
        $months = $beneficiary->plazo_credito;
        $sequential = $beneficiary->plans()->exists() ? 'on' : null;

        $startDate = now();

        $planData = $this->generarPlan(
            $initialCapital,
            $beneficiary->gastos_judiciales,
            $months,
            $interestRate,
            $secureRate,
            $sequential,
            $beneficiary->plazo_credito,
            $startDate
        );

        $this->deactivateRelatedRecords($beneficiary);
        $this->createNewPlans($beneficiary, $planData);
    }

    private function deactivateRelatedRecords(\App\Models\Beneficiary $beneficiary)
    {
        $relatedModels = ['helpers', 'plans', 'readjustments'];

        foreach ($relatedModels as $relation) {
            $beneficiary->$relation()->update([
                'estado' => 'INACTIVO',
                'user_id' => auth()->id()
            ]);
        }
    }

    private function createNewPlans(\App\Models\Beneficiary $beneficiary, $planData)
    {
        $newPlans = $planData->map(function ($item) use ($beneficiary) {
            return [
                'idepro' => $beneficiary->idepro,
                'fecha_ppg' => $item->vencimiento,
                'prppgnpag' => $item->nro_cuota,
                'prppgcapi' => $item->abono_capital,
                'prppginte' => $item->interes,
                'prppgsegu' => $item->seguro,
                'prppgotro' => $item->gastos_judiciales,
                'prppgtota' => $item->total_cuota,
                'estado' => 'ACTIVO',
                'user_id' => auth()->id(),
            ];
        })->toArray();

        \App\Models\Plan::insert($newPlans);
    }

    public function bulkAdjust($data)
    {
        $identificationNumbers = json_decode($data, true);

        $beneficiaries = \App\Models\Beneficiary::whereIn('ci', $identificationNumbers)
            ->where('estado', '<>', 'CANCELADO')
            ->get();

        try {
            \DB::transaction(function () use ($beneficiaries) {
                foreach ($beneficiaries as $beneficiary) {
                    $this->adjustBeneficiary($beneficiary);
                }
            });

            return redirect()->route('beneficiario.index')
                ->with('success', "El reajuste masivo-automático de {$beneficiaries->count()} beneficiarios fue realizado");
        } catch (\Exception $e) {
            return redirect()->route('beneficiario.index')
                ->with('error', "El reajuste masivo-automático de {$beneficiaries->count()} beneficiarios no fue realizado");
        }
    }

    private function adjustBeneficiary(\App\Models\Beneficiary $beneficiary)
    {
        $initialCapital = $beneficiary->total_activado - ($beneficiary->payments()->sum('montopago') ?? 0);
        $months = $beneficiary->plazo_credito;
        $interestRate = 0;
        $sequential = $beneficiary->plans()->exists() ? 'on' : null;

        $startDate = now();

        $planData = $this->generarPlan(
            $initialCapital,
            $beneficiary->gastos_judiciales,
            $months,
            $interestRate,
            null,
            $sequential,
            $beneficiary->plazo_credito,
            $startDate
        );

        $this->deactivateRelatedRecords($beneficiary);
        $this->createNewReadjustments($beneficiary, $planData);
    }

    private function createNewReadjustments(\App\Models\Beneficiary $beneficiary, $planData)
    {
        $newReadjustments = $planData->map(function ($item) use ($beneficiary) {
            return [
                'idepro' => $beneficiary->idepro,
                'fecha_ppg' => $item->vencimiento,
                'prppgnpag' => $item->nro_cuota,
                'prppgcapi' => $item->abono_capital,
                'prppginte' => $item->interes,
                'prppgsegu' => $item->seguro,
                'prppgotro' => $item->gastos_judiciales,
                'prppgtota' => $item->total_cuota,
                'estado' => 'ACTIVO',
                'user_id' => auth()->id(),
            ];
        })->toArray();

        \App\Models\Readjustment::insert($newReadjustments);
    }
}
