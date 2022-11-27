<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\User;

class AuthController extends Controller
{

    public function checkUser(Request $request) {

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'role' => 'required'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
                'error' => $errors
            ], 200);
        }
        if ($validator->passes()) {
            $user = User::where(function($q) use($request) {
                $q->orWhere('username', $request['username'])
                    ->orWhere('email', $request['username']);
            })->first();

            if(empty($user)) {
                $errors = [
                    'errors' => [
                        'username' => 'User Does Not Exist'
                    ]
                ];
                return response()->json($errors, 200);
            }

            if(!$user->hasRole($request['role'])) {
                $errors = [
                    'errors' =>[ 
                        'role' => 'User Not associated with this Role'
			]
                ];
                return response()->json($errors, 200);
            }

            return response()->json([
                'success' => true
            ], 200);


        }

        return response()->json([
            'errors' => [
                'others' => [
                    'BAD Request'
                ]
            ]
        ], 200);


    }

    public function register(Request $request) {

         // Validate request data
         $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|min:10',
        ]);
        // Return errors if validation error occur.
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
                'error' => $errors
            ], 400);
        }
        // Check if validation pass then create user and auth token. Return the auth token
        if ($validator->passes()) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);
           
            
            $token = $user->createToken('auth_token')->accessToken;
        
            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
        }

    }

    public function me(Request $request)
    {
        return $request->user();
    }

}
