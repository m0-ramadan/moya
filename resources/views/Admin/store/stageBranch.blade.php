@extends('Admin.layout.master')

@section('title')
    محتوي المخزن
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
        }

        .center {
            text-align: center !important;
            vertical-align: middle;
        }

        .action-btn {
            padding: 5px 10px;
            font-size: 14px;
        }

        .barcode-img {
            max-width: 100px;
            height: auto;
        }

        .status-select {
            color: black !important;
            font-size: 0.875rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
        }

        .status-select.status-10 {
            background-color: #0dcaf0;
        }

        .modal-content {
            font-family: 'Cairo', sans-serif;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>محتوي المخزن</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">الرئيسية</a></li>
                        <li class="breadcrumb-item active">محتوي المخزن</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>قائمة محتويات الشحنات (حالة: مرحلة إلى فرع آخر)</h5>
                    </div>
                    <div class="card-body">
                        @if ($contents->isEmpty())
                            <div class="alert alert-info">
                                لا توجد محتويات شحنات بحالة "مرحلة إلى فرع آخر".
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="display" id="basic-1">
                                    <thead>
                                        <tr>
                                            <th class="center">#</th>
                                            <th class="center">كود الشحنة</th>
                                            <th class="center">من فرع</th>
                                            <th class="center">إلى فرع</th>
                                            <th class="center">كود المحتوى</th>
                                            <th class="center">اسم المحتوى</th>
                                            <th class="center">الكمية</th>
                                            <th class="center">الكمية المأخوذة</th>
                                            <th class="center">الحالة</th>
                                            <th class="center">الباركود</th>
                                            <th class="center">الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($contents as $index => $tripContent)
                                            <tr>
                                                <td class="center">{{ $index + 1 }}</td>
                                                <td class="center">
                                                    {{ $tripContent->shipmentContent->shipment->code ?? '--' }}</td>
                                                <td class="center">
                                                    {{ $tripContent->shipmentContent->shipment->branchFrom->name ?? 'غير متوفر' }}
                                                </td>
                                                <td class="center">
                                                    {{ $tripContent->shipmentContent->shipment->branchTo->name ?? 'غير متوفر' }}
                                                </td>
                                                <td class="center">{{ $tripContent->shipmentContent->code ?? '--' }}</td>
                                                <td class="center">{{ $tripContent->shipmentContent->name ?? '--' }}</td>
                                                <td class="center">{{ $tripContent->quantity ?? 0 }}</td>
                                                <td class="center">{{ $tripContent->taken ?? 0 }}</td>
                                                <td class="center">
                                                    <span class="status-select status-10">مرحلة إلى فرع آخر</span>
                                                </td>
                                                <td class="center">
                                                    @if ($tripContent->shipmentContent->barcode)
                                                        <img src="{{ asset('public/storage/' . $tripContent->shipmentContent->barcode) }}"
                                                            alt="Barcode" class="barcode-img">
                                                    @else
                                                        --
                                                    @endif
                                                </td>
                                                <td class="center">
                                                    <button class="btn btn-success action-btn" data-bs-toggle="modal"
                                                        data-bs-target="#contentModal"
                                                        data-content='{{ json_encode([
                                                            'code' => $tripContent->shipmentContent->code ?? '--',
                                                            'name' => $tripContent->shipmentContent->name ?? '--',
                                                            'quantity' => $tripContent->quantity ?? 0,
                                                            'taken' => $tripContent->taken ?? 0,
                                                            'remaining' => ($tripContent->quantity ?? 0) - ($tripContent->taken ?? 0),
                                                            'shipment_code' => $tripContent->shipmentContent->shipment->code ?? '--',
                                                            'branch_from' => $tripContent->shipmentContent->shipment->branchFrom->name ?? 'غير متوفر',
                                                            'branch_to' => $tripContent->shipmentContent->shipment->branchTo->name ?? 'غير متوفر',
                                                        ]) }}'>
                                                        <i class="fa fa-eye text-white"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Content Details -->
    <div class="modal fade" id="contentModal" tabindex="-1" aria-labelledby="contentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="contentModalLabel">تفاصيل المحتوى</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>كود الشحنة:</strong> <span id="shipment-code"></span></p>
                    <p><strong>من فرع:</strong> <span id="branch-from"></span></p>
                    <p><strong>إلى فرع:</strong> <span id="branch-to"></span></p>
                    <p><strong>كود المحتوى:</strong> <span id="content-code"></span></p>
                    <p><strong>الاسم:</strong> <span id="content-name"></span></p>
                    <p><strong>الكمية الكلية:</strong> <span id="content-quantity"></span></p>
                    <p><strong>الكمية المأخوذة:</strong> <span id="content-taken"></span></p>
                    <p><strong>الكمية المتبقية:</strong> <span id="content-remaining"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatable-extension/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatable-extension/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatable-extension/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatable-extension/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatable-extension/vfs_fonts.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatable-extension/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatable-extension/buttons.print.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            var table = $('#basic-1').DataTable({
                pagingType: 'full_numbers',
                dom: 'lBfrtip',
                buttons: [{
                        extend: 'copyHtml5',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8] // Exclude barcode and actions
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                        }
                    },
                    {
                        extend: 'print',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                        }
                    },
                    'colvis'
                ],
                language: {
                    url: '{{ asset('assets/js/datatable/datatables/Arabic.json') }}'
                },
                columnDefs: [{
                        targets: [9, 10],
                        orderable: false
                    } // Disable sorting on barcode and actions
                ]
            });

            // Handle modal data population
            $('#basic-1').on('click', '.action-btn', function() {
                var content = $(this).data('content');
                $('#shipment-code').text(content.shipment_code);
                $('#branch-from').text(content.branch_from);
                $('#branch-to').text(content.branch_to);
                $('#content-code').text(content.code);
                $('#content-name').text(content.name);
                $('#content-quantity').text(content.quantity);
                $('#content-taken').text(content.taken);
                $('#content-remaining').text(content.remaining);
            });
        });
    </script>
@endsection
