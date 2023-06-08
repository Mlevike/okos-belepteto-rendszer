<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!--Ebben a fájlban található a felhasználók nézet sablonja.-->
<head>
@include('head')
</head>
<body>
@include('header')
<main class="p-2">
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
        <img class="image rounded-circle" alt="{{ __('site.picture') }}" src="{{asset('/storage/pictures/profile/'.$user->picture)}}" alt="profile_image" style="width: 200px;height: 200px; padding: 10px; margin: 0px; object-fit: cover; "/>
    @endif
    </div>
    <div class="col">
    <p>{{ __('site.language') }}:
        <td>
            @if($user->language == 'en')
                <span class="fi fi-gb"></span>
            @elseif($user->language == 'hu')
            <span class="fi fi-hu"></span>
            @endif
        </td>
    </p>
    <p>{{ __('site.profile') }}: {{$user->profile}}</p>
    <p>{{ __('site.isAdmin') }}:
        @if($user->role == 'admin')
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
        @if($user->role == 'employee')
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
    <a type="button" class="btn btn-danger" href="{{ route('users-delete', [$userId = $user->id]) }}" role="button"><i class="bi bi-eye-fill"></i> {{ __('site.delete') }}</a>
    <a type="button" class="btn btn-primary" href="{{ route('users-edit', [$userId = $user->id]) }}" role="button"><i class="bi bi-pencil-square"></i> {{ __('site.edit') }}</a>
    <a type="button" class="btn btn-primary" href="{{ route('users') }}" role="button"><i class="bi bi-arrow-left-circle-fill"></i> {{ __('site.back') }}</a>
</main>
</body>
</html>
