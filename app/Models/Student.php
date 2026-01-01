<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'classroom_id',
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
}
