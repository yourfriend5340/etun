@extends('layouts.app')

@section('content')

<div class="row mx-1">
    <p class="p-test mt-1 mb-0 fs-3">客戶資料總覽</p>

    <table class="table table-bordered table-striped table-hover text-center align-middle table-responsive-md">
        <thead>
            <tr class="col text-left">
            <td>ID</td>
            <td>客戶群組</td>

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
            @foreach($customers as $customer )
                <tr>
                    <td>{{$customer->id}}</td>
                    <td>{{$customer->group}}</td>


                    @can('admin')
                    <td>
                        <input class="btn btn-light btn-md active" type="submit" value="更新" onclick="submit_onclick_request({{$customer->id}})">
                    </td>
                    <td>
                        <input class="btn btn-light btn-md active" type="button" value="刪除" onclick="submit_onclick({{$customer->id}})">
                    </td>


                    @elsecan('super_manager')
                    <td>
                        <input class="btn btn-light btn-md active" type="submit" value="更新" onclick="submit_onclick_request({{$customer->id}})">
                    </td>
                    <td>
                        <input class="btn btn-light btn-md active" type="button" value="刪除" onclick="submit_onclick({{$customer->customer_id}})">
                    </td>

                    @endcan
                </tr>
            @endforeach
      </tbody>
    </table>
    <div class="d-inline-flex p-2 bd-highlight">
        {{ $customers->links() }}  
    </div>
</div>

@endsection


<script>

    function submit_onclick(id){

        if (confirm('確定要刪除ID： '+id+' 號資料嗎？')==true)
        {window.location.href="/customer_group/delete/"+id;}

    }

    function submit_onclick_request(id){

        if (confirm('確定要調閱ID： '+id+' 號資料嗎？')==true)
        {window.location.href="/customer_group/request/"+id;}

    }

</script>