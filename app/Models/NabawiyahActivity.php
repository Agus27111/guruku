<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NabawiyahActivity extends Model
{

    use SoftDeletes;
    protected $guarded = [];

    protected $casts = [
        // Cast semua pilar ke boolean secara otomatis
        'pilar_himmah' => 'boolean',
        'pilar_ihsaan' => 'boolean',
        'pilar_izzah' => 'boolean',
        'pilar_waqaar' => 'boolean',
        'pilar_azimah' => 'boolean',
        'pilar_nasyaath' => 'boolean',
        'pilar_firaasah' => 'boolean',
        'pilar_husnuzhan' => 'boolean',
        'pilar_dzakaa' => 'boolean',
        'pilar_hikmah' => 'boolean',
        'pilar_kitmaan' => 'boolean',
        'pilar_satr' => 'boolean',
        'pilar_shidq' => 'boolean',
        'pilar_iffah' => 'boolean',
        'pilar_shamt' => 'boolean',
        'pilar_hayaa' => 'boolean',
        'pilar_qanaah' => 'boolean',
        'pilar_anaah' => 'boolean',
        'pilar_hilm' => 'boolean',
        'pilar_tawaadhu' => 'boolean',
        'pilar_shabr' => 'boolean',
        'pilar_syajaaah' => 'boolean',
        'pilar_ghairah' => 'boolean',
        'pilar_munaafasah' => 'boolean',
        'pilar_nashiihah' => 'boolean',
        'pilar_fashaahah' => 'boolean',
        'pilar_nashrah' => 'boolean',
        'pilar_sakhaa' => 'boolean',
        'pilar_taawun' => 'boolean',
        'pilar_ulfah' => 'boolean',
        'pilar_adaalah' => 'boolean',
        'pilar_wafaa' => 'boolean',
        'pilar_muzaah' => 'boolean',
        'pilar_basyaasyah' => 'boolean',
        'pilar_rifq' => 'boolean',
        'pilar_rahmah' => 'boolean',
        'pilar_mahabbah' => 'boolean',
        'pilar_iitsaar' => 'boolean',
        'pilar_amaanah' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function school()
    {
        return $this->belongsTo(School::class);
    }
    public function students()
    {
        return $this->belongsToMany(Student::class, 'nabawiyah_activity_student');
    }
}
