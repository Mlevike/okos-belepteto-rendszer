<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
@include('head')
</head>
<body>
@include('header')
<h1>{{ __('site.logs') }}</h1>
</body>
</html>
