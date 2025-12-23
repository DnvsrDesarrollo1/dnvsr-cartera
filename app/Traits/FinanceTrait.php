<?php

namespace App\Traits;

use App\Models\Beneficiary;
use Illuminate\Database\Eloquent\Collection;

trait FinanceTrait
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

    private function calcularPagoMensual(float $valorPresente, float $tasaInteres, int $numPeriodos): float
    {
        if ($valorPresente == 0) {
            return 0;
        }

        if ($tasaInteres == 0) {
            return round($valorPresente / $numPeriodos, 4);
        }

        $tasaInteres = ($tasaInteres / 100) / 12;

        $numerador = pow(1 + $tasaInteres, $numPeriodos) * $tasaInteres;
        $denominador = pow(1 + $tasaInteres, $numPeriodos) - 1;
        $pago = $valorPresente * ($numerador / $denominador);

        return $pago;
    }

    private function calcularTasaSeguro($plan, float $saldoInicial): float
    {
        $seguro = 0;
        if (count($plan) > 0) {
            $primerCuota = $plan->first();

            $seguroPrimeraCuota = round($primerCuota->prppgsegu, 5);

            $seguro = round(($seguroPrimeraCuota / $saldoInicial), 5);
        }

        return $seguro;
    }

    private function calcularPagoInteres(float $saldoInicial, float $tasaInteres): float
    {
        if ($saldoInicial == 0) {
            return 0;
        }

        if ($tasaInteres == 0) {
            return 0;
        }

        return round($saldoInicial * (($tasaInteres / 100) / 12), 4);
    }

    private function calcularPagoSeguro(float $saldoInicial, float $tasaSeguro): float
    {
        if ($saldoInicial == 0) {
            return 0;
        }

        return round($saldoInicial * $tasaSeguro, 4);
    }

    public function actualizarPlanActual(string $idepro, float $saldoCapital, Collection $planVigente, float $totalPlan): Collection
    {
        $cuotasPendientes = $planVigente->count();

        if ($cuotasPendientes == 0) {
            return new Collection;
        }

        $beneficiary = Beneficiary::where('idepro', $idepro)->with(['plans'])->first();

        $tasaInteres = $beneficiary->tasa_interes ?? 0;

        $saldoInicial = $saldoCapital;

        $planBase = \App\Models\Plan::where('idepro', $idepro)->orderBy('fecha_ppg', 'asc')->get();

        $tasaSeguro = $this->calcularTasaSeguro($planBase, $totalPlan);

        $amortizacion = $this->calcularPagoMensual($saldoCapital, $tasaInteres, $cuotasPendientes);

        $abonoInteres = $this->calcularPagoInteres($saldoCapital, $tasaInteres);

        $abonoSeguro = $this->calcularPagoSeguro($saldoCapital, $tasaSeguro);

        $abonoCapital = $amortizacion - $abonoInteres;

        $planActualizado = new Collection;

        $totalCapitalizado = 0;

        foreach ($planVigente as $key => $value) {

            /** @var \App\Models\Plan $value */

            $abonoCapital = round($abonoCapital, 4);

            $saldoInicial -= ($abonoCapital);

            $totalCapitalizado += $abonoCapital;

            if ($key == $cuotasPendientes - 1) {
                $abonoCapital += ($saldoInicial);
                $saldoInicial = 0;
            }

            $value->update([
                'prppgcapi' => round($abonoCapital, 4),
                'prppginte' => round($abonoInteres, 4),
                'prppgsegu' => round($abonoSeguro, 4),
                'prppgtota' => round($abonoCapital + $abonoInteres + $abonoSeguro + $value->prppgcarg + $value->prppggral + $value->prppgotro, 4),
                'prppgahor' => $saldoInicial,
                //'prppgmpag' => $planBase->first()->prppgsegu.' / '.$totalPlan,
                'estado' => 'ACTIVO',
                'user_id' => \Illuminate\Support\Facades\Auth::user()->id ?? 1,
            ]);

            $abonoInteres = $this->calcularPagoInteres($saldoInicial, $tasaInteres);

            $abonoCapital = $amortizacion - $abonoInteres;

            $abonoSeguro = $this->calcularPagoSeguro($saldoInicial + $abonoInteres, $tasaSeguro);

            if ($saldoCapital <= 0) {
                $value->update([
                    'prppgcapi' => 0,
                    'prppginte' => 0,
                    'prppgsegu' => 0,
                    'prppgtota' => 0,
                    'prppgahor' => 0,
                    'estado' => 'CANCELADO',
                    'user_id' => \Illuminate\Support\Facades\Auth::user()->id ?? 1,
                ]);
            }

            $value->refresh();

            $planActualizado->push($value);
        }

        return $planActualizado;
    }

    public function createPayment($comprobante, $index, $codigoPrestamo, $codRef, $codCon, $fechaPago, $horaPago, $glosa, $montoPagado, $usuario, $numeroCuota, $departamento, $observaciones)
    {
        //PAYMENT
        \App\Models\Payment::create([
            'numtramite' => $comprobante,
            'prtdtitem' => $index,
            'numprestamo' => $codigoPrestamo,
            'prtdtpref' => $codRef,
            'prtdtccon' => $codCon,
            'fecha_pago' => $fechaPago,
            'prtdtdesc' => $glosa,
            'montopago' => $montoPagado,
            'prtdtuser' => $usuario,
            'hora_pago' => $horaPago,
            'prtdtfpro' => null,
            'prtdtnpag' => $numeroCuota,
            'depto_pago' => $departamento,
            'obs_pago' => $observaciones,
        ]);
    }

    public function createVoucher($agenciaPago, $descripcion, $fechaPago, $horaPago, $montoPagado, $numeroCuota, $numPrestamo, $numTramite, $departamento, $observaciones)
    {
        \App\Models\Voucher::create([
            'agencia_pago' => $agenciaPago,
            'descripcion' => $descripcion,
            'fecha_pago' => $fechaPago,
            'hora_pago' => $horaPago,
            'montopago' => $montoPagado,
            'numpago' => $numeroCuota,
            'numprestamo' => $numPrestamo,
            'numtramite' => $numTramite,
            'depto_pago' => $departamento,
            'obs_pago' => $observaciones,
        ]);
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
        $tasaMensual = $taza_interes / 100 / 12;
        $seguroMensual = $seguro / 100;

        $interesDiffPorCuota = $meses > 0 ? bcdiv($i_diff, $meses, 6) : 0;
        $seguroDiffPorCuota = bcdiv($s_diff, '12', 4);

        $gjDistribuido = [];
        if ($gastos_judiciales > 0) {
            $cuotasGj = $meses;
            if ($gastos_judiciales <= 100) {
                $cuotasGj = min(5, $meses);
            } elseif ($gastos_judiciales <= 300) {
                $cuotasGj = min(12, $meses);
            }
            $gjDistribuido = $this->dividirConRedondeo($gastos_judiciales, $cuotasGj);
        }

        if ($taza_interes > 0) {
            $cuota = $this->calcularPago($capital_inicial, $tasaMensual, $meses);
            $abonosDistribuidos = [];
        } else {
            $abonosDistribuidos = $this->dividirConRedondeo($capital_inicial, $meses);
            $cuota = 0;
        }

        $indiceInicial = 1;
        $totalCuotas = $meses;
        if ($correlativo === 'on') {
            $indiceInicial = ($plazo_credito - $meses) + 1;
            $totalCuotas = $plazo_credito;
            if ($indiceInicial < 1) {
                $indiceInicial = 1;
            }
        }

        $fechaBase = \Carbon\Carbon::parse($fecha_inicio)->copy()->day(15);

        $plan = [];
        $saldo = $capital_inicial;
        $acumGj = 0;
        $acumCap = 0;

        for ($i = $indiceInicial; $i <= $totalCuotas; $i++) {
            $gjIndex = $i - $indiceInicial;
            $gj = isset($gjDistribuido[$gjIndex]) ? $gjDistribuido[$gjIndex] : 0;
            $acumGj += $gj;

            $interes = $saldo * $tasaMensual;

            if ($taza_interes > 0) {
                $abono = $cuota - $interes;
            } else {
                $abono = isset($abonosDistribuidos[$gjIndex]) ? $abonosDistribuidos[$gjIndex] : 0;
                $cuota = $abono;
            }

            $saldoFin = $saldo - $abono;
            $seguroCuota = ($saldo + $interes) * $seguroMensual;

            $acumCap += $abono;

            if ($i === $totalCuotas || $i === $indiceInicial + $meses - 1) {
                if ($taza_interes > 0) {
                    $abono += $saldoFin;
                } else {
                    $interes = 0;
                    $cuota += $saldoFin;
                }
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
                'gastos_judiciales' => $acumGj > $gastos_judiciales ? 0 : round($gj, 4),
                'total_cuota' => round($cuota + $seguroCuota + ($acumGj >= $gastos_judiciales ? 0 : $gj) + $interesDiffPorCuota + ($i > 12 ? 0 : $seguroDiffPorCuota), 4),
                'saldo_final' => round($saldoFin, 4),
                'vencimiento' => $fechaVen,
                'interes_devengado' => round($interesDiffPorCuota, 4),
                'seguro_devengado' => round($i > 12 ? 0 : $seguroDiffPorCuota, 4),
            ];

            $saldo = $saldoFin;

            if ($acumGj > $gastos_judiciales) {
                $gj = 0;
            }

            if ($i === 12) {
                $seguroDiffPorCuota = 0;
            }

            if ($saldo <= 0.01) {
                break;
            }
        }

        $capitalPlanificado = array_sum(array_column($plan, 'abono_capital'));
        $diferenciaCapital = $capital_inicial - $capitalPlanificado;

        if (abs($diferenciaCapital) > 0.0001) {
            $ultimoIndice = count($plan) - 1;
            $plan[$ultimoIndice]['abono_capital'] += $diferenciaCapital;
            $plan[$ultimoIndice]['total_cuota'] += $diferenciaCapital;
        }

        return new \Illuminate\Database\Eloquent\Collection(
            array_map(fn($row) => (object) $row, $plan)
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
                'vencimiento' => date('Y/m/15', strtotime($fechaInicial . '+ ' . $i . 'month')),
                'estado' => 'ACTIVO',
            ]);
        }

        return $data;
    }

    public function obtenerFechaVencimiento($fecha_inicio, $ms)
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
}
