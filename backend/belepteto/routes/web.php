<?php

use App\Models\User;
use \App\Models\History;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

use App\Http\Resources\ValidateResource;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


//A felhasználó nyelvi beállításának alkalmazása
//Ellenőrizzük azt, hogy a felhasználó authentikálva, van-e? Ezt is majd lehet, hogy egyszerűbben is meg lehet oldani!
$user = User::findOrFail(1); //Ez egyenlőre nem teljesen működőlépes
App::setLocale($user->language);


//Itt találhatóak a különböző nézetekhez tartozó útvonal definiciók

//Vezérlőpulthoz tartozó útvonal
Route::get('', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

//A logok oldalhoz tartozó útvonal
Route::get('logs', function () {
    $history = History::all();
    return view('logs', ["history"=>$history]);
})->middleware('auth')->name('logs');

//A felhasználók oldalhoz tartozó útvonalak

Route::get('users', 'App\Http\Controllers\UsersViewController@index')->middleware('auth')->name('users');
//Route::match(['get', 'post'], '/users/add', 'App\Http\Controllers\UsersViewController@add')->middleware('auth');

Route::get('users/add', 'App\Http\Controllers\UsersViewController@add')->middleware('auth')->name('users-add'); //Ezt majd később lehet egy sorba is írni!
Route::post('users/add', 'App\Http\Controllers\UsersViewController@add')->middleware('auth')->name('users-add');

Route::get('users/edit/{userId}', 'App\Http\Controllers\UsersViewController@edit')->middleware('auth')->name('users-edit'); //Ezt majd később lehet egy sorba is írni!
Route::post('users/edit/{userId}', 'App\Http\Controllers\UsersViewController@edit')->middleware('auth')->name('users-edit');

Route::match(['get', 'post'],'users/delete/', 'App\Http\Controllers\UsersViewController@delete')->middleware('auth')->name('users-delete');


//A kártya validációhoz tartozó útvonalm ennek egyenéőre nem adunk nevet!
//Ez még csak ideiglenes, a végleges változatban majd az adatbázisból kéri le az információkat
Route::get('validate/{uid}', function() {
    //Az uid beolvasása a kérésből
    $uid = Route::input('uid');
    //A megfelelő cardID-val rendelkező user kiválasztása
    $user = User::where('cardId', $uid)->first();
    if ($user == '' or $user == null){
        return response()->json(['code' => '', 'isHere' => '']);
    }else{
        return response()->json(['code' => $user->code, 'isHere' => 'false']);
    }

});

//A belépési kísérletek logolására szolgáló útvonal, ezt majd lehet, hogy máshogy kell megcsinálni
Route::get('log', function (Request $request){
    return response()->json(['code' => $request->has('successful'), 'isHere' => '']);
});

