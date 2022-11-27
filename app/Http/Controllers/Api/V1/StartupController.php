<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Startup;

class StartupController extends Controller
{
    public function index(Request $request) {
        $builder = Startup::query();
        if($request->has("partner_id") && !empty($request->partner_id)) {
            $builder->where('partner_id', $request->partner_id);
        }

        $startups = $builder->pluck("name", "id");

        return response()->json($startups);
    }
}
