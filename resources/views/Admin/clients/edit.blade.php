@extends('Admin.layout.master')

@section('title', 'Admins ')
@section('css')

@endsection

@section('content')
<div class="card">
    <div class="card-header pb-0">
        <h5>Update employee</h5>
    </div>
    <form class="form theme-form" action="{{ route('admin.employees.update', $transfer) }}" method="post">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="row">
                @include('Admin.employee.__form')
            </div>
        </div>

        <div class="card-footer text-end">
            <button class="btn btn-primary" type="submit">Update</button>
        </div>
    </form>
</div>
@endsection
@section('js')
<script src="{{ asset('admin/assets/js/tooltip-init.js') }}"></script>
@endsection