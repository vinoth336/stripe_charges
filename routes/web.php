<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CheckOutController;

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

Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name("home");
Route::post('/checkout/session', [CheckOutController::class, 'createCheckoutSession'])->name('product.checkout');
Route::get('/checkout/success', [CheckOutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/cancel', function () {
    return view('checkout.cancel');
})->name('checkout.cancel');
