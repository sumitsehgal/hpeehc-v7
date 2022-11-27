<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Utils\Admin;
use App\Http\Utils\Project;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TimeLimitController extends Controller
{

    public function index() {
        return response()->json([
            'LW' => 'LW',
            'LM' => 'LM',
            '3M' => '3M',
            '6M' => '6M',
            'LY' => 'LY',
            'CY' => 'CY',
        ]);
    }

}

