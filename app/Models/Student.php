<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use SoftDeletes, BelongsToSchool, BelongsToUser;
    protected $fillable = [
        'classroom_id',
        'school_id',
        'user_id',
        'name',
        'nisn',
        'photo',
    ];


    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function scores() { return $this->hasMany(AssessmentScore::class); }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function developments()
    {
        return $this->hasMany(StudentDevelopment::class);
    }
    public function school()
    {
        return $this->belongsTo(\App\Models\School::class);
    }
    public function nabawiyahActivities()
    {
        return $this->belongsToMany(NabawiyahActivity::class, 'nabawiyah_activity_student');
    }

    // App/Models/Student.php

    // app/Models/Student.php

    public function getNabawiyahStats(): array
    {
        // Ambil relasi kegiatan nabawiyah siswa ini
        $activities = $this->nabawiyahActivities;

        // Daftar 40 pilar
        $pilarKeys = [
            'pilar_himmah',
            'pilar_ihsaan',
            'pilar_izzah',
            'pilar_waqaar',
            'pilar_azimah',
            'pilar_nasyaath',
            'pilar_firaasah',
            'pilar_husnuzhan',
            'pilar_dzakaa',
            'pilar_hikmah',
            'pilar_kitmaan',
            'pilar_satr',
            'pilar_shidq',
            'pilar_iffah',
            'pilar_shamt',
            'pilar_hayaa',
            'pilar_qanaah',
            'pilar_anaah',
            'pilar_hilm',
            'pilar_tawaadhu',
            'pilar_shabr',
            'pilar_syajaaah',
            'pilar_ghairah',
            'pilar_munaafasah',
            'pilar_nashiihah',
            'pilar_fashaahah',
            'pilar_nashrah',
            'pilar_sakhaa',
            'pilar_taawun',
            'pilar_ulfah',
            'pilar_adaalah',
            'pilar_wafaa',
            'pilar_muzaah',
            'pilar_basyaasyah',
            'pilar_rifq',
            'pilar_rahmah',
            'pilar_mahabbah',
            'pilar_iitsaar',
            'pilar_amaanah'
        ];

        $results = [];

        foreach ($pilarKeys as $pilar) {
            // Menghitung berapa kali pilar ini bernilai true/1 dalam semua aktivitas siswa
            $results[$pilar] = $activities->where($pilar, true)->count();
        }

        return $results;
    }
}
