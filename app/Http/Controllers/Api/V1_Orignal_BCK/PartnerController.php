<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Utils\Admin;
use App\Http\Utils\Project;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PartnerController extends Controller
{

    public $admin, $project;

    public function __construct()
    {
        $this->admin = new Admin;
        $this->project = new Project;
    }

    public function index(Request $request) {

        $filters = [];

        if($request->has('site_category')) {
            $siteKeys = $this->admin->getSiteCategoryKeys();
            $filters['site_category'] = isset($siteKeys[$request['site_category']]) ? $siteKeys[$request['site_category']] : $request['site_category'];
        }
        if($request->has('state_id')) {
            $filters['state_id'] = $request['state_id'];
        }

        $this->admin->setFilters($filters);

        $partners = $this->admin->getPartners();
        return response()->json([
            'partners' => $partners
        ]);

    }

}