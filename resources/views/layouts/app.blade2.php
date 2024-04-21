<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
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
        <nav class="navbar navbar-expand-md navbar-light">

                <a class="navbar-brand" href="{{ url('/') }}">
                    {{--{{ config('app.name', 'Laravel') }}--}}
                    <img src="{{ URL::asset('images/logo.png') }}" class="img-fluid">
                </a>
                {{--<button class="navbar-toggler width=30px justify" type="button" data-bs-toggle="collapse"
                 data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>--}}

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
                            <li class="nav-item dropdown bs-tog">
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

                                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <!--<a class="dropdown-item" href="{{ route('home') }}">會員管理系統</a>-->
                                    <a class="dropdown-item width=2000px" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
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

                <nav class="navbar navbar-expand-lg navbar-light mb-0 me-0 pe-2 justify-content-end w-10" style="background-color: #e3f2fd;">
             
                    {{--<button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">--}}
                     <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" 
                     aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">    
                        <span class="navbar-toggler-icon"></span>
                    </button>
        
        
                    <div class="offcanvas offcanvas-end" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel" tabindex="-1">
                        
                        <div class="offcanvas-header mb-0 pb-0" style="background-color: #e3f2fd;">
                            <h3 class="offcanvas-title" id="offcanvasExampleLabel">功能選單</h3>
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
                                                                    <li class="nav-item dropdown">
                                                                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                                                            使用者名稱：{{ Auth::user()->name }}
                                                                        </a>
                                        
                                                                        <div class="dropdown-menu" aria-labelledby="navbarDropdown" style="background-color: #e3f2fd;">
                                                                            <!--<a class="dropdown-item" href="{{ route('home') }}">會員管理系統</a>-->
                                                                            <a class="dropdown-item width=2000px" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                                                登出{{--{{ __('Logout') }}--}}
                                                                            </a>
                                                                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                                                                @csrf
                                                                            </form>
                                                                        </div>
                                                                    </li>
                                                                @endguest
                                                            </ul>
                            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                        </div>
        
                        <div class="offcanvas-body justify-content-start navbar-light" style="background-color: #e3f2fd;">--}}
                            <hr class="d-lg-none text-info m-0 p-0">
        
                            <ul class="navbar-nav flex-wrap flex-row">
                                <li class="nav-item mx-2">
                                    <a class="nav-link" aria-current="page" href="home"> 萬宇首頁</a>
                                </li>
                                
                                @can('group_admin')
                                <div class="nav-item dropdown mx-2">
                                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#">公告管理</a>
                                    
                                    <!-- 下拉式選單dropdown level1 -->
                                    <ul class="dropdown-menu" style="background-color: #e3f2fd;">

                                        <li><a class="dropdown-item" href={{route('announcement')}}>新增公告</a></li>

                                        <li><a class="dropdown-item" href="{{route('announcement_asc')}}">異動/刪除公告及總覽</a></li>
           
                                        
                                    </ul> <!-- end of level1 ul-->
                                
                                </div>              

                                @endcan

                            <div class="nav-item dropdown mx-2">
                                <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#">人事管理</a>
                                
                                <!-- 下拉式選單dropdown level1 -->
                                <ul class="dropdown-menu" style="background-color: #e3f2fd;">
                                    <li><a class="dropdown-item" href="{{ route('employee')}}">新增人事資料</a></li>
                                    <li><a class="dropdown-item" href="{{ route('employee_desc')}}">更新@can('group_admin')/刪除@endcan 人事資料及總覽</a></li>

                                    
                                </ul> <!-- end of level1 ul-->
    
                            </div>
                            
                            <div class="nav-item dropdown mx-2">
                                <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#">客戶管理</a>
                                
                                <!-- 下拉式選單dropdown level1 -->
                                <ul class="dropdown-menu" style="background-color: #e3f2fd;">
                                    <li><a class="dropdown-item" href={{route('customer')}}>新增客戶資料</a></li>
                                    @can('group_admin')
                                    <li><a class="dropdown-item" href={{route('customer_asc')}}>更新/刪除客戶資料及總覽</a></li>
                                    @endcan
         
                                </ul> <!-- end of level1 ul-->   
                            </div>
                            
                            @can('group_admin')
                            <div class="nav-item dropdown mx-2">
                                <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#">組織管理</a>
                                
                                <!-- 下拉式選單dropdown level1 -->
                                <ul class="dropdown-menu" style="background-color: #e3f2fd;">
                                    <li><a class="dropdown-item" href={{route('organize')}}>新增組織資料</a></li>
                                    <li><a class="dropdown-item" href="{{route('organize_asc')}}">更新/刪除組織資料及總覽</a></li>
         
                                </ul> <!-- end of level1 ul-->   
                            </div>

                            @endcan

                            <div class="nav-item dropdown mx-2">
                                <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#">排班與巡邏查詢</a>
                                
                                <!-- 下拉式選單dropdown level1 -->
                                <ul class="dropdown-menu" style="background-color: #e3f2fd;">
                                    <li class="nav-item mx-2">
                                        <a class="nav-link" href={{ route('schedule')}}>排班與巡邏機匯入</a>
                                    </li>
                                    
                                    <li class="nav-item mx-2">
                                        <a class="nav-link" href="{{ route('patrol_record')}}">巡邏紀錄查詢</a>
                                    </li>
         
                                </ul> <!-- end of level1 ul-->  
                            
                            </div>
           
                                <li class="nav-item mx-2">
                                    <a class="nav-link" href="#">APP管理</a>
                                </li>
                                

                                <div class="nav-item dropdown mx-2">
                                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#">客戶QR-CODE製作</a> 
                                    <!-- 下拉式選單dropdown level1 -->
                                    <ul class="dropdown-menu" style="background-color: #e3f2fd;">
                                        <li><a class="dropdown-item" href={{route('qrcode')}}>新增QR-CODE</a></li>
                                        <li><a class="dropdown-item" href={{route('qrcode_show')}}>更新、刪除QR-CODE</a></li>
                                        <li><a class="dropdown-item" href={{route('qrcode.setprint')}}>輸出QR-CODE</a></li>
                                    </ul> <!-- end of level1 ul-->  
                                </div>


                                @if(Gate::check('admin') || Gate::check('super_manager'))
                                <div class="nav-item dropdown mx-2">
                                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#">後台使用者管理</a>
                                    
                                    <!-- 下拉式選單dropdown level1 -->
                                    <ul class="dropdown-menu" style="background-color: #e3f2fd;">
                                        <li><a class="dropdown-item" href="register">註冊後台使用者</a></li>
                                        <li><a class="dropdown-item" href="user_asc">刪除及後台使用者清單</a></li>
             
                                    </ul> <!-- end of level1 ul-->   
                                </div>
                                @endif

                                <div class="nav-item dropdown mx-2">
                                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#">年後驗收項目</a>
                                    
                                    <!-- 下拉式選單dropdown level1 -->
                                    <ul class="dropdown-menu" style="background-color: #e3f2fd;">
                                        <li><a class="dropdown-item" href="#">離職、請假單輸出</a></li>
                                        <li><a class="dropdown-item" href="#">員工打卡功能</a></li>
             
                                    </ul> <!-- end of level1 ul-->   
                                </div>
                            <!--
                                <div class="nav-item dropdown mx-2">
                                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#">dropdown</a>
                                    
                                    
                                    <ul class="dropdown-menu" style="background-color: #e3f2fd;">
                                        <li><a class="dropdown-item" href="#">Layer1</a></li>
                                        <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle" href="#">Layer1_2</a>
        
                                            
                                            <ul class="dropdown-menu" style="background-color: #e3f2fd;">
                                                <li><a class="dropdown-item" href="#">Layer2</a></li>
                                                <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle" href="#">Layer2_1</a>
                                    
                                                    
                                                    <ul class="dropdown-menu" style="background-color: #e3f2fd;">
                                                        <li><a class="dropdown-item" href="#">Layer3</a></li>
                                                    </ul>    
                                            
                                                </li>
                                            </ul>
                                        </li> 
                                    </ul> 
                                
                                </div>-->
                            </ul>
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
