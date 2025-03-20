<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

abstract class Controller
{
    function calcularPago($valorPresente, $tasaInteres, $numPeriodos)
    {
        $numerador = pow(1 + $tasaInteres, $numPeriodos) * $tasaInteres;
        $denominador = pow(1 + $tasaInteres, $numPeriodos) - 1;
        $pago = $valorPresente * ($numerador / $denominador);
        return $pago;
    }

    public function generarPlan($capital_inicial, $gastos_judiciales = 0, $meses, $taza_interes, $seguro = 0.040, $correlativo, $plazo_credito, $fecha_inicio, $i_diff = 0, $s_diff = 0)
    {
        $c = (float)$capital_inicial;
        $n = $meses;
        $i = ($taza_interes / 100) / 12;
        $s = $seguro / 100;

        $id = bcdiv($i_diff, $n, 6);
        $sd = bcdiv($s_diff, '12', 6);

        $gj = 0;

        if ($gastos_judiciales > 0) {
            $gj = ($gastos_judiciales / ($meses));
        }

        $a = $c / $meses;

        if ($taza_interes > 0) {
            //$a = $c * (bcdiv(($i), (1 - pow((1 + $i), ($n) * -1)), 6));
            $a = $this->calcularPago($c, $i, $n);
        }

        $saldo_inicial = $c;

        $nuevo_plan = [];

        $abono_capital = 0;
        $saldo_final = 0;

        $ix = 1;
        $ms = 1;

        if ($correlativo === 'on') {
            $ix = ($plazo_credito - $n) + 1;
            $n = $plazo_credito;
            if ($n == $plazo_credito) {
                $ix = 1;
            }
        }

        $gjx = 0;

        $fecha_inicio = \Carbon\Carbon::parse($fecha_inicio);

        /* if ($fecha_inicio->day < 15) {
            $ms = 0;
        } */

        if ($fecha_inicio->day >= 15) {
            $fecha_inicio = $fecha_inicio->format('Y-m-15');
        }

        $totalGenerado = 0;

        for ($ix; $ix <= $n; $ix++) {
            $gjx += $gj;
            $interes = $saldo_inicial * $i;
            $abono_capital = $a - $interes;
            $saldo_final = $saldo_inicial - $abono_capital;
            $seguro = ($saldo_inicial + $interes) * $s;

            $totalGenerado += $abono_capital;

            $fecha_vencimiento = $this->obtenerFechaVencimiento($fecha_inicio, $ms);

            if ($ix == 1) {
                $fecha_vencimiento = date('Y-m-d', strtotime($fecha_inicio . '+ ' . $ms . ' month'));
            }
            /* if ($ix == 1) {
                $fecha = date('Y-m-24', strtotime($fecha_inicio));
                $diaSemana = date('w', strtotime($fecha));

                if ($diaSemana == 6) {
                    $fecha = date('Y-m-d', strtotime($fecha . '+2 days'));
                } elseif ($diaSemana == 0) {
                    $fecha = date('Y-m-d', strtotime($fecha . '+1 day'));
                }
                $fecha_vencimiento = $fecha;
            } else {
                $fecha_vencimiento = $this->obtenerFechaVencimiento($fecha_inicio, $ms);
            } */

            if ($ix >= $meses) {

                $totalGenerado = round($capital_inicial, 2) - round($totalGenerado, 2);

                if ($taza_interes > 0) {
                    //$interes = max(0, $interes - $saldo_final);
                    $abono_capital += $saldo_final;
                } else {
                    $interes = 0;
                    $a += $saldo_final;
                }

                //$seguro = 0;
                //$saldo_final = 0;

                $nuevo_plan[] = [
                    $ix,
                    $saldo_inicial,
                    $a - $totalGenerado,
                    $abono_capital,
                    $interes,
                    $seguro,
                    $gj,
                    $a + $seguro + $gj + $id + $sd,
                    $saldo_final,
                    $fecha_vencimiento,
                    $id,
                    $sd
                ];

                break;
            }

            $nuevo_plan[] = [
                $ix,
                $saldo_inicial,
                $a,
                $abono_capital,
                $interes,
                $seguro,
                $gj,
                $a + $seguro + $gj + $id + $sd,
                $saldo_final,
                $fecha_vencimiento,
                $id,
                $sd
            ];

            $saldo_inicial = $saldo_final;

            $ms++;

            if ($gjx >= ($gastos_judiciales)) {
                $gj = 0;
            }

            if ($ix == 12) {
                $sd = 0;
            }
        }

        $data = new \Illuminate\Database\Eloquent\Collection();

        foreach ($nuevo_plan as $n) {
            $data->push((object)[
                'nro_cuota' => $n[0],
                'saldo_inicial' => $n[1],
                'amortizacion' => ($n[2]),
                'abono_capital' => ($n[3]),
                'interes' => ($n[4]),
                'seguro' => ($n[5]),
                'gastos_judiciales' => ($n[6]),
                'total_cuota' => ($n[7]),
                'saldo_final' => ($n[8]),
                'vencimiento' => ($n[9]),
                'interes_devengado' => ($n[10]),
                'seguro_devengado' => ($n[11]),
            ]);
        }

        return $data;
    }

