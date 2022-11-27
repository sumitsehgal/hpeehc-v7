<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Utils\Admin;
use App\Http\Utils\Project;

class DVController extends Controller
{
    public $admin, $project;

    public function __construct()
    {
        $this->admin = new Admin;
        $this->project = new Project;
    }

    public function getCSC(Request $request) {

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

        // Site Filters
        $this->admin->setFilters($filters);

        $sites = $this->admin->getSites()->pluck('siteid')->toArray();


        $totalCSC = $this->admin->getCSC($sites);
        

        return response()->json([
            'success' => true,
            'csc' => $totalCSC
        ]);
    }


    public function getStudents(Request $request) {

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

        // Site Filters
        $this->admin->setFilters($filters);

        $sites = $this->admin->getSites()->pluck('siteid')->toArray();


        $totalStudents = $this->admin->getStudents($sites);
        

        return response()->json([
            'success' => true,
            'students' => $totalStudents
        ]);
    }

}
