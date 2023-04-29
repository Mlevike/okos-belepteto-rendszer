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
']);             return redirect('/users')->with('status', 'Felhasználó törölve!');
             }else{
                 return view('users.add', ['user' => User::all(), 'errors' => "A csillagal jelölt mezők kitöltése kötelező!"]);
             }

         }
         //return view('users.add', ['user' => User::all(), 'error' => "Külső"]);
     }

     public function edit(Request $request){

         if ($request->isMethod('GET')){
             return view('users.edit', ['user' => User::all(), 'errors' => "", "userId" => $id]);
         }
         if ($request->isMethod('POST'))
         {
             if(($request->filled('name')) and ($request->filled('email')) and  ($request->filled('password'))){
                 User::create(['name'=> $request->name, 'email'=> $request->email, 'email_verified_at'=> now(), 'password'=> Hash::make($request->password, ['memory' => 1024, 'time' => 2, 'threads' => 2,]), 'picture'=>'', 'code'=>Hash::make('1111', ['memory' => 1024, 'time' => 2, 'threads' => 2,]), 'fingerprint'=>'', 'language'=>'en', 'profile'=>'Kártya2', 'isAdmin'=> false, 'isWebEnabled'=> false, 'isEntryEnabled'=> true, 'isEmployee'=> false, 'cardId' =>'724b41f
']);             return redirect('/users')->with('status', 'Felhasználó törölve!');
             }else{
                 return view('users.edit', ['user' => User::all(), 'errors' => "A csillagal jelölt mezők kitöltése kötelező!"]);
             }

         }
     }

    public function delete(Request $request){
        //Felhasználó törlése, ezt majd lehet hogy rövidebben kéne megvalósítani!
        $user = User::findOrFail($request->id);
        $user->name = "Törölt felhasználó";
        unset($user->email);
        unset($user->password);
        unset($user->picture);
        unset($user->code);
        unset($user->fingerprint);
        unset($user->language);
        unset($user->profile);
        unset($user->isAdmin);
        unset($user->isWebEnabled);
        unset($user->isEntryEnabled);
        unset($user->isEmployee);
        unset($user->cardId);
        $user->save();
        User::find($request->id)->delete();
        return redirect('/users')->with('status', 'Felhasználó törölve!');
    }
}
