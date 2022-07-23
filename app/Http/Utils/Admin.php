<?php
namespace App\Http\Utils;

use Illuminate\Support\Facades\DB;

class Admin {

    protected $sites, $partners, $states;
    public $filters;

    public function getSites() {
        if(!empty($this->sites)) {
            return $this->sites;
        }

        $builder = DB::connection("admin")->table("sites")->select('siteid', 'site_title', 'site_category');
        
        if(!empty($this->filters['site_category'])) {
            $site_category = strtolower($this->filters['site_category']);
            $builder->where('site_category', $site_category);
        }
        

        $this->sites = $builder->get();
        return $this->sites;
    }

    public function getSitesByCategory() {
        return $this->getSites()->groupBy('site_category');
    }

    public function getPartners($filters) {

        if(!empty($this->partners)) {
            return $this->partners;
        }

        $filters = array_merge(
            array(),
            $filters
        );

        $builder = DB::connection("admin")->table("sites")->select("site_type");
        
        $builder->whereNotNull('site_type')->where("site_type", "!=", "");


        if(isset($filters['site_category']) && !empty($filters['site_category'])) {
            $builder->where('site_category', $filters['site_category']);
        }


        $this->partners = $builder->distinct()->pluck('site_type', 'site_type');
        return $this->partners;
    }

    public function getStates($country_id) {

        if(!empty($this->states)) {
            return $this->states;
        }

        $builder = DB::connection("admin")->table("states")->select("id", "name");

        $builder->where('country_id', $country_id);

        $this->states = $builder->distinct()->pluck('name', 'id');

        return $this->states;
    }

    public function setFilters($filters) {
        $this->filters = $filters;
    }

}

?>