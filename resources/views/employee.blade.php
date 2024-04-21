@extends('layouts.app')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

        <div class="container container-fluid">

            <div class="row w-100"></div>



            <form onsubmit="return false">
                <div class="col-md-auto border-0 d-inline-flex align-self-center pe-0 me-0 mt-3"><font color=red>*</font>職工編號 :</div>   
                <input type="text" name="uid" id="uid" form="form1" value="{{ old('uid') }}"

                    {{--oninput="value=value.replace(/[^A-Z0-9]/g,'')">--}}
                    onbeforepaste="clipboardData.setData('text',clipboardData.getData('text').replace(/[^\d]/g,''))" 
                    onkeyup="value=value.replace(/[^\d.]/g,'')" />

                <input type="submit" value="檢查" onclick="submit_onclick()">   
            </form>

            
            <form method="POST" action="{{ route('employee.store') }}" enctype="multipart/form-data" class="row" id="form1">
              {{ csrf_field() }}
            <div class="row mt-2 align-items-center">
                {{--
                <div class="col-md-auto border-0 d-inline-flex align-self-center pe-0 me-0">職工編號:</div>

                <div class="organize col-md-auto border-1 align-self-center">    
                    <input class="pic border-1" name="member_sn" placeholder="必填，輸入並按'檢查'">
                    <input type="submit" class="check" value="檢查">


                </div>
                --}}

                <div class="organize col-md-auto border-0 d-inline-flex align-self-center"><font color=red>*</font>所屬組織 :</div>
                <div class="organize col-md-auto border-0 align-self-center">
                    <select class="form-select form-select-sm" aria-label="Default select example" name="organize">
                        {{--<option value="">請選擇</option>--}}
                        
                        @if ($errors->any())
                        <option value="{{ old('organize') }}">{{ old('organize') }}</option>
                        @endif
                        <option value="">請選擇</option>
                        @foreach($organizes as $organize )
                        <option value="{{$organize->company}}">{{$organize->company}}</option>
                        @endforeach
                  

                    </select>
                </div>

                <div class="organize col-md-auto border-0 d-inline-flex align-self-center"><font color=red>*</font>所屬區域 :</div>
                <div class="organize col-md-auto border-0 align-self-center">
                    <select class="form-select form-select-sm" aria-label="Default select example" name="area">

                        @if ($errors->any())
                        <option selected value="{{ old('area') }}">{{ old('area') }}</option>
                        @endif

                        <option value="">請選擇</option>
                        <option value="基隆市">1.基隆市</option>
                        <option value="臺北市">2.臺北市</option>
                        <option value="新北市">3.新北市</option>
                        <option value="桃園市">4.桃園市</option>
                        <option value="新竹市">5.新竹市</option>
                        <option value="新竹縣">6.新竹縣</option>
                        <option value="苗栗縣">7.苗栗縣</option>
                        <option value="臺中市">8.臺中市</option>
                        <option value="彰化縣">9.彰化縣</option>
                        <option value="南投縣">10.南投縣</option>
                        <option value="雲林縣">11.雲林縣</option>
                        <option value="嘉義縣">12.嘉義縣</option>
                        <option value="嘉義市">13.嘉義市</option>
                        <option value="台南市">14.台南市</option>
                        <option value="高雄市">15.高雄市</option>
                        <option value="屏東縣">16.屏東縣</option>
                        <option value="宜蘭縣">17.宜蘭縣</option>
                        <option value="花蓮縣">18.花蓮縣</option>
                        <option value="臺東縣">19.臺東縣</option>
                        <option value="澎湖縣">20.澎湖縣</option>
                        <option value="金門縣">21.金門縣</option>
                        <option value="連江縣">22.連江縣</option>
                      </select>
                </div>
            </div>

            <div class="row mt-2 align-items-center">    
                <div class="col-md-auto border-0 d-inline-flex align-item-center py-1"><font color=red>*</font>姓名:</div>
                <div class="col-md-auto border-0 d-inline-flex align-item-center">
                    <input class="pic border-1 w-75" name="member_name" placeholder="請輸入" value="{{ old('member_name') }}">
                </div>
                <div class="w-100"></div>
                <div class="col-md-auto border-0 d-inline-flex align-item-center py-1 pe-0 me-0"><font color=red>*</font>身份字號:</div>
                <div class="col-md-auto border-0 d-inline-flex align-item-center">
                    <input class="inp border-1 d-inline-flex" name="SSN" placeholder="R123456789" value="{{ old('SSN') }}"  style="width: 120px" oninput="value=value.replace(/[^A-Z0-9]/g,'')">
                </div>
                
                <div class="col-md-auto border-0 d-inline-flex align-item-center"><font color=red>*</font>出生日期:</div>
                <div class="col-md-auto border-1 align-self-center">
                    <input class="border-1 pe-1" type="date" name="Birthday" value="{{ old('Birthday') }}">
                </div>

                <div class="w-100"></div>

                <div class="col-md-auto border-0 d-inline-flex align-item-center py-1"><font color=red>*</font>手機:</div>
                <div class="col-md-auto border-0 d-inline-flex align-item-center">
                    <input class="col-md-auto border-1 w-75 d-inline-flex" type="tel" name="mobile" placeholder="0912345678"  value="{{ old('mobile') }}" 
                    onbeforepaste="clipboardData.setData('text',clipboardData.getData('text').replace(/[^\d]/g,''))" 
                    onkeyup="value=value.replace(/[^\d.]/g,'')" />
                </div>

                <div class="col-md-auto border-0 d-inline-flex align-item-center py-1"><font color=white>*</font>電話:</div>
                <div class="col-md-auto border-0 d-inline-flex align-item-center">
                    <input class="border-1 w-75 d-inline-flex" name="member_phone" type="tel" placeholder="061234567" value="{{ old('member_phone') }}"
                    onbeforepaste="clipboardData.setData('text',clipboardData.getData('text').replace(/[^\d]/g,''))" 
                    onkeyup="value=value.replace(/[^\d.]/g,'')" />
                </div>

                <div class="w-100"></div>
                
                <div class="col-md-auto border-0 d-inline-flex align-item-center py-1"><font color=white>*</font>  身高:</div>
                <div class="col-md-auto border-0 d-inline-flex align-item-center">
                    <input class="pic border-1 w-75" name="Height" type="text" placeholder="cm"  value="{{ old('Height') }}"
                    onbeforepaste="clipboardData.setData('text',clipboardData.getData('text').replace(/[^\d]/g,''))" 
                    onkeyup="value=value.replace(/[^\d.]/g,'')" />
                </div>
                <div class="col-md-auto border-0 d-inline-flex align-item-center py-1"><font color=white>*</font>體重:</div>
                <div class="col-md-auto border-0 d-inline-flex align-item-center">
                    <input class="border-1 w-75 d-inline-flex" name="Weight" type="text" placeholder="kg"  value="{{ old('Weight') }}"
                    onbeforepaste="clipboardData.setData('text',clipboardData.getData('text').replace(/[^\d]/g,''))" 
                    onkeyup="value=value.replace(/[^\d.]/g,'')" />
                </div>

                <div class="w-100"></div>
                <div class="col-md-auto border-0 d-inline-flex align-item-center py-1"><font color=red>*</font>性別:</div>
                <div class="organize col-md-auto border-0 align-self-center">
                    <select class="form-select form-select-sm" name="Gender" aria-label="Default select example">
                        <option value="">請選擇</option>

                        @if ($errors->any())
                        <option selected value="{{ old('Gender') }}">{{ old('Gender') }}</option>
                        @endif

                        <option value="男">男</option>
                        <option value="女">女</option>
                      </select>
                </div>

                {{--
                <div class="col-md-auto border-0 d-inline-flex align-item-center py-1"><font color=red>*</font>血型:</div>
                <div class="organize col-md-auto border-0 align-self-center">
                    <select class="form-select form-select-sm" name="Blood_type" aria-label="Default select example">
                        <option value="">請選擇</option>

                        @if ($errors->any())
                        <option selected value="{{ old('Blood_type') }}">{{ old('Blood_type') }}</option>
                        @endif

                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="O">O</option>
                      </select>
                </div>     
                --}}
                <div class="col-md-auto border-0 d-inline-flex align-item-center py-1"><font color=red>*</font>役別:</div>
                <div class="organize col-md-auto border-0 align-self-center">
                    <select class="form-select form-select-sm w-auto" aria-label="Default select example" name="Branch">
                        <option value="">請選擇</option>

                        @if ($errors->any())
                        <option selected value="{{ old('Branch') }}">{{ old('Branch') }}</option>
                        @endif

                        <option value="陸">陸</option>
                        <option value="海">海</option>
                        <option value="空">空</option>
                        <option value="海陸">海陸</option>
                        <option value="free">免役</option>
                      </select>
                </div>

                <div class="w-100"></div>

                <div class="col-md-auto border-1 d-inline-flex align-item-between py-1"><font color=white>*</font>電子郵件:</div>
                <div class="col-md-5 border-0 d-inline-flex align-item-center">
                    <input type="email" class="border-1 align-content-lg-around w-100" id="mail" name="mail" placeholder="name@example.com"  value="{{ old('mail') }}">
                </div>

                <div class="w-100"></div>
                <div class="col-md-auto border-1 d-inline-flex align-item-between py-1"><font color=red>*</font>戶籍地址:</div>
                <div class="col-md-5 border-0 d-inline-flex align-item-center">
                    <input type="text" class="border-1 align-content-lg-around w-100" placeholder="請輸入住址" name="addr"  value="{{ old('addr') }}">
                </div>
                <div class="w-100"></div>
                <div class="col-md-auto border-1 d-inline-flex align-item-between py-1"><font color=red>*</font>通訊地址:</div>
                <div class="col-md-5 border-0 d-inline-flex align-item-center">
                    <input type="text" class="border-1 align-content-lg-around w-100" placeholder="請輸入住址" name="current_addr" value="{{ old('current_addr') }}">
                </div>

                <div class="w-100"></div>


                <div class="w-100"></div>
                <div class="col-lg-auto border-1 d-inline-flex align-item-betweenp y-1">身份證正面路徑:</div>
                <div class="col-lg-auto border-1 d-inline-flex align-item-center">
                    <input class="employee_pic w-75 border-1" type="file" id="pic_route1" name="IDCard_front"  value="{{ old('IDCard_front') }}">
                </div>
  
                <div class="col-lg-auto border-1 d-inline-flex align-item-between py-1">身份證反面路徑:</div>
                <div class="col-lg-auto border-1 d-inline-flex align-item-center">
                    <input class="employee_pic w-75 border-1" type="file" id="pic_route2" name="IDCard_back"  value="{{ old('IDCard_back') }}">
                </div>

                <div class="w-100"></div>
                <div class="col-lg-auto border-1 d-inline-flex align-item-between py-1">雇員證件照路徑:</div>
                <div class="col-lg-auto border-1 d-inline-flex align-item-center">
                        <input class="employee_pic border-1" type="file" id="pic_route3" name="EmployeeCard"  value="{{ old('EmployeeCard') }}">
                </div>
               
                <div class="col-lg-auto border-1 d-inline-flex align-item-between py-1">其他檔案之路徑:</div>
                <div class="col-lg-auto border-1 d-inline-flex align-item-center">
                    <input class="employee_pic border-1" type="file" id="pic_route4" name="OthersCard"  value="{{ old('OtherCard') }}">
                </div>
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
                <p class="my-0">相關能力資訊</p>
            </div>

            <div class="row">
                <div class="col-md-auto border-0">駕照:
                    <div class="form-check form-check-inline" name="drive">
                        <input class="form-check-input" type="checkbox" value="汽車" id="flexCheckChecked"  name="drive[]" @if(is_array(old('drive')) && in_array('汽車', old('drive'))) checked @endif>
                        <label class="form-check-label" for="flexCheckDefault">汽車</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" value="機車" id="flexCheckDefault"  name="drive[]"  @if(is_array(old('drive')) && in_array('機車', old('drive'))) checked @endif>
                        <label class="form-check-label" for="flexCheckChecked">機車</label>
                    </div>

                </div>

                <div class="col-md-auto border-0" name="language" ss>語文能力:
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" value="中文" id="chinese" name="language[]" @if(is_array(old('language')) && in_array('中文', old('language'))) checked @endif>
                        <label class="form-check-label" for="chinese">中文</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" value="台語" id="taiwanese" name="language[]" @if(is_array(old('language')) && in_array('台語', old('language'))) checked @endif>
                        <label class="form-check-label" for="taiwanese">台語</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" value="客家語" id="hakka" name="language[]" @if(is_array(old('language')) && in_array('客家誥', old('language'))) checked @endif>
                        <label class="form-check-label" for="hakka">客家語</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" value="英語" id="english" name="language[]" @if(is_array(old('language')) && in_array('英語', old('language'))) checked @endif>
                        <label class="form-check-label" for="english">英語</label>
                    </div>
                </div>
                <div class="w-100"></div>
                <div class="col-md-auto border-0 align-self-center py-1">最高學歷:
                    <input class="border-1" name="School" placeholder="畢業學校"  value="{{ old('School') }}">
                </div>
                <div class="col-md-auto border-0 align-self-center py-1">科系:
                    <input class="border-1" name="Department" placeholder="畢業系所"  value="{{ old('Department') }}">
                </div>
                <div class="col-md-auto border-0 align-self-center py-1">畢肄:</div>
                <div class="graduate col-md-auto border-0 align-self-center">
                    <select class="form-select form-select-sm border-1" aria-label="Default select example" name="graduate">
                        
                        @if ($errors->any())
                        <option selected value="{{ old('graduate') }}">{{ old('graduate') }}</option>
                        @endif
                        <option value="畢業">畢業</option>
                        <option value="肄業">肄業</option>
                      </select>
                </div>

            </div>

            <hr>

            <div class="row">
                <p class="my-0">雇用相關設定</p>
            </div>

            <div class="row">

                <div class="col-md-auto borde-0 align-self-center py-1">任職狀態:</div>
                <div class="status col-md-auto border-0 align-self-center">
                    <select class="form-select form-select-sm" aria-label="Default select example"  name="status">
                        <option value="">請選擇</option>
                        @if ($errors->any())
                        <option selected value="{{ old('status') }}">{{ old('status') }}</option>
                        @endif
                        
                        <option value="在職">在職</option>
                        <option value="未到職">未到職</option>
                        <option value="離職">離職</option>
                        <option value="死亡">死亡</option>
                      </select>
                </div>

                <div class="col-md-auto border-0 align-self-center py-1 ps-3">點哨:</div>
                <div class="work_place col-md-auto border-0 align-self-center">
                    <select class="form-select form-select-sm" aria-label="Default select example" name="work_place">
                        
                        @if ($errors->any())
                        <option selected value="{{ old('work_place') }}">{{ old('work_place') }}</option>
                        @endif
                        <option value="">請選擇</option>
                        @foreach($customers as $customer )
                        <option value="{{$customer->firstname}}">{{$customer->firstname}}</option>
                        @endforeach

                    </select>
                </div>


                    <div class="col-md-auto border-0 align-self-center py-1">職稱:
                        <input class="border-1 py-0 my-0" name="position" type="text" value="{{ old('position') }}">
                    </div>

                    @can('admin')
                    {{--<div class="col-md-auto border-0 align-self-center py-1"><font color=white>*</font>薪資:
                        <input class="border-1 py-0 my-0" name="salary" type="number" value="{{ old('salary') }}">
                    </div>--}}
                    <div class="row w-100"></div>
                    {{--choice--}}
                        <div class="col col-md-auto py-1 align-self-center pt-2">
                            <input class="inp" name="userInput" type="radio" value="0"> 月薪

                            <input class="inp2" name="userInput" type="radio" value="1"> 時薪
                        </div>
                        <div class="col col-md-auto py-1">
                            <input type="button" value="切換輸入格式" onclick="choice()">
                        </div>
                    {{--choice--}}

                    <div class="row py-1" id="choice_salary">
                    </div>{{--end choice salary--}}
                    
                    
                    @elsecan('super_manager')
                    {{--<div class="col-md-auto border-0 align-self-center py-1"><font color=white>*</font>薪資:
                        <input class="border-1 py-0 my-0" name="salary" type="number" value="{{ old('salary') }}">
                    </div>--}}
                    <div class="row py-1">{{--choice--}}
                        <div class="col col-md-auto">
                            <input name="userInput" type="radio" value="0">月薪
                        </div>                
                        <div class="col col-md-auto">
                            <input name="userInput" type="radio" value="1">時薪
                        </div>
                        <div class="col col-md-auto">
                            <input type="button" value="切換輸入格式" onclick="choice()">
                        </div>
                    </div>{{--choice--}}

                    <div class="row py-1" id="choice_salary">
                    </div>{{--end choice salary--}}
                    @endcan

                    <div class="w-100"></div>
                    <div class="col-md-auto border-0 align-self-center py-1">員工到職日期:
                        <input class="pic border-1" name="regist" type="date"   value="{{ old('regist') }}">
                    </div>
                    <div class="col-md-auto border-0 align-self-center py-1">員工離職日期:
                        <input class="pic border-1" name="leave" type="date"  value="{{ old('leave') }}">
                    </div>
                </div>

                <div class="w-100"></div>
                <div class="row">
                    <div class="col-md-auto border-0 align-self-center py-1">查核寄出日期:
                        <input class="pic border-1" name="check_send" type="date"  value="{{ old('check_send') }}">
                    </div>
                
                    <div class="col-md-auto border-0 align-self-center py-1">查核寄回日期:
                        <input class="pic border-1" name="check_back" type="date"   value="{{ old('check_back') }}">
                    </div>
                    <div class="row">
                    <div class="col-md-auto border-0 align-self-center py-1">約定寄出日期:
                        <input class="pic border-1" name="agreement_send" type="date"   value="{{ old('agreement_send') }}">
                    </div>
                
                    <div class="col-md-auto border-0 align-self-center py-1">約定寄回日期:
                        <input class="pic border-1" name="agreement_back" type="date"   value="{{ old('agreement_back') }}">
                    </div>
                </div>
                @can('group_admin')
                <div class="row">
                    <div class="col-md-auto border-0 align-self-center py-1">勞保加保日期:
                        <input class="pic border-1" name="labor_date" type="date"  value="{{ old('labor_date') }}">
                    </div>
                    
                    <div class="col-md-auto border-0 align-self-center py-1">勞保投保金額:
                        <input class="pic border-1" name="labor_account" type="number"   value="{{ old('labor_account') }}">
                    </div>
                    <div class="col-md-auto border-0 align-self-center py-1">勞退投保金額:
                        <input class="pic border-1" name="retirement_account" type="number"   value="{{ old('retirement_account') }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-auto border-0 align-self-center py-1">健保加保日期:
                        <input class="pic border-1" name="health_date" type="date"   value="{{ old('health_date') }}">
                     </div>
                    <div class="col-md-auto border-0 align-self-center py-1">健保投保金額:
                        <input class="pic border-1" name="health_account" type="number"   value="{{ old('health_account') }}">
                    </div>
                </div>
                     
                <div class="row">
                <div class="col-md-auto border-0 align-self-center py-1">壽險加保日期:
                    <input class="pic border-1" name="life_date" type="date"   value="{{ old('life_date') }}">
                </div>
                <div class="col-md-auto border-0 align-self-center py-1">團險加保日期:
                    <input class="pic border-1" name="group_date" type="date"   value="{{ old('group_date') }}">
                </div>
                <div class="col-md-auto border-0 align-self-center py-1">顧補加保日期:
                    <input class="pic border-1" name="care_date" type="date"   value="{{ old('care_date') }}">
                </div>
                </div>
                @endcan
                                
                <div class="row">
                <div class="col-md-auto border-0 align-self-center py-1">最後體檢日期:
                    <input class="pic border-1" name="checkup" type="date"   value="{{ old('checkup') }}">
                </div>
                </div>
                <div class="col-md-audo border-0">備註:
                    <input class="pic border-1 w-100" name="memo" placeholder="請簡述"   value="{{ old('memo') }}">
                </div> 
            </div>

            <hr>

   

            <div class="row">
                <p class="my-0">App功能開通</p>
            </div>
            <div class="row">
                <div class="col-md-3 border">帳號:
                    <input class="pic border-0" name="member_account" placeholder="輸入資訊即能開通"   value="{{ old('member_account') }}">
                </div>   
                <div class="col-md-3 border">密碼:
                    <input class="pic border-0" name="member_password" placeholder="輸入資訊即能開通"   value="{{ old('member_password') }}">
                </div>
            </div>

            <div class="enter">
                <div class="row justify-content-center py-2"> 
                    <input class="w-25" type="submit" value="確認送出">
                </div>
            </div>

