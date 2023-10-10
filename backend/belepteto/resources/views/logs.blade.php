<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!--Ebben a fájlban található a logok nézet sablonja.-->
<head>
@include('head')
</head>
<body {{$current_user->darkMode ? 'data-bs-theme=dark' : ''}}>
@include('header', ['$current_user'=>$current_user])
<main class="p-2">
<h1>{{ __('site.logs') }}</h1>
<h2>{{ __('site.user_logs') }}</h2>
<!--A felhasználókkal kapcsolatos logok megjelenítése táblázatos formában-->
<div class="table-responsive" style="margin: 0px 10px 0px 10px;">
    <table class="table table-hover">
        <thead>
            <th>#</th>
            <th>{{ __('site.cardId') }}</th>
            <th>{{ __('site.name') }}</th>
            <th>{{ __('site.direction') }}</th>
            <th>{{ __('site.successful') }}?</th>
            <th>{{ __('site.arriveTime') }}</th>
            <th>{{ __('site.leaveTime') }}</th>
            <!--<th>{{ __('site.workTime') }}</th>-->
        </thead>
        <tbody>
    @foreach($history as $current)
            <tr>
                <td>{{$current->id}}</td>
                <td>{{$current->cardId}}</td>
                <td>
                            {{$current->usersName}}
                </td>
                <td>
                    @if($current->direction == 'in')
                        {{ __('site.in') }}
                    @elseif($current->direction == 'out')
                        {{ __('site.out') }}
                    @endif
                </td>
                <td>
                    @if($current->successful)
                        <i class="bi bi-check-square-fill" style="color: green"></i>
                    @else
                        <i class="bi bi-x-square-fill" style="color: red"></i>
                    @endif
                </td>
                <td>{{$current->arriveTime}}</td>
                <td>{{$current->leaveTime}}</td>
                <!-- <td>{{$current->workTime}}</td> --> <!--Ideiglenesen elrejtve -->
            </tr>
    @endforeach
        </tbody>
    </table>
</div>
<div>
    {{$history->links()}}
</div>
@if($current_user->role == 'admin')
    <!--A rendszerrel kapcsolatos logok megjelenítése táblázatos formában -->
        <h2>{{ __('site.system_logs') }}</h2>
        <div class="table-responsive" style="margin: 0px 10px 0px 10px;">
        <table class="table table-hover">
            <thead>
                <th>#</th>
                <th>{{ __('site.errorLevel') }}</th>
                <th>{{ __('site.description') }}</th>
                <th>{{ __('site.time') }}</th>
            </thead>
            <tbody>
                    @foreach($logs as $current)
                        @if($current != null)
                            <tr
                                @if($current->level_name == 'EMERGENCY' or $current->level_name == 'ALERT' or $current->level_name == 'CRITICAL' or $current->level_name == 'ERROR')
                                    class="bg-danger"
                                @elseif($current->level_name == 'WARNING')
                                    class="bg-warning"
                                @endif
                            >
                                <td>{{$current->id}}</td>
                                <td>{{$current->level_name}}</td>
                                <td>{{$current->message}}</td>
                                <td>{{$current->logged_at}}</td>
                            </tr>
                        @endif
                    @endforeach
            </tbody>
        </table>
        </div>
        <div>
            {{$logs->links()}}
        </div>
@endif
</main>
@include('footer')
</body>
</html>
