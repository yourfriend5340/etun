@extends('layouts.app')

@section('content')

<div class="row mx-1">
<p class="p-test mt-1 mb-0 fs-3">更新、刪除QRcode</p>
   <form>  
        <div class="row">     
            <div class="col-md-auto align-self-center py-2"><font color='red'>*</font>客戶名稱：</div>
            <div class="col-md-3 input-group-sm align-self-center py-2 pe-1">
                    <select class="form-select form-select-sm" aria-label="Default select example" name="name" id="name" autocomplete="on">
                        @if ($errors->any())
                        <option value="{{ old('name') }}">{{ old('name') }}</option>
                        @endif
                        <option value="">請選擇</option>
                        @foreach($cus_info as $user )
                        <option value="{{$user->customer_id}}">{{$user->firstname}}</option>
                        @endforeach
                    </select>                
            </div> 

            <div class="col-md-auto align-self-center py-2">
                <input class="btn btn-success" type="button" value="遞增查詢" onclick="submit_onclick_asc()">  
                <input class="btn btn-success" type="button" value="遞減查詢" onclick="submit_onclick_desc()">  
            </div>
        </div>
    </form>
</div>
<div class="contain mx-1">
    @if (isset($qrcodes))
    <table class="table table-bordered table-striped table-hover text-center align-middle table-responsive-md">
        <thead>
            <tr class="col text-left">
            <td>ID</td>
            <td>客戶名稱</td>
            <td>巡邏場所</td>

            @can('admin')
            <td>更新</td>
            <td>刪除</td>
            @elsecan('super_manager')
            <td>更新</td>
            <td>刪除</td>
            @endcan
            
            </tr>
        </thead>
      
        <tbody>  
            @foreach($qrcodes as $qrcode )
                <tr>
                    <td>{{$qrcode->patrol_RD_No}}</td>
                    <td>{{$qrcode->firstname}}</td>
                    <td class="text-start">{{$qrcode->patrol_RD_Name}}</td>

                    @can('admin')
                    <td>
                        <input class="btn btn-light btn-md active" type="submit" value="更新" onclick="submit_onclick_request('{{$qrcode->patrol_RD_No}}')">
                    </td>
                    <td>
                        <input class="btn btn-light btn-md active" type="button" value="刪除" onclick="submit_onclick('{{$qrcode->patrol_RD_No}}')">
                    </td>

                    @elsecan('super_manager')
                    <td>
                        <input class="btn btn-light btn-md active" type="submit" value="更新" onclick="submit_onclick_request('{{$qrcode->patrol_RD_No}}')">
                    </td>
                    <td>
                        <input class="btn btn-light btn-md active" type="button" value="刪除" onclick="submit_onclick('{{$qrcode->patrol_RD_No}}')">
                    </td>
                    @endcan
                </tr>
            @endforeach
      </tbody>
    </table>
    <div class="d-inline-flex p-2 bd-highlight">
        {{ $qrcodes->links() }}  
    </div>
    @endif
</div>
    
@endsection

<script>
    function submit_onclick_asc(){

        var name = document.getElementById("name");
        var text =name.options[name.selectedIndex].text;
        var id = document.getElementById("name").value;

        if (id!=""){
            query='/qrcode_asc/'+id;
                
            if (confirm('你輸入的名字是：'+text)==true)
            {window.location.href=query;}
            }
        else{
            alert('請選擇一個客戶！'); 
        }  
    }    

    function submit_onclick_desc(){

        var name = document.getElementById("name");
        var text =name.options[name.selectedIndex].text;
        var id = document.getElementById("name").value;

        if (id!=""){
            query='/qrcode_desc/'+id;
                
            if (confirm('你輸入的名字是：'+text)==true)
            {window.location.href=query;}
            }
        else{
            alert('請選擇一個客戶！'); 
        }  
    }  


    function submit_onclick(id){

        if (confirm('確定要刪除ID： '+id+' 號資料嗎？')==true)
        {window.location.href="/qrcode/delete/"+id;}

    }

    function submit_onclick_request(id){

        if (confirm('確定要調閱ID： '+id+' 號資料嗎？')==true)
        {window.location.href="/qrcode/request/"+id;}

    }


</script>