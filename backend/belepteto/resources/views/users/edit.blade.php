<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!--Ebben a fájlban található a logok nézet sablonja.-->
<head>
    @include('head') <!--Be includoljuk a bootstrap hivatkozásokat és más könyvtárakat behivatkozó blade templatet-->
    <script>
        function ShowErrorMessage(message){ //A hibaüzenetek megjelenítéséért felelős függvény
            let errorMessage = document.getElementById("error-message"); //Létehozunk egy változót a div objekzum tárolására
            errorMessage.innerText = message; //Beállítjuk a hibaüzenet szövegét
            errorMessage.style.display = "block"; //Beállítjuk a hibaüzenet láthatóságát
            document.body.scrollTop = document.documentElement.scrollTop = 0; //Visszatekerjük az csúszkát az oldal tetejére, hogy biztosan láthassuk s hibaüzenetet!
        }

        function ValidateForm(){ //Az űrlap frontenden történő ellenőrzésére szolgáló függvény
            //Létrehozunk változókat a form mezőinek
            let password = document.getElementById("password").value;
            let password_again = document.getElementById("password_again").value;
            if(password !== password_again){ //Ha a megadott két jelszó nem egyezik meg
                ShowErrorMessage("{{ __('site.no_password_match') }}"); //Megjelenítjük a hibaüzenetet
            }else{ //Ha minden adat stimmelt a formon..
                document.getElementById("user-edit-form").submit(); //..akkor elküldjük a formot a szerverre
            }
        }
    </script>
