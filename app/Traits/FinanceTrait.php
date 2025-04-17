<?php

namespace App\Traits;

use App\Models\Beneficiary;
use Illuminate\Database\Eloquent\Collection;

trait FinanceTrait
{
    private function calcularPagoMensual(float $valorPresente, float $tasaInteres, int $numPeriodos): float
    {
        if ($tasaInteres == 0) {
            return round($valorPresente / $numPeriodos, 2);
        }

        $tasaInteres = ($tasaInteres / 100) / 12;

        $numerador = pow(1 + $tasaInteres, $numPeriodos) * $tasaInteres;
        $denominador = pow(1 + $tasaInteres, $numPeriodos) - 1;
        $pago = $valorPresente * ($numerador / $denominador);
        return $pago;
    }

    private function calcularTasaSeguro(Collection $plan, float $saldoInicial): float
    {
        $seguro = 0;
        if (count($plan) > 0) {
            $primerCuota = $plan->first();

            $seguro = round(($primerCuota->prppgsegu / $saldoInicial), 5);
        }

        return $seguro;
    }

    private function calcularPagoInteres(float $saldoInicial, float $tasaInteres): float
    {
        return round($saldoInicial * (($tasaInteres / 100) / 12), 2);
    }

    private function calcularPagoSeguro(float $saldoInicial, float $tasaSeguro): float
    {
        return round($saldoInicial * $tasaSeguro, 4);
    }


    public function actualizarPlanActual(string $idepro, float $saldoCapital, Collection $planVigente): Collection
    {
        $cuotasPendientes = $planVigente->count();

        if ($cuotasPendientes == 0) {
            return new Collection();
        }

        $tasaInteres = Beneficiary::where('idepro', $idepro)
            ->first()
            ->tasa_interes ?? 0;

        $saldoInicial = $saldoCapital;

        $tasaSeguro = $this->calcularTasaSeguro($planVigente, $saldoCapital);

        $amortizacion = $this->calcularPagoMensual($saldoCapital, $tasaInteres, $cuotasPendientes);

        $abonoInteres = $this->calcularPagoInteres($saldoCapital, $tasaInteres);

        $abonoSeguro = $this->calcularPagoSeguro($saldoCapital, $tasaSeguro);

        $abonoCapital = $amortizacion - $abonoInteres;

        $planActualizado = new Collection();

        $totalCapitalizado = 0;

        foreach ($planVigente as $key => $value) {

            $abonoCapital = round($abonoCapital, 2);

            $saldoInicial -= ($abonoCapital);

            $totalCapitalizado += $abonoCapital;

            if ($key == $cuotasPendientes - 1) {
                $abonoCapital += ($saldoInicial);
                $saldoInicial = 0;
            }

            $value->update([
                'prppgcapi' => round($abonoCapital, 2),
                'prppginte' => round($abonoInteres, 2),
                'prppgsegu' => round($abonoSeguro, 2),
                'prppgtota' => round($abonoCapital + $abonoInteres + $abonoSeguro + $value->prppgcarg + $value->prppggral + $value->prppgotro, 2),
                'prppgahor' => $saldoInicial,
                'estado' => 'ACTIVO',
                'user_id' => \Illuminate\Support\Facades\Auth::user()->id,
            ]);

            $abonoInteres = $this->calcularPagoInteres($saldoInicial, $tasaInteres);

            $abonoCapital = $amortizacion - $abonoInteres;

            $abonoSeguro = $this->calcularPagoSeguro($saldoInicial + $abonoInteres, $tasaSeguro);

            $planActualizado->push($value);
        }

        return ($planActualizado);
    }
}
