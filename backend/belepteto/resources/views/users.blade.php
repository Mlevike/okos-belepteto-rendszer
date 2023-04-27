<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!--Ebben a fájlban található a felhasználók nézet sablonja.-->
<head>
@include('head')
</head>
<body>
@include('header')
<h1>{{ __('site.users') }}</h1>
<div class="table-responsive" style="margin: 0px 10px 0px 10px;">
    <table class="table table-hover">
        <thead>
            <th>{{ __('site.name') }}</th>
            <th>{{ __('site.picture') }}</th>
            <th>{{ __('site.code') }}</th>
            <th>{{ __('site.fingerprint') }}</th>
            <th>{{ __('site.language') }}</th>
            <th>{{ __('site.profile') }}</th>
            <th>{{ __('site.isAdmin') }}</th>
            <th>{{ __('site.isWebEnabled') }}</th>
            <th>{{ __('site.isEntryEnabled') }}</th>
            <th>{{ __('site.isEmployee') }}</th>
            <th>{{ __('auth.email') }}</th>
            <th>{{ __('auth.password') }}</th>
            <th>{{ __('site.cardId') }}</th>
            <th>{{ __('site.options') }}</th>
    </thead>
    <tbody>

    @foreach($users as $user)
        <tr>
            <td>{{$user->name}} </td>
            <td>{{$user->picture}} </td>
            <td>
                <i class="bi bi-pencil-square"></i>
            </td>
            <td>{{$user->fingerprint}} </td>
            <td>{{$user->language}} </td>
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
                @if($user->isEmployee)
                    <i class="bi bi-check-square-fill" style="color: green"></i>
                @else
                    <i class="bi bi-x-square-fill" style="color: red"></i>
                @endif
            </td>
            <td>{{$user->email}} </td>
            <td>
                <i class="bi bi-pencil-square"></i>
            </td>
            <td>{{$user->cardId}} </td>
            <td style="width: 100px"> <!--Egyenlőre így jó, de lehet hogy később változtatni kell rajta!-->
                <a href="/users/delete"><i class="bi bi-trash3-fill" style="color: red"></i></a>
                <i class="bi bi-pencil-square"></i>
                <i class="bi bi-eye-fill"></i>
            </td>
        </tr>
    @endforeach
        </tbody>
</table>
    <a type="button" class="btn btn-primary" href="/users/add" role="button">{{ __('site.addUser') }}</a>
</div>
</body>
</html>
