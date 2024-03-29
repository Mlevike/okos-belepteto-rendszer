<?php

namespace App\Http\Controllers;

use App\Models\History;
use App\Models\User;
use App\Models\Settings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\SystemSideOperations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{

    public function index(Request $request)
    {
        $current_user = Auth::user(); //Jelenleg bejelentkezett felhasználó adatainak lekérése
        if ($current_user->role == 'admin') {
            $users = User::all(); //Lekérjük az összes felhasználót az adatbázisból
            //Definiáljuk az itt levő felhasználókat megszámláló változókat
            $here = 0;
            $notHere = 0;
            //Definiáljuk a felhasználók hitelesítésének módjait megszámláló változókat
            $validateWithCode = User::where('validationMethod', '=', 'code')->get()->count();
            $validateWithFingerprint = User::where('validationMethod', '=', 'fingerprint')->get()->count();
            $validateWithBoth = User::where('validationMethod', '=', 'both')->get()->count();
            $notValidate = User::where('validationMethod', '=', 'none')->get()->count();
            //Definiáljuk az objektumba történő nelépési jogosultsággal rendelkező felhasználókat egszámláló változókat
            $hasEntryPermission = User::where('isEntryEnabled', '=', true)->get()->count();
            $notHasEntryPermission = User::where('isEntryEnabled', '=', false)->get()->count();
            //Definiáljuk a különböző szerepkörű felhasználókat megszámláló változókat
            $userRole = User::where('role', '=', 'user')->get()->count();
            $employeeRole = User::where('role', '=', 'employee')->get()->count();
            $adminRole = User::where('role', '=', 'admin')->get()->count();

            $hash = '';
            $isEntryEnabled = Settings::all()->where('setting_name', 'isEntryEnabled')->first(); //Engedélyezve van-e beléptetés
            $isExitEnabled = Settings::all()->where('setting_name', 'isExitEnabled')->first(); //Engedélyezve van-e a kiléptetés
            $systemSideOperations = DB::table('system_side_operations')->paginate(10, ['*'], 'operation_page'); //20 elem látszódjon egyszerre a rendszer szintő műveletekből
            $usedFingerprintIDs = array(); //Létrehozunk egy tömböt a már használt ujjlenyomatazonosítrók tárolására
            foreach ($users as $user) { //Megszámoljuk az itt levő felhasználókat
                if ($user != null) {
                    if ($user->isHere) {
                        $here++;
                    } else {
                        $notHere++;
                    }
                }
                if ($user->fingerprint)
                    array_push($usedFingerprintIDs, $user->fingerprint);
            }
            return view('dashboard', ['current_user' => $current_user, 'here' => $here, 'notHere' => $notHere, 'isEntryEnabled' => $isEntryEnabled, "isExitEnabled" => $isExitEnabled, "hash" => $hash, "systemSideOperations" => $systemSideOperations, "usedFingerprintIDs" => $usedFingerprintIDs, 'validateWithCode' => $validateWithCode, 'validateWithFingerprint' => $validateWithFingerprint, 'validateWithBoth' => $validateWithBoth, 'notValidate' => $notValidate, 'hasEntryPermission' => $hasEntryPermission, 'notHasEntryPermission' => $notHasEntryPermission, 'userRole' => $userRole, 'employeeRole' => $employeeRole, 'adminRole' => $adminRole, 'current_page' => $systemSideOperations->currentPage()]); //Ez lehet, hogy csak ideiglenes megooldás lesz
        } else {
            return view('error', ['errors' => "Nincs jogosultságod a kért művelet elvégzéséhez!", 'back_link' => route('users'), 'current_user' => $current_user]); //Ez majd lehet, hogy máshová irányít át később
        }
    }

    public function setEntryEnabled(){
        $setting = Settings::all()->where('setting_name', 'isEntryEnabled')->first(); //Itt módosítjuk a isEntryEnabled beállítást!
        $setting->setting_value = !($setting->setting_value);
        $setting->save();
        return back();
    }

    public function setExitEnabled(){
        $setting = Settings::all()->where('setting_name', 'isExitEnabled')->first(); //Itt módosítjuk a isExitEnabled beállítást!
        $setting->setting_value = !($setting->setting_value);
        $setting->save();
        return back();
    }

    public function generateToken(){
        $current_user = Auth::user(); //Jelenleg bejelentkezett felhasználó adatainak lekérése
        if ($current_user->role == 'admin') {
            $hash = hash('sha256', $plainTextToken = Str::random(40)); //Legeneráljunk a token-t
            if (Settings::all()->where('setting_name', 'access_token')->isEmpty()) {
                Settings::create(['setting_name' => 'access_token', 'setting_value' => '']);
            }
            $token = Settings::all()->where('setting_name', 'access_token')->first();
            $token->setting_value = $hash;
            $token->save(); //Elmentjük a token értékét az adatbázisba
            return view('token', ['current_user' => $current_user, "hash" => $hash]); //Ez lehet, hogy csak ideiglenes megooldás lesz
        }
        return redirect()->route('dashboard');//Átadjuk au új token-t a dashboardnak
    }

    public function startFingerprintRecord(Request $request){ //Az ujjlenyomat felvételi folyamat elindításáért felelős metódus
        $current_user = Auth::user(); //Jelenleg bejelentkezett felhasználó adatainak lekérése
        if($request->fingerID >= 1 && $request->fingerID <= 127){ //Abban az esetben, ha az ujjlenyomat ID 1 és 127 közé esik, akkor elévgezzük a műveleteket
            SystemSideOperations::create(['name' => "register_fingerprint",'operation_state'  => "created", 'options' => json_encode(["id" => $request->fingerID]), "reference_token" => hash('sha256', $plainTextToken = Str::random(40)), 'timeout' => 300]); //Létrehozunk egy új adatbázis bejegyzést
            return redirect(route('dashboard'))->with('status', 'Folyamat elindítva'); //Visszairányítjuk a felhasználót a vezérlőpultra
        }else{ //Ha az ujjlenyomat ID nem felel meg a követelményeknek, akkor visszaadjuk a hibaoldalt!
            return view('error', ['errors' => "Nem megfelelő az ID!", 'back_link' => route('dashboard'), 'current_user' => $current_user]);
        }

    }

    public function cancelOperation(Request $request){ //Az elindított rendszerműveletek törlésséért felelős metódus
        $operation = SystemSideOperations::findOrFail($request->id); //Lekérdezzük, az törölni kívánt sort az adatbázisból
        if($operation->operation_state == 'created') { //Erre azért van szükség, hogy a már kiküldött műveleteket ne lehessen törölni!
            $operation->delete(); //Töröljük a sort
            return redirect(route('dashboard', ["operation_page" => $request->operation_page]))->with('status', 'Törlés sikeres!'); //Visszairányítjuk a felhasználót a vezérlőpultra
        }else{
            return redirect(route('dashboard'))->with('status', 'Törlés sikertelen!'); //Visszairányítjuk a felhasználót a vezérlőpultra
        }
    }

    public function pollDashboard(Request $request){
        $current_user = Auth::user();
        if ($current_user != null and $current_user->role == 'admin' ) {
            $users = User::all(); //Lekérjük az összes felhasználót az adatbázisból
            $here = 0;
            $notHere = 0;
            //Definiáljuk a felhasználók hitelesítésének módjait megszámláló változókat
            $validateWithCode = User::where('validationMethod', '=', 'code')->get()->count();
            $validateWithFingerprint = User::where('validationMethod', '=', 'fingerprint')->get()->count();
            $validateWithBoth = User::where('validationMethod', '=', 'both')->get()->count();
            $notValidate = User::where('validationMethod', '=', 'none')->get()->count();
            //Definiáljuk az objektumba történő nelépési jogosultsággal rendelkező felhasználókat egszámláló változókat
            $hasEntryPermission = User::where('isEntryEnabled', '=', true)->get()->count();
            $notHasEntryPermission = User::where('isEntryEnabled', '=', false)->get()->count();
            //Definiáljuk a különböző szerepkörű felhasználókat megszámláló változókat
            $userRole = User::where('role', '=', 'user')->get()->count();
            $employeeRole = User::where('role', '=', 'employee')->get()->count();
            $adminRole = User::where('role', '=', 'admin')->get()->count();
            foreach ($users as $user) { //Megszámoljuk az itt levő felhasználókat
                if ($user != null) {
                    if ($user->isHere) {
                        $here++;
                    } else {
                        $notHere++;
                    }
                }
            }
            return response()->json(['reload' => false, 'here' => $here, 'notHere' => $notHere, 'validateWithCode' => $validateWithCode, 'validateWithFingerprint' => $validateWithFingerprint, 'validateWithBoth' => $validateWithBoth, 'notValidate' => $notValidate, 'hasEntryPermission' => $hasEntryPermission, 'notHasEntryPermission' => $notHasEntryPermission, 'userRole' => $userRole, 'employeeRole' => $employeeRole, 'adminRole' => $adminRole]);
    }
        return response()->json([]);
    }
}
