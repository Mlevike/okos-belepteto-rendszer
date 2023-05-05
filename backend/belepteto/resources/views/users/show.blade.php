<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!--Ebben a fájlban található a felhasználók nézet sablonja.-->
<head>
@include('head')
</head>
<body>
@include('header')
<h1>{{ $user->name }}</h1>
<div class="container">
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
    <div class="col">
    @if($user->picture == "")
        <svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
            <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
        </svg>
    @else
        <img alt="{{ __('site.picture') }}" src="{{$user->picture}}"/>
    @endif
    </div>
    <div class="col">
    <p>{{ __('site.language') }}: {{$user->language}}</p>
    <p>{{ __('site.profile') }}: {{$user->profile}}</p>
    <p>{{ __('site.isAdmin') }}:
        @if($user->isAdmin)
            <i class="bi bi-check-square-fill" style="color: green"></i>
        @else
            <i class="bi bi-x-square-fill" style="color: red"></i>
        @endif
    </p>
    <p>{{ __('site.isWebEnabled') }}:
        @if($user->isWebEnabled)
            <i class="bi bi-check-square-fill" style="color: green"></i>
        @else
            <i class="bi bi-x-square-fill" style="color: red"></i>
        @endif
    </p>
    <p>{{ __('site.isEntryEnabled') }}:
        @if($user->isEntryEnabled)
            <i class="bi bi-check-square-fill" style="color: green"></i>
        @else
            <i class="bi bi-x-square-fill" style="color: red"></i>
        @endif
    </p>
        <p>
            {{ __('site.hasCode') }}:
            @if($user->code != null and $user->code != "")
                <i class="bi bi-check-square-fill" style="color: green"></i>
            @else
                <i class="bi bi-x-square-fill" style="color: red"></i>
            @endif
        </p>
        <p>
            {{ __('site.hasFingerprint') }}:
            @if($user->fingerprint != null and $user->fingerprint != "")
                <i class="bi bi-check-square-fill" style="color: green"></i>
            @else
                <i class="bi bi-x-square-fill" style="color: red"></i>
            @endif
        </p>
    <p>{{ __('site.isEmployee') }}:
        @if($user->isEmployee)
            <i class="bi bi-check-square-fill" style="color: green"></i>
        @else
            <i class="bi bi-x-square-fill" style="color: red"></i>
        @endif
    </p>
    <p>{{ __('site.isHere') }}:
        @if($user->isHere)
            <i class="bi bi-check-square-fill" style="color: green"></i>
        @else
            <i class="bi bi-x-square-fill" style="color: red"></i>
        @endif
    </p>
    <p>{{ __('auth.email') }}: {{$user->email}}</p>
    <p>{{ __('site.cardId') }}: {{$user->cardId}}</p>
</div>
</div>
</div>
<a type="button" class="btn btn-danger" href="{{ route('users') }}" role="button">{{ __('site.cancel') }}</a>
</body>
</html>
