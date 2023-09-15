<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Settings;
use App\Models\User;
use App\Models\History;

class ValidationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function validate(Request $request, array $rules, array $messages = [], array $attributes = [])
    {
            Settings::where('setting_name', 'access_token')->where('setting_value', $request->access_token)->FindOrFail(1); //Ellenőrizzük az access_token-t
            //A megfelelő cardID-val rendelkező user kiválasztása
            $user = User::where('cardId', $request->uid)->first();
            if(Settings::where('setting_name', 'isEntryEnabled')->first()->setting_value){
                    if($user != null){ //Ezt majd lehet, hogy kevesebb ifből kéne megoldani
                        if($request->entry && !$user->isHere){ //Bemeneti próbálkozás esetén, ha a felhasználó nincs itt
                            if($request->filled('fingerprint') && ($request->fingerprint != $user->fingerprint)){
                                History::create(['cardId' => $user->uid, 'userId' => $user->id, 'direction' => 'in', 'successful' => false, 'arriveTime' =>  now(),  'workTime' => null]); //Mentünk a logba!
                                return response()->json(['success' => false, 'message' => "Hibás ujjlenyomat!"]);
                            }
                            if($request->filled('code') && ($request->code != $user->code)){
                                History::create(['cardId' => $user->cardId, 'userId' => $user->id, 'direction' => 'in', 'successful' => false, 'arriveTime' =>  now(),  'workTime' => null]); //Mentünk a logba!
                                return response()->json(['success' => false, 'message' => "Hibás kód!"]);
                            }else{ //Abban az esetben, ha mind az ujjlenyomat, mind a kód megfelel, lehet hogy ezt később máshgyan kéne csinálni!
                                History::create(['cardId' => $user->cardId, 'userId' => $user->id, 'direction' => 'in', 'successful' => true, 'arriveTime' =>  now(),  'workTime' => null]); //Mentünk a logba!
                                $user->isHere = true;
                                $user->save(); //Mentjük az user objektumot
                                return response()->json(['success' => true, 'message' => "Sikeres beléptetés!"]);
                            }
                        }else if(!$request->entry && $user->isHere){ //Kimeneti próbálkozás esetén, ha a felhasználó itt van
                            $user->isHere = false;
                            $user->save(); //Mentjük az user objektumot
                            History::where('cardId', $user->cardId)->where('successful', true)->latest()->firstOrFail()->update(['leaveTime' => now()]); //Frissítjük a távozás időpontját
                            return response()->json(['success' => true, 'message' => "Sikeres kiléptetés!"]);
                        }else{
                            return response()->json(['success' => false, 'message' => "Sikertelen!"]); //Ezt egyenlőre nem logoljuk!
                        }
                    }else{
                        History::create(['cardId' => $request->uid, 'userId' => null, 'direction' => $request->entry ? 'in' : 'out', 'successful' => false, 'arriveTime' =>  now(),  'workTime' => null]);
                        return response()->json(['success' => false, 'message' => "Nincs ilyen felhasználó!"]); //Ezt sem logoljuk egyenlőre
                    }
                    }
        return response()->json(['success' => false, 'message' => "Ismeretlen hiba!"]); //Ezt sem logoljuk egyenlőre
    }

    public function getMethods(Request $request, array $rules, array $messages = [], array $attributes = []){
        Settings::where('setting_name', 'access_token')->where('setting_value', $request->access_token)->FindOrFail(1); //Ellenőrizzük az access_token-t
        $user = User::where('cardId', $request->uid)->firstOrFail();
        return response()->json(['code' => !empty($user->code), 'fingerprint' => !empty($user->fingerprint), 'enabled' => !empty($user->isEntryEnabled)]);
    }
}
