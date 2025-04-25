@extends('layouts.app')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

        <div class="row mx-1">
            <p class="p-test mt-1 mb-0 fs-3">新增公告</p>
            <form method="POST" action="{{ route('announcement.store') }}" enctype="multipart/form-data" class="row">
              {{ csrf_field() }}

                <div class="row align-items-center mt-2">    
                    <div class="col-md-auto border-0 d-inline-flex align-items-center py-2">公告標頭：</div>
                    <div class="col-md-10 border-0 d-inline-flex align-items-center">
                        <input class="ann w-100" name="title" placeholder="請輸入">
                    </div>
                    <div class="col-md-auto border-0 d-inline-flex align-items-center py-2">
                        <div class="form-check form-check-inline" name="top">
                            <label class="form-check-label" for="flexCheckDefault">頂置</label>
                            <input class="form-check-input" type="checkbox" value="1" id="Check"  name="top" @if(is_array(old('top')) && in_array('Y', old('top'))) checked @endif>
                        </div>
                    </div>
                </div>

                <div class="row align-items-center">
                    <div class="col-md-auto border-0 d-inline-flex align-items-center py-2">公告內文：</div>
                    <div class="col-md-10">
                        <textarea class="form-control textcontrol px-1" name="text_area" rows="8"></textarea>
                    </div>
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
