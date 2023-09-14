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
//Ez még csak ideiglenes, a végleges változatban majd az adatbázisból kéri le az információkat
Route::post('validation/validate', 'App\Http\Controllers\ValidationController@validate')->name('validate');

//A legutóbb bejelentkezett felhasználó pollingolására szolgáló útvonal
Route::get('poll/current', 'App\Http\Controllers\UsersViewController@currentAccess')->name('current-poll');
