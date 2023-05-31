<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!--Ebben a fájlban található a logok nézet sablonja.-->
<head>
@include('head') <!--Be includoljuk a bootstrap hivatkozásokat és más könyvtárakat behivatkozó blade templatet-->
</head>
<body>
@include('header') <!--Be include-oljuk a menüt tartalmazó blade templatet -->
<main class="p-2">
    <h1>{{$user != null ?  __('site.editUser')  :  __('site.addUser') }}</h1>
        <p>{{ session('status') }}</p>
        <!--A felhasználó szerkeztésére szolgáló form-->
        <form action="" method="post" enctype=multipart/form-data> <!-- Létrehozunk egy formot a felhasználük adatainak szerkesztéséhez-->
            @csrf
            <label for="name">{{ __('site.name') }}:* </label>
            <input type="text" class="form-control" id="name" placeholder="Kis Géza" name="name" value="{{ $user != null ? $user->name : '' }}" required autofocus>
            <label for="picture">{{ __('site.picture') }}: </label>
            <input type="file" class="form-control" id="picture" name="picture">
            <label for="code">{{ __('site.code') }}: </label>
            <input type="password" class="form-control" id="code" name="code" placeholder="****" pattern="[0-9]{4}"> <!--A pattern arra szplgál, hogy csak numerikus négy számjegyű kód legyen megadható, az számok mennyiségét lehet, hogy majd később át kell gondolni -->
            <label for="fingerprint">{{ __('site.fingerprint') }}: </label>
            <input type="text" class="form-control" id="fingerprint" name="fingerprint" value="{{ $user != null ? $user->fingerprint : '' }}">
            <label for="languages">{{ __('site.language') }}:* </label>
            <br>
            <select name="language" id="languages" value="{{ $user != null ? $user->language : '' }}" required="required">
                <option value="en">
                    <p>English</p>
                </option>
                <option value="hu">
                    <p>Magyar</p>
                </option>
            </select>
            <br> <!--Ez azért kell, hogy a nyelvválasztó elkülönüljön-->
            <label for="profile">{{ __('site.profile') }}: </label>
            <input type="text" class="form-control" id="profile" name="profile" value="{{$user != null ? $user->profile : ''}}">
            <!--A felhasználó jogosultságait szabályozó kapcsolók-->
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="isAdminSwitch" name="isAdmin" {{$user != null ? $user->isAdmin  ? 'checked' : '' : ''}}>
                <label class="form-check-label" for="isAdminSwitch">{{ __('site.isAdmin') }}</label>
            </div>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="isWebEnabledSwitch" name="isWebEnabled" {{$user != null ? $user->isWebEnabled  ? 'checked' : '' : ''}}>
                <label class="form-check-label" for="isWebEnabledSwitch">{{ __('site.isWebEnabled') }}</label>
            </div>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="isEntryEnabledSwitch" name="isEntryEnabled" {{$user != null ? $user->isEntryEnabled  ? 'checked' : '' : ''}}>
                <label class="form-check-label" for="isEntryEnabledSwitch">{{ __('site.isEntryEnabled') }}</label>
            </div>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="isEmployeeSwitch" name="isEmployee" {{$user != null ? $user->isEmployee  ? 'checked' : '' : ''}}>
                <label class="form-check-label" for="isEmployeeSwitch">{{ __('site.isEmployee') }}</label>
            </div>
            <label for="email">{{ __('auth.email') }}:* </label>
            <input type="email" class="form-control" id="floatingInput" placeholder="name@example.com" name="email" value="{{$user != null ? $user->email : ''}}" required>
            <label for="password">{{ __('auth.password') }}:* </label>
            <input type="password" class="form-control" id="password" name="password" placeholder="********" {{$user == null ? 'reguired' : ''}}>
            <label for="cardId">{{ __('site.cardId') }}: </label>
            <input type="text" class="form-control" id="cardId" name="cardId" value="{{$user != null ? $user->cardId : ''}}">
            <button type="submit" class="btn btn-primary mt-2 mb-2"  >{{$user != null ?  __('site.editUser')  :  __('site.addUser') }}</button>
            <a type="button" class="btn btn-danger mt-2 mb-2" href="{{ route('users') }}" role="button">{{ __('site.cancel') }}</a>
        </form>
</main>
</body>
</html>
