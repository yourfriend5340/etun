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
                    {{--{{ config('app.name', 'Laravel') }}--}}
                    <img src="{{ URL::asset('images/logo.png') }}" class="img-fluid">
                </a>
                {{--<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>--}}


            
        </nav>
    </div>
        <div class="container">
            <div class="row justify-content-center mt-5">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">{{ __('Login') }}系統，請輸入帳號或email</div>
        
                        <div class="card-body">
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                
                                {{--email登入
                                <div class="row mb-3">
                                    <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>
        
                                    <div class="col-md-6">
                                        <input id="email" type="email" class="form-control 
                                        @error('email') 
                                        is-invalid 
                                        @enderror" 
                                        
                                        name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
        
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                --}}

                                {{--使用email及name登入--}}
                                <div class="row mb-3">
                                    <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('帳號') }}</label>
        
                                    <div class="col-md-6">
                                        <input id="email" type="text" 
                                        class="form-control{{ $errors->has('name') || $errors->has('email') ? ' is-invalid' : '' }}"
                                        name="email" value="{{ old('name') ?: old('email') }}" required autofocus>
 
                                        @if ($errors->has('name') || $errors->has('email'))
                                            <span class="invalid-feedback">
                                                <strong>{{ $errors->first('name') ?: $errors->first('email') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
        
                                <div class="row mb-3">
                                    <label for="password" class="col-md-4 col-form-label text-md-end">密碼{{--{{ __('Password') }}--}}</label>
        
                                    <div class="col-md-6">
                                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
        
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
        
                               {{-- <div class="row mb-3">
                                    <div class="col-md-6 offset-md-4">
                                        <div class="form-check">
                                             <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
        
                                           <label class="form-check-label" for="remember">
                                                {{ __('Remember Me') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                --}}
                                
                                <div class="row mb-0">
                                    <div class="col-md-8 offset-md-6 justify-content-center align-items-center">
                                        <button type="submit" class="btn btn-primary">
                                            登入{{--{{ __('Login') }}--}}
                                        </button>
        
                                        {{--@if (Route::has('password.request'))
                                            <a class="btn btn-link" href="{{ route('password.request') }}">
                                                {{ __('Forgot Your Password?') }}
                                            </a>
                                        @endif--}}
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>        


</body>
</html>





