<!--Ebben a fájlban a menü sablonja található.-->
{{App::setLocale($current_user->language)}} <!--Beállítjuk a lokalizációt-->
<header class="p-3 mb-3 border-bottom">
    <div>
        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
            <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
                <li><a href="{{ route('users') }}" class="nav-link px-2 link-body-emphasis">{{ __('site.users') }}</a></li>
                @if($current_user->role == 'admin' or $current_user->role == 'employee')
                    <li><a href="{{ route('logs') }}" class="nav-link px-2 link-body-emphasis">{{ __('site.logs') }}</a></li>
                    @if($current_user->role == 'admin') <!--Ez azért van, hogy a dashboardot csak admin tudja megnyitni -->
                        <li><a href="{{ route('dashboard') }}" class="nav-link px-2 link-body-emphasis">{{ __('site.dashboard') }}</a></li>
                    @endif
                @endif
            </ul>

            <div class="dropdown text-end">
                <a href="#" class="d-block {{$current_user->darkMode ? 'link-light' : 'link-dark'}} text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    @if($current_user->picture == "")
                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                            <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                        </svg>
                    @else
                        <img class="rounded-circle shadow-4-strong" alt="{{ __('site.picture') }}" src="{{asset('/storage/pictures/profile/'.$current_user->picture)}}" alt="profile_image" style="width: 50px;height: 50px; padding: 10px; margin: 0px; object-fit: cover; "/>
                    @endif
                </a>
                <ul class="dropdown-menu text-small"> <!-- A lenyíló menü konténer eleme -->
                    <li><p class="dropdown-header">Hello, {{$current_user->name}}</p></li> <!-- A bejelentkezett felhasználót üdvözlő bekezdés -->
                    <div class="dropdown-divider"></div> <!-- A lenyíló menü elválasztó eleme -->
                        <li>
                            <a href="{{route('set-dark-mode')}}" class="dropdown-item"> {{ __('site.darkMode') }}: {{$current_user->darkMode ? __('site.on') : __('site.off')}}</a> <!-- A sötét mód beállításáért felelős hivatkozás -->
                        </li>
                        <li><a class="dropdown-item" href="{{ route('users-show', [$userId = $current_user->id]) }}">{{__('site.my_profile')}}</a></li> <!-- Az aktuális feléhasználó profiljára irányuló hivatkozás-->
                        <div class="dropdown-divider"></div> <!-- A lenyíló menü elválasztó eleme -->
                        <form id="logout-form" action="{{ route('logout') }}" method="POST"> <!-- Az oldalról történő kiléptetésért felelős űrlap -->
                            @csrf <!-- Itt található a CSRF token, enélkül a Laravel nem fogad HTTP POST metódussal küldött lekérdezéseket -->
                        </form>
                    <li><a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">{{__('auth.logout')}}</a></li> <!-- A kijelentkezés hivatkozása -->
                </ul>
            </div>
        </div>
    </div>
</header>
<!--Csinálunk egy <noscript> figyelmeztetést arra az esetre, ha a felhasználónál nem lenne javascript képes eszköz -->
<noscript>
    <div class="alert alert-warning mx-2" role="alert">
        {{__('site.no_script')}}
    </div>
</noscript>
