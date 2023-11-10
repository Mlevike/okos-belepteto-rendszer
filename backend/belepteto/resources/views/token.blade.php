<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('head')
    <script>
      function updateClipboard(){ //A vágólapra történő másolásért felelő függvény
          try {
              navigator.clipboard.writeText(document.getElementById("copy-target").value).then(
                  console.log("Sikeres másolás!")
          );
          }catch(ex){
              alert("Sikertelen másolás!")
              console.log("Sikertelen másolás!")
          }
      }
    </script>
</head>
<body {{$current_user->darkMode ? 'data-bs-theme=dark' : ''}}>
@include('header', ['$current_user'=>$current_user])
<main class="p-2">
    <h1>{{__('site.newToken')}}</h1>
    <h5>{{__('site.newTokenText')}}</h5>
    <div class="input-group w-100">
        <input type="text" value="{{$hash}}" class="form-control" id="copy-target"/>
        <button class="btn btn-primary" type="button" id="copy-button" data-toggle="tooltip" data-placement="button" title="{{__("site.copy_to_clipboard")}}" onclick="updateClipboard()">
            <i class="bi bi-clipboard"></i>
        </button>
    </div>
    <a type="button" class="btn btn-primary mt-2 mb-2" href="javascript:history.back()" role="button">{{ __('site.back') }}</a> <!--Erre majd kell Laraveles megoldás is-->
</main>
@include('footer')
</body>
</html>
