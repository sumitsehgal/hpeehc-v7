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


    public function index() {

        $visitorCount = $this->getVisitorCount([]);
        
        return response()->json([
            'total' => ($visitorCount['Male'] + $visitorCount['Female']),
            'male' => $visitorCount['Male'],
            'female' => $visitorCount['Female'],
        ]);
    }

    public function getVisitorCount($filters) {

        $filters = array_merge(
            array('start_date' => '1970-01-01 00:00:00', 'end_date' => Carbon::now()->format('Y-m-d 23:59:59') ),
            $filters
        );

        $databases = $this->project->getSiteDatabaseNames();

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
                    $query[] = " SELECT count({$formTbl}.id) as cnt, {$patientTbl}.sex FROM {$patientTbl} INNER JOIN {$formTbl} ON {$patientTbl}.pid = {$formTbl}.pid WHERE {$formTbl}.`date` >= '{$filters['start_date']}' AND {$formTbl}.`date` <= '{$filters['end_date']}' GROUP BY {$patientTbl}.sex ";

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