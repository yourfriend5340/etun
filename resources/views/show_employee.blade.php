@extends('layouts.app')

@section('content')

<div class="row table-responsive table-bordered mx-1">
    <p class="p-test mt-1 mb-0 fs-3">人事資料總覽</p>
    
    <form>
    <div class="row justify-content-between">
        <div class="col-md-auto align-self-center py-2 justify-content-end">    
            <div class="col-md-auto border-0 d-inline-flex align-items-center py-2">員工姓名：</div>
            <div class="col-md-4 border-0 d-inline-flex align-items-start align-items-center">
                <input class="place d-inline-flex" id="empName" placeholder="輸入姓氏或全名">

                <input class="btn btn-success ms-3" type="submit" value="確認送出" onclick="submit_onclick_requestName()">
            </div>
        </div>
        
        @if (!isset($fromName))
        <div class="col-md-5"></div>
        <div class="col-md-auto align-self-center py-2 justify-content-end">
            <a class="btn btn-secondary m-2" href="{{route('employee_asc')}}" role="button">遞增</a>
            <a class="btn btn-secondary m-2" href="{{route('employee_desc')}}" role="button">遞減</a>
        </div>
        @endif
    </div>
    </form>


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

    function submit_onclick_requestName(){
   
        var inputName = document.getElementById("empName");
        var name = inputName.value;
        
        if (name!='')
        {
            window.location.href="/employee/requestName/"+name;
            window.event.returnValue=false;
        }       
    }

</script>