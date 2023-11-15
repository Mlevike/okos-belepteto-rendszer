<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\History;
use Illuminate\Support\Facades\App;
use App\Models\User;
use Illuminate\Support\Facades\Log;


class LogController extends Controller
{
    public function index(){
        $current_user = Auth::user(); //Jelenleg bejelentkezett felhasználó adatainak lekérése
        App::setLocale($current_user->language); //Betöltjük a nyelvi beállítást
        //Az időt megfelelő formátumúra, a szótár segítségével átalakító függvény
        function ConvertTime($timeduration)
        {
            $workTime = ""; //Létrehozzuk a kimeneti változót
            //Kiszámoljuk mindegyik mennyiség értékét
            $days = floor($timeduration / 86400);
            $timeduration = $timeduration - ($days * 86400);
            $hours = floor($timeduration / 3600);
            $timeduration = $timeduration - ($hours * 3600);
            $minutes = floor($timeduration / 60);
            $seconds = $timeduration - ($minutes * 60);
            //Legeneráljuk a felhasználó
            if($days != 0){ // Ha a nap értéke nem nulla
                $workTime = $days." ".__('site.day')." ".$hours." ".__('site.hour')." ".$minutes.__('site.minute')." ".$seconds." ".__('site.second');
            }else if($hours != 0){ // Ha az óra értéke nem nulla
                $workTime = $hours." ".__('site.hour')." ".$minutes.__('site.minute')." ".$seconds." ".__('site.second');
            }else if($minutes != 0){ // Ha a perc értéke nem nulla
                $workTime = $minutes.__('site.minute')." ".$seconds." ".__('site.second');
            }else{ // Ha csak a másoderc értéke maradt már csak
                $workTime = $seconds." ".__('site.second');
            }
            return $workTime;
        }
        $history = History::select('users.id as usersId', 'users.name as usersName', 'history.*')->leftJoin('users', 'history.userId', '=', 'users.id')->paginate(20, ['*'], 'history_page'); //20 elem látszódjon egyszerre, a select az egyező oszlopnevek miatt kell!
        $logs = DB::table('log_messages')->paginate(20, ['*'], 'log_page'); //20 elem látszódjon egyszerre a rendszer szintő logokból
        foreach ($history as $current){
            if($current->workTime != null && $current->workTime != ""){
                $current->workTime = ConvertTime($current->workTime);
            }
        }
        if($current_user->role == 'admin' or $current_user->role == 'employee') {
            return view('logs', ["history" => $history, 'logs' => $logs, "current_user" => $current_user]);
        }else{
            return view('error', [ 'errors' => "Nincs jogosultságod a kért művelet elvégzéséhez!", 'back_link' => route('users')]); //Ez majd lehet, hogy máshová irányít át később
        }
    }
}
