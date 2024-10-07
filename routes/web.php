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
    Route::resources([
        'beneficiary' => \App\Http\Controllers\BeneficiaryController::class,
        //'payment' => \App\Http\Controllers\PaymentController::class,
        'plan' => \App\Http\Controllers\PlanController::class,
        'voucher' => \App\Http\Controllers\VoucherController::class,
        'project' => \App\Http\Controllers\ProjectController::class
    ]);
});
