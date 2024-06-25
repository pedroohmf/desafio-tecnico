<?php

use App\Http\Controllers\operacoesController;
use App\Http\Middleware\CheckAccountExists;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/criarconta', [operacoesController::class, 'create']);
Route::post('/deposito/{idConta}/{moeda}/{valor}', [operacoesController::class, 'deposito'])->middleware(CheckAccountExists::class);

Route::get('/saldo/{idConta}/{moeda?}', [operacoesController::class, 'saldo'])->middleware(CheckAccountExists::class)->name('saldo');
Route::put('/saque/{idConta}/{moeda}/{valor}', [operacoesController::class, 'saque']);
