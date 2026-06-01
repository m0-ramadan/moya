@extends('Admin.layout.master')

@section('title')
    Countries
@endsection

@section('css')
    <!-- Plugins css start-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/datatables.css') }}">
    <!-- Plugins css Ends-->
@endsection

@section('content')
    @php($adminUser = auth('admin')->user())
    @if (admin_can_access_module('countries', 'view', $adminUser))
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    @if (admin_can_access_module('countries', 'create', $adminUser))
                        <a class="btn btn-success" href="{{ route('admin.country.create') }}">إضافة دولة</a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="display" id="basic-1">
                            <thead>
                                <tr>
                                    <th style="text-align: center;">#</th>
                                    <th style="text-align: center;">الدولة</th>
                                    <th style="text-align: center;">رسالة الشحن</th>
                                    <th style="text-align: center;">عمولة التجميع</th>
                                    <th style="text-align: center;">العمليات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($countries as $key => $country)
                                    <tr>
                                        <td style="text-align: center;">{{ ++$key }}</td>
                                        <td style="text-align: center;">{{ $country->country_ar }}</td>
                                        <td style="text-align: center;">{{ $country->message ?? 'غير محدد' }}</td>
                                        <td style="text-align: center;">{{ $country->collection_commission ?? '0' }}</td>
                                        <td style="text-align: center;" class="d-flex gap-1 justify-content-center">
                                            @if (admin_can_access_module('countries', 'edit', $adminUser))
                                                <a href="{{ route('admin.country.edit', $country->id) }}"
                                                    class="btn btn-success">
                                                    <i class="fa fa-edit text-white"></i>
                                                </a>
                                            @endif
                                            @if (admin_can_access_module('countries', 'delete', $adminUser))
                                                <form method="POST"
                                                    action="{{ route('admin.country.destroy', $country->id) }}"
                                                    onsubmit="return confirm('هل أنت متأكد من حذف هذه الدولة؟')">
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
