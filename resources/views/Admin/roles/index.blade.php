@extends('Admin.layout.master')

@section('title')
    الأدوار
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
    </style>
@endsection

@section('content')
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>إدارة الأدوار</h5>
                <a href="{{ route('admin.roles.create') }}" class="btn btn-success">إضافة دور</a>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <!-- Debug Permissions -->
                {{-- <div>Debug Permissions: {{ count($permissions) }} صلاحيات متاحة. IDs:
                    {{ implode(', ', $permissions->pluck('id')->toArray()) }}</div> --}}
                <div class="table-responsive">
                    <table class="display" id="basic-1">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>الاسم</th>
                                <th>الحارس</th>
                                <th>الصلاحيات</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($roles as $key => $role)
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td>{{ $role->name }}</td>
                                    <td>{{ $role->guard_name ?? 'web' }}</td>
                                    <td>
                                        @foreach ($role->permissions as $permission)
                                            <span class="badge badge-primary">{{ $permission->name }} (ID:
                                                {{ $permission->id }})</span>
                                        @endforeach
                                    </td>
                                    <td class="d-flex gap-1">
                                        <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-success btn-sm"
                                            title="تعديل">
                                            <i class="fa fa-edit text-white"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.roles.destroy', $role->id) }}"
                                            class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="حذف">
                                                <i class="fa fa-trash-o"></i>
                                            </button>
                                        </form>

                                    </td>
                                </tr>

                                <!-- Edit Role Modal -->
                                <div class="modal fade" id="editRoleModal{{ $role->id }}" tabindex="-1"
                                    aria-labelledby="editRoleModalLabel{{ $role->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editRoleModalLabel{{ $role->id }}">تعديل
                                                    الدور</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form method="POST" action="{{ route('admin.roles.update', $role->id) }}"
                                                class="role-form">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="name{{ $role->id }}" class="form-label">اسم
                                                            الدور</label>
                                                        <input type="text" class="form-control"
                                                            id="name{{ $role->id }}" name="name"
                                                            value="{{ $role->name }}" required>
                                                        @error('name')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">الصلاحيات</label>
                                                        <div class="permissions-grid">
                                                            @foreach ($permissions as $permission)
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="permissions[]"
                                                                        id="permission{{ $permission->id }}_{{ $role->id }}"
                                                                        value="{{ $permission->id }}"
                                                                        {{ $role->permissions->contains($permission->id) ? 'checked' : '' }}>
                                                                    <label class="form-check-label"
                                                                        for="permission{{ $permission->id }}_{{ $role->id }}">
                                                                        {{ $permission->name }} (ID:
                                                                        {{ $permission->id }})
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        @error('permissions')
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
                                                        <span class="btn-text">تحديث الدور</span>
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

    <!-- Create Role Modal -->
    <div class="modal fade" id="createRoleModal" tabindex="-1" aria-labelledby="createRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createRoleModalLabel">إضافة دور</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('admin.roles.store') }}" class="role-form">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">اسم الدور</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">الصلاحيات</label>
                            <div class="permissions-grid">
                                @foreach ($permissions as $permission)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]"
                                            id="permission{{ $permission->id }}" value="{{ $permission->id }}">
                                        <label class="form-check-label" for="permission{{ $permission->id }}">
                                            {{ $permission->name }} (ID: {{ $permission->id }})
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('permissions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                        <button type="submit" class="btn btn-primary btn-loading">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"
                                style="display: none;"></span>
                            <span class="btn-text">إنشاء الدور</span>
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
        // Initialize DataTables with Arabic translation
        $(document).ready(function() {
            // Destroy existing DataTable instance if it exists
            if ($.fn.DataTable.isDataTable('#basic-1')) {
                $('#basic-1').DataTable().destroy();
            }

            // Initialize DataTable
            $('#basic-1').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Arabic.json'
                }
            });
        });

        // Initialize Bootstrap modals
        var createModal = new bootstrap.Modal(document.getElementById('createRoleModal'));
        @foreach ($roles as $role)
            var editModal{{ $role->id }} = new bootstrap.Modal(document.getElementById(
                'editRoleModal{{ $role->id }}'));
        @endforeach

        // Handle form submissions with AJAX
        document.querySelectorAll('.role-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const submitButton = form.querySelector('.btn-loading');
                submitButton.disabled = true;
                submitButton.querySelector('.spinner-border').style.display = 'inline-block';
                submitButton.querySelector('.btn-text').style.display = 'none';

                // Log form data for debugging
                const formData = new FormData(form);
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

                fetch(form.action, {
                        method: form.method,
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
                                createModal.hide();
                                @foreach ($roles as $role)
                                    editModal{{ $role->id }}.hide();
                                @endforeach
                                form.reset();
                                window.location.reload();
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
