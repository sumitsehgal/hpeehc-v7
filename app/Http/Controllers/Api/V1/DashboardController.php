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

        $sites = $this->admin->getSites()->pluck('siteid')->toArray();

        $this->categoryBySites = $this->admin->getSitesByCategory();
        $this->sitesFromProject = $this->project->getSites($sites);

        return response()->json([
            'eHC' => $this->getIntersectedSitesByCategory('ehc'),
            'MeHC' => $this->getMobileEhC($sites),
            'COVID' => $this->getIntersectedSitesByCategory('covid'),
            'Vaccination' => $this->getIntersectedSitesByCategory('vaccination'),
            'TB' => $this->getIntersectedSitesByCategory('tb'),
            'COE' => $this->getIntersectedSitesByCategory('coe'),
            'Digital Village' => $this->getIntersectedSitesByCategory('digital village'),
            // 'opd' => 5194196,
            // 'patients' => 2766699,
        ]);
    }

    private function getIntersectedSitesByCategory($category) {
        return  (isset($this->categoryBySites[$category]) && !empty($this->categoryBySites[$category])) ? $this->categoryBySites[$category]->pluck("siteid")->intersect($this->sitesFromProject)->count() : 0;
    }

    public function getMobileEhC($selectedSites) {
        
        $databases = $this->project->getSiteDatabaseNames($selectedSites);
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