</head>
<body {{$current_user->darkMode ? 'data-bs-theme=dark' : ''}}>
@include('header') <!--Be include-oljuk a menüt tartalmazó blade templatet -->
<div class="alert alert-danger mx-2" role="alert" id="error-message" style="display: none"> <!-- Hibák esetén megjelenő üzenet -->
</div>
<main>
    <h1 class="p-2">{{$user != null ?  __('site.editUser')  :  __('site.addUser') }}</h1>
    <div class="container-fluid">
        <!--A felhasználó szerkesztésére szolgáló form-->
        <form action="" method="post" enctype=multipart/form-data id="user-edit-form"> <!-- Létrehozunk egy formot a felhasználük adatainak szerkesztéséhez-->
            @csrf
            <div class="row">
                <div class="col-12 col-md-6">
                <label for="name">{{ __('site.name') }}:* </label>
                <input type="text" class="form-control" id="name" placeholder="Kis Géza" name="name" value="{{ $user != null ? $user->name : '' }}" required autofocus>
                </div>
                <div class="col-12 col-md-6">
                <label for="email">{{ __('auth.email') }}:* </label>
                <input type="email" class="form-control" id="floatingInput" placeholder="name@example.com" name="email" value="{{$user != null ? $user->email : ''}}" required>
            </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-6">
                    <label for="code">{{ __('site.code') }}: </label>
                    <input type="password" class="form-control" id="code" name="code" placeholder="****" pattern="[0-9]{4}"> <!--A pattern arra szplgál, hogy csak numerikus négy számjegyű kód legyen megadható, az számok mennyiségét lehet, hogy majd később át kell gondolni -->
                </div>
                <div class="col-12 col-md-6">
                    <label for="fingerprint">{{ __('site.fingerprint') }}: </label>
                    <input type="text" class="form-control" id="fingerprint" name="fingerprint" value="{{ $user != null ? $user->fingerprint : '' }}">
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-10">
                    <label for="profile">{{ __('site.profile') }}: </label>
                    <textarea type="text" class="form-control" id="profile" name="profile" style="height: 100px">{{$user != null ? $user->profile : ''}}</textarea>
                </div>
                <div class="col-12 col-md-2">
                    <label for="picture">{{ __('site.picture') }}: </label>
                    <input type="file" class="form-control" id="picture" name="picture">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="isEntryEnabledSwitch" name="isEntryEnabled" {{$user != null ? $user->isEntryEnabled  ? 'checked' : '' : ''}}>
                        <label class="form-check-label" for="isEntryEnabledSwitch">{{ __('site.isEntryEnabled') }}</label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="darkMode" name="darkMode" {{$user != null ? $user->darkMode  ? 'checked' : '' : ''}}>
                        <label class="form-check-label" for="darkModeSwitch">{{ __('site.darkMode') }}</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-6">
            <div class="col-12">
                <div class="row">
                    <div class="col-md-4">
                        <label for="language">{{ __('site.language') }}:* </label>
                        <br>
                        <select class="w-100" name="language" id="language" {{$user == null ? 'reguired' : ''}}>
                            <option value="" selected disabled hidden>{{ __('site.choseHere') }}</option> <!--Arra az estre ha nem akarunk nyelvet választani -->
                            <option value="en"> <!--Egenlőre az angol lesz az alapértelmezett nyelv-->
                                <p>English</p>
                            </option>
                            <option value="hu">
                                <p>Magyar</p>
                            </option>
                        </select>
                        <br> <!--Ez azért kell, hogy a nyelvválasztó elkülönüljön-->
                    </div>
                    <div class="col-md-4">
                        <label for="role">{{ __('site.role') }}:* </label>
                        <br>
                        <select class="w-100" name="role" id="role" {{$user == null ? 'reguired' : ''}}>
                            <option value="" selected disabled hidden>{{ __('site.choseHere') }}</option> <!--Arra az estre ha nem akarunk nyelvet választani -->
                            <option value="user"> <!--Egyenlőre az angol lesz az alapértelmezett nyelv-->
                                <p>User</p>
                            </option>
                            <option value="admin">
                                <p>Admin</p>
                            </option>
                            <option value="employee">
                                <p>Employee</p>
                            </option>
                        </select>
                        <br>
                    </div>
                    <div class="col-md-4">
                        <label for="validation-method">{{ __('site.validationMethod') }}:* </label>
                        <br>
                        <select class="w-100" name="validationMethod" id="validation-method" {{$user == null ? 'reguired' : ''}}>
                            <option value="" selected disabled hidden>{{ __('site.choseHere') }}</option> <!--Arra az estre ha nem akarunk nyelvet választani -->
                            <option value="code"> <!--Egyenlőre az angol lesz az alapértelmezett nyelv-->
                                <p>Code</p>
                            </option>
                            <option value="fingerprint">
                                <p>Fingerprint</p>
                            </option>
                            <option value="both">
                                <p>Both</p>
                            </option>
                            <option value="none">
                                <p>None</p>
                            </option>
                        </select>
                    </div>
                </div>
            <label for="cardId">{{ __('site.cardId') }}: </label>
            <input type="text" class="form-control" id="cardId" name="cardId" value="{{$user != null ? $user->cardId : ''}}">
            </div>
                    <div class="row">
                <div class="col-12 col-md-6">
                    <label for="password">{{ __('auth.password') }}:* </label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="********" {{$user == null ? 'reguired' : ''}}>
                </div>
                <div class="col-12 col-md-6">
                    <label for="password_again">{{ __('auth.password_again') }}:* </label>
                    <input type="password" class="form-control" id="password_again" name="password_again" placeholder="********" {{$user == null ? 'reguired' : ''}}>
                </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 gy-2">
                    <ul class="list-group ">
                        <li class="list-group-item bg-warning">{{ __('site.requirements_of_the_pw') }}</li>
                        <li class="list-group-item"><i class="bi bi-x-square-fill"></i> {{ __('site.pw_min_amount_of_letters') }}</li>
                        <li class="list-group-item"><i class="bi bi-x-square-fill"></i> {{ __('site.pw_small_and_capital_letters') }}</li>
                        <li class="list-group-item"><i class="bi bi-x-square-fill"></i> {{ __('site.pw_special_chars') }}</li>
                    </ul>
                </div>
            </div>
        </form>
    </div>
    <div class="p-2">
    <a type="button" class="btn btn-primary mt-2 mb-2" onclick="ValidateForm()" >{{$user != null ?  __('site.editUser')  :  __('site.addUser') }}</a>
    <a type="button" class="btn btn-danger mt-2 mb-2" href="javascript:history.back()" role="button">{{ __('site.cancel') }}</a> <!--Erre majd kell Laraveles megoldás is-->
    </div>
</main>
<script>
    @if(session('status') != null && session('status') != "") //A backendről értkeő hibák kijelzése, azért ide került, hogy az oldal betöltése után jöjjön be és így nem lesz hiba, mert nem találja azokat!
    ShowErrorMessage("{{ session('status') }}");
    @endif
</script>
</body>
@include('footer')
</html>
