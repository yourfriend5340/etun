@extends('layouts.app')

@section('content')


<div class="container container-fluid table-responsive table-bordered p-0 mt-5">

    <table class="table table-bordered table-striped table-hover text-center align-middle table-responsive-md">
        <thead>
            <tr class="col text-left">
            <td>ID</td>
            <td>公司名稱</td>
            <td>住址</td>
            <td>電話</td>
            <td>建立時間</td>
            <td>更新時間</td>
            @can('admin')
            <td>更新</td>
            <td>刪除</td>
            @elsecan('super_manager')
            <td>更新</td>
            <td>刪除</td>
            @endcan
            
            </tr>
        </thead>
      
        <tbody>  
            @foreach($organizes as $organize )
                <tr>
                    <td>{{$organize->id}}</td>
                    <td>{{$organize->company}}</td>
                    <td class="text-start">{{$organize->addr}}</td>
                    <td>{{$organize->tel}}</td>
                    <td>{{$organize->created_at}}</td>
                    <td>{{$organize->updated_at}}</td>
                    <td>
                        <input class="btn btn-light btn-md active" type="submit" value="更新" onclick="submit_onclick_request({{$organize->id}})">
                    </td>
                    @can('admin')
                    <td>
                        <input class="btn btn-light btn-md active" type="button" value="刪除" onclick="submit_onclick({{$organize->id}})">
                    </td>

                    @elsecan('super_manager')
                    <td>
                        <input class="btn btn-light btn-md active" type="button" value="刪除" onclick="submit_onclick({{$organize->id}})">
                    </td>
                    @endcan
                </tr>
            @endforeach
      </tbody>
    </table>
    <div class="d-inline-flex p-2 bd-highlight">
        {{ $organizes->links() }}  
    </div>
</div>

@endsection

<script>

    function submit_onclick(id){

        if (confirm('確定要刪除ID： '+id+' 號資料嗎？')==true)
        {window.location.href="/organize/delete/"+id;}

    }

    function submit_onclick_request(id){

        if (confirm('確定要調閱ID： '+id+' 號資料嗎？')==true)
        {window.location.href="/organize/request/"+id;}

    }

</script>