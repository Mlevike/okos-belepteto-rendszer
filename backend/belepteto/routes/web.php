<?php

use App\Models\User;
use App\Models\Settings;
use \App\Models\History;
use Illuminate\Support\Facades\Auth;
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
//Itt találhatóak a különböző nézetekhez tartozó útvonal definiciók

//Elsődleges útvonal
Route::get('', 'App\Http\Controllers\UsersViewController@index')->middleware('auth');

//Vezérlőpulthoz tartozó útvonal
Route::get('dashboard', function () {
    $current_user = Auth::user(); //Jelenleg bejelentkezett felhasználó adatainak lekérése
    if($current_user->role == 'admin') {
        $users = User::all(); //Lekérjük az összes felhasználót az adatbázisból
        //Deklaráljuk az itt levő felhasználókat megszámláló változókat
        $here = 0;
        $notHere = 0;
        $hash = '';
        $isEntryEnabled = Settings::all()->where('setting_name', 'isEntryEnabled')->first(); //Engedélyezve van-e beléptetés
        $isExitEnabled = Settings::all()->where('setting_name', 'isExitEnabled')->first(); //Engedélyezve van-e a kiléptetés

        foreach ($users as $user) { //Megszámoljuk az itt levő felhasználókat
            if ($user != null) {
                if ($user->isHere) {
                    $here++;
                } else {
                    $notHere++;
                }
            }
        }
        return view('dashboard', ['current_user' => $current_user, 'here' => $here, 'notHere' => $notHere, 'isEntryEnabled' => $isEntryEnabled, "isExitEnabled" => $isExitEnabled, "hash" => $hash]); //Ez lehet, hogy csak ideiglenes megooldás lesz
    }else{
        return view('error', [ 'errors' => "Nincs jogosultságod a kért művelet elvégzéséhez!", 'back_link' => route('users'), 'current_user' => $current_user]); //Ez majd lehet, hogy máshová irányít át később
    }
})->middleware('auth')->name('dashboard');

//A bejárat engedélyezésére szolgáló útvonal
Route::get('dashboard/setEntryEnabled', function () {
    $setting = Settings::all()->where('setting_name', 'isEntryEnabled')->first(); //Itt módosítjuk a isEntryEnabled beállítást!
    $setting->setting_value = !($setting->setting_value);
    $setting->save();
    return back();
})->middleware('auth')->name('set-entry-enabled');

//A kijárat engedélyezésére szolgáló útvonal
Route::get('dashboard/setExitEnabled', function () {
    $setting = Settings::all()->where('setting_name', 'isExitEnabled')->first(); //Itt módosítjuk a isExitEnabled beállítást!
    $setting->setting_value = !($setting->setting_value);
    $setting->save();
    return back();
})->middleware('auth')->name('set-exit-enabled');

//Az új token generálásához használható útvonal
Route::get('dashboard/generate-token', function (){
        $current_user = Auth::user(); //Jelenleg bejelentkezett felhasználó adatainak lekérése
        if ($current_user->role == 'admin') {
            $hash = hash('sha256', $plainTextToken = Str::random(40)); //Legeneráljunk a token-t
            if (Settings::all()->where('setting_name', 'access_token')->isEmpty()) {
                Settings::create(['setting_name' => 'access_token', 'setting_value' => '']);
            }
            $token = Settings::all()->where('setting_name', 'access_token')->first();
            $token->setting_value = $hash;
            $token->save(); //Elmentjük a token értékét az adatbázisba
            return view('token', ['current_user' => $current_user, "hash" => $hash]); //Ez lehet, hogy csak ideiglenes megooldás lesz
        }
    return redirect()->route('dashboard');//Átadjuk au új token-t a dashboardnak
})->middleware('auth')->name('generate-token');

//A logok oldalhoz tartozó útvonal
Route::get('logs', function () {
    $history = History::paginate(20, ['*'], 'history_page'); //20 elem látszódjon egyszerre
    $users = User::all();
    $logs = DB::table('log_messages')->paginate(20, ['*'], 'log_page'); //20 elem látszódjon egyszerre a rendszer szintő logokból
    $current_user = Auth::user(); //Jelenleg bejelentkezett felhasználó adatainak lekérése
    if($current_user->role == 'admin' or $current_user->role == 'employee') {
        return view('logs', ["history" => $history, 'logs' => $logs, "users" => $users, "current_user" => $current_user]);
    }else{
        return view('error', [ 'errors' => "Nincs jogosultságod a kért művelet elvégzéséhez!", 'back_link' => route('users')]); //Ez majd lehet, hogy máshová irányít át később
    }
})->middleware('auth')->name('logs');

//A felhasználók oldalhoz tartozó útvonalak

Route::get('users', 'App\Http\Controllers\UsersViewController@index')->middleware('auth')->name('users'); //A felhasználók listázásához vezető útvonal linkje

