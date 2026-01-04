<?php

namespace App\Traits;

use App\Models\School;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait BelongsToSchool
{
    public static function bootBelongsToSchool(): void
    {
        // 1. Otomatis isi school_id saat record dibuat (INSERT)
        static::creating(function (Model $model) {
            if (empty($model->school_id) && $tenant = Filament::getTenant()) {
                $model->school_id = $tenant->id;
            }
        });

        // 2. Otomatis filter data berdasarkan school_id (SELECT)
        static::addGlobalScope('school', function (Builder $builder) {
            if ($tenant = Filament::getTenant()) {
                $builder->where('school_id', $tenant->id);
            }
        });
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
