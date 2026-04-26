<?php

use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ClothingController;
use App\Http\Controllers\OutfitController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\TagsController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*Авторизация*/
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);
Route::post('/verify-otp', [LoginController::class, 'verifyOtp']);
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth:sanctum');
/*Авторизация*/

Route::get('/tags', [TagController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/clothes', [ClothingController::class, 'index']);
    Route::post('/clothes', [ClothingController::class, 'store']);
    Route::get('/clothes/recent', [ClothingController::class, 'lastAdded']);
    Route::get('/clothes/{clothing}/outfits', [ClothingController::class, 'outfitsUsingClothing']);
    Route::delete('/clothes/{clothing}', [ClothingController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'profile']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/outfits', [OutfitController::class, 'index']);
    Route::post('/outfits', [OutfitController::class, 'store']);
    Route::delete('/outfits/{id}', [OutfitController::class, 'destroy']);
    Route::get('/outfits/clothing-categories', [OutfitController::class, 'getClothingByCategories']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/outfits/weather', [UserController::class, 'weatherOutfits']);
});

// Публичные образы
Route::get('/public-outfits', [OutfitController::class, 'publicIndex']);

// Лайки (middleware auth)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/outfits/{outfit}/like', [OutfitController::class, 'like']);
    Route::delete('/outfits/{outfit}/like', [OutfitController::class, 'unlike']);
    Route::get('/user-liked-outfits', [OutfitController::class, 'userLikedOutfits']);
});

Route::get('/outfits/{outfit}/comments', [OutfitController::class, 'getComments']);
Route::middleware('auth:sanctum')->post('/outfits/{outfit}/comments', [OutfitController::class, 'storeComment']);


Route::middleware(['auth:sanctum'])->prefix('admin')->group(function () {
    Route::get('tags', [TagsController::class, 'index']);
    Route::post('/tags', [TagsController::class, 'store']);
    Route::put('/tags/{tag}', [TagsController::class, 'update']);
    Route::delete('/tags/{tag}', [TagsController::class, 'destroy']);

    Route::get('users', [UsersController::class, 'index']);
    Route::put('/users/{user}', [UsersController::class, 'update']);
});
