<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $guarded = [];

    public function startups() {
        return $this->hasMany(Startup::class);
    }
}
