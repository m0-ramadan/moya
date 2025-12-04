@extends('Admin.layout.master')

@section('title')
    تعديل الدور
@endsection

@section('css')
    <!-- Plugins css start-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/datatables.css') }}">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Plugins css Ends-->
    <style>
        body {
            direction: rtl;
            text-align: right;
        }

        .modal-header,
        .modal-footer {
            background-color: #f8f9fa;
        }

        .modal-title {
            font-weight: bold;
        }

        .form-control.is-invalid {
            border-color: #dc3545;
        }

        .invalid-feedback {
            display: none;
        }

        .is-invalid~.invalid-feedback {
            display: block;
        }

        .btn-loading .spinner-border {
            display: inline-block;
        }

        .btn-loading:not(.disabled) .btn-text {
            display: none;
        }

        .table th,
        .table td {
            text-align: right;
        }

        .d-flex.gap-1 {
            justify-content: flex-end;
        }

        .permissions-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .permissions-grid .form-check {
            flex: 0 0 25%;
            padding-right: 20px;
        }

        .permission-group {
            width: 100%;
            margin-bottom: 20px;
        }

        .permission-group h6 {
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
    </style>
@endsection

@section('content')
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>تعديل الدور: {{ $role->name }}</h5>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <form method="POST" action="{{ route('admin.roles.update', $role->id) }}" class="role-form">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="name" class="form-label">اسم الدور</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ $role->name }}"
                            required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الصلاحيات</label>
                        <div class="permissions-grid">
                            @php
                                $groupedPermissions = $permissions->groupBy('group');
                            @endphp
                            @foreach ($groupedPermissions as $group => $groupPermissions)
                                @if ($group)
                                    <!-- Only show groups with a non-null name -->
                                    <div class="permission-group">
                                        <h6>{{ $group }}</h6>
                                        <div class="permissions-grid">
                                            @foreach ($groupPermissions as $permission)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="permissions[]"
                                                        id="permission{{ $permission->id }}" value="{{ $permission->name }}"
                                                        {{ $role->permissions->contains($permission->id) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="permission{{ $permission->id }}">
                                                        {{ $permission->name }} (ID: {{ $permission->id }})
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        @error('permissions')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="d-flex gap-1">
                        <button type="submit" class="btn btn-primary btn-loading">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"
                                style="display: none;"></span>
                            <span class="btn-text">تحديث الدور</span>
                        </button>
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">رجوع</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <!-- Plugins JS start-->
    <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatables/datatable.custom.js') }}"></script>
    <script src="{{ asset('assets/js/tooltip-init.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Plugins JS Ends-->
    <script>
        // Handle form submission with AJAX
        document.querySelector('.role-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const submitButton = this.querySelector('.btn-loading');
            submitButton.disabled = true;
            submitButton.querySelector('.spinner-border').style.display = 'inline-block';
            submitButton.querySelector('.btn-text').style.display = 'none';

            // Log form data for debugging
            const formData = new FormData(this);
            const formDataObj = Object.fromEntries(formData.entries());
            console.log('Form Data:', formDataObj);
            console.log('Permissions Array:', formData.getAll('permissions[]'));

            // Client-side validation
            if (!formData.get('name').trim()) {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: 'اسم الدور مطلوب.',
                });
                submitButton.disabled = false;
                submitButton.querySelector('.spinner-border').style.display = 'none';
                submitButton.querySelector('.btn-text').style.display = 'inline-block';
                return;
            }

            fetch(this.action, {
                    method: this.method,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => {
                    console.log('Response Status:', response.status);
                    console.log('Response Headers:', [...response.headers.entries()]);
                    return response.json();
                })
                .then(data => {
                    submitButton.disabled = false;
                    submitButton.querySelector('.spinner-border').style.display = 'none';
                    submitButton.querySelector('.btn-text').style.display = 'inline-block';

                    if (data.success) {
                        console.log('Success Response:', data);
                        Swal.fire({
                            icon: 'success',
                            title: 'نجاح',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = '{{ route('admin.roles.index') }}';
                        });
                    } else {
                        console.error('Error Response:', data);
                        let errorMessage = data.message || 'حدث خطأ ما!';
                        if (data.errors) {
                            errorMessage = Object.values(data.errors).flat().join('<br>');
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            html: errorMessage,
                        });
                    }
                })
                .catch(error => {
                    submitButton.disabled = false;
                    submitButton.querySelector('.spinner-border').style.display = 'none';
                    submitButton.querySelector('.btn-text').style.display = 'inline-block';
                    console.error('Fetch Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        html: 'حدث خطأ أثناء معالجة طلبك: ' + error.message,
                    });
                });
        });
    </script>
@endsection
