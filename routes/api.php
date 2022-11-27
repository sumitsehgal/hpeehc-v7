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
    
    Route::post('/check-user', 'Api\V1\AuthController@checkUser');

    Route::namespace('Api\V1')->middleware('auth:api')->group(function () {
        Route::get('/me', 'AuthController@me');

        Route::group(['prefix'=>'ehc'], function(){
            Route::get('patients', 'EhcController@patients');
            Route::get('visitors', 'EhcController@visitors');
        });

        Route::get('dashboard', 'DashboardController@index');
        Route::get('patient/count', 'PatientController@index');
        Route::get('visitor/count', 'VisitorController@index');
        Route::get('patient/list', 'PatientController@sitewise');
        Route::get('visitor/list', 'VisitorController@sitewise');

        Route::get('visitor/categorywise', 'VisitorController@categorywise');
        
        Route::group(['prefix'=>'states'], function(){
            Route::get('list', 'StateController@index');
        });

        Route::group(['prefix'=>'sites'], function(){
            Route::get('list', 'SiteController@index');
            Route::get('maps', 'SiteController@longLat');
        });

        Route::group(['prefix'=>'partners'], function(){
            Route::get('list', 'PartnerController@index');
            Route::get('app-partners', 'PartnerController@appPartners');
        });

        Route::group(['prefix'=>'startups'], function(){
            Route::get('list', 'StartupController@index');
        });

        Route::group(['prefix'=>'studio'], function(){
            Route::get('list', 'StudioController@index');
        });

        Route::group(['prefix' => 'digital-village'], function() {
            Route::get('csc', 'DVController@getCSC'); 
            Route::get('students', 'DVController@getStudents'); 
        });

        Route::group(['prefix' => 'time-limit'], function() {
            Route::get('filters', 'TimeLimitController@index');
        });

    });

});

