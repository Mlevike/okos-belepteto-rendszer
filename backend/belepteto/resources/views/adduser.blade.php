<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!--Ebben a fájlban található a logok nézet sablonja.-->
<head>
@include('head')
</head>
<body>
@include('header')
<main>
    <h1>{{ __('site.addUser') }}</h1>
    <p>{{ $errors }}</p>
    <a type="button" class="btn btn-primary" href="/users/add" role="button">{{ __('site.addUser') }}</a>
    <a type="button" class="btn btn-danger" href="/users" role="button">{{ __('site.cancel') }}</a>
</main>
</body>
</html>
