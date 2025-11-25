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
}
