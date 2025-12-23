<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

abstract class Controller
{
    use \App\Traits\FinanceTrait;










    public function generatePlanData(Request $request)
    {
        return $this->generarPlan(
            $request->input('capital_inicial'),
            \App\Models\Spend::where('idepro', $request->input('idepro'))->where('estado', 'ACTIVO')->sum('monto') ?? 0,
            $request->input('meses'),
            $request->input('taza_interes'),
            $request->input('seguro'),
            $request->input('correlativo'),
            $request->input('plazo_credito'),
            $request->input('fecha_inicio'),
            \App\Models\Earn::where('idepro', $request->input('idepro'))->sum('interes') ?? 0,
            \App\Models\Earn::where('idepro', $request->input('idepro'))->sum('seguro') ?? 0,
        );
    }

    public function generateDiferimentoIfNeeded(Request $request, $data)
    {
        if ($request->filled(['diff_cuotas', 'diff_capital', 'diff_interes'])) {
            return $this->generarDiferimento(
                $request->input('diff_cuotas'),
                $request->input('diff_capital'),
                $request->input('diff_interes'),
                $request->input('plazo_credito'),
                $data->last()->vencimiento
            );
        }

        return collect();
    }

    public function deactivateExistingRecords($idepro)
    {
        $models = [
            //\App\Models\Helper::class,
            \App\Models\Plan::class,
            \App\Models\Readjustment::class,
        ];

        $cuotaPagada = collect();

        foreach ($models as $model) {
            $cuotaPagada = $cuotaPagada->merge(
                $model::where('idepro', $idepro)->where('estado', 'CANCELADO')->get()
            );
        }

        foreach ($models as $model) {
            $model::where('idepro', $idepro)->delete();
        }

        return $cuotaPagada;
    }

    public function createNewRecords($data, $diferimento, Request $request, $userId)
    {
        $dynaModel = $request->input('correlativo') ? 'App\\Models\\Readjustment' : 'App\\Models\\Plan';
        $idepro = $request->input('idepro');

        if ($data) {
            foreach ($data as $d) {
                $dynaModel::create([
                    'idepro' => $idepro,
                    'fecha_ppg' => $d->vencimiento,
                    'prppgnpag' => $d->nro_cuota,
                    'prppgcapi' => ($d->abono_capital),
                    'prppginte' => ($d->interes),
                    'prppggral' => ($d->interes_devengado),
                    'prppgsegu' => ($d->seguro),
                    'prppgotro' => ($d->gastos_judiciales),
                    'prppgcarg' => ($d->seguro_devengado),
                    'prppgtota' => ($d->total_cuota),
                    'estado' => 'ACTIVO',
                    'user_id' => $userId,
                ]);
            }
        }
        if ($diferimento) {
            foreach ($diferimento as $d) {
                \App\Models\Helper::create([
                    'idepro' => $idepro,
                    'indice' => $d->nro_cuota,
                    'capital' => round($d->capital, 2),
                    'interes' => round($d->interes, 2),
                    'vencimiento' => $d->vencimiento,
                    'estado' => $d->estado,
                    'user_id' => $userId,
                ]);
            }
        }
    }

    public function downloadPlanCollection($cuotas, $fileTitle)
    {
        // Generate CSV
        $fileName = $fileTitle . '_' . uniqid() . '.csv';
        $filePath = storage_path('app/public/exports/' . $fileName);

        $file = fopen($filePath, 'w');
        fputcsv($file, [
            'idepro',
            'fecha_ppg',
            'prppgnpag',
            'prppgcapi',
            'prppginte',
            'prppggral',
            'prppgsegu',
            'prppgotro',
            'prppgcarg',
            'prppgtota',
            'prppgahor',
            'prppgmpag',
            'estado',
            'user_id',
        ]); // Adjust headers as needed

        foreach ($cuotas as $cuota) {
            fputcsv($file, [
                $cuota->idepro,
                $cuota->fecha_ppg,
                $cuota->prppgnpag,
                $cuota->prppgcapi,
                $cuota->prppginte,
                $cuota->prppggral,
                $cuota->prppgsegu,
                $cuota->prppgotro,
                $cuota->prppgcarg,
                $cuota->prppgtota,
                $cuota->prppgahor,
                $cuota->prppgmpag,
                $cuota->estado,
                $cuota->user_id,
            ]);
        }

        fclose($file);

        return asset('storage/exports/' . basename($filePath));
    }
}
