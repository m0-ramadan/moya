@extends('Admin.layout.master')

@section('title', 'الموظف')
@section('css')

@endsection

@section('content')
<div class="card">
    <div class="card-header pb-0">
        <h5>Create Admin</h5>
    </div>
    <form class="form theme-form" action="{{ route('admin.transfer.store') }}" method="post">
        @csrf
        <div class="card-body">
            <div class="row">
                @include('Admin.transfers.__form')
            </div>
        </div>

        <div class="card-footer text-end">
            <button class="btn btn-primary" type="submit">Create</button>
        </div>
    </form>
</div>
@endsection

@section('js')

<script src="{{ asset('admin/assets/js/tooltip-init.js') }}"></script>
@endsection