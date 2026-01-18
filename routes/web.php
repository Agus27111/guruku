<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Payment\MidtransController;
use Illuminate\Support\Facades\Artisan;

Route::get('/', function () {
    return redirect('/admin');
});


Route::post('/api/midtrans-callback', [MidtransController::class, 'handleWebhook']);


Route::get('/clear-config', function () {
    Artisan::call('config:cache');
    Artisan::call('cache:clear');
    return "Konfigurasi Berhasil Di-refresh";
});
