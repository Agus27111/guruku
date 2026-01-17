<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    public function getSnapToken($subscription)
    {
        $params = [
            'transaction_details' => [
                'order_id' => $subscription->invoice_number,
                'gross_amount' => $subscription->amount,
            ],
            'customer_details' => [
                'first_name' => auth()->user()->name,
                'email' => auth()->user()->email,
            ],
            // Kamu bisa menambahkan item_details jika ingin rincian produk
        ];

        return Snap::getSnapToken($params);
    }
}
