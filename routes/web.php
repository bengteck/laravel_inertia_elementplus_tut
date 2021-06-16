<?php

use App\Activities\ImportActivity;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

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
Route::get('/query', function(Request $request){
    $query = $request->get('type');
    $title = $request->get('title');
    $rows = \App\Models\Progress::select([
        'parent',
        \DB::raw('max(date) as "last date"'),
        \DB::raw('now() as "today"'),
        ])
        ->groupBy('parent')
        ->get();
    // $rows = [];
    return view('query', compact('rows','title'));
});
Route::get('/', function () {
    Debugbar::info('Hello');
    \App\Models\User::whereIn('id',\App\Models\User::select('id')->where('name','like','Mr%'))->get();
    return view('welcome');
});

Route::get('process', function(ImportActivity $activity){
    $rows = $activity->importFromHistory();
    return view('listing', compact('rows'));
});

Route::get('search', function(Request $request){
    $selection = $request->validate([
        'parent' => 'string',
        'from_date' => 'string',
        'to_date' => 'string',
    ]);
    $parents = \App\Models\ParentAcc::select('name')->pluck('name')->all();
    $dates = [
        'last' => \App\Models\Progress::selectRaw('max(date) as lastdate')->first()->lastdate
    ];

    $rows = [];
    if( count($selection) > 0 ) {
        $rows = \App\Models\Progress::select('date','kid_name','level','subject','type','unit','section','duration','score','link')
            ->where('parent',$selection['parent'])
            ->whereBetween('date',[$selection['from_date'], $selection['to_date']])
            ->orderBy('date')
            ->orderBy('kid_name')
            ->orderBy('subject')
            ->get();
    }

    // return view('search_listing', compact('parents','dates','rows','selection'))->withInput();
    return view('search_listing', compact('parents','dates','rows','selection'));
});
