<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;

class UsersViewController extends Controller
{
    public function index()
    {
        $users = User::All();
        $users = $users;

        return view('users')->with([
            'users' => $users
        ]);
    }

     public function add(Request $request){

         if ($request->isMethod('GET')){
            return view('users.add', ['user' => User::all(), 'errors' => ""]);
        }
         if ($request->isMethod('POST'))
         {
             if(($request->filled('name')) and ($request->filled('email')) and  ($request->filled('password'))){
                 User::create(['name'=> $request->name, 'email'=> $request->email, 'email_verified_at'=> now(), 'password'=> Hash::make($request->password, ['memory' => 1024, 'time' => 2, 'threads' => 2,]), 'picture'=>'', 'code'=>Hash::make('1111', ['memory' => 1024, 'time' => 2, 'threads' => 2,]), 'fingerprint'=>'', 'language'=>'en', 'profile'=>'Kártya2', 'isAdmin'=> false, 'isWebEnabled'=> false, 'isEntryEnabled'=> true, 'isEmployee'=> false, 'cardId' =>'724b41f
']);             return redirect(route('users'))->with('status', 'Felhasználó törölve!');
             }else{
                 return view('users.add', ['user' => User::all(), 'errors' => "A csillagal jelölt mezők kitöltése kötelező!"]);
             }

         }
         //return view('users.add', ['user' => User::all(), 'error' => "Külső"]);
     }

     public function edit(Request $request, string $userId){

         if ($request->isMethod('GET')){
             $user = User::findOrFail($request->userId);
             return view('users.edit', ['user' => User::all(), 'errors' => "", "user" => $user]);
         }
         if ($request->isMethod('POST'))
         {
             if(($request->filled('name')) and ($request->filled('email')) and  ($request->filled('password'))){
                 $user = User::findOrFail($request->userId); //Felhasználó módosítás, ezt majd lehet, hogy egyszerűbben is mbeg lehet oldani! Illetve, most még hiányos is!
                 if($request->filled('name')) { //Az if-ek azért szükségesek, hogy meggyőződjunk affelől, hogy a lekérésben szerepel az általunk változtatni kívánt attribútum!
                     $user->name = $request->name;
                 }
                 if($request->filled('email')) {
                     $user->email = $request->email;
                 }
                 if($request->filled('picture')) {
                     $user->picture = $request->picture;
                 }
                 if($request->filled('fingerprint')) {
                     $user->fingerprint = $request->fingerprint;
                 }
                 if($request->filled('language')) {
                     $user->language = $request->language;
                 }
                 if($request->filled('profile')) {
                     $user->profile = $request->profile;
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
                 return view('users.edit', ['user' => User::all(), 'errors' => "A csillagal jelölt mezők kitöltése kötelező!"]);
             }

         }
     }

    public function delete(Request $request){
        //Felhasználó törlése, ezt majd lehet hogy rövidebben kéne megvalósítani!
        $user = User::findOrFail($request->id);
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
        User::find($request->id)->delete();
        return redirect(route('users'))->with('status', 'Felhasználó törölve!');
    }
}
