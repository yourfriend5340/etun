@extends('layouts.app')

@section('content')



<div class="row pt-3 mx-1">

    <div class="col-12">
        <h2 class="h_one my-0 p-1">重要公告</h2>
    </div>
    
    <div class="col-12 table-responsive">
        <table class="table table-bordered table-striped table-hover text-center align-middle">

            <thead>
                <tr>
                <td>ID</td>
                <td>標題</td>
                <td>內文</td>
                <td>時間</td>
                </tr>
            </thead>
        
            <tbody>  
                <tr>
                        <td scope="top">{{$topAnn->id}}</td>
                        <td scope="top" width=200px>{{$topAnn->title}}</td>
                        <td scope="top" class="text-start textcontrol">{{$topAnn->announcement}}</td>
                        <td scope="top">{{$topAnn->created_at}}</td>
                </tr>
                @foreach($announcements as $announcement )
                    <tr>
                        <td>{{$announcement->id}}</td>
                        <td>{{$announcement->title}}</td>
                        <td class="text-start textcontrol">{{$announcement->announcement}}</td>
                        <td>{{$announcement->created_at}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    


    @if (count($leaves) != 0)
    <div class="col-12">
        <h2 class="h_two my-0 p-1">請假審核</h2>
    </div>
    <div class="col-12 table-responsive">
        <table class="table table-bordered table-striped table-hover text-center align-middle">

            <thead>
                <tr>
                <td>ID</td>
                <td>類別</td>
                <td>申請人</td>
                <td>起始時間</td>
                <td>結束時間</td>
                <td>原由</td>
                <td>審核</td>
                </tr>
            </thead>

            <tbody>  
                @can('group_admin')
                    @if (isset($leaves))
                        @foreach($leaves as $l )
                            <tr>
                                <td>{{$l->id}}</td>
                                <td>{{$l->type}}</td>
                                <td>{{$l->member_name}}</td>

                                <td>{{$l->start}}</td>
                                @if (!is_null($l->end))
                                    <td>{{$l->end}}</td>
                                @else
                                    <td> - </td>
                                @endif
                                <td>{{$l->reason}}</td>
                                <td>
                                    <input class="btn btn-light btn-md active" id="yes" type="button" value="審核" onclick="submit_request({{$l->id}})">
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7">目前沒有需要審核的申請</td>
                        </tr>
                    @endif

                @else
                    <tr>
                        <td colspan="7">您沒有權限審核</td>
                    </tr>
                @endcan    
            </tbody>
        </table>

        @can('group_admin')
            <div class="d-inline-flex p-2 bd-highlight">
                {{ $leaves->links() }}  
            </div>
        @endcan
    </div>
    @endif
    
    <div class="col-12">
        <h2 class="h_three my-0 p-1">巡邏紀錄</h2>
    </div>

    <div class="col-12 table-responsive">
        <table class="table table-bordered table-striped table-hover text-center align-middle">
            <thead>
                <tr>
                    <td>ID</td>
                    <td>保全員</td>
                    <td>客戶</td>
                    <td>場所</td>
                    <td>照片</td>
                    <td>日期</td>
                    <td>時間</td>
                </tr>
            </thead>
                            
            <tbody>  
                @foreach($patrol_records as $patrol )
                    @if ( $patrol->picturePath != '')
                        <tr>
                            <td scope="top">{{$patrol->id}}</td>
                            <td scope="top">{{$patrol->patrol_upload_user}}</td>
                            <td scope="top">{{$patrol->firstname}}</td>
                            <td scope="top">{{$patrol->patrol_RD_Name}}</td>

                            <td scope="top"> 
                                <input class="btn btn-light btn-md active" id="btn" type="button" value="連結" onclick="submit_onclick_PIC({{$patrol->id}})">
                                <input type="hidden" id="path{{$patrol->id}}" name="path" value={{$patrol->picturePath}} /> 
                            </td>
                            <td scope="top">{{$patrol->patrol_RD_DateB}}</td>
                            <td scope="top">{{$patrol->patrol_RD_TimeB}}</td>
                        </tr>
                    @else
                        <tr>
                            <td>{{$patrol->id}}</td>
                            <td>{{$patrol->patrol_upload_user}}</td>
                            <td>{{$patrol->firstname}}</td>
                            <td>{{$patrol->patrol_RD_Name}}</td>
                            <td> 
                                <em>-</em>
                            </td>
                            <td>{{$patrol->patrol_RD_DateB}}</td>
                            <td>{{$patrol->patrol_RD_TimeB}}</td>
                        </tr>
                    @endif


                @endforeach
            </tbody>
        </table>
    </div>
</div>    
@endsection

<script>
    function submit_onclick_PIC(id) {
        var input = document.getElementById("path"+id);
        var path = input.value;
        var host = window.location.host;
        var url = 'https://'+host+'/'+path;
        var btn = document.getElementById('btn');
        var btnRect = btn.getBoundingClientRect();
        var x = btnRect.left + window.screenX ;
        var y = btnRect.top + btnRect.height + window.screenY + (window.outerHeight - window.innerHeight);
        var win = window.open(url, '_blank', 
            `popup=yes,width=600,height=400,left=${x},top=${y}`);
        
        //window.open(path, '_blank', 'width=600,height=400');
        //window.event.returnValue=false;
    }

    function submit_request(id) {
        if (id !='')
        {
            window.location.href="/table/request/"+id;
            //window.event.returnValue=false;
        }    
    }


    
</script>