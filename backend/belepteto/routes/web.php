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
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::get('/hello', function () {
    return view('hello');
});

/*Route::get('/validate', function () {
   /* if($id == '16722ba2'){
        return response()->json('uid', '0000');
    }else{
        return response()->json('uid', '');
    }
    //return view('hello');
    header("Content-type:application/json");
    return json_encode(array("code" => "0000", "isHere" => true));
}); */
Route::get('/validate/{uid}', function() {
    $uid = Route::input('uid');
    if($uid == '16722ba2') {
        return response()->json(['code' => '0000', 'isHere' => 'false']);
    }else {
        return response()->json(['code' => '', 'isHere' => '']);
    }
});
