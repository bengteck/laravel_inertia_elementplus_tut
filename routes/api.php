<?php
 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('extracted', function(Request $request){
    $records = $request->all();
    if( is_array($records) && isset($records[0]) ) {
        foreach ($records as $record) {
            \Log::info($record);
            \App\Models\ExtractedHistory::create($record);
        }
    } else {
        return response()->json('expect an array', 400);
    }
    return response()->json('success');
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
