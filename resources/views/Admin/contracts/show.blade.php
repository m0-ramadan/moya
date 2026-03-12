@extends('Admin.layout.master')

@section('title', 'تفاصيل العقد - ' . $contract->contract_number)

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
            --dark-bg: #1e1e2d;
            --dark-card: #2b3b4c;
        }

        body {
            font-family: "Cairo", sans-serif !important;
            background: var(--dark-bg);
            color: #fff;
        }

        .contract-detail-card {
            background: var(--dark-card);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 25px;
            overflow: hidden;
        }

        .card-header {
            background: rgba(255, 255, 255, 0.05);
            padding: 20px 25px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            font-weight: 600;
            color: #fff;
        }

        .card-header i {
            color: var(--primary-color);
            margin-left: 10px;
        }

        .card-body {
            padding: 25px;
        }

        .contract-number-badge {
            background: var(--primary-gradient);
            color: white;
            padding: 10px 20px;
            border-radius: 10px;
            display: inline-block;
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 1px;
            direction: ltr;
        }

        .badge-status {
            padding: 8px 20px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .status-active {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }

        .status-expired {
            background: rgba(108, 117, 125, 0.2);
            color: #6c757d;
            border: 1px solid rgba(108, 117, 125, 0.3);
        }

        .status-pending {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }

        .status-cancelled {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
            border: 1px solid rgba(220, 53, 69, 0.3);
        }

        .info-row {
            display: flex;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            min-width: 150px;
            color: rgba(255, 255, 255, 0.7);
            font-weight: 500;
        }

        .info-label i {
            width: 25px;
            color: var(--primary-color);
        }

        .info-value {
            color: #fff;
            font-weight: 500;
        }

        .amount-box {
            background: rgba(105, 108, 255, 0.1);
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            border: 1px solid rgba(105, 108, 255, 0.3);
        }

        .amount-title {
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
            margin-bottom: 5px;
        }

        .amount-number {
            font-size: 28px;
            font-weight: 700;
            color: #fff;
        }

        .amount-number.small {
            font-size: 20px;
        }

        .progress {
            height: 10px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
            overflow: hidden;
            margin: 10px 0;
        }

        .progress-bar {
            background: var(--primary-gradient);
        }

        .progress-bar.success {
            background: linear-gradient(135deg, #28a745, #20c997);
        }

        .progress-bar.warning {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
        }

        .location-item {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .location-item:hover {
            background: rgba(105, 108, 255, 0.1);
            border-color: var(--primary-color);
        }

        .location-priority {
            display: inline-block;
            width: 30px;
            height: 30px;
            background: var(--primary-gradient);
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 30px;
            font-weight: 700;
            margin-left: 10px;
        }

        .payment-item {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .payment-status {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .payment-completed {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
        }

        .payment-pending {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
        }

        .payment-failed {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
        }

        .order-item {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .order-status {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .order-new {
            background: rgba(13, 202, 240, 0.2);
            color: #0dcaf0;
        }

        .order-processing {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
        }

        .order-completed {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
        }

        .order-cancelled {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
        }

        .timeline {
            position: relative;
            padding: 20px 0;
        }

        .timeline-item {
            position: relative;
            padding-right: 30px;
            margin-bottom: 25px;
        }

        .timeline-item:before {
            content: '';
            position: absolute;
            right: 0;
            top: 0;
            width: 2px;
            height: 100%;
            background: rgba(105, 108, 255, 0.3);
        }

        .timeline-item:last-child:before {
            display: none;
        }

        .timeline-dot {
            position: absolute;
            right: -6px;
            top: 0;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: var(--primary-gradient);
            border: 2px solid var(--dark-card);
        }

        .timeline-date {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.5);
            margin-bottom: 5px;
        }

        .timeline-content {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 15px;
        }

        .btn-action {
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .btn-primary {
            background: var(--primary-gradient);
            border: none;
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
        }

        .btn-warning {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
            border: none;
            color: #000;
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545, #c82333);
            border: none;
        }

        .btn-info {
            background: linear-gradient(135deg, #17a2b8, #0dcaf0);
            border: none;
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6c757d, #5a6268);
            border: none;
        }

        .user-info-card {
            display: flex;
            align-items: center;
            gap: 20px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .user-avatar-lg {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: 700;
            color: white;
        }

        .user-details {
            flex: 1;
        }

        .user-name {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .user-contact {
            display: flex;
            gap: 20px;
            color: rgba(255, 255, 255, 0.7);
        }

        .user-contact i {
            width: 20px;
            color: var(--primary-color);
        }

        .document-preview {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            border: 2px dashed rgba(105, 108, 255, 0.3);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .document-preview:hover {
            background: rgba(105, 108, 255, 0.1);
            border-color: var(--primary-color);
        }

        .document-preview i {
            font-size: 48px;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .document-preview span {
            display: block;
            color: rgba(255, 255, 255, 0.7);
        }

        .stat-box {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .stat-number {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-color);
        }

        .stat-label {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.7);
        }

        .nav-tabs {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .nav-tabs .nav-link {
            color: rgba(255, 255, 255, 0.7);
            border: none;
            padding: 12px 20px;
            font-weight: 600;
        }

        .nav-tabs .nav-link i {
            margin-left: 8px;
        }

        .nav-tabs .nav-link:hover {
            color: #fff;
            border: none;
        }

        .nav-tabs .nav-link.active {
            color: var(--primary-color);
            background: transparent;
            border-bottom: 2px solid var(--primary-color);
        }

        .tab-content {
            padding: 25px 0;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
        }

        .empty-state-icon {
            font-size: 60px;
            color: rgba(255, 255, 255, 0.1);
            margin-bottom: 15px;
        }

        .empty-state-text {
            color: rgba(255, 255, 255, 0.7);
        }

        @media (max-width: 768px) {
            .info-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }
            
            .user-info-card {
                flex-direction: column;
                text-align: center;
            }
            
            .user-contact {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- مسار التنقل -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.index') }}">الرئيسية</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.contracts.index') }}">العقود</a>
                </li>
                <li class="breadcrumb-item active">تفاصيل العقد {{ $contract->contract_number }}</li>
            </ol>
        </nav>

        <!-- رأس الصفحة مع الإجراءات -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">
                    <span class="contract-number-badge">
                        <i class="fas fa-file-contract me-2"></i>
                        {{ $contract->contract_number }}
                    </span>
                </h4>
                <p class="text-muted mb-0">
                    <i class="fas fa-calendar-alt me-2"></i>
                    تاريخ الإنشاء: {{ $contract->created_at->format('Y-m-d') }} | 
                    <i class="fas fa-clock me-2 ms-2"></i>
                    آخر تحديث: {{ $contract->updated_at->diffForHumans() }}
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.contracts.index') }}" class="btn btn-secondary btn-action">
                    <i class="fas fa-arrow-right me-2"></i>
                    عودة للقائمة
                </a>
                <a href="{{ route('admin.contracts.edit', $contract->id) }}" class="btn btn-warning btn-action">
                    <i class="fas fa-edit me-2"></i>
                    تعديل
                </a>
                <button type="button" class="btn btn-danger btn-action delete-btn" data-id="{{ $contract->id }}">
                    <i class="fas fa-trash me-2"></i>
                    حذف
                </button>
            </div>
        </div>

        <!-- شريط التبويبات -->
        <ul class="nav nav-tabs mb-4" id="contractTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button">
                    <i class="fas fa-info-circle"></i>
                    المعلومات الأساسية
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="locations-tab" data-bs-toggle="tab" data-bs-target="#locations" type="button">
                    <i class="fas fa-map-marker-alt"></i>
                    مواقع التوصيل
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments" type="button">
                    <i class="fas fa-money-bill-wave"></i>
                    المدفوعات
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button">
                    <i class="fas fa-shopping-cart"></i>
                    الطلبات
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="timeline-tab" data-bs-toggle="tab" data-bs-target="#timeline" type="button">
                    <i class="fas fa-history"></i>
                    السجل الزمني
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button">
                    <i class="fas fa-file-pdf"></i>
                    المستندات
                </button>
            </li>
        </ul>

        <!-- محتوى التبويبات -->
        <div class="tab-content" id="contractTabsContent">
            <!-- تبويب المعلومات الأساسية -->
            <div class="tab-pane fade show active" id="info" role="tabpanel">
                <div class="row">
                    <!-- العمود الأيمن - معلومات العقد -->
                    <div class="col-lg-8">
                        <!-- معلومات العميل -->
                        <div class="contract-detail-card">
                            <div class="card-header">
                                <i class="fas fa-user"></i>
                                معلومات العميل
                            </div>
                            <div class="card-body">
                                <div class="user-info-card">
                                    <div class="user-avatar-lg">
                                        {{ substr($contract->user->name ?? '?', 0, 1) }}
                                    </div>
                                    <div class="user-details">
                                        <div class="user-name">{{ $contract->user->name ?? 'عميل محذوف' }}</div>
                                        <div class="user-contact">
                                            <span>
                                                <i class="fas fa-phone"></i>
                                                {{ $contract->user->phone ?? 'لا يوجد' }}
                                            </span>
                                            <span>
                                                <i class="fas fa-envelope"></i>
                                                {{ $contract->user->email ?? 'لا يوجد' }}
                                            </span>
                                        </div>
                                        @if($contract->user)
                                            <div class="mt-2">
                                                <a href="{{ route('admin.users.show', $contract->user_id) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-external-link-alt me-1"></i>
                                                    عرض ملف العميل
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- تفاصيل العقد -->
                        <div class="contract-detail-card">
                            <div class="card-header">
                                <i class="fas fa-file-contract"></i>
                                تفاصيل العقد
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-row">
                                            <span class="info-label">
                                                <i class="fas fa-tag"></i>
                                                رقم العقد:
                                            </span>
                                            <span class="info-value">{{ $contract->contract_number }}</span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label">
                                                <i class="fas fa-user-tie"></i>
                                                نوع العقد:
                                            </span>
                                            <span class="info-value">
                                                @if($contract->contract_type == 'individual')
                                                    <span class="badge-status" style="background: rgba(23, 162, 184, 0.2); color: #17a2b8;">
                                                        <i class="fas fa-user me-1"></i>فردي
                                                    </span>
                                                @else
                                                    <span class="badge-status" style="background: rgba(111, 66, 193, 0.2); color: #6f42c1;">
                                                        <i class="fas fa-building me-1"></i>شركة
                                                        @if($contract->company_name)
                                                            <span class="me-2">({{ $contract->company_name }})</span>
                                                        @endif
                                                    </span>
                                                @endif
                                            </span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label">
                                                <i class="fas fa-user"></i>
                                                مقدم الطلب:
                                            </span>
                                            <span class="info-value">{{ $contract->applicant_name ?? '--' }}</span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label">
                                                <i class="fas fa-phone"></i>
                                                رقم الجوال:
                                            </span>
                                            <span class="info-value">{{ $contract->phone ?? '--' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-row">
                                            <span class="info-label">
                                                <i class="fas fa-calendar-plus"></i>
                                                تاريخ البداية:
                                            </span>
                                            <span class="info-value">{{ $contract->start_date?->format('Y-m-d') ?? '--' }}</span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label">
                                                <i class="fas fa-calendar-times"></i>
                                                تاريخ النهاية:
                                            </span>
                                            <span class="info-value">
                                                {{ $contract->end_date?->format('Y-m-d') ?? '--' }}
                                                @if($contract->end_date)
                                                    @php
                                                        $daysLeft = now()->diffInDays($contract->end_date, false);
                                                    @endphp
                                                    @if($contract->status == 'active' && $daysLeft > 0)
                                                        <span class="badge bg-{{ $daysLeft <= 7 ? 'danger' : 'success' }} me-2">
                                                            متبقي {{ $daysLeft }} يوم
                                                        </span>
                                                    @endif
                                                @endif
                                            </span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label">
                                                <i class="fas fa-sync-alt"></i>
                                                تاريخ التجديد:
                                            </span>
                                            <span class="info-value">{{ $contract->renewal_date?->format('Y-m-d') ?? '--' }}</span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label">
                                                <i class="fas fa-clock"></i>
                                                مدة العقد:
                                            </span>
                                            <span class="info-value">
                                                @switch($contract->duration_type)
                                                    @case('monthly')
                                                        شهري
                                                        @break
                                                    @case('quarterly')
                                                        ربع سنوي
                                                        @break
                                                    @case('semi_annual')
                                                        نصف سنوي
                                                        @break
                                                    @case('annual')
                                                        سنوي
                                                        @break
                                                    @default
                                                        --
                                                @endswitch
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                @if($contract->notes)
                                    <div class="mt-3 p-3" style="background: rgba(255, 255, 255, 0.05); border-radius: 10px;">
                                        <strong><i class="fas fa-sticky-note me-2"></i>ملاحظات:</strong>
                                        <p class="mt-2 mb-0">{{ $contract->notes }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- العمود الأيسر - الحالة والمبالغ -->
                    <div class="col-lg-4">
                        <!-- حالة العقد -->
                        <div class="contract-detail-card">
                            <div class="card-header">
                                <i class="fas fa-info-circle"></i>
                                حالة العقد
                            </div>
                            <div class="card-body text-center">
                                <span class="badge-status status-{{ $contract->status }} mb-3">
                                    @switch($contract->status)
                                        @case('active')
                                            <i class="fas fa-check-circle"></i>
                                            @break
                                        @case('expired')
                                            <i class="fas fa-clock"></i>
                                            @break
                                        @case('pending')
                                            <i class="fas fa-hourglass-half"></i>
                                            @break
                                        @case('cancelled')
                                            <i class="fas fa-ban"></i>
                                            @break
                                    @endswitch
                                    @lang("contracts.status.{$contract->status}")
                                </span>

                                <div class="mt-3">
                                    <button type="button" class="btn btn-{{ $contract->status == 'active' ? 'warning' : 'success' }} w-100 toggle-status-btn">
                                        <i class="fas fa-power-off me-2"></i>
                                        {{ $contract->status == 'active' ? 'تعطيل العقد' : 'تفعيل العقد' }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- المبالغ المالية -->
                        <div class="contract-detail-card">
                            <div class="card-header">
                                <i class="fas fa-money-bill-wave"></i>
                                المبالغ المالية
                            </div>
                            <div class="card-body">
                                <div class="amount-box mb-3">
                                    <div class="amount-title">إجمالي العقد</div>
                                    <div class="amount-number">{{ number_format($contract->total_amount, 2) }} ر.س</div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-6">
                                        <div class="stat-box">
                                            <div class="stat-number text-success">{{ number_format($contract->paid_amount, 2) }}</div>
                                            <div class="stat-label">مدفوع</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="stat-box">
                                            <div class="stat-number text-danger">{{ number_format($contract->remaining_amount, 2) }}</div>
                                            <div class="stat-label">متبقي</div>
                                        </div>
                                    </div>
                                </div>

                                @if($contract->total_amount > 0)
                                    @php $paidPercentage = ($contract->paid_amount / $contract->total_amount) * 100; @endphp
                                    <div class="mt-3">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>نسبة الدفع</span>
                                            <span>{{ number_format($paidPercentage, 1) }}%</span>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-bar success" style="width: {{ $paidPercentage }}%"></div>
                                        </div>
                                    </div>
                                @endif

                                <div class="mt-3">
                                    <a href="{{ route('admin.contracts.payments', $contract->id) }}" class="btn btn-primary w-100">
                                        <i class="fas fa-credit-card me-2"></i>
                                        إدارة المدفوعات
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- حدود الطلبات -->
                        <div class="contract-detail-card">
                            <div class="card-header">
                                <i class="fas fa-chart-line"></i>
                                حدود الطلبات
                            </div>
                            <div class="card-body">
                                <div class="row g-3 mb-3">
                                    <div class="col-6">
                                        <div class="stat-box">
                                            <div class="stat-number">{{ $contract->total_orders_limit ?? '∞' }}</div>
                                            <div class="stat-label">الحد الأقصى</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="stat-box">
                                            <div class="stat-number">{{ $contract->remaining_orders ?? '∞' }}</div>
                                            <div class="stat-label">المتبقي</div>
                                        </div>
                                    </div>
                                </div>

                                @if($contract->total_orders_limit > 0)
                                    @php $ordersUsed = $contract->total_orders_limit - $contract->remaining_orders; @endphp
                                    <div class="mt-2">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>الطلبات المستخدمة</span>
                                            <span>{{ $ordersUsed }}/{{ $contract->total_orders_limit }}</span>
                                        </div>
                                        <div class="progress">
                                            @php $ordersPercentage = ($ordersUsed / $contract->total_orders_limit) * 100; @endphp
                                            <div class="progress-bar {{ $ordersPercentage > 80 ? 'warning' : 'success' }}" 
                                                 style="width: {{ $ordersPercentage }}%"></div>
                                        </div>
                                    </div>
                                @endif

                                <div class="mt-3">
                                    <a href="{{ route('admin.contracts.orders', $contract->id) }}" class="btn btn-info w-100">
                                        <i class="fas fa-shopping-cart me-2"></i>
                                        عرض الطلبات
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- تبويب مواقع التوصيل -->
            <div class="tab-pane fade" id="locations" role="tabpanel">
                <div class="contract-detail-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-map-marker-alt"></i>
                            مواقع التوصيل
                        </div>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addLocationModal">
                            <i class="fas fa-plus me-1"></i>
                            إضافة موقع
                        </button>
                    </div>
                    <div class="card-body">
                        @if($contract->deliveryLocations->isEmpty())
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="empty-state-text">
                                    لا توجد مواقع توصيل لهذا العقد
                                </div>
                                <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addLocationModal">
                                    <i class="fas fa-plus me-2"></i>
                                    إضافة موقع جديد
                                </button>
                            </div>
                        @else
                            @foreach($contract->deliveryLocations->sortBy('priority') as $index => $location)
                                <div class="location-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="d-flex align-items-center">
                                            <span class="location-priority">{{ $location->priority }}</span>
                                            <div>
                                                <h6 class="mb-1">{{ $location->savedLocation->name ?? 'موقع غير محدد' }}</h6>
                                                <p class="mb-1 text-muted small">
                                                    <i class="fas fa-map-pin me-1"></i>
                                                    {{ $location->savedLocation->full_address ?? '--' }}
                                                </p>
                                                @if($location->notes)
                                                    <p class="mb-0 text-muted small">
                                                        <i class="fas fa-sticky-note me-1"></i>
                                                        {{ $location->notes }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-link" type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="#" onclick="editLocation({{ $location->id }})">
                                                        <i class="fas fa-edit me-2"></i>تعديل
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-danger" href="#" onclick="deleteLocation({{ $location->id }})">
                                                        <i class="fas fa-trash me-2"></i>حذف
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <!-- تبويب المدفوعات -->
            <div class="tab-pane fade" id="payments" role="tabpanel">
                <div class="contract-detail-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-money-bill-wave"></i>
                            سجل المدفوعات
                        </div>
                        <a href="{{ route('admin.contracts.payments.create', $contract->id) }}" class="btn btn-sm btn-success">
                            <i class="fas fa-plus me-1"></i>
                            تسديد دفعة جديدة
                        </a>
                    </div>
                    <div class="card-body">
                        @if($contract->payments->isEmpty())
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                                <div class="empty-state-text">
                                    لا توجد مدفوعات مسجلة لهذا العقد
                                </div>
                                <a href="{{ route('admin.contracts.payments.create', $contract->id) }}" class="btn btn-success mt-3">
                                    <i class="fas fa-plus me-2"></i>
                                    تسجيل دفعة جديدة
                                </a>
                            </div>
                        @else
                            @foreach($contract->payments as $payment)
                                <div class="payment-item">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center gap-3">
                                                <div>
                                                    <span class="badge-status payment-{{ $payment->status }}">
                                                        @switch($payment->status)
                                                            @case('completed')
                                                                <i class="fas fa-check-circle"></i>
                                                                @break
                                                            @case('pending')
                                                                <i class="fas fa-clock"></i>
                                                                @break
                                                            @case('failed')
                                                                <i class="fas fa-times-circle"></i>
                                                                @break
                                                        @endswitch
                                                        {{ $payment->status_name }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <strong>{{ number_format($payment->amount, 2) }} ر.س</strong>
                                                    <div class="text-muted small">
                                                        <i class="fas fa-calendar"></i>
                                                        {{ $payment->payment_date->format('Y-m-d') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-muted small">
                                                <i class="fas fa-credit-card"></i>
                                                {{ $payment->payment_method_name }}
                                            </div>
                                            @if($payment->transaction_id)
                                                <div class="text-muted small">
                                                    <i class="fas fa-hashtag"></i>
                                                    معرف العملية: {{ $payment->transaction_id }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-2 text-left">
                                            <a href="{{ route('admin.payments.show', $payment->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                    @if($payment->notes)
                                        <div class="mt-2 p-2" style="background: rgba(255, 255, 255, 0.05); border-radius: 5px;">
                                            <small><i class="fas fa-sticky-note me-1"></i>{{ $payment->notes }}</small>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <!-- تبويب الطلبات -->
            <div class="tab-pane fade" id="orders" role="tabpanel">
                <div class="contract-detail-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-shopping-cart"></i>
                            طلبات العقد
                        </div>
                        <a href="{{ route('admin.orders.create', ['contract_id' => $contract->id]) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus me-1"></i>
                            إنشاء طلب جديد
                        </a>
                    </div>
                    <div class="card-body">
                        @if($contract->orders->isEmpty())
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                                <div class="empty-state-text">
                                    لا توجد طلبات لهذا العقد
                                </div>
                                <a href="{{ route('admin.orders.create', ['contract_id' => $contract->id]) }}" class="btn btn-primary mt-3">
                                    <i class="fas fa-plus me-2"></i>
                                    إنشاء طلب جديد
                                </a>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>رقم الطلب</th>
                                            <th>التاريخ</th>
                                            <th>الحالة</th>
                                            <th>المبلغ</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($contract->orders->take(10) as $order)
                                            <tr>
                                                <td>{{ $order->order_number }}</td>
                                                <td>{{ $order->created_at->format('Y-m-d') }}</td>
                                                <td>
                                                    <span class="order-status order-{{ $order->status }}">
                                                        {{ $order->status_name }}
                                                    </span>
                                                </td>
                                                <td>{{ number_format($order->total, 2) }} ر.س</td>
                                                <td>
                                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if($contract->orders->count() > 10)
                                <div class="text-center mt-3">
                                    <a href="{{ route('admin.contracts.orders', $contract->id) }}" class="btn btn-link">
                                        عرض جميع الطلبات ({{ $contract->orders->count() }})
                                    </a>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <!-- تبويب السجل الزمني -->
            <div class="tab-pane fade" id="timeline" role="tabpanel">
                <div class="contract-detail-card">
                    <div class="card-header">
                        <i class="fas fa-history"></i>
                        السجل الزمني للعقد
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            @php
                                $activities = collect([
                                    [
                                        'date' => $contract->created_at,
                                        'type' => 'created',
                                        'description' => 'تم إنشاء العقد',
                                        'user' => $contract->user->name ?? 'النظام'
                                    ],
                                    [
                                        'date' => $contract->start_date,
                                        'type' => 'started',
                                        'description' => 'بداية سريان العقد',
                                        'user' => 'النظام'
                                    ],
                                    [
                                        'date' => $contract->end_date,
                                        'type' => 'end',
                                        'description' => 'نهاية العقد',
                                        'user' => 'النظام'
                                    ]
                                ])->sortByDesc('date');
                            @endphp

                            @foreach($activities as $activity)
                                @if($activity['date'])
                                    <div class="timeline-item">
                                        <div class="timeline-dot"></div>
                                        <div class="timeline-date">{{ $activity['date']->format('Y-m-d h:i A') }}</div>
                                        <div class="timeline-content">
                                            <strong>{{ $activity['description'] }}</strong>
                                            <div class="text-muted small mt-1">
                                                <i class="fas fa-user me-1"></i>
                                                بواسطة: {{ $activity['user'] }}
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- تبويب المستندات -->
            <div class="tab-pane fade" id="documents" role="tabpanel">
                <div class="contract-detail-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-file-pdf"></i>
                            المستندات
                        </div>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
                            <i class="fas fa-upload me-1"></i>
                            رفع مستند
                        </button>
                    </div>
                    <div class="card-body">
                        @if($contract->payment_proof)
                            <div class="document-preview mb-3" onclick="viewDocument('{{ Storage::url($contract->payment_proof) }}')">
                                <i class="fas fa-file-pdf"></i>
                                <span>إثبات الدفع - العقد {{ $contract->contract_number }}</span>
                                <small class="d-block text-muted">اضغط لعرض المستند</small>
                            </div>
                        @endif

                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="fas fa-file-upload"></i>
                            </div>
                            <div class="empty-state-text">
                                لا توجد مستندات مرفوعة
                            </div>
                            <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
                                <i class="fas fa-upload me-2"></i>
                                رفع مستند جديد
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- مودال إضافة موقع -->
    <div class="modal fade" id="addLocationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="background: var(--dark-card); color: #fff;">
                <div class="modal-header">
                    <h5 class="modal-title">إضافة موقع توصيل جديد</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.contracts.locations.store', $contract->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">الموقع <span class="text-danger">*</span></label>
                            <select name="saved_location_id" class="form-select" required>
                                <option value="">اختر موقعاً</option>
                                @foreach($contract->user->savedLocations ?? [] as $location)
                                    <option value="{{ $location->id }}">{{ $location->name }} - {{ $location->full_address }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">الأولوية <span class="text-danger">*</span></label>
                            <input type="number" name="priority" class="form-control" min="1" value="1" required>
                            <small class="text-muted">كلما قل الرقم زادت الأولوية</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ملاحظات</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="ملاحظات إضافية عن الموقع..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary">حفظ الموقع</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- مودال رفع مستند -->
    <div class="modal fade" id="uploadDocumentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="background: var(--dark-card); color: #fff;">
                <div class="modal-header">
                    <h5 class="modal-title">رفع مستند</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.contracts.documents.upload', $contract->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">نوع المستند</label>
                            <select name="document_type" class="form-select">
                                <option value="contract">عقد</option>
                                <option value="payment_proof">إثبات دفع</option>
                                <option value="identity">هوية</option>
                                <option value="other">أخرى</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">الملف <span class="text-danger">*</span></label>
                            <input type="file" name="document" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                            <small class="text-muted">الصيغ المسموحة: PDF, JPG, PNG (الحد الأقصى 5MB)</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ملاحظات</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary">رفع</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // تبديل حالة العقد
            $('.toggle-status-btn').on('click', function() {
                const contractId = '{{ $contract->id }}';
                const currentStatus = '{{ $contract->status }}';
                const newStatus = currentStatus === 'active' ? 'expired' : 'active';
                
                Swal.fire({
                    title: 'تأكيد تغيير الحالة',
                    text: `هل أنت متأكد من ${currentStatus === 'active' ? 'تعطيل' : 'تفعيل'} هذا العقد؟`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'نعم، تغيير',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin.contracts.toggle-status', $contract->id) }}",
                            type: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                _method: 'PATCH'
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'تم التغيير',
                                    text: response.message || 'تم تغيير حالة العقد بنجاح',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'خطأ',
                                    text: xhr.responseJSON?.message || 'حدث خطأ أثناء التحديث'
                                });
                            }
                        });
                    }
                });
            });

            // حذف العقد
            $('.delete-btn').on('click', function() {
                const contractId = $(this).data('id');
                const contractNumber = '{{ $contract->contract_number }}';

                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: `سيتم حذف العقد "${contractNumber}" نهائياً`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'نعم، احذف',
                    cancelButtonText: 'إلغاء',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin.contracts.destroy', $contract->id) }}",
                            type: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                _method: 'DELETE'
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'تم الحذف',
                                    text: 'تم حذف العقد بنجاح',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.href = "{{ route('admin.contracts.index') }}";
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'خطأ',
                                    text: xhr.responseJSON?.message || 'حدث خطأ أثناء الحذف'
                                });
                            }
                        });
                    }
                });
            });
        });

        function viewDocument(url) {
            window.open(url, '_blank');
        }

        function editLocation(locationId) {
            // تنفيذ تعديل الموقع
            Swal.fire({
                title: 'تعديل الموقع',
                text: 'سيتم إضافة وظيفة التعديل قريباً',
                icon: 'info'
            });
        }

function deleteLocation(contractId, locationId) {
    Swal.fire({
        title: 'تأكيد الحذف',
        text: 'هل أنت متأكد من حذف هذا الموقع؟',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'نعم، احذف',
        cancelButtonText: 'إلغاء'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "{{ route('admin.contracts.locations.destroy', ['contract' => ':contractId', 'location' => ':locationId']) }}"
                    .replace(':contractId', contractId)
                    .replace(':locationId', locationId),
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    _method: 'DELETE'
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم الحذف',
                        text: response.message || 'تم حذف الموقع بنجاح',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        $('#location-' + locationId).fadeOut(300, function() {
                            $(this).remove();
                            // Check if no locations left
                            if ($('#locationsContainer .location-item').length === 0) {
                                $('#noLocations').show();
                            }
                        });
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: xhr.responseJSON?.message || 'حدث خطأ أثناء الحذف'
                    });
                }
            });
        }
    });
}
        // رسائل التنبيه من الجلسة
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'نجاح',
                text: "{{ session('success') }}",
                timer: 2000,
                showConfirmButton: false
            });
        @endif

        @if(session('error'))
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