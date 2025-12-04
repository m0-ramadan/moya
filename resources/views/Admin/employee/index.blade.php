@extends('Admin.layout.master')

@section('title', 'موظفي التحويلات')

@section('css')
    <!-- Plugins css start-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/datatables.css') }}">
    <!-- Plugins css Ends-->
@endsection

@section('content')
    @if (!auth()->user()->role || auth()->user()->hasPermissionTo('عرض موظفي التحويلات'))
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    @if (!auth()->user()->role || auth()->user()->hasPermissionTo('إضافة موظف تحويلات'))
                        <a class="btn btn-success" href="{{ route('admin.employees.create') }}">إضافة موظف</a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="display" id="basic-1">
                            <thead>
                                <tr>
                                    <th style="text-align: center;">#</th>
                                    <th style="text-align: center;">الأسم</th>
                                    <th style="text-align: center;">البريد الإلكتروني</th>
                                    <th style="text-align: center;">التليفون</th>
                                    <th style="text-align: center;">الكود</th>
                                    <th style="text-align: center;">النوع</th>
                                    <th style="text-align: center;">الفرع</th>
                                    <th style="text-align: center;">التحويلات</th>
                                    <th style="text-align: center;">العمليات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transfers as $key => $transfer)
                                    <tr>
                                        <td style="text-align: center;">{{ ++$key }}</td>
                                        <td style="text-align: center;">{{ $transfer->name }}</td>
                                        <td style="text-align: center;">{{ $transfer->email ?? 'غير محدد' }}</td>
                                        <td style="text-align: center;">{{ $transfer->phone ?? 'غير محدد' }}</td>
                                        <td style="text-align: center;">{{ $transfer->code }}</td>
                                        <td style="text-align: center;">{{ $transfer->type_name ?? $transfer->type }}</td>
                                        <td style="text-align: center;">
                                            {{ $transfer->branch ? $transfer->branch->name : 'غير محدد' }}</td>
                                        <td style="text-align: center;">{{ $transfer->transfer_moneys_count }}</td>
                                        <td class="d-flex gap-1 justify-content-center">
                                            @if (!auth()->user()->role || auth()->user()->hasPermissionTo('تعديل موظفي التحويلات'))
                                                <a href="{{ route('admin.employees.edit', $transfer->id) }}"
                                                    class="btn btn-success" title="تعديل">
                                                    <i class="fa fa-edit text-white"></i>
                                                </a>
                                            @endif
                                            @if (!auth()->user()->role || auth()->user()->hasPermissionTo('حذف موظفي التحويلات'))
                                                <form method="POST"
                                                    action="{{ route('admin.employees.destroy', $transfer->id) }}"
                                                    style="display:inline;"
                                                    onsubmit="return confirm('هل أنت متأكد أنك تريد حذف هذا الموظف؟');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger" title="حذف">
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
