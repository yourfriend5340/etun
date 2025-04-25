@extends('layouts.app')

@section('content')

<div class="row mx-1">
    <p class="p-test mt-1 mb-0 fs-3">公告總覽</p>
    
    <table class="table table-bordered table-striped table-hover text-center align-middle ">
        <thead>
            <tr>
            <td>ID</td>
            <td width="200px">標題</td>
            <td>內文</td>
            <td  width="200px">建立時間</td>
            <td>更新</td>

            @can('admin')
            <td>刪除</td>
            <td>頂置</td>
            @elsecan('super_manager')
            <td>刪除</td>
            <td>頂置</td>
            @endcan
            </tr>
        </thead>
      
        <tbody>  
                <tr>
                    <td scope="top">{{$topAnn->id}}</td>
                    <td scope="top">{{$topAnn->title}}</td>
                    <td scope="top"class="text-start textcontrol">{{$topAnn->announcement}}</td>
                    <td scope="top">{{$topAnn->created_at}}</td>
                    <td scope="top">
                        <input class="btn btn-light btn-md active" type="submit" value="更新" onclick="submit_onclick_request({{$topAnn->id}})">
                    </td>
                    @can('admin')
                    <td  scope="top">
                        
                        <input class="btn btn-light btn-md active" type="button" value="刪除" onclick="submit_onclick({{$topAnn->id}})">
                        
                    </td>
                    <td scope="top">
                        <input name="top" type="radio" value={{$topAnn->id}} @if ($topAnn->top == 1) checked @endif onclick="submit_onclick_top({{$topAnn->id}})">
                    </td>

                    @elsecan('super_manager')
                    <td scope="top">
                      
                        <input class="btn btn-light btn-md active" type="button" value="刪除" onclick="submit_onclick({{$topAnn->id}})">
                 
                    </td>
                    <td scope="top">
                        <input name="top" type="radio" value={{$topAnn->id}}  @if ($topAnn->top == 1) checked @endif onclick="submit_onclick_top({{$topAnn->id}})">
                    </td>
                    @endcan
                </tr>
                
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
                    <td>
                        <input name="top" type="radio" value={{$announcement->id}} @if ($announcement->top == 1) checked @endif onclick="submit_onclick_top({{$announcement->id}})">
                    </td>

                    @elsecan('super_manager')
                    <td>
                      
                        <input class="btn btn-light btn-md active" type="button" value="刪除" onclick="submit_onclick({{$announcement->id}})">
                 
                    </td>
                    <td>
                        <input name="top" type="radio" value={{$announcement->id}}  @if ($announcement->top == 1) checked @endif onclick="submit_onclick_top({{$announcement->id}})">
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

        function submit_onclick_top(id){

        if (confirm('確定要頂置ID： '+id+' 號資料嗎？')==true)
        {window.location.href="/announcement/top/"+id;}

    }

</script>