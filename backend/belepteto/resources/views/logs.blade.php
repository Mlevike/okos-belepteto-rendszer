<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!--Ebben a fájlban található a logok nézet sablonja.-->
<head>
@include('head')
</head>
<body>
@include('header')
<h1>{{ __('site.logs') }}</h1>
<h2>{{ __('site.user_logs') }}</h2>
<h2>{{ __('site.system_logs') }}</h2>
</body>
</html>
