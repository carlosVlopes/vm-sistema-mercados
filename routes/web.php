<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;


Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/login', [AuthController::class, 'showLogin'])->name('auth.login');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login.submit');
Route::get('/registrar', [AuthController::class, 'showRegister'])->name('auth.register');
Route::post('/registrar', [AuthController::class, 'register'])->name('auth.register.submit');

Route::get('/registrar-senha/sucesso', fn () => view('client-password-success'))->name('registrar-senha.sucesso');

Route::get('/registrar-senha/{token}', [ClientController::class, 'registerPassword'])->name('registrar-senha');

Route::post('/registrar-senha', [ClientController::class, 'storePassword'])->name('registrar-senha.store');