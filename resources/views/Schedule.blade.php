@extends('layouts.app')

@section('content')

   <div class="container-fluid">   
      <div class="row justify-content-center">

         <!-- 表格视图 -->
         <div class="im col-md-4 justify-content-center border border-5 m-2 p-3">
            <h2 class='mt-2'>匯入排班資料</h2>  
            <form method="POST" action="{{ route('schedules.import') }}" enctype="multipart/form-data">
            {{ csrf_field() }}
               <div class="row col-md-auto">
                  <!--<label for="file" class="form-label">請選擇檔案：(use import1)</label>-->
                  <input id="file" type="file" class="form-control" name="select_file" accept="">
               </div>
                
               <div class="row mt-3 mb-3">
                  <button type="submit" class="btn btn-success">上傳表格</button>
               </div>
            </form>
         </div>

         <!-- 表格视图 -->
         <div class="im col-md-4 justify-content-center border border-5 m-2 p-3">
            <h2 class='mt-2'>匯入巡邏機紀錄</h2>  
            <form method="POST" action="{{ route('patrol_record.import') }}" enctype="multipart/form-data">
            {{ csrf_field() }}
               <div class="row col-md-auto">
                  <!--<label for="file" class="form-label">請選擇檔案：(use import1)</label>-->
                  <input id="file" type="file" class="form-control" name="select_file1" accept="">
               </div>
                
               <div class="row mt-3 mb-3">
                  <button type="submit" class="btn btn-success">上傳表格</button>
               </div>
            </form>
         </div>  

         <!--export use phpspreadsheet-->
         <div class="ex col-md-4 align-items-center justify-content-center border border-5 m-2 p-3">
               <h2 class='mt-2'>下載班表範例檔(含萬年曆)</h2> 
               <form method="POST" action="{{ route('schedules.download_example') }}" enctype="multipart/form-data">
               {{ csrf_field() }}

               <div class="row mb-3">
                  <div class="col-md-auto align-self-center">選擇輸出時間：</div>
                  <div class="col-md-auto align-self-center  border border-1">
                     <input id="month" class="pic border-0" name="exportbymonth" type="month">
                  </div>
               </div>  

               <div class="w-100"></div>  
               <div class="row mt-3 mb-3">
                  <button type="submit" class="btn btn-success">下載表格</button>   
               </div>

               </form>

         </div>

         <!--export use phpspreadsheet-->
         <div class="ex col-md-4 align-items-center justify-content-center border border-5 m-2 p-3">
               <h2 class='mt-2'>匯出排班資料</h2> 
               <form method="POST" action="{{ route('schedules.export') }}" enctype="multipart/form-data">
               {{ csrf_field() }}

               <div class="row mb-3">
                  <div class="col-md-auto align-self-center">選擇輸出客戶：</div>
                     <select class="col-md-auto align-self-center border-1" name="type">     
                        @foreach($customers as $customer )
                           <option value="{{$customer->firstname}}">{{$customer->firstname}}</option>
                        @endforeach 
                     </select>
                  </div>
            
                  <div class="row mb-3">
                  <div class="col-md-auto align-self-center">選擇輸出時間：</div>
                  <div class="col-md-auto align-self-center  border border-1">
                     <input id="month" class="pic border-0" name="exportbymonth" type="month">
                  </div>
               </div>  

               <div class="w-100"></div>  
               <div class="row mt-3 mb-3">
                  <button type="submit" class="btn btn-success">匯出表格</button>   
               </div>
               </form>
         </div>

      </div>

      
   </div>
   <div class="container container-fluid"> 
      <div class="col justify-content-center">
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

            {{-- Display errors --}}
            @if (count($errors) > 0)
               <div class="row">
                  <div class="col-md-12 ">
                     <div> 
                        <!--<div class="alert alert-primary">-->
                        <ul>
                           @foreach($errors->all() as $error)
                              <li>{{ $error }} </li>
                           @endforeach 
                        </ul> 
                     </div>
                  </div>
               </div>
            @endif
         </div>
   </div>


</body>
</html>


@endsection
