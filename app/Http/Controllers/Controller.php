<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

abstract class Controller
{
    private function dividirConRedondeo($total, $partes)
    {
        if ($partes <= 0) {
            return [];
        } // Validación básica

        // Convertir el total a centavos (trabajar en enteros)
        $totalCentavos = round($total * 100);

        // Calcular la base entera y el residuo
        $baseEntera = floor($totalCentavos / $partes);
        $residuo = $totalCentavos % $partes; // Centavos sobrantes

        $resultados = [];
        for ($i = 0; $i < $partes; $i++) {
            $centavos = $baseEntera;
            // Distribuir el residuo: un centavo extra en las primeras $residuo partes
            if ($i < $residuo) {
                $centavos += 1;
            }
            $resultados[] = $centavos / 100; // Convertir a dólares
        }

        return $resultados;
    }

    private function calcularPago($valorPresente, $tasaInteres, $numPeriodos)
    {
        $numerador = pow(1 + $tasaInteres, $numPeriodos) * $tasaInteres;
        $denominador = pow(1 + $tasaInteres, $numPeriodos) - 1;
        $pago = $valorPresente * ($numerador / $denominador);

        return $pago;
    }

    public function generarPlan(
        float $capital_inicial,
        float $gastos_judiciales,
        int $meses,
        float $taza_interes,
        float $seguro,
        $correlativo,
        int $plazo_credito,
        string $fecha_inicio,
        float $i_diff = 0,
        float $s_diff = 0
    ): \Illuminate\Database\Eloquent\Collection {
        // Normalizar tasa de interés y seguro a mensual
        $tasaMensual = $taza_interes / 100 / 12;
        $seguroMensual = $seguro / 100;

        // Distribuir diferimientos
        $interesDiffPorCuota = $meses > 0 ? bcdiv($i_diff, $meses, 6) : 0;
        $seguroDiffPorCuota = bcdiv($s_diff, '12', 4);

        // Calcular cuota de gastos judiciales
        $gj = 0;
        if ($gastos_judiciales > 0) {
            if ($gastos_judiciales <= 100) {
                $gj = $gastos_judiciales / 5;
            } elseif ($gastos_judiciales <= 300) {
                $gj = $gastos_judiciales / 12;
            } else {
                $gj = $gastos_judiciales / $meses;
            }
        }

        // Calcular cuota fija (amortización)
        $cuota = $taza_interes > 0
            ? $this->calcularPago($capital_inicial, $tasaMensual, $meses)
            : $capital_inicial / $meses;

        // Ajustar índice inicial y cantidad de cuotas si es correlativo
        $indiceInicial = 1;
        $totalCuotas = $meses;
        if ($correlativo === 'on') {
            $indiceInicial = ($plazo_credito - $meses) + 1;
            $totalCuotas = $plazo_credito;
            if ($indiceInicial < 1) {
                $indiceInicial = 1;
            }
        }

        // Fecha de inicio siempre día 15
        $fechaBase = \Carbon\Carbon::parse($fecha_inicio)->copy()->day(15);

        $plan = [];
        $saldo = $capital_inicial;
        $acumGj = 0;
        $acumCap = 0;

        for ($i = $indiceInicial; $i <= $totalCuotas; $i++) {
            $acumGj += $gj;

            $interes = $saldo * $tasaMensual;
            $abono = $cuota - $interes;
            $saldoFin = $saldo - $abono;
            $seguroCuota = ($saldo + $interes) * $seguroMensual;

            $acumCap += $abono;

            // Última cuota: ajustar errores de redondeo
            if ($i === $totalCuotas || $i === $indiceInicial + $meses - 1) {
                if ($taza_interes > 0) {
                    $abono += $saldoFin;
                } else {
                    $interes = 0;
                    $cuota += $saldoFin;
                }
                // Ajustar cuota si el capital acumulado no cuadra
                if (! bccomp($acumCap, $capital_inicial, 2)) {
                    $delta = $capital_inicial - $acumCap;
                    $cuota += $delta;
                    $abono += $delta;
                }
                $saldoFin = 0;
            }

            $fechaVen = $i === $indiceInicial
                ? $fechaBase->copy()->addMonth()->format('Y-m-d')
                : $this->obtenerFechaVencimiento($fechaBase, $i - $indiceInicial + 1);

            $plan[] = [
                'nro_cuota' => $i,
                'saldo_inicial' => round($saldo, 4),
                'amortizacion' => round($cuota, 4),
                'abono_capital' => round($abono, 4),
                'interes' => round($interes, 4),
                'seguro' => round($seguroCuota, 4),
                'gastos_judiciales' => $acumGj >= $gastos_judiciales ? 0 : round($gj, 4),
                'total_cuota' => round($cuota + $seguroCuota + ($acumGj >= $gastos_judiciales ? 0 : $gj) + $interesDiffPorCuota + ($i > 12 ? 0 : $seguroDiffPorCuota), 4),
                'saldo_final' => round($saldoFin, 4),
                'vencimiento' => $fechaVen,
                'interes_devengado' => round($interesDiffPorCuota, 4),
                'seguro_devengado' => round($i > 12 ? 0 : $seguroDiffPorCuota, 4),
            ];

            $saldo = $saldoFin;

            // Gastos judiciales ya cubiertos
            if ($acumGj >= $gastos_judiciales) {
                $gj = 0;
            }

            // Seguro diferido solo 12 meses
            if ($i === 12) {
                $seguroDiffPorCuota = 0;
            }

            // Saldo cero => terminamos
            if ($saldo <= 0.01) {
                break;
            }

        }

        // Comparar capital planificado con capital inicial
        $capitalPlanificado = array_sum(array_column($plan, 'abono_capital'));
        $diferenciaCapital = $capital_inicial - $capitalPlanificado;

        // Ajustar última cuota con la diferencia
        if (abs($diferenciaCapital) > 0.001) {
            $ultimoIndice = count($plan) - 1;
            $plan[$ultimoIndice]['abono_capital'] += $diferenciaCapital;
            $plan[$ultimoIndice]['total_cuota'] += $diferenciaCapital;
        }

        return new \Illuminate\Database\Eloquent\Collection(
            array_map(fn ($row) => (object) $row, $plan)
        );

    }

