@extends('layouts.app')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="row mx-1">

        <p class="p-test mt-1 mb-5 fs-3">員工申請資料審核</p>
        <h5>員工申請內容：</h5>
        <table class="table table-bordered table-striped table-hover text-center align-middle">

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
                        <td>
                            <input class="btn btn-light btn-md active" id="yes" type="button" value="同意" onclick="submitY({{$results->id}})">
                            <input class="btn btn-light btn-md active" id="no" type="button" value="否決" onclick="submitN({{$results->id}})">
                        </td>
                    </tr>
           
            </tbody>
        </table>
    </div>

    <div class="row mx-1 mt-5">
        <h5>員工當日及前日班表：</h5>
        <table class="table table-bordered table-striped table-hover text-center align-middle">
            <thead>
                <tr>
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
        ... 
    </div>
@endsection


<script>
    function submitY(id)
    {
        if(window.confirm('確定將單號 '+ id + ' 號審核通過嗎？')){
            window.location.href="/table/update/id=" + id + "&status=Y";
            //window.event.returnValue=false;
        }
    }

    function submitN(id)
    {
        if(window.confirm('確定將單號 '+ id + ' 號否決嗎？')){
            window.location.href="/table/update/id=" + id + "&status=N";
        }
    }
</script>

