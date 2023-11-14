<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!--Ebben a fájlban található a "A legutóbbi belépési kísérlet" nézet sablonja.-->
<head>
    @include('head')
    <script>
        /*Az fetchAPI pollingért felelős metódus*/
        function fetchCurrent(){
            /*Létrehozunk HTML objektumokra mutató változókat*/
            let nameField = document.getElementById("name");
            let cardIDField = document.getElementById("cardID");
            let successfulField = document.getElementById("successful");
            let directionField = document.getElementById("direction");

            fetch('{{ route('current-poll') }}').then(response => response.json()).then(data => { //Le fetcheljük az adatot az API segítségével
                //Ellenőrizzük, hogy változnak az adatok és csak akkor frissítjük az oldalt!
                //Felhasználó neve
                if((data.name != nameField.innerText) || ((nameField.innerText.length == 0) || (nameField.innerText === null))){
                    nameField.innerText = data.name;
                }
                //Felhasználó kártya azononosítója (UID)
                if((data.cardID != cardIDField.innerText) || ((cardIDField.innerText.length == 0) || (cardIDField.innerText === null))){
                    cardIDField.innerText = data.cardID;
                }
                //A beléptetési próbálkozás sikeressége
                if((data.successful != successfulField.innerText) || ((successfulField.innerText.length == 0) || (successfulField.innerText === null))){
                    successfulField.innerText = data.successful;
                    if(data.successfulValue == 0){
                        successfulField.className = 'text-danger'
                    }else if(data.successfulValue == 1){
                        successfulField.className = "text-success"
                    }
                }
                //A beléptetési próbálkozás iránya
                if((data.direction != directionField.innerText) || ((directionField.innerText.length == 0) || (directionField.innerText === null))){
                    directionField.innerText = data.direction;
                    if(data.directionValue == 'out'){
                        directionField.className = 'text-danger'
                    }else if(data.directionValue == 'in'){
                        directionField.className = "text-success"
                    }
                }
                //A felhasználó profilképe
                if(data.profilePicture != pictureField.getAttribute("src")){
                    if ((data.profilePicture.length == 0) || (data.profilePicture === null)){
                        pictureField.setAttribute("src", "");
                    }else {
                        pictureField.setAttribute("src", data.profilePicture);
                    }
                }
            })
        }
       setInterval(()=>{ //A setInterval() metódus segítségével megadjuk ms-ban, hogy bizonyos ídőközönként kérje le az adatokat a szerverről
            fetchCurrent()
        },2000)
    </script>
</head>
<body {{$current_user->darkMode ? 'data-bs-theme=dark' : ''}} onload="fetchCurrent()"> <!--Az onload attribútum segítségével már az oldal betöltésekkor meghívjuk a fetchCurrent() metódust! -->
<h1 style="margin-left: 10px">{{__('site.theLastEntryAttempt')}}</h1>
<div class="container mt-4">
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
    <div class="col">
        <img class="image rounded-circle" id="pictureField" alt="" src="" alt="profile_image" style="width: 200px;height: 200px; padding: 10px; margin: 0px; object-fit: cover; "/>
    </div>
    <div class="col">
        <h2 id="name">{{__('site.loading')}}</h2>
        <h2 id="cardID"></h2>
        <h2 id="successful"></h2>
        <h2 id="direction"></h2>
    </div>
</div>
</div>
</body>
@include('footer')
</html>
