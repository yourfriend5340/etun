@extends('layouts.app')

@section('content')


<div class="container container-fluid table-responsive table-bordered p-0 mt-5">

    <table class="table table-bordered table-striped table-hover text-center align-middle table-responsive-md">
        <thead>
            <tr class="col text-left">
            <td>ID</td>
            <td>群組名稱</td>
            <td>名稱</td>
            <td>電話</td>
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
            @if (isset($groups))
            @foreach($groups as $group )
                <tr>
                    <td>{{$group->id}}</td>
                    <td>{{$group->groupName}}</td>
                    <td>{{$group->contactName}}</td>
                    <td>{{$group->contactPhone}}</td>
                    <td>
                        <input class="btn btn-light btn-md active" type="submit" value="更新" onclick="submit_onclick_request({{$group->id}})">
                    </td>
                    @can('admin')
                    <td>
                        <input class="btn btn-light btn-md active" type="button" value="刪除" onclick="submit_onclick({{$group->id}})">
                    </td>

                    @elsecan('super_manager')
                    <td>
                        <input class="btn btn-light btn-md active" type="button" value="刪除" onclick="submit_onclick({{$group->id}})">
                    </td>
                    @endcan
                </tr>
            @endforeach
            @endif
      </tbody>
    </table>
    <div class="d-inline-flex p-2 bd-highlight">
        {{ $groups->links() }}  
    </div>
</div>

@endsection

<script>

    function submit_onclick(id){

        if (confirm('確定要刪除ID： '+id+' 號資料嗎？')==true)
        {window.location.href="/contact/delete/"+id;}

    }

    function submit_onclick_request(id){

        if (confirm('確定要調閱ID： '+id+' 號資料嗎？')==true)
        {window.location.href="/contact/request/"+id;}

    }

</script>