<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Utils\Admin;
use App\Http\Utils\Project;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class VisitorController extends Controller
{

    public $admin, $project;

    public $categoryBySites, $sitesFromProject;

    public function __construct()
    {
        $this->admin = new Admin;
        $this->project = new Project;
    }


    public function index(Request $request) {
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

        $visitorCount = $this->getVisitorCount($filters, $sites);
        
        return response()->json([
            'Total Visits' => ($visitorCount['Male'] + $visitorCount['Female']),
            'male' => $visitorCount['Male'],
            'female' => $visitorCount['Female'],
        ]);
    }

    public function getVisitorCount($filters, $selectedSites) {

        $filters = array_merge(
            array('start_date' => '1970-01-01 00:00:00', 'end_date' => Carbon::now()->format('Y-m-d 23:59:59') ),
            $filters
        );

        $databases = $this->project->getSiteDatabaseNames($selectedSites);

        $response = [
            'Male' => 0,
            'Female' => 0
        ];

        if(!empty($databases)) {
            foreach(array_chunk($databases, 300) as $dbs) {
                $query = [];
                foreach($dbs as $db) {
                    $patientTbl = "{$db}.patient_data";
                    $formTbl = "{$db}.form_encounter";
                    $usersTbl =  "{$db}.users";

                    $mainQ = " SELECT count({$formTbl}.id) as cnt, {$patientTbl}.sex FROM {$patientTbl} ";

                    $joinQ = " INNER JOIN {$formTbl} ON {$patientTbl}.pid = {$formTbl}.pid ";

                    $whereQ = " ";
                    // $whereQ = " WHERE {$formTbl}.`date` >= '{$filters['start_date']}' AND {$formTbl}.`date` <= '{$filters['end_date']}' ";

                    $otherQ = " GROUP BY {$patientTbl}.sex ";

                    if( isset($filters['site_category']) && !empty($filters['site_category']) ) {

                        $userType = (strtolower($filters['site_category']) != "mobile") ? "EHC" : "MOBILE";
                        
                        $joinQ .= " INNER JOIN {$usersTbl} ON {$usersTbl}.id = {$patientTbl}.user_id AND {$usersTbl}.user_type = '{$userType}'  ";

                    }

                    $query[] = $mainQ.$joinQ.$whereQ.$otherQ;

                }

                if(!empty($query)) {
                    $joinedQuery = implode(" UNION ALL ", $query);
                    $finalQuery = " SELECT SUM(cnt) as total, sex FROM ( {$joinedQuery}  ) AS TBL GROUP BY sex ";
                    $results = DB::select($finalQuery);
                    if(!empty($results)) {
                       foreach($results as $row) {
                            if($row->sex == "Male") {
                                $response["Male"] += $row->total;
                            }else if($row->sex == "Female") {
                                $response["Female"] += $row->total;
                            }
                       }
                    }
                }
            }
        }

        return $response;

    }

    public function sitewise(Request $request) {
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

        $visitorCount = $this->getVisitorSitewise($filters, $sites);
        
        return response()->json($visitorCount);

    }

    public function getVisitorSitewise($filters, $selectedSites) {
        $filters = array_merge(
            array('start_date' => '1970-01-01 00:00:00', 'end_date' => Carbon::now()->format('Y-m-d 23:59:59') ),
            $filters
        );

        $databases = $this->project->getSiteDatabaseNames($selectedSites);
        $sitesTitle = $this->admin->getSitesTitle();

        $response = [
            'sites' => []
        ];

        if(!empty($databases)) {
            foreach(array_chunk($databases, 300, true) as $dbs) {
                $query = [];
                foreach($dbs as $site=>$db) {
                    $patientTbl = "{$db}.patient_data";
                    $formTbl = "{$db}.form_encounter";
                    $usersTbl =  "{$db}.users";

                    $siteTitle = (isset($sitesTitle[$site])) ? $sitesTitle[$site] : $site;

                    $mainQ = " SELECT count({$formTbl}.id) as cnt, {$patientTbl}.sex, '".$site."' AS site_id, '".$siteTitle."' AS site_title FROM {$patientTbl} ";

                    $joinQ = " INNER JOIN {$formTbl} ON {$patientTbl}.pid = {$formTbl}.pid ";

                    $whereQ = " ";
                    // $whereQ = " WHERE {$formTbl}.`date` >= '{$filters['start_date']}' AND {$formTbl}.`date` <= '{$filters['end_date']}' ";

                    $otherQ = " GROUP BY site_id, site_title, {$patientTbl}.sex ";

                    if( isset($filters['site_category']) && !empty($filters['site_category']) ) {

                        $userType = (strtolower($filters['site_category']) != "mobile") ? "EHC" : "MOBILE";
                        
                        $joinQ .= " INNER JOIN {$usersTbl} ON {$usersTbl}.id = {$patientTbl}.user_id AND {$usersTbl}.user_type = '{$userType}'  ";

                    }

                    $query[] = $mainQ.$joinQ.$whereQ.$otherQ;

                }

                if(!empty($query)) {
                    $joinedQuery = implode(" UNION ALL ", $query);
                    // $finalQuery = " SELECT SUM(cnt) as total, sex FROM ( {$joinedQuery}  ) AS TBL GROUP BY sex ";
                    $finalQuery = $joinedQuery;
                    $results = DB::select($finalQuery);
                    if(!empty($results)) {
                        foreach($results as $row) {
                             if(!isset($response['sites'][$row->site_id])) {
                                 $response['sites'][$row->site_id] = [
                                     'Male' => 0,
                                     'Female' => 0,
                                     'Total' => 0
                                 ];
                             }
                             $response['sites'][$row->site_id]['siteName'] = $row->site_title;
 
                             if($row->sex == "Male") {
                                 $response['sites'][$row->site_id]["Male"] = $row->cnt;
                                 $response['sites'][$row->site_id]["Total"] += $row->cnt;
                             }else if($row->sex == "Female") {
                                 $response['sites'][$row->site_id]["Female"] = $row->cnt;
                                 $response['sites'][$row->site_id]["Total"] += $row->cnt;
                             }
                        }
                     }
                }
            }
        }

        return $response;




    }

}