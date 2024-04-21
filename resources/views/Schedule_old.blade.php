@extends('layouts.app')

@section('content')

        <div class="container container-fluid table-responsive table-bordered p-0 align-items-center">
            <p class="p-test mt-1 mb-0 fs-3">排班資料1</p>
 
            <table class="table table-bordered table-striped table-hover align-middle text-center">
                <thead>    
                <tr>
                    <th scope="col" class="thh text-nowrap">名字/日期</th>
                    @for ($i=1;$i<=31;$i++)
                        <th>{{ $i }}</th>
                    @endfor
                    </tr>            
                </thead>

                <tbody>
                    <!--5人份資料-->
                    @for ($i=1;$i<=5;$i++)
                    <tr>
                        <td>人員{{ $i }}</td>
                        
                        <!--產生一個月份的day1~day31的id-->
                        @for ($j=1;$j<=31;$j++)
                            <td class="tdd"><select class="select mb-1" id="person{{ $i }}_day{{ $j }}" onchange="changeCollege({{$i}},{{$j}},this.selectedIndex)">
                            {{--</select><select id="person{{$i}}_sector-day{{ $j }}"></select>--}}
                        </td>
                        @endfor
                    </tr>
                    @endfor 
                    
                    <tr>
                        <td>檢查結果</td>    
                        <td>o</td>
                        <td>x</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>  
                </tbody>
            </table>
            
            <div>
                <div class="row">
                    <div class="col color">若需定義時間，排班前，請確實輸入，否則檢查功能會出錯</div>   
                <div class="row align-items-center">
                    <div class="col-auto">新增彈性班別：</div>
                    <div class="col-md-auto">
                        <input type="text" class="form-control width=30px" id="patrol_add_count" aria-describedby="add_count_Help" placeholder="名稱">
                    </div>
                    <div class="col-md-auto">
                        <input type="time" class="form-control width=30px" id="patrol_add_count" aria-describedby="add_count_Help" placeholder="名稱">
                    </div>
                    <div class="col-md-auto">
                        <input type="time" class="form-control width=30px" id="patrol_add_count" aria-describedby="add_count_Help" placeholder="名稱">
                    </div>
                    <!--<div class="col-md-1">
                        <input type="number" min="1" step="1" class="form-control width=30px" id="patrol_add_count" aria-describedby="add_count_Help" placeholder="數量">
                    </div>-->
                    <div class="col-md-auto">
                        <input type="submit" class="form-control width=30px" id="patrol_add_count" aria-describedby="add_count_Help" placeholder="名稱">
                    </div>

                </div> 

            </div>
        </div>
        

        <br><br>
        
        <div class="container container-fluid">
            <p class="p-test mt-1 mb-0 fs-3">排班資料2</p>

            <div class="row container-fluid border-1 p-0 align-items-center text-center">
                @for ($i=0;$i<=6;$i++)
                    @if ($i===0)
                        <div class="col-1 border m-0 p-0">人員/日期</div>
                        @for ($j=1;$j<=31;$j++)
                            <div class="col border m-0 p-0">{{$j}}</div>
                        @endfor
                        <div class="w-100"></div>
                    @else
                        <div class="col-1 border m-0 p-0">人員{{$i}}</div>
                        @for ($j=1;$j<=31;$j++)
                            <div class="col border m-0 p-0">日</div>
                        @endfor
                    <div class="w-100"></div>
                    @endif
                @endfor
                <div class="col-1 border m-0 p-0 border">人員/日期</div>
                @for ($j=1;$j<=31;$j++)
                <div class="col border m-0 p-0">O</div>
                @endfor
            </div>
        </div>
        
        <!--
        <form>
            <p>請選擇班別</p>
            //給定這id，等等需要用他填入學院資料,onchange內動到這element會呼叫裡面的方法this.selectedIndex是選到第幾項的值(0開始)傳入當參數
            <select id="list" onchange="changeCollege(this.selectedIndex)"></select>
            <br>
            <br>
            <p>當你選擇日夜班後，下列選單會列出該日所有班別</p>
            <select id="sector-list"></select>//給定id，用他填入對應的資料
        </form>
        -->

        <div class="container mt-5">

            <!-- Success message -->
            @if(Session::has('success'))
               <div class="alert alert-success">
                  {{ Session::get('success') }}
               </div>
            @endif
      

            
            <!--Import data-->
            <form method='post' action="{{ route('Schedules.importdata') }}" enctype="multipart/form-data">
               @csrf
               <div class="mb-3">
                  <label for="file" class="form-label">File</label>
                  <input type="file" class="form-control" id="file" name="file" value="">
               </div>
      
               <button type="submit" class="btn btn-success">Import</button>
            </form>
            
      
            <!-- Import data with validation -->
            <h2 class='mt-5'>匯入排班資料</h2>
            {{-- Display errors --}}
            @if (count($errors) > 0)
               <div class="row">
                  <div class="col-md-12 ">
                      <div class="alert alert-danger">
                         <ul>
                            @foreach($errors->all() as $error)
                               <li>{{ $error }} </li>
                            @endforeach 
                         </ul> 
                      </div>
                  </div>
               </div>
            @endif
      
            <form method='post' action="{{ route('Schedules.validateandimportdata') }}" enctype="multipart/form-data">
               {{--@csrf
               <button type="submit" class="btn btn-success">Import</button>--}}
               @csrf
               <div class="mb-3">
                  <label for="file" class="form-label">請選擇檔案：</label>
                  <input type="file" class="form-control" id="files" name="files" value="">
               </div>
      
               <button type="submit" class="btn btn-success">Import</button>
            </form>
         </div>


         <!--export-->
         <div class="container mt-5">

            <!--<a class="btn btn-primary" href="{{ route('schedules.exportcsv') }}">CSV Export</a> &nbsp;&nbsp;-->
            <a class="btn btn-primary" href="{{ route('schedules.exportexcel') }}">Excel Export</a><br><br>
         </div>


        <!--use phpspreadsheet-->
        <!-- 显示上传文件产生的错误 -->
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
                </ul>
        
            @endif

        <!-- 表格视图 -->
            <form method="POST" action="{{ route('schedules.import') }}" enctype="multipart/form-data">
            {{ csrf_field() }}
                <div class="mb-3">
                    <label for="file" class="form-label">請選擇檔案：</label>
                    <input id="file" type="file" class="form-control" name="select_file" accept="">
                </div>
                
                <button type="submit" class="btn btn-success">上傳表格</button>
            </form>

    </body>
