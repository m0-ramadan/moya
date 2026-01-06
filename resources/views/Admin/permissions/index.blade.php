@extends('Admin.layout.master')

@section('title', 'إدارة الصلاحيات')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #696cff;
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --dark-bg: #1e1e2d;
            --dark-card: #2b3b4c;
        }

        body {
            font-family: "Cairo", sans-serif !important;
            background: var(--dark-bg);
            color: #fff;
        }

        .permission-card {
            background: var(--dark-card);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .permission-header {
            background: var(--primary-gradient);
            color: white;
            padding: 25px 30px;
            border-radius: 15px 15px 0 0;
            margin-bottom: 25px;
        }

        .module-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .module-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4);
            background: rgba(105, 108, 255, 0.05);
        }

        .module-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .module-title {
            font-size: 18px;
            font-weight: 700;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .module-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            background: var(--primary-gradient);
            color: white;
        }

        .permission-item {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
        }

        .permission-item:hover {
            background: rgba(105, 108, 255, 0.1);
            border-color: var(--primary-color);
        }

        .permission-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .permission-name {
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
        }

        .permission-description {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.6);
            margin-top: 5px;
        }

        .permission-actions {
            display: flex;
            gap: 10px;
        }

        .permission-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .badge-create {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }

        .badge-read {
            background: rgba(12, 99, 228, 0.2);
            color: #0c63e4;
            border: 1px solid rgba(12, 99, 228, 0.3);
        }

        .badge-update {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }

        .badge-delete {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
            border: 1px solid rgba(220, 53, 69, 0.3);
        }

        .badge-manage {
            background: var(--primary-gradient);
            color: white;
        }

        .module-stats {
            display: flex;
            gap: 15px;
            margin-top: 10px;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.7);
        }

        .search-box {
            position: relative;
        }

        .search-box input {
            padding-right: 40px;
            border-radius: 25px;
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .search-box input:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--primary-color);
            color: #fff;
            box-shadow: 0 0 0 0.25rem rgba(105, 108, 255, 0.25);
        }

        .search-box .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.5);
        }

        .empty-state {
            text-align: center;
            padding: 50px 20px;
        }

        .empty-state-icon {
            font-size: 60px;
            color: rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }

        .empty-state-text {
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 20px;
        }

        .btn-primary {
            background: var(--primary-gradient);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4a9a 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(105, 108, 255, 0.4);
        }

        @media (max-width: 768px) {
            .permission-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .permission-actions {
                width: 100%;
                justify-content: flex-start;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y" bis_skin_checked="1">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.index') }}">الرئيسية</a>
                </li>
                <li class="breadcrumb-item active">الصلاحيات</li>
            </ol>
        </nav>

        <!-- البحث والإحصاءات -->
        <div class="row mb-4" bis_skin_checked="1">
            <div class="col-12" bis_skin_checked="1">
                <div class="permission-card" bis_skin_checked="1">
                    <div class="permission-header" bis_skin_checked="1">
                        <div class="d-flex justify-content-between align-items-center" bis_skin_checked="1">
                            <div bis_skin_checked="1">
                                <h5 class="mb-1">إدارة الصلاحيات</h5>
                                <small class="opacity-75">عرض وتعديل جميع الصلاحيات في النظام</small>
                            </div>
                            <div class="d-flex gap-3 align-items-center" bis_skin_checked="1">
                                <div class="search-box" bis_skin_checked="1">
                                    <i class="fas fa-search search-icon"></i>
                                    <input type="text" class="form-control" placeholder="بحث عن صلاحية..."
                                        id="searchInput" value="{{ request('search') }}">
                                </div>
                                <a href="{{ route('admin.permissions.create') }}" class="btn btn-light">
                                    <i class="fas fa-plus me-2"></i>إضافة صلاحية جديدة
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body" bis_skin_checked="1">
                        @if ($permissionsByModule->isEmpty())
                            <div class="empty-state" bis_skin_checked="1">
                                <div class="empty-state-icon" bis_skin_checked="1">
                                    <i class="fas fa-key"></i>
                                </div>
                                <h5 class="empty-state-text">لا توجد صلاحيات</h5>
                                <p class="text-muted">لم يتم إنشاء أي صلاحيات حتى الآن</p>
                                <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>إضافة صلاحية أولى
                                </a>
                            </div>
                        @else
                            @foreach ($permissionsByModule as $module => $permissions)
                                <div class="module-card" bis_skin_checked="1">
                                    <div class="module-header" bis_skin_checked="1">
                                        <div class="module-title" bis_skin_checked="1">
                                            <div class="module-icon" bis_skin_checked="1">
                                                <i class="fas fa-{{ module_icon($module) }}"></i>
                                            </div>
                                            <span>{{ module_display_name($module) }}</span>
                                        </div>
                                        <div class="module-stats" bis_skin_checked="1">
                                            <span class="stat-item">
                                                <i class="fas fa-key"></i>
                                                {{ $permissions->count() }} صلاحية
                                            </span>
                                            <span class="stat-item">
                                                <i class="fas fa-users"></i>
                                                {{ $permissions->sum('roles_count') }} رتبة
                                            </span>
                                        </div>
                                    </div>

                                    <div class="row" bis_skin_checked="1">
                                        @foreach ($permissions as $permission)
                                            @php
                                                // استخدم الـ helper functions الصحيحة
                                                $permissionType = permission_type($permission->name);
                                                $permissionTypeLabel = permission_type_label($permission->name);
                                                $badgeClass = permission_badge_class($permission->name);
                                            @endphp
                                            <div class="col-lg-6 mb-3" bis_skin_checked="1">
                                                <div class="permission-item" bis_skin_checked="1">
                                                    <div class="permission-info" bis_skin_checked="1">
                                                        <div bis_skin_checked="1">
                                                            <div class="permission-name" bis_skin_checked="1">
                                                                {{ $permission->display_name }}
                                                                <span class="permission-badge {{ $badgeClass }}">
                                                                    {{ $permissionTypeLabel }}
                                                                </span>
                                                            </div>
                                                            <div class="permission-description" bis_skin_checked="1">
                                                                {{ $permission->description ?? 'لا يوجد وصف' }}
                                                            </div>
                                                            <small class="text-muted">
                                                                <i class="fas fa-hashtag me-1"></i>
                                                                {{ $permission->name }}
                                                            </small>
                                                        </div>
                                                        <div class="permission-actions" bis_skin_checked="1">
                                                            <a href="{{ route('admin.permissions.edit', $permission) }}"
                                                                class="btn btn-sm btn-warning">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <button type="button"
                                                                class="btn btn-sm btn-danger delete-permission"
                                                                data-id="{{ $permission->id }}"
                                                                data-name="{{ $permission->display_name }}">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // البحث مع تأخير
            let searchTimeout;
            $('#searchInput').on('keyup', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    const searchValue = $(this).val();
                    const url = new URL(window.location.href);
                    if (searchValue) {
                        url.searchParams.set('search', searchValue);
                    } else {
                        url.searchParams.delete('search');
                    }
                    window.location.href = url.toString();
                }, 500);
            });

            // حذف الصلاحية
            $('.delete-permission').on('click', function() {
                const permissionId = $(this).data('id');
                const permissionName = $(this).data('name');

                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: `سيتم حذف الصلاحية "${permissionName}" نهائياً`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'نعم، احذف',
                    cancelButtonText: 'إلغاء',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin.permissions.destroy', '') }}/" +
                                permissionId,
                            type: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                _method: 'DELETE'
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'تم الحذف',
                                    text: response.success,
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'خطأ',
                                    text: xhr.responseJSON?.message ||
                                        'حدث خطأ أثناء الحذف',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            }
                        });
                    }
                });
            });

            // رسائل التنبيه من الجلسة
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'نجاح',
                    text: "{{ session('success') }}",
                    timer: 2000,
                    showConfirmButton: false
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: "{{ session('error') }}",
                    timer: 2000,
                    showConfirmButton: false
                });
            @endif
        });
    </script>
@endsection
