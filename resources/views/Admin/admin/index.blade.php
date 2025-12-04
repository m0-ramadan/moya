@extends('Admin.layout.master')

@section('title', 'الموظفين')

@section('css')
    <!-- Plugins css start-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/datatables.css') }}">
    <!-- Plugins css Ends-->
@endsection

@section('content')
    @if (!auth()->user()->role || auth()->user()->hasPermissionTo('عرض الإدمن'))
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    @if (!auth()->user()->role || auth()->user()->hasPermissionTo('إضافة الإدمن'))
                        <a class="btn btn-success" href="{{ route('admin.admins.create') }}">إضافة موظف جديد</a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="display" id="basic-1">
                            <thead>
                                <tr>
                                    <th style="text-align: center;">#</th>
                                    <th style="text-align: center;">الاسم</th>
                                    <th style="text-align: center;">الإيميل</th>
                                    <th style="text-align: center;">الصلاحية</th>
                                    <th style="text-align: center;">الفرع</th>
                                    <th style="text-align: center;">العمليات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($admins as $key => $admin)
                                    <tr>
                                        <td style="text-align: center;">{{ ++$key }}</td>
                                        <td style="text-align: center;">{{ $admin->name }}</td>
                                        <td style="text-align: center;">{{ $admin->email }}</td>
                                        <td style="text-align: center;">
                                            @php
                                                $roleName = $admin->roles->first()->name ?? 'لم يتم تسجيل دوره';
                                            @endphp
                                            {{ $roleName }}
                                        </td>

                                        <td style="text-align: center;">
                                            {{ $admin->branch ? $admin->branch->name : 'لا يوجد فرع تابع له' }}
                                        </td>
                                        <td class="d-flex gap-1 justify-content-center">
                                            @if (!auth()->user()->role || auth()->user()->hasPermissionTo('تعديل الإدمن'))
                                                <a href="{{ route('admin.admins.edit', $admin) }}" class="btn btn-success">
                                                    <i class="fa fa-edit text-white"></i>
                                                </a>
                                            @endif
                                            @if (!auth()->user()->role || auth()->user()->hasPermissionTo('حذف الإدمن'))
                                                <form method="POST" action="{{ route('admin.admins.destroy', $admin) }}"
                                                    onsubmit="return confirm('هل أنت متأكد من حذف هذا الموظف؟')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="fa fa-trash-o"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-danger">
            ليس لديك صلاحية لعرض هذه الصفحة.
        </div>
    @endif
@endsection

@section('js')
    <!-- Plugins JS start-->
    <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatables/datatable.custom.js') }}"></script>
    <script src="{{ asset('assets/js/tooltip-init.js') }}"></script>
    <!-- Plugins JS Ends-->
@endsection
