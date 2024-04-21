@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container container-fluid gx-0">
    
    <form>  
        <div class="row justify-content-center mx-1">     
            <div class="col-md-auto align-self-center py-2 m-0"><font color='red'>*</font>客戶名稱：</div>
            <div class="col-md-auto input-group-sm align-self-center py-2 m-0">
                    <select class="form-select form-select-sm" aria-label="Default select example" name="name" id="name" autocomplete="on">
                        
                        @if ($errors->any())
                        <option value="{{ old('name') }}">{{ old('name') }}</option>
                        @endif
                        <option value="">請選擇</option>
                        @foreach($cus_info as $cus )
                        <option value="{{$cus->customer_id}}">{{$cus->firstname}}</option>
                        @endforeach
                  
                    </select>                
            </div> 

            <div class="col-md-auto align-self-center py-2">
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

<div class="container container-fluid table-responsive">

    @if (isset($qr_info))
    <form method="POST" action="{{ route('qrcode.print') }}" enctype="multipart/form-data" class="row">
    {{ csrf_field() }}
    <table class="table table-bordered table-striped table-hover text-center align-middle table-responsive-md">
        <thead>
            <tr class="col text-left">
            <td>流水號</td>
            <td>客戶名稱</td>
            <td>巡邏場所ID</td>
            <td>巡邏場所</td>
            <td>列印</td>
            </tr>
        </thead>

        <tbody>

            @foreach($qr_info as $qr )
                <tr>
                    
                    <td>{{$qr->id}}</td>
                    <td>{{$qr->firstname}}</td>
                    <td>{{$qr->patrol_RD_No}}</td>
                    <td class="text-start">{{$qr->patrol_RD_Name}}</td>
                    <td>
                        <div class="col col-xs-2 col-xs-offset-4">
                        <input class="form-check-input" type="checkbox" name="chk[]" value="{{$qr->id}}" 
                        @if ($qr->printQR==1)
                            checked
                        @endif
                        >
                        </div>
                    </td>

                    
                </tr>
            @endforeach
            
      </tbody>

    </table>

            <div class="enter">
                <div class="row justify-content-center py-2"> 
                    <input class="btn btn-success w-25" type="submit" value="確認送出" onclick="submit_onclick()">
                </div>
            </div>
    </form>

    <div class="d-inline-flex p-2 bd-highlight">
        
    {{-- $qr_info->links(); --}}

    </div>
    @endif
   
</div>

@endsection


<script>
    function submit_onclick_asc(){

        var name = document.getElementById("name");
        var text =name.options[name.selectedIndex].text;
        var id = document.getElementById("name").value;

        if (id!=''){
            query='/qrcode/print_asc/'+id;
            if (confirm('你選擇的客戶是：'+text+'\n 網址為：'+query)==true)
            {window.location.href=query;}
        }       
        
        else{alert('請選擇一個客戶！');}

    }    

    function submit_onclick_desc(){

        var name = document.getElementById("name");
        var text =name.options[name.selectedIndex].text;
        var id = document.getElementById("name").value;

        if (id!=''){
            query='/qrcode/print_desc/'+id;
            if (confirm('你選擇的客戶是：'+text+'\n 網址為：'+query)==true)
            {window.location.href=query;}
        }       
        
        else{alert('請選擇一個客戶！');}  
    }

        function submit_onclick(){     
        
        {alert('QR CODE製作，因同時要更新資料庫資訊、製作excel、插入圖表，製作視資料筆數而定，學會需要等待約五到十秒，按確認後開始製作。');} 
    }
  
</script>