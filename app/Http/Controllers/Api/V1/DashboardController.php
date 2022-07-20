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
        $this->sitesFromProject = $this->project->getSites();

        return response()->json([
            'ehc' => $this->getIntersectedSitesByCategory('ehc'),
            'mehc' => 137,
            'covid' => $this->getIntersectedSitesByCategory('covid'),
            'vaccination' => $this->getIntersectedSitesByCategory('vaccination'),
            'tb' => $this->getIntersectedSitesByCategory('tb'),
            'coe' => $this->getIntersectedSitesByCategory('coe'),
            'dv' => $this->getIntersectedSitesByCategory('digital village'),
            'opd' => 5194196,
            'patients' => 2766699,
        ]);
    }

    private function getIntersectedSitesByCategory($category) {
        return  (isset($this->categoryBySites[$category]) && !empty($this->categoryBySites[$category])) ? $this->categoryBySites[$category]->pluck("siteid")->intersect($this->sitesFromProject)->count() : 0;
    }

}
