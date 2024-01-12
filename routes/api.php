<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
// Route::get('/user',function(){
//     return "Hello Hassan";
// });

// Route::post('/user',function(){
//     return response()->json("Hello Mr. Hassn");
// });

// Route::delete('/user/{id}',function($id){
//     return response("Delete" . $id,200);
// });

// Route::put('/user/{id}',function($id){
//     return response("Put " .$id,200);
// });
// Route::get('/test',function(){
//         p("Working");
//     });
Route::post('user/store','App\Http\Controllers\Api\UserController@store');

Route::get('users/get/{flag}',[UserController::class,'index']);

Route::get('user/{id}',[UserController::class,'show']);

Route::delete('user/delete/{id}',[UserController::class,'destroy']);

// Route::get('users/{id?}',[UserController::class,'get']);

Route::put('user/{id}', [UserController::class, 'update']);
Route::patch('change-password/{id}', [UserController::class, 'changePassword']);
