<!--Ebben a fájlban a menü sablonja található.-->
<header class="p-3 mb-3 border-bottom">
    <div>
        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
            <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
                <li><a href="{{ route('dashboard') }}" class="nav-link px-2 link-secondary">{{ __('site.dashboard') }}</a></li>
                <li><a href="{{ route('users') }}" class="nav-link px-2 link-body-emphasis">{{ __('site.users') }}</a></li>
                <li><a href="{{ route('logs') }}" class="nav-link px-2 link-body-emphasis">{{ __('site.logs') }}</a></li>
            </ul>

            <div class="dropdown text-end">
                <a href="#" class="d-block link-dark text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle"></i>
                </a>
                <ul class="dropdown-menu text-small">
                    <li><a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
    <form id="logout-form" action="{{ route('logout') }}" method="POST">
        @csrf
    </form>
</header>