{{--
            <div class="row">
                <div class="col col-md-auto">
                    <input name="userInput" type="radio" value="0"> 月薪
                </div>                
                <div class="col col-md-auto">
                    <input name="userInput" type="radio" value="1"> 時薪
                </div>
                <div class="col col-md-auto">
                    <input type="button" value="切換" onclick="choice()">
                </div>
            </div>

            <div class="row" id="choice_salary">

            </div>
--}}

            </form> 
        </div>




@endsection



<script text="text/javascript">
    function choice(){
        var userInput = document.querySelector('input[name="userInput"]:checked').value;
        //alert(userInput);
        if (userInput==1){
        var el = document.getElementById("choice_salary");
        el.innerHTML = `
            <div class="work_place col-md-auto border-0 align-self-center" >
                    <select class="form-select form-select-sm" aria-label="Default select example" name="work_place" id="work_place">
                        
                        @if ($errors->any())
                        <option selected value="{{ old('work_place') }}">{{ old('work_place') }}</option>
                        @endif
                        <option value="">請選擇</option>
                        @foreach($customers as $customer )
                        <option value="{{$customer->firstname}}">{{$customer->firstname}}</option>
                        @endforeach

                    </select>
            </div>

            <div class="col-md-auto border-0 align-self-center py-1"><font color=red>*</font>時薪:
                <input class="border-1 py-0 my-0" type="number" value="{{ old('salary') }}" id="salary">
            </div>

            <div class="col col-md-auto">
                <input type="button" value="儲存" onclick="example()" />
            </div>
            <div class="col col-md-auto">
                <input type="button" value="刪除" onclick="example2()" />
            </div>
            <textarea id="text2" style="font-size:large" rows="8" cols="20" name="clock_salary" placeholder="輸入的薪資條件會顯示在此!"></textarea>
        `;
        }
        
        else{
            var el = document.getElementById("choice_salary");
            el.innerHTML = `
            <div class="col-md-auto border-0 align-self-center py-1"><font color=white>*</font>月薪:
                <input class="border-1 py-0 my-0" name="salary" type="number" value="{{ old('salary') }}" id="salary">
            </div>
            `;
        }
    }

    function example(){
            var salary = document.getElementById("salary").value;
            var place = document.getElementById("work_place").value;
            var textnode=document.createTextNode(place+','+salary+',');
            
            if (salary!="" && place!=""){
            var area=document.getElementById("text2");
            area.appendChild(textnode);
            }
            else
            {alert('請輸入完整資訊')}

    }

    function example2(){
            //var area=document.getElementById("text2");

            //area.removeChild(area.firstElementChild);

            const list = document.getElementById("text2");
            list.removeChild(list.lastChild);
    }

    function submit_onclick(){
        var mem_id = document.getElementById("uid").value;
        //document.getElementById("name").value="";
        if (mem_id!="")
        {
        ajaxRequestPost(mem_id);
        }

        else{
        alert('無輸入值，請輸入職工編號');
        }
    }

    function ajaxRequestPost(id){

        $.ajaxSetup({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });

        $.ajax({
            type:'POST',
            url:'/ajaxRequest',
            data:{uid:id},

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