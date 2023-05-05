<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!--Ebben a fájlban található a felhasználók nézet sablonja.-->
<head>
@include('head')
</head>
<body>
@include('header', ['current_user', $current_user])
<h1>{{ __('site.users') }}</h1>
<div class="table-responsive" style="margin: 0px 10px 0px 10px;">
    <table class="table table-hover">
        <thead>
            <th>#</th>
            <th>{{ __('site.name') }}</th>
            <th>{{ __('site.picture') }}</th>
            <th>{{ __('site.language') }}</th>
            <th>{{ __('site.profile') }}</th>
            <th>{{ __('site.isAdmin') }}</th>
            <th>{{ __('site.isWebEnabled') }}</th>
            <th>{{ __('site.isEntryEnabled') }}</th>
            <th>{{ __('site.hasCode') }}</th>
            <th>{{ __('site.hasFingerprint') }}</th>
            <th>{{ __('site.isEmployee') }}</th>
            <th>{{ __('site.isHere') }}</th>
            <th>{{ __('auth.email') }}</th>
            @if($current_user->isAdmin)
            <th>{{ __('auth.password') }}</th>
            @endif
            <th>{{ __('site.cardId') }}</th>
            @if($current_user->isAdmin)
            <th>{{ __('site.options') }}</th>
            @endif
    </thead>
    <tbody>

    @foreach($users as $user)
        <tr>
            <td>{{$user->id}} </td>
            <td>{{$user->name}} </td>
            <td>{{$user->picture}} </td>
            <td>
                <span class="fi fi-{{$user->language}}"></span>
            </td>
            <td>{{$user->profile}} </td>
            <td>
                @if($user->isAdmin)
                    <i class="bi bi-check-square-fill" style="color: green"></i>
                @else
                    <i class="bi bi-x-square-fill" style="color: red"></i>
                @endif
            </td>
            <td>
                @if($user->isWebEnabled)
                    <i class="bi bi-check-square-fill" style="color: green"></i>
                @else
                    <i class="bi bi-x-square-fill" style="color: red"></i>
                @endif
            </td>
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
                @if($user->isEmployee)
                    <i class="bi bi-check-square-fill" style="color: green"></i>
                @else
                    <i class="bi bi-x-square-fill" style="color: red"></i>
                @endif
            </td>
            <td>
                @if($user->isHere)
                    <i class="bi bi-check-square-fill" style="color: green"></i>
                @else
                    <i class="bi bi-x-square-fill" style="color: red"></i>
                @endif
            </td>
            <td>{{$user->email}} </td>
            @if($current_user->isAdmin)
            <td>
                <a type="button" class="btn btn-primary" href="{{ route('users-edit', [$userId = $user->id]) }}" role="button"><i class="bi bi-pencil-square"></i></a>
            </td>
            @endif
            <td>{{$user->cardId}} </td>
            @if($current_user->isAdmin)
            <td style="width: 100px"> <!--Egyenlőre így jó, de lehet hogy később változtatni kell rajta!-->
                <div class="btn-group" role="group" >
                    <form action="{{ route('users-delete') }}" method="post">
                        @csrf
                        <input type="hidden" value="{{$user->id}}" name="id">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-trash3-fill" style="color: red"></i></button>
                    </form>
                    <a type="button" class="btn btn-primary" href="{{ route('users-edit', [$userId = $user->id]) }}" role="button"><i class="bi bi-pencil-square"></i></a>
                    <a type="button" class="btn btn-primary" href="{{ route('users-show', [$userId = $user->id]) }}" role="button"><i class="bi bi-eye-fill"></i></a>
                </div>
            </td>
            @endif
        </tr>
    @endforeach
        </tbody>
</table>
</div>
<div>
    {{$users->links()}}
</div>
@if($current_user->isAdmin)
    <a type="button" class="btn btn-primary" href="{{ route('users-add') }}" role="button">{{ __('site.addUser') }}</a>
@endif
</body>
</html>
