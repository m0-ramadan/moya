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

        .modal-content td.status-1 {
            color: green;
        }

        /* اتسلمت */
        .modal-content td.status-2 {
            color: blue;
        }

        /* في الفرع */
        .modal-content td.status-3 {
            color: orange;
        }

        /* انتقل لفرع آخر */
        .modal-content td.status-4 {
            color: red;
        }

        /* تلف */
        .modal-content td.status-5 {
            color: purple;
        }

        /* ناقص */
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
                                @php
                                    $userBranchId = auth()->user()->branch_id;
                                    $isAdmin = is_null($userBranchId);
                                    // Get warehouse contents for this shipment
                                    $warehouseContents = $warehouseData[$shipment->id] ?? collect([]);
                                    // Filter contents by branches_from for non-admins
                                    $filteredContents = $isAdmin
                                        ? $warehouseContents
                                        : $warehouseContents->filter(function ($warehouse) use ($userBranchId) {
                                            return $warehouse->branches_from == $userBranchId;
                                        });
                                @endphp
                                @if ($filteredContents->count() > 0)
                                    <tr>
                                        <td class="center">{{ $i++ }}</td>
                                        <td class="center">
                                            {{ $shipment->code ?? '--' }}</td>
                                        <td class="center">
                                            {{ $shipment->person->name ?? '--' }}
                                        </td>
                                        <td class="center">


                                            <button class="btn btn-success action-btn" data-bs-toggle="modal"
                                                data-bs-target="#contentModal"
                                                data-content='{{ json_encode(
                                                    $filteredContents->map(function ($warehouse) use ($userBranchId) {
                                                            $content = $warehouse->shContent;
                                                            $tripContent = $warehouse->trContent;
                                                            $tripC = $warehouse->shipment;
                                                            if (auth()->user()->role == 0) {
                                                                $quantity = $content->quantity;
                                                            } else {
                                                                $quantity = $warehouse->quantity ?? 0;
                                                            }
                                                            $statusLabel = match ($warehouse->status) {
                                                                1 => 'اتسلمت',
                                                                2 => 'في الفرع',
                                                                3 => 'انتقل لفرع آخر',
                                                                4 => 'تلف',
                                                                5 => 'ناقص',
                                                                default => 'غير معروف',
                                                            };
                                                
                                                            return [
                                                                'code' => $content ? $content->code ?? '--' : '--',
                                                                'name' => $content ? $content->name ?? '--' : '--',
                                                                'price' => $content ? $content->price ?? '--' : '--',
                                                
                                                                'quantity' => $quantity ?? 0,
                                                                'taken' => $tripContent ? $tripContent->taken ?? 0 : ($content ? $content->taken ?? 0 : 0),
                                                                'remaining' => max(
                                                                    0,
                                                                    $warehouse->quantity -
                                                                        ($tripContent ? $tripContent->taken ?? 0 : ($content ? $content->taken ?? 0 : 0)),
                                                                ),
                                                
                                                                'status' => $statusLabel,
                                                            ];
                                                        })->values()->toArray(),
                                                ) }}'>
                                                <i class="fa fa-eye text-white"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endif
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
                    <h5 class="modal-title" id="contentModalLabel">تفاصيل المحتويات
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table>
                        <thead>
                            <tr>
                                <th>كود المحتوى</th>
                                <th>الاسم</th>
                                <th>الاجمالي</th>
                                <th>سعر القطعة</th>
                                <th>الكمية المأخوذة</th>
                                <th>الكمية المتبقية</th>
                                <th>الحالة</th>
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
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
@endsection

@section('js')
    <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatables/datatable.custom.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#basic-1').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Arabic.json'
                },
                columnDefs: [{
                    orderable: false,
                    targets: 3
                }]
            });

            $('.action-btn').on('click', function() {
                const contents = $(this).data('content');
                console.log('Contents:', contents);
                const $modalBody = $('#modalContentBody');
                $modalBody.empty();

                if (contents && Array.isArray(contents) && contents.length > 0) {
                    contents.forEach(content => {
                        $modalBody.append(`
                            <tr>
                                <td>${String(content.code || '--')}</td>
                                <td>${String(content.name || '--')}</td>
                                <td>${String(content.price || '--')}</td>

                                <td>${content.quantity || 0}</td>
                                <td>${content.taken || 0}</td>
                                <td>${content.remaining || 0}</td>
                                <td class="status-${content.status === 'اتسلمت' ? 1 : content.status === 'في الفرع' ? 2 : content.status === 'انتقل لفرع آخر' ? 3 : content.status === 'تلف' ? 4 : 5}">${content.status || 'غير معروف'}</td>
                            </tr>
                        `);
                    });
                } else {
                    $modalBody.append('<tr><td colspan="6" class="center">لا توجد محتويات متاحة</td></tr>');
                }
            });
        });
    </script>
@endsection
