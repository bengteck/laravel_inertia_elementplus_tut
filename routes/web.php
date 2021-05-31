<?php

use App\Activities\ImportActivity;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    Debugbar::info('Hello');
    \App\Models\User::whereIn('id',\App\Models\User::select('id')->where('name','like','Mr%'))->get();
    return view('welcome');
});

Route::get('process', function(ImportActivity $activity){
   
    $rows = $activity->importFromHistory();
    return view('listing', compact('rows'));
});
