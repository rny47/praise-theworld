<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\AppController;
use Modules\Admin\Http\Middleware\CorsMiddleware;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('{pattern}', [AppController::class, 'downApk'])
    ->middleware(CorsMiddleware::class)
    ->where('pattern', '^[a-zA-Z0-9]+$')
    ->fallback();

Route::get('/d/{pattern}', [AppController::class, 'getDownUrl'])
    ->middleware(CorsMiddleware::class)
    ->where('pattern', '^[a-zA-Z0-9]+$')
    ->fallback();
