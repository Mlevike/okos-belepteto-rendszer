<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use Illuminate\Http\Request;
use App\Models\SystemSideOperations;

class SystemController extends Controller
{
    public function getCommand(Request $request){ //A rendszer által végrehajtandó feladat lekérdezésére szolgáló függvény
        Settings::where('setting_name', 'access_token')->where('setting_value', $request->access_token)->FindOrFail(1); //Ellenőrizzük az access_token-t
        $command = SystemSideOperations::where('operation_state', '=', 'created')->firstOrFail(); //Lekérdezzük a legelső végrehajtandó feladatot az adatbázisból
        $command->sent_time = date('Y-m-d H:i:s'); //Hozzárendelünk egy időbélyeget, mely a kiküldés időpontját rögzíti
        $command->operation_state = "sent"; //Átállítjuk a parancs állapotát elküldöttre állítjuk
        $command->save(); //Elmentjük az adatbázisban a változtatásokat
        return response()->json(["command" => $command->name, "options" => $command->options, "reference_token" => $command->reference_token]); //Elküldjük a kliens felé a választ
    }

    public function logCommandState(Request $request){ //A feladat sikerességének rögzítésére szolgáló metódus
        Settings::where('setting_name', 'access_token')->where('setting_value', $request->access_token)->FindOrFail(1); //Ellenőrizzük az access_token-t
        $command = SystemSideOperations::where('reference_token', '=', $request->reference_token)->firstOrFail(); //Lekérdezzük a legelső végrehajtandó feladatot az adatbázisból
        if($request->state == 'successful'){
            $command->operation_state = "successful"; //A parancs állapotát sikeresre állítjuk
        }else{
            $command->operation_state = "failed"; //A parancs állapotát sikertelenre  állítjuk, ezt majd lehet hogy később mődosítani kell
        }
        $command->save(); //Elmentjük az adatbázisban a változtatásokat
        return response("", 200); //Sikeres mentés esetén 200-as kódot adunk vissza
    }


}
