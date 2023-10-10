<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!--Ebben a fájlban található a felhasználók nézet sablonja.-->
<head>
@include('head')
</head>
<body {{$current_user->darkMode ? 'data-bs-theme=dark' : ''}}>
@include('header', ['current_user', $current_user])
<main class="p-2">
<h1>{{ __('site.users') }}</h1>
<div class="table-responsive" style="margin: 0px 10px 0px 10px;">
    <table class="table table-hover">
        <thead>
            <th>#</th>
            <th>{{ __('site.name') }}</th>
            <th>{{ __('site.picture') }}</th>
            <th>{{ __('site.language') }}</th>
            <th>{{ __('site.profile') }}</th>
            <th>{{ __('site.isEntryEnabled') }}</th>
            <th>{{ __('site.hasCode') }}</th>
            <th>{{ __('site.hasFingerprint') }}</th>
            <th>{{ __('site.validationMethod') }}</th>
            <th>{{ __('site.role') }}</th>
            <th>{{ __('site.isHere') }}</th>
            <th>{{ __('auth.email') }}</th>
            <th>{{ __('site.cardId') }}</th>
    </thead>
    <tbody>

    @foreach($users as $user)
        <tr onclick="window.location='{{ route('users-show', [$userId = $user->id]) }}'" style="cursor: pointer;">
            <td>{{$user->id}} </td>
            <td>{{$user->name}} </td>
            <td>
                @if($user->picture == "")
                    <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                        <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                        <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                    </svg>
                @else
                    <img class="rounded-circle shadow-4-strong" alt="{{ __('site.picture') }}" src="{{asset('/storage/pictures/profile/'.$user->picture)}}" alt="profile_image" style="width: 100px;height: 100px; padding: 10px; margin: 0px; object-fit: cover; "/>
                @endif
            </td>
            <td>
                @if($user->language == 'en')
                    <span class="fi fi-gb"></span>
                @elseif($user->language == 'hu')
                <span class="fi fi-hu"></span>
                @endif
            </td>
            <td>{{$user->profile}}</td>


            <td>
                @if($user->isEntryEnabled)
                    <i class="bi bi-check-square-fill" style="color: green"></i>
                @else
                    <i class="bi bi-x-square-fill" style="color: red"></i>
                @endif
            </td>
            <td>
                @if($user->code != null and $user->code != "")
                    <i class="bi bi-check-square-fill" style="color: green"></i>
                @else
                    <i class="bi bi-x-square-fill" style="color: red"></i>
                @endif
            </td>
            <td>
                @if($user->fingerprint != null and $user->fingerprint != "")
                    <i class="bi bi-check-square-fill" style="color: green"></i>
                @else
                    <i class="bi bi-x-square-fill" style="color: red"></i>
                @endif
            </td>
            <td>
                {{$user->validationMethod}}
            </td>
            <td>
                {{$user->role}}
            </td>
            <td>
                @if($user->isHere)
                    <i class="bi bi-check-square-fill" style="color: green"></i>
                @else
                    <i class="bi bi-x-square-fill" style="color: red"></i>
                @endif
            </td>
            <td>{{$user->email}} </td>
            <td>{{$user->cardId}} </td>
        </tr>
    @endforeach
        </tbody>
</table>
</div>
<div>
    {{$users->links()}}
</div>
@if($current_user->role == 'admin')
    <a type="button" class="btn btn-primary mt-2 mb-2" href="{{ route('users-add') }}" role="button"><i class="bi bi-plus"></i> {{ __('site.addUser') }}</a>
@endif
</main>
@include('footer')
</body>
</html>
