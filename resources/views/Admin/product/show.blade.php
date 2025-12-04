@extends('Admin.layout.master')

@section('title')
    تفاصيل التاجر
@endsection

@section('css')
    <!-- Plugins css start-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/datatables.css') }}">
    <!-- Plugins css Ends-->
    <style>
        .nav-tabs .nav-link.active {
            background-color: #28a745;
            color: white;
            border-color: #28a745;
        }

        .nav-tabs .nav-link {
            color: #28a745;
        }

        .tab-content {
            margin-top: 20px;
        }

        .card-body {
            padding: 1.5rem;
        }

        .table-responsive {
            margin-top: 10px;
        }
    </style>
@endsection

@section('content')
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>تفاصيل التاجر: {{ $client->name }}</h5>
                <a class="btn btn-primary" href="{{ route('admin.product.index') }}">رجوع</a>
                <a class="btn btn-success" href="{{ route('admin.product.create', $client->id) }}"> اضافة منتج
                    ل{{ $client->name }} </a>

            </div>
            <div class="card-body">
                <!-- Client Details -->
                <div class="row">
                    <div class="col-md-6">
                        <h6>المعلومات الأساسية</h6>
                        <p><strong>الاسم:</strong> {{ $client->name }}</p>
                        <p><strong>الكود:</strong> {{ $client->code ?? '--' }}</p>
                        <p><strong>البريد الإلكتروني:</strong> {{ $client->email ?? '--' }}</p>
                        <p><strong>رقم الهاتف:</strong> {{ $client->phone ?? '--' }}</p>
                        <p><strong>رقم الهاتف الثاني:</strong> {{ $client->phone2 ?? '--' }}</p>
                        <p><strong>العنوان:</strong> {{ $client->address ?? '--' }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>معلومات إضافية</h6>
                        <p><strong>الفرع:</strong> {{ $client->branch->name ?? '--' }}</p>
                        <p><strong>المنطقة:</strong> {{ $client->region->region_ar ?? '--' }}</p>
                        <p><strong>الدولة:</strong> {{ $client->country->country_ar ?? '--' }}</p>
                        <p><strong>الحالة:</strong> {{ $client->status_label }}</p>
                        <p><strong>عدد المنتجات:</strong> {{ $client->product_count }}</p>
                        <p><strong>إجمالي المخزون:</strong> {{ $client->product_sum }}</p>
                    </div>
                </div>

                <!-- Tabs for Products and Shipments -->
                <ul class="nav nav-tabs" id="clientTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="products-tab" data-toggle="tab" href="#products" role="tab"
                            aria-controls="products" aria-selected="true">المنتجات</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="shipments-tab" data-toggle="tab" href="#shipments" role="tab"
                            aria-controls="shipments" aria-selected="false">الشحنات</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tasks-tab" data-toggle="tab" href="#tasks" role="tab"
                            aria-controls="tasks" aria-selected="false">المهام</a>
                    </li>
                </ul>

                <div class="tab-content" id="clientTabsContent">
                    <!-- Products Tab -->
                    <div class="tab-pane fade show active" id="products" role="tabpanel" aria-labelledby="products-tab">
                        @if ($client->products->isEmpty())
                            <p class="text-center">لا توجد منتجات لهذا التاجر</p>
                        @else
                            <div class="table-responsive">
                                <table class="display" id="products-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>اسم المنتج</th>
                                            <th>الكود</th>
                                            <th>الفئة</th>
                                            <th>العلامة التجارية</th>
                                            <th>الكمية في المخزون</th>
                                            <th>سعر البيع</th>
                                            <th>الخصم</th>
                                            <th>العمليات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($client->products as $key => $product)
                                            <tr>
                                                <td>{{ ++$key }}</td>
                                                <td>{{ $product->name }}</td>
                                                <td>{{ $product->code ?? '--' }}</td>
                                                <td>{{ $product->category ?? '--' }}</td>
                                                <td>{{ $product->brand ?? '--' }}</td>
                                                <td>{{ $product->in_stock_quantity ?? '--' }}</td>
                                                <td>{{ $product->sale_price ?? '--' }}</td>
                                                <td>{{ $product->discounts ?? '--' }}</td>
                                                <td class="d-flex gap-1">
                                                    <a href="{{ route('admin.product.edit', $product->id) }}"
                                                        class="btn btn-success" title="تعديل">
                                                        <i class="fa fa-edit text-white"></i>
                                                    </a>
                                                    <form method="POST"
                                                        action="{{ route('admin.product.destroy', $product->id) }}"
                                                        onsubmit="return confirm('هل أنت متأكد أنك تريد حذف هذا المنتج؟');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger" title="حذف">
                                                            <i class="fa fa-trash-o"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <!-- Shipments Tab -->
                    <div class="tab-pane fade" id="shipments" role="tabpanel" aria-labelledby="shipments-tab">
                        @if ($client->shipments->isEmpty())
                            <p class="text-center">لا توجد شحنات لهذا التاجر</p>
                        @else
                            <div class="table-responsive">
                                <table class="display" id="shipments-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>رقم الشحنة</th>
                                            <th>الحالة</th>
                                            <th>قيمة الشحنة</th>
                                            <th>تكلفة الشحن</th>
                                            <th>الوصف</th>
                                            <th>العمله</th>
                                            <th>رسوم الشحن</th>
                                            <th>الإجمالي</th>
                                            <th>الفرع من</th>
                                            <th>الفرع إلى</th>
                                            <th>العمليات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($client->shipments as $key => $shipment)
                                            <tr>
                                                <td>{{ ++$key }}</td>
                                                <td>{{ $shipment->code ?? '--' }}</td>
                                                <td>
                                                    <span class="badge {{ $shipment->status_badge_class }}">
                                                        {{ $shipment->status_label }}
                                                    </span>
                                                </td>
                                                <td>{{ $shipment->price ?? '--' }}</td>
                                                <td>{{ $shipment->shipping_cost ?? '--' }}</td>
                                                <td>{{ $shipment->describe_shipments ?? '--' }}</td>
                                                <td>{{ $shipment->effective_currency_label ?? '--' }}</td>
                                                <td>{{ $shipment->additional_shipping_cost == 1 ? ' مرسل ' : ' مستلم ' }}</td>


                                                <td>{{ $shipment->additional_shipping_cost == 1 ? $shipment->price - $shipment->shipping_cost : $shipment->price + $shipment->shipping_cost }}
                                                </td>
                                                <td>{{ $shipment->branchFrom->name ?? '--' }}</td>
                                                <td>{{ $shipment->branchTo->name ?? '--' }}</td>
                                                <td class="d-flex gap-1">
                                                    <a href="{{ route('admin.shipment.show', $shipment->id) }}"
                                                        class="btn btn-success" title="عرض">
                                                        <i class="fa fa-eye text-white"></i>
                                                    </a>
                                                    <form method="POST"
                                                        action="{{ route('admin.shipment.destroy', $shipment->id) }}"
                                                        onsubmit="return confirm('هل أنت متأكد أنك تريد حذف هذه الشحنة؟');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger" title="حذف">
                                                            <i class="fa fa-trash-o"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <!-- tasks Tab -->
                    <!-- tasks Tab -->
                    <div class="tab-pane fade" id="tasks" role="tabpanel" aria-labelledby="tasks-tab">
                        @if ($client->tasks->isEmpty())
                            <p class="text-center">لا توجد مهام لهذا التاجر</p>
                        @else
                            <div class="table-responsive">
                                <table class="display" id="tasks-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>نوع المهمة</th>
                                            <th>عدد الطلبات</th>
                                            <th>العنوان</th>
                                            <th>تاريخ التنفيذ</th>
                                            <th>المدة</th>
                                            <th>الملاحظات</th>
                                            <th>طريقة الاستلام</th>
                                            <th>قيمة المبلغ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($client->tasks as $key => $task)
                                            <tr>
                                                <td>{{ ++$key }}</td>
                                                <td>
                                                    @switch($task->type_task)
                                                        @case(\App\Models\Task::TYPE_COLLECT)
                                                            تجميع
                                                        @break

                                                        @case(\App\Models\Task::TYPE_SETTLE)
                                                            تسوية
                                                        @break

                                                        @case(\App\Models\Task::TYPE_DELIVER_RETURNS)
                                                            تسليم مرتجعات
                                                        @break

                                                        @default
                                                            --
                                                    @endswitch
                                                </td>
                                                <td>{{ $task->number_order ?? '--' }}</td>
                                                <td>{{ $task->address ?? '--' }}</td>
                                                <td>{{ $task->date_implementation ?? '--' }}</td>
                                                <td>
                                                    @switch($task->duration)
                                                        @case(\App\Models\Task::DURATION_MONTH)
                                                            شهر
                                                        @break

                                                        @case(\App\Models\Task::DURATION_TWO_MONTHS)
                                                            شهرين
                                                        @break

                                                        @case(\App\Models\Task::DURATION_THREE_MONTHS)
                                                            ثلاثة أشهر
                                                        @break

                                                        @default
                                                            --
                                                    @endswitch
                                                </td>
                                                <td>{{ $task->notes ?? '--' }}</td>
                                                <td>
                                                    @switch($task->receive_via)
                                                        @case(\App\Models\Task::RECEIVE_BRANCH)
                                                            فرع
                                                        @break

                                                        @case(\App\Models\Task::RECEIVE_REPRESENTATIVE)
                                                            مندوب
                                                        @break

                                                        @case(\App\Models\Task::RECEIVE_BANK)
                                                            بنك
                                                        @break

                                                        @default
                                                            --
                                                    @endswitch
                                                </td>
                                                <td>{{ $task->value_amount ?? '--' }}</td>
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
@endsection

@section('js')
    <!-- Plugins JS start-->
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize Products DataTable
            $('#products-table').DataTable({
                language: {
                    url: '{{ asset('assets/js/datatable/datatables/Arabic.json') }}'
                }
            });

            // Initialize Shipments DataTable only when the tab is shown
            let shipmentsTableInitialized = false;
            $('#shipments-tab').on('shown.bs.tab', function() {
                if (!shipmentsTableInitialized) {
                    $('#shipments-table').DataTable({
                        language: {
                            url: '{{ asset('assets/js/datatable/datatables/Arabic.json') }}'
                        }
                    });
                    shipmentsTableInitialized = true;
                }
            });

            // Ensure tabs are initialized
            $('#clientTabs a').on('click', function(e) {
                e.preventDefault();
                $(this).tab('show');
            });
        });
    </script>
    <script src="{{ asset('assets/js/tooltip-init.js') }}"></script>
    <!-- Plugins JS Ends-->
@endsection
