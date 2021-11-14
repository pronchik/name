<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PostsController;
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
    return $request->user();
});
Route::post('/login',[AuthController::class,'login']);
Route::get('/products',[ProductController::class,'index']);
//protected routs
Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::get('/users',[UserController::class,'index']);
    Route::get('/users/{id}',[UserController::class,'show']);
    Route::get('/my',[UserController::class,'myData']);
    Route::post('/logout',[AuthController::class,'logout']);


    Route::post('/create',[ProductController::class,'create']);
    Route::get('/myProducts',[ProductController::class,'myProducts']);
    Route::post('/buyProduct/{id}',[ProductController::class,'buyProduct']);
    Route::get('/products/{id}',[ProductController::class,'show']);

});

Route::apiResources([
    'posts' => PostsController::class,
]);
//register
Route::post('/register', [AuthController::class, 'register']);

