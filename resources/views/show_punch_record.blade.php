@extends('layouts.app')

@section('content')


<div class="row mx-1">
<p class="p-test mt-1 mb-0 fs-3">出勤紀錄查詢</p>
    <form>  
        <div class="row">     
            <div class="col-md-auto align-self-center py-2 me-0 pe-0"><font color='red'>*</font>員工姓名：</div>
            <div class="col-md-auto input-group-md align-self-center py-2 mx-0 px-0">
                <select class="form-select form-select-sm" aria-label="Default select example" name="name" id="name">
                    @if ($errors->any())
                    <option value="{{ old('name') }}">{{ old('name') }}</option>
                    @endif
                    <option value="">請選擇</option>
                    @foreach($user_info as $user )
                    <option value="{{$user->member_name}}">{{$user->member_name}}</option>
                    @endforeach
                </select> 
            </div> 
            <div class="col-md-auto align-self-center py-2 mx-1 px-0">
                <input class="patrol" name='inputName' id='inputName' type='text' placeholder="或輸入名字">  
            </div>
            <div class="col-md-auto align-self-center py-2"><font color='red'>*</font>開始時間:
                <input class="patrol" name="start_time" id="start_time" type="date">
            </div>

            <div class="col-md-auto align-self-center py-2"><font color='red'>*</font>結束時間:
                <input class="patrol" name="end_time" id="end_time" type="date">
            </div>

            <div class="col-md-auto align-self-center py-2 justify-content-end">
                <input class="btn btn-success" type="button" value="查詢" onclick="submit_onclick_asc()">  
            </div>
        </div>
    </form>
    <!-- Success message -->
    @if(Session::has('success'))
       <div class="alert alert-success">
            {{ Session::get('success') }}
        </div>
    @endif

    @if(Session::has('danger'))
        <div class="alert alert-danger">
          {{ Session::get('danger') }}
        </div>
    @endif
</div>


<div class="contain table-responsive table-bordered mx-1">

        <table class="table table-bordered table-striped table-hover text-center align-middle table-responsive-md">
            <thead>
                <tr class="col text-left">
                    
                    <td>ID</td>
                    <td>員工ID</td>
                    <td>員工姓名</td>
                    <td>種類</td>
                    <td>時間</td>
                </tr>
            </thead>

            <tbody>
            @if (isset($punch_records) && count($punch_records) != 0)
                @foreach($punch_records as $p )
                        <tr>
                            <td>{{$p->id}}</td>
                            <td>{{$p->member_sn}}</td>
                            <td >{{$p->member_name}}</td>
                            @if($p->type == 'IN')
                                <td>上班</td>
                            @else
                                <td>下班</td>
                            @endif
                            <td>{{$p->punchTime}}</td>   
                        </tr>
                @endforeach
            @else
                <tr>
                    <td colspan='5'>查無紀錄</td>
                </tr>
            @endif    
            </tbody>

        </table>
   
</div>

@endsection


<script>
    function submit_onclick_asc(){
        var name = document.getElementById("name").value;
        var start_time = document.getElementById("start_time").value;
        var end_time = document.getElementById("end_time").value;
        var inputName = document.getElementById("inputName").value;
        var queryName = "";

        if(inputName != "")
        {
            queryName = inputName;
        }
        else{
            queryName = name;
        }

        if (queryName == "" || start_time == "" || end_time == ""){
            alert('員工姓名 或 開始巡邏時間 或 結束巡邏時間不可為空值。');    
        }

        if (queryName!="" & start_time!="" & end_time!=""){      

            if (start_time>end_time)
            {
                alert('"開始時間" 不可能比 "結束時間" 還早發生，請重新輸入條件');
            }
            else{
                query='/punch_record/request/name='+queryName+'&start_time='+start_time+'&end_time='+end_time;
        
                if (confirm('你輸入的名字是：'+queryName+'\n開始時間：'+start_time+'\n結束時間：'+end_time+'\n\n確認後開始搜尋!!') == true)
                {window.location.href=query;}
            }    
        }
    }  


</script>