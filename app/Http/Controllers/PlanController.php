<?php

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use App\Models\Plan;
use App\Models\Readjustment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'capital_inicial' => 'required|numeric',
            //'n_cuotas' => 'required|numeric',
            'taza_interes' => 'required|numeric',
            'meses' => 'required|numeric',
            'fecha_inicio' => 'required|date',
        ]);

        //return $request;

        $c = (float)$request->input('capital_inicial');
        $n = $request->input('meses');
        $i = ($request->input('taza_interes') / 100) / 12; // <== 0.25% MENSUAL
        $s = 0.00040; // <== 0.040% MENSUAL

        $a = round($c * (($i) / (1 - pow((1 + $i), ($n) * -1))), 2);

        $saldo_inicial = round($c, 2);

        $nuevo_plan = [];

        $abono_capital = 0;
        $saldo_final = 0;

        $ix = 1;
        $ms = 1;

        if ($request->input('correlativo')) {
            $ix = ($request->input('plazo_credito') - $n) + 1;
            $n = $request->input('plazo_credito');
        }

        for ($ix; $ix <= $n; $ix++) {

            $interes = round($saldo_inicial * $i, 2);

            $abono_capital = round($a - $interes, 2);

            $saldo_final = round($saldo_inicial - $abono_capital, 2);

            $seguro = round(($saldo_final + $interes) * $s, 2);

            if ($saldo_inicial < $a)
            {
                $interes -= $saldo_final;
                $abono_capital += $saldo_final;
                $seguro = 0;
                $saldo_final = 0;

                $nuevo_plan[] = [
                    $ix,
                    $saldo_inicial,
                    $a,
                    $abono_capital,
                    $interes,
                    $seguro,
                    round($a + $seguro, 2),
                    $saldo_final,
                    date('Y-m-16', strtotime($request->input('fecha_inicio') . '+' . $ms . ' month'))
                ];

                break; // Salida del bucle para evitar saldos negativos
            }

            $nuevo_plan[] = [
                $ix,
                $saldo_inicial,
                $a,
                $abono_capital,
                $interes,
                $seguro,
                round($a + $seguro, 2),
                $saldo_final,
                date('Y-m-15', strtotime($request->input('fecha_inicio') . '+' . $ix . ' month'))
            ];

            $saldo_inicial = $saldo_final;
            $ms++;

        }

        $data = new Collection();

        foreach ($nuevo_plan as $n) {
            $data->push((object)[
                'no' => $n[0],
                'saldo_inicial' => $n[1],
                'amortizacion' => ($n[2]),
                'abono_capital' => ($n[3]),
                'interes' => ($n[4]),
                'seguro' => ($n[5]),
                'total_cuota' => ($n[6]),
                'saldo_final' => ($n[7]),
                'vencimiento' => ($n[8])
            ]);
        }

        /* foreach ($data as $d) {
            Readjustment::create([
                'idepro' => $request->input('idepro'),
                'fecha_ppg' => $d->vencimiento,
                'prppgnpag' => $d->no,
                'prppgcapi' => $d->amortizacion,
                'prppginte' => $d->interes,
                'prppggral' => 0,
                'prppgsegu' => $d->seguro,
                'prppgotro' => 0,
                'prppgcarg' => 0,
                'prppgtota' => $d->total_cuota,
                'prppgahor' => 0,
                'prppgmpag' => 'POR_REAJUSTE_AUTOMATICO',
            ]);
        } */

        return view('plans.show', compact('data'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Plan $plan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        return $request;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Plan $plan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Plan $plan)
    {
        //
    }
}
