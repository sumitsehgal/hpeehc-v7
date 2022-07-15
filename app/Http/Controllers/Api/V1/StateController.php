<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StateController extends Controller
{
    public function index() {

        return response()->json([
                              "1"=>"Andaman and Nicobar Islands",
                              "2"=>"Andhra Pradesh",
                              "3"=>"Arunachal Pradesh",
                              "4"=>"Assam",
                              "5"=>"Bihar",
                              "6"=>"Chandigarh",
                              "7"=>"Chhattisgarh",
                              "8"=>"Dadra and Nagar Haveli",
                              "9"=>"Daman and Diu",
                              "10"=>"Delhi",
                              "11"=>"Goa",
                              "12"=>"Gujarat",
                              "13"=>"Haryana",
                              "14"=>"Himachal Pradesh",
                              "15"=>"Jammu and Kashmir",
                              "16"=>"Jharkhand",
                              "17"=>"Karnataka",
                              "19"=>"Kerala",
                              "20"=>"Lakshadweep",
                              "21"=>"Madhya Pradesh",
                              "22"=>"Maharashtra",
                              "23"=>"Manipur",
                              "24"=>"Meghalaya",
                              "25"=>"Mizoram",
                              "26"=>"Nagaland",
                              "29"=>"Odisha",
                              "31"=>"Puducherry",
                              "32"=>"Punjab",
                              "33"=>"Rajasthan",
                              "34"=>"Sikkim",
                              "35"=>"Tamil Nadu",
                              "36"=>"Telangana",
                              "37"=>"Tripura",
                              "38"=>"Uttar Pradesh",
                              "39"=>"Uttarakhand",
                              "41"=>"West Bengal",
                              "4124"=>"Ladakh",     
                ]);
    }
}
