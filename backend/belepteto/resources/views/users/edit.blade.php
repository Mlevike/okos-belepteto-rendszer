<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!--Ebben a fájlban található a logok nézet sablonja.-->
<head>
@include('head')
</head>
<body>
@include('header')
<main>
    <h1>{{ __('site.editUser') }}</h1>
        <p>{{ $errors }}</p>
        <!--A felhasználó szerkeztésére szolgáló form-->
        <form action="" method="post" enctype=multipart/form-data>
            @csrf
            <p>{{ __('site.name') }}:* </p>
            <input type="text" class="form-control" id="name" placeholder="Kis Géza" name="name" value="{{ $user->name }}">
            <p>{{ __('site.picture') }}: </p>
            <input type="file" class="form-control" id="picture" name="picture">
            <p>{{ __('site.code') }}: </p>
            <input type="password" class="form-control" id="code" name="code" value="{{__('site.code') }}">
            <p>{{ __('site.fingerprint') }}: </p>
            <input type="text" class="form-control" id="fingerprint" name="fingerprint" value="{{__('site.fingerprint') }}">
            <p>{{ __('site.language') }}: </p>
            <input type="text" class="form-control" id="language" name="language" value="{{ $user->language }}">
            <p>{{ __('site.profile') }}: </p>
            <input type="text" class="form-control" id="profile" name="profile" value="{{ $user->profile }}">
            <!--A felhasználó jogosultságait szabályozó kapcsolók-->
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="isAdminSwitch" name="isAdmin" {{  $user->isAdmin  ? 'checked' : '' }}>
                <label class="form-check-label" for="isAdminSwitch">{{ __('site.isAdmin') }}</label>
            </div>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="isWebEnabledSwitch" name="isWebEnabled" {{  $user->isWebEnabled  ? 'checked' : '' }}>
                <label class="form-check-label" for="isWebEnabledSwitch">{{ __('site.isWebEnabled') }}</label>
            </div>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="isEntryEnabledSwitch" name="isEntryEnabled" {{  $user->isEntryEnabled  ? 'checked' : '' }}>
                <label class="form-check-label" for="isEntryEnabledSwitch">{{ __('site.isEntryEnabled') }}</label>
            </div>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="isEmployeeSwitch" name="isEmployee" {{  $user->isEmployee  ? 'checked' : '' }}>
                <label class="form-check-label" for="isEmployeeSwitch">{{ __('site.isEmployee') }}</label>
            </div>
            <p>{{ __('auth.email') }}:* </p>
            <input type="email" class="form-control" id="floatingInput" placeholder="name@example.com" name="email" value="{{ $user->email}}">
            <p>{{ __('auth.password') }}:* </p>
            <input type="password" class="form-control" id="password" name="password" value="{{__('auth.password') }}">
            <p>{{ __('site.cardId') }}: </p>
            <input type="text" class="form-control" id="cardId" name="cardId" value="{{ $user->cardId }}">
            <button type="submit" class="btn btn-primary"  >{{ __('site.editUser') }}</button>
            <a type="button" class="btn btn-danger" href="{{ route('users') }}" role="button">{{ __('site.cancel') }}</a>
        </form>
</main>
</body>
</html>
