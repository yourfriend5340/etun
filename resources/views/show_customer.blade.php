@extends('layouts.app')

@section('content')

<div class="con container-fluid table-responsive table-bordered p-0">
    <div class="d-flex justify-content-end">
  
        <a class="btn btn-secondary m-2" href="{{route('customer_asc')}}" role="button">遞增</a>

        <a class="btn btn-secondary m-2" href="{{route('customer_desc')}}" role="button">遞減</a>

    </div>
    <table class="table table-bordered table-striped table-hover text-center align-middle table-responsive-md">
        <thead>
            <tr class="col text-left">
            <td>ID</td>
            <td>客戶編號</td>
            <td>客戶名稱</td>
            <td>客戶群組</td>
            <td>客戶status</td>
            {{--<td>客戶active</td>--}}
            <td>客戶地址</td>
            <td>電話</td>
            <td>帳號</td>
            <td>經度</td>
            <td>緯度</td>



            @can('admin')
            <td>更新</td>
            <td>刪除</td>
            <td>聯絡人</td>
            @elsecan('super_manager')
            <td>更新</td>
            <td>刪除</td>
            <td>聯絡人</td>
            @endcan
            
            </tr>
        </thead>
      
        <tbody>  
            @foreach($customers as $customer )
                <tr>
                    <td>{{$customer->customer_id}}</td>
                    <td>{{$customer->customer_sn}}</td>
                    <td class="text-start">{{$customer->firstname}}</td>
                    <td>{{$customer->group}}</td>
                    <td>{{$customer->status}}</td>
                    {{--<td>{{$customer->active}}</td>--}}
                    <td class="text-start">{{$customer->addr}}</td>
                    <td>{{$customer->tel}}</td>
                    <td>{{$customer->account}}</td>
                    <td>{{$customer->lng}}</td>
                    <td>{{$customer->lat}}</td>

                    @can('admin')
                    <td>
                        <input class="btn btn-light btn-md active" type="submit" value="更新" onclick="submit_onclick_request({{$customer->customer_id}})">
                    </td>
                    <td>
                        <input class="btn btn-light btn-md active" type="button" value="刪除" onclick="submit_onclick({{$customer->customer_id}})">
                    </td>
                    <td>
                        <input class="btn btn-light btn-md active" type="button" value="編輯" onclick="submit_onclick1({{$customer->customer_id}})">
                    </td>

                    @elsecan('super_manager')
                    <td>
                        <input class="btn btn-light btn-md active" type="submit" value="更新" onclick="submit_onclick_request({{$customer->customer_id}})">
                    </td>
                    <td>
                        <input class="btn btn-light btn-md active" type="button" value="刪除" onclick="submit_onclick({{$customer->customer_id}})">
                    </td>
                    <td>
                        <input class="btn btn-light btn-md active" type="button" value="編輯" onclick="submit_onclick1({{$customer->customer_id}})">
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
        {window.location.href="/customer/delete/"+id;}

    }

    function submit_onclick1(id){

        if (confirm('確定要編輯ID： '+id+' 號資料的聯絡人嗎？')==true)
        {window.location.href="/customer/request/"+id;}

    }

    function submit_onclick_request(id){

        if (confirm('確定要調閱ID： '+id+' 號資料嗎？')==true)
        {window.location.href="/customer/request/"+id;}

    }

</script>