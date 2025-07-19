@extends('layouts.app')

@section('content')


<div class="row mx-1">
<p class="p-test mt-1 mb-0 fs-3">離職、請假單總覽</p>
    <form>  
        <div class="row">     
            <div class="col-md-auto align-self-center py-2 me-0 pe-0"><font color='red'>*</font>表單名稱：</div>
            <div class="col-md-auto input-group-md align-self-center py-2">

                <select class="form-select form-select-sm" aria-label="Default select example" name="name" id="name">
                    @if ($errors->any())
                    <option value="{{ old('name') }}">{{ old('name') }}</option>
                    @endif
                    <option value="">請選擇</option>
                    
                    <option value="請假">請假</option>
                    <option value="離職">離職</option>
                    
                </select>                
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

    @if (isset($results))
        <table class="table table-bordered table-striped table-hover text-center align-middle table-responsive-md">
            <thead>
                <tr class="col text-left">
                    <td>員工ID</td>
                    <td>員工名字</td>
                    <td>表單種類</td>
                    <td>代理人</td>
                    <td>開始時間</td>
                    <td>結束時間</td>
                    <td>原因</td>
                    <td>審核結果</td>
                    <td>檔案</td>
                </tr>
            </thead>

            <tbody>
                @foreach($results as $r )
                    <tr>
                        <td>{{$r->empid}}</td>
                        <td>{{$r->member_name}}</td>
                        <td>{{$r->type}}</td>
                        @if(isset($r->coverMan))
                        <td>{{$r->coverMan}}</td>
                        @else
                        <td>-</td>
                        @endif
                        <td>{{$r->start}}</td>
                        @if($r->end == "")
                            <td> - </td>
                        @else
                            <td>{{$r->end}}</td>
                        @endif
                        <td>{{$r->reason}}</td>
                        @if($r->status == "")
                            <td><font color='red'>尚未審核</font></td>
                        @else
                            <td>{{$r->status}}</td>
                        @endif
                        @if($r->status == "Y")
                        <td><input class="btn btn-light btn-md active" id="btn" type="button" value="下載" onclick="submit_download({{$r->id}})"></td>
                        @else
                        <td> - </td>
                        @endif
                    </tr>
                @endforeach
                
        </tbody>
        </table>

        <div class="d-inline-flex p-2 bd-highlight">
            {{ $results->links(); }}
        </div>
    @endif
</div>
@endsection

<script>
    function submit_onclick_asc(){
        var name = document.getElementById("name").value;
        var start_time = document.getElementById("start_time").value;
        var end_time = document.getElementById("end_time").value;

        if (name=="" || start_time=="" || end_time==""){
            //window.location.href="/patrol_record_asc";
            alert('表單名稱 與 開始時間 與 結束巡邏時間不可為空值。');    
        }
        

        if (name!="" & start_time!="" & end_time!=""){    
            
            if (start_time > end_time){
                alert('開始時間 不可能大於 結束時間。');    
            }
            else{
                query='/table/requestoverview/name='+name+'&start_time='+start_time+'&end_time='+end_time;
                
                if (confirm('你選擇的表單是：'+name+'\n開始時間：'+start_time+'\n結束時間：'+end_time+'\n\n確認後開始搜尋!!')==true)
                {window.location.href=query;}
            }
        }      
    }

    function submit_download(id){
        var path = '/table/download/'+id;
        window.location.href=path;

    }
</script>