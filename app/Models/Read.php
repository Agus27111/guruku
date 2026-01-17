<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Read extends Model
{
    use SoftDeletes, BelongsToSchool, BelongsToUser;

    protected $fillable = [
        'user_id',
        'school_id',
        'student_id',
        'read_at',
        'type',
        'volume',
        'page',
        'predicate',
        'note',
    ];


    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
