<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use SoftDeletes, BelongsToSchool;
    protected $fillable = [
        'classroom_id',
        'school_id',
        'name',
        'nisn',
    ];

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function developments()
    {
        return $this->hasMany(StudentDevelopment::class);
    }
    public function school()
    {
        return $this->belongsTo(\App\Models\School::class);
    }
}
