<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EhcController extends Controller
{
    public function patients() {

        return response()->json([
            'male' => 4523,
            'female' => 7896
        ]);
    }

    public function visitors() {
        return response()->json([
            'male' => 7923,
            'female' => 12896
        ]);
    }
}
