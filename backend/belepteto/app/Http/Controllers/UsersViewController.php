<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Image;

class UsersViewController extends Controller
{
    public function index()
    {
        $users = User::paginate(20); //Ez azért kell, hogy max. csak húsz találatot jelenítsünk meg egyszerre
        $users = $users;
        $current_user = Auth::user();

        return view('users')->with([
            'users' => $users,
            'current_user' => $current_user
        ]);
    }

     public function add(Request $request){
         $current_user = Auth::user();
         if($current_user->isAdmin) {
         if ($request->isMethod('GET')){
            return view('users.edit', [ 'errors' => "", 'user' => null, 'current_user'=> $current_user]);
        }
         if ($request->isMethod('POST'))
         {

                 if (($request->filled('name')) and ($request->filled('email')) and ($request->filled('password'))) {

                     /*if($request->hasFile('picture')){ //A profilkép feltöltésének kezelése
                         $filename = $user->id.'.'.$request->picture->extension(); //Ez adja a fájl nevét
                         //$img = Image::make($request->picture);
                         /*$img->resize(1024, null, function ($constraint){ //Majd valahogy a képek méretét jó lenne ha tudnánk állítani!!
                             $constraint->aspectRatio();
                         })->save(public_path('/pictures/profile/').$filename);
                         $request->picture->storeAs('pictures/profile',$filename,'public'); //Ennek a segítségével tároljuk el
                         $user->picture = $filename; //És végül ezzel frissítjük az adatbázist
                     }*/
                     if (User::where('email', '=', $request->email)->exists()) { //Ellenőrizzük azt, hogy ilyen email címmel létezik-e már felhasználó
                         return back()->withInput()->with('status', 'Ezzel az email címmel már létezik felhasználó!');
                     }
                     if($request->filled('isAdmin')) {
                         $request->isAdmin = true;
                     }else{
                         $request->isAdmin = false;
                     }
                     if($request->filled('isWebEnabled')) {
                         $request->isWebEnabled = true;
                     }else{
                         $request->isWebEnabled = false;
                     }
                     if($request->filled('isEntryEnabled')) {
                         $request->isEntryEnabled = true;
                     }else{
                         $request->isEntryEnabled = false;
                     }
                     if($request->filled('isEmployee')) {
                         $request->isEmployee = true;
                     }else{
                         $request->isEmployee = false;
                     }
                     $user = User::create(['name' => $request->name, 'email' => $request->email, 'email_verified_at' => now(), 'password' => Hash::make($request->password, ['memory' => 1024, 'time' => 2, 'threads' => 2,]), 'picture' => '', 'code' => ($request->code != null ? Hash::make($request->code, ['memory' => 1024, 'time' => 2, 'threads' => 2,]): ''), 'fingerprint' => $request->fingerprint != null ? $request->fingerprint : '', 'language' => $request->language != null ? $request->language : '', 'profile' => $request->profile != null ? $request->profile : '', 'isAdmin' => $request->isAdmin, 'isWebEnabled' => $request->isWebEnabled, 'isEntryEnabled' => $request->isEntryEnabled, 'isEmployee' => $request->isEmployee, 'isHere' => false, 'cardId' => $request->cardId != null ? $request->cardId : '',]);
                     //A profilkép beállítása
                     if($request->hasFile('picture')){ //A profilkép feltöltésének kezelése
                         $filename = $user->id.'.'.$request->picture->extension(); //Ez adja a fájl nevét
                         //$img = Image::make($request->picture);
                         /*$img->resize(1024, null, function ($constraint){ //Majd valahogy a képek méretét jó lenne ha tudnánk állítani!!
                             $constraint->aspectRatio();
                         })->save(public_path('/pictures/profile/').$filename);*/
                         $request->picture->storeAs('pictures/profile',$filename,'public'); //Ennek a segítségével tároljuk el
                         $user->picture = $filename;
                         $user->save(); //És végül ezzel frissítjük az adatbázist
                     }
                     return redirect(route('users'))->with('status', 'Felhasználó törölve!');
                 } else {
                     return back()->withInput()->with('status', 'A csillaggal jelölt mezők kitöltése kötelező!'); //A withInput nem elképzeléseim szerint működik valami miatt
                 }
             }
         }else{
             return view('error', [ 'user' => null, 'errors' => "Nincs jogosultságod a kért művelet elvégzéséhez!", 'back_link' => route('users'), 'current_user'=>$current_user]); //Ehhez miért kell külön view?
         }
     }

