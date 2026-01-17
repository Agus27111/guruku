<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{

    use \App\Traits\BelongsToUser, BelongsToSchool;
    protected $fillable = [
        'user_id',
        'school_id',
        'name',
        'code',
    ];


    public function learningJournals()
    {
        return $this->hasMany(LearningJournal::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function school()
    {
        return $this->belongsTo(\App\Models\School::class);
    }
}
