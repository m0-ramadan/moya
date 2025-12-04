@extends('Admin.layout.master')

@section('title', 'المستخدمين')

@section('css')
    <!-- Plugins css start-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/datatables.css') }}">
    <!-- Plugins css Ends-->
@endsection

@section('content')
    @if (!auth()->user()->role || auth()->user()->hasPermissionTo('عرض الزبائن'))
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>المستخدمين</h5>
                    @if (!auth()->user()->role || auth()->user()->hasPermissionTo('إرسال إشعار للعميل'))
                        <a class="btn btn-success" href="{{ route('admin.client.sendnotifications', ['t' => 1]) }}">إرسال
                            إشعارات للكل</a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="display" id="basic-1">
                            <thead>
                                <tr>
                                    <th style="text-align: center;">#</th>
                                    <th style="text-align: center;">الأسم</th>
                                    <th style="text-align: center;">الأيميل</th>
                                    <th style="text-align: center;">التليفون</th>
                                    <th style="text-align: center;">الحالة</th>
                                    <th style="text-align: center;">العمليات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($clients as $key => $client)
                                    <tr>
                                        <td style="text-align: center;">{{ ++$key }}</td>
                                        <td style="text-align: center;">{{ $client->name }}</td>
                                        <td style="text-align: center;">{{ $client->email ?? 'غير محدد' }}</td>
                                        <td style="text-align: center;">{{ $client->phone }}</td>
                                        <td style="text-align: center;">
                                            @if (!auth()->user()->role || auth()->user()->hasPermissionTo('تغيير حالة العملاء'))
                                                <a href="{{ route('admin.client.changeStatus', $client->id) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    {{ $client->status_label }}
                                                </a>
                                            @else
                                                {{ $client->status_label }}
                                            @endif
                                        </td>
                                        <td class="d-flex gap-1 justify-content-center">
                                            @if (!auth()->user()->role || auth()->user()->hasPermissionTo('تعديل العملاء'))
                                                <a href="{{ route('admin.client.edit', $client->id) }}"
                                                    class="btn btn-success">
                                                    <i class="fa fa-edit text-white"></i>
                                                </a>
                                            @endif
                                            @if (!auth()->user()->role || auth()->user()->hasPermissionTo('إرسال إشعار للعميل'))
                                                <a href="{{ route('admin.client.sendNotificationToUser', $client->id) }}"
                                                    class="btn btn-success">
                                                    <i class="fa fa-bell text-white"></i>
                                                </a>
                                            @endif
                                            @if (!auth()->user()->role || auth()->user()->hasPermissionTo('حذف العملاء'))
                                                <form method="POST"
                                                    action="{{ route('admin.client.destroy', $client->id) }}"
                                                    style="display:inline;"
                                                    onsubmit="return confirm('هل أنت متأكد من حذف هذا العميل؟');">
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
