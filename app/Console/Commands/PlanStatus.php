<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PlanStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:plan-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if a plan is expired and change its status to VENCIDO.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $plans = \App\Models\Plan::all();
        foreach ($plans as $p)
        {
            if ($p->estado == 'ACTIVO' AND $p->fecha_ppg < now()){
                $p->estado = 'VENCIDO';
            }
        }

        Log::info('Plan status updated');
    }
}
