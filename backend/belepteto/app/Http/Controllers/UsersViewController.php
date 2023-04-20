<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\View\View;

class UsersViewController extends Controller
{
    public function showAllUsers(): View{
        return view('users', ['user' => User::all()]);
    }
}
