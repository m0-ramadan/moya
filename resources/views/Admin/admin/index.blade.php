@extends('Admin.layout.master')

@section('title', 'إدارة الموظفين')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <style>
        :root {
            --primary-color: #696cff;
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --dark-bg: #1e1e2d;
            --dark-card: #2b3b4c;
        }

        body {
            font-family: "Cairo", sans-serif !important;
            background: var(--dark-bg);
            color: #fff;
        }

        .card {
            background: var(--dark-card);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .card-header {
            background: var(--primary-gradient);
            color: white;
            padding: 15px 25px;
            border-radius: 15px 15px 0 0;
            border-bottom: none;
        }

        .card-body {
            padding: 25px;
        }

        .table {
            color: #fff;
        }

        .table thead th {
            background: rgba(255, 255, 255, 0.05);
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
        }

        .table tbody td {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            vertical-align: middle;
        }

        .role-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            background: rgba(105, 108, 255, 0.2);
            color: var(--primary-color);
            margin-right: 5px;
            margin-bottom: 5px;
        }

        .btn-outline-primary {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            color: #fff;
        }

        .dataTables_wrapper .dt-buttons .btn {
            margin-right: 5px;
            padding: 5px 12px;
            font-size: 13px;
        }

        .btn-icon {
            padding: 5px 10px;
            font-size: 14px;
        }

        .modal-content {
            background: var(--dark-card);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .modal-header {
            background: var(--primary-gradient);
            border-bottom: none;
            border-radius: 15px 15px 0 0;
            padding: 20px 25px;
        }

        .modal-body {
            padding: 25px;
        }

        .modal-footer {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 20px 25px;
        }

        .form-check-label {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 15px;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .form-check-input:checked+.form-check-label {
            background: rgba(105, 108, 255, 0.15);
            border-left: 3px solid var(--primary-color);
        }

        .form-check-label:hover {
            background: rgba(105, 108, 255, 0.08);
        }

        .btn-close-white {
            filter: invert(1);
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y" dir="rtl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">الرئيسية</a></li>
                <li class="breadcrumb-item active">الموظفين</li>
            </ol>
        </nav>

        @if (!auth()->user()->role || auth()->user()->hasPermissionTo('عرض الإدمن'))
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h5 class="mb-0"><i class="fas fa-users-cog me-2"></i>قائمة الموظفين</h5>
                    <div class="d-flex gap-2 align-items-center">
                        @if (!auth()->user()->role || auth()->user()->hasPermissionTo('إضافة الإدمن'))
                            <a href="{{ route('admin.admins.create') }}" class="btn btn-light">
                                <i class="fas fa-plus-circle me-1"></i> إضافة موظف جديد
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="adminsTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th style="text-align: center;">#</th>
                                    <th style="text-align: center;">الاسم</th>
                                    <th style="text-align: center;">الإيميل</th>
                                    <th style="text-align: center;">الرتب</th>

                                    <th style="text-align: center;">العمليات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($admins as $key => $admin)
                                    <tr>
                                        <td style="text-align: center;">{{ ++$key }}</td>
                                        <td style="text-align: center;">{{ $admin->name }}</td>
                                        <td style="text-align: center;" dir="ltr">{{ $admin->email }}</td>
                                        <td style="text-align: center;">
                                            @forelse ($admin->roles as $role)
                                                <span class="role-badge">{{ $role->display_name ?? $role->name }}</span>
                                            @empty
                                                <span class="text-muted">بدون رتب</span>
                                            @endforelse
                                        </td>
          
                                        <td class="d-flex gap-1 justify-content-center">
                                            @if (!auth()->user()->role || auth()->user()->hasPermissionTo('تعديل الإدمن'))
                                                <a href="{{ route('admin.admins.edit', $admin) }}" class="btn btn-success btn-icon">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            @endif

                                            {{-- زر تعيين الرتب --}}
                                            <button class="btn btn-primary btn-icon assign-roles-btn"
                                                    data-id="{{ $admin->id }}"
                                                    data-name="{{ $admin->name }}"
                                                    data-roles="{{ $admin->roles->pluck('id') }}">
                                                <i class="fa fa-user-tag"></i>
                                            </button>

                                            @if (!auth()->user()->role || auth()->user()->hasPermissionTo('حذف الإدمن'))
                                                <button class="btn btn-danger btn-icon delete-admin-btn"
                                                        data-id="{{ $admin->id }}"
                                                        data-name="{{ $admin->name }}">
                                                    <i class="fa fa-trash-o"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i> ليس لديك صلاحية لعرض هذه الصفحة.
            </div>
        @endif
    </div>

    <!-- نافذة تعيين الرتب -->
    <div class="modal fade" id="assignRolesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user-shield me-2"></i>تعيين الرتب للموظف: <span id="adminName">...</span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="assignRolesForm">
                    @csrf
                    <input type="hidden" name="admin_id" id="adminIdInput">
                    <div class="modal-body">
                        <div class="row">
                            @foreach ($roles as $role)
                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input d-none" type="checkbox"
                                               name="roles[]" value="{{ $role->id }}"
                                               id="role_{{ $role->id }}">
                                        <label class="form-check-label w-100" for="role_{{ $role->id }}">
                                            <div class="d-flex justify-content-between align-items-center w-100">
                                                <div>
                                                    <strong>{{ $role->display_name ?? $role->name }}</strong>
                                                    @if($role->description)
                                                        <br><small class="text-muted">{{ $role->description }}</small>
                                                    @endif
                                                </div>
                                                <i class="fas fa-check-circle text-success" style="display: none;"></i>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>إلغاء
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>حفظ الرتب
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            // تهيئة DataTable مع الأزرار
            var table = $('#adminsTable').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json'
                },
                responsive: true,
                ordering: true,
                dom: '<"row"<"col-md-6"B><"col-md-6"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
                buttons: [
                    { extend: 'copyHtml5', text: '<i class="far fa-copy"></i> نسخ', className: 'btn btn-outline-secondary btn-sm', exportOptions: { columns: ':visible' } },
                    { extend: 'excelHtml5', text: '<i class="far fa-file-excel"></i> Excel', className: 'btn btn-outline-success btn-sm', exportOptions: { columns: ':visible' } },
                    { extend: 'pdfHtml5', text: '<i class="far fa-file-pdf"></i> PDF', className: 'btn btn-outline-danger btn-sm', exportOptions: { columns: ':visible' } },
                    { extend: 'print', text: '<i class="fas fa-print"></i> طباعة', className: 'btn btn-outline-dark btn-sm', exportOptions: { columns: ':visible' } }
                ]
            });

            // ---------------------- تعيين الرتب ----------------------
            $(document).on('click', '.assign-roles-btn', function() {
                var adminId = $(this).data('id');
                var adminName = $(this).data('name');
                var rolesStr = $(this).data('roles');
                // قد يكون rolesStr عبارة عن مصفوفة أرقام أو سلسلة مفصولة بفواصل
                var rolesArray = [];
                if (Array.isArray(rolesStr)) {
                    rolesArray = rolesStr;
                } else if (typeof rolesStr === 'string' && rolesStr.length > 0) {
                    rolesArray = rolesStr.split(',').map(Number);
                } else {
                    rolesArray = [];
                }

                $('#adminIdInput').val(adminId);
                $('#adminName').text(adminName);

                // إعادة ضبط جميع المربعات
                $('input[name="roles[]"]').prop('checked', false);
                $('.form-check-label i.fa-check-circle').hide();

                // تحديد الرتب الحالية
                rolesArray.forEach(function(roleId) {
                    var checkbox = $('#role_' + roleId);
                    if (checkbox.length) {
                        checkbox.prop('checked', true);
                        checkbox.siblings('label').find('i.fa-check-circle').show();
                    }
                });

                $('#assignRolesModal').modal('show');
            });

            // إظهار/إخفاء علامة الصح عند النقر على البطاقة
            $(document).on('change', 'input[name="roles[]"]', function() {
                var icon = $(this).siblings('label').find('i.fa-check-circle');
                if ($(this).is(':checked')) {
                    icon.show();
                } else {
                    icon.hide();
                }
            });

            // إرسال نموذج تعيين الرتب عبر AJAX
            $('#assignRolesForm').on('submit', function(e) {
                e.preventDefault();
                var adminId = $('#adminIdInput').val();
                var selectedRoles = $('input[name="roles[]"]:checked').map(function() { return this.value; }).get();

                Swal.fire({
                    title: 'تأكيد التعيين',
                    text: 'هل تريد حفظ الرتب الجديدة لهذا الموظف؟',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'حفظ',
                    cancelButtonText: 'إلغاء',
                    background: '#2b3b4c',
                    color: '#fff'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route("admin.roles.assign.store") }}',
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                admin_id: adminId,
                                roles: selectedRoles
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({ title: 'تم!', text: response.message, icon: 'success', background: '#2b3b4c', color: '#fff' })
                                        .then(() => location.reload());
                                } else {
                                    Swal.fire({ title: 'خطأ', text: response.message, icon: 'error', background: '#2b3b4c', color: '#fff' });
                                }
                            },
                            error: function() {
                                Swal.fire({ title: 'خطأ', text: 'فشل الاتصال بالخادم', icon: 'error', background: '#2b3b4c', color: '#fff' });
                            }
                        });
                    }
                });
            });

            // ---------------------- حذف الموظف ----------------------
            $(document).on('click', '.delete-admin-btn', function() {
                var adminId = $(this).data('id');
                var adminName = $(this).data('name');

                Swal.fire({
                    title: 'حذف الموظف',
                    text: "هل أنت متأكد من حذف " + adminName + " نهائياً؟",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'نعم، احذف',
                    cancelButtonText: 'إلغاء',
                    background: '#2b3b4c',
                    color: '#fff'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // إنشاء form مخفي وإرساله
                        var form = $('<form>', {
                            'method': 'POST',
                            'action': '{{ route("admin.admins.destroy", ":id") }}'.replace(':id', adminId),
                            'style': 'display:none;'
                        });
                        form.append($('<input>', { 'name': '_token', 'value': '{{ csrf_token() }}' }));
                        form.append($('<input>', { 'name': '_method', 'value': 'DELETE' }));
                        $('body').append(form);
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection
