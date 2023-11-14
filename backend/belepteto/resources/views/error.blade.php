<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!--Ebben a fájlban található a vezérlőpult nézet sablonja.-->
<head>
@include('head')
</head>
<body {{$current_user->darkMode ? 'data-bs-theme=dark' : ''}}>
@include('header')
<main class="container m-1 w-100">
<h1>{{ __('Hiba') }}</h1>
<p>{{$errors}}</p>
<a type="button" class="btn btn-primary" href="{{ $back_link }}" role="button">{{ __('site.cancel') }}</a>
</main>
</body>
@include('footer')
</html>
