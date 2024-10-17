<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait LogicTrait
{
    public function generarPlan($capital_inicial, $meses, $taza_interes, $correlativo, $plazo_credito, $fecha_inicio)
    {
        $c = (float)$capital_inicial;
        $n = $meses;
        $i = ($taza_interes / 100) / 12;
        $s = 0.00040;

        $a = round($c * (($i) / (1 - pow((1 + $i), ($n) * -1))), 2);

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

        for ($ix; $ix <= $n; $ix++) {
            $interes = round($saldo_inicial * $i, 2);
            $abono_capital = round($a - $interes, 2);
            $saldo_final = round($saldo_inicial - $abono_capital, 2);
            $seguro = round(($saldo_final + $interes) * $s, 2);

            if ($saldo_inicial < $a) {
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
                    date('Y/m/16', strtotime($fecha_inicio . '+' . $ms . ' month'))
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
                round($a + $seguro, 2),
                $saldo_final,
                date('Y/m/15', strtotime($fecha_inicio . '+' . $ms . ' month'))
            ];

            $saldo_inicial = $saldo_final;
            $ms++;
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
                'total_cuota' => ($n[6]),
                'saldo_final' => ($n[7]),
                'vencimiento' => ($n[8])
            ]);
        }

        return $data;
    }
}
