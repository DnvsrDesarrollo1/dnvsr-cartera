<?php

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use App\Models\Plan;
use App\Models\Readjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\FinanceTrait;

class PlanController extends Controller
{
    use FinanceTrait;

    public function index()
    {
        //$plans = Plan::paginate(500);
        //return view('plans.index', compact('plans'));
    }

    public function create() {}

    public function pdfMora()
    {
        $lVencidos = Plan::where('estado', 'VENCIDO')->distinct('idepro')->pluck('idepro');
        $lBeneficiarios = Beneficiary::whereIn('idepro', $lVencidos)->where('estado', '!=', 'BLOQUEADO')->orderBy('proyecto')->get();
        $lProyectos = Beneficiary::whereIn('idepro', $lVencidos)->orderBy('proyecto')->distinct('proyecto')->get(['proyecto', 'departamento']);
        $todosProyectos = Beneficiary::whereIn('proyecto', $lProyectos->pluck('proyecto'))->orderBy('proyecto')->distinct('proyecto')->get(['proyecto', 'departamento']);
        $lProyectos = $lBeneficiarios->groupBy('proyecto')->map(function ($group, $proyecto) use ($todosProyectos) {
            $totalBeneficiarios = Beneficiary::where('proyecto', $proyecto)->count();
            $morosos = $group->count();
            $porcentajeMora = $totalBeneficiarios > 0 ? ($morosos / $totalBeneficiarios) * 100 : 0;
            $departamento = $group->first()->departamento ?? 'N/A';

            return [
                'morosos' => $morosos,
                'total' => $totalBeneficiarios,
                'porcentajeMora' => $porcentajeMora,
                'departamento' => $departamento,
            ];
        });

        $lProyectos = $lProyectos->sortBy('departamento');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('plans.mora-pdf', compact('lProyectos'));
        return $pdf->stream('mora-pdf');
    }

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
            $validatedData['fecha_inicio'],
            \App\Models\Earn::where('idepro', $request->input('idepro'))->sum('interes') ?? 0,
            \App\Models\Earn::where('idepro', $request->input('idepro'))->sum('seguro') ?? 0,
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
        $beneficiary = Beneficiary::where('idepro', $request->input('idepro'))->first();

        $userId = \Illuminate\Support\Facades\Auth::user()->id ?? 1;

        $data = $this->generatePlanData($request);
        $diferimento = $this->generateDiferimentoIfNeeded($request, $data);

        $cuotasPagadas = $this->deactivateExistingRecords($request->input('idepro'));

        $filePath = $cuotasPagadas->count() > 0 ? $this->downloadPlanCollection($cuotasPagadas, 'cuotas_canceladas') : null;

        $this->createNewRecords($data, $diferimento, $request, $userId);

        // Redirect after storing the file
        return redirect()->route('beneficiario.show', ['cedula' => $beneficiary->ci])
            ->with('success', 'El plan de pagos fue generado correctamente!')
            ->with('file', $filePath);
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
            Beneficiary::where('idepro', $beneficiary)->first()->spends()->where('estado', 'ACTIVO')->sum('monto') ?? 0,
            $validatedData['meses'],
            $validatedData['taza_interes'],
            $request->input('seguro'),
            $request->input('correlativo'),
            $request->input('plazo_credito'),
            $validatedData['fecha_inicio'],
            \App\Models\Earn::where('idepro', $request->input('idepro'))->sum('interes') ?? 0,
            \App\Models\Earn::where('idepro', $request->input('idepro'))->sum('seguro') ?? 0,
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

