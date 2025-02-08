<?php

use App\Http\Controllers\PaymentsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/payment/submit', [PaymentsController::class, 'submitPayment'])->name('payment.submit');
Route::post('/payment/verify', [PaymentsController::class, 'verifyToken'])->name('payment.verify');
Route::get('/payment/success', [PaymentsController::class, 'paymentSuccess'])->name('payment.success');
Route::get('/payment/failure', [PaymentsController::class, 'paymentFailure'])->name('payment.failure');
