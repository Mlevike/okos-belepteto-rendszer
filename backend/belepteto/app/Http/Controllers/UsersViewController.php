<?php

namespace App\Http\Controllers;

use App\Models\History;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Image;

//Ez a kontroller felel a felhasználók kezeléséért

class UsersViewController extends Controller
{
    public function index() //A felhasználók kilistázásáért felelős metódus
    {
        $users = User::paginate(20); //Ez azért kell, hogy max. csak húsz találatot jelenítsünk meg egyszerre
        $current_user = Auth::user(); //Lekérjük a jelenleg bejelentkezett felhasználó adatait

        return view('users')->with([ //Visszaadjuk a felhasználók kilistázására szolgáló nézetet
            'users' => $users,
            'current_user' => $current_user
        ]);
    }

     public function add(Request $request) //A felhasználó hozzáadásáért felelős metódus
     {
         $current_user = Auth::user(); //Lekérjük a jelenleg bejelentkezett felhasználó adatait
         if ($current_user->role == 'admin') { //Ha a jelenlegi felhasználó admin
             if ($request->isMethod('GET')) { //GET request esetén csak a formot küldjük vissza válaszként
                 return view('users.edit', ['errors' => "", 'user' => null, 'current_user' => $current_user]);
             }
             if ($request->isMethod('POST')) { //POST reguest esetén megpróbáljuk a felhasználó által adott információkat feldolgozni és hiba esetén értesítjük a felhasználót

                 if (($request->filled('name')) and ($request->filled('email')) and ($request->filled('password'))) {

                     if (User::where('email', '=', $request->email)->exists()) { //Ellenőrizzük azt, hogy ilyen email címmel létezik-e már felhasználó
                         return back()->withInput()->with('status', 'Ezzel az email címmel már létezik felhasználó!');
                     }

                     if ($request->filled('isEntryEnabled')) {
                         $request->isEntryEnabled = true;
                     } else {
                         $request->isEntryEnabled = false;
                     }

                     if ($request->filled('darkMode')) { //Sötét mód beállítása
                         $request->darkMode = true;
                     } else {
                         $request->darkMode = false;
                     }

                     if (!($request->filled('role'))) {
                         $request->role = 'user'; //Alapértelmezett role beállítás megadása
                     }

                     if (!($request->filled('language'))) {
                         $request->language = 'en'; //Alapértelmezett nyelv beállítás megadása
                     }

                     $user = User::create(['name' => $request->name, 'email' => $request->email, 'email_verified_at' => now(), 'password' => Hash::make($request->password, ['memory' => 1024, 'time' => 2, 'threads' => 2,]), 'picture' => '', 'code' => ($request->code != null ? Hash::make($request->code, ['memory' => 1024, 'time' => 2, 'threads' => 2,]) : ''), 'fingerprint' => $request->fingerprint != null ? $request->fingerprint : '', 'language' => $request->language != null ? $request->language : '', 'profile' => $request->profile != null ? $request->profile : '', 'isEntryEnabled' => $request->isEntryEnabled, 'role' => $request->role, 'isHere' => false, 'cardId' => $request->cardId != null ? $request->cardId : '',]);
                     //A profilkép beállítása
                     if ($request->hasFile('picture')) { //A profilkép feltöltésének kezelése
                         $filename = $user->id . '.' . $request->picture->extension(); //Ez adja a fájl nevét
                         $request->picture->storeAs('pictures/profile', $filename, 'public'); //Ennek a segítségével tároljuk el
                         $user->picture = $filename;
                         $user->save(); //És végül ezzel frissítjük az adatbázist
                     }
                     return redirect(route('users'));
                 } else {
                     return back()->withInput()->with('status', 'A csillaggal jelölt mezők kitöltése kötelező!');
                 }
             } else {
                 return view('error', ['user' => null, 'errors' => "Nincs jogosultságod a kért művelet elvégzéséhez!", 'back_link' => route('users'), 'current_user' => $current_user]);
             }
         }
     }

