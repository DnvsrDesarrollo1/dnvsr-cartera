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
        try {
            $today = now()->toDateString();

            \App\Models\Plan::where('estado', '!=', 'CANCELADO')->update([
                'estado' => \Illuminate\Support\Facades\DB::raw("
                    CASE
                        WHEN fecha_ppg < '$today' AND fecha_ppg != '$today' THEN 'VENCIDO'
                        WHEN fecha_ppg > '$today' THEN 'ACTIVO'
                        ELSE estado
                    END
                "),
            ]);

            \App\Models\Plan::where('estado', 'VENCIDO')
                ->update([
                    'prppgmpag' => \Illuminate\Support\Facades\DB::raw("
                    CASE
                        WHEN fecha_ppg <= ('$today'::date - INTERVAL '60 days') THEN 'NO'
                        ELSE 'SI'
                    END
                "),
                ]);

            \App\Models\Readjustment::where('estado', '!=', 'CANCELADO')->update([
                'estado' => \Illuminate\Support\Facades\DB::raw("
                    CASE
                        WHEN fecha_ppg < '$today' AND fecha_ppg != '$today' THEN 'VENCIDO'
                        WHEN fecha_ppg > '$today' THEN 'ACTIVO'
                        ELSE estado
                    END
                "),
            ]);

            \App\Models\Readjustment::where('estado', 'VENCIDO')
                ->update([
                    'prppgmpag' => \Illuminate\Support\Facades\DB::raw("
                    CASE
                        WHEN fecha_ppg <= ('$today'::date - INTERVAL '60 days') THEN 'NO'
                        ELSE 'SI'
                    END
                "),
                ]);

            Log::info('Plan status updated');
        } catch (\Exception $e) {
            Log::info('Error updating plan status: '.$e->getMessage());
        }
    }
}
