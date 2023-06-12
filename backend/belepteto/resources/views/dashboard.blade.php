<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!--Ebben a fájlban található a vezérlőpult nézet sablonja.-->
<head>
@include('head')
    <script src=" https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js ">
    </script>
    <script type="text/javascript">
        function triggerNewTokenDialog(){ //A törlés megerősítésére szolgáló dialog megjelenítése
            $('.modal').modal('show')
        };
    </script>
</head>
<body>
@include('header', ['current_user'=>$current_user])
<main class="container m-1 w-100">
    <h1>{{ __('site.dashboard') }}</h1>
    <div class="row">
        <div class="col-12 col-md-6">
            <h2>Jelenleg bent levő felhasználók</h2>
            <div class="position-relative">
                <canvas id="isHereChart" class=""></canvas> <!--Ez azért kell, hogy a helyén középre legyen rendezve a diagram -->
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
        <div class="col-12 col-md-6">
            <h2>{{__('site.options')}}</h2>
            <a type="button" class="btn btn-primary w-100" href="{{ route('current') }}" role="button" target="_blank">{{ __('site.showCurrentUser') }}</a>
            <div class="bg-danger rounded mt-2 p-2">
                <h3 class="text-center text-bold text-white">{{__('site.dangerZone')}}</h3>
                <div class="mt-2 mb-2">
                    <p class="text-white d-inline-block">{{ __('site.isInsideEntryEnabled') }}:
                        @if($isEntryEnabled->setting_value)
                            {{__('site.yes')}}
                        @elseif(!($isEntryEnabled->setting_value))
                            {{__('site.no')}}
                        @endif
                    </p>
                    <a type="button" class="btn btn-warning d-inline-block" href="{{ route('set-entry-enabled') }}" role="button"><i class="bi bi-pencil-fill"></i></a>
                </div>
                <div class="mt-2 mb-2">
                        <p class="text-white d-inline-block">{{ __('site.isOutsideEntryEnabled') }}:
                            @if($isExitEnabled->setting_value)
                                {{__('site.yes')}}
                            @elseif(!($isExitEnabled->setting_value))
                                {{__('site.no')}}
                            @endif
                        </p>
                        <a type="button" class="btn btn-warning d-inline-block" href="{{ route('set-exit-enabled') }}" role="button"><i class="bi bi-pencil-fill"></i></a>
                </div>
                <a type="button" class="btn btn-warning w-100" onclick="triggerNewTokenDialog()" role="button">{{ __('site.generateNewAccessToken') }}</a>
            </div>
        </div>
        </div>
</main>
<!-- New Token Dialog -->
<div class="modal fade" id="deleteDialog" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="idDeleteDialogTitle">{{ __('site.confirmation') }}</h5>
            </div>
            <div class="modal-body">
                <p>{{ __('site.areYouSureToGenerateNewToken') }}</p>
            </div>
            <div class="modal-footer">
                <a type="button" class="btn btn-warning" onclick="$('.modal').modal('hide')" href="{{ route('generate-token')}}" role="button">{{ __('site.generateNewAccessToken') }}</a>
                <a type="button" class="btn btn-primary" onclick="$('.modal').modal('hide')">{{ __('site.back') }}</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
