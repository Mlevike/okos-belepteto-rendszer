<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!--Ebben a fájlban található a vezérlőpult nézet sablonja.-->
<head>
@include('head')
    <script src=" https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js "></script>
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
                if(data.here != c1_yValues[0] || data.notHere != c1_yValues[1]){
                    c1_yValues = [data.here, data.notHere];
                    chart1.data.datasets[0].data = c1_yValues;
                    chart1.update();
                }
                if(data.notValidate != c2_yValues[0] || data.validateWithCode != c2_yValues[1] || data.validateWithFingerprint != c2_yValues[2] || data.validateWithBoth != c2_yValues[3]){
                    c2_yValues = [data.notValidate, data.validateWithCode, data.validateWithFingerprint, data.validateWithBoth];
                    chart2.data.datasets[0].data = c2_yValues;
                    chart2.update();
                }
                if(data.hasEntryPermission != c3_yValues[0] || data.notHasEntryPermission != c3_yValues[1]){
                    c3_yValues = [data.hasEntryPermission, data.notHasEntryPermission];
                    chart3.data.datasets[0].data = c3_yValues;
                    chart3.update();
                }
                if(data.adminRole != c4_yValues[0] || data.employeeRole != c4_yValues[1] || data.userRole != c4_yValues[2]){
                    c4_yValues = [data.adminRole, data.employeeRole, data.userRole];
                    chart4.data.datasets[0].data = c4_yValues;
                    chart4.update();
                }
            })
        }

        setInterval(()=>{ //A setInterval() metódus segítségével megadjuk ms-ban, hogy bizonyos ídőközönként kérje le az adatokat a szerverről
            fetchDashboard();
        },5000)

        function checkFingerprintID(){
            let usedIDs = @json($usedFingerprintIDs); //Átalakítjuk JSON tömbbé a JS miatt
            let id = document.getElementById("fingerID").value;
            if(usedIDs.includes(id)){
                $('#fpRecordDialog').modal('hide');
                $('#fpDuplicateWarnDialog').modal('show');
            }else{
                $('#fpRecordDialog').modal('hide');
                document.getElementById("fingerprintRecordForm").submit();
            }
        }
    </script>
