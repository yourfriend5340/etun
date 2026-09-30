
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>萬宇保全巡邏紀錄查詢系統</title>
    <link rel="shortcut icon" href="{{ asset('images/etuns.png') }}">

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>

<body>

<div class="head container-fluid" id="app">
    <nav class="navbar navbar-expand-md navbar-light">
        <a class="navbar-brand" href="{{ url('/') }}">
            <img src="{{ URL::asset('images/logo.png') }}" class="img-fluid">
        </a>
    </nav>
</div>

<div class="container">
    <div class="row justify-content-center mt-5">

        <div class="col-md-8">

            <div class="card">

                <div class="card-header">
                    {{ __('Login') }}系統，請輸入帳號或email
                </div>

                <div class="card-body">

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        {{-- 帳號 --}}
                        <div class="row mb-3 justify-content-center">

                            <label for="email"
                                   class="col-md-2 col-form-label text-md-end">
                                帳號
                            </label>

                            <div class="col-md-5">

                                <input id="email"
                                       type="text"
                                       class="form-control{{ $errors->has('name') || $errors->has('email') ? ' is-invalid' : '' }}"
                                       name="email"
                                       value="{{ old('name') ?: old('email') }}"
                                       required
                                       autofocus>

                                @if ($errors->has('name') || $errors->has('email'))
                                    <span class="invalid-feedback">
                                        <strong>
                                            {{ $errors->first('name') ?: $errors->first('email') }}
                                        </strong>
                                    </span>
                                @endif

                            </div>

                        </div>

                        {{-- 密碼 --}}
                        <div class="row mb-3 justify-content-center">

                            <label for="password"
                                   class="col-md-2 col-form-label text-md-end">
                                密碼
                            </label>

                            <div class="col-md-5">

                                <input id="password"
                                       type="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       name="password"
                                       required
                                       autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                            </div>

                        </div>

                        {{-- 登入按鈕 --}}
                        <div class="row mb-0 justify-content-center">

                            <div class="col-md-8 text-center">

                                <button type="submit" class="btn btn-primary">
                                    登入
                                </button>

                            </div>

                        </div>

                    </form>

                </div>

            </div>

            {{-- 隱私權 --}}
            <div class="row mt-2">

                <div class="col-md-12 text-end">

                    <a href="{{ route('privacy') }}"
                       target="_blank"
                       rel="noopener noreferrer">
                        隱私權保護政策
                    </a>

                </div>

            </div>

        </div>

    </div>
</div>

</body>
</html>
