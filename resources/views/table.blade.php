@extends('layouts.app')

@section('content')

         <div class="row justify-content-center mt-2">
            <div class="col-md-10 align-items-center justify-content-center">
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

      <div class="row justify-content-center mx-1">

         <div class="ex col-md-5 align-items-center justify-content-center border border-5 mx-2 mb-2 mt-0 px-3 pb-3">
               <h2 class='mt-2'>輸出離職單</h2> 
               <form method="POST" action="{{ route('table.resign') }}" enctype="multipart/form-data">
               {{ csrf_field() }}

               <div class="row mb-3">
                  <div class="col-md-auto align-self-center">選擇輸出員工：
                     <select class="col-md-auto align-self-center border-1" name="id" id="resignInputId">     
                        @foreach($employees as $employee )
                           <option value="{{$employee->id}}">{{$employee->member_name}}</option>
                        @endforeach
                     </select>
                     <input class="place w-25 d-inline-flex mx-2" name="inputName" id="resignInputName" placeholder="或輸入名字">

                     <div class='w-100'></div>
                     <em>(下拉選單僅顯示系統中目前為"在職"的人員，或至</em><a href="https://www.etunproject.com/table_overview"> 總覽 </a><em>可查詢審核結果，或至server存檔區尋找檔案)</em>
                  </div>

               </div>

               <div class="w-100"></div>  
               <div class="row mt-3 mb-3">
                  <button type="submit" class="btn btn-success">輸出</button>   
               </div>

               </form>

         </div>

         <div class="ex col-md-5 align-items-center justify-content-center border border-5 mx-2 mb-2 mt-0 px-3 pb-3">
               <h2 class='mt-2'>輸出請假單</h2> 
               <form method="POST" action="{{ route('table.leave') }}" enctype="multipart/form-data">
               {{ csrf_field() }}

               <div class="row mb-3">
                  <div class="col-md-auto align-self-center">選擇輸出員工：
                     <select class="col-md-auto align-self-center border-1 mb-1" name="id">     
                        @foreach($leaves as $leave )
                           <option value="{{$leave->id}}">{{$leave->member_name." ".$leave->start.'至'.$leave->end}}</option>
                        @endforeach 
                     </select>
                     
                     <div class="w-100"></div>
                     <em>(下拉選單僅顯示一年內，"已通過審核"之人員，或至</em><a href="https://www.etunproject.com/table_overview"> 總覽 </a><em>可查詢審核結果，或至server存檔區尋找檔案)</em>
                  </div>
               </div>

               <div class="w-100"></div>  
               <div class="row mt-3 mb-3">
                  <button type="submit" class="btn btn-success">輸出</button>   
               </div>
               </form>
         </div>

         <div class="ex col-md-5 align-items-center justify-content-center border border-5 m-2 p-3">
               <h2 class='mt-2'>輸出簽到單</h2> 
               <form method="POST" action="{{ route('table.attendance') }}" enctype="multipart/form-data">
               {{ csrf_field() }}

               <div class="row mb-3">
                  <div class="col-md-auto align-self-center">選擇輸出員工：
                     <select class="col-md-auto align-self-center border-1" name="emp_id" id='signId'>     
                        @foreach($employees as $emp )
                           <option value="{{$emp->id}}">{{$emp->member_name}}</option>
                        @endforeach
                     </select>
                     {{-- <div class="col col-md-auto border-0 py-1"> --}}

                        &nbsp;&nbsp;月份：<input id="signMonth" class="pic border-1 m-1" name="signMonth" type="month">
                        {{-- <div class="w-100"></div> --}}
                        &nbsp;&nbsp;人名：<input class="border-1 m-1" type="text" value="{{ old('signName') }}" name="inputName" id='signName' placeholder="或輸入名字">
                        
                        <input type="button" value="儲存" onclick="signInput()" />
                        <input type="button" value="刪除" onclick="signInput2()" />          
                     {{-- </div> --}}
                  </div>
                  <textarea id="signlist" style="font-size:large" rows="3" cols="20" name="signlist" placeholder="同時選擇及輸入人名，以輸入人名優先優先。"></textarea>
               </div>
                  
               <div class="w-100"></div>  
               <div class="row mt-3 mb-3">
                  <button type="submit" class="btn btn-success">輸出</button>   
               </div>

               </form>

         </div>

         <div class="ex col-md-5 align-items-center justify-content-center border border-5 m-2 p-3">
               <h2 class='mt-2'>輸出薪資單</h2> 
               <form method="POST" action="{{ route('table.salary') }}" enctype="multipart/form-data">
               {{ csrf_field() }}

               <div class="row mb-3">
                  <div class="col-md-auto align-self-center">選擇輸出員工：
                     <select class="col-md-auto align-self-center border-1" name="namelist" id="namelist">     
                        @foreach($employees as $employee )
                           <option value="{{$employee->id}}">{{$employee->member_name}}</option>
                        @endforeach
                     </select>
                     {{-- <div class="col col-md-auto border-0 py-1"> --}}
                           &nbsp;&nbsp;月份：<input id="exmonth" class="pic border-1 m-1" name="exmonth" type="month">
                           {{-- <div class="w-100"></div> --}}
                           &nbsp;&nbsp;人名：<input class="border-1 m-1" type="text" value="{{ old('exname') }}" id="exname" name="exname" placeholder="或請輸入人名">

                           <input type="button" value="儲存" onclick="example()" />
                           <input type="button" value="刪除" onclick="example2()" />    
                    {{-- </div> --}}
                    
                  </div>
                  <textarea id="exlist" style="font-size:large" rows="3" cols="20" name="exlist" placeholder="同時選擇及輸入人名，以輸入人名優先優先。"></textarea>
               </div>

               <div class="w-100"></div>  
               <div class="row mt-3 mb-3">
                  <button type="submit" class="btn btn-success">輸出</button>   
               </div>

               </form>
         </div>

         <div class="ex col-md-5 align-items-center justify-content-center border border-5 m-2 p-3">
               <h2 class='mt-2'>輸出薪資試算表</h2> 
               <form method="POST" action="{{ route('table.export_access_salary') }}" enctype="multipart/form-data">
               {{ csrf_field() }}

               <div class="row mb-3 align-items-center" style="min-height:50px">
                  <div class="col-md-auto align-items-center">選擇月份：</div>
                  <div class="col-md-auto align-items-center border">
                     <input id="month" class="pic border-0" name="exportbymonth" type="month">
                  </div>
                  {{-- <em>(所匯出的表單，僅顯示"在職"之員工)</em> --}}
               </div>

               <div class="w-100"></div>  
               <div class="row mt-3 mb-3">
                  <button type="submit" class="btn btn-success">輸出</button>   
               </div>

               </form>
         </div>

         <div class="ex col-md-5 align-items-center justify-content-center border border-5 m-2 p-3">
               <h2 class='mt-2'>匯入已編輯薪資試算表</h2> 
               <form method="POST" action="{{ route('table.import_access_salary') }}" enctype="multipart/form-data">
               {{ csrf_field() }}

               <div class="row mb-3 align-items-center" style="min-height:50px">
                  <div class="col-md-12 align-items-center">
                        <input id="file" type="file" class="form-control" name="select_file" accept="">
                  </div>
               </div>

               <div class="w-100"></div>  
               <div class="row mt-3 mb-3">
                  <button type="submit" class="btn btn-success">匯入</button>   
               </div>

               </form>
         </div>

         <div class="ex col-md-5 align-items-center justify-content-center border border-5 m-2 p-3">
               <h2 class='mt-2'>輸出代班人員表格</h2> 
               <form method="POST" action="{{ route('table.export_extra_schedule') }}" enctype="multipart/form-data">
               {{ csrf_field() }}

               <div class="row mb-3 align-items-center" style="min-height:50px">
                  <div class="col-md-auto align-items-center">選擇月份：</div>
                  <div class="col-md-auto align-items-center border">
                     <input id="month" class="pic border-0" name="exportbymonth" type="month">
                  </div>
                  {{-- <em>(所匯出的表單，僅顯示"在職"之員工)</em> --}}
               </div>

               <div class="w-100"></div>  
               <div class="row mt-3 mb-3">
                  <button type="submit" class="btn btn-success">輸出</button>   
               </div>

               </form>
         </div>

         <div class="ex col-md-5 align-items-center justify-content-center border border-5 m-2 p-3">
               <h2 class='mt-2'>擴充用</h2> 
         </div>

      </div>

