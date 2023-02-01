<?php

use App\Http\Controllers\bKashPaymentController;
use App\Http\Controllers\SslCommerzPaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get(
    '/',
    function () {
        return view('checkout');
    }
)->name('checkout');

Route::controller(bKashPaymentController::class)->group(
    function () {
        Route::post('token', 'token')->name('token');
        Route::get('createpayment', 'createpayment')->name('createpayment');
        Route::get('executepayment', 'executepayment')->name('executepayment');
    }
);
