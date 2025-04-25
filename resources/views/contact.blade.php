@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
        
        <div class="row mx-1">
            <p class="p-test mt-1 mb-0 fs-3">新增聯絡資訊</p>
            
            <form method="POST" action="{{ route('contact.store') }}" enctype="multipart/form-data" class="row">
              {{ csrf_field() }}
            
                <div class="row align-items-center d-inline-flex"> 
                    <div class="col-md-auto">選擇群組：</div>
                    <div class="col-md-auto ">
                        <select class="form-select form-select-md" aria-label="Default select example" name="group_name">
                            <option value="">請選擇</option>
                            @foreach($groups as $group )
                            <option value="{{$group->groupName}}">{{$group->groupName}}</option>
                            @endforeach
                    
                        </select>
                    </div>    
                    
                </div>    

                <div class="row border-0 d-inline-flex align-items-center py-2">
                    <div class="col-md-auto">新增名稱：</div>
                    <div class="col-md-10 d-inline-flex ">
                        <input class="group_name border-1 w-25" name="group_user_name" placeholder="請輸入">
                    </div>
                </div>

                <div class="row border-0 d-inline-flex align-items-center py-2">
                    <div class="col-md-auto">新增電話：</div>
                    <div class="col-md-10 d-inline-flex">
                        <input class="group_name border-1 w-25" name="group_user_phone" placeholder="06-1234567、0911-123456"  
                        onbeforepaste="clipboardData.setData('text',clipboardData.getData('text').replace(/[^\d]/g,''))" 
                        onkeyup="value=value.replace(/-[^\d.]/g,'')" />
                    </div>
                </div>

                <div class="row py-2 mx-1"> 
                    <input class="btn btn-success w-25" type="submit" value="確認送出">
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