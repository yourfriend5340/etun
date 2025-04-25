@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

        <div class="row mx-1">

        <p class="p-test mt-1 mb-0 fs-3">更新QRcode</p>
            
            <form method="POST" action="{{ route('qrcode.update') }}" enctype="multipart/form-data" class="row">
              {{ csrf_field() }}

            @foreach($qrcodes as $qrcode )
            <div class="row mt-2 align-items-center">    
                <div class="col-md-auto border-0 d-inline-flex align-items-center py-2">QR Code：</div>
                <div class="col-md-4 border-0 d-inline-flex align-items-center">
                    <input class="qrcode border-1 w-50" name="qrcode" placeholder="請輸入" value={{$qrcode->patrol_RD_No}} readonly>
                </div>

                <div class="row w-100"></div>
                            
                <div class="col-md-auto border-0 d-inline-flex align-items-center py-2">客戶名稱：</div>
                <div class="col-md-4 border-0 d-inline-flex align-items-center">
                    <input class="qrcode_name border-1 w-50" name="qrcode_name" placeholder="請輸入" value={{$qrcode->firstname}} readonly>
                </div>

                <div class="row w-100"></div>

                <div class="col-md-auto border-0 d-inline-flex align-items-center py-2">巡邏地點：</div>
                <div class="col-md-4 border-0 d-inline-flex align-items-start">
                    <input class="qrcode_addr border-1 w-50 d-inline-flex" name="qrcode_addr" placeholder="請輸入住址" value={{$qrcode->patrol_RD_Name}}>
                </div>
                
            @endforeach
            <div class="enter">
                <div class="row py-2 mx-1"> 
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