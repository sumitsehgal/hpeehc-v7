<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['prefix'=> 'v1'], function() {
    
    // Route::post('/register', [AuthController::class, 'register']);
    // Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register','Api\V1\AuthController@register');
    
    

    Route::namespace('Api\V1')->middleware('auth:api')->group(function () {
        Route::get('/me', 'AuthController@me');

        Route::group(['prefix'=>'ehc'], function(){
            Route::get('patients', 'EhcController@patients');
            Route::get('visitors', 'EhcController@visitors');
        });

        Route::get('dashboard', 'DashboardController@index');
        
        Route::group(['prefix'=>'states'], function(){
            Route::get('list', 'StateController@index');
        });

    });

});

