<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Settings;
use App\Models\User;
use App\Models\History;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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


    public function validate(Request $request, array $rules, array $messages = [], array $attributes = []) //A felhasználó hitelesítéséért felelős metódus
    {
            Settings::where('setting_name', 'access_token')->where('setting_value', $request->access_token)->FirstOrFail(); //Ellenőrizzük az access_token-t
            //A megfelelő cardID-val rendelkező user kiválasztása
            $user = User::where('cardId', $request->uid)->first();
            //A kamerakép feltöltéséért felelős rész
            $picture = "";
            if($request->picture != null && $request != ""){
                $picture = base64_decode($request->picture); //Változóba írjuk a base64-es kódolású képet
            }
            if(Settings::where('setting_name', 'isEntryEnabled')->FirstOrFail()->setting_value){
                    if($user != null){ //Ezt majd lehet, hogy kevesebb ifből kéne megoldani
                        if($request->entry && !$user->isHere){ //Bemeneti próbálkozás esetén, ha a felhasználó nincs itt
                            if(($user->validationMethod == 'fingerprint' || $user->validationMethod == 'both') && (($request->fingerprint != $user->fingerprint) || ($request->fingerprint == null || $request->fingerprint == ""))){
                                $history = History::create(['cardId' => $user->cardId, 'userId' => $user->id, 'direction' => 'in', 'successful' => false, 'arriveTime' =>  now(),  'workTime' => null, 'picture' => null]); //Mentünk a logba!
                                if($picture != ""){
                                    $filename = $history->id . '.jpg'; //Ez adja a fájl nevét
                                    $history->picture = $filename;
                                    Storage::disk('public')->put('pictures/log/'.$filename, $picture);
                                    $history->save();
                                }
                                if($user->fingerprint == "" || $user->fingerprint == null){ //Ha a felhasználót ujjlenyomattal akarjuk validálni, de az nem rendelkezik ujjlenyomat mintával
                                    Log::Warning("A ".$user->cardId." kártyaazonosítójú felhasználó validációs módja ujjlenyomat, de nem rendelkezik ujjlenyomat mintával!");
                                    return response()->json(['success' => false, 'message' => "A felhasználó nem rendelkezik ujjlenyomat mintával!"]);
                                }
                                return response()->json(['success' => false, 'message' => "Hibás ujjlenyomat!"]);
                                }
                            else if(($user->validationMethod == 'code' || $user->validationMethod == 'both') && (!(Hash::check($request->code, $user->code)) || ($request->code == null || $request->code == "" ))){
                                $history = History::create(['cardId' => $user->cardId, 'userId' => $user->id, 'direction' => 'in', 'successful' => false, 'arriveTime' =>  now(),  'workTime' => null, 'picture' => null]); //Mentünk a logba!
                                if($picture != ""){
                                    $filename = $history->id . '.jpg'; //Ez adja a fájl nevét
                                    $history->picture = $filename;
                                    Storage::disk('public')->put('pictures/log/'.$filename, $picture);
                                    $history->save();
                                }
                                if($user->code == "" || $user->code == null){ //Ha a felhasználót kóddal akarjuk validálni, de az nem kóddal
                                    Log::Warning("A ".$user->cardId." kártya azonosítójú felhasználó validációs módja kód, de nem rendelkezik kóddal!"); //Később majd a both esetre is gondolni kell a megfogalmazásban!
                                    return response()->json(['success' => false, 'message' => "A felhasználó nem rendelkezik kóddal!"]);
                                }
                                return response()->json(['success' => false, 'message' => "Hibás kód!"]);
                            }else{ //Abban az esetben, ha mind az ujjlenyomat, mind a kód megfelel, lehet hogy ezt később máshgyan kéne csinálni!
                                History::create(['cardId' => $user->cardId, 'userId' => $user->id, 'direction' => 'in', 'successful' => true, 'arriveTime' =>  now(),  'workTime' => null, 'picture' => null]); //Mentünk a logba!
                                $user->isHere = true;
                                $user->save(); //Mentjük az user objektumot
                                return response()->json(['success' => true, 'message' => "Sikeres beléptetés!"]);
                            }
                        }else if(!$request->entry && $user->isHere){ //Kimeneti próbálkozás esetén, ha a felhasználó itt van
                            $user->isHere = false;
                            $entry = History::where('direction', '=', 'in')->where('userId', '=', $user->id)->where('successful', '=', true)->orderBy('created_at', 'desc')->first();; //Lekérdezzük az adott felhaszbáló utolsó sikeres belépési kísérletét
                            $workTime = null;
                            if($entry != null){
                                $workTime = time() - strtotime($entry->arriveTime);
                            }
                            $user->save(); //Mentjük az user objektumot
                            History::where('cardId', $user->cardId)->where('successful', true)->latest()->firstOrFail()->update(['leaveTime' => now(), 'workTime' => $workTime]); //Frissítjük a távozás időpontját
                            return response()->json(['success' => true, 'message' => "Sikeres kiléptetés!"]);
                        }else{
                            $history = History::create(['cardId' => $user->cardId, 'userId' => $user->id, 'direction' => $request->entry ? 'in' : 'out', 'successful' => false, 'arriveTime' =>  now(),  'workTime' => null, 'picture' => null]);
                            if($picture != "" && $request->entry){
                                $filename = $history->id . '.jpg'; //Ez adja a fájl nevét
                                $history->picture = $filename;
                                Storage::disk('public')->put('pictures/log/'.$filename, $picture);
                                $history->save();
                            }
                            return response()->json(['success' => false, 'message' => "Sikertelen!"]);
                        }
                    }else{
                        $history = History::create(['cardId' => $request->uid, 'userId' => null, 'direction' => $request->entry ? 'in' : 'out', 'successful' => false, 'arriveTime' =>  now(),  'workTime' => null, 'picture' => null]);
                        if($picture != ""){
                            $filename = $history->id . '.jpg'; //Ez adja a fájl nevét
                            $history->picture = $filename;
                            Storage::disk('public')->put('pictures/log/'.$filename, $picture);
                            $history->save();
                        }
                        return response()->json(['success' => false, 'message' => "Nincs ilyen felhasználó!"]); //Ezt sem logoljuk egyenlőre
                    }
                    }else{
                return response()->json(['success' => false, 'message' => "Felhasználó nincs engedélyezve!"]); //Ezt sem logoljuk egyenlőre
            }
        return response()->json(['success' => false, 'message' => "Ismeretlen hiba!"]); //Ezt sem logoljuk egyenlőre
    }

    public function getMethods(Request $request, array $rules, array $messages = [], array $attributes = []){ //A hitelesítési módok lekéréséért felelős metódus
        Settings::where('setting_name', 'access_token')->where('setting_value', $request->access_token)->FindOrFail(1); //Ellenőrizzük az access_token-t
        $user = User::where('cardId', $request->uid)->firstOrFail(); //Lekérdezzük a felhasználót
        if($user->isEntryEnabled){
            if($user->validationMethod == 'code'){ //Amennyiben kóddal hitelesítjük a felhasználót
                return response()->json(['code' => true, 'fingerprint' => false, 'enabled' => $user->isEntryEnabled]);
            }else if($user->validationMethod == 'fingerprint'){ //Amennyiben ujjlenyomattal hitelesítjük a felhasználót
                return response()->json(['code' => false, 'fingerprint' => true, 'enabled' => $user->isEntryEnabled]);
            }else if($user->validationMethod == 'both'){ //Amennyiben mindkét módszerrel hitelesítjük a felhasználót
                return response()->json(['code' => true, 'fingerprint' => true, 'enabled' => $user->isEntryEnabled]);
            }else if(($user->validationMethod == 'none')){ //Amennyiben a kártyán kívül nem alkalmazunk további hitelesítést
                return response()->json(['code' => false, 'fingerprint' => false, 'enabled' => $user->isEntryEnabled]);
            }else{
                return response()->json(['code' => false, 'fingerprint' => false, 'enabled' => false]); //Amennyiben nincs érvényes érték, akkor ne engedjük be a felhasználót
            }
        }else{
            return response()->json(['code' => false, 'fingerprint' => false, 'enabled' => false]); //Ha nincsen engedélyezve a felhasználó
        }

    }
}
