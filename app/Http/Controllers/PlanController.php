<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlanController extends Controller
{
    public function index()
    {
        //$plans = Plan::paginate(500);
        //return view('plans.index', compact('plans'));
    }

    public function create() {}

    public function store(Request $request)
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
            \App\Models\Spend::where('idepro', $request->input('idepro'))->where('estado', 'ACTIVO')->first()->monto ?? 0,
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
        $beneficiary = \App\Models\Beneficiary::where('idepro', $request->input('idepro'))->first();

        $userId = auth()->user()->id ?? 1;

        $data = $this->generatePlanData($request);
        $diferimento = $this->generateDiferimentoIfNeeded($request, $data);

        $this->deactivateExistingRecords($request->input('idepro'), $userId);
        $this->createNewRecords($data, $diferimento, $request, $userId);

        return redirect()->route(
            'beneficiario.show',
            [
                'cedula' => $beneficiary->ci
            ]
        )->with('success', 'El plan de pagos fue generado correctamente!');
    }

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

        $beneficiary = $request->input('idepro');

        $data = $this->generarPlan(
            $validatedData['capital_inicial'],
            \App\Models\Spend::where('idepro', $request->input('idepro'))->where('estado', 'ACTIVO')->first()->monto ?? 0,
            $validatedData['meses'],
            $validatedData['taza_interes'],
            $request->input('seguro'),
            $request->input('correlativo'),
            $request->input('plazo_credito'),
            $validatedData['fecha_inicio'],
            \App\Models\Earn::where('idepro', $beneficiary)->first()->interes ?? 0,
            \App\Models\Earn::where('idepro', $beneficiary)->first()->seguro ?? 0,
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

        //return $data;

        return view('plans.show', compact(
            'data',
            'request',
            'diferimento'
        ));
    }

    public function bulkActivation($data)
    {
        $decodedData = json_decode($data, true);
        $interestRate = $decodedData['interes'] ?? -1;
        $secureRate = $decodedData['seguro'] ?? -1;
        $identificationNumbers = array_diff(array_values($decodedData), [$interestRate]);

        //return $interestRate . ' ' . $secureRate . ' ' . json_encode($identificationNumbers);

        $beneficiaries = \App\Models\Beneficiary::whereIn('ci', $identificationNumbers)
            ->where('estado', '<>', 'CANCELADO')
            //->where('estado', '<>', 'BLOQUEADO')
            ->get();

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($beneficiaries, $interestRate, $secureRate) {
                foreach ($beneficiaries as $beneficiary) {
                    $this->activateBeneficiary($beneficiary, $interestRate, $secureRate);
                }
            });

            return redirect()->route('beneficiario.index')
                ->with('success', "La activación masiva-automática de {$beneficiaries->count()} beneficiarios fue realizada");
        } catch (\Exception $e) {
            return redirect()->route('beneficiario.index')
                ->with('error', "La activación masiva-automática de {$beneficiaries->count()} beneficiarios no fue realizada ->" . $e->getMessage());
        }
    }

    public function activateBeneficiary(\App\Models\Beneficiary $beneficiary, $interestRate, $secureRate)
    {
        $initialCapital = $beneficiary->saldo_credito;
        if ($initialCapital == 0) {
            $beneficiary->total_activado - ($beneficiary->payments()->where('prtdtdesc', 'like', '%CAPI%')->sum('montopago') ?? 0);
        }

        $finPlazo = date('Y-m-d', strtotime($beneficiary->fecha_activacion . ' + 20 years'));

        $date1 = date('Y-m-d', strtotime($beneficiary->fecha_activacion));
        //$date1 = '2024-12-16';
        $date2 = $finPlazo;
        $d1 = new \DateTime($date2);
        $d2 = new \DateTime($date1);
        $MonthsX = $d2->diff($d1);
        $months = (($MonthsX->y) * 12) + ($MonthsX->m) + (($MonthsX->invert) ? -1 : 0) + (($MonthsX->d > 15) ? 1 : 0);

        $sequential = $beneficiary->plans()->exists() ? 'on' : null;

        //$startDate = '2024-10-20';
        $startDate = now();
        //$startDate = date('Y-m-15', strtotime($beneficiary->fecha_activacion));

        if ($interestRate < 0 || $interestRate == -1 || $interestRate == '-1') {
            $interestRate = ($beneficiary->tasa_interes > 0) ? $beneficiary->tasa_interes : 0;
        }

        if ($secureRate < 0 || $secureRate == -1 || $secureRate == '-1') {
            $secureRate = ($beneficiary->insurance && $beneficiary->insurance->exists()) ? $beneficiary->insurance->tasa_seguro : 0;
        }

        $planData = $this->generarPlan(
            $initialCapital,
            \App\Models\Spend::where('idepro', $beneficiary->idepro)->where('estado', 'ACTIVO')->first()->monto ?? 0,
            //$beneficiary->plazo_credito,
            $months,
            $interestRate,
            $secureRate,
            $sequential,
            $beneficiary->plazo_credito,
            $startDate,
            \App\Models\Earn::where('idepro', $beneficiary->idepro)->where('estado', 'ACTIVO')->first()->interes ?? 0,
            \App\Models\Earn::where('idepro', $beneficiary->idepro)->where('estado', 'ACTIVO')->first()->seguro ?? 0,
        );

        $this->deactivateRelatedRecords($beneficiary);
        $this->createNewPlans($beneficiary, $planData);
    }

    public function deactivateRelatedRecords(\App\Models\Beneficiary $beneficiary)
    {
        $relatedModels = [
            //'helpers',
            'plans',
            'readjustments'
        ];

        foreach ($relatedModels as $relation) {
            $beneficiary->$relation()->update([
                'estado' => 'INACTIVO',
                'user_id' => 1
            ]);
        }
    }

    public function createNewPlans(\App\Models\Beneficiary $beneficiary, $planData)
    {
        $newPlans = $planData->map(function ($item) use ($beneficiary) {
            return [
                'idepro' => $beneficiary->idepro,
                'fecha_ppg' => $item->vencimiento,
                'prppgnpag' => $item->nro_cuota,
                'prppgcapi' => ($item->abono_capital),
                'prppginte' => ($item->interes),
                'prppggral' => ($item->interes_devengado),
                'prppgsegu' => ($item->seguro),
                'prppgotro' => ($item->gastos_judiciales),
                'prppgcarg' => ($item->seguro_devengado),
                'prppgtota' => ($item->total_cuota),
                'estado' => 'ACTIVO',
                'user_id' => auth()->id() ?? 1,
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
            DB::transaction(function () use ($beneficiaries) {
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

    public function adjustBeneficiary(\App\Models\Beneficiary $beneficiary)
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

    public function createNewReadjustments(\App\Models\Beneficiary $beneficiary, $planData)
    {
        $newReadjustments = $planData->map(function ($item) use ($beneficiary) {
            return [
                'idepro' => $beneficiary->idepro,
                'fecha_ppg' => $item->vencimiento,
                'prppgnpag' => $item->nro_cuota,
                'prppgcapi' => round($item->abono_capital, 2),
                'prppginte' => round($item->interes, 2),
                'prppgsegu' => round($item->seguro, 2),
                'prppgotro' => round($item->gastos_judiciales, 2),
                'prppgtota' => round($item->total_cuota, 2),
                'estado' => 'ACTIVO',
                'user_id' => auth()->id() ?? 1,
            ];
        })->toArray();

        \App\Models\Readjustment::insert($newReadjustments);
    }
}
