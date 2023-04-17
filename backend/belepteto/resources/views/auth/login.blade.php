<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('head')
    <style>
        html,
        body {
            height: 100%;
        }

        body {
            display: flex;
            align-items: center;
            padding-top: 40px;
            padding-bottom: 40px;
            background-color: #f5f5f5;
        }

        .form-signin {
            max-width: 330px;
            padding: 15px;
        }

        .form-signin .form-floating:focus-within {
            z-index: 2;
        }

        .form-signin input[type="email"] {
            margin-bottom: -1px;
            border-bottom-right-radius: 0;
            border-bottom-left-radius: 0;
        }

        .form-signin input[type="password"] {
            margin-bottom: 10px;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }
        .login-errors{
            padding: 5px;
            margin-top: 10px;
        }

    </style>
</head>
<body class="text-center">
<main class="form-signin w-50 m-auto">
    <form action="" method="post">
        @csrf
        <h1 class="h3 mb-3 fw-normal">{{ __('auth.please_login') }}</h1>

        <div class="form-floating">
            <input type="email" class="form-control" id="floatingInput" placeholder="name@example.com" name="email">
            <label for="floatingInput">{{ __('auth.email') }}</label>
        </div>
        <div class="form-floating">
            <input type="password" class="form-control" id="floatingPassword" placeholder="{{ __('auth.password') }}" name="password">
            <label for="floatingPassword">{{ __('auth.password') }}</label>
        </div>

        <div class="checkbox mb-3">
            <label>
                <input type="checkbox" value="remember-me"> {{ __('auth.remember') }}
            </label>
        </div>
        <button class="w-100 btn btn-lg btn-primary" type="submit">{{ __('auth.login') }}</button>
        @if ($errors->any())
            <div class="login-errors alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
            </div>
        @endif
    </form>
</main>
</body>
</html>