    public function generarDiferimento($cuotasDiferibles, $diffCapital, $diffInteres, $indiceInicial, $fechaInicial)
    {
        $cap = bcdiv($diffCapital, $cuotasDiferibles, 8);
        $int = bcdiv($diffInteres, $cuotasDiferibles, 8);

        $data = new \Illuminate\Database\Eloquent\Collection;

        for ($i = 1; $i <= $cuotasDiferibles; $i++) {
            $data->push((object) [
                'nro_cuota' => $i + $indiceInicial,
                'capital' => $cap,
                'interes' => $int,
                'vencimiento' => date('Y/m/15', strtotime($fechaInicial.'+ '.$i.'month')),
                'estado' => 'ACTIVO',
            ]);
        }

        return $data;
    }

    public function obtenerFechaVencimiento($fecha_inicio, $ms)
    {
        $fecha = date('Y-m-16', strtotime($fecha_inicio.'+'.$ms.' month'));
        $diaSemana = date('w', strtotime($fecha));

        if ($diaSemana == 6) {
            $fecha = date('Y-m-d', strtotime($fecha.'+2 days'));
        } elseif ($diaSemana == 0) {
            $fecha = date('Y-m-d', strtotime($fecha.'+1 day'));
        }

        return $fecha;
    }

    public function generatePlanData(Request $request)
    {
        return $this->generarPlan(
            $request->input('capital_inicial'),
            \App\Models\Spend::where('idepro', $request->input('idepro'))->where('estado', 'ACTIVO')->sum('monto') ?? 0,
            $request->input('meses'),
            $request->input('taza_interes'),
            $request->input('seguro'),
            $request->input('correlativo'),
            $request->input('plazo_credito'),
            $request->input('fecha_inicio'),
            \App\Models\Earn::where('idepro', $request->input('idepro'))->sum('interes') ?? 0,
            \App\Models\Earn::where('idepro', $request->input('idepro'))->sum('seguro') ?? 0,
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

    public function deactivateExistingRecords($idepro)
    {
        $models = [
            //\App\Models\Helper::class,
            \App\Models\Plan::class,
            \App\Models\Readjustment::class,
        ];

        $cuotaPagada = collect();

        foreach ($models as $model) {
            $cuotaPagada = $cuotaPagada->merge(
                $model::where('idepro', $idepro)->where('estado', 'CANCELADO')->get()
            );
        }

        foreach ($models as $model) {
            $model::where('idepro', $idepro)->delete();
        }

        return $cuotaPagada;
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
                    'prppgcapi' => ($d->abono_capital),
                    'prppginte' => ($d->interes),
                    'prppggral' => ($d->interes_devengado),
                    'prppgsegu' => ($d->seguro),
                    'prppgotro' => ($d->gastos_judiciales),
                    'prppgcarg' => ($d->seguro_devengado),
                    'prppgtota' => ($d->total_cuota),
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
                    'capital' => round($d->capital, 2),
                    'interes' => round($d->interes, 2),
                    'vencimiento' => $d->vencimiento,
                    'estado' => $d->estado,
                    'user_id' => $userId,
                ]);
            }
        }
    }

    public function downloadPlanCollection($cuotas, $fileTitle)
    {
        // Generate CSV
        $fileName = $fileTitle.'_'.uniqid().'.csv';
        $filePath = storage_path('app/public/exports/'.$fileName);

        $file = fopen($filePath, 'w');
        fputcsv($file, [
            'idepro',
            'fecha_ppg',
            'prppgnpag',
            'prppgcapi',
            'prppginte',
            'prppggral',
            'prppgsegu',
            'prppgotro',
            'prppgcarg',
            'prppgtota',
            'prppgahor',
            'prppgmpag',
            'estado',
            'user_id',
        ]); // Adjust headers as needed

        foreach ($cuotas as $cuota) {
            fputcsv($file, [
                $cuota->idepro,
                $cuota->fecha_ppg,
                $cuota->prppgnpag,
                $cuota->prppgcapi,
                $cuota->prppginte,
                $cuota->prppggral,
                $cuota->prppgsegu,
                $cuota->prppgotro,
                $cuota->prppgcarg,
                $cuota->prppgtota,
                $cuota->prppgahor,
                $cuota->prppgmpag,
                $cuota->estado,
                $cuota->user_id,
            ]);
        }

        fclose($file);

        return asset('storage/exports/'.basename($filePath));
    }
}
