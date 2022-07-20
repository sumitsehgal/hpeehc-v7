<?php
namespace App\Http\Utils;

use Illuminate\Support\Facades\DB;

class Admin {

    protected $sites;

    public function getSites() {
        if(!empty($this->sites)) {
            return $this->sites;
        }
        $this->sites = DB::connection("admin")->table("sites")->select('siteid', 'site_title', 'site_category')->get();
        return $this->sites;
    }

    public function getSitesByCategory() {
        return $this->getSites()->groupBy('site_category');
    }


}

?>