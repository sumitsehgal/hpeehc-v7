<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Utils\Admin;
use App\Http\Utils\Project;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PatientController extends Controller
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

        $patientCount = $this->getPatientCount($filters, $sites);

        return response()->json([
            'Total Patients' => ($patientCount['Male'] + $patientCount['Female']),
            'male' => $patientCount['Male'],
            'female' => $patientCount['Female']
        ]);
    }

    public function getPatientCount($filters, $sites) {

        $filters = array_merge(
            array('start_date' => '1970-01-01 00:00:00', 'end_date' => Carbon::now()->format('Y-m-d 23:59:59') ),
            $filters
        );
        

        $databases = $this->project->getSiteDatabaseNames($sites);

        $response = [
            'Male' => 0,
            'Female' => 0
        ];

        if(!empty($databases)) {
            foreach(array_chunk($databases, 500) as $dbs) {
                $query = [];
                foreach($dbs as $db) {
                    $patientTbl = "{$db}.patient_data";
                    $usersTbl =  "{$db}.users";

                    $mainQ = " SELECT count({$patientTbl}.id) as cnt, {$patientTbl}.sex FROM {$patientTbl} ";

                    $joinQ = "";
                    $whereQ = " ";
                    if(isset($filters['isTimeLimit']) && $filters['isTimeLimit']) {
                        $whereQ = " WHERE {$patientTbl}.`date` >= '{$filters['start_date']}' AND {$patientTbl}.`date` <= '{$filters['end_date']}' ";
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

        $patientCount = $this->getPatientSiteWise($filters, $sites);

        return response()->json($patientCount);


    }


    public function getPatientSiteWise($filters, $sites) {

        $filters = array_merge(
            array('start_date' => '1970-01-01 00:00:00', 'end_date' => Carbon::now()->format('Y-m-d 23:59:59') ),
            $filters
        );

        $databases = $this->project->getSiteDatabaseNames($sites);
        $sitesTitle = $this->admin->getSitesTitle();
        $sitesStatus = $this->admin->getSitesStatus();

        $response = [
            'sites' => []
        ];

        if(!empty($databases)) {
            foreach(array_chunk($databases, 100, true) as $dbs) {
                $query = [];
                foreach($dbs as $site=>$db) {
                    $patientTbl = "{$db}.patient_data";
                    $usersTbl =  "{$db}.users";

                    $siteTitle = (isset($sitesTitle[$site])) ? $sitesTitle[$site] : $site;
                    $siteStatus = (isset($sitesStatus[$site])) ? $sitesStatus[$site] : 1;

                    $mainQ = " SELECT count(*) as cnt, {$patientTbl}.sex, '".$site."' AS site_id, '".$siteTitle."' AS site_title, '".$siteStatus."' AS site_status FROM {$patientTbl} ";

                    $joinQ = "";
                    
                    // $whereQ = " WHERE {$patientTbl}.`date` >= '{$filters['start_date']}' AND {$patientTbl}.`date` <= '{$filters['end_date']}' ";

                    $whereQ = " ";

                    $otherQ = " GROUP BY site_id, site_title, {$patientTbl}.sex ";
                    
                    if( isset($filters['site_category']) && !empty($filters['site_category']) ) {

                        $userType = (strtolower($filters['site_category']) != "mobile") ? "EHC" : "MOBILE";
                        
                        $joinQ .= " INNER JOIN {$usersTbl} ON {$usersTbl}.id = {$patientTbl}.user_id AND {$usersTbl}.user_type = '{$userType}'  ";

                    }


                    $query[] = $mainQ.$joinQ.$whereQ.$otherQ;
                }

                if(!empty($query)) {
                    $joinedQuery = implode(" UNION ALL ", $query);
                    // $finalQuery = " SELECT SUM(cnt) as total, sex, site_id, site_title FROM ( {$joinedQuery}  ) AS TBL GROUP BY site_id, site_title, sex ";
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