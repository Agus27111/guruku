<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Assessment extends Model
{
    use SoftDeletes, BelongsToUser, BelongsToSchool; // Cukup ini, GlobalScope sudah ada di Trait

    protected $fillable = [
        'school_id',
        'student_id',
        'user_id',
        'subject_id',
        'assessment_type',
        'assessment_date',
        'score',
        'max_score',
        'remarks',
    ];

    protected $casts = [
        'assessment_date' => 'date',
    ];

    // Pindahkan logic isPro() ke Resource/Policy, jangan di sini.

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function schoool()
    {
        return $this->belongsTo(\App\Models\School::class);
    }
}