</body>
</html>


@endsection

<script>

    function example(){
            var name = document.getElementById("exname").value;
            var month = document.getElementById("exmonth").value;


             if(name == ""){
                var e = document.getElementById("namelist");
                //var value = e.value;
                var name = e.options[e.selectedIndex].text;
             }

            var textnode=document.createTextNode(name+','+month+',');
             if (name!="" && month!=""){
               var area=document.getElementById("exlist");
               area.appendChild(textnode);
             }
             else
             {alert('請輸入完整資訊')}

    }

    function example2(){
            //var area=document.getElementById("text2");

            //area.removeChild(area.firstElementChild);

            const list = document.getElementById("exlist");
            list.removeChild(list.lastChild);
    }

   function signInput(){
            var name = document.getElementById("signName").value;
            var month = document.getElementById("signMonth").value;
             
            if(name == ""){
                var e = document.getElementById("signId");
                var name = e.options[e.selectedIndex].text;
             }

            var textnode=document.createTextNode(name+ ','+month+',');
             if (name!="" && month!=""){
               var area=document.getElementById("signlist");
               area.appendChild(textnode);
             }
             else
             {alert('請輸入完整資訊')}

    }

    function signInput2(){
            const list = document.getElementById("signlist");
            list.removeChild(list.lastChild);
    }

</script>