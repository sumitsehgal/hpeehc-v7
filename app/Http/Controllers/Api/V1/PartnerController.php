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
            $filters['site_category'] = $request['site_category'];
        }

        $partners = $this->admin->getPartners($filters);
        return response()->json([
            'partners' => $partners
        ]);

    }

}