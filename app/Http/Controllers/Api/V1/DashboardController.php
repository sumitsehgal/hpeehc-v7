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

    public function __construct()
    {
        $this->admin = new Admin;
        $this->project = new Project;
    }

    public function index() {

        $categoryBySites = $this->admin->getSitesByCategory();
        $sitesFromProject = $this->project->getSites();
        

        return response()->json([
            'ehc' => 139,
            'mehc' => 137,
            'covid' => 50,
            'vaccination' => 185,
            'tb' => 11,
            'coe' => 4,
            'dv' => 68,
            'opd' => 5194196,
            'patients' => 2766699,
        ]);
    }
}
