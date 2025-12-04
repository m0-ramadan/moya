@extends('Admin.layout.master')

@section('title', 'مديرين الحولات')
@section('css')

@endsection

@section('content')
<div class="card">
    
    <form class="form theme-form" action="{{ route('admin.managers.store') }}" method="post"  onsubmit="return validatePasswords()">
        @csrf
        <div class="card-body">
            <div class="row">
                @include('Admin.manager.__form')
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

<script>
function validatePasswords() {
    var password = document.getElementById("password").value;
    var confirm = document.getElementById("confirm_password").value;
    var error = document.getElementById("error_message");

    if (password !== confirm) {
        error.textContent = "كلمتا المرور غير متطابقتين!";
        return false; // يمنع إرسال النموذج
    }

    error.textContent = ""; // يمسح الخطأ لو كانت متطابقة
    return true;
}
</script>
@endsection