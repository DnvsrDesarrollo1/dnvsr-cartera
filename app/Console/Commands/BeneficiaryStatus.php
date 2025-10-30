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

            $total = 0;

            if ($b->getCurrentPlan('VENCIDO', '=') && $b->getCurrentPlan('VENCIDO', '=')->count() > 0) {
                $fechaInicio = \Carbon\Carbon::parse($b->getCurrentPlan('VENCIDO', '=')->fecha_ppg);
                $fechaFin = now()->startOfDay();
                $diff = $fechaInicio->diffInDays($fechaFin);
                $total = (int) $diff;
            }

            if ($total > 60) {
                $b->update([
                    'estado' => 'EJECUCION',
                ]);
            }
            if ($total > 0 && $total <= 60) {
                $b->update([
                    'estado' => 'VENCIDO',
                ]);
            }
            if ($total <= 0) {
                $b->update([
                    'estado' => 'VIGENTE',
                ]);
            }

            $count++;
        }

        Log::info('Beneficiaries status updated ['.$count.']');
    }
}
