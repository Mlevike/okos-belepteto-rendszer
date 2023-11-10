<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!--Ebben a fájlban található a vezérlőpult nézet sablonja.-->
<head>
@include('head')
    <script src=" https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js ">
    </script>
    <script type="text/javascript">
        function triggerNewTokenDialog(){ //A törlés megerősítésére szolgáló dialog megjelenítése
            $('#newTokenDialog').modal('show')
        }
        function triggerRecordFingerprintDialog(){ //Az ujjlenyomat felvételéért felelős metódus megjelenítése
            $('#fpRecordDialog').modal('show')
        }

        function triggerCancelOperationDialog(id){ //Az ujjlenyomat felvételéért felelős metódus megjelenítése
            $('#cancelDialog').modal('show')
            document.getElementById('cancel-operation-button').href = document.getElementById('cancel-operation-button').href + "?id=" + id;
        }

       function fetchIDS(){
            /*Létrehozunk HTML objektumokra mutató változókat*/
            fetch('{{ route('get-usable-ids') }}').then(response => response.json()).then(data => { //Le fetcheljük az adatot az API segítségével
                //Ellenőrizzük, hogy változnak az adatok és csak akkor frissítjük az oldalt
            })
        }

        function fetchDashboard(){
            /*Létrehozunk HTML objektumokra mutató változókat*/

            fetch('{{ route('poll-dashboard') }}').then(response => response.json()).then(data => { //Le fetcheljük az adatot az API segítségével
                //Ellenőrizzük, hogy változnak az adatok és csak akkor frissítjük az oldalt!
                if(data.here != yValues[0] || data.notHere != yValues[1]){
                    yValues = [data.here, data.notHere];
                    chart.data.datasets[0].data = yValues;
                    chart.update();
                }
            })
        }

        setInterval(()=>{ //A setInterval() metódus segítségével megadjuk ms-ban, hogy bizonyos ídőközönként kérje le az adatokat a szerverről
            fetchDashboard();
        },5000)

      /*  function handleShowUsedClick(){ //A már használt ID-k megjelenítésére szolgáló metódus
            const used = document.querySelectorAll('used-fp-id');
            if(document.querySelector('#showUsed').checked){
                console.log("Eljut idáig");
                used.forEach(current => {
                    current.style.display = "block";
                });
            }else{
                console.log("Idáig is");
                used.forEach(current => {
                    current.prop('disabled', true);
                });
                console.log("Ez is lefut...")
            }
        }/* /*Egyenlőre ez nem használjuk*/
    </script>
    <style>
        /*.used-fp-id{
            display: none;
        }*/
    </style>
