@extends('Admin.layout.master')

@section('title', 'تعديل الطلب #' . $order->id)

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #696cff;
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --light-bg: #f8f9fa;
            --border-color: #e9ecef;
            --text-muted: #6c757d;
            --dark-bg: #1e1e2d;
            --dark-card: #2b3b4c;
        }

        body {
            font-family: "Cairo", sans-serif !important;
            background: var(--dark-bg);
            color: #fff;
        }

        .order-edit-card {
            background: var(--dark-card);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .order-edit-header {
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .badge-status {
            padding: 6px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 13px;
        }

        .status-pending {
            background: rgba(133, 100, 4, 0.2);
            color: #ffc107;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }

        .status-in-road {
            background: rgba(12, 84, 96, 0.2);
            color: #0dcaf0;
            border: 1px solid rgba(13, 202, 240, 0.3);
        }

        .status-scheduled {
            background: rgba(103, 58, 183, 0.2);
            color: #ab8ce4;
            border: 1px solid rgba(171, 140, 228, 0.3);
        }

        .status-delivered {
            background: linear-gradient(135deg, rgba(21, 87, 36, 0.2) 0%, rgba(32, 201, 151, 0.2) 100%);
            color: #20c997;
            border: 1px solid rgba(32, 201, 151, 0.3);
        }

        .status-cancelled {
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.2) 0%, rgba(253, 126, 20, 0.2) 100%);
            color: #fd7e14;
            border: 1px solid rgba(253, 126, 20, 0.3);
        }

        .payment-status {
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }

        .payment-pending {
            background: rgba(133, 100, 4, 0.2);
            color: #ffc107;
        }

        .payment-processing {
            background: rgba(0, 64, 133, 0.2);
            color: #0dcaf0;
        }

        .payment-paid {
            background: rgba(21, 87, 36, 0.2);
            color: #20c997;
        }

        .payment-failed {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
        }

        .payment-refunded {
            background: rgba(56, 61, 65, 0.2);
            color: #adb5bd;
        }

        .form-section {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .form-section h6 {
            color: var(--primary-color);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
        }

        .required::after {
            content: " *";
            color: var(--danger-color);
        }

        .alert-guide {
            background: rgba(12, 99, 228, 0.1);
            border-right: 4px solid var(--primary-color);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            border: 1px solid rgba(12, 99, 228, 0.2);
        }

        .alert-guide h6 {
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .alert-guide ul {
            margin-bottom: 0;
            padding-right: 20px;
        }

        .alert-guide li {
            margin-bottom: 8px;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.8);
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .items-table th {
            background: rgba(255, 255, 255, 0.05);
            padding: 12px;
            text-align: right;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.8);
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
        }

        .items-table td {
            padding: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            vertical-align: middle;
            color: rgba(255, 255, 255, 0.9);
        }

        .item-row:hover {
            background: rgba(105, 108, 255, 0.1);
        }

        .driver-info {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .driver-detail {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .driver-detail:last-child {
            margin-bottom: 0;
        }

        .driver-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
        }

        .summary-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 20px;
            border: 2px solid rgba(255, 255, 255, 0.1);
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dashed rgba(255, 255, 255, 0.1);
        }

        .summary-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .summary-label {
            font-weight: 600;
            color: rgba(255, 255, 255, 0.8);
        }

        .summary-value {
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
        }

        .total-row {
            font-size: 18px;
            color: #20c997;
        }

        .help-text {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.6);
            margin-top: 5px;
        }

        .info-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            border-right: 4px solid var(--primary-color);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .info-row {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .info-row:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .info-label {
            min-width: 120px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.8);
        }

        .info-value {
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
        }

        .form-control, .form-select {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .form-control:focus, .form-select:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--primary-color);
            color: #fff;
            box-shadow: 0 0 0 0.25rem rgba(105, 108, 255, 0.25);
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .form-label {
            color: rgba(255, 255, 255, 0.9);
        }

        .input-group-text {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.1);
            color: #fff;
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

        .btn-success {
            background: #20c997;
            border: none;
        }

        .btn-success:hover {
            background: #1ba87e;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.3);
        }

        .btn-outline-danger {
            color: #dc3545;
            border-color: #dc3545;
        }

        .btn-outline-danger:hover {
            background: #dc3545;
            color: white;
        }

        .badge-gateway {
            background: rgba(105, 108, 255, 0.2);
            color: var(--primary-color);
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
            border: 1px solid rgba(105, 108, 255, 0.3);
        }

        @media (max-width: 768px) {
            .order-edit-card {
                padding: 20px;
            }

            .items-table {
                display: block;
                overflow-x: auto;
            }

            .info-row {
                flex-direction: column;
                align-items: flex-start;
            }

            .info-label {
                min-width: auto;
                margin-bottom: 5px;
            }

            .summary-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .summary-row .input-group {
                width: 100% !important;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.index') }}">الرئيسية</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.orders.index') }}">الطلبات</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.orders.show', $order) }}">الطلب #{{ $order->id }}</a>
                </li>
                <li class="breadcrumb-item active">تعديل</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-12">
                <div class="order-edit-card">
                    <div class="order-edit-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1">تعديل الطلب #{{ $order->id }}</h5>
                                <div class="d-flex align-items-center gap-3 flex-wrap">
                                    @if($order->status)
                                        <span class="badge-status status-{{ $order->status->name }}">
                                            {{ $order->status->label }}
                                        </span>
                                    @endif
                                    <span class="payment-status payment-{{ $order->payment_status }}">
                                        @switch($order->payment_status)
                                            @case('pending')
                                                <i class="fas fa-clock me-1"></i>قيد الانتظار
                                                @break
                                            @case('processing')
                                                <i class="fas fa-spinner me-1"></i>قيد المعالجة
                                                @break
                                            @case('paid')
                                                <i class="fas fa-check-circle me-1"></i>مدفوع
                                                @break
                                            @case('failed')
                                                <i class="fas fa-times-circle me-1"></i>فشل الدفع
                                                @break
                                            @case('refunded')
                                                <i class="fas fa-undo-alt me-1"></i>مسترد
                                                @break
                                            @default
                                                {{ $order->payment_status }}
                                        @endswitch
                                    </span>
                                </div>
                            </div>
                            <div class="btn-group">
                                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-outline-info">
                                    <i class="fas fa-eye me-2"></i>عرض
                                </a>
                                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-right me-2"></i>رجوع
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- معلومات الطلب -->
                    <div class="info-card">
                        <h6 class="mb-3">معلومات الطلب</h6>

                        <div class="info-row">
                            <div class="info-label">رقم الطلب:</div>
                            <div class="info-value">#{{ $order->id }}</div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">تاريخ الطلب:</div>
                            <div class="info-value">
                                {{ $order->order_date ? $order->order_date->translatedFormat('d M Y - h:i A') : $order->created_at->translatedFormat('d M Y - h:i A') }}
                            </div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">آخر تحديث:</div>
                            <div class="info-value">
                                {{ $order->updated_at->translatedFormat('d M Y - h:i A') }}
                            </div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">الخدمة:</div>
                            <div class="info-value">{{ $order->service->name ?? 'غير محدد' }}</div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">نوع المياه:</div>
                            <div class="info-value">{{ $order->waterType->name ?? 'غير محدد' }}</div>
                        </div>

                        @if($order->expires_at)
                            <div class="info-row">
                                <div class="info-label">ينتهي في:</div>
                                <div class="info-value">
                                    {{ $order->expires_at->translatedFormat('d M Y - h:i A') }}
                                    @if($order->expires_at->isPast())
                                        <span class="badge bg-danger ms-2">منتهي</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="alert-guide">
                        <h6><i class="fas fa-lightbulb me-2"></i>نصائح للتعديل:</h6>
                        <ul>
                            <li>يمكنك تعديل معلومات العميل والعنوان</li>
                            <li>يمكنك تحديث حالة الطلب وحالة الدفع</li>
                            <li>تغيير السعر سيؤثر على الإجمالي النهائي</li>
                            <li>احفظ التغييرات بعد الانتهاء</li>
                        </ul>
                    </div>

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
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

                    <form action="{{ route('admin.orders.update', $order) }}" method="POST" id="editOrderForm">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-lg-8">
                                <!-- معلومات العميل -->
                                <div class="form-section">
                                    <h6><i class="fas fa-user me-2"></i>معلومات العميل</h6>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="user_id" class="form-label required">اختر العميل</label>
                                            <select class="form-select" id="user_id" name="user_id" required>
                                                <option value="">-- اختر عميل --</option>
                                                @foreach($users as $user)
                                                    <option value="{{ $user->id }}" 
                                                        {{ old('user_id', $order->user_id) == $user->id ? 'selected' : '' }}>
                                                        {{ $user->name }} - {{ $user->phone }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="saved_location_id" class="form-label">العنوان المحفوظ</label>
                                            <select class="form-select" id="saved_location_id" name="saved_location_id">
                                                <option value="">-- اختر عنوان --</option>
                                                @if($order->user)
                                                    @foreach($order->user->savedLocations as $location)
                                                        <option value="{{ $location->id }}" 
                                                            {{ old('saved_location_id', $order->saved_location_id) == $location->id ? 'selected' : '' }}>
                                                            {{ $location->label }} - {{ $location->address_details }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- معلومات السائق (للعرض فقط) -->
                                @if($order->driver)
                                <div class="form-section">
                                    <h6><i class="fas fa-truck me-2"></i>معلومات السائق</h6>

                                    <div class="driver-info">
                                        <div class="driver-detail">
                                            <div class="driver-icon">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div>
                                                <strong>{{ $order->driver->user->name ?? 'غير معروف' }}</strong>
                                                @if($order->driver->is_verified)
                                                    <span class="badge bg-success ms-2">موثق</span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        @if($order->driver->vehicle_plate_number)
                                        <div class="driver-detail">
                                            <div class="driver-icon">
                                                <i class="fas fa-car"></i>
                                            </div>
                                            <div>{{ $order->driver->vehicle_plate_number }}</div>
                                        </div>
                                        @endif

                                        @if($order->acceptedOffer)
                                        <div class="driver-detail">
                                            <div class="driver-icon">
                                                <i class="fas fa-money-bill"></i>
                                            </div>
                                            <div>السعر: {{ number_format($order->acceptedOffer->price, 2) }} ر.س</div>
                                        </div>
                                        <div class="driver-detail">
                                            <div class="driver-icon">
                                                <i class="fas fa-clock"></i>
                                            </div>
                                            <div>مدة التوصيل: {{ $order->acceptedOffer->delivery_duration_minutes }} دقيقة</div>
                                        </div>
                                        @endif
                                    </div>

                                    <div class="help-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        لتغيير السائق، قم بإلغاء الطلب وإنشاء طلب جديد أو انتظر انتهاء الطلب الحالي.
                                    </div>
                                </div>
                                @endif

                                <!-- معلومات إضافية -->
                                <div class="form-section">
                                    <h6><i class="fas fa-info-circle me-2"></i>معلومات إضافية</h6>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="service_id" class="form-label required">الخدمة</label>
                                            <select class="form-select" id="service_id" name="service_id" required>
                                                <option value="">-- اختر خدمة --</option>
                                                @foreach($services as $service)
                                                    <option value="{{ $service->id }}" 
                                                        {{ old('service_id', $order->service_id) == $service->id ? 'selected' : '' }}>
                                                        {{ $service->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="water_type_id" class="form-label required">نوع المياه</label>
                                            <select class="form-select" id="water_type_id" name="water_type_id" required>
                                                <option value="">-- اختر نوع المياه --</option>
                                                @foreach($waterTypes as $type)
                                                    <option value="{{ $type->id }}" 
                                                        {{ old('water_type_id', $order->water_type_id) == $type->id ? 'selected' : '' }}>
                                                        {{ $type->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="order_status_id" class="form-label required">حالة الطلب</label>
                                            <select class="form-select" id="order_status_id" name="order_status_id" required>
                                                @foreach($orderStatuses as $status)
                                                    <option value="{{ $status->id }}" 
                                                        {{ old('order_status_id', $order->order_status_id) == $status->id ? 'selected' : '' }}>
                                                        {{ $status->label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="order_date" class="form-label required">تاريخ الطلب</label>
                                            <input type="datetime-local" class="form-control" id="order_date"
                                                name="order_date"
                                                value="{{ old('order_date', $order->order_date ? $order->order_date->format('Y-m-d\TH:i') : $order->created_at->format('Y-m-d\TH:i')) }}" required>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="expires_at" class="form-label">تاريخ الانتهاء</label>
                                            <input type="datetime-local" class="form-control" id="expires_at"
                                                name="expires_at"
                                                value="{{ old('expires_at', $order->expires_at ? $order->expires_at->format('Y-m-d\TH:i') : '') }}">
                                        </div>

                                        <div class="col-12 mb-3">
                                            <label for="notes" class="form-label">ملاحظات</label>
                                            <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes', $order->notes) }}</textarea>
                                            <div class="help-text">ملاحظات إضافية حول الطلب (اختياري)</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <!-- معلومات الدفع -->
                                <div class="summary-card">
                                    <h6 class="mb-3">معلومات الدفع</h6>

                                    <div class="summary-row">
                                        <span class="summary-label">حالة الدفع:</span>
                                        <select class="form-select form-select-sm" name="payment_status" required>
                                            <option value="pending" {{ old('payment_status', $order->payment_status) == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                            <option value="processing" {{ old('payment_status', $order->payment_status) == 'processing' ? 'selected' : '' }}>قيد المعالجة</option>
                                            <option value="paid" {{ old('payment_status', $order->payment_status) == 'paid' ? 'selected' : '' }}>مدفوع</option>
                                            <option value="failed" {{ old('payment_status', $order->payment_status) == 'failed' ? 'selected' : '' }}>فشل الدفع</option>
                                            <option value="refunded" {{ old('payment_status', $order->payment_status) == 'refunded' ? 'selected' : '' }}>مسترد</option>
                                        </select>
                                    </div>

                                    <div class="summary-row">
                                        <span class="summary-label">طريقة الدفع:</span>
                                        <select class="form-select form-select-sm" name="payment_method" required>
                                            <option value="wallet" {{ old('payment_method', $order->payment_method) == 'wallet' ? 'selected' : '' }}>محفظة</option>
                                            <option value="credit_card" {{ old('payment_method', $order->payment_method) == 'credit_card' ? 'selected' : '' }}>بطاقة ائتمان</option>
                                            <option value="mada" {{ old('payment_method', $order->payment_method) == 'mada' ? 'selected' : '' }}>مدى</option>
                                            <option value="apple_pay" {{ old('payment_method', $order->payment_method) == 'apple_pay' ? 'selected' : '' }}>Apple Pay</option>
                                        </select>
                                    </div>

                                    <div class="summary-row">
                                        <span class="summary-label">بوابة الدفع:</span>
                                        <select class="form-select form-select-sm" name="payment_gateway">
                                            <option value="">-- اختر --</option>
                                            <option value="wallet" {{ old('payment_gateway', $order->payment_gateway) == 'wallet' ? 'selected' : '' }}>محفظة</option>
                                            <option value="paymob" {{ old('payment_gateway', $order->payment_gateway) == 'paymob' ? 'selected' : '' }}>Paymob</option>
                                            <option value="tamara" {{ old('payment_gateway', $order->payment_gateway) == 'tamara' ? 'selected' : '' }}>Tamara</option>
                                            <option value="tabby" {{ old('payment_gateway', $order->payment_gateway) == 'tabby' ? 'selected' : '' }}>Tabby</option>
                                        </select>
                                    </div>

                                    <div class="summary-row">
                                        <span class="summary-label">رقم المعاملة:</span>
                                        <input type="text" class="form-control form-control-sm" 
                                            name="payment_transaction_id" 
                                            value="{{ old('payment_transaction_id', $order->payment_transaction_id) }}">
                                    </div>

                                    @if($order->paid_at)
                                    <div class="summary-row">
                                        <span class="summary-label">تاريخ الدفع:</span>
                                        <span class="summary-value">
                                            {{ $order->paid_at->translatedFormat('d M Y - h:i A') }}
                                        </span>
                                    </div>
                                    @endif
                                </div>

                                <!-- السعر -->
                                <div class="summary-card mt-4">
                                    <h6 class="mb-3">تفاصيل السعر</h6>

                                    @if($order->acceptedOffer)
                                    <div class="summary-row">
                                        <span class="summary-label">السعر الحالي:</span>
                                        <span class="summary-value">
                                            {{ number_format($order->acceptedOffer->price, 2) }} ر.س
                                        </span>
                                    </div>
                                    <div class="summary-row">
                                        <span class="summary-label">مدة التوصيل:</span>
                                        <span class="summary-value">
                                            {{ $order->acceptedOffer->delivery_duration_minutes }} دقيقة
                                        </span>
                                    </div>
                                    <div class="help-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        لتغيير السعر، يجب تحديث العرض المقبول أو تعيين سائق جديد.
                                    </div>
                                    @else
                                    <div class="summary-row">
                                        <span class="summary-label">السعر المقترح:</span>
                                        <div class="input-group input-group-sm" style="width: 150px;">
                                            <input type="number" class="form-control" id="price" name="price"
                                                value="{{ old('price', 0) }}" step="0.01" min="0">
                                            <span class="input-group-text">ر.س</span>
                                        </div>
                                    </div>

                                    <div class="summary-row">
                                        <span class="summary-label">مدة التوصيل:</span>
                                        <div class="input-group input-group-sm" style="width: 150px;">
                                            <input type="number" class="form-control" id="delivery_duration" 
                                                name="delivery_duration" value="{{ old('delivery_duration', 30) }}" 
                                                min="1">
                                            <span class="input-group-text">دقيقة</span>
                                        </div>
                                    </div>

                                    <div class="summary-row">
                                        <span class="summary-label">تعيين سائق:</span>
                                        <select class="form-select form-select-sm" name="driver_id">
                                            <option value="">-- اختر سائق --</option>
                                            @foreach($drivers ?? [] as $driver)
                                                <option value="{{ $driver->id }}">
                                                    {{ $driver->user->name ?? 'سائق #' . $driver->id }}
                                                    @if($driver->vehicle_plate_number) - {{ $driver->vehicle_plate_number }} @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @endif
                                </div>

                                <!-- الأزرار -->
                                <div class="mt-4">
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-success btn-lg">
                                            <i class="fas fa-save me-2"></i>حفظ التعديلات
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                                            <i class="fas fa-trash me-2"></i>حذف الطلب
                                        </button>
                                    </div>
                                </div>

                                <!-- معلومات سريعة -->
                                <div class="mt-4">
                                    <div class="alert alert-warning">
                                        <h6><i class="fas fa-exclamation-triangle me-2"></i>تنبيهات مهمة</h6>
                                        <ul class="mt-2 mb-0 pe-3">
                                            <li>تغيير حالة الطلب قد يؤثر على إشعارات العميل والسائق</li>
                                            <li>تغيير حالة الدفع إلى "مدفوع" سيؤدي إلى تسجيل وقت الدفع</li>
                                            <li>لا يمكن تغيير السائق بعد قبول العرض</li>
                                            <li>تأكد من صحة المعلومات قبل الحفظ</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Form -->
    <form id="deleteForm" action="{{ route('admin.orders.destroy', $order) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // تحميل عناوين المستخدم عند تغيير المستخدم
            $('#user_id').on('change', function() {
                const userId = $(this).val();
                if (userId) {
                    $.ajax({
                        url: "{{ route('admin.users.locations', '') }}/" + userId,
                        type: 'GET',
                        success: function(response) {
                            const $locationSelect = $('#saved_location_id');
                            $locationSelect.empty().append('<option value="">-- اختر عنوان --</option>');
                            
                            if (response.locations && response.locations.length > 0) {
                                response.locations.forEach(function(location) {
                                    $locationSelect.append(
                                        `<option value="${location.id}">${location.label} - ${location.address_details}</option>`
                                    );
                                });
                            }
                        }
                    });
                }
            });
        });

        function confirmDelete() {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: 'سيتم حذف الطلب #{{ $order->id }} نهائياً',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('deleteForm').submit();
                }
            });
        }

        // رسائل التنبيه من الجلسة
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
                timer: 2000,
                showConfirmButton: false
            });
        @endif
    </script>
@endsection