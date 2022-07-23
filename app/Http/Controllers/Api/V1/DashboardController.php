<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Utils\Admin;
use App\Http\Utils\Project;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public $admin, $project;

    public $categoryBySites, $sitesFromProject;

    public function __construct()
    {
        $this->admin = new Admin;
        $this->project = new Project;
    }

    public function index() {

        $this->categoryBySites = $this->admin->getSitesByCategory();
        $this->sitesFromProject = $this->project->getSites([]);

        return response()->json([
            'ehc' => $this->getIntersectedSitesByCategory('ehc'),
            'mehc' => $this->getMobileEhC(),
            'covid' => $this->getIntersectedSitesByCategory('covid'),
            'vaccination' => $this->getIntersectedSitesByCategory('vaccination'),
            'tb' => $this->getIntersectedSitesByCategory('tb'),
            'coe' => $this->getIntersectedSitesByCategory('coe'),
            'dv' => $this->getIntersectedSitesByCategory('digital village'),
            // 'opd' => 5194196,
            // 'patients' => 2766699,
        ]);
    }

    private function getIntersectedSitesByCategory($category) {
        return  (isset($this->categoryBySites[$category]) && !empty($this->categoryBySites[$category])) ? $this->categoryBySites[$category]->pluck("siteid")->intersect($this->sitesFromProject)->count() : 0;
    }

    public function getMobileEhC() {
        $databases = $this->project->getSiteDatabaseNames([]);
        $totalMobileEhc = 0;
        
        if(!empty($databases)) {
            foreach(array_chunk($databases, 500) as $dbs) {
                $query = [];
                foreach($dbs as $db) {
                    $userTbl = "{$db}.users";
                    $patientTbl = "{$db}.patient_data";

                    $query[] = " SELECT count({$userTbl}.id)  as cnt FROM {$userTbl}  WHERE {$userTbl}.user_type = 'MOBILE' AND {$userTbl}.username != 'admin' AND  (select count(*) from {$patientTbl}  where {$patientTbl}.user_id={$userTbl}.id)>0 ";

                }

                if(!empty($query)) {
                    $joinedQuery = implode(" UNION ALL ", $query);
                    $finalQuery = " SELECT SUM(cnt) as total FROM ( {$joinedQuery}  ) AS TBL ";
                    $results = DB::select($finalQuery);
                    if(!empty($results)) {
                        $totalMobileEhc += $results[0]->total;
                    }
                }


            }
        }

        return $totalMobileEhc;

    }

}
