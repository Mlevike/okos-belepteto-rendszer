<?php

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

//Egy útvonal arra a célra, hogy egy adott felhasználónál használható hitelesítési módot lekérdezzük
Route::post('validation/get-methods', 'App\Http\Controllers\ValidationController@getMethods')->name('get-validation-methods');

//A kártya validációhoz tartozó útvonal ennek egyenéőre nem adunk nevet!
Route::post('validation/validate', 'App\Http\Controllers\ValidationController@validate')->name('validate');

//A legutóbb bejelentkezett felhasználó pollingolására szolgáló útvonal
Route::get('poll/current', 'App\Http\Controllers\UsersViewController@currentAccess')->name('current-poll');

//A legutóbb bejelentkezett felhasználó pollingolására szolgáló útvonal
Route::get('poll/dashboard', 'App\Http\Controllers\DashboardController@pollDashboard')->name('poll-dashboard');

//Az használható ujjlenyomat helyek listázására szolgáló útvonal
Route::get('fp/get-usable-ids', function (Request $request){
        return response()->json(["ids" => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]]);
})->name('get-usable-ids'); //Ehhez majd új konténert kell létrehozni, a function csak ideiglenes jelleggel van ott

//A beléptető rendszer vezérléséért felelős útvonal
Route::post('poll/get-command', 'App\Http\Controllers\SystemController@getCommand')->name('get-command');

//A beléptető rendszer vezérlése végett elküldött parancsok sikerességének logolására szolgáló útvonal
Route::post('poll/log-command-state', 'App\Http\Controllers\SystemController@logCommandState')->name('log-command-state');
