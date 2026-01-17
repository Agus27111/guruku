<?php

namespace App\Models;

use App\Enums\PlanType;
use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{

    use BelongsToUser;

    protected $fillable = [
        'user_id',
        'invoice_number',
        'amount',
        'status',
        'plan_type',
        'payment_type',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'amount' => 'integer',
        'plan_type' => PlanType::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
