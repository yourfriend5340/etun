<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" >
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>萬宇保全巡邏紀錄查詢系統</title>
    <link rel="icon" href="{{ url('images/etuns.png') }}">
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    {{--<link rel="stylesheet" href="/css/app.css">--}}


</head>
<body>

    <div class="divheader" id="app">
        <nav class="navbar navbar-expand-lg navbar-light">
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="{{ URL::asset('images/logo.png') }}" class="img-fluid">
            </a>


                <div class="collapse navbar-collapse" id="navbarSupportedContent">

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto me-5">
                        <!-- Authentication Links -->
                        @guest
                        @if (Route::has('login'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">會員登入{{ __('Login') }}</a>
                            </li>
                        @endif
                                
                        {{--@if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">會員註冊{{ __('Register') }}</a>
                            </li>
                        @endif
                        --}}
                        @else
                            <li class="nav-item dropdown justify-content-end">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    使用者名稱：
                                    @can('admin')
                                    系統管理者:
                                    @elsecan('super_manager')
                                    最高管理者:
                                    @elsecan('manager')
                                    管理者:
                                    @else
                                    使用者:
                                    @endcan
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu" aria-labelledby="navbarDropdown" id="navusrlogout">
                                    <!--<a class="dropdown-item" href="{{ route('home') }}">會員管理系統</a>-->
                                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        登出{{--{{ __('Logout') }}--}}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>           
        </nav>


        <!-- Nav bar offcanvas -->
        <nav class="navbar navbar-expand-lg navbar-light pe-2 justify-content-end" style="background-color: #e3f2fd;">
             
            <button class="navbar-toggler my-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
                    
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasExampleLabel">
                
                <div class="offcanvas-header">
                    <div class="row">
                        <button type="button" class="btn-close text-reset ms-auto m-1" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    <h5 class="h5 mt-0 px-2 justify-content-center align-items-center">功能選項</h5>
                    <hr class="hr py-0 my-0">
                                            
                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto me-5 px-2">
                        <!-- Authentication Links -->
                        @guest
                        @if (Route::has('login'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">會員登入{{ __('Login') }}</a>
                            </li>
                        @endif

                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    使用者名稱：
                                    @can('admin')
                                    系統管理者:
                                    @elsecan('super_manager')
                                    最高管理者:
                                    @elsecan('manager')
                                    管理者:
                                    @else
                                    使用者:
                                    @endcan
                                    {{ Auth::user()->name }}
                                </a>
                          
                                <div class="dropdown-menu" aria-labelledby="navbarDropdown" id="usrlogout">
                                   
                                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        登出
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                    </div>

                </div>{{--end head--}}

                <div class="offcanvas-body navbar-expand-lg text-left">

                    <div class="dropdown my-1 px-0">
                        <a href="{{route('home')}}" class="btn" tabindex="-1" role="button">首頁</a>
                    </div>    
                     
                    @can('group_admin')
                    <div class="dropdown my-1 px-0">
                        <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown">
                            公告管理
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="background-color: #e3f2fd;">
                            <li><a class="dropdown-item" href="{{route('announcement')}}">新增公告</a></li>
                            <li><a class="dropdown-item" href="{{route('announcement_desc')}}">異動/刪除 公告總覽</a></li>
                            <li><a class="dropdown-item" href="{{route('contact.group')}}">新增群組</a></li>
                            <li><a class="dropdown-item" href="{{route('contact_group.asc')}}">異動/刪除 群組總覽</a></li>
                            <li><a class="dropdown-item" href="{{route('contact')}}">新增聯絡資訊</a></li>
                            <li><a class="dropdown-item" href="{{route('contact.asc')}}">異動/刪除 資訊總覽</a></li>
                        </ul>
                    </div>            
                    @endcan
           
                    <div class="dropdown my-1 px-0">
                        <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" style="background-color: #e3f2fd;">
                            人事管理
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="background-color: #e3f2fd;">
                            <li><a class="dropdown-item" href="{{route('employee')}}">新增人事資料</a></li>
                            @can('group_admin')<li><a class="dropdown-item" href="{{route('clocksalary')}}">鐘點人員薪資修改</a></li>@endcan
                            <li><a class="dropdown-item" href="{{route('employee_desc')}}">更新@can('group_admin')/刪除@endcan 人事資料及總覽</a></li>

                        </ul>
                    </div>

                    <div class="dropdown my-1 px-0">
                        <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" style="background-color: #e3f2fd;">
                            客戶管理
                        </button>
                        <ul class="dropdown-menu px-0" aria-labelledby="dropdownMenuButton" style="background-color: #e3f2fd;">
                            <li><a class="dropdown-item" href="{{route('customer')}}">新增客戶資料</a></li>
                            <li><a class="dropdown-item" href="{{route('customer_asc')}}">更新/刪除客戶資料及總覽</a></li>
                            <li><a class="dropdown-item" href="{{route('customer_group')}}">客戶群組定義</a></li>
                            <li><a class="dropdown-item" href="{{route('customer_group_asc')}}">更新/刪除客戶群組及總覽</a></li>
                            <li><a class="dropdown-item" href="{{route('customer.active')}}">客戶狀態定義</a></li>
                            <li><a class="dropdown-item" href="{{route('customer_active_asc')}}">更新/刪除客戶狀態及總覽</a></li>
                        </ul>
                    </div>

                    @can('group_admin')      
                    <div class="dropdown my-1 px-0">
                        <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" style="background-color: #e3f2fd;">
                            組織管理
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="background-color: #e3f2fd;">
                            <li><a class="dropdown-item" href="{{route('organize')}}">新增組織資料</a></li>
                            <li><a class="dropdown-item" href="{{route('organize_asc')}}">更新/刪除組織資料及總覽</a></li>
                        </ul>
                    </div>
                    @endcan    

                    <div class="dropdown my-1 px-0">
                        <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" style="background-color: #e3f2fd;">
                            排班與巡邏查詢
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="background-color: #e3f2fd;">
                            <li><a class="dropdown-item" href="{{route('patrol_record')}}">巡邏紀錄查詢</a></li>
                            <li><a class="dropdown-item" href="{{route('schedule')}}">排班與巡邏匯入匯出</a></li>
                        </ul>
                    </div>
           
                    <div class="dropdown my-1 px-0">
                        <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" style="background-color: #e3f2fd;">
                            QR-code管理
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="background-color: #e3f2fd;">
                            <li><a class="dropdown-item" href="{{route('qrcode')}}">新增QR-code</a></li>
                            <li><a class="dropdown-item" href={{route('qrcode.setprint')}}>輸出QR-code</a></li>
                            <li><a class="dropdown-item" href="{{route('qrcode_show')}}">更新、刪除QR-code</a></li>

                        </ul>
                    </div>

                    @if(Gate::check('admin') || Gate::check('super_manager'))
                    <div class="dropdown my-1 px-0">
                        <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" style="background-color: #e3f2fd;">
                            後台使用者管理
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="background-color: #e3f2fd;">
                            <li><a class="dropdown-item" href="register">註冊後台使用者</a></li>
                            <li><a class="dropdown-item" href="user_asc">刪除及後台使用者清單</a></li>
                        </ul>
                    </div>
                    @endif

                    <div class="dropdown my-1 px-0">
                        <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" style="background-color: #e3f2fd;">
                            表單功能
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="background-color: #e3f2fd;">
                            <li><a class="dropdown-item" href={{route('table')}}>離職、請假單輸出</a></li>

                            {{--
                            <li><a class="dropdown-item" href="#">員工打卡功能(API)</a></li>
                            <li><a class="dropdown-item" href="#">員工巡邏功能(API)</a></li>
                            <li><a class="dropdown-item" href="#">APP管理？？</a></li>
                            --}}
                        </ul>
                    </div>

                </div>
            </div>
        </nav>
    


        <main class="main_page bg-white" id="main">
            @yield('content')
        </main>
    
        <footer class="footer align-items-end bg-white">@include('footer')</footer>

    </div>
</body>
</html>
