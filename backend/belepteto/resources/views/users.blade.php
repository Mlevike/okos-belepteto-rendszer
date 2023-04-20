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
        <th>Name</th>
        <th>Picture</th>
        <th>Code</th>
        <th>Fingerprint</th>
        <th>Language</th>
        <th>Profile</th>
        <th>isAdmin</th>
        <th>isWebEnabled</th>
        <th>isEntryEnabled</th>
        <th>isEmployee</th>
        <th>email</th>
        <th>password</th>
    </thead>
    <tbody>

    @foreach($users as $user)
        <tr>
            <td>{{$user->name}} </td>
            <td>{{$user->picture}} </td>
            <td>{{$user->code}} </td>
            <td>{{$user->fingerprint}} </td>
            <td>{{$user->language}} </td>
            <td>{{$user->profile}} </td>
            <td>{{$user->isAdmin}} </td>
            <td>{{$user->isWebEnabled}} </td>
            <td>{{$user->isEntryEnabled}} </td>
            <td>{{$user->isEmplyee}} </td>
            <td>{{$user->email}} </td>
            <td>{{$user->password}} </td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
