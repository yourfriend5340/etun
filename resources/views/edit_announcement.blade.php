@extends('layouts.app')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

        <div class="row mx-1">
            <p class="p-test mt-1 mb-0 fs-3">更新公告</p>
            <form method="POST" action="{{ route('announcement.update') }}" enctype="multipart/form-data" class="row">
              {{ csrf_field() }}

            @foreach($announcements as $ann )
            <div class="row mt-2 align-items-center">
                <div class="col-md-auto border-0 d-inline-flex align-items-center py-2">公告編號：</div>
                <div class="col-md-10 border-0 d-inline-flex align-items-center">
                    <input class="ann w-100" name="aid" value={{$ann->id}} readonly>
                </div>
            </div>
            <div class="row">
                <div class="col-md-auto border-0 d-inline-flex align-items-center py-2">公告標頭：</div>
                <div class="col-md-10 border-0 d-inline-flex align-items-center">
                    <input class="ann w-100" name="title" value={{$ann->title}}>
                </div>
            </div>   
            <div class="row">
                <div class="col-md-auto align-items-center d-inline-flex">
                    <label for="exampleFormControlTextarea1" class="form-label">公告內文：</label>
                </div>
                <div class="col-md-10 align-items-center">
                    <textarea class="form-control px-1" name="text_area" rows="8">{{$ann->announcement}}</textarea>
                </div>
            </div>
            @endforeach
            <div class="enter">
                <div class="row justify-content-center py-2"> 
                    <input class="btn btn-success w-25" type="submit" value="確認送出">
                </div>
            </div>
            </form> 
        </div>

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

@endsection
