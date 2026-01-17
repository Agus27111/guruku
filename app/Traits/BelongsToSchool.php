<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait BelongsToSchool
{
    public static function bootBelongsToSchool(): void
    {
        static::creating(function (Model $model) {
            if (empty($model->school_id) && auth()->check()) {
                // Langsung ambil school_id dari kolom di tabel users
                $model->school_id = auth()->user()->school_id;
            }
        });

        static::addGlobalScope('school', function ($builder) {
            if (auth()->check()) {
                // Pastikan data yang tampil hanya milik sekolah si guru yang login
                $builder->where('school_id', auth()->user()->school_id);
            }
        });
    }

    public function school()
    {
        return $this->belongsTo(\App\Models\School::class);
    }
}
