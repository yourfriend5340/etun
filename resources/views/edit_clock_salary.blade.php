@extends('layouts.app')

@section('content')


<div class="container container-fluid">
    <form>  
        <div class="row justify-content-center my-1">     
            <div class="col-md-auto align-self-center"><font color='red'>*</font>員工名稱(未定義薪資或鐘點人員)：</div>
            <div class="col-md-auto input-group-sm align-self-center">
                <select class="form-select form-select-sm" aria-label="Default select example" name="name" id="name" autocomplete="on">
                    @if ($errors->any())
                        <option value="{{ old('name') }}">{{ old('name') }}</option>
                    @endif
                        <option value="">請選擇</option>
                    @foreach($name as $na )
                        <option value="{{$na->member_sn}}">{{$na->member_name}}</option>
                    @endforeach
                </select>                
            </div> 
    
            <div class="col-md-auto align-self-center">
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

<div class="container container-fluid">

    @if (isset($records))
        <div class="row justify-content-center my-1">    
            <div class="col-md-auto align-self-center"><font color='red'>*</font>員工名稱：</div> 
            <div class="col-md-auto align-self-center">
               
                <input class="inp" type="text" id="add_employee" value="{{$input}}" readonly>

            </div>
            <div class="w-100"></div>
            <div class="col-md-auto align-self-center"><font color='red'>*</font>客戶名稱：</div>
            <div class="col-md-auto input-group-sm align-self-center mx-0 px-0">
            <select class="form-select form-select-sm border-1 mx-0 px-0" aria-label="Default select example" name="addname" id="addname">
                    @if ($errors->any())
                        <option value="{{ old('addname') }}">{{ old('addname') }}</option>
                    @endif
                        <option value="">請選擇</option>
                    @foreach($customers as $cus )
                        <option value="{{$cus->firstname}}">{{$cus->firstname}}</option>
                    @endforeach
            </select>                
        </div>
        <div class="col-md-auto align-self-center">鐘點薪資：
                    <input class="inp me-2" type="text" id="add_salary" 
                    onbeforepaste="clipboardData.setData('text',clipboardData.getData('text').replace(/[^\d]/g,''))" 
                    onkeyup="value=value.replace(/[^\d.]/g,'')" /> 
            <input class="btn btn-success" type="button" value="增加" onclick="submit_onclick_add()">  
        </div>  
    </div>
   
    <table class="table table-bordered table-striped table-hover text-center align-middle table-responsive-md">
        <thead>
            <tr class="col text-left">
            <td>ID</td>
            <td>員工</td>
            <td>客戶</td>
            <td>鐘點薪資(小時/元)</td>
            <td>刪除</td>            
            </tr>
        </thead>

        <tbody>

            @foreach($records as $record )
                <tr>
                    <td>{{$record->id}}</td>
                    <td>{{$record->member_name}}</td>
                    <td>{{$record->customer}}</td>
                    <td>{{$record->salary}}</td>    

                    @can('admin')
                    <td>
                        <input class="btn btn-light btn-md active" type="button" value="刪除" onclick="submit_onclick({{$record->id}})">
                    </td>

                    @elsecan('super_manager')
                    <td>
                        <input class="btn btn-light btn-md active" type="button" value="刪除" onclick="submit_onclick({{$record->id}})">
                    </td>
                    @endcan

                </tr>
            @endforeach
           
      </tbody>

    </table>

    <div class="d-inline-flex p-2 bd-highlight">
        

    </div>
    @endif
   
</div>

@endsection


<script>
    function submit_onclick(id){
        if (confirm('確認刪除'+id+'號資料？')==true){
            window.location.href="/clocksalary/delete/"+id;
        }
    }

    function submit_onclick_asc(){

        var name = document.getElementById("name")
        var text =name.options[name.selectedIndex].text;
        var id = document.getElementById("name").value;
        query='/clocksalary/'+id;
        
        if (text!='請選擇'){
            if (confirm('請確認你輸入的名字是：'+text) ==true)
            {window.location.href=query;} 
        }  
    }

        function submit_onclick_add(){
        var empname = document.getElementById("add_employee").value;
        var name = document.getElementById("addname");
        var text =name.options[name.selectedIndex].text;//cusname
        var salary = document.getElementById("add_salary").value;

        query='/clocksalary/add/empname='+empname+'&name='+text+'&salary='+salary;
        
        if (salary!="" && text!="請選擇"){
            if (confirm('請確認你輸入的名字是：'+text+'，鐘點薪資是：'+salary+'元') ==true)
            {window.location.href=query;}   
        }
        else{
            alert('請完整輸入要增加的資料');
        }

    }

</script>