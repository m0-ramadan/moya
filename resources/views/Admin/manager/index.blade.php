@extends('Admin.layout.master')

@section('title', 'مدير التحويلات')

@section('css')
<!-- Plugins css start-->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/datatables.css') }}">
<!-- Plugins css Ends-->
@endsection

@section('content')
<div class="col-sm-12">
    <div class="card">
        <div class="card-header">
             <a class="btn btn-success" href="{{ route('admin.managers.create') }}">اضافة موظف</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="display" id="basic-1">
                    <thead>
                        <tr>
                            <th  class="center">#</th>
                            <th class="center">الاسم</th>
                            <th class="center">الأيميل</th>
                            <th class="center">رقم الهاتف</th>
                            <th class="center">الكود</th>
                            <th class="center">المدينة</th>
                            <th class="center">الفرع</th>
                            <th class="center">النوغ</th>

                            <th style="text-align: right;">العمليات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transfers as $key => $transfer)
                        <tr>
                            <td class="center">{{ ++$key }}</td>
                            <td class="center">{{ $transfer->name }}</td>
                            <td class="center">{{ $transfer->email }}</td>
                            <td class="center" >{{ $transfer->phone }}</td>
                            <td class="center" >{{ $transfer->code }}</td>
                            <td class="center">{{ $transfer->city_id ? $transfer->region->region_ar : '-' }}</td>
                            <td class="center">{{ $transfer->branch ? $transfer->branch->name : '-' }}</td>

                            <td class="center">
                                @if ($transfer->type == 1&&$transfer->permissions==1)
                                مدير 
                                @elseif ($transfer->type == 1&&$transfer->permissions==0)
                                محاسب
                                @else
                                Merchant
                                @endif
                            </td>
                           
                            <td class="d-flex gap-1" >
                                <a href="{{ route('admin.managers.edit', $transfer->id) }}" class="btn btn-success">
                                    <i class="fa fa-edit text-white"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.managers.destroy', $transfer->id) }}"
                                    style="display:inline;"
                                    onsubmit="return confirm('هل تريد حذف المدير');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-trash-o"></i>
                                    </button>
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