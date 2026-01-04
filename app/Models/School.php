<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class School extends Model
{

    use \Illuminate\Database\Eloquent\SoftDeletes;
    protected $fillable = ['name', 'slug'];

    protected static function booted(): void
    {
        static::creating(function (School $school) {
            // Jika slug kosong, buat dari name
            if (! $school->slug) {
                $school->slug = static::generateUniqueSlug($school->name);
            }
        });
    }

    /**
     * Logika untuk membuat slug yang unik
     */
    private static function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        // Cek jika slug sudah ada di database (termasuk yang di soft delete)
        while (static::where('slug', $slug)->withTrashed()->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    // relasi resource contoh (nanti dipakai scoping resource):
    public function learningJournals(): HasMany
    {
        return $this->hasMany(LearningJournal::class);
    }
    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }
    public function classrooms()
    {
        return $this->hasMany(Classroom::class);
    }
    public function students()
    {
        return $this->hasMany(Student::class);
    }
    public function studentDevelopments()
    {
        return $this->hasMany(StudentDevelopment::class);
    }
}
