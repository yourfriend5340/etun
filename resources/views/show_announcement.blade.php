@extends('layouts.app')

@section('content')

<div class="container container-fluid table-responsive table-bordered p-0">
    <p class="p-test mt-1 mb-0 fs-3">公告</p>
    
    <table class="table table-bordered table-striped table-hover text-center align-middle vh-100">
        <thead>
            <tr>
            <td>ID</td>
            <td width="200px">標題</td>
            <td>內文</td>
            <td  width="110px">建立時間</td>
            <td>更新</td>

            @can('admin')
            <td>刪除</td>
            @elsecan('super_manager')
            <td>刪除</td>
            @endcan
            </tr>
        </thead>
      
        <tbody>  
            @foreach($announcements as $announcement )
                <tr>
                    <td>{{$announcement->id}}</td>
                    <td>{{$announcement->title}}</td>
                    <td class="text-start textcontrol">{{$announcement->announcement}}</td>
                    <td>{{$announcement->created_at}}</td>
                    <td>
                  
                        <input class="btn btn-light btn-md active" type="submit" value="更新" onclick="submit_onclick_request({{$announcement->id}})">
                       
                    </td>
                    @can('admin')
                    <td>
                        
                        <input class="btn btn-light btn-md active" type="button" value="刪除" onclick="submit_onclick({{$announcement->id}})">
                        
                    </td>

                    @elsecan('super_manager')
                    <td>
                      
                        <input class="btn btn-light btn-md active" type="button" value="刪除" onclick="submit_onclick({{$announcement->id}})">
                 
                    </td>
                    @endcan
                </tr>
            @endforeach
      </tbody>
    </table>

    <div class="d-inline-flex p-2 bd-highlight">
        {{ $announcements->links() }}  
    </div>
   
</div>

@endsection
<script>

    function submit_onclick(id){

        if (confirm('確定要刪除ID： '+id+' 號資料嗎？')==true)
        {window.location.href="/announcement/delete/"+id;}

    }

    function submit_onclick_request(id){

        if (confirm('確定要調閱ID： '+id+' 號資料嗎？')==true)
        {window.location.href="/announcement/request/"+id;}

    }

</script>