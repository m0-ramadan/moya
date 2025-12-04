@extends('Admin.layout.master')

@section('title', 'مندوبى التوصيل')

@section('css')
    <!-- Plugins css start-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/datatables.css') }}">
    <!-- Plugins css Ends-->
@endsection

@section('content')


    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <a class="btn btn-success" href="{{ route('admin.representatives.create') }}">اضافة مندوب جديد </a>

                <a class="btn btn-success" href="{{ route('admin.representatives.sendnotifications') }}">ارسل اشعارات
                    لكل</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="display table table-bordered" id="basic-1">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>الكود</th>

                                <th>الأسم</th>
                                <th>التليفون</th>
                                <th> المدينة</th>
                                <th>النوع</th>
                                <th>رقم الهوية</th>
                                <th>المحفظة</th>
                                <th> نسبة المندوب </th>
                                <th>حالة</th>
                                <th>العمليات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($representatives as $key => $representative)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $representative->code ?? '--' }}</td>
                                    <td>{{ $representative->name }}</td>
                                    <td>{{ $representative->phone }}</td>
                                    <td>{{ $representative->region ? $representative->region->region_ar : 'N/A' }}</td>
                                    <td>{{ $representative->gender_type_name ?? 'N/A' }}</td>
                                    <td>{{ $representative->card_number ?? 'N/A' }}</td>
                                    <td>تفاصيل</td>
                                    <td>{{ $representative->commission ?? 'N/A' }}</td>


                                    <td>
                                        <a href="{{ route('admin.representatives.changeStatus', $representative->id) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            {{ $representative->status_label }}
                                        </a>
                                    </td>

                                    <td class="d-flex gap-1 right">


                                        <button class="btn btn-success"> <a
                                                href="{{ route('admin.representatives.edit', $representative->id) }}"><i
                                                    class="fa fa-edit text-white"></i> </a> </button>
                                        <button class="btn btn-success"> <a
                                                href="{{ route('admin.representatives.sendNotificationToUser', $representative->id) }}"><i
                                                    class="fa fa-bell  text-white"></i> </a> </button>

                                        <form method="post"
                                            action="{{ route('admin.representatives.destroy', $representative->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-primary"><i
                                                    class="fa fa-trash-o"></i></button>
                                        </form>

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('js')
    <!-- Plugins JS start-->
    <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatables/datatable.custom.js') }}"></script>
    <script src="{{ asset('assets/js/tooltip-init.js') }}"></script>
    <!-- Plugins JS Ends-->
@endsection
