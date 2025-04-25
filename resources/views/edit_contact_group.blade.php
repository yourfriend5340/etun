@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

        <div class="row mx-1">
        <p class="p-test mt-1 mb-0 fs-3">更新群組名稱</p>
            <form method="POST" action="{{ route('contact_group.update') }}" enctype="multipart/form-data" class="row">
              {{ csrf_field() }}

            @foreach($groups as $group )
            <div class="row mt-2 align-items-center">    
                <div class="col-md-auto border-0 d-inline-flex align-items-center py-2">組織名稱：</div>
                <div class="col-md-auto border-0 d-inline-flex align-items-center">
                    <input type="hidden" name="old_group_name" value={{$group->groupName}}>
                    <input class="group_name border-1" name="group_name" placeholder="請輸入" value={{$group->groupName}}>
                </div>
                


                <div class="col-md-auto justify-content-center py-2"> 
                    <input class="btn btn-success" type="submit" value="確認送出">
                </div>
            </div>
            @endforeach
            </form> 
        </div>

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

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