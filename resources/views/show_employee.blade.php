@extends('layouts.app')

@section('content')
{{--<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">會員管理系統{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    你已經登入{{ __('You are logged in!') }}
                </div>

                <div class="card-body alert">
                    
                        <a class="alert-link text-success" href="{{route('logout')}}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            點此處登出
                        </a>
                    
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>

                    
                </div>
            </div>
        </div>
    </div>
</div>--}}

<div class="container container-fluid table-responsive table-bordered p-0">
    <div class="d-flex justify-content-end">
  
        <a class="btn btn-secondary m-2" href="{{route('employee_asc')}}" role="button">遞增</a>

        <a class="btn btn-secondary m-2" href="{{route('employee_desc')}}" role="button">遞減</a>

    </div>
    <table class="table table-bordered table-striped table-hover text-center align-middle table-responsive-md">
        <thead>
            <tr class="col text-left">
            <td>ID</td>
            <td>職工編號</td>
            <td>姓名</td>
            <td>電話</td>
            <td>帳號</td>
            <td>密碼</td>
            <td>更新</td>

            @can('admin')
            <td>刪除</td>
            @elsecan('super_manager')
            <td>刪除</td>
            @endcan
            
            </tr>
        </thead>
      
        <tbody>  
            @foreach($employees as $employee )
                <tr>
                    <td>{{$employee->id}}</td>
                    <td>{{$employee->member_sn}}</td>
                    <td>{{$employee->member_name}}</td>
                    <td>{{$employee->member_phone}}</td>
                    <td>{{$employee->member_account}}</td>
                    <td>{{$employee->member_password_text}}</td>
                    <td>
                        <input class="btn btn-light btn-md active" type="submit" value="更新" onclick="submit_onclick_request({{$employee->member_sn}})">
                    </td>
                    @can('admin')
                    <td>
                        <input class="btn btn-light btn-md active" type="button" value="刪除" onclick="submit_onclick({{$employee->member_sn}})">
                    </td>

                    @elsecan('super_manager')
                    <td>
                        <input class="btn btn-light btn-md active" type="button" value="刪除" onclick="submit_onclick({{$employee->member_sn}})">
                    </td>
                    @endcan
                </tr>
            @endforeach
      </tbody>
    </table>
    <div class="d-inline-flex p-2 bd-highlight">
        {{ $employees->links() }}  
    </div>
</div>

@endsection


<script>

    function submit_onclick(id){

        if (confirm('確定要刪除ID： '+id+' 號資料嗎？')==true)
        {window.location.href="/employee/delete/"+id;}

    }

    function submit_onclick_request(id){

        if (confirm('確定要調閱ID： '+id+' 號資料嗎？')==true)
        {window.location.href="/employee/request/"+id;}

    }

</script>