<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public function generarPlan($capital_inicial, $gastos_judiciales = 0, $meses, $taza_interes, $seguro = 0.040, $correlativo, $plazo_credito, $fecha_inicio)
    {
        $c = (float)$capital_inicial;
        $n = $meses;
        $i = ($taza_interes / 100) / 12;
        $s = $seguro / 100;

        $gj = 0;

        if ($gastos_judiciales > 0) {
            $gj = bcdiv($gastos_judiciales, '12', 8);
        }

        $a = round($c / $meses, 2);

        if ($taza_interes > 0) {
            $a = round($c * (($i) / (1 - pow((1 + $i), ($n) * -1))), 2);
        }

        $saldo_inicial = round($c, 2);

        $nuevo_plan = [];

        $abono_capital = 0;
        $saldo_final = 0;

        $ix = 1;
        $ms = 1;

        if ($correlativo === 'on') {
            $ix = ($plazo_credito - $n) + 1;
            $n = $plazo_credito;
        }
        $gjx = 0;

        if ($fecha_inicio->day < 15) {
            $ms = 0;
        }
        if ($fecha_inicio->day >= 15) {
            $fecha_inicio = date('Y-m-20', strtotime($fecha_inicio));
        }

        for ($ix; $ix <= $n; $ix++) {
            $gjx += $gj;
            $interes = round($saldo_inicial * $i, 2);
            $abono_capital = round($a - $interes, 2);
            $saldo_final = round($saldo_inicial - $abono_capital, 2);
            $seguro = round(($saldo_inicial + $interes) * $s, 2);

            $fecha_vencimiento = $this->obtenerFechaVencimiento($fecha_inicio, $ms);

            if ($ix >= $n or $saldo_inicial <= $a) {
                if ($taza_interes > 0) {
                    $interes -= $saldo_final;
                    $abono_capital += $saldo_final;
                } else {
                    $interes = 0;
                    $a += $saldo_final;
                }

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
                    $gj,
                    round($a + $seguro + $gj, 2),
                    $saldo_final,
                    $fecha_vencimiento
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
                round($a + $seguro + $gj, 2),
                $saldo_final,
                $fecha_vencimiento
            ];

            $saldo_inicial = $saldo_final;

            $ms++;

            if ($gjx >= ($gj * 12)) {
                $gj = 0;
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
                'vencimiento' => ($n[9])
            ]);
        }

        return $data;
    }

    public function generarDiferimento($diffCuotas, $diffCapital, $diffInteres, $indiceInicial, $fechaInicial)
    {
        $cap = round((float)$diffCapital / $diffCuotas, 2);
        $int = round((float)$diffInteres / $diffCuotas, 2);

        $data = new \Illuminate\Database\Eloquent\Collection();

        for ($i = 1; $i <= $diffCuotas; $i++) {
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
        $fecha = date('Y-m-15', strtotime($fecha_inicio . '+' . $ms . ' month'));
        $diaSemana = date('w', strtotime($fecha));

        if ($diaSemana == 6) {
            $fecha = date('Y-m-d', strtotime($fecha . '+2 days'));
        } elseif ($diaSemana == 0) {
            $fecha = date('Y-m-d', strtotime($fecha . '+1 day'));
        }

        return $fecha;
    }
}
