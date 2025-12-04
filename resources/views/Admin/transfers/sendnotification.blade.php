@extends('Admin.layout.master')

@section('title', 'الأشعارات')
@section('css')

@endsection

@section('content')
<div class="card">
    
    <form class="form theme-form" action="{{ route('admin.transfer.sendnotification') }}" method="post">
                @csrf
                <div class="card-body">
            <div class="row">
        @if(isset($representative))

          

        <input  name="id" id="name"  type="hidden"  value="{{ old('id', isset($representative) ? $representative->id : '') }}"  >

            <div class="col-md-6 mt-3">
        <label class="mr-sm-2" for="name" style="font-family: 'Cairo', sans-serif;">اسم المندوب</label>
        <input class="form-control @error('name') is-invalid @enderror"  type="text"
            placeholder="اسم المندوب" value="{{ old('name', $representative->name ?? '') }}" data-parsley-required="true"
            data-parsley-trigger="change">
        @error('name')
        <span class="invalid-feedback text-black font-weight-bold text-capitalize" role="alert">
            {{ $message }}
        </span>
        @enderror
    </div>

        <div class="col-md-6 mt-3">
        <label class="mr-sm-2" for="phone" style="font-family: 'Cairo', sans-serif;">رقم الهاتف</label>
        <input class="form-control @error('phone') is-invalid @enderror" name="phone" id="phone" type="text"
            placeholder="رقم الهاتف" value="{{ old('phone', $representative->phone ?? '') }}" data-parsley-required="true"
            data-parsley-trigger="change">
        @error('phone')
        <span class="invalid-feedback text-black font-weight-bold text-capitalize" role="alert">
            {{ $message }}
        </span>
        @enderror
    </div>


 @endif
                    <div class="col-md-3 mt-3">
        <label class="mr-sm-2" for="name" style="font-family: 'Cairo', sans-serif;">عنوان الأشعار</label>
        <input class="form-control @error('name') is-invalid @enderror" name="title" id="title" type="text"
            placeholder="عنوان الأشعار" value="{{ old('title') }}" data-parsley-required="true"
            data-parsley-trigger="change">
        @error('name')
        <span class="invalid-feedback text-black font-weight-bold text-capitalize" role="alert">
            {{ $message }}
        </span>
        @enderror
    </div>

        <div class="col-md-9 mt-3">
        <label class="mr-sm-2" for="name" style="font-family: 'Cairo', sans-serif;">محتوى الأشعار</label>
        <input class="form-control @error('name') is-invalid @enderror" name="content" id="content" type="text"
            placeholder="محتوى الأشعار" value="{{ old('content')}}" data-parsley-required="true"
            data-parsley-trigger="change">
        @error('name')
        <span class="invalid-feedback text-black font-weight-bold text-capitalize" role="alert">
            {{ $message }}
        </span>
        @enderror
    </div>
            </div>
        </div>

        <div class="card-footer text-end">
            <button class="btn btn-primary" type="submit">حفظ المطلوب</button>
        </div>
    </form>
</div>
@endsection

@section('js')

<script src="{{ asset('admin/assets/js/tooltip-init.js') }}"></script>
@endsection