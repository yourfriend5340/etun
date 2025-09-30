@extends('layouts.app')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="row mx-1">

        <p class="p-test mt-1 mb-5 fs-3">員工申請資料審核</p>
        <h5>員工申請內容：</h5>
        <table class="table table-bordered table-striped table-hover text-center align-middle" id="leaveTable">

            <thead>
                <tr>
                <td>ID</td>
                <td>類別</td>
                <td>職工編號</td>
                <td>申請人</td>
                <td>起始時間</td>
                <td>結束時間</td>
                <td>原由</td>
                <td>審核</td>
                </tr>
            </thead>

            <tbody>  
                <tr>
                    <td>{{$results->id}}</td>
                    <td>{{$results->type}}</td>
                    <td>{{$results->empid}}</td>
                    <td>{{$results->member_name}}</td>
                    <td>{{$results->start}}</td>
                    <td>{{$results->end}}</td>
                    <td>{{$results->reason}}</td>
                    
                    @if ($results->type != '離職')   
                        <td>
                            <input class="btn btn-light btn-md active" id="yes" type="button" value="同意" onclick="submitY({{$results->id}})">
                            <input class="btn btn-light btn-md active" id="no" type="button" value="否決" onclick="submitN({{$results->id}})">
                        </td>
                    @else
                        <td>
                            <input class="btn btn-light btn-md active" id="yes" type="button" value="同意" onclick="submit2Y({{$results->id}})">
                            <input class="btn btn-light btn-md active" id="no" type="button" value="否決" onclick="submit2N({{$results->id}})">
                        </td>
                    @endif
                </tr>
           
            </tbody>
        </table>
    </div>

    @if ($results->type != '離職')   
    <div class="row mx-1 mt-5">
        <h5>員工當日及前日班表：</h5>
        <table class="table table-bordered table-striped table-hover text-center align-middle">
            <thead>
                <tr>
                    <td>核選</td>
                    <td>客戶</td>
                    <td>班別</td>
                    <td>開始時間</td>
                    <td>結束時間</td>
                </tr>
            </thead>

            @php
                if(isset($yesterday))
                {
 
                    for($i=0;$i<count($yesterday);$i++)
                    {
                        for($j=0;$j<count($yesterday[$i]);$j++)
                        {
                            echo '<tr>';
                            echo '<td> <input type="radio" id="y'.$i.$j.'" name="drone" value="'.$yesterday[$i][$j]['customer_id'].'"></td>';
                            echo '<td>'.$yesterday[$i][$j]['customer'].'</td>';
                            echo '<td>'.$yesterday[$i][$j]['class'].'</td>';
                            echo '<td>'.$yesterday[$i][$j]['start'].'</td>';
                            echo '<td>'.$yesterday[$i][$j]['end'].'</td>';   
                            echo '</tr>';
                        }
                    }

                }

                if(isset($today))
                {
   
                    for($i=0;$i<count($today);$i++)
                    {

                        for($j=0;$j<count($today[$i]);$j++)
                        {
                            echo '<tr>';
                            echo '<td> <input type="radio" id="t'.$i.$j.'" name="drone" value="'.$today[$i][$j]['customer_id'].'"></td>';
                            echo '<td>'.$today[$i][$j]['customer'].'</td>';
                            echo '<td>'.$today[$i][$j]['class'].'</td>';
                            echo '<td>'.$today[$i][$j]['start'].'</td>';
                            echo '<td>'.$today[$i][$j]['end'].'</td>';   
                            echo '</tr>';
                        }    
                    }
                    echo '</tr>';
                }

            @endphp
        </table>
    </div>

    <div class="row mx-1 mt-5">
        <h5>代理人選取：</h5>
            <div class="col-md-2 align-self-center">
                <label for="selectId">選擇代班員工：</label>Í
                <select class="selectName align-self-center border-1" id="selectId" name="selectId" onchange="select_change()">   
                <option value="">--請選擇--</option>
                @foreach($empList as $e )
                    <option value="{{$e->member_sn}}">{{$e->member_name}}</option>
                @endforeach 
                </select>
                <!--<input class="place w-25 d-inline-flex mx-2" name="inputName" placeholder="或輸入名字">-->
                <em>(下拉選單僅顯示"在職"人員)</em>
            </div>
    </div>
    <div id='check'></div>
    @endif


