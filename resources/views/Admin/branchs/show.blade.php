@extends('Admin.layout.master')

@section('title')
    المخازن
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/datatables.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .center {
            text-align: center !important;
        }

        .action-btn {
            font-size: 14px;
            padding: 5px 10px;
        }

        .modal-content {
            font-family: 'Cairo', sans-serif;
        }

        .modal-content table {
            width: 100%;
            border-collapse: collapse;
        }

        .modal-content th,
        .modal-content td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: center;
        }

        .modal-content th {
            background-color: #f8f9fa;
        }
    </style>
@endsection

@section('content')
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                {{-- <a class="btn btn-success" href="{{ route('admin.stores.create') }}">إضافة مخزن</a> --}}

            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="display" id="basic-1">
                        <thead>
                            <tr>
                                <th class="center">#</th>
                                <th class="center">كود الشحنة</th>
                                <th class="center">صاحب الشحنة</th>
                                <th class="center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i = 1; @endphp
                            @foreach ($shipments as $shipment)
                                <tr>
                                    <td class="center">{{ $i++ }}</td>
                                    <td class="center">{{ $shipment->code ?? '--' }}</td>
                                    <td class="center">{{ $shipment->person->name ?? '--' }}</td>
                                    <td class="center">
                                        <button class="btn btn-success action-btn" data-bs-toggle="modal"
                                            data-bs-target="#contentModal"
                                            data-content='{{ json_encode(
                                                $shipment->contents->map(function ($content) {
                                                        return [
                                                            'code' => $content->code ?? '--',
                                                            'name' => $content->name ?? '--',
                                                            'quantity' => $content->quantity ?? 0,
                                                            'taken' => $content->taken ?? 0,
                                                            'remaining' => ($content->quantity ?? 0) - ($content->taken ?? 0),
                                                        ];
                                                    })->values()->toArray(),
                                            ) }}'>
                                            <i class="fa fa-eye text-white"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Modal -->
    <div class="modal fade" id="contentModal" tabindex="-1" aria-labelledby="contentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="contentModalLabel">تفاصيل المحتويات</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table>
                        <thead>
                            <tr>
                                <th>كود المحتوى</th>
                                <th>الاسم</th>
                                <th>الكمية</th>
                                <th>الكمية المأخوذة</th>
                                <th>الكمية المتبقية</th>
                            </tr>
                        </thead>
                        <tbody id="modalContentBody"></tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Display Flash Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="number" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
@endsection

@section('js')
    <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatables/datatable.custom.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTables
            $('#basic-1').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Arabic.json'
                },
                columnDefs: [{
                    orderable: false,
                    targets: 3
                }]
            });

            // Handle modal content population
            $('.action-btn').on('click', function() {
                const contents = $(this).data('content');
                console.log('Contents:', contents); // Debug
                const $modalBody = $('#modalContentBody');
                $modalBody.empty();

                if (contents && Array.isArray(contents) && contents.length > 0) {
                    contents.forEach(content => {
                        $modalBody.append(`
                            <tr>
                                <td>${String(content.code || '--')}</td>
                                <td>${String(content.name || '--')}</td>
                                <td>${content.quantity || 0}</td>
                                <td>${content.taken || 0}</td>
                                <td>${content.remaining || 0}</td>
                            </tr>
                        `);
                    });
                } else {
                    $modalBody.append('<tr><td colspan="5" class="center">لا توجد محتويات متاحة</td></tr>');
                }
            });
        });
    </script>
@endsection
