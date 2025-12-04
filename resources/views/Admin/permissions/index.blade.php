@extends('Admin.layout.master')

@section('title')
    الصلاحيات
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
    </style>
@endsection

@section('content')
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>إدارة الصلاحيات</h5>
                {{-- <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createPermissionModal">إضافة
                    صلاحية</button> --}}
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <div class="table-responsive">
                    <table class="display" id="basic-1">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>الاسم</th>
                                <th>الحارس</th>
                                {{-- <th>الإجراءات</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($permissions as $key => $permission)
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td>{{ $permission->name }}</td>
                                    <td>{{ $permission->guard_name ?? 'web' }}</td>
                                    {{-- <td class="d-flex gap-1">
                                        <button class="btn btn-success btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#editPermissionModal{{ $permission->id }}" title="تعديل">
                                            <i class="fa fa-edit text-white"></i>
                                        </button>
                                        <form method="POST"
                                            action="{{ route('admin.permissions.destroy', $permission->id) }}"
                                            class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="حذف">
                                                <i class="fa fa-trash-o"></i>
                                            </button>
                                        </form>
                                        <a class="btn btn-info btn-sm"
                                            href="{{ route('admin.permissions.show', $permission->id) }}" title="عرض">
                                            <i class="fa fa-eye text-white"></i>
                                        </a>
                                    </td>
                                </tr> --}}

                                <!-- Edit Permission Modal -->
                                <div class="modal fade" id="editPermissionModal{{ $permission->id }}" tabindex="-1"
                                    aria-labelledby="editPermissionModalLabel{{ $permission->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editPermissionModalLabel{{ $permission->id }}">
                                                    تعديل الصلاحية</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form method="POST"
                                                action="{{ route('admin.permissions.update', $permission->id) }}"
                                                class="permission-form">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="name{{ $permission->id }}" class="form-label">اسم
                                                            الصلاحية</label>
                                                        <input type="text" class="form-control"
                                                            id="name{{ $permission->id }}" name="name"
                                                            value="{{ $permission->name }}" required>
                                                        @error('name')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">إغلاق</button>
                                                    <button type="submit" class="btn btn-primary btn-loading">
                                                        <span class="spinner-border spinner-border-sm" role="status"
                                                            aria-hidden="true" style="display: none;"></span>
                                                        <span class="btn-text">تحديث الصلاحية</span>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Permission Modal -->
    <div class="modal fade" id="createPermissionModal" tabindex="-1" aria-labelledby="createPermissionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createPermissionModalLabel">إضافة صلاحية</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('admin.permissions.store') }}" class="permission-form">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">اسم الصلاحية</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                        <button type="submit" class="btn btn-primary btn-loading">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"
                                style="display: none;"></span>
                            <span class="btn-text">إنشاء الصلاحية</span>
                        </button>
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
        // Initialize Bootstrap modals
        var createModal = new bootstrap.Modal(document.getElementById('createPermissionModal'));
        @foreach ($permissions as $permission)
            var editModal{{ $permission->id }} = new bootstrap.Modal(document.getElementById(
                'editPermissionModal{{ $permission->id }}'));
        @endforeach

        // Handle form submissions with AJAX
        document.querySelectorAll('.permission-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const submitButton = form.querySelector('.btn-loading');
                submitButton.disabled = true;
                submitButton.querySelector('.spinner-border').style.display = 'inline-block';
                submitButton.querySelector('.btn-text').style.display = 'none';

                const formData = new FormData(form);
                fetch(form.action, {
                        method: form.method,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        submitButton.disabled = false;
                        submitButton.querySelector('.spinner-border').style.display = 'none';
                        submitButton.querySelector('.btn-text').style.display = 'inline-block';

                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'نجاح',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                createModal.hide();
                                @foreach ($permissions as $permission)
                                    editModal{{ $permission->id }}.hide();
                                @endforeach
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ',
                                text: data.message || 'حدث خطأ ما!',
                            });
                        }
                    })
                    .catch(error => {
                        submitButton.disabled = false;
                        submitButton.querySelector('.spinner-border').style.display = 'none';
                        submitButton.querySelector('.btn-text').style.display = 'inline-block';
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: 'حدث خطأ أثناء معالجة طلبك.',
                        });
                    });
            });
        });

        // Handle delete confirmation with SweetAlert
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: 'لن تتمكن من التراجع عن هذا!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'نعم، احذفه!',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection
