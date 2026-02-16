<?php

use App\Http\Controllers\ClientController;
use Illuminate\Support\Facades\Route;


Route::get('/registrar-senha/{token}', [ClientController::class, 'registerPassword'])->name('registrar-senha');

Route::post('/registrar-senha', [ClientController::class, 'storePassword'])->name('registrar-senha.store');