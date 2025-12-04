@extends('Admin.layout.master')

@section('title', 'المناديب')
@section('css')

@endsection

@section('content')
<div class="card">
     
    <form class="form theme-form" action="{{ route('admin.representatives.update', $representative) }}" method="post"
        enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="row">
                @include('Admin.representative.__form')
            </div>
        </div>

        <div class="card-footer text-end">
            <button class="btn btn-primary" type="submit">حفظ الأعدادت</button>
        </div>
    </form>
</div>
@endsection
@section('js')
<script src="{{ asset('admin/assets/js/tooltip-init.js') }}"></script>
@endsection