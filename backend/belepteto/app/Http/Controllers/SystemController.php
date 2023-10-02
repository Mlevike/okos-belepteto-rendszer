<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use Illuminate\Http\Request;
use App\Models\SystemSideOperations;

class SystemController extends Controller
{
    public function getCommand(Request $request){ //A rendszer által végrehajtandó feladat lekérdezésére szolgáló függvény
        Settings::where('setting_name', 'access_token')->where('setting_value', $request->access_token)->FindOrFail(1); //Ellenőrizzük az access_token-t
        $command = SystemSideOperations::where('operation_state', '=', 'created')->first(); //Lekérdezzük a legelső végrehajtandó feladatot az adatbázisból
        $command->sent_time = date('Y-m-d H:i:s'); //Hozzárendelünk egy időbélyeget, mely a kiküldés időpontját rögzíti
        $command->operation_state = "sent"; //Átállítjuk a parancs állapotát elküldöttre állítjuk
        $command->save(); //Elmentjük az adatbázisban a változtatásokat
        return response()->json(["command" => $command->name, "options" => $command->options, "reference_token" => $command->reference_token]); //Elküldjük a kliens felé a választ
    }
}
