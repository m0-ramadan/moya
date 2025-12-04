@extends('Admin.layout.master')

@section('title', 'الموظف')
@section('css')
<style>
    .error-message {
        color: red;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
</style>
@endsection

@section('content')
<div class="card">
    <!-- عرض رسائل الخطأ العامة (مثل رسائل نجاح أو فشل) -->
    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    <!-- عرض أخطاء التحقق من الصحة العامة -->
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form class="form theme-form" action="{{ route('admin.admins.store') }}" method="post">
        @csrf
        <div class="card-body">
            <div class="row">
                @include('Admin.admin.__form')
            </div>
        </div>

        <div class="card-footer text-end">
            <button class="btn btn-primary" type="submit">حفظ الإعدادات</button>
        </div>
    </form>
</div>
@endsection

@section('js')
<script src="{{ asset('admin/assets/js/tooltip-init.js') }}"></script>
@endsection