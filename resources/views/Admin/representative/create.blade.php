@extends('Admin.layout.master')

@section('title', 'منديب ')
@section('css')

@endsection

@section('content')
<div class="card">
    
    <form class="form theme-form" action="{{ route('admin.representatives.store') }}" method="post">
        @csrf
        <div class="card-body">
            <div class="row">
                @include('Admin.representative.__form',['representative' => new \App\Models\Representative()])
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