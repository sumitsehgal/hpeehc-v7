<?php

namespace App\Http\Controllers\Api\V1;

use App\Asset;
use App\Studio;
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


    public function categorywise(Request $request) {

        $filters = [];
        $label = "Total Visits";
        if($request->has('site_type')) {
            $filters['site_type'] = $request['site_type'];
        }

        if($request->has('siteid')) {
            $filters['siteid'] = $request['siteid'];
        }
        
        if($request->has('site_category')) {
            $siteKeys = $this->admin->getSiteCategoryKeys();
            $filters['site_category'] = isset($siteKeys[$request['site_category']]) ? $siteKeys[$request['site_category']] : $request['site_category'];
        }else {
            $label = 'Total Population Served';
        }

        if($request->has('state_id')) {
            $filters['state_id'] = $request['state_id'];
        }

        if($request->has('partner')) {
            $filters['partner'] = $request['partner'];
        }

        $filters['isTimeLimit'] = false;
        if($request->has('timeLimit')) {
            if(!empty($request->timeLimit)) {
                $filters['isTimeLimit'] = true;
                switch($request->timeLimit) {
                    case 'LW':
                       $filters['start_date'] = date('Y-m-d 00:00:00', strtotime('first day of last week')); 
                       $filters['end_date'] = date('Y-m-d 23:59:59', strtotime('last day of last week')); 
                        break;
                    case 'LM':
                        $filters['start_date'] = date('Y-m-d 00:00:00', strtotime('first day of last month')); 
                        $filters['end_date'] = date('Y-m-d 23:59:59', strtotime('last day of last month')); 
                        break;

                    case '3M':
                        $filters['start_date'] = date('Y-m-d 00:00:00', strtotime('first day of -3 month')); 
                        $filters['end_date'] = date('Y-m-d 23:59:59', strtotime('last day of -1 month')); 
                        break;
                    case '6M':
                        $filters['start_date'] = date('Y-m-d 00:00:00', strtotime('first day of -6 month')); 
                        $filters['end_date'] = date('Y-m-d 23:59:59', strtotime('last day of -1 month')); 
                        break;
                    case '1Y':
                        $year = date('Y') - 1;
                        $filters['start_date'] = date('Y-m-d 00:00:00', strtotime('first day of January '.$year)); 
                        $filters['end_date'] = date('Y-m-d 23:59:59', strtotime('last day of December '.$year));

                        break;
                    case 'CY':
                        $year = date('Y');
                        $filters['start_date'] = date('Y-m-d 00:00:00', strtotime('first day of January '.$year)); 
                        $filters['end_date'] = date('Y-m-d 23:59:59', strtotime('last day of December '.$year));

                        break;
                    default:
                        $filters['isTimeLimit'] = false;
                        break;      
                }
            }

        }

        // Site Filters
        $this->admin->setFilters($filters);


        $sitesWithCategories = $this->admin->getSitesByCategory();
        $categoryBaseData = [];
        $assetCategories = Asset::$siteCategories;

        if($sitesWithCategories->isNotEmpty()) {
            foreach($sitesWithCategories as $siteCategory => $sitesCollection) {
                $siteIds = $sitesCollection->pluck('siteid')->toArray();
                $project = new Project();
                $dbs = $project->getSiteDatabaseNames($siteIds);

                $visitorCount = $this->getVisitorCount($filters, $siteIds, $dbs);

                $offset = 0;
                $teleConsultants = 0;
                if(!$request->has('site_category')) {
                    $csc = $this->admin->getCSC($siteIds);
                    $students = $this->admin->getStudents($siteIds);
                    $offset = $csc + $students;
                }

                $siteType = isset($assetCategories[$siteCategory]) ? $assetCategories[$siteCategory] : $siteCategory;
                if(!in_array($siteType, ["mobile"])) {
                    $categoryBaseData[$siteType] = [
                        $label => ($visitorCount['Male'] + $visitorCount['Female']) + $offset + $teleConsultants,
                        'Male' => $visitorCount['Male'],
                        'Female' => $visitorCount['Female'],
                        'offset' => $offset,
                        'siteids' => count($siteIds),
                        'TeleConsultants' => $teleConsultants
                    ];
                }

            }

        }

        // TODO: Need to Setup Studio
        $teleConsultants = (int) $this->admin->getStudio()->sum('stats'); 
        $categoryBaseData['Studio'] = [
            $label =>  $teleConsultants,
            'Male' => 0,
            'Female' => 0,
            'offset' => 0,
            'siteids' => 0,
            'TeleConsultants' => $teleConsultants
        ];


        return response()->json($categoryBaseData);




    }


    public function index(Request $request) {
        $filters = [];
        $label = "Total Visits";
        if($request->has('site_type')) {
            $filters['site_type'] = $request['site_type'];
        }

        if($request->has('siteid')) {
            $filters['siteid'] = $request['siteid'];
        }
        
        if($request->has('site_category')) {
            $siteKeys = $this->admin->getSiteCategoryKeys();
            $filters['site_category'] = isset($siteKeys[$request['site_category']]) ? $siteKeys[$request['site_category']] : $request['site_category'];
        }else {
            $label = 'Total Population Served';
        }

        if($request->has('state_id')) {
            $filters['state_id'] = $request['state_id'];
        }

        if($request->has('partner')) {
            $filters['partner'] = $request['partner'];
        }

        $filters['isTimeLimit'] = false;
        if($request->has('timeLimit')) {
            if(!empty($request->timeLimit)) {
                $filters['isTimeLimit'] = true;
                switch($request->timeLimit) {
                    case 'LW':
                       $filters['start_date'] = date('Y-m-d 00:00:00', strtotime('first day of last week')); 
                       $filters['end_date'] = date('Y-m-d 23:59:59', strtotime('last day of last week')); 
                        break;
                    case 'LM':
                        $filters['start_date'] = date('Y-m-d 00:00:00', strtotime('first day of last month')); 
                        $filters['end_date'] = date('Y-m-d 23:59:59', strtotime('last day of last month')); 
                        break;

                    case '3M':
                        $filters['start_date'] = date('Y-m-d 00:00:00', strtotime('first day of -3 month')); 
                        $filters['end_date'] = date('Y-m-d 23:59:59', strtotime('last day of -1 month')); 
                        break;
                    case '6M':
                        $filters['start_date'] = date('Y-m-d 00:00:00', strtotime('first day of -6 month')); 
                        $filters['end_date'] = date('Y-m-d 23:59:59', strtotime('last day of -1 month')); 
                        break;
                    case '1Y':
                        $year = date('Y') - 1;
                        $filters['start_date'] = date('Y-m-d 00:00:00', strtotime('first day of January '.$year)); 
                        $filters['end_date'] = date('Y-m-d 23:59:59', strtotime('last day of December '.$year));

                        break;
                    case 'CY':
                        $year = date('Y');
                        $filters['start_date'] = date('Y-m-d 00:00:00', strtotime('first day of January '.$year)); 
                        $filters['end_date'] = date('Y-m-d 23:59:59', strtotime('last day of December '.$year));

                        break;
                    default:
                        $filters['isTimeLimit'] = false;
                        break;      
                }
            }

        }

        // Site Filters
        $this->admin->setFilters($filters);

        $sites = $this->admin->getSites()->pluck('siteid')->toArray();

        $visitorCount = $this->getVisitorCount($filters, $sites);

        $offset = 0;
        if(!$request->has('site_category')) {
            $csc = $this->admin->getCSC($sites);
            $students = $this->admin->getStudents($sites);
            $offset = $csc + $students;
        }

        $studio = $this->admin->getStudio()->sum('stats');
       
        
        return response()->json([
            $label => ($visitorCount['Male'] + $visitorCount['Female']) + $offset + $studio,
            'Male' => $visitorCount['Male'],
            'Female' => $visitorCount['Female'],
            'offset' => $offset,
            'studio' => $studio

        ]);
    }

    public function getVisitorCount($filters, $selectedSites, $databases = []) {

        $filters = array_merge(
            array('start_date' => '1970-01-01 00:00:00', 'end_date' => Carbon::now()->format('Y-m-d 23:59:59') ),
            $filters
        );

        if(empty($databases)) {
            $databases = $this->project->getSiteDatabaseNames($selectedSites);
        }
        // echo count($databases);

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
                    if(isset($filters['isTimeLimit']) && $filters['isTimeLimit']) {
                        $whereQ = " WHERE {$formTbl}.`date` >= '{$filters['start_date']}' AND {$formTbl}.`date` <= '{$filters['end_date']}' ";
                    }
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
        $sitesStatus = $this->admin->getSitesStatus();

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
                    $siteStatus = (isset($sitesStatus[$site])) ? $sitesStatus[$site] : 1;

                    $mainQ = " SELECT count({$formTbl}.id) as cnt, {$patientTbl}.sex, '".$site."' AS site_id, '".$siteTitle."' AS site_title, '".$siteStatus."' AS site_status FROM {$patientTbl} ";

                    $joinQ = " INNER JOIN {$formTbl} ON {$patientTbl}.pid = {$formTbl}.pid ";

                    $whereQ = " ";
                    // $whereQ = " WHERE {$formTbl}.`date` >= '{$filters['start_date']}' AND {$formTbl}.`date` <= '{$filters['end_date']}' ";

                    $otherQ = " GROUP BY site_id, site_title, {$patientTbl}.sex ";

                    if( isset($filters['site_category']) && !empty($filters['site_category']) ) {

                        $userType = (strtolower($filters['site_category']) != "mobile") ? "EHC" : "MOBILE";
                        
                        $joinQ .= " LEFT JOIN {$usersTbl} ON {$usersTbl}.id = {$patientTbl}.user_id AND {$usersTbl}.user_type = '{$userType}'  ";

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
                             $response['sites'][$row->site_id]['siteStatus'] = $row->site_status;
 
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