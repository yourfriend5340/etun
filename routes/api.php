<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

//Route::apiResource('employees',EmployeeController::class);

//Route::post('login', 'LoginController@login'); //登入


Route::POST('/login', [App\Http\Controllers\Auth\AuthUserController::class, 'login']);

Route::middleware('auth:api')->group(function () {
//Route::group(['prefix' => 'v1', 'namespace' => 'Api\v1', 'middleware' => ['api','auth:api'] ], function () {
Route::POST('/logout', [App\Http\Controllers\Auth\AuthUserController::class, 'logout']);
Route::POST('/patrol', [App\Http\Controllers\PatrolRecordController::class, 'api_store']);
Route::POST('/patrolPIC', [App\Http\Controllers\PatrolRecordController::class, 'api_store_PIC']);
Route::POST('/punchin', [App\Http\Controllers\Auth\AuthUserController::class, 'api_PunchIn']);
Route::POST('/punchout', [App\Http\Controllers\Auth\AuthUserController::class, 'api_PunchOut']);
Route::POST('/schedule', [App\Http\Controllers\Auth\AuthUserController::class, 'api_Schedule']);
Route::POST('/leave', [App\Http\Controllers\TableController::class, 'leaveAPI']);
Route::POST('/resign', [App\Http\Controllers\TableController::class, 'resignAPI']);

});
