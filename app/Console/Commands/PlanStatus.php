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
            $sixtyDaysAgo = now()->subDays(60)->toDateString();

            // 1. Mark expired plans (ACTIVO -> VENCIDO)
            \App\Models\Plan::where('estado', 'ACTIVO')
                ->where('fecha_ppg', '<', $today)
                ->update(['estado' => 'VENCIDO']);

            // 2. Fix incorrectly expired plans (VENCIDO -> ACTIVO) - rare case fix
            \App\Models\Plan::where('estado', 'VENCIDO')
                ->where('fecha_ppg', '>', $today)
                ->update(['estado' => 'ACTIVO']);

            // 3. Update 60-day delay flag for Plans
            // Mark YES
            \App\Models\Plan::where('estado', 'VENCIDO')
                ->where(function ($q) {
                    $q->whereNull('prppgmpag')->orWhere('prppgmpag', '!=', 'SI');
                })
                ->where('fecha_ppg', '<=', $sixtyDaysAgo)
                ->update(['prppgmpag' => 'SI']);

            // Mark NO
            \App\Models\Plan::where('estado', 'VENCIDO')
                ->where('prppgmpag', '!=', 'NO')
                ->where('fecha_ppg', '>', $sixtyDaysAgo)
                ->update(['prppgmpag' => 'NO']);

            // --- Readjustments (Same logic) ---

            // 1. Mark expired readjustments
            \App\Models\Readjustment::where('estado', 'ACTIVO')
                ->where('fecha_ppg', '<', $today)
                ->update(['estado' => 'VENCIDO']);

            // 2. Fix incorrectly expired readjustments
            \App\Models\Readjustment::where('estado', 'VENCIDO')
                ->where('fecha_ppg', '>', $today)
                ->update(['estado' => 'ACTIVO']);

            // 3. Update 60-day delay flag for Readjustments
            // Mark YES
            \App\Models\Readjustment::where('estado', 'VENCIDO')
                ->where(function ($q) {
                    $q->whereNull('prppgmpag')->orWhere('prppgmpag', '!=', 'SI');
                })
                ->where('fecha_ppg', '<=', $sixtyDaysAgo)
                ->update(['prppgmpag' => 'SI']);

            // Mark NO
            \App\Models\Readjustment::where('estado', 'VENCIDO')
                ->where('prppgmpag', '!=', 'NO')
                ->where('fecha_ppg', '>', $sixtyDaysAgo)
                ->update(['prppgmpag' => 'NO']);

            Log::info('Plan status updated');
        } catch (\Exception $e) {
            Log::info('Error updating plan status: ' . $e->getMessage());
        }
    }
}
