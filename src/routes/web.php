<?php

use App\Http\Controllers\ContractSignatureController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Public contract e-signature routes (no auth required)
Route::get('/sign/{token}', [ContractSignatureController::class, 'show'])->name('contracts.sign');
Route::post('/sign/{token}', [ContractSignatureController::class, 'confirm'])->name('contracts.sign.confirm');
