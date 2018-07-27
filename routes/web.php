<?php

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

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

Route::get('api_logs', function () {
    $list = \App\Models\ApiLog::orderByDesc('updated_at')->paginate(10);
    return view('logs', [
        'list' => $list
    ]);
});

Route::group(
    ['namespace'=>'Api'],
    function(){
        Route::get('download','WebController@download');
        Route::get('imagecache/neditor/{width}/{dir}/{filename}','WebController@imagecache');
    }
);





