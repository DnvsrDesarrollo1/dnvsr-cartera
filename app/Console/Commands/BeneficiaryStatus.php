<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BeneficiaryStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:beneficiary-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $beneficiaries = \App\Models\Beneficiary::where('estado', '!=', 'CANCELADO')
            ->where('estado', '!=', 'BLOQUEADO')
            ->get();

        $count = 0;

        foreach ($beneficiaries as $b) {
            $venc = $b
                ->getCurrentPlan('VENCIDO', '=')
                ->first() ?? null;

            $total = 0;

            if ($venc && $venc != null) {
                $fechaInicio = \Carbon\Carbon::parse($venc->fecha_ppg);
                $fechaFin = now();
                $diff = $fechaInicio->diffInDays($fechaFin);
                $total = $diff;
            }

            if ($total > 60) {
                $b->update([
                    'estado' => 'EJECUCION'
                ]);
            }
            if ($total > 0 && $total <= 60) {
                $b->update([
                    'estado' => 'VENCIDO'
                ]);
            }
            if ($total <= 0) {
                $b->update([
                    'estado' => 'VIGENTE'
                ]);
            }

            $count++;
        }

        Log::info('Beneficiaries status updated [' . $count . ']');
    }
}