    public function generarDiferimento($cuotasDiferibles, $diffCapital, $diffInteres, $indiceInicial, $fechaInicial)
    {
        $cap = bcdiv($diffCapital, $cuotasDiferibles, 8);
        $int = bcdiv($diffInteres, $cuotasDiferibles, 8);

        $data = new \Illuminate\Database\Eloquent\Collection();

        for ($i = 1; $i <= $cuotasDiferibles; $i++) {
            $data->push((object)[
                'nro_cuota' => $i + $indiceInicial,
                'capital' => $cap,
                'interes' => $int,
                'vencimiento' => date('Y/m/15', strtotime($fechaInicial . '+ ' . $i . 'month')),
                'estado' => 'ACTIVO'
            ]);
        }
        return $data;
    }

    function obtenerFechaVencimiento($fecha_inicio, $ms)
    {
        $fecha = date('Y-m-16', strtotime($fecha_inicio . '+' . $ms . ' month'));
        $diaSemana = date('w', strtotime($fecha));

        if ($diaSemana == 6) {
            $fecha = date('Y-m-d', strtotime($fecha . '+2 days'));
        } elseif ($diaSemana == 0) {
            $fecha = date('Y-m-d', strtotime($fecha . '+1 day'));
        }

        return $fecha;
    }

    public function generatePlanData(Request $request)
    {
        return $this->generarPlan(
            $request->input('capital_inicial'),
            \App\Models\Spend::where('idepro', $request->input('idepro'))->where('estado', 'ACTIVO')->first()->monto ?? 0,
            $request->input('meses'),
            $request->input('taza_interes'),
            $request->input('seguro'),
            $request->input('correlativo'),
            $request->input('plazo_credito'),
            $request->input('fecha_inicio')
        );
    }

    public function generateDiferimentoIfNeeded(Request $request, $data)
    {
        if ($request->filled(['diff_cuotas', 'diff_capital', 'diff_interes'])) {
            return $this->generarDiferimento(
                $request->input('diff_cuotas'),
                $request->input('diff_capital'),
                $request->input('diff_interes'),
                $request->input('plazo_credito'),
                $data->last()->vencimiento
            );
        }
        return collect();
    }

    public function deactivateExistingRecords($idepro, $userId)
    {
        $models = [\App\Models\Helper::class, \App\Models\Plan::class, \App\Models\Readjustment::class];

        foreach ($models as $model) {
            $model::where('idepro', $idepro)->update([
                'estado' => 'INACTIVO',
                'user_id' => $userId
            ]);
        }
    }

    public function createNewRecords($data, $diferimento, Request $request, $userId)
    {
        $dynaModel = $request->input('correlativo') ? 'App\\Models\\Readjustment' : 'App\\Models\\Plan';
        $idepro = $request->input('idepro');

        if ($data) {
            foreach ($data as $d) {
                $dynaModel::create([
                    'idepro' => $idepro,
                    'fecha_ppg' => $d->vencimiento,
                    'prppgnpag' => $d->nro_cuota,
                    'prppgcapi' => $d->abono_capital,
                    'prppginte' => $d->interes,
                    'prppgsegu' => $d->seguro,
                    'prppgotro' => $d->gastos_judiciales,
                    'prppgtota' => $d->total_cuota,
                    'estado' => 'ACTIVO',
                    'user_id' => $userId,
                ]);
            }
        }
        if ($diferimento) {
            foreach ($diferimento as $d) {
                \App\Models\Helper::create([
                    'idepro' => $idepro,
                    'indice' => $d->nro_cuota,
                    'capital' => $d->capital,
                    'interes' => $d->interes,
                    'vencimiento' => $d->vencimiento,
                    'estado' => $d->estado,
                    'user_id' => $userId,
                ]);
            }
        }
    }
}
