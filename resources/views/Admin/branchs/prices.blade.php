@extends('Admin.layout.master')
@section('title', 'أسعار شحن الفروع')

@section('css')
<!-- Plugins css start-->
<!-- Plugins css Ends-->
@endsection

@section('content')
<div class="col-sm-12">
    <div class="card">
        <div class="card-header">
            
             <x-breadcrumb :items="[
                        'لوحة التحكم' => '/admin',
                        'اسعار الفروع' => '',
                    ]" />
                    
            <a class="btn btn-success" href="{{ route('admin.branch.createpirce') }}">إضافة سعر جديد</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="display" id="example">
                    <thead>
                        <tr>
                            <th>#</th>
                             <th style="text-align: center;">من فرع</th>
                             <th style="text-align: center;">إلى مدينة</th>
                            <th style="text-align: center;">تكلفة النقل</th>
                            <th style="text-align: center;">العملة</th>
                            <th style="text-align: right;">العمليات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($prices as $key => $price)
                        <tr>
                            <td style="text-align: center;">{{ ++$key }}</td>
                             <td style="text-align: center;">{{ optional($price->branchOne)->name ?? 'غير متوفر' }}  </td>
                            <td style="text-align: center;">{{ optional($price->city)->region_ar ?? 'غير متوفر' }}</td>
                            <td style="text-align: center;">{{ $price->price }}</td>
                            <td style="text-align: center;">{{ $price->currency }}</td>
                            <td class="d-flex gap-1">
                                <a href="{{ route('admin.branch.editprice', $price->id) }}" class="btn btn-success">
                                    <i class="fa fa-edit text-white"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.branch.destroyprice', $price->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-trash-o"></i></button>
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
<script>
    $(document).ready(function() {
        var table = $('#example').DataTable({
            pagingType: 'full_numbers',
            dom: 'lBfrtip',
            buttons: [
                {
                    extend: 'copyHtml5',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    }
                },
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    }
                },
                {
                    extend: 'print',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    }
                },
                'colvis'
            ],
        });
    });
</script>
@endsection