</head>
<body {{$current_user->darkMode ? 'data-bs-theme=dark' : ''}} onload="fetchIDS()">
@include('header', ['current_user'=>$current_user])
<main class="container m-1 w-100">
    <h1>{{ __('site.dashboard') }}</h1>
    <div class="row">
        <div class="col-12 col-md-6">
            <h2>{{__('site.users_currently_in')}}</h2>
            <div class="position-relative">
                <canvas id="isHereChart" class=""></canvas> <!--Ez azért kell, hogy a helyén középre legyen rendezve a diagram -->
            </div>
            <script>
                var xValues = ["{{__('site.here')}}", "{{__('site.left')}}"];
             var yValues = [{{$here}}, {{$notHere}}];
                var barColors = [
                 "#00aba9",
                 "#b91d47",
                ];

                let chart = new Chart("isHereChart", {
                    type: "pie",
                    data: {
                    labels: xValues,
                    datasets: [{
                        backgroundColor: barColors,
                        data: yValues
                    }]
             },
                    options: {
                        animation: {
                            duration: 0
                        },
                    title: {
                        display: true,
                        text: "{{__('site.users_currently_in')}}"
                    }
                }
            });

            </script>
        </div>
        <div class="col-12 col-md-6">
            <h2>{{__('site.system_side_operations')}}</h2>
            <div class="table-responsive" style="margin: 0px 10px 0px 10px;">
                <table class="table table-hover">
                    <thead>
                    <th>#</th>
                    <th>{{ __('site.name') }}</th>
                    <th>{{ __('site.options') }}</th>
                    <th>{{ __('site.operation_state') }}</th>
                    <th>{{ __('site.sent_time') }}</th>
                    <th>{{ __('site.cancel') }}</th>
                    </thead>
                    <tbody>
                    @foreach($systemSideOperations as $current)
                        <tr>
                            <td>{{$current->id}}</td>
                            <td style="white-space: nowrap">
                                @if($current->name == 'register_fingerprint')
                                    {{ __('site.register_fp') }}
                                @else
                                    {{$current->name}}
                                @endif
                            </td>
                            <td>{{$current->options}}</td>
                            <td>{{ __('site.operation_state_'.$current->operation_state)}}</td>
                            <td>{{$current->sent_time}}</td>
                            <td>
                                @if($current->operation_state == 'created')
                                <a type="button" class="btn btn-danger"  onclick="triggerCancelOperationDialog({{$current->id}})"><i class="bi bi-x-octagon-fill"></i></a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div>
                {{$systemSideOperations->links()}}
            </div>
            <h2>{{__('site.options')}}</h2>
            <a type="button" class="btn btn-primary w-100" href="{{ route('current') }}" role="button" target="_blank">{{ __('site.showCurrentUser') }}</a>
            <a type="button" class="btn btn-primary w-100 mt-2 mb-2" onclick="triggerRecordFingerprintDialog()" role="button" >{{ __('site.register_FP_manually') }}</a>
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
               <!-- <div class="mt-2 mb-2">
                        <p class="text-white d-inline-block">{{ __('site.isOutsideEntryEnabled') }}:
                            @if($isExitEnabled->setting_value)
                                {{__('site.yes')}}
                            @elseif(!($isExitEnabled->setting_value))
                                {{__('site.no')}}
                            @endif
                        </p>
                        <a type="button" class="btn btn-warning d-inline-block" href="{{ route('set-exit-enabled') }}" role="button"><i class="bi bi-pencil-fill"></i></a>
                </div>  --> <!--Ideiglenesen kiszedve-->
                <a type="button" class="btn btn-warning w-100" onclick="triggerNewTokenDialog()" role="button">{{ __('site.generateNewAccessToken') }}</a>
            </div>
        </div>
        </div>
</main>
<!-- New Token Dialog -->
<div class="modal fade" id="newTokenDialog" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="idNewTokenDialogId">{{ __('site.confirmation') }}</h5>
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

<!-- Cancel Operation Dialog -->
<div class="modal fade" id="cancelDialog" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="idCancelDialogTitle">{{ __('site.confirmation') }}</h5>
            </div>
            <div class="modal-body">
                <p>{{ __('site.areYouSureToCancelOperation') }}</p>
            </div>
            <div class="modal-footer">
                <a type="button" class="btn btn-danger" onclick="$('.modal').modal('hide')" id="cancel-operation-button" href="{{ route('cancel-operation') }}" role="button">{{ __('site.cancel') }}</a>
                <a type="button" class="btn btn-primary" onclick="$('.modal').modal('hide')">{{ __('site.back') }}</a>
            </div>
        </div>
    </div>
</div>
<!-- FP Record Dialog -->
<div class="modal fade" id="fpRecordDialog" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{ route('start-fp-registration')}}">
            <div class="modal-header">
                <h5 class="modal-title" id="idDeleteDialogTitle">{{ __('site.register_FP_manually') }}</h5>
            </div>
            <div class="modal-body">
                <label class="mr-sm-2" for="inlineFormCustomSelect">Ujjlenyomat ID: </label>
                    @foreach($usedFingeprintIDs as $i)
                    <p>{{ $i }}</p>
                    @endforeach
                    <input id="fingerID" name="fingerID" type="number">
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" type="submit" value="Submit">{{ __('site.record') }}</button>
                <a type="button" class="btn btn-secondary" onclick="$('.modal').modal('hide')">{{ __('site.back') }}</a>
            </div>
            </form>
        </div>
    </div>
</div>
</body>
@include('footer')
</html>
