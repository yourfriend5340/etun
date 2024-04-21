@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

        <div class="container container-fluid">


            
            <form method="POST" action="{{ route('organize.store') }}" enctype="multipart/form-data" class="row">
              {{ csrf_field() }}


            <div class="row mt-2 align-items-center">    
                <div class="col-md-auto border-0 d-inline-flex align-items-center py-2">組織名稱：</div>
                <div class="col-md-auto border-0 d-inline-flex align-items-center">
                    <input class="organize_name border-1 w-75" name="organize_name" placeholder="請輸入">
                </div>

                <div class="row w-100"></div>

                <div class="col-md-auto border-0 d-inline-flex align-items-center py-2">組織電話：</div>
                <div class="col-md-auto border-0 d-inline-flex align-items-center">
                    <input class="organize_tel border-1 w-75 d-inline-flex" name="organize_tel" placeholder="0912345678"
                    onbeforepaste="clipboardData.setData('text',clipboardData.getData('text').replace(/[^\d]/g,''))" 
                    onkeyup="value=value.replace(/[^\d.]/g,'')" />
                </div>

                <div class="row w-100"></div>

                <div class="col-md-auto border-0 d-inline-flex align-items-center py-2">組織住址：</div>
                <div class="col-md-4 border-0 d-inline-flex align-items-start">
                    <input class="organize_addr border-1 w-100 d-inline-flex" name="organize_addr" placeholder="請輸入住址">
                </div>
                

            <div class="enter">
                <div class="row justify-content-center py-2"> 
                    <input class="btn btn-success w-25" type="submit" value="確認送出">
                </div>
            </div>
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