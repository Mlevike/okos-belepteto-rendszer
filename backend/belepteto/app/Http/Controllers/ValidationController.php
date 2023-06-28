<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Settings;

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
        if(!(Settings::all()->where('setting_name', 'access_token')->isEmpty())) { //Ellenőrizzük az access_token meglétét
            $token = Settings::all()->where('setting_name', 'access_token')->first(); //Lekérjük az access_token értékét
            $isEntryEnabled = Settings::all()->where('setting_name', 'isEntryEnabled')->first(); //Lekérjük az isEntryEnabled értékét
            if ($request->has('access_token')) {
                if($request->access_token == $token->setting_value) {
                    //Az uid beolvasása a kérésből
                    $uid = Route::input('uid');
                    //A megfelelő cardID-val rendelkező user kiválasztása
                    $user = User::where('cardId', $uid)->first();
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
            }
        }
    }
}
