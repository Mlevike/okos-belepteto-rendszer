<!--Ebben a fájlban a menü sablonja található.-->
{{App::setLocale($current_user->language)}} <!--Beállítjuk a lokalizációt-->
<header class="p-3 mb-3 border-bottom">
    <div>
        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
            <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
               <!-- <li><a href="{{ route('dashboard') }}" class="nav-link px-2 link-secondary">{{ __('site.dashboard') }}</a></li>--> <!--Ideiglenesen elrejtve-->
                <li><a href="{{ route('users') }}" class="nav-link px-2 link-body-emphasis">{{ __('site.users') }}</a></li>
                @if($current_user->role == 'admin' or $current_user->role == 'employee')
                <li><a href="{{ route('logs') }}" class="nav-link px-2 link-body-emphasis">{{ __('site.logs') }}</a></li>
                @endif
            </ul>

            <div class="dropdown text-end">
                <a href="#" class="d-block link-dark text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    @if($current_user->picture == "")
                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                            <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                        </svg>
                    @else
                        <img class="rounded-circle shadow-4-strong" alt="{{ __('site.picture') }}" src="{{asset('/storage/pictures/profile/'.$current_user->picture)}}" alt="profile_image" style="width: 50px;height: 50px; padding: 10px; margin: 0px; object-fit: cover; "/>
                    @endif
                </a>
                <ul class="dropdown-menu text-small">
                    <li><p class="dropdown-header">Hello, {{$current_user->name}}</p></li>
                    <div class="dropdown-divider"></div>
                    <li><a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">{{__('auth.logout')}}</a></li>
                </ul>
            </div>
        </div>
    </div>
    <form id="logout-form" action="{{ route('logout') }}" method="POST">
        @csrf
    </form>
</header>
