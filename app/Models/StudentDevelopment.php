<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentDevelopment extends Model
{
    use SoftDeletes;
    //traits
    use \App\Traits\BelongsToUser;
    protected $fillable = [
        'user_id',
        'student_id',
        'date',
        'category',
        'note',
        'follow_up',
        'photo',
    ];
    protected $casts = [
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
