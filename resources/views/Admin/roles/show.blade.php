@extends('Admin.layout.master')

@section('title', 'تفاصيل الرتبة')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            margin-bottom: 30px;
        }

        .card-header {
            background: var(--primary-gradient);
            color: white;
            padding: 20px 25px;
            border-radius: 15px 15px 0 0 !important;
            border-bottom: none;
        }

        .info-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 20px;
            height: 100%;
        }

        .info-card h6 {
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 10px;
        }

        .info-item {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }

        .info-label {
            font-weight: bold;
            color: rgba(255, 255, 255, 0.7);
            min-width: 120px;
        }

        .permission-badge {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 4px 12px;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.8);
            display: inline-block;
            margin: 0 4px 8px 0;
        }

        .permission-badge:hover {
            background: rgba(105, 108, 255, 0.2);
            color: #fff;
        }
        
        .module-group {
            margin-bottom: 20px;
            background: rgba(0, 0, 0, 0.2);
            padding: 15px;
            border-radius: 10px;
        }
        
        .module-group h6 {
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .table {
            color: #fff;
            margin-bottom: 0;
        }

        .table th {
            border-bottom-color: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.8);
            font-weight: 600;
        }

        .table td {
            border-bottom-color: rgba(255, 255, 255, 0.05);
            vertical-align: middle;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y" bis_skin_checked="1">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"> <a href="{{ route('admin.home') }}">الرئيسية</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">الرتب</a></li>
                <li class="breadcrumb-item active">تفاصيل الرتبة</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0 text-white">معلومات الرتبة</h5>
                    </div>
                    <div class="card-body mt-3">
                        <div class="info-card">
                            <div class="info-item">
                                <span class="info-label">اسم الرتبة (EN):</span>
                                <span>{{ $role->name }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">الاسم المعروض:</span>
                                <span>{{ $role->display_name }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">الوصف:</span>
                                <span>{{ $role->description ?? 'لا يوجد' }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">عدد المستخدمين:</span>
                                <span>{{ $role->users->count() }} مستخدم</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">عدد الصلاحيات:</span>
                                <span>{{ $role->permissions->count() }} صلاحية</span>
                            </div>
                            
                            <div class="mt-4 d-grid gap-2">
                                <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-2"></i> تعديل بيانات الرتبة
                                </a>
                                <a href="{{ route('admin.roles.permissions', $role->id) }}" class="btn btn-info">
                                    <i class="fas fa-key me-2"></i> إدارة الصلاحيات
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0 text-white">الصلاحيات الممنوحة ({{ $role->permissions->count() }})</h5>
                    </div>
                    <div class="card-body mt-3">
                        @if($permissionsByModule->isEmpty())
                            <div class="text-center py-5 text-muted">
                                <i class="fas fa-shield-alt fa-3x mb-3"></i>
                                <p>لا توجد صلاحيات ممنوحة لهذه الرتبة</p>
                            </div>
                        @else
                            @foreach($permissionsByModule as $module => $permissions)
                                <div class="module-group">
                                    <h6><i class="fas fa-{{ module_icon($module) }} me-2"></i> {{ module_display_name($module) }}</h6>
                                    <div>
                                        @foreach($permissions as $permission)
                                            <span class="permission-badge">
                                                {{ $permission->display_name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-white">أحدث المستخدمين الممنوحين لهذه الرتبة</h5>
                        <a href="{{ route('admin.roles.assign.index') }}" class="btn btn-sm btn-light">إدارة التعيين</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>الاسم</th>
                                        <th>البريد الإلكتروني</th>
                                        <th>تاريخ الإنضمام</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($role->users as $user)
                                        <tr>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->created_at->format('Y-m-d') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4 text-muted">لا يوجد مستخدمين بهذه الرتبة</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
