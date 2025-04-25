@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

        <div class="row mx-1">
        <p class="p-test mt-1 mb-0 fs-3">新增群組</p>
            <form method="POST" action="{{ route('contact_group.store') }}" enctype="multipart/form-data" class="row">
              {{ csrf_field() }}

                <div class="row mt-2 align-items-center">    
                    <div class="col-md-auto border-0 d-inline-flex align-items-center py-2">群組名稱：</div>
                    <div class="col-md-auto border-0 d-inline-flex align-items-center">
                        <input class="group_name border-1" name="group_name" placeholder="請輸入">
                    </div>

                    <div class="col-md-auto justify-content-center py-2"> 
                        <input class="btn btn-success" type="submit" value="確認送出">
                    </div>
                </div>
            </form> 


            @if ($errors->any())
            <div class="main alert alert-danger">
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
    function addcheckbox(){
        //initialize container

        var container = document.createElementByid("container");

        //initialize checkbox
        var check = document.createElement("input");
        checkbox.type="checkbox";
        checkbox.name="THE_NAME_YOU_WANT";
        checkbox.value="THE_VALUE_YOU_WANT";
        checkbox.id="THE_ID_YOU_WANT";
        checkbox.onclick=function(){
            //the trigger event you want
        }

        //initialize checkbox lable
        var label= document.createElement("lable");
        label.htmlFor="THE_ID_YOU_WANT";
        label.appendChild(document.createTextNode("THIS_IS_A_CHECKBOX"));

        //<br>
        var br = document.createElement("br");
        
        //add to container
        container.appendChild(check);
        container.appendChild(lable);
        container.appendChild(br);

    }


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