     public function edit(Request $request, string $userId) //A felhasználók szerkeztésére szolgáló metódus
     {
         $current_user = Auth::user(); //Lekérjük a jelenleg bejelentkezett felhasználó adatait
         if ($current_user->role == 'admin') { //Ha a jelenlegi felhasználó admin
             if ($request->isMethod('GET')) { //GET request esetén csak betöltjük a formot, illetve a jelenlegi adatokat
                 $user = User::findOrFail($request->userId);
                 return view('users.edit', ['errors' => "", 'user' => $user, 'current_user' => $current_user]);
             }
             if ($request->isMethod('POST')) { //POST reguest esetén fel is dolgozzuk a felhasználó által adott adatokat
                 $user = User::findOrFail($request->userId); //Felhasználó módosítás, ezt majd lehet, hogy egyszerűbben is mbeg lehet oldani! Illetve, most még hiányos is!
                 if (($request->filled('name')) and ($request->filled('email'))) {
                     if ($request->filled('name')) { //Az if-ek azért szükségesek, hogy meggyőződjunk affelől, hogy a lekérésben szerepel az általunk változtatni kívánt attribútum!
                         $user->name = $request->name;
                     }
                     if ($request->filled('email')) {
                         $user->email = $request->email;
                     }
                     if ($request->filled('fingerprint')) {
                         $user->fingerprint = $request->fingerprint;
                     }
                     if ($request->filled('language')) {
                         $user->language = $request->language;
                     }
                     if ($request->hasFile('picture')) { //A profilkép feltöltésének kezelése
                         $filename = $user->id . '.' . $request->picture->extension(); //Ez adja a fájl nevét
                         $request->picture->storeAs('pictures/profile', $filename, 'public'); //Ennek a segítségével tároljuk el
                         $user->picture = $filename; //És végül ezzel frissítjük az adatbázist
                     }
                     if ($request->filled('role')) { //A szerepkör mentéséért felelős rész
                         $user->role = $request->role;
                     }
                     if ($request->filled('isEntryEnabled')) {
                         $user->isEntryEnabled = true;
                     } else {
                         $user->isEntryEnabled = false;
                     }

                     if ($request->filled('darkMode')) { //Sötét mód beállítása
                         $user->darkMode = true;
                     } else {
                         $user->darkMode = false;
                     }

                     if ($request->filled('cardId')) {
                             $user->cardId = $request->cardId;
                     }
                     if ($request->filled('code')) {
                             $user->code = Hash::make($request->code, ['memory' => 1024, 'time' => 2, 'threads' => 2,]);
                     }
                     $user->save(); //Itt mentjük el az adatbázisban a módosításokat
                     return redirect(route('users'))->with('status', 'Felhasználó módosítva!');
                     } else {
                         return view('users.edit', ['user' => $user, 'errors' => "A csillagal jelölt mezők kitöltése kötelező!", 'current_user' => $current_user]);
                     }

                 }
             } else {
                 return view('error', ['errors' => "Nincs jogosultságod a kért művelet elvégzéséhez!", 'back_link' => route('users'), 'current_user' => $current_user]);
             }
         }

    public function delete(Request $request){ //A felhasználó törlésére szolgáló metódus
        $current_user = Auth::user();
        if($current_user->role == 'admin') { //Ha a jelenlegi felhasználó admin
        //Felhasználó törlése, azért kell nullázni az adatokat, hogy ne akadhasson öszze egy másik későbbi felhasználóval
        $user = User::findOrFail($request->userId);
        $user->name = "deleted_user_" . (string)$user->id;
        $user->email = "deleted_user_" . (string)$user->id;
        $user->password = 0;
        $user->picture = 0;
        $user->code = 0;
        $user->fingerprint = 0;
        $user->language = 0;
        $user->profile = "deleted_user_" . (string)$user->id;
        $user->role = null;
        $user->isEntryEnabled = 0;
        $user->cardId = 0;
        $user->isHere = false;
        $user->save();
        User::find($request->userId)->delete();
        return redirect(route('users'))->with('status', 'Felhasználó törölve!');
        }else{
            return view('error', [ 'errors' => "Nincs jogosultságod a kért művelet elvégzéséhez!", 'back_link' => route('users'), 'current_user'=>$current_user]);
        }
    }
    public function show(Request $request) //Felhasználó megtekintéséért felelős metódus
    {
        $current_user = Auth::user();
        if ($current_user->role == 'admin' or $current_user->role == 'employee' or $current_user->id == $request->userId) { //Ha a jelenlegi felhasználó admin, employee vagy a saját adatlapját szeretné megnézni
            if ($request->isMethod('GET')) {
                return view('users.show', ['user' => User::findOrFail($request->userId), 'current_user' => $current_user]);
            } else {
                return view('error', ['errors' => "Nincs jogosultságod a kért művelet elvégzéséhez!", 'back_link' => route('users'), 'current_user' => $current_user]);
            }
        }
    }

    public function setDarkMode(){ //A sötét mód állításáért felelős metódus
        $current_user = Auth::user();
        $current_user->darkMode = !($current_user->darkMode); //Dark mód beállítás átállítása a jelenlegi felhasználón
        $current_user->save(); //Elmentjük a változtatást
        return back();
    }

    public function currentAccess()
    {
        $history = History::latest()->first();
        if ($history != null and $history->userId != null) {
            return response()->json(['name' => $history->userId, 'cardID' => $history->cardId, ($history->successful == 1 ? __('site.success') : __('site.fail')), ($history->direction == "in" ? __('site.success') : __('site.fail')), 'successfulValue' => $history->successful, "directionValue" => $history->direction]);
        } else {
            return response()->json(['name' => "", 'cardID' => "", 'successful' => "", 'direction' => "", 'successfulValue' => null, "directionValue" => null]);
        }
    }
}
