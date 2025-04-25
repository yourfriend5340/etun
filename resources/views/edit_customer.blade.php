@extends('layouts.app')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

        <div class="container container-fluid">

            <div class="row w-100"></div>
            @foreach($customers as $customer )
{{--
            @if ($customer->customer_group_id==1)
            {{$customer_group_name='VIP客戶'}}
            @else
            {{$customer_group_name='普通客戶'}}
            @endif

            @if ($customer->status==1)
            {{$customer_status='現有客戶'}}
            @else
            {{$customer_status='非現有客戶'}}
            @endif

            @if ($customer->active==1)
            {{$customer_active='現有客戶'}}
            @else
            {{$customer_active='非現有客戶'}}
            @endif
--}}
            <form onsubmit="return false">
            <div class="row mt-2 align-items-center">
                <div class="organize col-md-auto border-0 d-inline-flex align-self-center"><font color=red>*</font>客戶編號 :</div>
                <div class="col-md-auto border-0 d-inline-flex align-item-center">
                <input class="organize me-1 w-50" type="text"name="cid" id="cid" form="form1" value="{{ $customer->customer_sn }}" readonly>

            
                </div>
            </div>
            </form>

            
            <form method="POST" action="{{ route('customer.update') }}" enctype="multipart/form-data" class="row" id="form1">
              {{ csrf_field() }}
            <div class="row mt-2 align-items-center">

                <div class="organize col-md-auto border-0 d-inline-flex align-self-center"><font color=red>*</font>客戶群組 :</div>
                <div class="organize col-md-auto border-0 align-self-center">
                    <select class="form-select form-select-sm" aria-label="Default select example" name="group">

                        @if ($errors->any())
                        <option selected value="{{ old('group') }}">{{ old('group') }}</option>
                        @endif

                        <option selected value="{{ $customer->customer_group_id }}">{{ $customer->group }}</option>
                        @foreach($groups as $g)
                        <option value={{$g->id}}>{{$g->group}}</option>
                        @endforeach
                        
                      </select>
                </div>

                <div class="organize col-md-auto border-0 d-inline-flex align-self-center"><font color=red>*</font>客戶status :</div>
                <div class="organize col-md-auto border-0 align-self-center">
                    <select class="form-select form-select-sm" aria-label="Default select example" name="status">

                        @if ($errors->any())
                        <option selected value="{{ old('status') }}">{{old('status')}}</option>
                        @endif

  
                        <option selected value="{{ $customer->id }}">{{ $customer->status }}</option>
                        @foreach($status as $s)
                        <option value={{$s->id}}>{{$s->status}}</option>
                        @endforeach
                      </select>
                </div>

                {{--康小姐不知道此欄功能，保留做日後開發用
                <div class="organize col-md-auto border-0 d-inline-flex align-self-center"><font color=red>*</font>客戶active :</div>
                <div class="organize col-md-auto border-0 align-self-center">
                    <select class="form-select form-select-sm" aria-label="Default select example" name="active">

                        @if ($errors->any())
                        <option selected value="{{ old('active') }}">{{ old('active') }}</option>
                        @endif

                        <option value="">請選擇</option>
                        <option selected value="{{ $customer->active }}">{{ $active }}</option>
                        <option value="1">現有客戶</option>
                        <option value="0">非現有客戶</option>
                      </select>
                </div>
                --}}
            </div>



            <div class="row mt-2 align-items-center">    
                <div class="col-md-1 border-0 d-inline-flex align-item-center py-1"><font color=red>*</font>客戶名稱 :</div>
                <div class="col-md-5 border-0 d-inline-flex align-item-center">
                    <input class="pic border-1 w-100" name="name" placeholder="請輸入" value="{{ $customer->firstname }}">
                </div>

                <div class="w-100"></div>
                <div class="col-md-1 border-1 d-inline-flex align-item-between py-1"><font color=red>*</font>通訊地址 :</div>
                <div class="col-md-5 border-0 d-inline-flex align-item-center">
                    <input type="text" class="border-1 align-content-lg-around w-100" placeholder="請輸入住址" name="addr" value="{{ $customer->addr }}">
                </div>

                <div class="row w-100"></div>
                <div class="col-md-1 border-0 d-inline-flex align-item-center py-1"><font color=red>*</font>電話號碼 :</div>
                <div class="col-md-auto border-0 d-inline-flex align-item-center">
                    <input class="border-1 w-100 d-inline-flex" name="phone" type="text" placeholder="061234567" value="{{ $customer->tel }}"
                    onbeforepaste="clipboardData.setData('text',clipboardData.getData('text').replace(/[^\d]/g,''))" 
                    onkeyup="value=value.replace(/[^\d.]/g,'')" />
                </div>

                <div class="row w-100"></div>
                <div class="col-md-1 border-0 d-inline-flex align-item-center py-1"><font color=white>*</font>聯絡人 :</div>
                <div class="col-md-5 border-0 d-inline-flex align-item-center">
                    <input class="pic border-1 w-100" name="person" placeholder="請輸入" value="{{ $customer->person }}">
                </div>

                <div class="row w-100"></div>
                <div class="col-md-1 border-0 d-inline-flex align-item-center py-1"><font color=white>*</font>電腦位址 : </div>
                <div class="col-md-auto border-0 d-inline-flex align-item-center">
                    <input class="border-1 w-100 d-inline-flex" name="ip" type="text" placeholder="192.168.0.1" value="{{ $customer->ip }}"
                    onbeforepaste="clipboardData.setData('text',clipboardData.getData('text').replace(/[^\d]/g,''))" 
                    onkeyup="value=value.replace(/[^\d.]/g,'')" />
                </div>

                <div class="row w-100"></div>
                <div class="col-md-1 border-0 d-inline-flex align-item-center py-1"><font color=white>*</font>地址緯度 :</div>
                <div class="col-md-auto border-0 d-inline-flex align-item-center">
                    <input class="border-1 w-100 d-inline-flex" name="lat" type="text" placeholder="23.010551" value="{{ $customer->lat }}"
                    onbeforepaste="clipboardData.setData('text',clipboardData.getData('text').replace(/[^\d]/g,''))" 
                    onkeyup="value=value.replace(/[^\d.]/g,'')" />
                </div>

                <div class="row w-100"></div>
                <div class="col-md-1 border-0 d-inline-flex align-item-center py-1"><font color=white>*</font>地址經度 :</div>
                <div class="col-md-auto border-0 d-inline-flex align-item-center">
                    <input class="border-1 w-100 d-inline-flex" name="lng" type="text" placeholder="120.182540" value="{{ $customer->lng }}"
                    onbeforepaste="clipboardData.setData('text',clipboardData.getData('text').replace(/[^\d]/g,''))" 
                    onkeyup="value=value.replace(/[^\d.]/g,'')" />
                </div>
                <div class="w-100">&nbsp;<font>(備註一：經緯度用於員工上班打卡判斷，若沒填寫其功能會不正常，請確實到google map查詢座標)</font></div>
                <div class="w-100">&nbsp;<font>(備註二：若公司改名，請新建客戶資料)</font></div>
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

            <hr>

            <div class="row">
                <p class="my-0">App功能開通</p>
            </div>
            <div class="row">
                <div class="col-md-3 border">帳號:
                    <input class="pic border-0" name="member_account" value={{$customer->account}}>
                </div>   
                <div class="col-md-3 border">密碼:
                    <input class="pic border-0" name="member_password" value={{$customer->password_text}}>
                </div>
            </div>
            <div class="row w-100"></div>
            <div class="enter">
                <div class="row justify-content-center py-2"> 
                    <input class="w-25" type="submit" value="確認送出">
                </div>
            </div>
            </form> 
        @endforeach
        </div>
@endsection

<script text="text/javascript">

    function submit_onclick(){
        var mem_id = document.getElementById("cid").value;
        //document.getElementById("name").value="";
        if (mem_id!="")
        {
        ajaxRequestPost(mem_id);
        }

        else{
        alert('無輸入值，請輸入客戶編號');
        }
    }

    function ajaxRequestPost(id){

        $.ajaxSetup({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });

        $.ajax({
            type:'POST',
            url:'/ajaxRequestCustomer',
            data:{cid:id},

            success:function(data) {
               //$("#data").html(data.msg);
               //alert("ID是:" + id + "\n狀態:" + status);
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