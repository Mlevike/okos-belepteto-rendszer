<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\View\View;

class UsersViewController extends Controller
{
    public function index()
    {
        $users = User::all();

        return view('users')->with([
            'users' => $users
        ]);
    }

     public function add(){
         return view('users.add', ['user' => User::all()]);
     }
}
