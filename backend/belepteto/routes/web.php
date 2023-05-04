<?php

use App\Models\User;
use App\Models\Settings;
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
    $users = User::all();
    return view('logs', ["history"=>$history, "users"=>$users]);
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
        if($user->isEntryEnabled) {
            return response()->json(['code' => $user->code, 'isHere' => 'false']);
        }else{
            return response()->json(['code' => '', 'isHere' => '']);
        }
    }

});

//A belépési kísérletek logolására szolgáló útvonal, ezt majd lehet, hogy máshogy kell megcsinálni
Route::get('log', function (Request $request){
    if($request->has('successful') and $request->has('uid') and $request->has('entry')) {
        $user = User::where('cardId', $request->uid)->first(); //Lekérjük a felhasználó azonosítóját kártya azonosító alapján
        History::create(['card_id' => $request->uid, 'user_id' => $user === null ? null : $user->id, 'direction' => $request->entry ? 'in' : 'out', 'successful' => $request->successful, 'arriveTime' => $request->entry ? now() : null, 'leaveTime' => $request->entry ? null : now(), 'workTime' => null]);
    }});

//A telepítésnél történő kártyabeolvasáshoz használt útvonal

Route::get('setup', function(Request $request){ //Egyemlőre még csak a kártyaazonosítóval működik
    if($request->has('fingerprint') or $request->has('cardId')){
        if(Settings::all()->where('setting_name', 'setup_cardId')->isEmpty()){
            Settings::create(['setting_name'=>'setup_cardId', 'setting_value'=>'']);
            $cardId = Settings::all()->where('setting_name', 'setup_cardId')->first();
            //return "Létrehozva, ".Settings::all()->where('setting_name', 'setup_cardId')->id;
            //Settings::all()->where('setting_name', 'setup_cardId')->setting_value = $request->cardId;
            $cardId->setting_value = $request->cardId;
            $cardId->save();

        }else{
            $cardId = Settings::all()->where('setting_name', 'setup_cardId')->first();
            $cardId->setting_value = $request->cardId;
            $cardId->save();
        }
    }
});

