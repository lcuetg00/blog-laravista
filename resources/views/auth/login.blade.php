<!doctype html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ trans('panel.login') }}</title>

    {{-- Bootstrap + FontAwesome --}}
    @vite('resources/js/app.js')

    {{-- CSS del panel --}}
    @vite('resources/css/panel.css')
</head>

<body class="d-flex flex-column min-vh-100">
    {{-- Header del panel --}}
    <div class="container-fluid bg-primary sticky-top shadow">
        @include('panel.layouts.header')
    </div>

    <main class="flex-grow-1 d-flex align-items-center justify-content-center px-3 py-4">
        <div class="w-100 login-card">
            <div class="text-center mb-4">
                <h1 class="h3 fw-bold">{{ config('app.name') }}</h1>
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <h2 class="h5 fw-semibold mb-4 text-center">{{ trans('panel.login') }}</h2>

                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">{{ trans('panel.email') }}</label>
                            <input type="email"
                                class="form-control @if ($errors->has('email') || $errors->has('password')) is-invalid @endif" id="email"
                                name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                                aria-describedby="@error('email') email-error @enderror">
                            @error('email')
                                <div id="email-error" class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">{{ trans('panel.password') }}</label>
                            <input type="password"
                                class="form-control @if ($errors->has('email') || $errors->has('password')) is-invalid @endif" id="password"
                                name="password" required autocomplete="current-password"
                                aria-describedby="@error('password') password-error @enderror">
                            @error('password')
                                <div id="password-error" class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">{{ trans('panel.remember_me') }}</label>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary">
                                {{ trans('panel.sign_in') }}
                            </button>
                        </div>

                        @if (Route::has('password.request'))
                            <div class="text-center">
                                <a href="{{ route('password.request') }}" class="small text-decoration-none">
                                    {{ trans('panel.forgot_password') }}
                                </a>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </main>

    {{-- Footer del panel --}}
    @include('panel.layouts.footer')
</body>

</html>