    public function bulkExportXLSX($data)
    {
        $beneficiaries = Beneficiary::find(json_decode($data, true))->pluck('idepro');

        $plans = Plan::join('beneficiaries', 'beneficiaries.idepro', '=', 'plans.idepro')
            ->select(
                'plans.id',
                'plans.idepro',
                'plans.fecha_ppg',
                'plans.prppgnpag',
                'plans.prppgcapi',
                'plans.prppginte',
                'plans.prppgsegu',
                'plans.prppgotro',
                'plans.prppgtota',
                'plans.prppggral',
                'plans.prppgcarg',
                'plans.estado',
                'beneficiaries.nombre',
                'beneficiaries.ci',
                'beneficiaries.complemento',
                'beneficiaries.expedido'
            )
            ->whereIn('plans.idepro', $beneficiaries)
            ->where('plans.estado', '<>', 'INACTIVO')
            ->orderBy('plans.fecha_ppg', 'asc')
            ->get();

        $readj = Readjustment::join('beneficiaries', 'beneficiaries.idepro', '=', 'readjustments.idepro')
            ->select(
                'readjustments.id',
                'readjustments.idepro',
                'readjustments.fecha_ppg',
                'readjustments.prppgnpag',
                'readjustments.prppgcapi',
                'readjustments.prppginte',
                'readjustments.prppgsegu',
                'readjustments.prppgotro',
                'readjustments.prppgtota',
                'readjustments.prppggral',
                'readjustments.prppgcarg',
                'readjustments.estado',
                'beneficiaries.nombre',
                'beneficiaries.ci',
                'beneficiaries.complemento',
                'beneficiaries.expedido'
            )
            ->whereIn('readjustments.idepro', $beneficiaries)
            ->where('readjustments.estado', '<>', 'INACTIVO')
            ->orderBy('readjustments.fecha_ppg', 'asc')
            ->get();

        try {
            $fileName = 'exportacion_planes_' . uniqid() . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ];

            $callback = function () use ($plans, $readj) {
                $file = fopen('php://output', 'w');
                fputcsv($file, [
                    'NOMBRES',
                    'CI',
                    'COMPLEMENTO',
                    'EXPEDIDO',
                    'ID',
                    'IDEPRO',
                    'FECHA PPG',
                    'NRO CUOTA',
                    'CAPITAL',
                    'INTERES',
                    'INTERES DEVG',
                    'SEGURO',
                    'SEGURO DEVG',
                    'GASTOS ADM/JUD',
                    'TOTAL CUOTA',
                    'ESTADO',
                ]);

                foreach ($plans as $plan) {
                    fputcsv($file, [
                        $plan->nombre,
                        $plan->ci,
                        $plan->complemento,
                        $plan->expedido,
                        $plan->id,
                        $plan->idepro,
                        $plan->fecha_ppg,
                        $plan->prppgnpag,
                        $plan->prppgcapi,
                        $plan->prppginte,
                        $plan->prppggral,
                        $plan->prppgsegu,
                        $plan->prppgcarg,
                        $plan->prppgotro,
                        $plan->prppgtota,
                        $plan->estado,
                    ]);
                }

                foreach ($readj as $r) {
                    fputcsv($file, [
                        $r->nombre,
                        $r->ci,
                        $r->complemento,
                        $r->expedido,
                        $r->id,
                        $r->idepro,
                        $r->fecha_ppg,
                        $r->prppgnpag,
                        $r->prppgcapi,
                        $r->prppginte,
                        $r->prppggral,
                        $r->prppgsegu,
                        $r->prppgcarg,
                        $r->prppgotro,
                        $r->prppgtota,
                        $r->estado,
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return redirect()->route('beneficiario.index')
                ->with('error', "La exportación masiva de {$beneficiaries->count()} beneficiarios no fue realizada.")
                ->with('data', $e->getMessage());
        }
    }

    public function bulkActivation($data)
    {
        $decodedData = json_decode($data, true);
        $interestRate = (float)$decodedData['interes'] ?? -1;
        $secureRate = (float)$decodedData['seguro'] ?? -1;
        $identificationNumbers = array_diff(array_values($decodedData), [$interestRate]);

        //return $interestRate . ' ' . $secureRate . ' ' . json_encode($identificationNumbers);

        $beneficiaries = Beneficiary::find($identificationNumbers)
            ->where('estado', '<>', 'CANCELADO');
        //->where('estado', '<>', 'BLOQUEADO');

        try {
            DB::transaction(function () use ($beneficiaries, $interestRate, $secureRate) {
                foreach ($beneficiaries as $beneficiary) {
                    $this->activateBeneficiary($beneficiary, $interestRate, $secureRate);
                }
            });

            return back()
                ->with('success', "La activación masiva-automática de {$beneficiaries->count()} beneficiarios fue realizada");
        } catch (\Exception $e) {
            return back()
                ->with('error', "La activación masiva-automática de {$beneficiaries->count()} beneficiarios no fue realizada ->" . $e->getMessage());
        }
    }

    public function activateBeneficiary(Beneficiary $beneficiary, $interestRate, $secureRate)
    {
        $initialCapital = $beneficiary->saldo_credito ?? 0;
        if ($initialCapital <= 0) {
            $initialCapital = $beneficiary->total_activado - ($beneficiary->payments()->where('prtdtdesc', 'like', '%CAPI%')->sum('montopago') ?? 0);
        }

        $finPlazo = date(
            'Y-m-d',
            //strtotime($beneficiary->fecha_activacion . ' + 20 years'));
            strtotime($beneficiary->fecha_activacion . ' + ' . $beneficiary->plazo_credito . ' months')
        );

        //// DETERMINAR LA CANTIDAD DE MESES/CUOTAS QUE SE VAN A GENERAR

        //$date1 = date('Y-m-d', strtotime($beneficiary->fecha_extendida));
        $date1 = now();
        $date2 = $finPlazo;
        $d1 = new \DateTime($date2);
        $d2 = new \DateTime($date1);
        $MonthsX = $d2->diff($d1);
        $months = (($MonthsX->y) * 12) + ($MonthsX->m) + (($MonthsX->invert) ? -1 : 0) + (($MonthsX->d > 15) ? 1 : 0);

        $sequential = $beneficiary->plans()->exists() ? 'on' : null;

        //// DETERMINAR LA FECHA DE LA PRIMERA CUOTA

        //$startDate = '2024-10-10'; //PARA CONSIDERAR EL MES DESPUES DEL ESPECIFICADO
        $startDate = now(); // PARA CONSIDERAR EL MES SIGUIENTE A AHORA
        //$startDate = date('Y-m-d', strtotime($beneficiary->fecha_extendida));

        if ($interestRate < 0 || $interestRate == -1 || $interestRate == '-1') {
            $interestRate = ($beneficiary->tasa_interes > 0) ? $beneficiary->tasa_interes : 0;
        }

        if ($secureRate < 0 || $secureRate == -1 || $secureRate == '-1') {
            $secureRate = ($beneficiary->insurance && $beneficiary->insurance->exists()) ? $beneficiary->insurance->tasa_seguro : 0;
        }

        $planData = $this->generarPlan(
            (float) $initialCapital,
            \App\Models\Spend::where('idepro', $beneficiary->idepro)->where('estado', 'ACTIVO')->sum('monto') ?? 0,
            //$beneficiary->plazo_credito,
            $months,
            $interestRate,
            $secureRate,
            $sequential,
            $beneficiary->plazo_credito,
            $startDate,
            \App\Models\Earn::where('idepro', $beneficiary->idepro)->sum('interes') ?? 0,
            \App\Models\Earn::where('idepro', $beneficiary->idepro)->sum('seguro') ?? 0,
        );

        $this->deactivateRelatedRecords($beneficiary);
        $this->createNewPlans($beneficiary, $planData);
    }

    public function deactivateRelatedRecords(Beneficiary $beneficiary)
    {
        $relatedModels = [
            //'helpers',
            'plans',
            'readjustments'
        ];

        foreach ($relatedModels as $relation) {
            $beneficiary->$relation()->delete();
        }
    }

    public function createNewPlans(Beneficiary $beneficiary, $planData)
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
                'user_id' => \Illuminate\Support\Facades\Auth::user()->id ?? 1,
            ];
        })->toArray();

        Plan::insert($newPlans);
    }

    public function bulkAdjust($data)
    {
        $identificationNumbers = json_decode($data, true);

        $beneficiaries = Beneficiary::find($identificationNumbers)
            ->where('estado', '<>', 'CANCELADO');

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

    public function adjustBeneficiary(Beneficiary $beneficiary)
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
            $startDate,
            \App\Models\Earn::where('idepro', $beneficiary->idepro)->sum('interes') ?? 0,
            \App\Models\Earn::where('idepro', $beneficiary->idepro)->sum('seguro') ?? 0,
        );

        $this->deactivateRelatedRecords($beneficiary);
        $this->createNewReadjustments($beneficiary, $planData);
    }

    public function createNewReadjustments(Beneficiary $beneficiary, $planData)
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
                'user_id' => \Illuminate\Support\Facades\Auth::user()->id ?? 1,
            ];
        })->toArray();

        Readjustment::insert($newReadjustments);
    }
}
