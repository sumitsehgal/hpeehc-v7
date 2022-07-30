<?php
namespace App\Http\Utils;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Admin {

    protected $sites, $partners, $states;
    public $filters;

    public function getSites() {
        if(!empty($this->sites)) {
            return $this->sites;
        }

        $builder = DB::connection("admin")->table("sites")->select('siteid', 'site_title', 'site_category', 'site_active_status');
        
        if(!empty($this->filters['site_category'])) {
            $site_category = strtolower($this->filters['site_category']);
            if($site_category != "mobile") {
                $builder->where('site_category', $site_category);
            }
        }

        if(isset($this->filters['state_id']) && !empty($this->filters['state_id'])) {
            $state_id = $this->filters['state_id'];
            $builder->where('state_id', $state_id);
        }

        if(isset($this->filters['partner']) && !empty($this->filters['partner'])) {
            $builder->where('site_type', $this->filters['partner']);
        }

        $loggedUser = Auth::user();
        if($loggedUser) {
            if($loggedUser->hasRole('state')) {
                $stateIds = $loggedUser->realAssetsByRole('state')->pluck('object_id');
                $builder->whereIn('state_id', $stateIds);
                
            }else if($loggedUser->hasRole('site')) {
                $siteIds = $loggedUser->realAssetsByRole('site')->pluck('object_id');
                $builder->whereIn('siteid', $siteIds);
            }

        }

        $this->sites = $builder->get();
        return $this->sites;
    }

    public function getSitesTitle() {
        $sites = $this->getSites();

        return $sites->pluck('site_title', 'siteid');
    }

    public function getSitesStatus() {
        $sites = $this->getSites();

        return $sites->pluck('site_active_status', 'siteid');
    }

    public function getSitesByCategory() {
        return $this->getSites()->groupBy('site_category');
    }

    public function getPartners() {

        if(!empty($this->partners)) {
            return $this->partners;
        }

        $builder = DB::connection("admin")->table("sites")->select("site_type");
        
        $builder->whereNotNull('site_type')->where("site_type", "!=", "");


        if(isset($this->filters['site_category']) && !empty($this->filters['site_category']) && strtolower($this->filters['site_category']) != "mobile") {
            $builder->where('site_category', $this->filters['site_category']);
        }

        if(isset($this->filters['state_id']) && !empty($this->filters['state_id'])) {
            $builder->where('state_id', $this->filters['state_id']);
        }

        $loggedUser = Auth::user();
        if($loggedUser) {
            if($loggedUser->hasRole('state')) {
                $stateIds = $loggedUser->realAssetsByRole('state')->pluck('object_id');
                $builder->whereIn('state_id', $stateIds);
                
            }else if($loggedUser->hasRole('site')) {
                $siteIds = $loggedUser->realAssetsByRole('site')->pluck('object_id');
                $builder->whereIn('siteid', $siteIds);
            }

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

        $loggedUser = Auth::user();
        if($loggedUser) {
            if($loggedUser->hasRole('state')) {
                $stateIds = $loggedUser->realAssetsByRole('state')->pluck('object_id');
                $builder->whereIn('id', $stateIds);
                
            }else if($loggedUser->hasRole('site')) {
                $siteIds = $loggedUser->realAssetsByRole('site')->pluck('object_id');
                $stateIds = DB::connection("admin")->table("sites")->select("state_id")->distinct()->whereIn('siteid', $siteIds)->pluck("state_id");
                $builder->whereIn('id', $stateIds);

            }

        }

        $this->states = $builder->distinct()->pluck('name', 'id');

        return $this->states;
    }

    public function setFilters($filters) {
        $this->filters = $filters;
    }

    public function getSiteCategoryKeys() {
        return [
            'eHC' => 'ehc',
            'COVID' => 'covid',
            'COE' => 'coe',
            'Digital Village' => 'digital village',
            'TB' => 'tb',
            'MeHC' => 'mobile',
            'Vaccination' => 'vaccination'
        ];

    }
 
}

?>