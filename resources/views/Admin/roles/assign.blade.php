@extends('Admin.layout.master')

@section('title', 'تعيين الرتب')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
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

        .select2-container--default .select2-selection--multiple {
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 4px;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: var(--primary-color);
            border: none;
            color: #fff;
            border-radius: 15px;
            padding: 4px 12px;
            margin-top: 4px;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: #fff;
            margin-right: 8px;
            border: none;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
            color: #ff4d4d;
            background: transparent;
        }
        
        .select2-dropdown {
            background-color: var(--dark-card);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: rgba(105, 108, 255, 0.2);
        }
        
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: var(--primary-color);
        }

        .btn-primary {
            background: var(--primary-gradient);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4a9a 100%);
            box-shadow: 0 5px 15px rgba(105, 108, 255, 0.4);
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y" bis_skin_checked="1">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                     <a href="{{ route('admin.home') }}">الرئيسية</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.roles.index') }}">الرتب</a>
                </li>
                <li class="breadcrumb-item active">تعيين الرتب</li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0 text-white">إدارة تعيين الرتب</h5>
                    <small class="opacity-75 text-white">تعيين رتب للمدراء والمشرفين في النظام</small>
                </div>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>اسم المدير</th>
                                <th>البريد الإلكتروني</th>
                                <th style="width: 40%">الرتب الحالية</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($admins as $admin)
                                <tr>
                                    <td>{{ $admin->name }}</td>
                                    <td>{{ $admin->email }}</td>
                                    <td>
                                        <form action="{{ route('admin.roles.assign.store') }}" method="POST" class="d-flex gap-2">
                                            @csrf
                                            <input type="hidden" name="admin_id" value="{{ $admin->id }}">
                                            <select name="roles[]" class="form-control select2" multiple="multiple">
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->id }}" 
                                                        {{ $admin->hasRole($role->name) ? 'selected' : '' }}>
                                                        {{ $role->display_name }} ({{ $role->name }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="btn btn-sm btn-primary align-self-start" style="height: 38px;">
                                                <i class="fas fa-save"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        @foreach ($admin->roles as $role)
                                            <span class="badge bg-primary mb-1">{{ $role->display_name }}</span>
                                        @endforeach
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">لا يوجد مدراء حالياً</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            @if($admins->hasPages())
                <div class="card-footer border-top border-secondary">
                    {{ $admins->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: 'اختر الرتب...',
                allowClear: true,
                width: '100%',
                dir: "rtl"
            });
            
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'تم بنجاح',
                    text: '{{ session('success') }}',
                    timer: 2000,
                    showConfirmButton: false
                });
            @endif
            
            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: '{{ session('error') }}'
                });
            @endif
        });
    </script>
@endsection