</html>

    <script type="text/javascript">
        //學院的陣列
        var shifts=['休假','日班','夜班','請假'];
        
   
        //var shiftSelect=document.getElementById("list");

    //5人份
    for(var p=1;p<=5;p++){    
        //day1~31的日夜tag標籤    
        for(var j=1;j<32;j++){
            //製造一個字串，以html的語法填入的陣列
            var inner="";
            
            //抓到標籤列
            var shiftSelect=document.getElementById("person"+p+"_day"+j);
            //console.log("person"+p+"_day"+j);

            for(var i=0;i<shifts.length;i++){
                //inner第一行就會像是 <option value=0>休</option>
                inner=inner+'<option value='+i+'>'+shifts[i]+'</option>';
           }
        
            //innerHTML 賦值inner給這element屬性
            shiftSelect.innerHTML=inner;
        }
    }        
        /*
        其實就是用程式碼的方式把XML文件修改成這樣
        <select id="college-list">
            <option value="0">商學院</option>
            <option value="1">工學院</option>
            <option value="2">建設學院</option>
            <option value="3">建築專業學院</option>		
            ....		
        </select>
        */

        //這裡放點哨的陣列(有順序性)
        var sectors=new Array();
            sectors[0]=[''];
            sectors[1]=['大門','後門'];
            sectors[2]=['東門','西門'];
            //sectors[0]=['會計學系 ',' 國際經營與貿易學系' ,' 國際經營與貿易學系國際企業管理全英語學士班' ,' 財稅學系' ,' 合作經濟暨社會事業經營學系' ,' 統計學系 ',' 經濟學系' ,' 企業管理學系' ,' 行銷學系' ,' 國際企業管理學士學位學程(英語專班)' ,' 商學進修學士學位學程' ,'財經法律研究所' ,' 科技管理碩士學位學程' ,' 產業碩士專班' ,' 商學專業碩士在職學位學程' ,'商學博士學位學程 '];
			//sectors[1]=['機械與電腦輔助工程學系 ',' 纖維與複合材料學系 ',' 工業工程與系統管理學系 ',' 化學工程學系 ',' 航太與系統工程學系 ',' 精密系統設計學士學位學程 ','電聲碩士學位學程 ',' 綠色能源科技碩士學位學程 ',' 創意設計碩士學位學程 ',' 材料與製造工程碩士在職專班 ',' 智能製造與工程管理碩士在職學位學程 ','機械與航空工程博士學位學程 '];	
			//sectors[2]=['土木工程學系 ',' 水利工程與資源保育學系 ',' 都市計畫與空間資訊學系 ',' 運輸與物流學系 ',' 土地管理學系 ','景觀與遊憩碩士學位學程 ',' 專案管理碩士在職學位學程 ',' 建設碩士在職學位學程 ','土木水利工程與建設規劃博士學位學程 '];	
			//sectors[3]=['風險管理與保險學系 ',' 財務金融學系 ',' 財務工程與精算學士學位學程 ','金融碩士在職學位學程 ','金融博士學位學程 '];
			//sectors[4]=['建築專業學院學士班 ',' 建築學士學位學程 ',' 室內設計學士學位學程 ',' 室內設計進修學士班 ',' 創新設計學士學位學程 ',' 建築碩士學位學程 ',' 建築碩士在職學位學程','澳洲墨爾本皇家理工大學商學與創新雙學士學位學程 ','美國聖荷西州立大學商學大數據分析雙學士學位學程 ']
			//sectors[5]=['美國普渡大學電機資訊雙學士學位學程 ','西班牙薩拉戈薩大學物流供應鏈管理與創新創業雙碩士學位學程 ','國際經營管理碩士學位學程 '];
			//sectors[6]=['資訊工程學系 ',' 電機工程學系 ',' 電子工程學系 ',' 自動控制工程學系 ',' 通訊工程學系',' 資電不分系榮譽班 ','資訊電機工程碩士在職學位學程 ',' 產業研發碩士專班 ',' 生醫資訊暨生醫工程碩士學位學程 ',' 視光科技碩士在職學位學程 ','電機與通訊工程博士學位學程 ',' 智慧聯網產業博士學位學程'];
			//sectors[7]=['中國文學系 ',' 外國語文學系 ','歷史與文物研究所 ',' 公共事務與社會創新研究所 '];
			//sectors[8]=['應用數學系 ',' 環境工程與科學學系 ',' 材料科學與工程學系 ',' 光電學系 ','微積分教學中心 ',' 物理教學研究中心 '];
			//sectors[9]=['經營管理碩士在職學位學程 ',' 電子商務碩士在職專班 '];
			//sectors[10]=[];
			//sectors[11]=['通識教育中心 ',' 雲端學院 ','外語教學中心 ',' 國語文教學中心 ','全校國際生大一不分系學士班 '];

        //動到"list"這select元素後呼叫此方法
        function changeCollege(person,day,index){
            console.log('人員：'+person+"的第"+day+"天，選擇"+index);
            //跟剛剛一樣，製造一個字串，以html的語法填入系所的陣列
            var Sinner="";

            for(var i=0;i<sectors[index].length;i++){
                Sinner=Sinner+'<option value='+i+'>'+sectors[index][i]+'</option>';
            }
            //var test="person"+person+"_sector-day"+day;
            //console.log(test);

            //抓到"sector-list"這select元素，修改其值
            var sectorSelect=document.getElementById("person"+person+"_sector-day"+day);
            //var sectorSelect=document.getElementById("person1_sector-day1");
            sectorSelect.innerHTML=Sinner;
            //console.log(sectorSelect.innerHTML);
        }


        //呼叫"changeCollege"，輸出資料
       //changeCollege(document.getElementById("person1_day1").selectedIndex);

       //for (var i=1;i<=5;i++){
       //     for (var j=1;i<=31;j++){
       //         //console.log('第'+i+'個人是'+j+'班');
                //console.log(test);
       //     }

       //}






        </script>


@endsection
