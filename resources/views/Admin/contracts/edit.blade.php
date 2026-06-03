@extends('Admin.layout.master')

@section('title', 'تعديل العقد - ' . $contract->contract_number)

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

        .edit-card {
            background: var(--dark-card);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 25px;
            overflow: hidden;
        }

        .card-header {
            background: var(--primary-gradient);
            color: white;
            padding: 20px 25px;
            border-bottom: none;
            font-weight: 600;
            font-size: 18px;
        }

        .card-header i {
            margin-left: 10px;
        }

        .card-body {
            padding: 30px;
        }

        .form-section {
            background: rgba(255, 255, 255, 0.02);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .section-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .section-title i {
            margin-left: 8px;
        }

        .form-label {
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-label .required {
            color: #dc3545;
            margin-right: 3px;
        }

        .form-control,
        .form-select {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 12px 15px;
            color: #fff;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--primary-color);
            color: #fff;
            box-shadow: 0 0 0 0.25rem rgba(105, 108, 255, 0.25);
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        .form-select option {
            background: var(--dark-card);
            color: #fff;
        }

        .input-group-text {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.7);
            border-radius: 10px;
        }

        .form-text {
            color: rgba(255, 255, 255, 0.5);
            font-size: 12px;
            margin-top: 5px;
        }

        .form-check-input {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .form-check-label {
            color: rgba(255, 255, 255, 0.9);
        }

        .btn-action {
            padding: 12px 30px;
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

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545, #c82333);
            border: none;
        }

        .info-box {
            background: rgba(105, 108, 255, 0.1);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid rgba(105, 108, 255, 0.3);
        }

        .info-box i {
            color: var(--primary-color);
            margin-left: 10px;
        }

        .current-value {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            padding: 8px 15px;
            display: inline-block;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.7);
        }

        .current-value strong {
            color: var(--primary-color);
            margin-right: 5px;
        }

        .validation-error {
            color: #dc3545;
            font-size: 13px;
            margin-top: 5px;
        }

        .locations-list {
            max-height: 400px;
            overflow-y: auto;
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

        .priority-badge {
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

        .nav-tabs {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 25px;
        }

        .nav-tabs .nav-link {
            color: rgba(255, 255, 255, 0.7);
            border: none;
            padding: 12px 20px;
            font-weight: 600;
            font-size: 15px;
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

        .tab-pane {
            padding: 20px 0;
        }

        .alert-warning {
            background: rgba(255, 193, 7, 0.1);
            border: 1px solid rgba(255, 193, 7, 0.3);
            color: #ffc107;
            border-radius: 10px;
        }

        .alert-info {
            background: rgba(23, 162, 184, 0.1);
            border: 1px solid rgba(23, 162, 184, 0.3);
            color: #17a2b8;
            border-radius: 10px;
        }

        @media (max-width: 768px) {
            .card-body {
                padding: 20px;
            }

            .form-section {
                padding: 15px;
            }

            .btn-action {
                width: 100%;
                margin-bottom: 10px;
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
                     <a href="{{ route('admin.home') }}">الرئيسية</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.contracts.index') }}">العقود</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.contracts.show', $contract->id) }}">{{ $contract->contract_number }}</a>
                </li>
                <li class="breadcrumb-item active">تعديل</li>
            </ol>
        </nav>

        <!-- رأس الصفحة -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">
                    <i class="fas fa-edit text-primary me-2"></i>
                    تعديل العقد
                </h4>
                <p class="text-muted mb-0">
                    <i class="fas fa-hashtag me-2"></i>
                    {{ $contract->contract_number }}
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.contracts.show', $contract->id) }}" class="btn btn-info btn-action">
                    <i class="fas fa-eye me-2"></i>
                    عرض التفاصيل
                </a>
                <a href="{{ route('admin.contracts.index') }}" class="btn btn-secondary btn-action">
                    <i class="fas fa-arrow-right me-2"></i>
                    عودة للقائمة
                </a>
            </div>
        </div>

        <!-- نموذج التعديل -->
        <form action="{{ route('admin.contracts.update', $contract->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- تبويبات التعديل -->
            <ul class="nav nav-tabs" id="editTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic"
                        type="button">
                        <i class="fas fa-info-circle"></i>
                        المعلومات الأساسية
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="financial-tab" data-bs-toggle="tab" data-bs-target="#financial"
                        type="button">
                        <i class="fas fa-money-bill-wave"></i>
                        المعلومات المالية
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="locations-tab" data-bs-toggle="tab" data-bs-target="#locations"
                        type="button">
                        <i class="fas fa-map-marker-alt"></i>
                        مواقع التوصيل
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents"
                        type="button">
                        <i class="fas fa-file-pdf"></i>
                        المستندات
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="advanced-tab" data-bs-toggle="tab" data-bs-target="#advanced"
                        type="button">
                        <i class="fas fa-cog"></i>
                        إعدادات متقدمة
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="editTabsContent">
                <!-- تبويب المعلومات الأساسية -->
                <div class="tab-pane fade show active" id="basic" role="tabpanel">
                    <div class="edit-card">
                        <div class="card-header">
                            <i class="fas fa-info-circle"></i>
                            المعلومات الأساسية
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- معلومات العميل -->
                                <div class="col-md-6">
                                    <div class="form-section">
                                        <div class="section-title">
                                            <i class="fas fa-user"></i>
                                            معلومات العميل
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i class="fas fa-user-circle"></i>
                                                العميل <span class="required">*</span>
                                            </label>
                                            <select name="user_id"
                                                class="form-select @error('user_id') is-invalid @enderror" required>
                                                <option value="">اختر العميل</option>
                                                @foreach ($users ?? [] as $user)
                                                    <option value="{{ $user->id }}"
                                                        {{ old('user_id', $contract->user_id) == $user->id ? 'selected' : '' }}>
                                                        {{ $user->name }} - {{ $user->phone }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('user_id')
                                                <div class="validation-error">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i class="fas fa-user-tie"></i>
                                                نوع العقد <span class="required">*</span>
                                            </label>
                                            <div class="d-flex gap-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="contract_type"
                                                        id="type_individual" value="individual"
                                                        {{ old('contract_type', $contract->contract_type) == 'individual' ? 'checked' : '' }}
                                                        required>
                                                    <label class="form-check-label" for="type_individual">
                                                        <i class="fas fa-user me-1"></i>فردي
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="contract_type"
                                                        id="type_company" value="company"
                                                        {{ old('contract_type', $contract->contract_type) == 'company' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="type_company">
                                                        <i class="fas fa-building me-1"></i>شركة
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3 company-fields"
                                            style="{{ old('contract_type', $contract->contract_type) == 'company' ? '' : 'display: none;' }}">
                                            <label class="form-label">
                                                <i class="fas fa-building"></i>
                                                اسم الشركة <span class="required">*</span>
                                            </label>
                                            <input type="text" name="company_name"
                                                class="form-control @error('company_name') is-invalid @enderror"
                                                value="{{ old('company_name', $contract->company_name) }}"
                                                placeholder="أدخل اسم الشركة">
                                            @error('company_name')
                                                <div class="validation-error">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- معلومات الاتصال -->
                                <div class="col-md-6">
                                    <div class="form-section">
                                        <div class="section-title">
                                            <i class="fas fa-address-card"></i>
                                            معلومات الاتصال
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i class="fas fa-user"></i>
                                                اسم مقدم الطلب
                                            </label>
                                            <input type="text" name="applicant_name"
                                                class="form-control @error('applicant_name') is-invalid @enderror"
                                                value="{{ old('applicant_name', $contract->applicant_name) }}"
                                                placeholder="أدخل اسم مقدم الطلب">
                                            @error('applicant_name')
                                                <div class="validation-error">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i class="fas fa-phone"></i>
                                                رقم الجوال
                                            </label>
                                            <input type="text" name="phone"
                                                class="form-control @error('phone') is-invalid @enderror"
                                                value="{{ old('phone', $contract->phone) }}"
                                                placeholder="أدخل رقم الجوال">
                                            @error('phone')
                                                <div class="validation-error">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="current-value">
                                            <i class="fas fa-info-circle"></i>
                                            <strong>رقم العقد الحالي:</strong> {{ $contract->contract_number }}
                                            <input type="hidden" name="contract_number"
                                                value="{{ $contract->contract_number }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- مدة العقد -->
                                <div class="col-md-6">
                                    <div class="form-section">
                                        <div class="section-title">
                                            <i class="fas fa-calendar-alt"></i>
                                            مدة العقد
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i class="fas fa-clock"></i>
                                                نوع المدة <span class="required">*</span>
                                            </label>
                                            <select name="duration_type"
                                                class="form-select @error('duration_type') is-invalid @enderror" required>
                                                <option value="">اختر المدة</option>
                                                <option value="monthly"
                                                    {{ old('duration_type', $contract->duration_type) == 'monthly' ? 'selected' : '' }}>
                                                    شهري</option>
                                                <option value="quarterly"
                                                    {{ old('duration_type', $contract->duration_type) == 'quarterly' ? 'selected' : '' }}>
                                                    ربع سنوي</option>
                                                <option value="semi_annual"
                                                    {{ old('duration_type', $contract->duration_type) == 'semi_annual' ? 'selected' : '' }}>
                                                    نصف سنوي</option>
                                                <option value="annual"
                                                    {{ old('duration_type', $contract->duration_type) == 'annual' ? 'selected' : '' }}>
                                                    سنوي</option>
                                            </select>
                                            @error('duration_type')
                                                <div class="validation-error">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">
                                                    <i class="fas fa-play"></i>
                                                    تاريخ البداية <span class="required">*</span>
                                                </label>
                                                <input type="date" name="start_date"
                                                    class="form-control @error('start_date') is-invalid @enderror"
                                                    value="{{ old('start_date', $contract->start_date?->format('Y-m-d')) }}"
                                                    required>
                                                @error('start_date')
                                                    <div class="validation-error">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">
                                                    <i class="fas fa-stop"></i>
                                                    تاريخ النهاية <span class="required">*</span>
                                                </label>
                                                <input type="date" name="end_date"
                                                    class="form-control @error('end_date') is-invalid @enderror"
                                                    value="{{ old('end_date', $contract->end_date?->format('Y-m-d')) }}"
                                                    required>
                                                @error('end_date')
                                                    <div class="validation-error">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i class="fas fa-sync-alt"></i>
                                                تاريخ التجديد
                                            </label>
                                            <input type="date" name="renewal_date"
                                                class="form-control @error('renewal_date') is-invalid @enderror"
                                                value="{{ old('renewal_date', $contract->renewal_date?->format('Y-m-d')) }}">
                                            @error('renewal_date')
                                                <div class="validation-error">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">يترك فارغاً إذا لم يحدد</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- حدود الطلبات -->
                                <div class="col-md-6">
                                    <div class="form-section">
                                        <div class="section-title">
                                            <i class="fas fa-chart-line"></i>
                                            حدود الطلبات
                                        </div>

                                        <div class="alert alert-info mb-3">
                                            <i class="fas fa-info-circle me-2"></i>
                                            اترك 0 للطلبات غير المحدودة
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i class="fas fa-calculator"></i>
                                                الحد الأقصى للطلبات
                                            </label>
                                            <input type="number" name="total_orders_limit"
                                                class="form-control @error('total_orders_limit') is-invalid @enderror"
                                                value="{{ old('total_orders_limit', $contract->total_orders_limit) }}"
                                                min="0" step="1">
                                            @error('total_orders_limit')
                                                <div class="validation-error">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i class="fas fa-hourglass-half"></i>
                                                الطلبات المتبقية
                                            </label>
                                            <input type="number" name="remaining_orders"
                                                class="form-control @error('remaining_orders') is-invalid @enderror"
                                                value="{{ old('remaining_orders', $contract->remaining_orders) }}"
                                                min="0" step="1">
                                            @error('remaining_orders')
                                                <div class="validation-error">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">العدد الحالي: {{ $contract->remaining_orders }}</div>
                                        </div>

                                        <div class="current-value">
                                            <i class="fas fa-chart-bar"></i>
                                            <strong>الطلبات المستخدمة:</strong>
                                            {{ $contract->total_orders_limit - $contract->remaining_orders }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- حالة العقد -->
                                <div class="col-md-6">
                                    <div class="form-section">
                                        <div class="section-title">
                                            <i class="fas fa-info-circle"></i>
                                            حالة العقد
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i class="fas fa-tag"></i>
                                                الحالة <span class="required">*</span>
                                            </label>
                                            <select name="status"
                                                class="form-select @error('status') is-invalid @enderror" required>
                                                <option value="active"
                                                    {{ old('status', $contract->status) == 'active' ? 'selected' : '' }}>
                                                    نشط</option>
                                                <option value="expired"
                                                    {{ old('status', $contract->status) == 'expired' ? 'selected' : '' }}>
                                                    منتهي</option>
                                                <option value="pending"
                                                    {{ old('status', $contract->status) == 'pending' ? 'selected' : '' }}>
                                                    معلق</option>
                                                <option value="cancelled"
                                                    {{ old('status', $contract->status) == 'cancelled' ? 'selected' : '' }}>
                                                    ملغي</option>
                                            </select>
                                            @error('status')
                                                <div class="validation-error">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- ملاحظات -->
                                <div class="col-md-6">
                                    <div class="form-section">
                                        <div class="section-title">
                                            <i class="fas fa-sticky-note"></i>
                                            ملاحظات
                                        </div>

                                        <div class="mb-3">
                                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="4"
                                                placeholder="أدخل أي ملاحظات إضافية...">{{ old('notes', $contract->notes) }}</textarea>
                                            @error('notes')
                                                <div class="validation-error">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- تبويب المعلومات المالية -->
                <div class="tab-pane fade" id="financial" role="tabpanel">
                    <div class="edit-card">
                        <div class="card-header">
                            <i class="fas fa-money-bill-wave"></i>
                            المعلومات المالية
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-section">
                                        <div class="section-title">
                                            <i class="fas fa-coins"></i>
                                            المبالغ
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i class="fas fa-calculator"></i>
                                                إجمالي المبلغ <span class="required">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="number" name="total_amount"
                                                    class="form-control @error('total_amount') is-invalid @enderror"
                                                    value="{{ old('total_amount', $contract->total_amount) }}"
                                                    step="0.01" min="0" required>
                                                <span class="input-group-text">ر.س</span>
                                            </div>
                                            @error('total_amount')
                                                <div class="validation-error">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i class="fas fa-check-circle text-success"></i>
                                                المبلغ المدفوع
                                            </label>
                                            <div class="input-group">
                                                <input type="number" name="paid_amount"
                                                    class="form-control @error('paid_amount') is-invalid @enderror"
                                                    value="{{ old('paid_amount', $contract->paid_amount) }}"
                                                    step="0.01" min="0">
                                                <span class="input-group-text">ر.س</span>
                                            </div>
                                            @error('paid_amount')
                                                <div class="validation-error">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i class="fas fa-times-circle text-danger"></i>
                                                المبلغ المتبقي
                                            </label>
                                            <div class="input-group">
                                                <input type="number" name="remaining_amount"
                                                    class="form-control @error('remaining_amount') is-invalid @enderror"
                                                    value="{{ old('remaining_amount', $contract->remaining_amount) }}"
                                                    step="0.01" min="0" readonly>
                                                <span class="input-group-text">ر.س</span>
                                            </div>
                                            <div class="form-text">يتم حسابه تلقائياً</div>
                                            @error('remaining_amount')
                                                <div class="validation-error">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-section">
                                        <div class="section-title">
                                            <i class="fas fa-chart-pie"></i>
                                            ملخص المبالغ
                                        </div>

                                        <div class="info-box">
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>إجمالي العقد:</span>
                                                <strong>{{ number_format($contract->total_amount, 2) }} ر.س</strong>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>المدفوع:</span>
                                                <strong
                                                    class="text-success">{{ number_format($contract->paid_amount, 2) }}
                                                    ر.س</strong>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span>المتبقي:</span>
                                                <strong
                                                    class="text-danger">{{ number_format($contract->remaining_amount, 2) }}
                                                    ر.س</strong>
                                            </div>
                                            <hr class="my-3" style="border-color: rgba(255,255,255,0.1);">
                                            <div class="d-flex justify-content-between">
                                                <span>نسبة الدفع:</span>
                                                <strong>
                                                    @if ($contract->total_amount > 0)
                                                        {{ number_format(($contract->paid_amount / $contract->total_amount) * 100, 1) }}%
                                                    @else
                                                        0%
                                                    @endif
                                                </strong>
                                            </div>
                                        </div>

                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            تغيير المبالغ قد يؤثر على سجل المدفوعات
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- تبويب مواقع التوصيل -->
                <div class="tab-pane fade" id="locations" role="tabpanel">
                    <div class="edit-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-map-marker-alt"></i>
                                مواقع التوصيل
                            </div>
                            <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal"
                                data-bs-target="#addLocationModal">
                                <i class="fas fa-plus me-1"></i>
                                إضافة موقع
                            </button>
                        </div>
                        <div class="card-body">
                            @if ($contract->deliveryLocations->isEmpty())
                                <div class="text-center py-5">
                                    <i class="fas fa-map-marker-alt fa-3x mb-3" style="color: rgba(255,255,255,0.2);"></i>
                                    <p class="text-muted">لا توجد مواقع توصيل لهذا العقد</p>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#addLocationModal">
                                        <i class="fas fa-plus me-2"></i>
                                        إضافة موقع جديد
                                    </button>
                                </div>
                            @else
                                <div class="locations-list">
                                    @foreach ($contract->deliveryLocations->sortBy('priority') as $index => $location)
                                        <div class="location-item" id="location-{{ $location->id }}">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="d-flex align-items-center">
                                                    <span class="priority-badge">{{ $location->priority }}</span>
                                                    <div>
                                                        <h6 class="mb-1">
                                                            {{ $location->savedLocation->name ?? 'موقع غير محدد' }}</h6>
                                                        <p class="mb-1 text-muted small">
                                                            <i class="fas fa-map-pin me-1"></i>
                                                            {{ $location->savedLocation->full_address ?? '--' }}
                                                        </p>
                                                        @if ($location->notes)
                                                            <p class="mb-0 text-muted small">
                                                                <i class="fas fa-sticky-note me-1"></i>
                                                                {{ $location->notes }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div>
                                                    <button type="button" class="btn btn-sm btn-link text-danger"
                                                        onclick="deleteLocation({{ $contract->id }}, {{ $location->id }})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <input type="hidden" name="existing_locations[]"
                                                value="{{ $location->id }}">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- تبويب المستندات -->
                <div class="tab-pane fade" id="documents" role="tabpanel">
                    <div class="edit-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-file-pdf"></i>
                                المستندات
                            </div>
                            <div>
                                <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#uploadDocumentModal">
                                    <i class="fas fa-upload me-1"></i>
                                    رفع مستند
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            @if ($contract->payment_proof)
                                <div class="document-item d-flex justify-content-between align-items-center p-3 mb-3"
                                    style="background: rgba(255,255,255,0.05); border-radius: 10px;">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-file-pdf fa-2x text-danger me-3"></i>
                                        <div>
                                            <strong>إثبات الدفع</strong>
                                            <div class="text-muted small">
                                                <i class="fas fa-calendar me-1"></i>
                                                مرفق مع العقد
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <a href="{{ Storage::url($contract->payment_proof) }}" target="_blank"
                                            class="btn btn-sm btn-info me-2">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger"
                                            onclick="removeDocument('payment_proof')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endif

                            <div class="text-center py-4">
                                <i class="fas fa-file-upload fa-3x mb-3" style="color: rgba(255,255,255,0.2);"></i>
                                <p class="text-muted">اسحب وأفلت الملفات هنا أو اضغط لرفع مستندات جديدة</p>
                                <input type="file" name="new_documents[]" class="d-none" id="fileInput" multiple>
                                <button type="button" class="btn btn-outline-primary"
                                    onclick="document.getElementById('fileInput').click()">
                                    <i class="fas fa-upload me-2"></i>
                                    اختيار ملفات
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- تبويب الإعدادات المتقدمة -->
                <div class="tab-pane fade" id="advanced" role="tabpanel">
                    <div class="edit-card">
                        <div class="card-header">
                            <i class="fas fa-cog"></i>
                            إعدادات متقدمة
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning mb-4">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                هذه الإعدادات متقدمة وقد تؤثر على وظائف العقد
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-section">
                                        <div class="section-title">
                                            <i class="fas fa-bell"></i>
                                            إشعارات العقد
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox"
                                                    name="enable_expiry_notifications" id="expiry_notifications"
                                                    value="1"
                                                    {{ old('enable_expiry_notifications', true) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="expiry_notifications">
                                                    تفعيل إشعارات انتهاء العقد
                                                </label>
                                            </div>
                                            <div class="form-text">إرسال إشعارات قبل انتهاء العقد بفترة</div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">فترة التنبيه (قبل الانتهاء)</label>
                                            <select name="notification_period" class="form-select">
                                                <option value="7">7 أيام</option>
                                                <option value="15" selected>15 يوم</option>
                                                <option value="30">30 يوم</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-section">
                                        <div class="section-title">
                                            <i class="fas fa-lock"></i>
                                            صلاحيات العقد
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="allow_auto_renewal"
                                                    id="auto_renewal" value="1"
                                                    {{ old('allow_auto_renewal', false) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="auto_renewal">
                                                    السماح بالتجديد التلقائي
                                                </label>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="allow_extra_orders"
                                                    id="extra_orders" value="1"
                                                    {{ old('allow_extra_orders', false) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="extra_orders">
                                                    السماح بطلبات إضافية بعد نفاذ الحد
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="form-section">
                                        <div class="section-title">
                                            <i class="fas fa-history"></i>
                                            سجل التعديلات
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>التاريخ</th>
                                                        <th>المستخدم</th>
                                                        <th>الإجراء</th>
                                                        <th>التفاصيل</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>{{ $contract->created_at->format('Y-m-d H:i') }}</td>
                                                        <td>النظام</td>
                                                        <td>إنشاء العقد</td>
                                                        <td>تم إنشاء العقد برقم {{ $contract->contract_number }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ $contract->updated_at->format('Y-m-d H:i') }}</td>
                                                        <td>{{ auth()->user()->name }}</td>
                                                        <td>تعديل</td>
                                                        <td>جاري تعديل العقد</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- أزرار الحفظ -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="d-flex gap-3 justify-content-center">
                        <button type="submit" class="btn btn-primary btn-action px-5">
                            <i class="fas fa-save me-2"></i>
                            حفظ التغييرات
                        </button>
                        <a href="{{ route('admin.contracts.show', $contract->id) }}"
                            class="btn btn-secondary btn-action px-5">
                            <i class="fas fa-times me-2"></i>
                            إلغاء
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- مودال إضافة موقع -->
    <div class="modal fade" id="addLocationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="background: var(--dark-card); color: #fff;">
                <div class="modal-header">
                    <h5 class="modal-title">إضافة موقع توصيل جديد</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">الموقع <span class="text-danger">*</span></label>
                        <select name="new_location_id" class="form-select" id="newLocationId">
                            <option value="">اختر موقعاً</option>
                            @foreach ($contract->user->savedLocations ?? [] as $location)
                                <option value="{{ $location->id }}">{{ $location->name }} -
                                    {{ $location->full_address }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الأولوية <span class="text-danger">*</span></label>
                        <input type="number" id="newLocationPriority" class="form-control" min="1"
                            value="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ملاحظات</label>
                        <textarea id="newLocationNotes" class="form-control" rows="3" placeholder="ملاحظات إضافية..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-primary" onclick="addNewLocation()">إضافة الموقع</button>
                </div>
            </div>
        </div>
    </div>

    <!-- مودال رفع مستند -->
    <div class="modal fade" id="uploadDocumentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="background: var(--dark-card); color: #fff;">
                <div class="modal-header">
                    <h5 class="modal-title">رفع مستند جديد</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">نوع المستند</label>
                        <select id="documentType" class="form-select">
                            <option value="contract">عقد</option>
                            <option value="payment_proof">إثبات دفع</option>
                            <option value="identity">هوية</option>
                            <option value="other">أخرى</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الملف <span class="text-danger">*</span></label>
                        <input type="file" id="documentFile" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                        <div class="form-text">الصيغ المسموحة: PDF, JPG, PNG (الحد الأقصى 5MB)</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-primary" onclick="uploadDocument()">رفع</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // إظهار/إخفاء حقل اسم الشركة
            $('input[name="contract_type"]').on('change', function() {
                if ($(this).val() === 'company') {
                    $('.company-fields').slideDown();
                } else {
                    $('.company-fields').slideUp();
                }
            });

            // حساب المبلغ المتبقي تلقائياً
            $('input[name="total_amount"], input[name="paid_amount"]').on('input', function() {
                let total = parseFloat($('input[name="total_amount"]').val()) || 0;
                let paid = parseFloat($('input[name="paid_amount"]').val()) || 0;
                let remaining = Math.max(0, total - paid);
                $('input[name="remaining_amount"]').val(remaining.toFixed(2));
            });

            // التحقق من تواريخ العقد
            $('input[name="start_date"], input[name="end_date"]').on('change', function() {
                let start = $('input[name="start_date"]').val();
                let end = $('input[name="end_date"]').val();

                if (start && end && new Date(start) > new Date(end)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: 'تاريخ النهاية يجب أن يكون بعد تاريخ البداية',
                        timer: 3000,
                        showConfirmButton: false
                    });
                    $(this).val('');
                }
            });

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

            @if ($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ في البيانات',
                    text: 'يرجى التحقق من المدخلات',
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif
        });

        // إضافة موقع جديد (AJAX)
        function addNewLocation() {
            let locationId = $('#newLocationId').val();
            let priority = $('#newLocationPriority').val();
            let notes = $('#newLocationNotes').val();

            if (!locationId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'تحذير',
                    text: 'الرجاء اختيار موقع',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }

            $.ajax({
                url: "{{ route('admin.contracts.locations.store', $contract->id) }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    saved_location_id: locationId,
                    priority: priority,
                    notes: notes
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم',
                        text: 'تم إضافة الموقع بنجاح',
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
                        text: xhr.responseJSON?.message || 'حدث خطأ أثناء إضافة الموقع'
                    });
                }
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
                                    if ($('#locationsContainer .location-item')
                                        .length === 0) {
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
        // رفع مستند
        function uploadDocument() {
            let file = $('#documentFile')[0].files[0];
            let type = $('#documentType').val();

            if (!file) {
                Swal.fire({
                    icon: 'warning',
                    title: 'تحذير',
                    text: 'الرجاء اختيار ملف',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }

            let formData = new FormData();
            formData.append('_token', "{{ csrf_token() }}");
            formData.append('document', file);
            formData.append('document_type', type);

            $.ajax({
                url: "{{ route('admin.contracts.documents.upload', $contract->id) }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم',
                        text: 'تم رفع المستند بنجاح',
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
                        text: xhr.responseJSON?.message || 'حدث خطأ أثناء رفع المستند'
                    });
                }
            });
        }

        // حذف مستند
        function removeDocument(type) {
            Swal.fire({
                title: 'تأكيد الحذف',
                text: 'هل أنت متأكد من حذف هذا المستند؟',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.contracts.documents.remove', $contract->id) }}",
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            document_type: type
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'تم الحذف',
                                text: 'تم حذف المستند بنجاح',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ',
                                text: 'حدث خطأ أثناء الحذف'
                            });
                        }
                    });
                }
            });
        }

        // تحذير قبل مغادرة الصفحة
        let formChanged = false;
        $('form input, form select, form textarea').on('change', function() {
            formChanged = true;
        });

        window.addEventListener('beforeunload', function(e) {
            if (formChanged) {
                e.preventDefault();
                e.returnValue = '';
            }
        });

        $('form').on('submit', function() {
            formChanged = false;
        });
    </script>
@endsection