</head>
<body {{$current_user->darkMode ? 'data-bs-theme=dark' : ''}} onload="fetchIDS()">
@include('header', ['current_user'=>$current_user])
<main class="container m-1 w-100">
    <h1>{{ __('site.dashboard') }}</h1>
    <div class="row">
        <div class="col-12 col-md-6">
            <h2 style="text-align: center">{{__('site.users_currently_in')}}</h2>
            <div class="position-relative">
                <canvas id="isHereChart" class=""></canvas> <!--Ez azért kell, hogy a helyén középre legyen rendezve a diagram -->
            </div>
            <script>
                var c1_xValues = ["{{__('site.here')}}", "{{__('site.left')}}"];
                var c1_yValues = [{{$here}}, {{$notHere}}];
                var c1_barColors = [
                 "#dc3545",
                 "#0d6efd",
                ];

                let chart1 = new Chart("isHereChart", {
                    type: "pie",
                    data: {
                    labels: c1_xValues,
                    datasets: [{
                        backgroundColor: c1_barColors,
                        data: c1_yValues
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
            <h2 style="text-align: center">{{__('site.validation_methods')}}</h2>
            <div class="position-relative">
                <canvas id="validationMethodsChart" class=""></canvas> <!--Ez azért kell, hogy a helyén középre legyen rendezve a diagram -->
            </div>
            <script>
                var c2_xValues = ["{{__('site.not_validate')}}", "{{__('site.code')}}", "{{__('site.fingerprint')}}", "{{__('site.both')}}"];
                var c2_yValues = [{{$notValidate}}, {{$validateWithCode}}, {{$validateWithFingerprint}}, {{$validateWithBoth}}];
                var c2_barColors = [
                    "#dc3545",
                    "#0d6efd",
                    "#198754",
                    "#ffc107",
                ];

                let chart2 = new Chart("validationMethodsChart", {
                    type: "pie",
                    data: {
                        labels: c2_xValues,
                        datasets: [{
                            backgroundColor: c2_barColors,
                            data: c2_yValues
                        }]
                    },
                    options: {
                        animation: {
                            duration: 0
                        },
                        title: {
                            display: true,
                            text: "{{__('site.validation_methods')}}"
                        },
                    }
                });

            </script>
        </div>
        <div class="col-12 col-md-6">
            <h2 style="text-align: center">{{__('site.users_with_entry_permission')}}</h2>
            <div class="position-relative">
                <canvas id="usersWithEntryPermissionChart" class=""></canvas> <!--Ez azért kell, hogy a helyén középre legyen rendezve a diagram -->
            </div>
            <script>
                var c3_xValues = ["{{__('site.has_entry_permission')}}", "{{__('site.not_has_entry_permission')}}"];
                var c3_yValues = [{{$hasEntryPermission}}, {{$notHasEntryPermission}}];
                var c3_barColors = [
                    "#dc3545",
                    "#0d6efd",
                ];

                let chart3 = new Chart("usersWithEntryPermissionChart", {
                    type: "pie",
                    data: {
                        labels: c3_xValues,
                        datasets: [{
                            backgroundColor: c3_barColors,
                            data: c3_yValues
                        }]
                    },
                    options: {
                        animation: {
                            duration: 0
                        },
                        title: {
                            display: true,
                            text: "{{__('site.users_with_entry_permission')}}"
                        }
                    }
                });

            </script>
        </div>
        <div class="col-12 col-md-6">
            <h2 style="text-align: center">{{__('site.user_group_by_roles')}}</h2>
            <div class="position-relative">
                <canvas id="usersByRoleChart" class=""></canvas> <!--Ez azért kell, hogy a helyén középre legyen rendezve a diagram -->
            </div>
            <script>
                var c4_xValues = ["admin", "employee", "user"];
                var c4_yValues = [{{$adminRole}}, {{$employeeRole}}, {{$userRole}}];
                var c4_barColors = [
                    "#dc3545",
                    "#0d6efd",
                    "#198754"
                ];

                let chart4 = new Chart("usersByRoleChart", {
                    type: "pie",
                    data: {
                        labels: c4_xValues,
                        datasets: [{
                            backgroundColor: c4_barColors,
                            data: c4_yValues
                        }]
                    },
                    options: {
                        animation: {
                            duration: 0
                        },
                        title: {
                            display: true,
                            text: "{{__('site.users_by_role')}}"
                        }
                    }
                });

            </script>
        </div>
        <div class="col-12 col-md-6">
            <h2 style="text-align: center">{{__('site.system_side_operations')}}</h2>
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
                                <a type="button" class="btn btn-danger"  onclick="triggerCancelOperationDialog({{$current->id}})" data-toggle="tooltip" title="{{ __('site.cancel_operation') }}"data-toggle="tooltip" title="{{ __('site.cancel_operation') }}"><i class="bi bi-x-octagon-fill"></i></a>
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
                    <a type="button" class="btn btn-warning d-inline-block" href="{{ route('set-entry-enabled') }}" role="button" data-toggle="tooltip" title="{{ __('site.enable_diable_entry') }}"><i class="bi bi-pencil-fill"></i></a>
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
                <a type="button" class="btn btn-danger" onclick="$('.modal').modal('hide')" id="cancel-operation-button" href="{{ route('cancel-operation') }}" role="button">{{ __('site.cancel_operation') }}</a>
                <a type="button" class="btn btn-primary" onclick="$('.modal').modal('hide')">{{ __('site.back') }}</a>
            </div>
        </div>
    </div>
</div>
<!-- FP Record Dialog -->
<div class="modal fade" id="fpRecordDialog" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="fingerprintRecordForm" action="{{ route('start-fp-registration')}}">
            <div class="modal-header">
                <h5 class="modal-title" id="idDeleteDialogTitle">{{ __('site.register_FP_manually') }}</h5>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="mr-sm-2" for="fingerID">Ujjlenyomat ID: </label>
                    <input id="fingerID" name="fingerID" type="number" min="1" max="127" step="1">
                </div>
            </div>
            <div class="modal-footer">
                <input type="button" class="btn btn-primary" id="recordFingerprintButton" value="{{ __('site.record') }}" onclick="checkFingerprintID()">
                <a type="button" class="btn btn-secondary" onclick="$('.modal').modal('hide')">{{ __('site.back') }}</a>
            </div>
            </form>
        </div>
    </div>
</div>
<!--A már felvett ujjlenyomatra figyelmeztető dialógus -->
<div class="modal fade" id="fpDuplicateWarnDialog" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="idDeleteDialogTitle">{{ __('site.confirmation' ) }}</h5>
                </div>
                <div class="modal-body">
                    <p>A regisztrálni kívánt ujjlenyomat azonosító már használatban van, biztos folytatja a műveletet?</p>
                </div>
                <div class="modal-footer">
                    <input type="button" class="btn btn-primary" id="recordFingerprintButton" value="{{ __('site.record') }}" onclick=document.getElementById("fingerprintRecordForm").submit();>
                    <a type="button" class="btn btn-secondary" onclick="$('.modal').modal('hide'); $('#fpRecordDialog').modal('show');">{{ __('site.back') }}</a>
                </div>
        </div>
    </div>
</div>
</body>
@include('footer')
</html>
