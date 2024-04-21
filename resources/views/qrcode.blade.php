@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

        <div class="container container-fluid">

            <form method="POST" action="{{ route('qrcode.store') }}" enctype="multipart/form-data" class="row">
              {{ csrf_field() }}

            <div class="row mt-2 align-items-center mx-1">    
               <div class="col-md-auto border-0 d-inline-flex align-items-center py-2">客戶名稱：</div>
               <div class="col-md-auto border-0 d-inline-flex align-items-center">
                     <select class="form-select form-select-sm w-100" aria-label="Default select example" name="cus_name">
                        
                       {{-- @if ($errors->any())
                        <option value="{{ old('cus_name') }}">{{ old('cus_name') }}</option>
                        @endif
                        --}}
                        <option value="">請選擇</option>
                        @foreach($customers as $customer )
                        <option value="{{$customer->customer_id}}">{{$customer->firstname}}</option>
                        @endforeach
                  
                     </select>
               </div>

               <div class="row w-100"></div>

               <div class="col-md-auto border-0 d-inline-flex align-items-center py-2">巡邏地點：</div>
               <div class="col-md-4 border-0 d-inline-flex align-items-start">
                  <input class="place w-100 d-inline-flex" name="patrol_place" placeholder="{{old('patrol_place')}}">
               </div>
               <div class="row w-100"></div>


               <div class="enter">
                  <div class="row justify-content-center py-2"> 
                     <input class="btn btn-success w-25" type="submit" value="確認送出">
                  </div>
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

            @if(Session::has('ex_success'))
               <div class="alert alert-success">
                  {{ Session::get('ex_success') }}
               </div>
     
            @endif
            @if(Session::has('ex_error'))
               <div class="alert alert-danger">
                  {{ Session::get('ex_error') }}
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