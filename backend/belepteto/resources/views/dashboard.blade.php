<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!--Ebben a fájlban található a vezérlőpult nézet sablonja.-->
<head>
@include('head')
    <script src=" https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js ">
    </script>
</head>
<body>
@include('header', ['current_user'=>$current_user])
<main class="p-2">
    <h1>{{ __('site.dashboard') }}</h1>
    <h2>Jelenleg bent levő felhasználók</h2>
    <canvas id="isHereChart" class="mb-2 w-25 h-25"></canvas>
    <script>
        var xValues = ["Itt van", "Elment"];
        var yValues = [{{$here}}, {{$notHere}}];
        var barColors = [
            "#00aba9",
            "#b91d47",
        ];

        new Chart("isHereChart", {
            type: "pie",
            data: {
                labels: xValues,
                datasets: [{
                    backgroundColor: barColors,
                    data: yValues
                }]
            },
            options: {
                title: {
                    display: true,
                    text: "World Wide Wine Production 2018"
                }
            }
        });
    </script>
    <a type="button" class="btn btn-primary" href="{{ route('current') }}" role="button" target="_blank">{{ __('site.showCurrentUser') }}</a>
</main>
</body>
</html>
