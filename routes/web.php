<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\StripeWebhookController;
use Illuminate\Support\Facades\Route;


Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/login', [AuthController::class, 'showLogin'])->name('auth.login');

Route::get('/login/mercado', [AuthController::class, 'showLoginMercado'])->name('auth.login.mercado');
Route::post('/login/mercado', [AuthController::class, 'loginMercado'])->name('auth.login.mercado.submit')->middleware('throttle:6,1');
Route::get('/login/mercado/2fa', [AuthController::class, 'show2faChallenge'])->name('auth.login.mercado.2fa');
Route::post('/login/mercado/2fa', [AuthController::class, 'verify2faChallenge'])->name('auth.login.mercado.2fa.submit')->middleware('throttle:6,1');

Route::get('/login/sindico', [AuthController::class, 'showLoginSindico'])->name('auth.login.sindico');
Route::post('/login/sindico', [AuthController::class, 'loginSindico'])->name('auth.login.sindico.submit')->middleware('throttle:6,1');
Route::get('/registrar', [AuthController::class, 'showRegister'])->name('auth.register');
Route::post('/registrar', [AuthController::class, 'register'])->name('auth.register.submit')->middleware('throttle:6,1');
Route::get('/registrar/pagamento', [AuthController::class, 'showCheckout'])->name('auth.register.checkout');
Route::get('/registrar/retorno', [AuthController::class, 'checkoutReturn'])->name('auth.register.return');

Route::get('/assinatura-inativa', fn () => view('auth.subscription-inactive'))->name('assinatura.inativa');
Route::get('/assinatura-reativar', [AuthController::class, 'reactivateSubscription'])->name('assinatura.reativar');

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])->name('stripe.webhook');

Route::get('/registrar-senha/sucesso', fn () => view('client-password-success'))
    ->middleware('auth:client')
    ->name('registrar-senha.sucesso');

Route::get('/registrar-senha/{token}', [ClientController::class, 'registerPassword'])->name('registrar-senha');

Route::post('/registrar-senha', [ClientController::class, 'storePassword'])->name('registrar-senha.store')->middleware('throttle:6,1');
