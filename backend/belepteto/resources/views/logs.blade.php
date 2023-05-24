<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!--Ebben a fájlban található a logok nézet sablonja.-->
<head>
@include('head')
</head>
<body>
@include('header', ['$current_user'=>$current_user])
<main class="p-2">
<h1>{{ __('site.logs') }}</h1>
<h2>{{ __('site.user_logs') }}</h2>
<!--A felhasználókkal kapcsolatos logok megjelenítése táblázatos formában-->
<div class="table-responsive" style="margin: 0px 10px 0px 10px;">
    <table class="table table-hover">
        <thead>
            <th>#</th>
            <th>Kártya azonosító</th>
            <th>Név</th>
            <th>Direction</th>
            <th>Successful?</th>
            <th>arriveTime</th>
            <th>leaveTime</th>
            <th>workTime</th>
        </thead>
        <tbody>
    @foreach($history as $current)
            <tr>
                <td>{{$current->id}}</td>
                <td>{{$current->card_id}}</td>
                <td>
                    @foreach($users as $user)
                        @if($user->id == $current->user_id) <!--Ez lehet, hogy nem a legoptimálisabb megoldás,de egyenlőre nem találtam jobbat!-->
                            {{$user->name}}
                        @endif
                    @endforeach
                </td>
                <td>{{$current->direction}}</td>
                <td> @if($current->successful)
                        <i class="bi bi-check-square-fill" style="color: green"></i>
                    @else
                        <i class="bi bi-x-square-fill" style="color: red"></i>
                    @endif</td>
                <td>{{$current->arriveTime}}</td>
                <td>{{$current->leaveTime}}</td>
                <td>{{$current->workTime}}</td>
            </tr>
    @endforeach
        </tbody>
    </table>
</div>
<div>
    {{$history->links()}}
</div>
@if($current_user->isAdmin)
<h2>{{ __('site.system_logs') }}</h2>
@endif
</main>
</body>
</html>
