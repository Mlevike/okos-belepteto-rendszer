<?php

use App\Models\User;
use App\Models\Settings;
use App\Models\History;
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
Route::get('dashboard', 'App\Http\Controllers\DashboardController@index')->middleware('auth')->name('dashboard');

//A bejárat engedélyezésére szolgáló útvonal
Route::get('dashboard/setEntryEnabled', 'App\Http\Controllers\DashboardController@setEntryEnabled')->middleware('auth')->name('set-entry-enabled');

//A kijárat engedélyezésére szolgáló útvonal
Route::get('dashboard/setExitEnabled', 'App\Http\Controllers\DashboardController@setExitEnabled')->middleware('auth')->name('set-exit-enabled');

//Az új token generálásához használható útvonal
Route::get('dashboard/generate-token', 'App\Http\Controllers\DashboardController@generateToken')->middleware('auth')->name('generate-token');

//Az ujjlenyomat regisztráció elindításért felelős útvonal
Route::get('dashboard/start-fingerprint-registration', 'App\Http\Controllers\DashboardController@startFingerprintRecord')->middleware('auth')->name('start-fp-registration');

//A logok oldalhoz tartozó útvonal
Route::get('logs', 'App\Http\Controllers\LogController@index')->middleware('auth')->name('logs');

//A felhasználók oldalhoz tartozó útvonalak

Route::get('users', 'App\Http\Controllers\UsersViewController@index')->middleware('auth')->name('users'); //A felhasználók listázásához vezető útvonal linkje

Route::get('users/add', 'App\Http\Controllers\UsersViewController@add')->middleware('auth')->name('users-add'); //Ezt majd később lehet egy sorba is írni!
Route::post('users/add', 'App\Http\Controllers\UsersViewController@add')->middleware('auth')->name('users-add');

Route::get('users/edit/{userId}', 'App\Http\Controllers\UsersViewController@edit')->middleware('auth')->name('users-edit'); //Ezt majd később lehet egy sorba is írni!
Route::post('users/edit/{userId}', 'App\Http\Controllers\UsersViewController@edit')->middleware('auth')->name('users-edit');

Route::get('users/show/{userId}', 'App\Http\Controllers\UsersViewController@show')->middleware('auth')->name('users-show'); //Az adott felhasználó adatainak megtekintésére szolgáló útvonal

Route::get('users/delete/{userId}', 'App\Http\Controllers\UsersViewController@delete')->middleware('auth')->name('users-delete'); //A felhasználók törléséhez vezető útvonal linkje

Route::get('users/set-dark-mode', 'App\Http\Controllers\UsersViewController@setDarkMode')->middleware('auth')->name('set-dark-mode'); //A sötét mód beállításához vezető útvonal linkje

//A legutóbbi bejelentkezési kísérlet megjelenítésére szolgáló weboldal
Route::get('current', function(){
    $current_user = Auth::user();
    return view('current', ['current_user' => $current_user]);
})->middleware('auth')->name('current');


/*//A belépési kísérletek logolására szolgáló útvonal, ezt majd lehet, hogy máshogy kell megcsinálni
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
});*/ //Ezt majd ha az új működik akkor törölni kell!

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

