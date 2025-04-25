@extends('layouts.app')

@section('content')

   <div class="row mx-1">   
      <div class="row justify-content-center">

         <!--export use phpspreadsheet-->
         <div class="ex col-md-4 align-items-center justify-content-center border border-5 m-2 p-3">
               <h2 class='mt-2'>輸出離職單</h2> 
               <form method="POST" action="{{ route('table.resign') }}" enctype="multipart/form-data">
               {{ csrf_field() }}

               <div class="row mb-3">
                  <div class="col-md-auto align-self-center">選擇輸出員工：</div>
                     <select class="col-md-auto align-self-center border-1" name="id">     
                        @foreach($employees as $employee )
                           <option value="{{$employee->id}}">{{$employee->member_name}}</option>
                        @endforeach
                     </select>
                  </div>

               <div class="w-100"></div>  
               <div class="row mt-3 mb-3">
                  <button type="submit" class="btn btn-success">輸出</button>   
               </div>

               </form>

         </div>

         <!--export use phpspreadsheet-->
         <div class="ex col-md-4 align-items-center justify-content-center border border-5 m-2 p-3">
               <h2 class='mt-2'>輸出請假單</h2> 
               <form method="POST" action="{{ route('table.leave') }}" enctype="multipart/form-data">
               {{ csrf_field() }}

               <div class="row mb-3">
                  <div class="col-md-auto align-self-center">選擇輸出員工：</div>
                     <select class="col-md-auto align-self-center border-1" name="id">     
                        @foreach($employees as $employee )
                           <option value="{{$employee->id}}">{{$employee->member_name}}</option>
                        @endforeach 
                     </select>
                  </div>

               <div class="w-100"></div>  
               <div class="row mt-3 mb-3">
                  <button type="submit" class="btn btn-success">輸出</button>   
               </div>
               </form>
         </div>

                  <!--export use phpspreadsheet-->
         <div class="ex col-md-4 align-items-center justify-content-center border border-5 m-2 p-3">
               <h2 class='mt-2'>輸出簽到單</h2> 
               <form method="POST" action="{{ route('table.attendance') }}" enctype="multipart/form-data">
               {{ csrf_field() }}

               <div class="row mb-3">
                  <div class="col-md-auto align-self-center">選擇輸出員工：</div>
                     <select class="col-md-auto align-self-center border-1" name="emp_id">     
                        @foreach($employees as $emp )
                           <option value="{{$emp->id}}">{{$emp->member_name}}</option>
                        @endforeach
                     </select>
                     <input class="place w-25 d-inline-flex mx-2" name="inputName" placeholder="或輸入名字">
                  </div>
                  
               <div class="w-100"></div>  
               <div class="row mt-3 mb-3">
                  <button type="submit" class="btn btn-success">輸出</button>   
               </div>

               </form>

         </div>

                  <!--export use phpspreadsheet-->
         <div class="ex col-md-4 align-items-center justify-content-center border border-5 m-2 p-3">
               <h2 class='mt-2'>輸出薪資單</h2> 
               <form method="POST" action="{{ route('table.salary') }}" enctype="multipart/form-data">
               {{ csrf_field() }}

               <div class="row mb-3">
                  <div class="col-md-auto align-self-center">選擇輸出員工：</div>
                     <select class="col-md-auto align-self-center border-1" name="name">     
                        @foreach($employees as $employee )
                           <option value="{{$employee->id}}">{{$employee->member_name}}</option>
                        @endforeach
                     </select>
                  </div>

               <div class="w-100"></div>  
               <div class="row mt-3 mb-3">
                  <button type="submit" class="btn btn-success">輸出</button>   
               </div>

               </form>

         </div>

         <div class="ex col-md-4 align-items-center justify-content-center border border-5 m-2 p-3">
               <h2 class='mt-2'>輸出薪資試算表</h2> 
               <form method="POST" action="{{ route('table.export_access_salary') }}" enctype="multipart/form-data">
               {{ csrf_field() }}

               <div class="row mb-3">
                  <div class="col-md-auto align-self-center">選擇輸出員工：</div>
                     <select class="col-md-auto align-self-center border-1" name="name">     
                        @foreach($employees as $employee )
                           <option value="{{$employee->id}}">{{$employee->member_name}}</option>
                        @endforeach
                     </select>
                  </div>

               <div class="w-100"></div>  
               <div class="row mt-3 mb-3">
                  <button type="submit" class="btn btn-success">輸出</button>   
               </div>

               </form>
         </div>

         <div class="ex col-md-4 align-items-center justify-content-center border border-5 m-2 p-3">
               <h2 class='mt-2'>匯入已編輯薪資試算表</h2> 
               <form method="POST" action="{{ route('table.import_access_salary') }}" enctype="multipart/form-data">
               {{ csrf_field() }}

               <div class="row mb-3">
                  <div class="col-md-auto align-self-center">選擇輸出員工：</div>
                     <select class="col-md-auto align-self-center border-1" name="name">     
                        @foreach($employees as $employee )
                           <option value="{{$employee->id}}">{{$employee->member_name}}</option>
                        @endforeach
                     </select>
                  </div>

               <div class="w-100"></div>  
               <div class="row mt-3 mb-3">
                  <button type="submit" class="btn btn-success">匯入</button>   
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