     public function edit(Request $request, string $userId){
        $current_user = Auth::user();
         if($current_user->isAdmin) {
         if ($request->isMethod('GET')){
             $user = User::findOrFail($request->userId);
             return view('users.edit', [ 'errors' => "", 'user' => $user, 'current_user'=>$current_user]);
         }
         if ($request->isMethod('POST'))
         {
             $user = User::findOrFail($request->userId); //Felhasználó módosítás, ezt majd lehet, hogy egyszerűbben is mbeg lehet oldani! Illetve, most még hiányos is!
             if(($request->filled('name')) and ($request->filled('email'))){
                 if($request->filled('name')) { //Az if-ek azért szükségesek, hogy meggyőződjunk affelől, hogy a lekérésben szerepel az általunk változtatni kívánt attribútum!
                     $user->name = $request->name;
                 }
                 if($request->filled('email')) {
                     $user->email = $request->email;
                 }
                 if($request->filled('fingerprint')) {
                     $user->fingerprint = $request->fingerprint;
                 }
                 if($request->filled('language')) {
                     $user->language = $request->language;
                 }
                 if($request->hasFile('picture')){ //A profilkép feltöltésének kezelése
                     $filename = $user->id.'.'.$request->picture->extension(); //Ez adja a fájl nevét
                     //$img = Image::make($request->picture);
                     /*$img->resize(1024, null, function ($constraint){ //Majd valahogy a képek méretét jó lenne ha tudnánk állítani!!
                         $constraint->aspectRatio();
                     })->save(public_path('/pictures/profile/').$filename);*/
                     $request->picture->storeAs('pictures/profile',$filename,'public'); //Ennek a segítségével tároljuk el
                     $user->picture = $filename; //És végül ezzel frissítjük az adatbázist
                 }
                 if($request->filled('isAdmin')) {

                         $user->isAdmin = true;
                 }else{
                         $user->isAdmin = false;
                 }
                 if($request->filled('isWebEnabled')) {
                     $user->isWebEnabled = true;
                 }else{
                     $user->isWebEnabled = false;
                 }
                 if($request->filled('isEntryEnabled')) {
                     $user->isEntryEnabled = true;
                 }else{
                     $user->isEntryEnabled = false;
                 }
                 if($request->filled('isEmployee')) {
                     $user->isEmployee = true;
                 }else{
                     $user->isEmployee = false;
                 }
                 if($request->filled('cardId')) {
                     $user->cardId = $request->cardId;
                 }
                 if($request->filled('code')) {
                     $user->code = Hash::make($request->code, ['memory' => 1024, 'time' => 2, 'threads' => 2,]);
                 }
                 $user->save();
                 return redirect(route('users'))->with('status', 'Felhasználó módosítva!');
             }else{
                 return view('users.edit', ['user' => $user, 'errors' => "A csillagal jelölt mezők kitöltése kötelező!", 'current_user'=>$current_user]);
             }

         }
         }else{
             return view('error', [ 'errors' => "Nincs jogosultságod a kért művelet elvégzéséhez!", 'back_link' => route('users'), 'current_user'=>$current_user]);
         }
     }

    public function delete(Request $request){
        $current_user = Auth::user();
        if($current_user->isAdmin) {
        //Felhasználó törlése, ezt majd lehet hogy rövidebben kéne megvalósítani!
        $user = User::findOrFail($request->userId);
        $user->name = "deleted_user_" . (string)$user->id;
        $user->email = "deleted_user_" . (string)$user->id;
        $user->password = 0;
        $user->picture = 0;
        $user->code = 0;
        $user->fingerprint = 0;
        $user->language = 0;
        $user->profile = "deleted_user_" . (string)$user->id;
        $user->isAdmin = 0;
        $user->isWebEnabled = 0;
        $user->isEntryEnabled = 0;
        $user->isEmployee = 0;
        $user->cardId = 0;
        $user->save();
        User::find($request->userId)->delete();
        return redirect(route('users'))->with('status', 'Felhasználó törölve!');
        }else{
            return view('error', [ 'errors' => "Nincs jogosultságod a kért művelet elvégzéséhez!", 'back_link' => route('users'), 'current_user'=>$current_user]);
        }
    }
    public function show(Request $request)
    {
        $current_user = Auth::user();
        if ($current_user->isAdmin or $current_user->isEmployee or $current_user->id == $request->userId) {
            if ($request->isMethod('GET')) {
                return view('users.show', ['user' => User::findOrFail($request->userId), 'current_user' => $current_user]);
            } else {
                return view('error', ['errors' => "Nincs jogosultságod a kért művelet elvégzéséhez!", 'back_link' => route('users'), 'current_user' => $current_user]);
            }
        }
    }
}
