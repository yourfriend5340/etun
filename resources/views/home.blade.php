@extends('layouts.app')

@section('content')


<div class="container container-fluid">
    <div class="row pt-3 justify-content-between">
        <div class="col-7 col-lg-7">
            <h2 class="h_one my-0 p-1">重要公告</h2>

               
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

        <div class="col-5 col-lg-5">
                <h2 class="h_two my-0 p-1">表單管理</h2>

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
                            {{--@foreach($announcements as $announcement )
                                <tr>
                                    <td>{{$announcement->id}}</td>
                                    <td>{{$announcement->title}}</td>
                                    <td class="text-start">{{$announcement->announcement}}</td>
                                    <td>{{$announcement->created_at}}</td>
                                </tr>
                            @endforeach--}}
                        </tbody>
                    </table>
 
            
        </div>
    </div>    

    <div class="row pt-3 d-flex">
        <div class="col-12 col-lg-12">
            <h2 class="h_three my-0 p-1">巡邏紀錄</h2>
        </div>

        <div class="col-12 col-md-12 table-responsiveHome">
            <table class="table table-bordered table-striped table-hover text-center align-middle">
                <thead>
                    <tr>
                        <td>ID</td>
                        <td>保全員</td>
                        <td>客戶</td>
                        <td>場所</td>
                        <td>日期</td>
                        <td>時間</td>
                    </tr>
                </thead>
                                
                <tbody>  
                    @foreach($patrol_records as $patrol )
                        <tr>
                            <td>{{$patrol->id}}</td>
                            <td>{{$patrol->patrol_upload_user}}</td>
                            <td>{{$patrol->firstname}}</td>
                            <td>{{$patrol->patrol_RD_Name}}</td>
                            <td>{{$patrol->patrol_RD_DateB}}</td>
                            <td>{{$patrol->patrol_RD_TimeB}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
