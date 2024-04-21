@extends('layouts.app')

@section('content')


<div class="container container-fluid">
    
    <form>  
        <div class="row justify-content-between mx-1">     
            <div class="col-md-auto align-self-center py-2 me-0 pe-0"><font color='red'>*</font>客戶名稱：</div>
            <div class="col-md-3 input-group-sm align-self-center py-2">
                {{--<input class="patrol w-100" name="name" id="name" placeholder="輸入客戶名稱">--}}
                    <select class="form-select form-select-sm" aria-label="Default select example" name="name" id="name">
                        {{--<option value="">請選擇</option>--}}
                        
                        @if ($errors->any())
                        <option value="{{ old('name') }}">{{ old('name') }}</option>
                        @endif
                        <option value="">請選擇</option>
                        @foreach($user_info as $user )
                        <option value="{{$user->firstname}}">{{$user->firstname}}</option>
                        @endforeach
                    </select>                
            </div> 
    
            <div class="col-md-2 align-self-center py-2"><font color='red'>*</font>起始巡邏:
                <input class="patrol" name="start_time" id="start_time" type="date">
            </div>

            <div class="col-md-2 align-self-center py-2"><font color='red'>*</font>結束巡邏:
                <input class="patrol" name="end_time" id="end_time" type="date">
            </div>

            <div class="col-md-2 align-self-center py-2">上傳日期:
                <input class="patrol" name="upload_time" id="upload_time" type="date">
            </div>

            <div class="col-md-auto align-self-center py-2 justify-content-end">
                <input class="btn btn-success" type="button" value="遞增查詢" onclick="submit_onclick_asc()">  
 
                <input class="btn btn-success" type="button" value="遞減查詢" onclick="submit_onclick_desc()">  
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

<div class="container container-fluid table-responsive table-bordered p-0">

    @if (isset($patrol_records))
    {{--
    <div class="col-md-auto d-flex justify-content-end">
        
        <a class="btn btn-secondary m-2" href="{{route('patrol_record_asc')}}" role="button">遞增</a>
        <a class="btn btn-secondary m-2" href="{{route('patrol_record_desc')}}" role="button">遞減</a>
    </div>
    --}}

    <table class="table table-bordered table-striped table-hover text-center align-middle table-responsive-md">
        <thead>
            <tr class="col text-left">
            {{--<td>ID</td>--}}
            <td>客戶名稱</td>
            <td>巡邏場所ID</td>
            <td>巡邏場所</td>
            <td>巡邏日期</td>
            <td>巡邏時間</td>
            <td>匯入方式</td>
            <td>上傳時間</td>

            
            </tr>
        </thead>

        <tbody>

            @foreach($patrol_records as $patrol_record )
                <tr>
                    {{--<td>{{$patrol_record->id}}</td>--}}
                    <td>{{$patrol_record->firstname}}</td>
                    <td>{{$patrol_record->patrol_RD_No}}</td>
                    <td class="text-start">{{$patrol_record->patrol_RD_Name}}</td>
                    <td>{{$patrol_record->patrol_RD_DateB}}</td>
                    <td>{{$patrol_record->patrol_RD_TimeB}}</td>
                    <td>{{$patrol_record->patrol_upload_user}}</td>
                    <td>{{$patrol_record->patrol_upload_date}}</td>
                    
                </tr>
            @endforeach
            
      </tbody>

    </table>

    <div class="d-inline-flex p-2 bd-highlight">
        
        {{ $patrol_records->links(); }}

    </div>
    @endif
   
</div>

@endsection


<script>
    function submit_onclick_asc(){

        var name = document.getElementById("name").value;
        var start_time = document.getElementById("start_time").value;
        var end_time = document.getElementById("end_time").value;
        var upload_time = document.getElementById("upload_time").value;

        if (name=="" || start_time=="" || end_time==""){
            //window.location.href="/patrol_record_asc";
            alert('名字 或 開始巡邏時間 或 結束巡邏時間不可為空值。');    
        }

        if (name!="" & start_time!="" & end_time!=""){      
            if (upload_time!=""){        
                if (upload_time<end_time){
                alert('"上傳時間" 不可能比"結束巡邏時間"還早發生，請重新選擇');
              
                }

                if (start_time>end_time)
                {alert('"結束巡邏時間" 不可能比 "起始巡邏時間" 還早發生，請重新輸入條件');
               
                }
                
                else{
                    if (upload_time>end_time){
                        query='/patrol_record/request/name='+name+'&start_time='+start_time+'&end_time='+end_time+'&upload_time='+upload_time;
                
                        if (confirm('你輸入的名字是：'+name+'\n開始巡邏時間：'+start_time+'\n結束巡邏時間：'+end_time+'\n上傳時間：'+upload_time+'\n\n確認後開始搜尋!!\n網址是：'+query)==true)
                        {window.location.href=query;}
                    }  
                }    
            } 
            
            else{
                upload_time=0;

                if (start_time>end_time){
                    alert('"結束巡邏時間" 不可能比 "起始巡邏時間" 還早發生，請重新輸入條件');
                }
                else{
                    query='/patrol_record/request/name='+name+'&start_time='+start_time+'&end_time='+end_time+'&upload_time='+upload_time;
                
                    if (confirm('你輸入的名字是：'+name+'\n開始巡邏時間：'+start_time+'\n結束巡邏時間：'+end_time+'\n上傳時間：'+upload_time+'\n\n確認後開始搜尋!!\n網址是：'+query)==true)
                    {window.location.href=query;}  
                }  
            }
        }
    }

    function submit_onclick_desc(){

        var name = document.getElementById("name").value;
        var start_time = document.getElementById("start_time").value;
        var end_time = document.getElementById("end_time").value;
        var upload_time = document.getElementById("upload_time").value;

        if (name=="" || start_time=="" || end_time==""){
            //window.location.href="/patrol_record_asc";
            alert('名字 或 開始巡邏時間 或 結束巡邏時間不可為空值。');    
        }

        if (name!="" & start_time!="" & end_time!=""){      
            if (upload_time!=""){        
                if (upload_time<end_time){
                alert('"上傳時間" 不可能比"結束巡邏時間"還早發生，請重新選擇');
              
                }

                if (start_time>end_time)
                {alert('"結束巡邏時間" 不可能比 "起始巡邏時間" 還早發生，請重新輸入條件');
               
                }
                
                else{
                    if (upload_time>end_time){
                        query='/patrol_record/request_desc/name='+name+'&start_time='+start_time+'&end_time='+end_time+'&upload_time='+upload_time;
                
                        if (confirm('你輸入的名字是：'+name+'\n開始巡邏時間：'+start_time+'\n結束巡邏時間：'+end_time+'\n上傳時間：'+upload_time+'\n\n確認後開始搜尋!!\n網址是：'+query)==true)
                        {window.location.href=query;}
                    }  
                }    
            } 
            
            else{
                upload_time=0;

                if (start_time>end_time){
                    alert('"結束巡邏時間" 不可能比 "起始巡邏時間" 還早發生，請重新輸入條件');
                }
                else{
                    query='/patrol_record/request_desc/name='+name+'&start_time='+start_time+'&end_time='+end_time+'&upload_time='+upload_time;
                
                    if (confirm('你輸入的名字是：'+name+'\n開始巡邏時間：'+start_time+'\n結束巡邏時間：'+end_time+'\n上傳時間：'+upload_time+'\n\n確認後開始搜尋!!\n網址是：'+query)==true)
                    {window.location.href=query;}  
                }  
            }
        }
      }
  
</script>