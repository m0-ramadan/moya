@extends('Admin.layout.master')

@section('title', 'إنشاء عقد جديد')

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

        .create-card {
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

        .btn-outline-primary {
            background: transparent;
            border: 1px dashed var(--primary-color);
            color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background: var(--primary-gradient);
            color: #fff;
        }

        .btn-outline-danger {
            background: transparent;
            border: 1px solid rgba(220, 53, 69, 0.3);
            color: #dc3545;
        }

        .btn-outline-danger:hover {
            background: #dc3545;
            color: #fff;
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

        .validation-error {
            color: #dc3545;
            font-size: 13px;
            margin-top: 5px;
        }

        .locations-container {
            max-height: 400px;
            overflow-y: auto;
            padding: 5px;
        }

        .location-item {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            transition: all 0.3s ease;
        }

        .location-item:hover {
            background: rgba(105, 108, 255, 0.1);
            border-color: var(--primary-color);
        }

        .location-item .remove-location {
            position: absolute;
            top: 10px;
            left: 10px;
            background: rgba(220, 53, 69, 0.2);
            border: 1px solid rgba(220, 53, 69, 0.3);
            color: #dc3545;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .location-item .remove-location:hover {
            background: #dc3545;
            color: #fff;
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

        .user-select-card {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 10px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .user-select-card:hover {
            background: rgba(105, 108, 255, 0.1);
            border-color: var(--primary-color);
        }

        .user-select-card.selected {
            background: rgba(105, 108, 255, 0.2);
            border-color: var(--primary-color);
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 20px;
        }

        .file-upload-area {
            border: 2px dashed rgba(105, 108, 255, 0.3);
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .file-upload-area:hover {
            border-color: var(--primary-color);
            background: rgba(105, 108, 255, 0.05);
        }

        .file-upload-area i {
            font-size: 48px;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .file-info {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            padding: 10px;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .progress {
            height: 8px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-bar {
            background: var(--primary-gradient);
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
                    <a href="{{ route('admin.index') }}">الرئيسية</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.contracts.index') }}">العقود</a>
                </li>
                <li class="breadcrumb-item active">إنشاء عقد جديد</li>
            </ol>
        </nav>

        <!-- رأس الصفحة -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">
                    <i class="fas fa-plus-circle text-primary me-2"></i>
                    إنشاء عقد جديد
                </h4>
                <p class="text-muted mb-0">
                    <i class="fas fa-file-contract me-2"></i>
                    أدخل بيانات العقد الجديد
                </p>
            </div>
            <div>
                <a href="{{ route('admin.contracts.index') }}" class="btn btn-secondary btn-action">
                    <i class="fas fa-arrow-right me-2"></i>
                    عودة للقائمة
                </a>
            </div>
        </div>

        <!-- نموذج إنشاء العقد -->
        <form action="{{ route('admin.contracts.store') }}" method="POST" enctype="multipart/form-data" id="contractForm">
            @csrf

            <!-- تبويبات الإنشاء -->
            <ul class="nav nav-tabs" id="createTabs" role="tablist">
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
                    <button class="nav-link" id="additional-tab" data-bs-toggle="tab" data-bs-target="#additional"
                        type="button">
                        <i class="fas fa-cog"></i>
                        إضافات
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="createTabsContent">
                <!-- تبويب المعلومات الأساسية -->
                <div class="tab-pane fade show active" id="basic" role="tabpanel">
                    <div class="create-card">
                        <div class="card-header">
                            <i class="fas fa-info-circle"></i>
                            المعلومات الأساسية
                        </div>
                        <div class="card-body">
                            <!-- اختيار العميل -->
                            <div class="form-section">
                                <div class="section-title">
                                    <i class="fas fa-user"></i>
                                    اختيار العميل
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">
                                        <i class="fas fa-search"></i>
                                        البحث عن عميل <span class="required">*</span>
                                    </label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" id="userSearch"
                                            placeholder="ابحث بالاسم أو رقم الجوال أو البريد الإلكتروني..."
                                            autocomplete="off">
                                        <button class="btn btn-primary" type="button" id="searchUserBtn">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                    <div id="searchResults" class="mb-3" style="display: none;">
                                        <div class="locations-container" id="usersList"></div>
                                    </div>

                                    <input type="hidden" name="user_id" id="selectedUserId" value="{{ old('user_id') }}"
                                        required>

                                    <div id="selectedUserInfo" class="info-box"
                                        style="{{ old('user_id') ? '' : 'display: none;' }}">
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar me-3" id="selectedUserAvatar"></div>
                                            <div>
                                                <h6 id="selectedUserName"></h6>
                                                <p class="mb-0 text-muted" id="selectedUserContact"></p>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-link text-danger me-auto"
                                                onclick="clearSelectedUser()">
                                                <i class="fas fa-times"></i> تغيير
                                            </button>
                                        </div>
                                    </div>
                                    @error('user_id')
                                        <div class="validation-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- نوع العقد -->
                            <div class="form-section">
                                <div class="section-title">
                                    <i class="fas fa-tag"></i>
                                    نوع العقد
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-user-tie"></i>
                                            نوع العقد <span class="required">*</span>
                                        </label>
                                        <div class="d-flex gap-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="contract_type"
                                                    id="type_individual" value="individual"
                                                    {{ old('contract_type', 'individual') == 'individual' ? 'checked' : '' }}
                                                    required>
                                                <label class="form-check-label" for="type_individual">
                                                    <i class="fas fa-user me-1"></i>فردي
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="contract_type"
                                                    id="type_company" value="company"
                                                    {{ old('contract_type') == 'company' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="type_company">
                                                    <i class="fas fa-building me-1"></i>شركة
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3 company-fields"
                                        style="{{ old('contract_type') == 'company' ? '' : 'display: none;' }}">
                                        <label class="form-label">
                                            <i class="fas fa-building"></i>
                                            اسم الشركة <span class="required">*</span>
                                        </label>
                                        <input type="text" name="company_name"
                                            class="form-control @error('company_name') is-invalid @enderror"
                                            value="{{ old('company_name') }}" placeholder="أدخل اسم الشركة">
                                        @error('company_name')
                                            <div class="validation-error">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- معلومات الاتصال -->
                            <div class="form-section">
                                <div class="section-title">
                                    <i class="fas fa-address-card"></i>
                                    معلومات الاتصال
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-user"></i>
                                            اسم مقدم الطلب
                                        </label>
                                        <input type="text" name="applicant_name"
                                            class="form-control @error('applicant_name') is-invalid @enderror"
                                            value="{{ old('applicant_name') }}" placeholder="أدخل اسم مقدم الطلب">
                                        @error('applicant_name')
                                            <div class="validation-error">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-phone"></i>
                                            رقم الجوال
                                        </label>
                                        <input type="text" name="phone"
                                            class="form-control @error('phone') is-invalid @enderror"
                                            value="{{ old('phone') }}" placeholder="أدخل رقم الجوال">
                                        @error('phone')
                                            <div class="validation-error">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- مدة العقد -->
                            <div class="form-section">
                                <div class="section-title">
                                    <i class="fas fa-calendar-alt"></i>
                                    مدة العقد
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-clock"></i>
                                            نوع المدة <span class="required">*</span>
                                        </label>
                                        <select name="duration_type"
                                            class="form-select @error('duration_type') is-invalid @enderror" required>
                                            <option value="">اختر المدة</option>
                                            <option value="monthly"
                                                {{ old('duration_type') == 'monthly' ? 'selected' : '' }}>شهري</option>
                                            <option value="quarterly"
                                                {{ old('duration_type') == 'quarterly' ? 'selected' : '' }}>ربع سنوي
                                            </option>
                                            <option value="semi_annual"
                                                {{ old('duration_type') == 'semi_annual' ? 'selected' : '' }}>نصف سنوي
                                            </option>
                                            <option value="annual"
                                                {{ old('duration_type') == 'annual' ? 'selected' : '' }}>سنوي</option>
                                        </select>
                                        @error('duration_type')
                                            <div class="validation-error">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-play"></i>
                                            تاريخ البداية <span class="required">*</span>
                                        </label>
                                        <input type="date" name="start_date"
                                            class="form-control @error('start_date') is-invalid @enderror"
                                            value="{{ old('start_date') }}" required>
                                        @error('start_date')
                                            <div class="validation-error">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-stop"></i>
                                            تاريخ النهاية <span class="required">*</span>
                                        </label>
                                        <input type="date" name="end_date"
                                            class="form-control @error('end_date') is-invalid @enderror"
                                            value="{{ old('end_date') }}" required>
                                        @error('end_date')
                                            <div class="validation-error">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-sync-alt"></i>
                                            تاريخ التجديد
                                        </label>
                                        <input type="date" name="renewal_date"
                                            class="form-control @error('renewal_date') is-invalid @enderror"
                                            value="{{ old('renewal_date') }}">
                                        <div class="form-text">يترك فارغاً إذا لم يحدد</div>
                                        @error('renewal_date')
                                            <div class="validation-error">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-tag"></i>
                                            الحالة <span class="required">*</span>
                                        </label>
                                        <select name="status" class="form-select @error('status') is-invalid @enderror"
                                            required>
                                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>نشط
                                            </option>
                                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>
                                                معلق</option>
                                            <option value="expired" {{ old('status') == 'expired' ? 'selected' : '' }}>
                                                منتهي</option>
                                            <option value="cancelled"
                                                {{ old('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                                        </select>
                                        @error('status')
                                            <div class="validation-error">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- تبويب المعلومات المالية -->
                <div class="tab-pane fade" id="financial" role="tabpanel">
                    <div class="create-card">
                        <div class="card-header">
                            <i class="fas fa-money-bill-wave"></i>
                            المعلومات المالية
                        </div>
                        <div class="card-body">
                            <div class="form-section">
                                <div class="section-title">
                                    <i class="fas fa-coins"></i>
                                    المبالغ
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-calculator"></i>
                                            إجمالي المبلغ <span class="required">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="number" name="total_amount" id="total_amount"
                                                class="form-control @error('total_amount') is-invalid @enderror"
                                                value="{{ old('total_amount') }}" step="0.01" min="0"
                                                required>
                                            <span class="input-group-text">ر.س</span>
                                        </div>
                                        @error('total_amount')
                                            <div class="validation-error">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-check-circle text-success"></i>
                                            المبلغ المدفوع
                                        </label>
                                        <div class="input-group">
                                            <input type="number" name="paid_amount" id="paid_amount"
                                                class="form-control @error('paid_amount') is-invalid @enderror"
                                                value="{{ old('paid_amount', 0) }}" step="0.01" min="0">
                                            <span class="input-group-text">ر.س</span>
                                        </div>
                                        @error('paid_amount')
                                            <div class="validation-error">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-times-circle text-danger"></i>
                                            المبلغ المتبقي
                                        </label>
                                        <div class="input-group">
                                            <input type="number" name="remaining_amount" id="remaining_amount"
                                                class="form-control" value="{{ old('remaining_amount', 0) }}"
                                                step="0.01" min="0" readonly>
                                            <span class="input-group-text">ر.س</span>
                                        </div>
                                        <div class="form-text">يتم حسابه تلقائياً</div>
                                    </div>
                                </div>

                                <div class="alert alert-info mt-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <span id="paymentSummary">المتبقي: 0.00 ر.س</span>
                                </div>
                            </div>

                            <!-- حدود الطلبات -->
                            <div class="form-section">
                                <div class="section-title">
                                    <i class="fas fa-chart-line"></i>
                                    حدود الطلبات
                                </div>

                                <div class="alert alert-warning mb-3">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    اترك 0 للطلبات غير المحدودة
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-calculator"></i>
                                            الحد الأقصى للطلبات
                                        </label>
                                        <input type="number" name="total_orders_limit" id="total_orders_limit"
                                            class="form-control @error('total_orders_limit') is-invalid @enderror"
                                            value="{{ old('total_orders_limit', 0) }}" min="0" step="1">
                                        @error('total_orders_limit')
                                            <div class="validation-error">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-hourglass-half"></i>
                                            الطلبات المتبقية
                                        </label>
                                        <input type="number" name="remaining_orders" id="remaining_orders"
                                            class="form-control @error('remaining_orders') is-invalid @enderror"
                                            value="{{ old('remaining_orders', 0) }}" min="0" step="1">
                                        @error('remaining_orders')
                                            <div class="validation-error">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-text">
                                    <i class="fas fa-info-circle"></i>
                                    إذا لم تحدد الطلبات المتبقية، سيتم تعيينها تلقائياً مساوية للحد الأقصى
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- تبويب مواقع التوصيل -->
                <div class="tab-pane fade" id="locations" role="tabpanel">
                    <div class="create-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-map-marker-alt"></i>
                                مواقع التوصيل
                            </div>
                            <button type="button" class="btn btn-light btn-sm" id="addLocationBtn" disabled>
                                <i class="fas fa-plus me-1"></i>
                                إضافة موقع
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="noUserWarning" class="alert alert-warning" style="display: none;">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                الرجاء اختيار العميل أولاً لإضافة مواقع التوصيل
                            </div>

                            <div id="locationsContainer" class="locations-container">
                                <!-- مواقع التوصيل ستضاف هنا ديناميكياً -->
                            </div>

                            <div id="noLocations" class="empty-state text-center py-5" style="display: none;">
                                <i class="fas fa-map-marker-alt fa-3x mb-3" style="color: rgba(255,255,255,0.2);"></i>
                                <p class="text-muted">لا توجد مواقع توصيل مضافة</p>
                                <button type="button" class="btn btn-outline-primary" id="addFirstLocationBtn" disabled>
                                    <i class="fas fa-plus me-2"></i>
                                    إضافة موقع أول
                                </button>
                            </div>

                            <template id="locationTemplate">
                                <div class="location-item" data-index="__INDEX__">
                                    <div class="remove-location" onclick="removeLocation(this)">
                                        <i class="fas fa-times"></i>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">الموقع <span class="required">*</span></label>
                                            <select name="locations[__INDEX__][saved_location_id]"
                                                class="form-select location-select" required>
                                                <option value="">اختر موقعاً</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <label class="form-label">الأولوية <span class="required">*</span></label>
                                            <input type="number" name="locations[__INDEX__][priority]"
                                                class="form-control priority-input" min="1" value="__PRIORITY__"
                                                required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">ملاحظات</label>
                                            <input type="text" name="locations[__INDEX__][notes]"
                                                class="form-control notes-input" placeholder="ملاحظات إضافية">
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- تبويب المستندات -->
                <div class="tab-pane fade" id="documents" role="tabpanel">
                    <div class="create-card">
                        <div class="card-header">
                            <i class="fas fa-file-pdf"></i>
                            المستندات
                        </div>
                        <div class="card-body">
                            <div class="form-section">
                                <div class="section-title">
                                    <i class="fas fa-upload"></i>
                                    إثبات الدفع
                                </div>

                                <div class="file-upload-area" onclick="document.getElementById('payment_proof').click()">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <h6>اضغط لرفع ملف إثبات الدفع</h6>
                                    <p class="text-muted small mb-0">PDF, JPG, PNG (الحد الأقصى 5MB)</p>
                                </div>

                                <input type="file" name="payment_proof" id="payment_proof" class="d-none"
                                    accept=".pdf,.jpg,.jpeg,.png" onchange="handleFileSelect(this)">

                                <div id="fileInfo" class="file-info" style="display: none;">
                                    <div>
                                        <i class="fas fa-file-pdf text-danger me-2"></i>
                                        <span id="fileName"></span>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-link text-danger"
                                        onclick="removeFile()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>

                                @error('payment_proof')
                                    <div class="validation-error mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-section">
                                <div class="section-title">
                                    <i class="fas fa-sticky-note"></i>
                                    ملاحظات العقد
                                </div>

                                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="5"
                                    placeholder="أدخل أي ملاحظات إضافية عن العقد...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="validation-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- تبويب الإضافات -->
                <div class="tab-pane fade" id="additional" role="tabpanel">
                    <div class="create-card">
                        <div class="card-header">
                            <i class="fas fa-cog"></i>
                            إضافات
                        </div>
                        <div class="card-body">
                            <div class="form-section">
                                <div class="section-title">
                                    <i class="fas fa-hashtag"></i>
                                    رقم العقد
                                </div>

                                <div class="row align-items-end">
                                    <div class="col-md-8 mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-tag"></i>
                                            رقم العقد
                                        </label>
                                        <input type="text" name="contract_number" id="contract_number"
                                            class="form-control @error('contract_number') is-invalid @enderror"
                                            value="{{ old('contract_number') }}"
                                            placeholder="سيتم إنشاؤه تلقائياً إذا ترك فارغاً">
                                        @error('contract_number')
                                            <div class="validation-error">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <button type="button" class="btn btn-outline-primary w-100"
                                            onclick="generateContractNumber()">
                                            <i class="fas fa-sync-alt me-2"></i>
                                            توليد رقم تلقائي
                                        </button>
                                    </div>
                                </div>
                                <div class="form-text">
                                    <i class="fas fa-info-circle"></i>
                                    إذا تركت الحقل فارغاً، سيتم إنشاء رقم عقد تلقائي
                                </div>
                            </div>

                            <div class="form-section">
                                <div class="section-title">
                                    <i class="fas fa-bell"></i>
                                    إعدادات إضافية
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="send_notification"
                                            id="send_notification" value="1" checked>
                                        <label class="form-check-label" for="send_notification">
                                            إرسال إشعار للعميل بإنشاء العقد
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="auto_activate"
                                            id="auto_activate" value="1" checked>
                                        <label class="form-check-label" for="auto_activate">
                                            تفعيل العقد تلقائياً
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <div class="section-title">
                                    <i class="fas fa-tasks"></i>
                                    ملخص العقد
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="info-box">
                                            <div class="d-flex justify-content-between">
                                                <span>العميل:</span>
                                                <strong id="summaryClient">غير محدد</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-box">
                                            <div class="d-flex justify-content-between">
                                                <span>المدة:</span>
                                                <strong id="summaryDuration">غير محددة</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-box">
                                            <div class="d-flex justify-content-between">
                                                <span>إجمالي المبلغ:</span>
                                                <strong id="summaryAmount">0.00 ر.س</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-box">
                                            <div class="d-flex justify-content-between">
                                                <span>مواقع التوصيل:</span>
                                                <strong id="summaryLocations">0</strong>
                                            </div>
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
                            حفظ العقد
                        </button>
                        <a href="{{ route('admin.contracts.index') }}" class="btn btn-secondary btn-action px-5">
                            <i class="fas fa-times me-2"></i>
                            إلغاء
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let locationIndex = 0;
        let selectedUser = null;
        let userLocations = [];

        $(document).ready(function() {
            // إظهار/إخفاء حقل اسم الشركة
            $('input[name="contract_type"]').on('change', function() {
                if ($(this).val() === 'company') {
                    $('.company-fields').slideDown();
                } else {
                    $('.company-fields').slideUp();
                }
            });

            // حساب المبلغ المتبقي
            $('#total_amount, #paid_amount').on('input', function() {
                let total = parseFloat($('#total_amount').val()) || 0;
                let paid = parseFloat($('#paid_amount').val()) || 0;
                let remaining = Math.max(0, total - paid);
                $('#remaining_amount').val(remaining.toFixed(2));
                $('#paymentSummary').text(`المتبقي: ${remaining.toFixed(2)} ر.س`);
            });

            // تحديث ملخص العقد
            $('select[name="duration_type"]').on('change', updateSummary);
            $('input[name="total_amount"]').on('input', updateSummary);
            $('select[name="user_id"]').on('change', updateSummary);

            // البحث عن العملاء
            let searchTimeout;
            $('#userSearch').on('keyup', function() {
                clearTimeout(searchTimeout);
                let query = $(this).val();

                if (query.length < 2) {
                    $('#searchResults').hide();
                    return;
                }

                searchTimeout = setTimeout(() => {
                    searchUsers(query);
                }, 500);
            });

            $('#searchUserBtn').on('click', function() {
                let query = $('#userSearch').val();
                if (query.length >= 2) {
                    searchUsers(query);
                }
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

            // تفعيل زر إضافة موقع بعد اختيار العميل
            if ($('#selectedUserId').val()) {
                loadUserLocations($('#selectedUserId').val());
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

        // البحث عن العملاء
        function searchUsers(query) {
            $.ajax({
                url: "{{ route('admin.contracts.users') }}",
                type: 'GET',
                data: {
                    search: query
                },
                beforeSend: function() {
                    $('#usersList').html(
                        '<div class="text-center p-3"><i class="fas fa-spinner fa-spin fa-2x"></i></div>');
                    $('#searchResults').show();
                },
                success: function(users) {
                    if (users.length === 0) {
                        $('#usersList').html('<div class="text-center p-3 text-muted">لا توجد نتائج</div>');
                        return;
                    }

                    let html = '';
                    users.forEach(user => {
                        html += `
                            <div class="user-select-card mb-2" onclick="selectUser(${user.id}, '${user.name}', '${user.phone}', '${user.email || ''}')">
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar me-3">${user.name.charAt(0)}</div>
                                    <div>
                                        <h6 class="mb-1">${user.name}</h6>
                                        <p class="mb-0 text-muted small">
                                            <i class="fas fa-phone me-1"></i>${user.phone}
                                            ${user.email ? ' | <i class="fas fa-envelope me-1"></i>' + user.email : ''}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    $('#usersList').html(html);
                },
                error: function() {
                    $('#usersList').html('<div class="text-center p-3 text-danger">حدث خطأ في البحث</div>');
                }
            });
        }

        // اختيار عميل
        function selectUser(id, name, phone, email) {
            selectedUser = {
                id,
                name,
                phone,
                email
            };
            $('#selectedUserId').val(id);
            $('#selectedUserAvatar').text(name.charAt(0));
            $('#selectedUserName').text(name);
            $('#selectedUserContact').html(
                `<i class="fas fa-phone me-2"></i>${phone} ${email ? ' | <i class="fas fa-envelope me-2"></i>' + email : ''}`
                );
            $('#selectedUserInfo').show();
            $('#searchResults').hide();
            $('#userSearch').val('');

            // تفعيل أزرار المواقع
            $('#addLocationBtn, #addFirstLocationBtn').prop('disabled', false);
            $('#noUserWarning').hide();

            // تحميل مواقع العميل
            loadUserLocations(id);

            // تحديث الملخص
            updateSummary();
        }

        // إلغاء اختيار العميل
        function clearSelectedUser() {
            selectedUser = null;
            $('#selectedUserId').val('');
            $('#selectedUserInfo').hide();
            $('#addLocationBtn, #addFirstLocationBtn').prop('disabled', true);
            $('#noUserWarning').show();
            $('#locationsContainer').empty();
            $('#noLocations').show();
            userLocations = [];
            updateSummary();
        }

        // تحميل مواقع العميل
        function loadUserLocations(userId) {
            $.ajax({
                url: `{{ url('admin/contracts/users') }}/${userId}/locations`,
                type: 'GET',
                success: function(locations) {
                    userLocations = locations;
                },
                error: function() {
                    console.error('Error loading locations');
                }
            });
        }

        // إضافة موقع جديد
        function addLocation() {
            if (!selectedUser) {
                Swal.fire({
                    icon: 'warning',
                    title: 'تحذير',
                    text: 'الرجاء اختيار العميل أولاً',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }

            let template = $('#locationTemplate').html();
            let newLocation = template.replace(/__INDEX__/g, locationIndex)
                .replace(/__PRIORITY__/g, locationIndex + 1);

            $('#locationsContainer').append(newLocation);

            // تعبئة خيارات المواقع
            let select = $(`select[name="locations[${locationIndex}][saved_location_id]"]`);
            select.empty();
            select.append('<option value="">اختر موقعاً</option>');

            userLocations.forEach(location => {
                select.append(
                    `<option value="${location.id}">${location.name} - ${location.full_address || ''}</option>`);
            });

            locationIndex++;
            $('#noLocations').hide();
            updateSummary();
        }

        // حذف موقع
        function removeLocation(element) {
            $(element).closest('.location-item').remove();
            if ($('#locationsContainer').children().length === 0) {
                $('#noLocations').show();
            }
            updateSummary();
        }

        // رفع ملف
        function handleFileSelect(input) {
            if (input.files && input.files[0]) {
                let file = input.files[0];
                let fileName = file.name;
                let fileSize = (file.size / 1024).toFixed(2);

                $('#fileName').text(`${fileName} (${fileSize} KB)`);
                $('#fileInfo').show();
            }
        }

        function removeFile() {
            $('#payment_proof').val('');
            $('#fileInfo').hide();
        }

        // توليد رقم عقد تلقائي
        function generateContractNumber() {
            $.ajax({
                url: "{{ route('admin.contracts.generate-number') }}",
                type: 'GET',
                success: function(response) {
                    $('#contract_number').val(response.contract_number);

                    // التحقق من uniqueness
                    checkContractNumber(response.contract_number);
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: 'حدث خطأ أثناء توليد الرقم'
                    });
                }
            });
        }

        // التحقق من رقم العقد
        function checkContractNumber(number) {
            $.ajax({
                url: "{{ route('admin.contracts.check-number') }}",
                type: 'GET',
                data: {
                    contract_number: number
                },
                success: function(response) {
                    if (!response.valid) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'تحذير',
                            text: response.message,
                            timer: 3000,
                            showConfirmButton: false
                        });
                    }
                }
            });
        }

        // تحديث الملخص
        function updateSummary() {
            // العميل
            if (selectedUser) {
                $('#summaryClient').text(selectedUser.name);
            } else {
                $('#summaryClient').text('غير محدد');
            }

            // المدة
            let duration = $('select[name="duration_type"] option:selected').text();
            $('#summaryDuration').text(duration || 'غير محددة');

            // المبلغ
            let amount = parseFloat($('#total_amount').val()) || 0;
            $('#summaryAmount').text(amount.toFixed(2) + ' ر.س');

            // عدد المواقع
            let locationCount = $('#locationsContainer').children().length;
            $('#summaryLocations').text(locationCount);
        }

        // التحقق قبل الإرسال
        $('#contractForm').on('submit', function(e) {
            if (!$('#selectedUserId').val()) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: 'الرجاء اختيار العميل'
                });
                return false;
            }

            // التحقق من تواريخ العقد
            let start = $('input[name="start_date"]').val();
            let end = $('input[name="end_date"]').val();

            if (new Date(start) > new Date(end)) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: 'تاريخ النهاية يجب أن يكون بعد تاريخ البداية'
                });
                return false;
            }

            // التحقق من المبالغ
            let total = parseFloat($('#total_amount').val()) || 0;
            let paid = parseFloat($('#paid_amount').val()) || 0;

            if (paid > total) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: 'المبلغ المدفوع لا يمكن أن يكون أكبر من إجمالي المبلغ'
                });
                return false;
            }

            return true;
        });

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
