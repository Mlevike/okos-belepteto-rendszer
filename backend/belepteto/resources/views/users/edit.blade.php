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
        <p>{{ $userId }}</p>
        <form action="" method="post">
            @csrf
            <p>{{ __('site.name') }}:* </p>
            <input type="text" class="form-control" id="name" placeholder="Kis Géza" name="name">
            <p>{{ __('site.picture') }}: </p>
            <input type="text" class="form-control" id="picture" name="picture">
            <p>{{ __('site.code') }}: </p>
            <input type="password" class="form-control" id="code" name="code">
            <p>{{ __('site.fingerprint') }}: </p>
            <input type="text" class="form-control" id="fingerprint" name="fingerprint">
            <p>{{ __('site.language') }}: </p>
            <input type="text" class="form-control" id="language" name="language">
            <p>{{ __('site.profile') }}: </p>
            <input type="text" class="form-control" id="profile" name="profile">
            <p>{{ __('site.isAdmin') }}: </p>
            <input type="text" class="form-control" id="isAdmin" name="isAdmin">
            <p>{{ __('site.isWebEnabled') }}: </p>
            <input type="text" class="form-control" id="isWebEnabled" name="isWebEnabled">
            <p>{{ __('site.isEntryEnabled') }}: </p>
            <input type="text" class="form-control" id="isEntryEnabled" name="isEntryEnabled">
            <p>{{ __('site.isEmployee') }}: </p>
            <input type="text" class="form-control" id="isEmployee" name="isEmployee">
            <p>{{ __('auth.email') }}:* </p>
            <input type="email" class="form-control" id="floatingInput" placeholder="name@example.com" name="email">
            <p>{{ __('auth.password') }}:* </p>
            <input type="password" class="form-control" id="password" name="password">
            <p>{{ __('site.cardId') }}: </p>
            <input type="text" class="form-control" id="cardId" name="cardId">
            <button type="submit" class="btn btn-primary"  >{{ __('site.addUser') }}</button>
            <a type="button" class="btn btn-danger" href="users" role="button">{{ __('site.cancel') }}</a>
        </form>
</main>
</body>
</html>
