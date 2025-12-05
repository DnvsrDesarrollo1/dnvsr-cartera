<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateBeneficiaryCsvZip;
use App\Models\Beneficiary;
use App\Models\Plan;
use App\Models\Readjustment;
use App\Traits\FinanceTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PlanController extends Controller
{
    use FinanceTrait;

    public function index()
    {
        // $plans = Plan::paginate(500);
        // return view('plans.index', compact('plans'));
    }

    public function create() {}

    public function pdfMora()
    {
        $lVencidos = Plan::where('estado', 'VENCIDO')
            ->where('prppgmpag', 'SI')
            ->whereNotNull('prppgmpag')
            ->distinct('idepro')
            ->pluck('idepro');

        // Obtenemos todos los beneficiarios vencidos en una sola consulta con los datos necesarios
        $lBeneficiarios = Beneficiary::whereIn('idepro', $lVencidos)
            ->where('estado', '!=', 'BLOQUEADO')
            ->where('estado', '!=', 'CANCELADO')
            ->orderBy('proyecto')
            ->get(['nombre', 'ci', 'proyecto', 'departamento']);

        // Obtenemos la lista única de proyectos directamente de los beneficiarios ya cargados
        $lProyectos = $lBeneficiarios->unique('proyecto')
            ->pluck('proyecto');

        // Pre-cargamos los contadores de beneficiarios por proyecto en una sola consulta
        $totalesPorProyecto = Beneficiary::whereIn('proyecto', $lProyectos)
            ->where('estado', '!=', 'BLOQUEADO')
            ->where('estado', '!=', 'CANCELADO')
            ->selectRaw('proyecto, COUNT(*) as total')
            ->groupBy('proyecto')
            ->pluck('total', 'proyecto');

        // Construimos la estructura final procesando los datos en memoria
        $lProyectos = $lBeneficiarios
            ->groupBy('proyecto')
            ->map(function ($group, $proyecto) use ($totalesPorProyecto) {
                $totalBeneficiarios = $totalesPorProyecto[$proyecto] ?? 0;
                $morosos = $group->count();
                $porcentajeMora = $totalBeneficiarios > 0 ? ($morosos / $totalBeneficiarios) * 100 : 0;
                $departamento = $group->first()->departamento ?? 'N/A';

                return [
                    'morosos' => $morosos,
                    'total' => $totalBeneficiarios,
                    'porcentajeMora' => $porcentajeMora,
                    'departamento' => $departamento,
                    'listaBeneficiarios' => $group
                        ->map(fn($item) => [
                            'nombre' => $item->nombre,
                            'ci' => $item->ci,
                        ])->toArray(),
                ];
            });

        $lProyectos = $lProyectos->sortBy('departamento');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('plans.mora-pdf', compact('lProyectos'));

        return $pdf->stream('mora-pdf');
    }

    public function pdfMoraProyecto($proyecto)
    {
        // 1) Obtener todos los beneficiarios del proyecto con sus relaciones necesarias (Eager Loading)
        // Usamos subquery para la fecha del último pago para evitar cargar todos los vouchers en memoria
        // Seleccionamos beneficiaries.* para asegurar que las claves foráneas para las relaciones estén presentes
        $beneficiarios = Beneficiary::query()
            ->where('proyecto', $proyecto)
            ->select('beneficiaries.*')
            ->addSelect([
                'ultimo_pago_fecha' => \App\Models\Voucher::select('fecha_pago')
                    ->whereColumn('numprestamo', 'beneficiaries.idepro')
                    ->orderBy('fecha_pago', 'desc')
                    ->limit(1)
            ])
            ->with([
                'plans' => function ($query) {
                    $query->where('estado', 'VENCIDO')->orderBy('fecha_ppg', 'asc');
                },
                'readjustments' => function ($query) {
                    $query->where('estado', 'VENCIDO')->orderBy('fecha_ppg', 'asc');
                }
            ])
            ->orderBy('nombre')
            ->get();

        // 2) Contar según estado (en memoria, usando la colección ya cargada)
        $estados = $beneficiarios->groupBy('estado')
            ->map(fn($grp) => $grp->count())
            ->sortKeys()
            ->toArray();

        // 3) Construir el reporte
        $reporte = collect();

        // Separador visual
        $reporte->push([
            'Nombre del Proyecto' => '',
            'Beneficiarios' => '',
            'CI' => '',
            'Cod. Prestamo' => '',
            'Estado' => '',
            'Dias Mora' => '',
            'Cuotas Vencidas' => '',
            'F. Ult. Pago' => '',
            'F. Activacion' => '',
        ]);

        foreach ($beneficiarios as $beneficiario) {
            // Lógica equivalente a getCurrentPlan('VENCIDO', '=') pero usando las relaciones eager loaded
            // para evitar consultas N+1 dentro del bucle
            $planesVencidos = $beneficiario->plans;

            if ($planesVencidos->isEmpty()) {
                $planesVencidos = $beneficiario->readjustments;
            }

            $vencidoPlan = $planesVencidos->first();
            $cantidadVencido = $planesVencidos->count();

            $mora = 0;

            if ($vencidoPlan && $vencidoPlan->fecha_ppg) {
                $fechaVenc = \Carbon\Carbon::parse($vencidoPlan->fecha_ppg)->startOfDay();
                $hoy = now()->startOfDay();
                $dias = $fechaVenc->diffInDays($hoy, false);
                $mora = $dias > 0 ? $dias : 0;
            }

            $reporte->push([
                'Nombre del Proyecto' => $proyecto,
                'Beneficiarios' => $beneficiario->nombre,
                'CI' => $beneficiario->ci,
                'Cod. Prestamo' => $beneficiario->idepro,
                'Estado' => $beneficiario->estado,
                'Dias Mora' => $mora,
                'Cuotas Vencidas' => $cantidadVencido,
                'F. Ult. Pago' => $beneficiario->ultimo_pago_fecha ?? 'N/A',
                'F. Activacion' => $beneficiario->fecha_activacion,
            ]);
        }

        $reporte->push([
            'Nombre del Proyecto' => '',
            'Beneficiarios' => '',
            'CI' => '',
            'Cod. Prestamo' => '',
            'Estado' => '',
            'Dias Mora' => '',
            'Cuotas Vencidas' => '',
            'F. Ult. Pago' => '',
            'F. Activacion' => '',
        ]);

        foreach ($estados as $est => $cant) {
            $reporte->push([
                'Nombre del Proyecto' => '',
                'Beneficiarios' => "Estado: {$est} = {$cant}",
                'CI' => '',
                'Cod. Prestamo' => '',
                'Estado' => '',
                'Dias Mora' => '',
                'Cuotas Vencidas' => '',
                'F. Ult. Pago' => '',
                'F. Activacion' => '',
            ]);
        }

        $reporte->push([
            'Nombre del Proyecto' => '',
            'Beneficiarios' => "Total: {$beneficiarios->count()}",
            'CI' => '',
            'Cod. Prestamo' => '',
            'Estado' => '',
            'Dias Mora' => '',
            'Cuotas Vencidas' => '',
            'F. Ult. Pago' => '',
            'F. Activacion' => '',
        ]);

        // 4) Exportar a Excel (usando Maatwebsite/Laravel-Excel)
        return (new \Rap2hpoutre\FastExcel\FastExcel($reporte))
            ->download("reporte_{$proyecto}.xlsx");
    }

    public function xlsxSeguros($periodo)
    {
        //return $periodo;
        // Ejecutar la consulta SQL requerida
        $rows = DB::select("
            WITH totales AS (
                SELECT
                    idepro,
                    MIN(fecha_ppg) AS fecha_pago,
                    MAX(prppgsegu) AS seguro_inicial,
                    SUM(prppgcapi) AS total_capital
                FROM \"plans\"
                GROUP BY idepro
            )
            SELECT
                B.nombre,
                B.ci,
                B.idepro AS cod_prestamo,
                B.fecha_activacion,
                T.total_capital,
                P.fecha_ppg AS vencimiento,
                P.prppgnpag AS nro_cuota,
                P.prppgcapi AS capital,
                P.prppginte AS interes,
                P.prppgsegu AS seguro,
                ROUND((ROUND(CAST(T.seguro_inicial AS numeric),4) / ROUND(CAST(T.total_capital AS numeric),4)) * 100, 3) AS tasa_seguro,
                P.estado AS estado_cuota
            FROM
                beneficiaries B
                INNER JOIN \"plans\" P ON B.idepro = P.idepro
                LEFT JOIN totales T ON B.idepro = T.idepro
            WHERE
                (P.fecha_ppg >= '{$periodo}-01' AND P.fecha_ppg < '{$periodo}-01'::DATE + INTERVAL '1 month')
                AND B.estado NOT IN ('BLOQUEADO', 'CANCELADO')
        ");

        $collection = collect($rows)->map(function ($item) {
            return (array) $item;
        });

        return (new \Rap2hpoutre\FastExcel\FastExcel($collection))
            ->download("seguros_{$periodo}_" . uniqid() . ".xlsx");
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
            \App\Models\Spend::where('idepro', $request->input('idepro'))->where('estado', 'ACTIVO')->sum('monto') ?? 0,
            $validatedData['meses'],
            $validatedData['taza_interes'],
            $request->input('seguro'),
            $request->input('correlativo') ?? 'off',
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

        $userId = Auth::user()->id ?? 1;

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
            \App\Models\Spend::where('idepro', $request->input('idepro'))->where('estado', 'ACTIVO')->sum('monto') ?? 0,
            $validatedData['meses'],
            $validatedData['taza_interes'],
            $request->input('seguro'),
            $request->input('correlativo') ?? 'off',
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
        $decodedData = json_decode($data, true);
        $identificationNumbers = array_values($decodedData);
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para realizar esta acción.');
        }

        // Dispatch the job to the queue
        GenerateBeneficiaryCsvZip::dispatch($identificationNumbers, $user->id);

        // Redirect back immediately with a success message
        return back()
            ->with('success', 'La exportación ha comenzado. Recibirás una notificación cuando el archivo esté listo para descargar.');
    }

    public function bulkActivation($data)
    {
        $decodedData = json_decode($data, true);
        // return $decodedData;
        $interestRate = -1;
        $secureRate = -1;
        $identificationNumbers = array_diff(array_values($decodedData), [$interestRate]);

        // return $interestRate . ' ' . $secureRate . ' ' . json_encode($identificationNumbers);

        $beneficiaries = Beneficiary::find($identificationNumbers)
            ->where('estado', '<>', 'CANCELADO');
        // ->where('estado', '<>', 'BLOQUEADO');

        try {
            DB::transaction(function () use ($beneficiaries, $interestRate, $secureRate) {
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

    public function activateBeneficiary(Beneficiary $beneficiary, $interestRate, $secureRate)
    {
        $initialCapital = $beneficiary->saldo_credito ?? 0;
        if ($initialCapital <= 0) {
            $initialCapital = $beneficiary->total_activado - ($beneficiary->payments()->where('prtdtdesc', 'like', '%CAPI%')->sum('montopago') ?? 0);
        }

        $finPlazo = date(
            'Y-m-d',
            // strtotime($beneficiary->fecha_activacion . ' + 20 years'));
            strtotime($beneficiary->fecha_activacion . ' + ' . $beneficiary->plazo_credito . ' months')
        );

        // // DETERMINAR LA CANTIDAD DE MESES/CUOTAS QUE SE VAN A GENERAR

        $date1 = date('Y-m-d', strtotime($beneficiary->fecha_extendida ?? $beneficiary->fecha_activacion));
        // $date1 = now();
        // $date1 = '2025-08-15';
        $date2 = $finPlazo;
        $d1 = new \DateTime($date2);
        $d2 = new \DateTime($date1);
        $MonthsX = $d2->diff($d1);
        $months = (($MonthsX->y) * 12) + ($MonthsX->m) + (($MonthsX->invert) ? -1 : 0) + (($MonthsX->d > 15) ? 1 : 0);

        $sequential = $beneficiary->plans()->exists() ? 'on' : null;

        // // DETERMINAR LA FECHA DE LA PRIMERA CUOTA

        // $startDate = '2025-08-15'; //PARA CONSIDERAR EL MES DESPUES DEL ESPECIFICADO
        // $startDate = now(); // PARA CONSIDERAR EL MES SIGUIENTE A AHORA
        $startDate = date('Y-m-d', strtotime($beneficiary->fecha_extendida ?? $beneficiary->fecha_activacion));

        if ($interestRate < 0 || $interestRate == -1 || $interestRate == '-1') {
            $interestRate = ($beneficiary->tasa_interes > 0) ? $beneficiary->tasa_interes : 0;
        }

        if ($secureRate < 0 || $secureRate == -1 || $secureRate == '-1') {
            $secureRate = ($beneficiary->insurance && $beneficiary->insurance->exists()) ? $beneficiary->insurance->tasa_seguro : 0.04;
        }

        $planData = $this->generarPlan(
            (float) $initialCapital,
            \App\Models\Spend::where('idepro', $beneficiary->idepro)->where('estado', 'ACTIVO')->sum('monto') ?? 0,
            // $beneficiary->plazo_credito,
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
            // 'helpers',
            'plans',
            'readjustments',
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
                'user_id' => Auth::user()->id ?? 1,
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
        $secureRate = 0.04;
        $sequential = $beneficiary->plans()->exists() ? 'on' : null;

        $startDate = now();

        $planData = $this->generarPlan(
            $initialCapital,
            \App\Models\Spend::where('idepro', $beneficiary->idepro)->where('estado', 'ACTIVO')->sum('monto') ?? 0,
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
                'user_id' => Auth::user()->id ?? 1,
            ];
        })->toArray();

        Readjustment::insert($newReadjustments);
    }
}
