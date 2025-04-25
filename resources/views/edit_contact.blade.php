@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

        <div class="row mx-1">
            <p class="p-test mt-1 mb-0 fs-3">新增聯絡資訊</p>
            
            <form method="POST" action="{{ route('contact.update') }}" enctype="multipart/form-data" class="row">
              {{ csrf_field() }}

                <div class="row mt-2 align-items-center"> 
                    <div class="col-md-auto">選擇群組：</div>
                    <div class="col-md-auto">
                        <select class="form-select form-select-md" aria-label="Default select example" name="group_name">
                            <option value="">請選擇</option>
                            @foreach($groups as $group )
                            <option value="{{$group->id}}" @if($gid == $group->id) selected @endif>{{$group->groupName}}</option>
                            @endforeach
                    
                        </select>
                    </div>
                </div>
                <div class="row border-0 d-inline-flex align-items-center py-2">
                    <div class="col-md-auto">新增名稱：</div>
                    <div class="col-md-10 d-inline-flex">
                        <input class="group_name border-1 w-25" name="group_user_name" value="{{$contacts->contactName}}">
                    </div>    
                </div>

                <div class="row border-0 d-inline-flex align-items-center py-2">
                    <div class="col-md-auto">新增電話：</div>
                    <div class="col-md-10 d-inline-flex">
                        <input class="group_name border-1 w-25" name="group_user_phone" value="{{$contacts->contactPhone}}"  
                        onbeforepaste="clipboardData.setData('text',clipboardData.getData('text').replace(/[^\d]/g,''))" 
                        onkeyup="value=value.replace(/-[^\d.]/g,'')" />
                        <input type="hidden" name="old_id" value="{{$contacts->id}}">
                    </div>
                </div>

                <div class="row mx-1 py-2"> 
                    <input class="btn btn-success w-25" type="submit" value="確認送出">
                </div>
                </div>  
            </form> 
        

            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
@endsection


<script text="text/javascript">
    function submit_onclick(){
        var mem_id = document.getElementById("uid").value;
        //document.getElementById("name").value="";
        ajaxRequestPost(mem_id);
        
    }

    function ajaxRequestPost(id){

        $.ajaxSetup({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });

        
        //console.log(id);
        $.ajax({
            type:'POST',
            url:'/ajaxRequest',
            data:{uid:id},

            success:function(data) {
               //$("#data").html(data.msg);
               //alert("ID是：" + id + "\n狀態：" + status);
               //console.log(data);
               alert(data);
            },
            error: function (msg) {
               console.log(msg);
               var errors = msg.responseJSON;
               
            }
         });
      
        
    }
</script>