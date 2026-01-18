<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Assessment extends Model
{
    use SoftDeletes, BelongsToUser, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'user_id',
        'subject_id',
        'classroom_id',
        'assessment_type',
        'assessment_date',
        'remarks',
    ];

    protected $casts = [
        'assessment_date' => 'date',
    ];


    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function school()
    {
        return $this->belongsTo(\App\Models\School::class);
    }

    public function assessmentScores(): HasMany
    {
        return $this->hasMany(\App\Models\AssessmentScore::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }
}
