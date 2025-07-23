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
            \App\Models\Plan::where('estado', 'ACTIVO')->chunk(15000, function ($plans) {
                foreach ($plans as $p) {
                    if ($p->fecha_ppg < now()) {
                        $p->update([
                            'estado' => 'VENCIDO'
                        ]);
                    }
                }
            });

            \App\Models\Readjustment::where('estado', 'ACTIVO')->chunk(10000, function ($plans) {
                foreach ($plans as $p) {
                    if ($p->fecha_ppg < now()) {
                        $p->update([
                            'estado' => 'VENCIDO'
                        ]);
                    }
                }
            });

            Log::info('Plan status updated');
        } catch (\Exception $e) {
            Log::info('Error updating plan status: ' . $e->getMessage());
        }
    }
}
