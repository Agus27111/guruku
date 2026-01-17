<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Payment\MidtransController;


Route::get('/', function () {
    return redirect('/admin');
});


Route::post('/api/midtrans-callback', [MidtransController::class, 'handleWebhook']);
