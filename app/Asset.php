<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $guarded =[];
    public $timestamps = false;


    public static $siteCategories = [
        'none' => 'None',
        'digital village' => 'Digital Village',
        'ehc' => 'eHC',
        'covid' => 'COVID Vaccination & OPD Center',
        'vaccination' => 'COVID Vaccination & OPD Center',
        // 'mobile' => 'Mobile',
        'coe' => 'CoE',
        'tb' => 'TB Screening'
    ];
}
