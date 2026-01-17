<?php

namespace App\Http\Controllers\Payment;

use App\Enums\PlanType;
use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransController extends Controller
{
    public function callback(Request $request)
    {
        $serverKey = config('services.midtrans.server_key');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed !== $request->signature_key) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $subscription = Subscription::where('invoice_number', $request->order_id)->first();

        if (!$subscription) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        $status = $request->transaction_status;

        if ($status == 'settlement' || $status == 'capture') {
            $subscription->update([
                'status' => 'success',
                'paid_at' => now(),
                'payment_type' => $request->payment_type,
            ]);

            $user = User::find($subscription->user_id);

            if ($user) {
                $duration = ($subscription->plan_type === PlanType::YEARLY)
                    ? now()->addYear()
                    : now()->addMonth();

                $user->update([
                    'is_pro' => true,
                    'pro_expired_at' => $duration,
                ]);
            }
        } elseif ($status == 'pending') {
            $subscription->update(['status' => 'pending']);
        } elseif (in_array($status, ['deny', 'expire', 'cancel'])) {
            $subscription->update(['status' => 'failed']);
        }

        return response()->json(['message' => 'Callback handled successfully']);
    }

    public function handleWebhook(Request $request)
    {
        $data = $request->all();
        Log::info('Data Midtrans:', $data);

        if (str_contains($data['order_id'], 'payment_notif_test')) {
            return response()->json(['message' => 'Ignore testing data'], 200);
        }

        $transaction = $data['transaction_status'];
        $order_id = $data['order_id'];
        $status_code = $data['status_code'];
        $gross_amount = $data['gross_amount'];

        $serverKey = config('services.midtrans.server_key');
        $signature = hash("sha512", $order_id . $status_code . $gross_amount . $serverKey);

        if ($signature !== $data['signature_key']) {
            return response()->json(['message' => 'Invalid Signature'], 403);
        }

        $subscription = Subscription::where('invoice_number', $order_id)->first();

        if (!$subscription) {
            return response()->json(['message' => 'Order tidak ditemukan'], 404);
        }

        if ($subscription->status === 'success') {
            return response()->json(['status' => 'OK', 'message' => 'Already processed'], 200);
        }

        if ($transaction == 'settlement' || $transaction == 'capture') {
            $subscription->update([
                'status' => 'success',
                'paid_at' => now(),
                'payment_type' => $data['payment_type'] ?? null,
            ]);

            $user = User::find($subscription->user_id);
            if ($user) {
                // PERBAIKAN: Bandingkan dengan Enum
                $duration = ($subscription->plan_type === PlanType::YEARLY)
                    ? now()->addYear()
                    : now()->addMonth();

                $user->update([
                    'is_pro' => true,
                    'pro_expired_at' => $duration,
                ]);
            }
        }
        return response()->json(['status' => 'OK']);
    }
}
