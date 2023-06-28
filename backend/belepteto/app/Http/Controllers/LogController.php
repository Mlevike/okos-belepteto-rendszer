<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\History;
use App\Models\User;

class LogController extends Controller
{
    public function index(){
        $history = History::paginate(20, ['*'], 'history_page'); //20 elem látszódjon egyszerre
        $users = User::all();
        $logs = DB::table('log_messages')->paginate(20, ['*'], 'log_page'); //20 elem látszódjon egyszerre a rendszer szintő logokból
        $current_user = Auth::user(); //Jelenleg bejelentkezett felhasználó adatainak lekérése
        if($current_user->role == 'admin' or $current_user->role == 'employee') {
            return view('logs', ["history" => $history, 'logs' => $logs, "users" => $users, "current_user" => $current_user]);
        }else{
            return view('error', [ 'errors' => "Nincs jogosultságod a kért művelet elvégzéséhez!", 'back_link' => route('users')]); //Ez majd lehet, hogy máshová irányít át később
        }
    }
}
