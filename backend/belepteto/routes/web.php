<?php

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


Route::get('/', function () {
    return view('dashboard');
})->middleware('auth');

Route::get('/logs', function () {
    return view('logs');
})->middleware('auth');

Route::get('/users', function () {
    return view('users');
})->middleware('auth');

Route::get('/validate/{uid}', function() {
    $uid = Route::input('uid');
    if($uid == '16722ba2') {
        return response()->json(['code' => '0000', 'isHere' => 'false']);
    }else {
        return response()->json(['code' => '', 'isHere' => '']);
    }
});