Route::get('users/add', 'App\Http\Controllers\UsersViewController@add')->middleware('auth')->name('users-add'); //Ezt majd később lehet egy sorba is írni!
Route::post('users/add', 'App\Http\Controllers\UsersViewController@add')->middleware('auth')->name('users-add');

Route::get('users/edit/{userId}', 'App\Http\Controllers\UsersViewController@edit')->middleware('auth')->name('users-edit'); //Ezt majd később lehet egy sorba is írni!
Route::post('users/edit/{userId}', 'App\Http\Controllers\UsersViewController@edit')->middleware('auth')->name('users-edit');

Route::get('users/show/{userId}', 'App\Http\Controllers\UsersViewController@show')->middleware('auth')->name('users-show'); //Az adott felhasználó adatainak megtekintésére szolgáló útvonal

Route::get('users/delete/{userId}', 'App\Http\Controllers\UsersViewController@delete')->middleware('auth')->name('users-delete'); //A felhasználók törléséhez vezető útvonal linkje

//A legutóbbi bejelentkezési kísérlet megjelenítésére szolgáló weboldal
Route::get('current', function(){
    $history = History::latest()->first();
    if($history != null and $history->userId != null){
    $user = User::where('id', $history->userId)->first();
    }else{
        $user = null;
    }
    return view('current', ['user'=>$user, 'history'=>$history]);
})->middleware('auth')->name('current'); //Az adott felhasználó adatainak megtekintésére szolgáló útvonal

//A kártya validációhoz tartozó útvonal ennek egyenéőre nem adunk nevet!
//Ez még csak ideiglenes, a végleges változatban majd az adatbázisból kéri le az információkat
Route::post('validate/{uid}', function(Request $request) {
    if(!(Settings::all()->where('setting_name', 'access_token')->isEmpty())) { //Ellenőrizzük az access_token meglétét
        $token = Settings::all()->where('setting_name', 'access_token')->first(); //Lekérjük az access_token értékét
        $isEntryEnabled = Settings::all()->where('setting_name', 'isEntryEnabled')->first(); //Lekérjük az isEntryEnabled értékét
        if ($request->has('access_token')) {
            if($request->access_token == $token->setting_value) {
                //Az uid beolvasása a kérésből
                $uid = Route::input('uid');
                //A megfelelő cardID-val rendelkező user kiválasztása
                $user = User::where('cardId', $uid)->first();
                if ($user == '' or $user == null) {
                    return response()->json(['code' => '', 'isHere' => '']);
                } else {
                    if ($user->isEntryEnabled && $isEntryEnabled->setting_value) { //Megnézzük, hogy a bejárat, illetve a felhasználó engedélyezve van-e?
                        return response()->json(['code' => $user->code, 'isHere' => $user->isHere]);
                    } else {
                        return response()->json(['code' => '', 'isHere' => '']);
                    }
                }
            }
        }
    }
});


//A belépési kísérletek logolására szolgáló útvonal, ezt majd lehet, hogy máshogy kell megcsinálni
Route::post('log', function (Request $request){
    if(!(Settings::all()->where('setting_name', 'access_token')->isEmpty())) { //Ellenőrizzük az access_token meglétét
        $token = Settings::all()->where('setting_name', 'access_token')->first(); //Lekérjük az access_token értékét
        if ($request->has('access_token')) {
            if($request->access_token == $token->setting_value) {
                if($request->has('successful') and $request->has('uid') and $request->has('entry')) {
                    $user = User::where('cardId', $request->uid)->first(); //Lekérjük a felhasználó azonosítóját kártya azonosító alapján
                    if($user != null){
                        if($request->entry ){
                            if($request->successful) {
                                User::where('cardId', $request->uid)->first()->update(['isHere' => true]);
                            }
                            History::create(['cardId' => $request->uid, 'userId' => $user == null ? null : $user->id, 'direction' => $request->entry ? 'in' : 'out', 'successful' => $request->successful, 'arriveTime' => $request->entry ? now() : null,  'workTime' => null]);
                        }
                            if(!($request->entry)){
                                if($request->successful) {
                                    History::where('cardId', $request->uid)->where('successful', true)->latest()->first()->update(['leaveTime' => now()]); //Elmentjük a távozás idejét
                                    User::where('cardId', $request->uid)->first()->update(['isHere' => false]); //Majd ki kell találni azt, hogy a sikertelen kilépéssel mi legyen??
            }
        }
                            }else{ //Egy ág arra az esetre, ha a felhasználó nem lenne regisztrálva
                                History::create(['cardId' => $request->uid, 'userId' => null, 'direction' => $request->entry ? 'in' : 'out', 'successful' => $request->successful, 'arriveTime' => $request->entry ? now() : null,  'workTime' => null]);
                            }
                    }
            }
        }
    }
});

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

