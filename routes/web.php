<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Beneficiarios
    Route::get('beneficiario', [\App\Http\Controllers\BeneficiaryController::class, 'index'])->name('beneficiario.index');
    Route::get('beneficiario/{cedula}', [\App\Http\Controllers\BeneficiaryController::class, 'show'])->name('beneficiario.show');
    Route::get('beneficiario/{beneficiary}/editar', [\App\Http\Controllers\BeneficiaryController::class, 'edit'])->name('beneficiario.edit');
    Route::post('beneficiario', [\App\Http\Controllers\BeneficiaryController::class, 'store'])->name('beneficiario.store');
    Route::put('beneficiario/{beneficiary}', [\App\Http\Controllers\BeneficiaryController::class, 'update'])->name('beneficiario.update');
    Route::delete('beneficiario/{beneficiary}', [\App\Http\Controllers\BeneficiaryController::class, 'destroy'])->name('beneficiario.destroy');

    // Planes
    Route::get('plan', [\App\Http\Controllers\PlanController::class, 'index'])->name('plan.index');
    Route::get('plan/{plan}', [\App\Http\Controllers\PlanController::class, 'show'])->name('plan.show');
    Route::get('plan/{plan}/editar', [\App\Http\Controllers\PlanController::class, 'edit'])->name('plan.edit');
    Route::post('plan', [\App\Http\Controllers\PlanController::class, 'store'])->name('plan.store');
    Route::post('plan/reajuste', [\App\Http\Controllers\PlanController::class, 'reajuste'])->name('plan.reajuste');
    Route::get('plan/ajuste-masivo/{data}', [\App\Http\Controllers\PlanController::class, 'bulkAdjust'])->name('plan.bulk-adjust');
    Route::put('plan/{plan}', [\App\Http\Controllers\PlanController::class, 'update'])->name('plan.update');
    Route::delete('plan/{plan}', [\App\Http\Controllers\PlanController::class, 'destroy'])->name('plan.destroy');

    // Vouchers
    Route::get('voucher', [\App\Http\Controllers\VoucherController::class, 'index'])->name('voucher.index');
    Route::get('voucher/{voucher}', [\App\Http\Controllers\VoucherController::class, 'show'])->name('voucher.show');
    Route::get('voucher/{voucher}/editar', [\App\Http\Controllers\VoucherController::class, 'edit'])->name('voucher.edit');
    Route::post('voucher', [\App\Http\Controllers\VoucherController::class, 'store'])->name('voucher.store');
    Route::put('voucher/{voucher}', [\App\Http\Controllers\VoucherController::class, 'update'])->name('voucher.update');
    Route::delete('voucher/{voucher}', [\App\Http\Controllers\VoucherController::class, 'destroy'])->name('voucher.destroy');

    // Proyectos
    Route::get('proyecto', [\App\Http\Controllers\ProjectController::class, 'index'])->name('proyecto.index');
    Route::get('proyecto/{proyecto}', [\App\Http\Controllers\ProjectController::class, 'show'])->name('proyecto.show');
    Route::get('proyecto/{proyecto}/editar', [\App\Http\Controllers\ProjectController::class, 'edit'])->name('proyecto.edit');
    Route::post('proyecto', [\App\Http\Controllers\ProjectController::class, 'store'])->name('proyecto.store');
    Route::put('proyecto/{proyecto}', [\App\Http\Controllers\ProjectController::class, 'update'])->name('proyecto.update');
    Route::delete('proyecto/{proyecto}', [\App\Http\Controllers\ProjectController::class, 'destroy'])->name('proyecto.destroy');

    // Exportaciones e Importaciones
    Route::post('excel/import-model', [\App\Http\Controllers\ExcelController::class, 'importModelCSV'])->name('excel.import-model');
    Route::get('excel/export-model/{model}', [\App\Http\Controllers\ExcelController::class, 'exportModel'])->name('excel.export-model');
    Route::post('excel/export-collection', [\App\Http\Controllers\ExcelController::class, 'exportCollection'])->name('excel.export-collection');
});
