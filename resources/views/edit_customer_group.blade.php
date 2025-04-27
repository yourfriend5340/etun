@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

        <div class="row mx-1">
        <p class="p-test mt-1 mb-0 fs-3">修改客戶群組</p>
            <form method="POST" action="{{ route('customer_group.update') }}" enctype="multipart/form-data" class="row">
              {{ csrf_field() }}

            <div class="row mt-2 align-items-center">    

               <div class="col-md-auto border-0 d-inline-flex align-items-center py-2">群組名稱：</div>
               <div class="col-md-4 border-0 d-inline-flex align-items-start">
                    <input class="place w-50 d-inline-flex" name="group" placeholder="{{old('group')}}" value={{$groups->group}}>
                    <input type="hidden" name="id" value= {{$groups->id}}>
               </div>
               <div class="row w-100"></div>

               <div class="enter">
                  <div class="row mx-1 py-2"> 
                     <input class="btn btn-success w-25" type="submit" value="確認送出">
                  </div>
               </div>

            </div>
            </form> 

            
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Success message -->
            @if(Session::has('success'))
               <div class="alert alert-success">
                  {{ Session::get('success') }}
               </div>
     
            @endif
            @if(Session::has('danger'))
               <div class="alert alert-danger">
                  {{ Session::get('danger') }}
               </div>
     
            @endif

            @if(Session::has('ex_success'))
               <div class="alert alert-success">
                  {{ Session::get('ex_success') }}
               </div>
     
            @endif
            @if(Session::has('ex_error'))
               <div class="alert alert-danger">
                  {{ Session::get('ex_error') }}
               </div>
            @endif
        </div>

@endsection


