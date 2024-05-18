<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GaragesController;
use App\Http\Controllers\PlacesController;
use App\Http\Controllers\BookingsController;




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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    $user = $request->user()
    $imageName = $user->user_img;
    $imageUrl = asset("images/$imageName"); 
    $user->user_img =$imageUrl;
    return Response()->json(['status'=>'success','data'=>['user'=> $user ]], 200 );
});
Route::post('register',[AuthController::class,'register']);
Route::post('login',[AuthController::class,'login']);

Route::group(['middleware'=>["auth:sanctum"]],function(){
    Route::post('/users/completeUserData',[UserController::class,'completeUserData']);
    Route::post('/users/update',[UserController::class,'update']);
    Route::post('/users/getImage',[UserController::class,'getImage']);

    //Route::resource('garages',GaragesController::class);
    Route::get('/garages', [GaragesController::class, 'index']);
    Route::get('/garages/show', [GaragesController::class, 'show']);
    Route::put('/garages/update', [GaragesController::class, 'update']);
    Route::delete('/garages/id={id}', [GaragesController::class, 'destroy']);
    Route::post('/garages', [GaragesController::class, 'store']);
    Route::get('/garages/getImage', [GaragesController::class, 'getImage']);
    Route::get('/garages/search', [GaragesController::class, 'search']);
    Route::get('/garages/rate', [GaragesController::class, 'rate']);

    //Route::resource('places',PlacesController::class);
    Route::get('/places', [PlacesController::class, 'index']);
    Route::get('/places/showByGarage', [PlacesController::class, 'showByGarage']);
    Route::get('/places/getNotBookedPlaces', [PlacesController::class, 'getNotBookedPlaces']);

    //Route::resource('booking',BookingsController::class);
    Route::get('/booking',[BookingsController::class,'index']);
    Route::post('/booking',[BookingsController::class,'store']);
    Route::put('/booking/updateToCompleted',[BookingsController::class,'updateToCompleted']);
    Route::put('/booking/updateToOccupied',[BookingsController::class,'updateToOccupied']);
    Route::put('/booking/cancel',[BookingsController::class,'destroy']);
    Route::get('/booking/showByStatus',[BookingsController::class,'showByStatus']);

    Route::post('/logout',[AuthController::class,'logout']);
});