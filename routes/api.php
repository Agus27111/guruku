<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Payment\MidtransController;

// Laravel akan otomatis menambah /api di depannya
Route::post('/midtrans-callback', [MidtransController::class, 'handleWebhook']);
