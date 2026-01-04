<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Classroom extends Model
{
    //traits
    use \App\Traits\BelongsToUser;
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'school_id',
        'name',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
    public function school()
    {
        return $this->belongsTo(\App\Models\School::class);
    }
}
