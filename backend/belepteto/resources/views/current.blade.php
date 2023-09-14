<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!--Ebben a fájlban található a felhasználók nézet sablonja.-->
<head>
    @include('head')
    <script>
        /*Az fetchAPI pollingért felelős metódus*/
        function fetchCurrent(){
            let nameField = document.getElementById("name");
            let cardIDField = document.getElementById("cardID");
            let successfulField = document.getElementById("successful");
            let directionField = document.getElementById("direction");

            fetch('{{ route('current-poll') }}').then(response => response.json()).then(data => {
                if((data.name != nameField.innerText) || ((nameField.innerText.length == 0) || (nameField.innerText.length === null))){
                    nameField.innerText = data.name;
                }
                if((data.cardID != cardIDField.innerText) || ((cardIDField.innerText.length == 0) || (cardIDField.innerText.length === null))){
                    cardIDField.innerText = data.cardID;
                }
                if((data.successful != successfulField.innerText) || ((successfulField.innerText.length == 0) || (successfulField.innerText.length === null))){
                    successfulField.innerText = data.successful;
                }
                if((data.direction != directionField.innerText) || ((directionField.innerText.length == 0) || (directionField.innerText.length === null))){
                    directionField.innerText = data.direction;
                }
            })

        }
       setInterval(()=>{
            fetchCurrent()
        },1000)
    </script>
</head>
<body {{$current_user->darkMode ? 'data-bs-theme=dark' : ''}} onload="fetchCurrent()">
<h1 style="margin-left: 10px">{{__('site.theLastEntryAttempt')}}</h1>
<div class="container mt-4">
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
    <div class="col">
        <svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
            <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
        </svg>
    </div>
    <div class="col">
        <h2 id="name"></h2>
        <h2 id="cardID"></h2>
        <h2 id="successful"></h2>
        <h2 id="direction"></h2>
    </div>
</div>
</div>
</body>
@include('footer')
</html>
