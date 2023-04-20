<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

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

//Itt találhatóak a különböző nézetekhez tartozó útvonal definiciók

//Vezérlőpulthoz tartozó útvonal
Route::get('/', function () {
    return view('dashboard');
})->middleware('auth');

//A logok oldalhoz tartozó útvonal
Route::get('/logs', function () {
    return view('logs');
})->middleware('auth');

//A felhasználók oldalhoz tartozó útvonal
Route::get('/users', function () {
    //return view('users');
    return view('users', ['users' => User::all()]);
})->middleware('auth');

//A kártya validációhoz tartozó útvonal
//Ez még csak ideiglenes, a végleges változatban majd az adatbázisból kéri le az információkat
Route::get('/validate/{uid}', function() {
    $uid = Route::input('uid');
    if($uid == '16722ba2') {
        return response()->json(['code' => '0000', 'isHere' => 'false']);
    }else {
        return response()->json(['code' => '', 'isHere' => '']);
    }
});
