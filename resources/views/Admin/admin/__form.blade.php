@php
    use App\Models\Admin;
@endphp

@if (session('success'))
    <div class="col-md-12">
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    </div>
@endif

<div class="col-md-4">
    <label class="mr-sm-2" for="name" style="font-family: 'Cairo', sans-serif;">الاسم</label>
    <input class="form-control @error('name') is-invalid @enderror" name="name" id="name" type="text"
        placeholder="الاسم" value="{{ old('name', isset($admin) ? $admin->name : '') }}">
    @error('name')
        <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>

<div class="col-md-4">
    <label class="mr-sm-2" for="email" style="font-family: 'Cairo', sans-serif;">الإيميل</label>
    <input class="form-control @error('email') is-invalid @enderror" name="email" id="email" type="email"
        placeholder="الإيميل" value="{{ old('email', isset($admin) ? $admin->email : '') }}">
    @error('email')
        <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>

<div class="col-md-4">
    <label class="mr-sm-2" for="password" style="font-family: 'Cairo', sans-serif;">كلمة السر</label>
    <input class="form-control @error('password') is-invalid @enderror" name="password" id="password" type="password"
        placeholder="كلمة السر" value="{{ old('password') }}">
    @error('password')
        <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>

<div class="col-md-4 mt-3">
    <label class="mr-sm-2" for="password_confirmation" style="font-family: 'Cairo', sans-serif;">تأكيد كلمة السر</label>
    <input class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation"
        id="password_confirmation" type="password" placeholder="تأكيد كلمة السر"
        value="{{ old('password_confirmation') }}">
    @error('password_confirmation')
        <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>

<div class="col-md-4 mt-3">
    <label class="mr-sm-2" for="role" style="font-family: 'Cairo', sans-serif;">الدول</label>
    <div class="input-group mb-3">
        <select class="form-select @error('role') is-invalid @enderror" aria-label="Role" name="role" id="role">
            <option value="" selected>-- اختر صلاحية الموظف --</option>
            @foreach ($roles as $role)
                <option value="{{ $role->name }}" @if (old('role', isset($admin) ? $admin->role : '') == $role->id) selected @endif>
                    {{ $role->name }}
                </option>
            @endforeach

        </select>
    </div>
    @error('role')
        <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>

<div class="col-md-4 mt-3">
    <label class="mr-sm-2" for="branch_id" style="font-family: 'Cairo', sans-serif;">الفرع</label>
    <div class="input-group mb-3">
        <select class="form-select @error('branch_id') is-invalid @enderror" aria-label="Branch" name="branch_id"
            id="branch_id">
            <option value="" selected>-- اختر الفرع --</option>
            @foreach ($branches as $branch)
                <option value="{{ $branch->id }}" @if (old('branch_id', isset($admin) ? $admin->branch_id : '') == $branch->id) selected @endif>
                    {{ $branch->name }}</option>
            @endforeach
        </select>
    </div>
    @error('branch_id')
        <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>
