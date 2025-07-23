@extends('layouts.app')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="row mx-1">
        <p class="p-test mt-1 mb-5 fs-3">補卡申請審核</p>
        <h5>員工申請內容：</h5>
            <table class="table table-bordered table-striped table-hover text-center align-middle" id="leaveTable">
                <thead>
                    <tr>
                        <td>ID</td>
                        <td>申請人ID</td>
                        <td>申請人</td>
                        <td>類別</td>
                        <td>時間</td>
                        <td>審核</td>
                    </tr>
                </thead>

                <tbody>  
                    <tr>
                        <td>{{$results->id}}</td>
                        <td>{{$results->employee_id}}</td>
                        <td>{{$results->member_name}}</td>
                        @if($results->type == 'IN')
                            <td>上班</td>
                        @else
                            <td>下班</td>
                        @endif
                        <td>{{$results->punchTime}}</td>
                        <td>
                            <input class="btn btn-light btn-md active" id="yes" type="button" value="同意" onclick="submitY({{$results->id}})">
                            <input class="btn btn-light btn-md active" id="no" type="button" value="否決" onclick="submitN({{$results->id}})">
                        </td>
                    </tr>
                </tbody>
            </table>
    </div>


    <div class="row mx-1 mt-5">
        <h5>員工排班紀錄：</h5>
        <table class="table table-bordered table-striped table-hover text-center align-middle table-responsive-md">
            <thead>
                <tr class="col text-left">
                    
                    <td>ID</td>
                    <td>員工姓名</td>
                    <td>客戶姓名</td>
                    <td>申請日排班</td>
                    <td>申請日前一天排班</td>
                    <td>定義</td>
                </tr>
            </thead>

            <tbody>
            @if (isset($schedules) && count($schedules) != 0)
                @foreach($schedules as $s )
                        <tr>
                            <td>{{$s->id}}</td>
                            <td>{{$s->member_name}}</td>
                            <td >{{$s->firstname}}</td>
                            <td>{{$s->applyDate}}</td>
                            <td>{{$s->applyYesterday}}</td>
                            <td>{{$s->timeDefind}}</td>
                        </tr>
                @endforeach
            @else
                <tr>
                    <td colspan='6'>查無紀錄</td>
                </tr>
            @endif    
            </tbody>

        </table>
   
    </div>

    <div class="row mx-1 mt-5">
        <h5>員工當日及前日打卡紀錄：</h5>
        <table class="table table-bordered table-striped table-hover text-center align-middle">
            <thead>
                <tr>
                    <td>ID</td>
                    <td>申請人ID</td>
                    <td>申請人</td>
                    <td>類別</td>
                    <td>時間</td>
                </tr>
            </thead>

            <tbody>
                @if(count($history) != 0)
                    @foreach($history as $h)
                    <tr>
                        <td>{{$h->id}}</td>
                        <td>{{$h->employee_id}}</td>
                        <td>{{$h->member_name}}</td>
                        @if($h->type == 'IN')
                            <td>上班</td>
                        @else
                            <td>下班</td>
                        @endif
                        <td>{{$h->punchTime}}</td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan='5'>無任何打卡紀錄</td>
                    </tr>
                @endif
            </tbody>

        </table>
    </div>

@endsection


<script>

    function submitY(id)
    {
        if(window.confirm('確定將單號 '+ id + ' 號審核通過嗎？'))
        {
            window.location.href="/table/update/addtional/id=" + id + "&additional=Y";
            //window.event.returnValue=false;
        }
    }

    function submitN(id)
    {
        if(window.confirm('確定將單號 '+ id + ' 號否決嗎？')){
            window.location.href="/table/update/addtional/id=" + id + "&additional=N";
        }
    }


</script>

