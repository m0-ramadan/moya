@extends('Admin.layout.master')
@section('title')
    الفروع
@endsection
@section('css')
    <!-- Plugins css start-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/datatables.css') }}">
    <!-- Plugins css Ends-->
@endsection

@section('content')
    @if (!auth()->user()->role || auth()->user()->hasPermissionTo('عرض الفروع'))
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    @if (request()->routeIs('admin.branch.index') && (!auth()->user()->role || auth()->user()->hasPermissionTo('إضافة فرع')))
                        <a class="btn btn-success" href="{{ route('admin.branch.create') }}">إضافة فرع</a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="display" id="example">
                            <thead>
                                <tr>
                                    <th style="text-align: center;">#</th>
                                    <th style="text-align: center;">الأسم</th>
                                    <th style="text-align: center;">رقم الهاتف</th>
                                    <th style="text-align: center;">العنوان</th>
                                    <th style="text-align: center;">عنوان على الخريطة</th>
                                    <th style="text-align: center;">الكود</th>
                                    <th style="text-align: center;">المدينة</th>
                                    <th style="text-align: center;">العمليات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($branchs as $key => $branch)
                                    <tr>
                                        <td style="text-align: center;">{{ ++$key }}</td>
                                        <td style="text-align: center;">{{ $branch->name }}</td>
                                        <td style="text-align: center;">
                                            {{ $branch->phone1 }}{{ $branch->phone2 ? ' - ' . $branch->phone2 : '' }}
                                        </td>
                                        <td style="text-align: center;">{{ $branch->address }}</td>
                                        <td style="text-align: center;">
                                            @if ($branch->link_address)
                                                <a href="{{ $branch->link_address }}" target="_blank">الموقع</a>
                                            @else
                                                --
                                            @endif
                                        </td>
                                        <td style="text-align: center;">{{ $branch->key }}</td>
                                        <td style="text-align: center;">{{ $branch->region->region_ar ?? 'غير محدد' }}</td>
                                        <td class="d-flex gap-1 justify-content-center">
                                            @if (request()->routeIs('admin.branch.index'))
                                                @if (!auth()->user()->role || auth()->user()->hasPermissionTo('تعديل الفروع'))
                                                    <a href="{{ route('admin.branch.edit', $branch->id) }}"
                                                        class="btn btn-success" title="تعديل">
                                                        <i class="fa fa-edit text-white"></i>
                                                    </a>
                                                @endif
                                                @if (!auth()->user()->role || auth()->user()->hasPermissionTo('حذف الفروع'))
                                                    <form method="POST"
                                                        action="{{ route('admin.branch.destroy', $branch->id) }}"
                                                        onsubmit="return confirm('هل أنت متأكد من حذف هذا الفرع؟');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger" title="حذف">
                                                            <i class="fa fa-trash-o"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                            @if (!auth()->user()->role || auth()->user()->hasPermissionTo('عرض المخازن الصادرة'))
                                                <a href="{{ route('admin.branch.outgoingWarehouses', $branch->id) }}"
                                                    class="btn btn-warning" title="المخازن الصادرة">
                                                    <i class="fa fa-arrow-up text-white"></i>
                                                </a>
                                            @endif
                                            @if (!auth()->user()->role || auth()->user()->hasPermissionTo('عرض المخازن الواردة'))
                                                <a href="{{ route('admin.branch.incomingWarehouses', $branch->id) }}"
                                                    class="btn btn-info" title="المخازن الواردة">
                                                    <i class="fa fa-arrow-down text-white"></i>
                                                </a>
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
    <script>
        $(document).ready(function() {
            var table = $('#example').DataTable({
                pagingType: 'full_numbers',
                dom: 'lBfrtip',
                buttons: [{
                        extend: 'copyHtml5',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        }
                    },
                    {
                        extend: 'print',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        }
                    },
                    'colvis'
                ],
            });
        });
    </script>
    <!-- Plugins JS Ends-->
@endsection
