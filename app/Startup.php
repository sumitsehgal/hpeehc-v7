<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Startup extends Model
{
    public $timestamps = false;
    protected $guarded = [];

    public function partner() {
        return $this->belongsTo(Partner::class);
    }

}
