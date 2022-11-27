<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Utils\Admin;
use App\Http\Utils\Project;

class SiteController extends Controller
{
    public $admin, $project;

    public function __construct()
    {
        $this->admin = new Admin;
        $this->project = new Project;
    }
    
    public function index() {
        $sites = $this->admin->getSites();

        return response()->json( $sites );


    }

    public function longLat(Request $request) {
        $filters = [];
        if($request->has('site_type')) {
            $filters['site_type'] = $request['site_type'];
        }
        
        if($request->has('site_category')) {
            $siteKeys = $this->admin->getSiteCategoryKeys();
            $filters['site_category'] = isset($siteKeys[$request['site_category']]) ? $siteKeys[$request['site_category']] : $request['site_category'];
        }

        if($request->has('state_id')) {
            $filters['state_id'] = $request['state_id'];
        }

        if($request->has('partner')) {
            $filters['partner'] = $request['partner'];
        }

        $filters['not_site_category'] = 'covid,mobile';

        // Site Filters
        $this->admin->setFilters($filters);

        $sites = $this->admin->getSites();
        // $sites = $sites->filter(function($value, $key) {
        //     return !in_array($value->site_category, ["covid", "mobile"]); 
        // });
        // $sites = $sites->whereNotIn('site_category', ["covid", "mobile"]);
    //    $sites = $sites->all();

        $siteCategories = $this->admin->getSiteCategoryKeys();
        $siteCategories = array_flip($siteCategories);

        if(!empty($sites)) {
            foreach($sites as &$site) {
                if(isset($siteCategories[$site->site_category])) {
                   $site->site_category = $siteCategories[$site->site_category];
                //    unset($site->site_category);
                }
            }
        }
        

        return response()->json( $sites );

    }


}

?>