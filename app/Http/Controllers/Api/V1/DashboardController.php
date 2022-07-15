<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index() {

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
