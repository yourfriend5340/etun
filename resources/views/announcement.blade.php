@extends('layouts.app')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

        <div class="container container-fluid">

            <form method="POST" action="{{ route('announcement.store') }}" enctype="multipart/form-data" class="row">
              {{ csrf_field() }}


            <div class="row mt-2 align-items-center">    
                <div class="col-md-auto border-0 d-inline-flex align-items-center py-2">公告標頭：</div>
                <div class="col-md-11 border-0 d-inline-flex align-items-center">
                    <input class="ann w-100" name="title" placeholder="請輸入">
                </div>

                <div class="row w-100"></div>

                <div class="col-md-auto border-0 d-inline-flex align-items-center py-2">公告標頭：</div>
                <textarea class="form-control textcontrol" name="text_area" rows="8"></textarea>

            </div>

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
