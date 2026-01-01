<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{

    use \App\Traits\BelongsToUser;
    protected $fillable = [
        'user_id',
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
}
