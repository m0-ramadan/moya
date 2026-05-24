@extends('Admin.layout.master')

@section('title', 'تفاصيل الطلب')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #696cff;
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-color: #20c997;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --dark-bg: #1e1e2d;
            --dark-card: #2b3b4c;
        }

        body {
            font-family: "Cairo", sans-serif !important;
            background: var(--dark-bg);
            color: #fff;
        }

        .order-show-card {
            background: var(--dark-card);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .order-show-header {
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .info-section,
        .summary-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .info-section h6,
        .summary-card h6 {
            color: var(--primary-color);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
        }

        .info-line {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            padding: 11px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            font-size: 14px;
        }

        .info-line:last-child {
            border-bottom: 0;
        }

        .info-label {
            color: rgba(255, 255, 255, 0.65);
            font-weight: 500;
        }

        .info-value {
            color: #fff;
            font-weight: 600;
            text-align: left;
            word-break: break-word;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .badge-pending {
            background: rgba(255, 193, 7, 0.16);
            color: #ffc107;
            border: 1px solid rgba(255, 193, 7, 0.28);
        }

        .badge-scheduled {
            background: rgba(23, 162, 184, 0.16);
            color: #17a2b8;
            border: 1px solid rgba(23, 162, 184, 0.28);
        }

        .badge-in-road {
            background: rgba(105, 108, 255, 0.16);
            color: #9da0ff;
            border: 1px solid rgba(105, 108, 255, 0.32);
        }

        .badge-delivered,
        .badge-paid,
        .badge-active {
            background: rgba(32, 201, 151, 0.16);
            color: #20c997;
            border: 1px solid rgba(32, 201, 151, 0.28);
        }

        .badge-cancelled,
        .badge-canceled,
        .badge-failed {
            background: rgba(220, 53, 69, 0.16);
            color: #ff6b7a;
            border: 1px solid rgba(220, 53, 69, 0.28);
        }

        .badge-default {
            background: rgba(255, 255, 255, 0.12);
            color: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }

        .alert-lock {
            background: rgba(220, 53, 69, 0.1);
            border-right: 4px solid var(--danger-color);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            border: 1px solid rgba(220, 53, 69, 0.25);
            color: #fff;
        }

        .alert-guide {
            background: rgba(12, 99, 228, 0.1);
            border-right: 4px solid var(--primary-color);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            border: 1px solid rgba(12, 99, 228, 0.2);
        }

        .btn-primary {
            background: var(--primary-gradient);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4a9a 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(105, 108, 255, 0.4);
        }

        .btn-danger {
            background: #dc3545;
            border: none;
        }

        .btn-danger:hover {
            background: #bb2d3b;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        .timeline {
            position: relative;
            padding: 0;
            margin: 0;
            list-style: none;
        }

        .timeline-item {
            position: relative;
            padding-right: 35px;
            padding-bottom: 22px;
        }

        .timeline-item:last-child {
            padding-bottom: 0;
        }

        .timeline-item::before {
            content: "";
            position: absolute;
            right: 12px;
            top: 28px;
            bottom: 0;
            width: 2px;
            background: rgba(255, 255, 255, 0.12);
        }

        .timeline-item:last-child::before {
            display: none;
        }

        .timeline-icon {
            position: absolute;
            right: 0;
            top: 0;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(105, 108, 255, 0.2);
            color: #fff;
            font-size: 12px;
            border: 1px solid rgba(105, 108, 255, 0.35);
        }

        .timeline-title {
            color: #fff;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .timeline-time {
            color: rgba(255, 255, 255, 0.6);
            font-size: 12px;
        }

        .table-dark-custom {
            width: 100%;
            color: #fff;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-dark-custom th,
        .table-dark-custom td {
            padding: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            vertical-align: middle;
        }

        .table-dark-custom th {
            color: rgba(255, 255, 255, 0.65);
            font-weight: 600;
            background: rgba(255, 255, 255, 0.04);
        }

        .empty-box {
            background: rgba(255, 255, 255, 0.04);
            border: 1px dashed rgba(255, 255, 255, 0.16);
            border-radius: 10px;
            padding: 18px;
            color: rgba(255, 255, 255, 0.65);
            text-align: center;
        }

        .reason-box {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 15px;
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.8;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        @media (max-width: 768px) {
            .order-show-card {
                padding: 20px;
            }

            .info-line {
                flex-direction: column;
                gap: 5px;
            }

            .info-value {
                text-align: right;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $statusName = optional($order->status)->name ?? (optional($order->orderStatus)->name ?? '');

        $statusLabel =
            optional($order->status)->label ?? (optional($order->orderStatus)->label ?? ($statusName ?? '-'));

        $isInRoad = $statusName === 'in-road';
        $isCancelled = in_array($statusName, ['cancelled', 'canceled', 'cancel']);
        $isDelivered = in_array($statusName, ['delivered', 'completed']);

        $cancelledStatus = \App\Models\OrderStatus::whereIn('name', ['cancelled', 'canceled', 'cancel'])->first();
        $cancelledStatusId = $cancelledStatus?->id;

        try {
            $orderDateValue = $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('Y-m-d H:i') : '-';

            $orderDateInputValue = $order->order_date
                ? \Carbon\Carbon::parse($order->order_date)->format('Y-m-d\TH:i')
                : now()->format('Y-m-d\TH:i');
        } catch (\Throwable $e) {
            $orderDateValue = $order->order_date ?? '-';
            $orderDateInputValue = now()->format('Y-m-d\TH:i');
        }

        $createdAtValue = $order->created_at ? $order->created_at->format('Y-m-d H:i') : '-';

        $updatedAtValue = $order->updated_at ? $order->updated_at->format('Y-m-d H:i') : '-';

        $paymentStatusClass = match ($order->payment_status) {
            'paid' => 'badge-paid',
            'failed' => 'badge-failed',
            'refunded' => 'badge-cancelled',
            default => 'badge-default',
        };

        $statusClass = match ($statusName) {
            'pending' => 'badge-pending',
            'scheduled' => 'badge-scheduled',
            'in-road' => 'badge-in-road',
            'delivered', 'completed' => 'badge-delivered',
            'cancelled', 'canceled', 'cancel' => 'badge-cancelled',
            default => 'badge-default',
        };

        $contract = $order->contract ?? null;
        $location = $order->location ?? null;
        $acceptedOffer = $order->acceptedOffer ?? null;
        $latestDriverLocation = $order->latestDriverLocation ?? null;
    @endphp

    <div class="container-xxl flex-grow-1 container-p-y">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                     <a href="{{ route('admin.home') }}">الرئيسية</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.orders.index') }}">الطلبات</a>
                </li>
                <li class="breadcrumb-item active">تفاصيل الطلب</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-12">
                <div class="order-show-card">
                    <div class="order-show-header">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <h5 class="mb-1">تفاصيل الطلب #{{ $order->id }}</h5>
                                <small class="text-muted">
                                    تاريخ الطلب: {{ $orderDateValue }}
                                </small>
                            </div>

                            <div class="d-flex gap-2 flex-wrap">
                                @if (!$isInRoad && !$isCancelled && !$isDelivered)
                                    <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-primary">
                                        <i class="fas fa-edit me-2"></i>تعديل الطلب
                                    </a>
                                @endif

                                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-right me-2"></i>رجوع للقائمة
                                </a>
                            </div>
                        </div>
                    </div>

                    @if ($isInRoad)
                        <div class="alert-lock">
                            <h6 class="mb-2">
                                <i class="fas fa-route me-2"></i>
                                الطلب في الطريق
                            </h6>
                            <div>
                                لا يمكن تعديل بيانات هذا الطلب لأنه في الطريق. الإجراء الوحيد المتاح من هذه الصفحة هو إلغاء
                                الطلب.
                            </div>
                        </div>
                    @elseif ($isCancelled)
                        <div class="alert-lock">
                            <h6 class="mb-2">
                                <i class="fas fa-ban me-2"></i>
                                الطلب ملغي
                            </h6>
                            <div>
                                هذا الطلب تم إلغاؤه ولا يمكن تعديله.
                            </div>
                        </div>
                    @elseif ($isDelivered)
                        <div class="alert-guide">
                            <h6 class="mb-2">
                                <i class="fas fa-check-circle me-2"></i>
                                الطلب مكتمل
                            </h6>
                            <div>
                                هذا الطلب تم توصيله أو اكتماله.
                            </div>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-lg-8">
                            {{-- بيانات الطلب الأساسية --}}
                            <div class="info-section">
                                <h6><i class="fas fa-info-circle me-2"></i>بيانات الطلب الأساسية</h6>

                                <div class="info-line">
                                    <span class="info-label">رقم الطلب</span>
                                    <span class="info-value">#{{ $order->id }}</span>
                                </div>

                                <div class="info-line">
                                    <span class="info-label">حالة الطلب</span>
                                    <span class="info-value">
                                        <span class="status-badge {{ $statusClass }}">
                                            <i class="fas fa-circle"></i>
                                            {{ $statusLabel }}
                                        </span>
                                    </span>
                                </div>

                                <div class="info-line">
                                    <span class="info-label">نوع الطلب</span>
                                    <span class="info-value">
                                        {{ $order->contract_id ? 'طلب تعاقد' : 'طلب عادي' }}
                                    </span>
                                </div>

                                <div class="info-line">
                                    <span class="info-label">توقيت الطلب</span>
                                    <span class="info-value">
                                        {{ $statusName === 'scheduled' ? 'طلب مجدول' : 'طلب حالي' }}
                                    </span>
                                </div>

                                <div class="info-line">
                                    <span class="info-label">تاريخ ووقت الطلب</span>
                                    <span class="info-value">{{ $orderDateValue }}</span>
                                </div>

                                <div class="info-line">
                                    <span class="info-label">تاريخ الإنشاء</span>
                                    <span class="info-value">{{ $createdAtValue }}</span>
                                </div>

                                <div class="info-line">
                                    <span class="info-label">آخر تحديث</span>
                                    <span class="info-value">{{ $updatedAtValue }}</span>
                                </div>

                                <div class="info-line">
                                    <span class="info-label">كود التأكيد</span>
                                    <span class="info-value">{{ $order->code_confirmation ?? '-' }}</span>
                                </div>
                            </div>

                            {{-- بيانات العميل والعنوان --}}
                            <div class="info-section">
                                <h6><i class="fas fa-user me-2"></i>بيانات العميل والعنوان</h6>

                                <div class="info-line">
                                    <span class="info-label">اسم العميل</span>
                                    <span class="info-value">{{ $order->user->name ?? '-' }}</span>
                                </div>

                                <div class="info-line">
                                    <span class="info-label">هاتف العميل</span>
                                    <span class="info-value">
                                        {{ $order->user->phone ?? ($order->user->phone_number ?? '-') }}
                                    </span>
                                </div>

                                <div class="info-line">
                                    <span class="info-label">البريد الإلكتروني</span>
                                    <span class="info-value">{{ $order->user->email ?? '-' }}</span>
                                </div>

                                <div class="info-line">
                                    <span class="info-label">العنوان المحفوظ</span>
                                    <span class="info-value">
                                        @if ($location)
                                            {{ $location->label ?? ($location->name ?? 'عنوان') }}
                                            -
                                            {{ $location->address_details ?? ($location->address ?? ($location->city ?? '-')) }}
                                        @else
                                            -
                                        @endif
                                    </span>
                                </div>
                            </div>

                            {{-- بيانات الخدمة --}}
                            <div class="info-section">
                                <h6><i class="fas fa-cogs me-2"></i>بيانات الخدمة</h6>

                                <div class="info-line">
                                    <span class="info-label">الخدمة</span>
                                    <span class="info-value">{{ $order->service->name ?? '-' }}</span>
                                </div>

                                <div class="info-line">
                                    <span class="info-label">نوع المياه</span>
                                    <span class="info-value">{{ $order->waterType->name ?? '-' }}</span>
                                </div>

                                <div class="info-line">
                                    <span class="info-label">السائق</span>
                                    <span class="info-value">
                                        @if ($order->driver)
                                            {{ $order->driver->user->name ?? 'سائق بدون اسم' }}
                                            -
                                            {{ $order->driver->user->phone ?? ($order->driver->user->phone_number ?? 'بدون هاتف') }}
                                        @else
                                            لم يتم تعيين سائق
                                        @endif
                                    </span>
                                </div>

                                @if ($acceptedOffer)
                                    <div class="info-line">
                                        <span class="info-label">سعر العرض المقبول</span>
                                        <span class="info-value">{{ number_format($acceptedOffer->price ?? 0, 2) }}</span>
                                    </div>

                                    <div class="info-line">
                                        <span class="info-label">مدة التوصيل المتوقعة</span>
                                        <span class="info-value">
                                            {{ $acceptedOffer->delivery_duration_minutes ?? '-' }} دقيقة
                                        </span>
                                    </div>
                                @endif
                            </div>

                            {{-- بيانات التعاقد --}}
                            @if ($order->contract_id)
                                <div class="info-section">
                                    <h6><i class="fas fa-file-contract me-2"></i>بيانات التعاقد</h6>

                                    <div class="info-line">
                                        <span class="info-label">رقم العقد</span>
                                        <span
                                            class="info-value">{{ $contract->contract_number ?? $order->contract_id }}</span>
                                    </div>

                                    <div class="info-line">
                                        <span class="info-label">نوع العقد</span>
                                        <span class="info-value">
                                            @if (($contract->contract_type ?? '') === 'individual')
                                                فردي
                                            @elseif (($contract->contract_type ?? '') === 'company')
                                                شركة
                                            @else
                                                {{ $contract->contract_type ?? '-' }}
                                            @endif
                                        </span>
                                    </div>

                                    <div class="info-line">
                                        <span class="info-label">اسم مقدم الطلب</span>
                                        <span class="info-value">{{ $contract->applicant_name ?? '-' }}</span>
                                    </div>

                                    <div class="info-line">
                                        <span class="info-label">الشركة</span>
                                        <span class="info-value">{{ $contract->company_name ?? '-' }}</span>
                                    </div>

                                    <div class="info-line">
                                        <span class="info-label">الطلبات المتبقية</span>
                                        <span class="info-value">
                                            {{ $contract->remaining_orders ?? '-' }}
                                            /
                                            {{ $contract->total_orders_limit ?? '-' }}
                                        </span>
                                    </div>
                                </div>
                            @endif

                            {{-- العروض --}}
                            <div class="info-section">
                                <h6><i class="fas fa-tags me-2"></i>عروض السائقين</h6>

                                @if ($order->offers && $order->offers->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table-dark-custom">
                                            <thead>
                                                <tr>
                                                    <th>السائق</th>
                                                    <th>السعر</th>
                                                    <th>المدة</th>
                                                    <th>الحالة</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($order->offers as $offer)
                                                    <tr>
                                                        <td>
                                                            {{ $offer->driver->user->name ?? 'سائق بدون اسم' }}
                                                        </td>
                                                        <td>{{ number_format($offer->price ?? 0, 2) }}</td>
                                                        <td>{{ $offer->delivery_duration_minutes ?? '-' }} دقيقة</td>
                                                        <td>
                                                            <span
                                                                class="status-badge {{ $offer->status === 'accepted' ? 'badge-active' : 'badge-default' }}">
                                                                {{ $offer->status ?? '-' }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="empty-box">
                                        لا توجد عروض على هذا الطلب.
                                    </div>
                                @endif
                            </div>

                            {{-- الإلغاء --}}
                            @if ($order->cancellation)
                                <div class="info-section">
                                    <h6><i class="fas fa-ban me-2"></i>بيانات الإلغاء</h6>

                                    <div class="info-line">
                                        <span class="info-label">سبب الإلغاء</span>
                                        <span class="info-value">{{ $order->cancellation->reason ?? '-' }}</span>
                                    </div>

                                    <div class="info-line">
                                        <span class="info-label">ملاحظات الإلغاء</span>
                                        <span class="info-value">{{ $order->cancellation->notes ?? '-' }}</span>
                                    </div>

                                    <div class="info-line">
                                        <span class="info-label">تاريخ الإلغاء</span>
                                        <span class="info-value">
                                            {{ $order->cancellation->created_at ? $order->cancellation->created_at->format('Y-m-d H:i') : '-' }}
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="col-lg-4">
                            {{-- الدفع --}}
                            <div class="summary-card">
                                <h6><i class="fas fa-credit-card me-2"></i>معلومات الدفع</h6>
                                <div class="info-line">
                                    <span class="info-label"> قيمة الدفع</span>
                                    <span class="info-value">{{ $order->acceptedOffer?->price ?? '-' }}</span>
                                </div>
                                <div class="info-line">
                                    <span class="info-label">حالة الدفع</span>
                                    <span class="info-value">
                                        <span class="status-badge {{ $paymentStatusClass }}">
                                            {{ $order->payment_status ?? '-' }}
                                        </span>
                                    </span>
                                </div>

                                <div class="info-line">
                                    <span class="info-label">طريقة الدفع</span>
                                    <span class="info-value">{{ $order->payment_method ?? '-' }}</span>
                                </div>

                                <div class="info-line">
                                    <span class="info-label">بوابة الدفع</span>
                                    <span class="info-value">{{ $order->payment_gateway ?? '-' }}</span>
                                </div>

                                <div class="info-line">
                                    <span class="info-label">رقم العملية</span>
                                    <span class="info-value">{{ $order->payment_transaction_id ?? '-' }}</span>
                                </div>

                                <div class="info-line">
                                    <span class="info-label">تاريخ الدفع</span>
                                    <span class="info-value">
                                        {{ $order->paid_at ? \Carbon\Carbon::parse($order->paid_at)->format('Y-m-d H:i') : '-' }}
                                    </span>
                                </div>
                            </div>

                            {{-- آخر موقع للسائق --}}
                            <div class="summary-card">
                                <h6><i class="fas fa-map-marker-alt me-2"></i>تتبع السائق</h6>

                                @if ($latestDriverLocation)
                                    <div class="info-line">
                                        <span class="info-label">خط العرض</span>
                                        <span class="info-value">{{ $latestDriverLocation->latitude ?? '-' }}</span>
                                    </div>

                                    <div class="info-line">
                                        <span class="info-label">خط الطول</span>
                                        <span class="info-value">{{ $latestDriverLocation->longitude ?? '-' }}</span>
                                    </div>

                                    <div class="info-line">
                                        <span class="info-label">المسافة المتبقية</span>
                                        <span
                                            class="info-value">{{ $latestDriverLocation->distance_to_destination ?? '-' }}</span>
                                    </div>

                                    <div class="info-line">
                                        <span class="info-label">آخر تحديث</span>
                                        <span class="info-value">
                                            {{ $latestDriverLocation->created_at ? $latestDriverLocation->created_at->format('Y-m-d H:i') : '-' }}
                                        </span>
                                    </div>
                                @else
                                    <div class="empty-box">
                                        لا يوجد تتبع حالي للسائق.
                                    </div>
                                @endif
                            </div>

                            {{-- التايم لاين --}}
                            <div class="summary-card">
                                <h6><i class="fas fa-stream me-2"></i>خط سير الطلب</h6>

                                @if (!empty($timeline))
                                    <ul class="timeline">
                                        @foreach ($timeline as $item)
                                            <li class="timeline-item">
                                                <div class="timeline-icon">
                                                    <i class="fas {{ $item['icon'] ?? 'fa-circle' }}"></i>
                                                </div>
                                                <div class="timeline-title">
                                                    {{ $item['label'] ?? '-' }}
                                                </div>
                                                <div class="timeline-time">
                                                    {{ isset($item['time']) && $item['time'] ? \Carbon\Carbon::parse($item['time'])->format('Y-m-d H:i') : '-' }}
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <div class="empty-box">
                                        لا توجد أحداث مسجلة.
                                    </div>
                                @endif
                            </div>

                            {{-- ملاحظات --}}
                            <div class="summary-card">
                                <h6><i class="fas fa-sticky-note me-2"></i>الملاحظات</h6>

                                @if ($order->notes)
                                    <div class="reason-box">
                                        {{ $order->notes }}
                                    </div>
                                @else
                                    <div class="empty-box">
                                        لا توجد ملاحظات.
                                    </div>
                                @endif
                            </div>

                            {{-- الإجراءات --}}
                            <div class="summary-card">
                                <h6><i class="fas fa-tools me-2"></i>الإجراءات</h6>

                                <div class="d-grid gap-2">
                                    @if (!$isInRoad && !$isCancelled && !$isDelivered)
                                        <a href="{{ route('admin.orders.edit', $order->id) }}"
                                            class="btn btn-primary btn-lg">
                                            <i class="fas fa-edit me-2"></i>تعديل الطلب
                                        </a>
                                    @endif

                                    @if ($isInRoad && !$isCancelled)
                                        <form action="{{ route('admin.orders.update', $order->id) }}" method="POST"
                                            id="cancelInRoadForm">
                                            @csrf
                                            @method('PUT')

                                            <input type="hidden" name="cancel_only" value="1">
                                            <input type="hidden" name="user_id" value="{{ $order->user_id }}">
                                            <input type="hidden" name="service_id" value="{{ $order->service_id }}">
                                            <input type="hidden" name="water_type_id"
                                                value="{{ $order->water_type_id }}">
                                            <input type="hidden" name="saved_location_id"
                                                value="{{ $order->saved_location_id }}">
                                            <input type="hidden" name="driver_id" value="{{ $order->driver_id }}">
                                            <input type="hidden" name="contract_id" value="{{ $order->contract_id }}">
                                            <input type="hidden" name="order_date" value="{{ $orderDateInputValue }}">
                                            <input type="hidden" name="payment_status"
                                                value="{{ $order->payment_status }}">
                                            <input type="hidden" name="payment_method"
                                                value="{{ $order->payment_method }}">
                                            <input type="hidden" name="payment_gateway"
                                                value="{{ $order->payment_gateway }}">
                                            <input type="hidden" name="payment_transaction_id"
                                                value="{{ $order->payment_transaction_id }}">
                                            <input type="hidden" name="order_status_id"
                                                value="{{ $cancelledStatusId }}">
                                            <textarea name="notes" style="display:none;">{{ $order->notes }}</textarea>

                                            <button type="submit" class="btn btn-danger btn-lg w-100"
                                                {{ !$cancelledStatusId ? 'disabled' : '' }}>
                                                <i class="fas fa-ban me-2"></i>إلغاء الطلب
                                            </button>
                                        </form>

                                        @if (!$cancelledStatusId)
                                            <div class="alert alert-warning mt-3 mb-0">
                                                لا توجد حالة إلغاء داخل حالات الطلبات. أضف حالة باسم cancelled أو canceled.
                                            </div>
                                        @endif
                                    @endif

                                    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-lg">
                                        <i class="fas fa-arrow-right me-2"></i>رجوع للقائمة
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cancelForm = document.getElementById('cancelInRoadForm');

            if (cancelForm) {
                cancelForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    Swal.fire({
                        icon: 'warning',
                        title: 'تأكيد إلغاء الطلب',
                        text: 'الطلب في الطريق. هل أنت متأكد أنك تريد إلغاءه؟',
                        showCancelButton: true,
                        confirmButtonText: 'نعم، إلغاء الطلب',
                        cancelButtonText: 'رجوع',
                        confirmButtonColor: '#dc3545'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            cancelForm.submit();
                        }
                    });
                });
            }

            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'نجاح',
                    text: "{{ session('success') }}",
                    timer: 2000,
                    showConfirmButton: false
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: "{{ session('error') }}",
                    timer: 2500,
                    showConfirmButton: false
                });
            @endif
        });
    </script>
@endsection
