<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!--Ebben a fájlban található a felhasználók nézet sablonja.-->
<head>
@include('head')
</head>
<body>
@include('header')
<h1>{{ $user->name }}</h1>
<div>
    <img alt="{{ __('site.picture') }}" src="{{$user->picture}}"/>
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
    <p>{{ __('site.isEmployee') }}:
        @if($user->isEmployee)
            <i class="bi bi-check-square-fill" style="color: green"></i>
        @else
            <i class="bi bi-x-square-fill" style="color: red"></i>
        @endif
    </p>
    <p>{{ __('auth.email') }}: {{$user->email}}</p>
    <p>{{ __('site.cardId') }}: {{$user->cardId}}</p>
    <a type="button" class="btn btn-danger" href="{{ route('users') }}" role="button">{{ __('site.cancel') }}</a>
</div>
</body>
</html>