@endsection


<script>

    function submitY(id)
    {
        var userSelect = document.getElementById('selectId');
        var index = userSelect.selectedIndex;
        var mem_name = userSelect.options[index].text;
        var mem_id = userSelect.options[index].value;
        var selectRadio = document.querySelector('input[name="drone"]:checked');

        if(selectRadio){
            var selectCus = selectRadio.value;
        }
        else{
            var selectCus = 0;
        }
        
        if(mem_id != "" && mem_name!="" && selectCus != 0)
        {
            var check = document.getElementById('result').value;
            if(check == 'Y')
            {
                if(window.confirm('確定將單號 '+ id + ' 號審核通過嗎？\n(代理人：' + mem_name + '，地點：'+ selectCus + ')'))
                {
                    window.location.href="/table/update/id=" + id + "&status=Y&emp=" + mem_id + "&cus=" + selectCus;
                    //window.event.returnValue=false;
                }
            }
            else
            {
                alert('您選擇代理人無法排班，請選擇後再行點選！！');
            }
        }
        else
        {
            alert('您無選擇代理人或核選標籤，請選擇後再行點選！！');
        }
    }

    function submitN(id)
    {
        if(window.confirm('確定將單號 '+ id + ' 號否決嗎？')){
            window.location.href="/table/update/id=" + id + "&status=N&emp=NULL&cus=NULL";
        }
    }


    function submit2Y(id)
    {

        if(window.confirm('確定將單號 '+ id + ' 號審核通過嗎？'))
        {
            window.location.href="/table/update/id=" + id + "&status=Y&emp=NULL&cus=NULL";
            //window.event.returnValue=false;
        }
    }

    function submit2N(id)
    {
        if(window.confirm('確定將單號 '+ id + ' 號否決嗎？')){
            window.location.href="/table/update/id=" + id + "&status=N&emp=NULL&cus=NULL";
        }
    }

    function select_change(id){
        var userSelect = document.getElementById('selectId');
        var index = userSelect.selectedIndex;
        var mem_name = userSelect.options[index].text;
        var mem_id = userSelect.options[index].value;

        var tbobj = document.getElementById('leaveTable');
        var time = tbobj.rows[1].cells[4].innerHTML;
        var etime = tbobj.rows[1].cells[5].innerHTML;

        if(mem_id != "")
        {
            if(confirm('將開始檢查  ' + mem_name + '  該日是否能排班？'))
            {
                ajaxRequestSchedule(mem_id,time,etime);
            }
            else{
                return false;
            }
        }
        else{
            return false;
        }

    }    

    function ajaxRequestSchedule(id,time,etime){

        $.ajaxSetup({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });

        $.ajax({
            type:'POST',
                dataType: 'json',
            url:'/ajaxRequestSchedule',
            data:{  cid:id,
                    ctime:time,
                    etime:etime
                },

            success:function(data) {
               //$("#data").html(data.msg);
               //alert("ID是:" + id + "\n狀態:" + status);
               //console.log(data);
                //var el = document.getElementById("result");
                //el.innerHTML = "<h1>" + data + "</h1>";
                if(data.result == true){
                    alert('可以排班');

                    var el = document.getElementById("check");
                    el.innerHTML = `<input type="hidden" value="Y" id="result">`;
                }
                else{
                    alert('不可排班，己於 ' + decodeURI(data.customer) + ' 有排班，\n時間是：' + data.start + ' 至 ' + data.end);

                    var el = document.getElementById("check");
                    el.innerHTML = `<input type="hidden" value="N" id="result">`;
                }
               
            },
            error: function (msg) {
               console.log(msg);
               var errors = msg.responseJSON;
            }
         });
    }

</script>

