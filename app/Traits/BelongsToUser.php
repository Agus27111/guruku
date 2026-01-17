<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToUser
{
    protected static function bootBelongsToUser()
    {
        // 1. OTOMATIS ISI user_id saat create data baru
        static::creating(function ($model) {
            if (Auth::check() && empty($model->user_id)) {
                $model->user_id = Auth::id();
            }
        });

        // 2. OTOMATIS FILTER data (Global Scope)
        static::addGlobalScope('user_filter', function (Builder $builder) {
            if (Auth::check()) {
                $user = Auth::user();

                // HANYA filter jika user BUKAN super_admin
                if (! $user->hasRole('super_admin')) {
                    $builder->where('user_id', $user->id);
                }
            }
        });
    }

    /**
     * Relasi ke Model User
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
