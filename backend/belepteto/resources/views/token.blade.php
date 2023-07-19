<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!--Ebben a fájlban található a logok nézet sablonja.-->
<head>
    @include('head')
</head>
<body data-bs-theme="dark">
@include('header', ['$current_user'=>$current_user])
<main class="p-2">
    <h1>{{__('site.newToken')}}</h1>
    <h5>{{__('site.newTokenText')}}</h5>
    <input value="{{$hash}}" class="w-100"/>
    <a type="button" class="btn btn-primary mt-2 mb-2" href="javascript:history.back()" role="button">{{ __('site.back') }}</a> <!--Erre majd kell Laraveles megoldás is-->
</main>
</body>
</html>
