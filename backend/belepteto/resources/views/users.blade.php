<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!--Ebben a fájlban található a felhasználók nézet sablonja.-->
<head>
@include('head')
</head>
<body>
@include('header')
<h1>{{ __('site.users') }}</h1>
<table class="table table-hover">
    <thead>
        <th>Username</th>
        <th>Orders</th>
        <th>Balance</th>
    </thead>
    <tbody>

    @foreach($users as $user)
        <tr>
            <td>{{$user->username}} </td>
            <td>{{$user->purchases}} </td>
            <td>{{$user->balance}} </td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
