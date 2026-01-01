<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LearningJournal extends Model
{
    use SoftDeletes;
    //traits
    use \App\Traits\BelongsToUser;
    protected $fillable = [
        'user_id',
        'classroom_id',
        'subject_id',
        'date',
        'teaching_hours',
        'topic',
        'activity',
        'note',
        'photo',
    ];
    protected $casts = [
        'teaching_hours' => 'array',
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
