@extends('Admin.layout.master')

@section('title', 'الموظف')
@section('css')

@endsection

@section('content')
    <div class="card">
     
        <form class="form theme-form" action="{{ route('admin.transfer.update') }}" method="post">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="row">
                @include('Admin.transfers.__form')
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
