<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

class User extends Authenticatable implements HasTenants
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function classrooms()
    {
        return $this->hasMany(Classroom::class);
    }

    public function learningJournals()
    {
        return $this->hasMany(LearningJournal::class);
    }

    // Filament Tenants
    public function schools(): BelongsToMany
    {
        return $this->belongsToMany(School::class)->withTimestamps();
    }

    public function getTenants(Panel $panel): Collection
    {
        return $this->schools;
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return true;
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(School::class)
            ->withPivot(['is_tahfidz_enabled', 'is_tahsin_enabled', 'is_read_enabled'])
            ->withTimestamps();
    }
}
