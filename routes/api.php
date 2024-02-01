<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\UserController;
use \App\Http\Controllers\Api\ProductController;
use \App\Http\Middleware\auth;

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
Route::post('/register',[UserController::class,"registration"]);
Route::post('/login',[UserController::class,"login"]);
Route::post('/logout',[UserController::class,"logout"]);
Route::get('/users',[UserController::class,'getUsers']);
Route::get('/users/{id}',[UserController::class,'getUserById']);
Route::delete('/users/{id}', [UserController::class, 'deleteUser'])->middleware(auth::class);

Route::get('/products',[ProductController::class, "getProducts"]);
Route::get('/products/{product_id}' ,[ProductController::class, "getProductById"]);
Route::post('/add-product',[ProductController::class, "addProduct"])->middleware(auth::class);
Route::patch('/products/{id}',[ProductController::class,"editProduct"])->middleware(auth::class);
Route::delete('/products/{id}', [ProductController::class, "deleteProduct"])->middleware(auth::class);
