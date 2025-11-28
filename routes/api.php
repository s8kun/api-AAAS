<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

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

// Handle all OPTIONS requests (CORS preflight)
Route::options('{any}', function () {
    return response('', 200);
})->where('any', '.*');

Route::get('/', [ProductController::class, 'index']);
Route::get('/{product}', [ProductController::class, 'show']);
Route::apiResource('products', ProductController::class);
