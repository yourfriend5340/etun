
<!DOCTYPE html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>ETUN</title>

        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>
        <!-- Styles -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    </head>

    <body>
        {{--<header class="header">@include('header')</header>--}}
        <div class="divhead">
            <a class="navbar-brand" href="https://www.google.com">
                <img src="{{ URL::asset('images/logo.png') }}" class="img-fluid">
            </a>
        </div> 

        <body class="antialiased">
            <div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center py-4 sm:pt-0">
                @if (Route::has('login'))
                    <div class="hidden fixed top-0 right-0 px-6 py-4 sm:block">
                        @auth
                            <a href="{{ url('/home') }}" class="text-sm text-gray-700 dark:text-gray-500 underline">Home</a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm text-gray-700 dark:text-gray-500 underline">登入</a>
    
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="ml-4 text-sm text-gray-700 dark:text-gray-500 underline">註冊</a>
                            @endif
                        @endauth
                    </div>
                @endif

        <div class="container container-fluid table-responsive table-bordered p-0">
            <p class="p-test mt-1 mb-0 fs-3">人事資料</p>
            <table class="table table-bordered table-striped table-hover text-left align-middle vh-100">
                <thead>
                    <tr>
                    <td>ID</td>
                    <td>姓名</td>
                    <td>電話</td>
                    <td>帳號</td>
                    <td>密碼</td>
                    <td>建立時間</td>
                    <td>更新時間</td>
                    <td>備註</td>
                    </tr>
                </thead>
              
                <tbody>  
                    @foreach($employees as $employee )
                        <tr>
                            <td>{{$employee->id}}</td>
                            <td>{{$employee->member_name}}</td>
                            <td>{{$employee->member_phone}}</td>
                            <td>{{$employee->member_account}}</td>
                            <td>{{$employee->member_password_text}}</td>
                            <td>{{$employee->created_at}}</td>
                            <td>{{$employee->updated_at}}</td>
                            <td>這是測試用</td>
                        </tr>
                    @endforeach
              </tbody>
            </table>
            {{ $employees->links() }}  
        </div>
        




        <footer class="foot align-items-end">@include('footer')</footer>
        
    </body>
</html>


