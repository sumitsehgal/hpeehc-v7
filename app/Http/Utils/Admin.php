<?php
namespace App\Http\Utils;

use App\Studio;
use App\StudioAsset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client as GuzzleClient;

class Admin {

    protected $sites, $partners, $states;
    public $filters;

    public function getSites() {
        if(!empty($this->sites)) {
            return $this->sites;
        }

        $builder = DB::connection("admin")->table("sites")->select('siteid', 'site_title', 'site_category', 'site_active_status', 'lng', 'lat', 'is_rejected', 'site_type');
        
        if(!empty($this->filters['site_category'])) {
            $site_category = strtolower($this->filters['site_category']);
            if($site_category != "mobile") {
                $builder->where('site_category', $site_category);
            }
        }

        if(!empty($this->filters['not_site_category'])) {
            $site_category = strtolower($this->filters['not_site_category']);
           $siteCategories = explode(",", $site_category);
           $builder->whereNotIn('site_category', $siteCategories);
        }

        if(isset($this->filters['state_id']) && !empty($this->filters['state_id'])) {
            $state_id = $this->filters['state_id'];
            $builder->where('state_id', $state_id);
        }

        if(isset($this->filters['partner']) && !empty($this->filters['partner'])) {
            $builder->where('site_type', $this->filters['partner']);
        }

        if(isset($this->filters['siteid']) && !empty($this->filters['siteid'])) {
            $builder->where('siteid', $this->filters['siteid']);
        }
	$builder->where('is_rejected',0);
        $loggedUser = Auth::user();
        if($loggedUser) {
            if($loggedUser->hasRole('state')) {
                $stateIds = $loggedUser->realAssetsByRole('state')->pluck('object_id');
                $builder->whereIn('state_id', $stateIds);
                
            }else if($loggedUser->hasRole('site')) {
                $siteIds = $loggedUser->realAssetsByRole('site')->pluck('object_id');
                $builder->whereIn('siteid', $siteIds);
            }else if($loggedUser->hasRole('partner')) {
                $partnerIds = $loggedUser->realAssetsByRole('partner')->pluck('object_id');
                $builder->whereIn('site_type', $partnerIds);
            }

        }

        $this->sites = $builder->get();
        return $this->sites;
    }

    public function getStudio() {

        $builder = StudioAsset::query();
        $loggedUser = Auth::user();

        $studioBuilder = Studio::query();

        if($loggedUser->hasRole('state')) {
            $stateIds = $loggedUser->realAssetsByType('state')->pluck('object_id');
            $builder->whereIn('object_id', $stateIds)->where('object_type', 'state');
            
        }else if($loggedUser->hasRole('site')) {
            $siteIds = [];
            $builder->whereIn('object_id', $siteIds)->where('object_type', 'site');
        }else if($loggedUser->hasRole('partner')) {
            $partnerIds = $loggedUser->realAssetsByRole('partner')->pluck('object_id');
            $builder->whereIn('object_id', $partnerIds)->where('object_type', 'partner');
        }

        $filteredStudioIds = $builder->pluck('studio_id');
        if(!$loggedUser->hasRole('admin')) {
            $studioBuilder->whereIn('id', $filteredStudioIds);
        }
        $studios = $studioBuilder->get();
        return $studios;

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
        return $this->getSites()->where('is_rejected', 0)->groupBy('site_category');
    }

    public function getPartners() {

        if(!empty($this->partners)) {
            return $this->partners;
        }

        $builder = DB::connection("admin")->table("sites")->select("site_type");
        
        $builder->whereNotNull('site_type')->where("site_type", "!=", "");
        $builder->where('site_type', '!=', 'test');


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
            }else if($loggedUser->hasRole('partner')) {
                $siteIds = $loggedUser->realAssetsByRole('partner')->pluck('object_id');
                $builder->whereIn('site_type', $siteIds);
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

            }else if($loggedUser->hasRole('partner')) {
                $siteIds = $loggedUser->realAssetsByRole('partner')->pluck('object_id');
                $stateIds = DB::connection("admin")->table("sites")->select("state_id")->distinct()->whereIn('site_type', $siteIds)->pluck("state_id");
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
            'CoE' => 'coe',
            'Digital Village' => 'digital village',
            'TB Screening' => 'tb',
            'MeHC' => 'mobile',
            // 'Vaccination' => 'vaccination',
            'COVID Vaccination & OPD Center' => 'vaccination'
        ];

    }

    public function getCSC($sites) {

        $client = new GuzzleClient();
        $res = $client->get('https://hpedigitalvillage.com/api/total-csc');

        $totalCSC = 0;
        if($res->getStatusCode() != 200) {
            $totalCSC = 0;
        }else {

            $dvSites = json_decode($res->getBody()->getContents());
            
            if(!is_array($dvSites) || empty($dvSites)) {
                $totalCSC = 0;
            }else {
                $user = Auth::user();
                foreach($dvSites as $dvSite) {
                    if(in_array($dvSite->site_name, $sites) || ($user->hasRole('admin') && $dvSite->site_name == null) ) {
                        $totalCSC += $dvSite->base_csc;
                    }
                }

            }
        }

        return $totalCSC;

    }

    public function getStudents($sites) {
        $client = new GuzzleClient();
        $res = $client->get('https://hpedigitalvillage.com/api/total-students');
        
        if($res->getStatusCode() != 200) {
            return response()->json([
                'error' => [
                    "message" => "Digital Village Server Not Responding",
                    "code" => $res->getStatusCode()
                ]
            ]);
        }

        $dvSites = json_decode($res->getBody()->getContents());
        // dd($dvSites);
        $totalStudents = 0;
        // TEMP-CHANGE
        // if(!is_array($dvSites) || empty($dvSites)) {
        //     $totalStudents = 0;
        // }else {
        //     $user = Auth::user();
        //     foreach($dvSites as $dvSite) {
        //         if(in_array($dvSite->site_name, $sites)) {
        //             $totalStudents += $dvSite->students;
        //         }
        //     }

        // }

        $totalStudents = $dvSites;

        return $totalStudents;
    }
 
}

?>
