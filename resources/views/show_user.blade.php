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
  
        <a class="btn btn-secondary m-2" href="{{route('user_asc')}}" role="button">遞增</a>

        <a class="btn btn-secondary m-2" href="{{route('user_desc')}}" role="button">遞減</a>

    </div>
    <table class="table table-bordered table-striped table-hover text-center align-middle table-responsive-md">
        <thead>
            <tr class="col text-left">
            <td>ID</td>
            <td>使用者群組</td>

            <td>姓名</td>
            <td>email</td>

            
            </tr>
        </thead>
      
        <tbody>  
            @foreach($users as $user )

                <tr>
                    <td>{{$user->id}}</td>

                    @if ($user->user_group_id==1)
                    <td>系統管理者</td>    

                    @elseif ($user->user_group_id==2)
                    <td>最高管理者</td>    

                    @elseif ($user->user_group_id==3)
                    <td>一般管理者</td>    
                    
                    @elseif($user->user_group_id==4)
                    <td>使用者</td>    

                    @else
                    <td>使用者</td> 
                    @endif
 
                    <td>{{$user->name}}</td>
                    <td>{{$user->email}}</td>


                    <td>

                       {{-- <a href="{{route('employee.delete',['Delete_id'=> $employee->id])}}" class="btn btn-light btn-md active" role="button" aria-pressed="true" id={{$employee->id}}
                            onclick="submit_onclick()">刪除</a>
                        --}}
                        <input class="btn btn-light btn-md active" type="submit" value="刪除" onclick="submit_onclick({{$user->id}})">
                    </td>
                </tr>
            @endforeach
      </tbody>
    </table>
    <div class="d-inline-flex p-2 bd-highlight">
        {{ $users->links() }}  
    </div>
</div>

@endsection


<script>

    function submit_onclick(id){

        if (confirm('確定要刪除ID： '+id+' 號資料嗎？')==true)
        {window.location.href="/user/delete/"+id;}

    }

    function submit_onclick_update(id){

        if (confirm('確定要更新ID： '+id+' 號資料嗎？')==true)
        {window.location.href="/user/update/"+id;}

    }

</script>