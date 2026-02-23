@extends('Admin.layout.master')

@section('title', 'تفاصيل الطلب #' . $order->id)

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

        .order-detail-card {
            background: var(--dark-card);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .order-detail-header {
            background: var(--primary-gradient);
            color: white;
            padding: 25px 30px;
            border-radius: 15px 15px 0 0;
            position: relative;
            margin: -30px -30px 30px -30px;
        }

        .badge-status {
            padding: 8px 20px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 14px;
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
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 13px;
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

        .info-section {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .info-section h6 {
            color: var(--primary-color);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
        }

        .info-row {
            display: flex;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px dashed rgba(255, 255, 255, 0.1);
        }

        .info-row:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .info-label {
            min-width: 150px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.8);
        }

        .info-value {
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
            flex-grow: 1;
        }

        .driver-info {
            display: flex;
            align-items: center;
            gap: 15px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .driver-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }

        .driver-details {
            flex-grow: 1;
        }

        .driver-name {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
            color: rgba(255, 255, 255, 0.9);
        }

        .driver-meta {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.7);
        }

        .driver-meta span i {
            margin-left: 5px;
            color: var(--primary-color);
        }

        .offer-item {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .offer-item:hover {
            background: rgba(105, 108, 255, 0.1);
            border-color: var(--primary-color);
        }

        .offer-item.accepted {
            border: 2px solid #20c997;
            background: rgba(32, 201, 151, 0.1);
        }

        .offer-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .offer-driver {
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
        }

        .offer-price {
            font-size: 16px;
            font-weight: 700;
            color: #20c997;
        }

        .offer-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
            margin-bottom: 10px;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.7);
        }

        .offer-status {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }

        .offer-status.pending {
            background: rgba(133, 100, 4, 0.2);
            color: #ffc107;
        }

        .offer-status.accepted {
            background: rgba(21, 87, 36, 0.2);
            color: #20c997;
        }

        .offer-status.rejected {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
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
            font-weight: 700;
        }

        .timeline {
            position: relative;
            padding-right: 30px;
        }

        .timeline:before {
            content: '';
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: var(--primary-color);
        }

        .timeline-item {
            position: relative;
            margin-bottom: 25px;
        }

        .timeline-item:before {
            content: '';
            position: absolute;
            right: -33px;
            top: 5px;
            width: 13px;
            height: 13px;
            border-radius: 50%;
            background: var(--dark-card);
            border: 3px solid var(--primary-color);
        }

        .timeline-content {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .timeline-date {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 5px;
        }

        .timeline-text {
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
        }

        .timeline-status {
            font-size: 11px;
            color: var(--primary-color);
            margin-top: 5px;
        }

        .action-buttons {
            position: absolute;
            left: 30px;
            top: 30px;
            display: flex;
            gap: 10px;
        }

        .btn-action {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-action:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(105, 108, 255, 0.4);
        }

        .status-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 15px;
        }

        .status-btn {
            padding: 8px 20px;
            border-radius: 25px;
            border: 2px solid transparent;
            background: rgba(255, 255, 255, 0.05);
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .status-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .status-btn.active {
            background: var(--primary-gradient);
            color: white;
            border-color: var(--primary-color);
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

        .btn-danger {
            background: #dc3545;
            border: none;
        }

        .btn-danger:hover {
            background: #c82333;
            transform: translateY(-2px);
        }

        .btn-warning {
            background: #ffc107;
            border: none;
            color: #000;
        }

        .btn-warning:hover {
            background: #e0a800;
        }

        .btn-info {
            background: #0dcaf0;
            border: none;
            color: #000;
        }

        .btn-info:hover {
            background: #0bb5d6;
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            color: white;
        }

        .rating-stars {
            color: #ffc107;
            font-size: 14px;
        }

        .rating-stars .fas.fa-star {
            color: #ffc107;
        }

        .rating-stars .far.fa-star {
            color: rgba(255, 255, 255, 0.3);
        }

        .badge-gateway {
            background: rgba(105, 108, 255, 0.2);
            color: var(--primary-color);
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
            border: 1px solid rgba(105, 108, 255, 0.3);
        }

        .location-info {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background: rgba(255, 255, 255, 0.05);
            padding: 15px;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .location-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .location-details {
            flex-grow: 1;
        }

        .location-label {
            font-size: 14px;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 5px;
        }

        .location-address {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 5px;
        }

        .location-coords {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.5);
        }

        @media (max-width: 768px) {
            .action-buttons {
                position: relative;
                left: 0;
                top: 0;
                margin-bottom: 20px;
                justify-content: center;
            }

            .info-row {
                flex-direction: column;
                align-items: flex-start;
            }

            .info-label {
                min-width: auto;
                margin-bottom: 5px;
            }

            .driver-info {
                flex-direction: column;
                text-align: center;
            }

            .driver-meta {
                justify-content: center;
            }

            .offer-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
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
                <li class="breadcrumb-item active">تفاصيل الطلب #{{ $order->id }}</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-12">
                <div class="order-detail-card">
                    <div class="order-detail-header">
                        <div class="action-buttons">
                            <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn-action" title="تعديل">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ route('admin.orders.print', $order->id) }}" class="btn-action" title="طباعة" target="_blank">
                                <i class="fas fa-print"></i>
                            </a>
                            <a href="{{ route('admin.orders.index') }}" class="btn-action" title="رجوع">
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>

                        <div class="text-center">
                            <h4 class="mb-2">الطلب #{{ $order->id }}</h4>
                            <div class="d-flex justify-content-center align-items-center gap-3 mb-3 flex-wrap">
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
                                <span class="text-white opacity-75">
                                    <i class="far fa-clock me-2"></i>
                                    {{ $order->order_date ? $order->order_date->translatedFormat('d M Y - h:i A') : $order->created_at->translatedFormat('d M Y - h:i A') }}
                                </span>
                            </div>
                            <div class="text-white opacity-75">
                                <i class="fas fa-money-bill-wave me-2"></i>
                                {{ number_format($order->acceptedOffer->price ?? 0, 2) }} ر.س
                            </div>
                        </div>
                    </div>

                    <div class="order-detail-body">
                        <div class="row">
                            <div class="col-lg-8">
                                <!-- معلومات العميل -->
                                <div class="info-section">
                                    <h6><i class="fas fa-user me-2"></i>معلومات العميل</h6>

                                    <div class="info-row">
                                        <div class="info-label">اسم العميل:</div>
                                        <div class="info-value">
                                            <i class="fas fa-user me-2 text-primary"></i>
                                            {{ $order->user->name ?? 'غير محدد' }}
                                        </div>
                                    </div>

                                    @if($order->user)
                                        <div class="info-row">
                                            <div class="info-label">البريد الإلكتروني:</div>
                                            <div class="info-value">
                                                <i class="fas fa-envelope me-2 text-primary"></i>
                                                <a href="mailto:{{ $order->user->email }}" class="text-decoration-none text-white">
                                                    {{ $order->user->email }}
                                                </a>
                                            </div>
                                        </div>

                                        <div class="info-row">
                                            <div class="info-label">رقم الجوال:</div>
                                            <div class="info-value">
                                                <i class="fas fa-phone me-2 text-primary"></i>
                                                <a href="tel:{{ $order->user->full_phone ?? $order->user->phone }}" class="text-decoration-none text-white">
                                                    {{ $order->user->full_phone ?? $order->user->phone }}
                                                </a>
                                            </div>
                                        </div>
                                    @endif

                                    @if($order->user && $order->user->phone_verified_at)
                                        <div class="info-row">
                                            <div class="info-label">حالة الجوال:</div>
                                            <div class="info-value">
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i>موثق
                                                </span>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- معلومات السائق -->
                                <div class="info-section">
                                    <h6><i class="fas fa-truck me-2"></i>معلومات السائق</h6>

                                    @if($order->driver && $order->driver->user)
                                        <div class="driver-info">
                                            <div class="driver-avatar">
                                                {{ substr($order->driver->user->name ?? 'س', 0, 1) }}
                                            </div>
                                            <div class="driver-details">
                                                <div class="driver-name">
                                                    {{ $order->driver->user->name }}
                                                </div>
                                                <div class="driver-meta">
                                                    <span>
                                                        <i class="fas fa-id-card"></i>
                                                        {{ $order->driver->national_id ?? 'رقم الهوية غير متوفر' }}
                                                    </span>
                                                    @if($order->driver->is_verified)
                                                        <span class="text-success">
                                                            <i class="fas fa-check-circle"></i>موثق
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="driver-meta">
                                                    @if($order->driver->vehicle_plate_number)
                                                        <span>
                                                            <i class="fas fa-car"></i>
                                                            {{ $order->driver->vehicle_plate_number }}
                                                        </span>
                                                    @endif
                                                    @if($order->driver->vehicle_size)
                                                        <span>
                                                            <i class="fas fa-tachometer-alt"></i>
                                                            {{ $order->driver->vehicle_size }}
                                                        </span>
                                                    @endif
                                                </div>
                                                @if($order->driver->is_vehicle_owner !== null)
                                                    <div class="driver-meta">
                                                        <span>
                                                            <i class="fas fa-{{ $order->driver->is_vehicle_owner ? 'user' : 'handshake' }}"></i>
                                                            {{ $order->driver->is_vehicle_owner ? 'مالك المركبة' : 'مستأجر' }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        @if($order->driver->user->phone)
                                            <div class="mt-3">
                                                <a href="tel:{{ $order->driver->user->phone }}" class="btn btn-success btn-sm">
                                                    <i class="fas fa-phone me-2"></i>اتصال بالسائق
                                                </a>
                                            </div>
                                        @endif
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-user-slash fa-3x mb-3 opacity-50"></i>
                                            <p class="text-muted">لم يتم تعيين سائق لهذا الطلب</p>
                                            @if($order->status->name == 'pending')
                                                <button class="btn btn-primary btn-sm" onclick="assignDriver({{ $order->id }})">
                                                    <i class="fas fa-user-plus me-2"></i>تعيين سائق
                                                </button>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                <!-- معلومات الخدمة والموقع -->
                                <div class="info-section">
                                    <h6><i class="fas fa-info-circle me-2"></i>تفاصيل الخدمة</h6>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-row">
                                                <div class="info-label">الخدمة:</div>
                                                <div class="info-value">
                                                    <i class="fas fa-cog me-2 text-primary"></i>
                                                    {{ $order->service->name ?? 'غير محدد' }}
                                                </div>
                                            </div>

                                            <div class="info-row">
                                                <div class="info-label">نوع المياه:</div>
                                                <div class="info-value">
                                                    <i class="fas fa-water me-2 text-primary"></i>
                                                    {{ $order->waterType->name ?? 'غير محدد' }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            @if($order->expires_at)
                                                <div class="info-row">
                                                    <div class="info-label">ينتهي في:</div>
                                                    <div class="info-value">
                                                        <i class="far fa-clock me-2 text-warning"></i>
                                                        {{ $order->expires_at->translatedFormat('d M Y - h:i A') }}
                                                        @if($order->expires_at->isPast())
                                                            <span class="badge bg-danger ms-2">منتهي</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    @if($order->location)
                                        <div class="location-info mt-3">
                                            <div class="location-icon">
                                                <i class="fas fa-map-marker-alt"></i>
                                            </div>
                                            <div class="location-details">
                                                <div class="location-label">
                                                    {{ $order->location->label ?? 'عنوان التوصيل' }}
                                                </div>
                                                <div class="location-address">
                                                    {{ $order->location->address_details }}
                                                </div>
                                                @if($order->location->building || $order->location->floor || $order->location->apartment_number)
                                                    <div class="location-address">
                                                        <small>
                                                            @if($order->location->building)مبنى {{ $order->location->building }} @endif
                                                            @if($order->location->floor) - دور {{ $order->location->floor }} @endif
                                                            @if($order->location->apartment_number) - شقة {{ $order->location->apartment_number }} @endif
                                                        </small>
                                                    </div>
                                                @endif
                                                @if($order->location->latitude && $order->location->longitude)
                                                    <div class="location-coords">
                                                        <a href="https://maps.google.com/?q={{ $order->location->latitude }},{{ $order->location->longitude }}" target="_blank">
                                                            <i class="fas fa-external-link-alt me-1"></i>عرض على الخريطة
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    @if($order->notes)
                                        <div class="info-row mt-3">
                                            <div class="info-label">ملاحظات:</div>
                                            <div class="info-value">
                                                <i class="fas fa-sticky-note me-2 text-info"></i>
                                                {{ $order->notes }}
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- العروض المقدمة -->
                                @if($order->offers->isNotEmpty())
                                    <div class="info-section">
                                        <h6><i class="fas fa-tags me-2"></i>العروض المقدمة ({{ $order->offers->count() }})</h6>

                                        @foreach($order->offers as $offer)
                                            <div class="offer-item {{ $offer->status == 'accepted' ? 'accepted' : '' }}">
                                                <div class="offer-header">
                                                    <div class="offer-driver">
                                                        <i class="fas fa-user me-2"></i>
                                                        {{ $offer->driver->user->name ?? 'سائق #' . $offer->driver_id }}
                                                        @if($offer->driver && $offer->driver->vehicle_plate_number)
                                                            <small class="text-muted me-2">({{ $offer->driver->vehicle_plate_number }})</small>
                                                        @endif
                                                    </div>
                                                    <div class="offer-price">
                                                        {{ number_format($offer->price, 2) }} ر.س
                                                    </div>
                                                </div>

                                                <div class="offer-details">
                                                    <span>
                                                        <i class="fas fa-clock me-1"></i>
                                                        مدة التوصيل: {{ $offer->delivery_duration_minutes }} دقيقة
                                                    </span>
                                                    <span>
                                                        <i class="fas fa-calendar me-1"></i>
                                                        {{ $offer->created_at->translatedFormat('d M Y - h:i A') }}
                                                    </span>
                                                    <span>
                                                        <span class="offer-status {{ $offer->status }}">
                                                            @if($offer->status == 'pending')
                                                                <i class="fas fa-clock me-1"></i>قيد الانتظار
                                                            @elseif($offer->status == 'accepted')
                                                                <i class="fas fa-check-circle me-1"></i>مقبول
                                                            @elseif($offer->status == 'rejected')
                                                                <i class="fas fa-times-circle me-1"></i>مرفوض
                                                            @endif
                                                        </span>
                                                    </span>
                                                </div>

                                                @if($offer->status == 'accepted' && !$order->driver_id)
                                                    <div class="mt-2">
                                                        <button class="btn btn-sm btn-success" onclick="acceptOffer({{ $offer->id }})">
                                                            <i class="fas fa-check me-2"></i>قبول العرض
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <!-- تقييمات الطلب -->
                                @if($order->ratings->isNotEmpty())
                                    <div class="info-section">
                                        <h6><i class="fas fa-star me-2"></i>التقييمات</h6>

                                        @foreach($order->ratings as $rating)
                                            <div class="product-item">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <div>
                                                        <strong>{{ $rating->user->name ?? 'مستخدم' }}</strong>
                                                        <small class="text-muted me-2">({{ $rating->rated_by == 'user' ? 'مستخدم' : 'سائق' }})</small>
                                                    </div>
                                                    <div class="rating-stars">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            @if($i <= $rating->rating)
                                                                <i class="fas fa-star"></i>
                                                            @else
                                                                <i class="far fa-star"></i>
                                                            @endif
                                                        @endfor
                                                    </div>
                                                </div>
                                                @if($rating->comment)
                                                    <p class="mb-2"><i class="fas fa-quote-right me-2 text-muted"></i>{{ $rating->comment }}</p>
                                                @endif
                                                @if($rating->aspects && is_array($rating->aspects))
                                                    <div class="mt-2">
                                                        @foreach($rating->aspects as $aspect => $value)
                                                            <span class="badge bg-info me-1">{{ $aspect }}: {{ $value }}/5</span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                                <small class="text-muted">{{ $rating->created_at->diffForHumans() }}</small>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="col-lg-4">
                                <!-- ملخص الطلب -->
                                <div class="summary-card">
                                    <h6 class="mb-3">ملخص الطلب</h6>

                                    <div class="summary-row">
                                        <span class="summary-label">المجموع الجزئي:</span>
                                        <span class="summary-value">
                                            {{ number_format($order->acceptedOffer->price ?? 0, 2) }} ر.س
                                        </span>
                                    </div>

                                    <div class="summary-row">
                                        <span class="summary-label">طريقة الدفع:</span>
                                        <span class="summary-value">
                                            @switch($order->payment_method)
                                                @case('wallet')
                                                    <i class="fas fa-wallet me-1"></i>محفظة
                                                    @break
                                                @case('credit_card')
                                                    <i class="fas fa-credit-card me-1"></i>بطاقة ائتمان
                                                    @break
                                                @case('mada')
                                                    <i class="fas fa-credit-card me-1"></i>مدى
                                                    @break
                                                @case('apple_pay')
                                                    <i class="fab fa-apple-pay me-1"></i>Apple Pay
                                                    @break
                                                @default
                                                    {{ $order->payment_method }}
                                            @endswitch
                                        </span>
                                    </div>

                                    @if($order->payment_gateway)
                                        <div class="summary-row">
                                            <span class="summary-label">بوابة الدفع:</span>
                                            <span class="summary-value">
                                                <span class="badge-gateway">
                                                    @switch($order->payment_gateway)
                                                        @case('wallet')
                                                            محفظة
                                                            @break
                                                        @case('paymob')
                                                            Paymob
                                                            @break
                                                        @case('tamara')
                                                            Tamara
                                                            @break
                                                        @case('tabby')
                                                            Tabby
                                                            @break
                                                        @default
                                                            {{ $order->payment_gateway }}
                                                    @endswitch
                                                </span>
                                            </span>
                                        </div>
                                    @endif

                                    @if($order->payment_transaction_id)
                                        <div class="summary-row">
                                            <span class="summary-label">رقم المعاملة:</span>
                                            <span class="summary-value">
                                                <small>{{ $order->payment_transaction_id }}</small>
                                            </span>
                                        </div>
                                    @endif

                                    @if($order->paid_at)
                                        <div class="summary-row">
                                            <span class="summary-label">تاريخ الدفع:</span>
                                            <span class="summary-value">
                                                {{ $order->paid_at->translatedFormat('d M Y - h:i A') }}
                                            </span>
                                        </div>
                                    @endif

                                    @if($order->acceptedOffer)
                                        <div class="summary-row total-row">
                                            <span class="summary-label">إجمالي السعر:</span>
                                            <span class="summary-value">
                                                {{ number_format($order->acceptedOffer->price, 2) }} ر.س
                                            </span>
                                        </div>

                                        <div class="summary-row">
                                            <span class="summary-label">مدة التوصيل:</span>
                                            <span class="summary-value">
                                                <i class="fas fa-clock me-1"></i>
                                                {{ $order->acceptedOffer->delivery_duration_minutes }} دقيقة
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                <!-- تغيير حالة الطلب -->
                                <div class="info-section mt-4">
                                    <h6><i class="fas fa-exchange-alt me-2"></i>تغيير الحالة</h6>

                                    <div class="status-buttons" id="statusButtons">
                                        @foreach($orderStatuses ?? [] as $status)
                                            <button type="button"
                                                class="status-btn {{ $order->status && $order->status->id == $status->id ? 'active' : '' }}"
                                                onclick="updateStatus({{ $status->id }}, '{{ $status->name }}')">
                                                @if($status->name == 'pending')
                                                    <i class="fas fa-clock me-1"></i>
                                                @elseif($status->name == 'in-road')
                                                    <i class="fas fa-truck me-1"></i>
                                                @elseif($status->name == 'scheduled')
                                                    <i class="fas fa-calendar-check me-1"></i>
                                                @elseif($status->name == 'delivered')
                                                    <i class="fas fa-check-circle me-1"></i>
                                                @elseif($status->name == 'cancelled')
                                                    <i class="fas fa-times-circle me-1"></i>
                                                @endif
                                                {{ $status->label }}
                                            </button>
                                        @endforeach
                                    </div>

                                    <div class="mt-3">
                                        <textarea class="form-control" id="statusNotes" placeholder="ملاحظات إضافية (اختياري)" rows="3"></textarea>
                                    </div>

                                    <button type="button" class="btn btn-primary w-100 mt-3" onclick="confirmStatusUpdate()">
                                        <i class="fas fa-save me-2"></i>تحديث الحالة
                                    </button>
                                </div>

                                <!-- الجدول الزمني -->
                                <div class="info-section mt-4">
                                    <h6><i class="fas fa-history me-2"></i>سجل الطلب</h6>

                                    <div class="timeline">
                                        @if($order->completionLog)
                                            <div class="timeline-item">
                                                <div class="timeline-content">
                                                    <div class="timeline-date">
                                                        {{ $order->completionLog->completed_at->translatedFormat('d M Y - h:i A') }}
                                                    </div>
                                                    <div class="timeline-text">
                                                        <i class="fas fa-check-circle text-success me-2"></i>
                                                        تم اكتمال الطلب
                                                    </div>
                                                    <div class="timeline-status">
                                                        <small>
                                                            المدة: {{ $order->completionLog->delivery_duration_minutes }} دقيقة |
                                                            المسافة: {{ number_format($order->completionLog->total_distance_km, 2) }} كم
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if($order->paid_at)
                                            <div class="timeline-item">
                                                <div class="timeline-content">
                                                    <div class="timeline-date">
                                                        {{ $order->paid_at->translatedFormat('d M Y - h:i A') }}
                                                    </div>
                                                    <div class="timeline-text">
                                                        <i class="fas fa-credit-card text-success me-2"></i>
                                                        تم الدفع
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if($order->acceptedOffer)
                                            <div class="timeline-item">
                                                <div class="timeline-content">
                                                    <div class="timeline-date">
                                                        {{ $order->acceptedOffer->created_at->translatedFormat('d M Y - h:i A') }}
                                                    </div>
                                                    <div class="timeline-text">
                                                        <i class="fas fa-check-circle text-success me-2"></i>
                                                        تم قبول العرض من {{ $order->acceptedOffer->driver->user->name ?? 'سائق' }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if($order->offers->isNotEmpty())
                                            <div class="timeline-item">
                                                <div class="timeline-content">
                                                    <div class="timeline-date">
                                                        {{ $order->offers->first()->created_at->translatedFormat('d M Y - h:i A') }}
                                                    </div>
                                                    <div class="timeline-text">
                                                        <i class="fas fa-tag text-info me-2"></i>
                                                        تم استلام أول عرض
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if($order->order_date)
                                            <div class="timeline-item">
                                                <div class="timeline-content">
                                                    <div class="timeline-date">
                                                        {{ $order->order_date->translatedFormat('d M Y - h:i A') }}
                                                    </div>
                                                    <div class="timeline-text">
                                                        <i class="fas fa-plus-circle text-primary me-2"></i>
                                                        إنشاء الطلب
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- آخر مواقع السائق -->
                                @if($order->driverLocations && $order->driverLocations->isNotEmpty())
                                    <div class="info-section mt-4">
                                        <h6><i class="fas fa-map-marked-alt me-2"></i>آخر مواقع السائق</h6>

                                        @foreach($order->driverLocations->take(5) as $location)
                                            <div class="product-item">
                                                <div class="d-flex justify-content-between">
                                                    <div>
                                                        <i class="fas fa-map-pin text-danger me-2"></i>
                                                        {{ $location->created_at->translatedFormat('h:i A') }}
                                                    </div>
                                                    @if($location->address)
                                                        <small class="text-muted">{{ Str::limit($location->address, 30) }}</small>
                                                    @endif
                                                </div>
                                                <div class="mt-2 small text-muted">
                                                    <i class="fas fa-crosshairs me-1"></i>
                                                    {{ number_format($location->latitude, 6) }}, {{ number_format($location->longitude, 6) }}
                                                    @if($location->speed)
                                                        | <i class="fas fa-tachometer-alt me-1"></i>{{ $location->speed }} كم/س
                                                    @endif
                                                    @if($location->battery_level)
                                                        | <i class="fas fa-battery-{{ $location->battery_level > 75 ? 'full' : ($location->battery_level > 25 ? 'half' : 'quarter') }} me-1"></i>
                                                        {{ $location->battery_level }}%
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach

                                        @if($order->latestDriverLocation)
                                            <div class="mt-2">
                                                <a href="https://maps.google.com/?q={{ $order->latestDriverLocation->latitude }},{{ $order->latestDriverLocation->longitude }}" 
                                                   class="btn btn-sm btn-outline-info w-100" target="_blank">
                                                    <i class="fas fa-map-marked-alt me-2"></i>عرض الموقع الحالي
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                <!-- إجراءات سريعة -->
                                <div class="info-section mt-4">
                                    <h6><i class="fas fa-bolt me-2"></i>إجراءات سريعة</h6>

                                    <div class="d-grid gap-2">
                                        <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-warning">
                                            <i class="fas fa-edit me-2"></i>تعديل الطلب
                                        </a>

                                        <a href="{{ route('admin.orders.print', $order) }}" class="btn btn-secondary" target="_blank">
                                            <i class="fas fa-print me-2"></i>طباعة الفاتورة
                                        </a>

                                        @if($order->driver)
                                            <a href="{{ route('admin.orders.tracking', $order) }}" class="btn btn-info">
                                                <i class="fas fa-map-marked-alt me-2"></i>تتبع الطلب
                                            </a>
                                        @endif

                                        @if($order->payment_status != 'paid' && $order->payment_status != 'refunded')
                                            <button class="btn btn-success" onclick="markAsPaid({{ $order->id }})">
                                                <i class="fas fa-check-circle me-2"></i>تأكيد الدفع
                                            </button>
                                        @endif

                                        @if($order->payment_status == 'paid' && $order->status->name != 'delivered' && $order->status->name != 'cancelled')
                                            <button class="btn btn-danger" onclick="cancelOrder({{ $order->id }})">
                                                <i class="fas fa-times-circle me-2"></i>إلغاء الطلب
                                            </button>
                                        @endif

                                        <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" id="deleteForm" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-danger w-100" onclick="confirmDelete()">
                                                <i class="fas fa-trash me-2"></i>حذف الطلب
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for assigning driver -->
    <div class="modal fade" id="assignDriverModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title">تعيين سائق للطلب #{{ $order->id }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="assignDriverForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">اختر السائق</label>
                            <select class="form-select" id="driverSelect" required>
                                <option value="">-- اختر سائق --</option>
                                @foreach($availableDrivers ?? [] as $driver)
                                    <option value="{{ $driver->id }}">
                                        {{ $driver->user->name ?? 'سائق #' . $driver->id }} 
                                        @if($driver->vehicle_plate_number) - {{ $driver->vehicle_plate_number }} @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">السعر (ر.س)</label>
                            <input type="number" class="form-control" id="offerPrice" step="0.01" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">مدة التوصيل (بالدقائق)</label>
                            <input type="number" class="form-control" id="deliveryDuration" min="1" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-primary" onclick="submitAssignDriver()">تعيين</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for cancellation reason -->
    <div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title">إلغاء الطلب #{{ $order->id }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="cancelOrderForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">سبب الإلغاء</label>
                            <select class="form-select" id="cancelReason" required>
                                <option value="">-- اختر السبب --</option>
                                <option value="customer_request">طلب العميل</option>
                                <option value="driver_unavailable">السائق غير متاح</option>
                                <option value="payment_issue">مشكلة في الدفع</option>
                                <option value="technical_issue">مشكلة تقنية</option>
                                <option value="other">سبب آخر</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ملاحظات إضافية</label>
                            <textarea class="form-control" id="cancelNotes" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-danger" onclick="submitCancelOrder()">تأكيد الإلغاء</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let selectedStatus = {{ $order->order_status_id }};
        let selectedStatusName = '{{ $order->status->name ?? '' }}';

        function updateStatus(statusId, statusName) {
            selectedStatus = statusId;
            selectedStatusName = statusName;

            // تحديث أزرار الحالة
            $('#statusButtons .status-btn').removeClass('active');
            $(`#statusButtons .status-btn[onclick="updateStatus(${statusId}, '${statusName}')"]`).addClass('active');
        }

        function confirmStatusUpdate() {
            if (selectedStatus === {{ $order->order_status_id }}) {
                Swal.fire({
                    icon: 'info',
                    title: 'لم يتغير شيء',
                    text: 'الحالة الحالية هي نفس الحالة المحددة',
                    timer: 1500,
                    showConfirmButton: false
                });
                return;
            }

            const notes = $('#statusNotes').val();

            Swal.fire({
                title: 'تأكيد تحديث الحالة',
                text: 'هل أنت متأكد من تغيير حالة الطلب؟',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'نعم، تحديث',
                cancelButtonText: 'إلغاء',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.orders.update-status', $order) }}",
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            order_status_id: selectedStatus,
                            notes: notes
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'تم التحديث',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ',
                                text: xhr.responseJSON?.message || 'حدث خطأ أثناء تحديث الحالة',
                            });
                        }
                    });
                }
            });
        }

        function assignDriver(orderId) {
            const modal = new bootstrap.Modal(document.getElementById('assignDriverModal'));
            modal.show();
        }

        function submitAssignDriver() {
            const driverId = $('#driverSelect').val();
            const price = $('#offerPrice').val();
            const duration = $('#deliveryDuration').val();

            if (!driverId) {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: 'يرجى اختيار سائق'
                });
                return;
            }

            if (!price || price <= 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: 'يرجى إدخال سعر صحيح'
                });
                return;
            }

            if (!duration || duration <= 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: 'يرجى إدخال مدة توصيل صحيحة'
                });
                return;
            }

            $.ajax({
                url: "{{ route('admin.orders.assign-driver', $order) }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    driver_id: driverId,
                    price: price,
                    delivery_duration_minutes: duration
                },
                success: function(response) {
                    if (response.success) {
                        bootstrap.Modal.getInstance(document.getElementById('assignDriverModal')).hide();
                        Swal.fire({
                            icon: 'success',
                            title: 'تم التعيين',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: xhr.responseJSON?.message || 'حدث خطأ أثناء تعيين السائق',
                    });
                }
            });
        }

        function acceptOffer(offerId) {
            Swal.fire({
                title: 'تأكيد قبول العرض',
                text: 'هل أنت متأكد من قبول هذا العرض؟',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'نعم، قبول',
                cancelButtonText: 'إلغاء',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.orders.accept-offer', '') }}/" + offerId,
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'تم القبول',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ',
                                text: xhr.responseJSON?.message || 'حدث خطأ',
                            });
                        }
                    });
                }
            });
        }

        function markAsPaid(orderId) {
            Swal.fire({
                title: 'تأكيد الدفع',
                text: 'هل أنت متأكد من تأكيد دفع الطلب؟',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'نعم، تأكيد',
                cancelButtonText: 'إلغاء',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.orders.update-payment-status', $order) }}",
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            payment_status: 'paid'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'تم التأكيد',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ',
                                text: xhr.responseJSON?.message || 'حدث خطأ',
                            });
                        }
                    });
                }
            });
        }

        function cancelOrder(orderId) {
            const modal = new bootstrap.Modal(document.getElementById('cancelOrderModal'));
            modal.show();
        }

        function submitCancelOrder() {
            const reason = $('#cancelReason').val();
            const notes = $('#cancelNotes').val();

            if (!reason) {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: 'يرجى اختيار سبب الإلغاء'
                });
                return;
            }

            $.ajax({
                url: "{{ route('admin.orders.cancel', $order) }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    reason: reason,
                    notes: notes
                },
                success: function(response) {
                    if (response.success) {
                        bootstrap.Modal.getInstance(document.getElementById('cancelOrderModal')).hide();
                        Swal.fire({
                            icon: 'success',
                            title: 'تم الإلغاء',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: xhr.responseJSON?.message || 'حدث خطأ أثناء إلغاء الطلب',
                    });
                }
            });
        }

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