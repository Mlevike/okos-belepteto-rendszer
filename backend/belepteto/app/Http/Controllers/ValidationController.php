<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Settings;
use App\Models\User;

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
            $isEntryEnabled = Settings::where('setting_name', 'isEntryEnabled')->first(); //Lekérjük az isEntryEnabled értékét
                    //A megfelelő cardID-val rendelkező user kiválasztása
                    $user = User::where('cardId', $request->uid)->first();
                    if ($user == '' or $user == null) {
                        return response()->json(['code' => '', 'isHere' => '']);
                    } else {
                        if ($user->isEntryEnabled && $isEntryEnabled->setting_value) { //Megnézzük, hogy a bejárat, illetve a felhasználó engedélyezve van-e?
                            return response()->json(['code' => $user->code, 'isHere' => $user->isHere]);
                        } else {
                            return response()->json(['code' => '', 'isHere' => '']);
                        }
                    }
    }

    public function getMethods(Request $request, array $rules, array $messages = [], array $attributes = []){
        Settings::where('setting_name', 'access_token')->where('setting_value', $request->access_token)->FindOrFail(1); //Ellenőrizzük az access_token-t
        $user = User::where('cardId', $request->uid)->findOrFail(1);
        return response()->json(['code' => !empty($user->code), 'fingerprint' => !empty($user->fingerprint), 'enabled' => !empty($user->isEntryEnabled)]);
    }
}
