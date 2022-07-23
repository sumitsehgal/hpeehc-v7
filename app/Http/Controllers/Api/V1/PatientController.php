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
            $filters['site_category'] = $request['site_category'];
        }

        // Site Filters
        $this->admin->setFilters($filters);

        $sites = $this->admin->getSites()->pluck('siteid')->toArray();

        $patientCount = $this->getPatientCount($filters, $sites);

        return response()->json([
            'total' => ($patientCount['Male'] + $patientCount['Female']),
            'male' => $patientCount['Male'],
            'female' => $patientCount['Female'],
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
                    
                    $whereQ = " WHERE {$patientTbl}.`date` >= '{$filters['start_date']}' AND {$patientTbl}.`date` <= '{$filters['end_date']}' ";

                    $otherQ = " GROUP BY {$patientTbl}.sex ";
                    
                    if( isset($filters['site_category']) && !empty($filters['site_category']) ) {
                        
                        $joinQ .= " INNER JOIN {$usersTbl} ON {$usersTbl}.id = {$patientTbl}.user_id AND {$usersTbl}.user_type = '{$filters['site_category']}'  ";

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


}