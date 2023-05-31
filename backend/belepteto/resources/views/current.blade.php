<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!--Ebben a fájlban található a felhasználók nézet sablonja.-->
<head>
    @include('head')
    <meta http-equiv="refresh" content="1">
</head>
<body>
<div class="container mt-4">
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
    <div class="col">
    @if($user != null)
            @if($user->picture == "")
                <svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                    <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                    <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                </svg>
            @else
                <img class="image rounded-circle" alt="{{ __('site.picture') }}" src="{{asset('/storage/pictures/profile/'.$user->picture)}}" alt="profile_image" style="width: 200px;height: 200px; padding: 10px; margin: 0px; object-fit: cover; "/>
            @endif
        @else
            <svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
            </svg>
        @endif
    </div>
    <div class="col">
        @if($user != null)
        <h2>{{ __('site.name') }}: {{$user != null ? $user->name : ''}}</h2>
        <h2>{{ __('site.cardId') }}: {{$history->card_id}}</h2>
        <h2 class="{{$history->successful ? 'text-success' : 'text-danger'}}"><b>{{$history->successful ?  __('site.successful') :  __('site.fail') }}</b></h2>
        <h2 class="{{$history->direction == 'in' ? 'text-success' : 'text-danger'}}"><b>{{$history->direction == 'in' ?  __('site.in') :  __('site.out') }}</b></h2>
        @else
            <h2>{{ __('site.name') }}: </h2>
            <h2>{{ __('site.cardId') }}: </h2>
        @endif
    </div>
</div>
</div>
</div>
</body>
</html>
