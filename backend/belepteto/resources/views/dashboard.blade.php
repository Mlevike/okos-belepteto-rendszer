<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!--Ebben a fájlban található a vezérlőpult nézet sablonja.-->
<head>
@include('head')
</head>
<body>
@include('header', ['current_user'=>$current_user])
<h1>{{ __('site.dashboard') }}</h1>
<a type="button" class="btn btn-primary" href="{{ route('current') }}" role="button" target="_blank">{{ __('site.showCurrentUser') }}</a>
</body>
</html>
