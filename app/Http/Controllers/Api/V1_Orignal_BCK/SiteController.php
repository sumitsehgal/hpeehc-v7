<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Utils\Admin;
use App\Http\Utils\Project;

class SiteController extends Controller
{
    public $admin, $project;

    public function __construct()
    {
        $this->admin = new Admin;
        $this->project = new Project;
    }
    
    public function index() {
        $sites = $this->admin->getSites();

        return response()->json( $sites );


    }


}

?>