<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tahfidz extends Model
{
    use SoftDeletes, BelongsToSchool, BelongsToUser;

    protected $fillable = [
        'user_id',
        'school_id',
        'student_id',
        'surah',
        'juz',
        'start_verse',
        'end_verse',
        'predicate',
        'note',
        'recorded_at',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }


}
