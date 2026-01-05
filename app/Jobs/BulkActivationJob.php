<?php

namespace App\Jobs;

use App\Models\Beneficiary;
use App\Models\Plan;
use App\Models\User;
use App\Traits\FinanceTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BulkActivationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use FinanceTrait;

    protected $identificationNumbers;
    protected $interestRate;
    protected $secureRate;
    protected $userId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $identificationNumbers, $interestRate, $secureRate, int $userId)
    {
        $this->identificationNumbers = $identificationNumbers;
        $this->interestRate = $interestRate;
        $this->secureRate = $secureRate;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $beneficiaries = Beneficiary::whereIn('id', $this->identificationNumbers)
            ->where('estado', '!=', 'CANCELADO')
            ->where('estado', '!=', 'BLOQUEADO')
            ->get();

        foreach ($beneficiaries as $beneficiary) {
            try {
                // Use a separate transaction for each beneficiary so one failure doesn't rollback the others
                DB::transaction(function () use ($beneficiary) {
                    $this->activateBeneficiary($beneficiary, $this->interestRate, $this->secureRate);
                });
            } catch (\Exception $e) {
                Log::error("Error activating beneficiary {$beneficiary->id}: " . $e->getMessage());
                // Continue to next beneficiary
            }
        }
    }

    public function activateBeneficiary(Beneficiary $beneficiary, $interestRate, $secureRate)
    {
        $initialCapital = $beneficiary->saldo_credito ?? 0;
        if ($initialCapital <= 0) {
            $initialCapital = $beneficiary->total_activado - ($beneficiary->payments()->where('prtdtdesc', 'like', '%CAPI%')->sum('montopago') ?? 0);
        }

        // Use the beneficiary's credit term (plazo_credito) in months to calculate end date
        // Logic from original controller: strtotime($beneficiary->fecha_activacion . ' + ' . $beneficiary->plazo_credito . ' months')
        // But simply using the months is enough for generarPlan.

        $months = $beneficiary->plazo_credito; // Simplify based on logic found in controller which recalculated months from dates but ended up being close to plazo_credito.
        // Wait, original controller logic:
        // $finPlazo = date('Y-m-d', strtotime($beneficiary->fecha_activacion . ' + ' . $beneficiary->plazo_credito . ' months'));
        // $date1 = date('Y-m-d', strtotime($beneficiary->fecha_extendida ?? $beneficiary->fecha_activacion));
        // $date2 = $finPlazo;
        // ... diff ...
        // $months = difference in months.

        // Let's replicate the original logic exactly to be safe.
        $finPlazo = date(
            'Y-m-d',
            strtotime($beneficiary->fecha_activacion . ' + ' . $beneficiary->plazo_credito . ' months')
        );

        $date1 = date('Y-m-d', strtotime($beneficiary->fecha_extendida ?? $beneficiary->fecha_activacion));
        $date2 = $finPlazo;
        $d1 = new \DateTime($date2);
        $d2 = new \DateTime($date1);
        $MonthsX = $d2->diff($d1);
        $months = (($MonthsX->y) * 12) + ($MonthsX->m) + (($MonthsX->invert) ? -1 : 0) + (($MonthsX->d > 15) ? 1 : 0);

        $sequential = 'off';

        $startDate = date('Y-m-d', strtotime($beneficiary->fecha_extendida ?? $beneficiary->fecha_activacion));

        if ($interestRate < 0 || $interestRate == -1 || $interestRate == '-1') {
            $interestRate = ($beneficiary->tasa_interes > 0) ? $beneficiary->tasa_interes : 0;
        }

        if ($secureRate < 0 || $secureRate == -1 || $secureRate == '-1') {
            $secureRate = ($beneficiary->insurance && $beneficiary->insurance->exists()) ? $beneficiary->insurance->tasa_seguro : 0.04;
        }

        // generarPlan is now in FinanceTrait
        $planData = $this->generarPlan(
            (float) $initialCapital,
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
        $this->createNewPlans($beneficiary, $planData);
    }

    public function deactivateRelatedRecords(Beneficiary $beneficiary)
    {
        $relatedModels = [
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
                'user_id' => $this->userId,
            ];
        })->toArray();

        Plan::insert($newPlans);
    }
}
