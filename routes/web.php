<?php

use App\Http\Controllers\PaymentsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::post('/charge/credit-card', [PaymentsController::class, 'chargeCreditCard'])->name('charge.credit-card');
Route::post('/charge/mobile-money', [PaymentsController::class, 'chargeMobileMoney'])->name('charge.mobile-money');
Route::get('/payment/success', [PaymentsController::class, 'paymentSuccess'])->name('payment.success');
Route::get('/payment/failure', [PaymentsController::class, 'paymentFailure'])->name('payment.failure');
