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
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_pro',
        'pro_expired_at',
        'is_tahfidz_enabled',
        'is_tahsin_enabled',
        'is_read_enabled',
        'is_studentDevelopment_enabled',
        'is_assessment_enabled',
        'is_nabawiyah_enabled',
        'school_id',
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
            'pro_expired_at' => 'datetime',
            'is_pro' => 'boolean',
            'is_nabawiyah_enabled' => 'boolean',
        ];
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function classrooms()
    {
        return $this->hasMany(Classroom::class);
    }

    public function learningJournals()
    {
        return $this->hasMany(LearningJournal::class);
    }

    public function students()
    {
        return $this->hasMany(\App\Models\Student::class);
    }

    public function schools(): BelongsToMany
    {
        return $this->belongsToMany(School::class);
    }
    public function isPro(): bool
    {
        // Jika expired_at null, anggap langganan selamanya (atau sesuaikan)
        if ($this->is_pro && $this->pro_expired_at === null) return true;

        return $this->is_pro && $this->pro_expired_at > now();
    }

    public function canAccessPanel($panel): bool
    {
        // Contoh: Hanya user yang punya role yang bisa masuk
        return $this->hasAnyRole(['super_admin', 'Guru']);
        // Atau sementara return true jika masih tahap testing, tapi Role akan memfilter menu.
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    // Helper untuk cek apakah ada pembayaran yang sedang pending
    public function pendingSubscription()
    {
        return $this->subscriptions()->where('status', 'pending')->first();
    }
}
