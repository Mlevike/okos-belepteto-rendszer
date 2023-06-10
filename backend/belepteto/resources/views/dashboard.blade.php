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
<main class="container m-1 w-100">
    <h1>{{ __('site.dashboard') }}</h1>
    <div class="row">
        <div class="col">
            <h2>Jelenleg bent levő felhasználók</h2>
            <div class="position-relative">
                <canvas id="isHereChart" class="position-absolute top-0 start-50 translate-middle-x"></canvas> <!--Ez azért kell, hogy a helyén középre legyen rendezve a diagram -->
            </div>
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
                        text: "Jelenleg bent levő felhasználók"
                    }
                }
            });
            </script>
        </div>
        <div class="col">
            <h2>{{__('site.options')}}</h2>
            <a type="button" class="btn btn-primary w-100" href="{{ route('current') }}" role="button" target="_blank">{{ __('site.showCurrentUser') }}</a>
            <div class="bg-danger rounded mt-2 p-2">
                <h3 class="text-center text-bold text-white">Danger zone</h3>
                <div class="mt-2 mb-2">
                    <p class="text-white d-inline-block">{{ __('site.isInsideEntryEnabled') }}: YES</p>
                    <a type="button" class="btn btn-warning d-inline-block" href="{{ route('current') }}" role="button" target="_blank"><i class="bi bi-pencil-fill"></i></a>
                </div>
                <div class="mt-2 mb-2">
                        <p class="text-white d-inline-block">{{ __('site.isInsideEntryEnabled') }}: NO</p>
                        <a type="button" class="btn btn-warning d-inline-block" href="{{ route('current') }}" role="button" target="_blank"><i class="bi bi-pencil-fill"></i></a>
                </div>
                <a type="button" class="btn btn-warning w-100" href="{{ route('current') }}" role="button" target="_blank">{{ __('site.generateNewAccessToken') }}</a>
            </div>
        </div>
        </div>
</main>
</body>
</html>
