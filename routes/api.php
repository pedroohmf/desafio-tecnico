<?php

use App\Http\Controllers\operacoesController;
use App\Http\Middleware\CheckAccountExists;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/criarconta', [operacoesController::class, 'create'])->name('criarconta.create');
Route::post('/deposito', [operacoesController::class, 'deposito'])->name('operacoescontroller.deposito');

Route::put('/saque', [operacoesController::class, 'saque'])->name('operacoescontroller.saque');

Route::get('/saldo', [operacoesController::class, 'saldo'])->name('saldo');
