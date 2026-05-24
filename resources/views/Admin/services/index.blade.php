@extends('Admin.layout.master')

@section('title', 'إدارة الخدمات')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: "Cairo", sans-serif !important;
        }

        /* Services Dashboard */
        .services-dashboard {
            padding: 20px 0;
        }

        /* Welcome Card */
        .welcome-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            padding: 30px;
            color: white;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .welcome-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .welcome-icon {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-left: 20px;
        }

        .welcome-content h3 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .welcome-content p {
            opacity: 0.9;
            margin-bottom: 0;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--bs-card-bg);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-left: 5px solid;
            cursor: pointer;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .stat-card.total {
            border-left-color: #696cff;
        }

        .stat-card.active {
            border-left-color: #198754;
        }

        .stat-card.inactive {
            border-left-color: #dc3545;
        }

        .stat-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-left: 15px;
            color: white;
        }

        .stat-card.total .stat-icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .stat-card.active .stat-icon {
            background: linear-gradient(135deg, #198754 0%, #20c997 100%);
        }

        .stat-card.inactive .stat-icon {
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
        }

        .stat-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--bs-heading-color);
            margin-bottom: 5px;
        }

        .stat-description {
            font-size: 13px;
            color: var(--bs-secondary-color);
        }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 10px;
        }

        /* Form Card */
        .form-card {
            background: var(--bs-card-bg);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--bs-border-color);
        }

        .form-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--bs-border-color);
        }

        .form-icon {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            background: linear-gradient(135deg, #198754 0%, #20c997 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-left: 15px;
        }

        .form-title h5 {
            font-size: 18px;
            font-weight: 600;
            color: var(--bs-heading-color);
            margin-bottom: 5px;
        }

        .form-title p {
            color: var(--bs-secondary-color);
            font-size: 14px;
            margin-bottom: 0;
        }

        .form-label {
            font-weight: 600;
            color: var(--bs-heading-color);
            margin-bottom: 8px;
        }

        .form-control,
        .form-select {
            border: 1px solid var(--bs-border-color);
            border-radius: 10px;
            padding: 10px 15px;
            background: var(--bs-card-bg);
            color: var(--bs-heading-color);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .image-preview {
            width: 120px;
            height: 120px;
            border-radius: 10px;
            border: 2px dashed var(--bs-border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
            background: var(--bs-light-bg-subtle);
            cursor: pointer;
            transition: all 0.3s;
        }

        .image-preview:hover {
            border-color: #667eea;
        }

        .image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .image-preview .placeholder {
            text-align: center;
            color: var(--bs-secondary-color);
        }

        .image-preview .placeholder i {
            font-size: 32px;
            margin-bottom: 5px;
        }

        .image-preview .placeholder span {
            font-size: 12px;
        }

        .btn-save {
            background: linear-gradient(135deg, #198754 0%, #20c997 100%);
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: opacity 0.3s;
        }

        .btn-save:hover {
            opacity: 0.9;
            color: white;
        }

        .btn-cancel {
            background: var(--bs-secondary-bg);
            color: var(--bs-heading-color);
            border: 1px solid var(--bs-border-color);
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .btn-cancel:hover {
            background: var(--bs-border-color);
        }

        .form-check-input:checked {
            background-color: #198754;
            border-color: #198754;
        }

        .required:after {
            content: " *";
            color: #dc3545;
        }

        /* Filter Card */
        .filter-card {
            background: var(--bs-card-bg);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .filter-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--bs-border-color);
        }

        .filter-icon {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-left: 15px;
        }

        .filter-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--bs-heading-color);
            margin-bottom: 5px;
        }

        .filter-subtitle {
            color: var(--bs-secondary-color);
            font-size: 14px;
        }

        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .filter-group {
            flex: 1 1 200px;
        }

        .filter-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--bs-heading-color);
            margin-bottom: 5px;
        }

        .filter-select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid var(--bs-border-color);
            border-radius: 8px;
            background: var(--bs-card-bg);
            color: var(--bs-heading-color);
        }

        .filter-actions {
            display: flex;
            gap: 10px;
            align-items: flex-end;
            flex: 1 1 200px;
        }

        .btn-filter {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: opacity 0.3s;
        }

        .btn-filter:hover {
            opacity: 0.9;
            color: white;
        }

        .btn-reset {
            background: var(--bs-secondary-bg);
            color: var(--bs-heading-color);
            border: 1px solid var(--bs-border-color);
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-reset:hover {
            background: var(--bs-border-color);
        }

        /* Table Card */
        .table-card {
            background: var(--bs-card-bg);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .table-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .table-title {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .table-icon {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .table-title h5 {
            font-size: 18px;
            font-weight: 600;
            color: var(--bs-heading-color);
            margin-bottom: 5px;
        }

        .table-title p {
            color: var(--bs-secondary-color);
            font-size: 14px;
            margin-bottom: 0;
        }

        .search-box {
            position: relative;
            width: 250px;
        }

        .search-box input {
            width: 100%;
            padding: 10px 40px 10px 15px;
            border: 1px solid var(--bs-border-color);
            border-radius: 25px;
            background: var(--bs-card-bg);
            color: var(--bs-heading-color);
        }

        .search-box i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--bs-secondary-color);
        }

        /* Services Table */
        .services-table {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: right;
            padding: 15px 10px;
            background: var(--bs-light-bg-subtle);
            color: var(--bs-heading-color);
            font-weight: 600;
            font-size: 14px;
            border-bottom: 2px solid var(--bs-border-color);
        }

        td {
            padding: 15px 10px;
            border-bottom: 1px solid var(--bs-border-color);
            vertical-align: middle;
        }

        /* Service Info */
        .service-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .service-image {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 20px;
            flex-shrink: 0;
            overflow: hidden;
        }

        .service-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .service-details h6 {
            font-size: 15px;
            font-weight: 600;
            color: var(--bs-heading-color);
            margin-bottom: 3px;
        }

        .service-details .service-title {
            font-size: 12px;
            color: var(--bs-secondary-color);
            margin-bottom: 3px;
        }

        .service-details .service-price {
            font-size: 13px;
            font-weight: 600;
            color: #198754;
        }

        /* Badges */
        .badge-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .badge-status.active {
            background: var(--bs-success-bg-subtle);
            color: var(--bs-success-text);
        }

        .badge-status.inactive {
            background: var(--bs-secondary-bg-subtle);
            color: var(--bs-secondary-text);
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }

        .btn-action {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
            color: white;
        }

        .btn-action.edit {
            background: linear-gradient(135deg, #fd7e14 0%, #ffc107 100%);
        }

        .btn-action.toggle {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        }

        .btn-action.delete {
            background: linear-gradient(135deg, #dc3545 0%, #d63384 100%);
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        }

        /* Pagination */
        .pagination-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--bs-border-color);
            flex-wrap: wrap;
            gap: 15px;
        }

        .pagination-details {
            color: var(--bs-secondary-color);
            font-size: 14px;
        }

        .pagination-links {
            display: flex;
            gap: 5px;
        }

        .page-link {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bs-card-bg);
            border: 1px solid var(--bs-border-color);
            color: var(--bs-heading-color);
            text-decoration: none;
            transition: all 0.3s;
        }

        .page-link:hover,
        .page-link.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: transparent;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 50px 20px;
        }

        .empty-icon {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: var(--bs-light-bg-subtle);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            color: var(--bs-secondary-color);
            margin: 0 auto 20px;
        }

        .empty-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--bs-heading-color);
            margin-bottom: 10px;
        }

        .empty-description {
            color: var(--bs-secondary-color);
            font-size: 14px;
            margin-bottom: 20px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .welcome-header {
                flex-direction: column;
                text-align: center;
            }

            .welcome-icon {
                margin-left: 0;
                margin-bottom: 15px;
            }

            .stat-header {
                flex-direction: column;
                text-align: center;
            }

            .stat-icon {
                margin-left: 0;
                margin-bottom: 10px;
            }

            .table-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .search-box {
                width: 100%;
            }

            .service-info {
                flex-direction: column;
                text-align: center;
            }

            td {
                min-width: 150px;
            }

            td:first-child {
                min-width: 200px;
            }
        }

        .edit-mode {
            background-color: rgba(102, 126, 234, 0.1);
            border: 2px solid #667eea;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                     <a href="{{ route('admin.home') }}">الرئيسية</a>
                </li>
                <li class="breadcrumb-item active">إدارة الخدمات</li>
            </ol>
        </nav>

        <div class="services-dashboard">
            <!-- Welcome Card -->
            <div class="welcome-card">
                <div class="welcome-header">
                    <div class="welcome-icon">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <div class="welcome-content">
                        <h3>إدارة الخدمات</h3>
                        <p>من هنا يمكنك إدارة جميع الخدمات المتاحة في النظام (عرض، إضافة، تعديل، تفعيل، تعطيل، حذف)</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <p class="mb-0">جميع العمليات متاحة في صفحة واحدة لتسهيل إدارة الخدمات</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="mt-3">
                            <span class="badge bg-light text-dark me-2">
                                <i class="fas fa-clock me-1"></i> {{ now()->format('H:i') }}
                            </span>
                            <span class="badge bg-light text-dark">
                                <i class="fas fa-calendar me-1"></i> {{ now()->translatedFormat('l، d F Y') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card total" onclick="filterByStatus('')">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <div>
                            <div class="stat-title">إجمالي الخدمات</div>
                            <div class="stat-description">جميع الخدمات المسجلة</div>
                        </div>
                    </div>
                    <div class="stat-value">{{ $totalServices }}</div>
                </div>

                <div class="stat-card active" onclick="filterByStatus('active')">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <div class="stat-title">خدمات نشطة</div>
                            <div class="stat-description">متاحة للعملاء</div>
                        </div>
                    </div>
                    <div class="stat-value">{{ $activeServices }}</div>
                </div>

                <div class="stat-card inactive" onclick="filterByStatus('inactive')">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="fas fa-ban"></i>
                        </div>
                        <div>
                            <div class="stat-title">خدمات غير نشطة</div>
                            <div class="stat-description">غير متاحة حالياً</div>
                        </div>
                    </div>
                    <div class="stat-value">{{ $inactiveServices }}</div>
                </div>
            </div>

            <!-- Add/Edit Form Card -->
            <div class="form-card" id="serviceForm">
                <div class="form-header">
                    <div class="form-icon">
                        <i class="fas {{ $editService ? 'fa-edit' : 'fa-plus' }}"></i>
                    </div>
                    <div class="form-title">
                        <h5>{{ $editService ? 'تعديل الخدمة' : 'إضافة خدمة جديدة' }}</h5>
                        <p>{{ $editService ? 'تعديل بيانات الخدمة' : 'أدخل بيانات الخدمة الجديدة' }}</p>
                    </div>
                </div>

                <form
                    action="{{ $editService ? route('admin.services.update', $editService->id) : route('admin.services.store') }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    @if ($editService)
                        @method('PUT')
                    @endif

                    <div class="row">
                        <div class="col-md-3 text-center">
                            <div class="mb-3">
                                <label class="form-label">صورة الخدمة</label>
                                <div class="image-preview mx-auto" onclick="document.getElementById('imageInput').click();">
                                    @if ($editService && $editService->image)
                                        <img src="{{ asset('storage/' . $editService->image) }}" alt="Service Image"
                                            id="previewImage">
                                    @else
                                        <div class="placeholder" id="imagePlaceholder">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                            <span>اختر صورة</span>
                                        </div>
                                        <img src="" alt="Preview" id="previewImage" style="display: none;">
                                    @endif
                                </div>
                                <input type="file" class="d-none" id="imageInput" name="image" accept="image/*"
                                    onchange="previewImage(this)">
                                <small class="text-muted d-block mt-2">PNG, JPG, GIF (حد أقصى 2MB)</small>
                            </div>
                        </div>

                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label required">اسم الخدمة</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name', $editService->name ?? '') }}"
                                        placeholder="أدخل اسم الخدمة" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="title" class="form-label required">العنوان</label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                                        id="title" name="title" value="{{ old('title', $editService->title ?? '') }}"
                                        placeholder="أدخل عنوان الخدمة" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="start_price" class="form-label required">سعر البداية (ريال)</label>
                                    <input type="text" class="form-control @error('start_price') is-invalid @enderror"
                                        id="start_price" name="start_price"
                                        value="{{ old('start_price', $editService->start_price ?? '') }}"
                                        placeholder="أدخل سعر البداية" required>
                                    @error('start_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-check mt-4">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                            value="1"
                                            {{ old('is_active', $editService->is_active ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            مفعل
                                        </label>
                                    </div>
                                </div>

                                <div class="col-12 text-start">
                                    @if ($editService)
                                        <a href="{{ route('admin.services.index') }}" class="btn-cancel">
                                            <i class="fas fa-times"></i>
                                            إلغاء التعديل
                                        </a>
                                    @endif
                                    <button type="submit" class="btn-save">
                                        <i class="fas {{ $editService ? 'fa-save' : 'fa-plus' }}"></i>
                                        {{ $editService ? 'حفظ التعديلات' : 'إضافة الخدمة' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Filter Card -->
            <div class="filter-card">
                <div class="filter-header">
                    <div class="filter-icon">
                        <i class="fas fa-filter"></i>
                    </div>
                    <div>
                        <h5 class="filter-title">فلترة الخدمات</h5>
                        <p class="filter-subtitle">تصفية النتائج حسب المعايير التالية</p>
                    </div>
                </div>

                <form method="GET" action="{{ route('admin.services.index') }}" class="filter-form" id="filterForm">
                    <div class="filter-group">
                        <label class="filter-label">البحث</label>
                        <input type="text" name="search" class="filter-select"
                            placeholder="اسم الخدمة أو العنوان..." value="{{ request('search') }}">
                    </div>

                    <div class="filter-group">
                        <label class="filter-label">الحالة</label>
                        <select name="status" class="filter-select" id="statusFilter">
                            <option value="">الكل</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط
                            </option>
                        </select>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn-filter">
                            <i class="fas fa-search me-2"></i> بحث
                        </button>
                        <a href="{{ route('admin.services.index') }}" class="btn-reset">
                            <i class="fas fa-redo me-2"></i> إعادة تعيين
                        </a>
                    </div>
                </form>
            </div>

            <!-- Services Table -->
            <div class="table-card">
                <div class="table-header">
                    <div class="table-title">
                        <div class="table-icon">
                            <i class="fas fa-list"></i>
                        </div>
                        <div>
                            <h5>قائمة الخدمات</h5>
                            <p>عرض جميع الخدمات المسجلة في النظام</p>
                        </div>
                    </div>

                    <div class="search-box">
                        <input type="text" id="searchInput" placeholder="بحث سريع..."
                            value="{{ request('search') }}">
                        <i class="fas fa-search"></i>
                    </div>
                </div>

                <div class="services-table">
                    <table>
                        <thead>
                            <tr>
                                <th>الخدمة</th>
                                <th>سعر البداية</th>
                                <th>الحالة</th>
                                <th>تاريخ الإضافة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($services as $service)
                                <tr>
                                    <td>
                                        <div class="service-info">
                                            <div class="service-image">
                                                @if ($service->image)
                                                    <img src="{{ asset('storage/' . $service->image) }}"
                                                        alt="{{ $service->name }}">
                                                @else
                                                    <i class="fas fa-cog"></i>
                                                @endif
                                            </div>
                                            <div class="service-details">
                                                <h6>{{ $service->name }}</h6>
                                                <div class="service-title">
                                                    <i class="fas fa-tag"></i>
                                                    {{ $service->title }}
                                                </div>
                                                <div class="service-price">
                                                    <i class="fas fa-money-bill-wave"></i>
                                                    {{ $service->start_price }} ريال
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <strong>{{ $service->start_price }}</strong>
                                        <small class="text-muted d-block">ريال</small>
                                    </td>

                                    <td>
                                        <span class="badge-status {{ $service->is_active ? 'active' : 'inactive' }}">
                                            <i
                                                class="fas {{ $service->is_active ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                            {{ $service->is_active ? 'نشط' : 'غير نشط' }}
                                        </span>
                                    </td>

                                    <td>
                                        <div>{{ $service->created_at->format('Y-m-d') }}</div>
                                        <small class="text-muted">{{ $service->created_at->diffForHumans() }}</small>
                                    </td>

                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.services.index', ['edit' => $service->id]) }}"
                                                class="btn-action edit" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            {{-- <button class="btn-action toggle"
                                                onclick="toggleStatus({{ $service->id }}, {{ $service->is_active ? 'true' : 'false' }})"
                                                title="{{ $service->is_active ? 'تعطيل' : 'تفعيل' }}">
                                                <i class="fas {{ $service->is_active ? 'fa-pause' : 'fa-play' }}"></i>
                                            </button> --}}

                                            <button class="btn-action delete"
                                                onclick="deleteService({{ $service->id }}, '{{ $service->name }}')"
                                                title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
                                        <div class="empty-state">
                                            <div class="empty-icon">
                                                <i class="fas fa-cogs"></i>
                                            </div>
                                            <h5 class="empty-title">لا يوجد خدمات</h5>
                                            <p class="empty-description">لم يتم إضافة أي خدمات حتى الآن. استخدم النموذج
                                                أعلاه لإضافة أول خدمة.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($services->hasPages())
                    <div class="pagination-info">
                        <div class="pagination-details">
                            عرض {{ $services->firstItem() }} - {{ $services->lastItem() }} من {{ $services->total() }}
                            خدمة
                        </div>
                        <div class="pagination-links">
                            {{ $services->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteServiceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">تأكيد الحذف</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>هل أنت متأكد من حذف الخدمة: <strong id="deleteServiceName"></strong>؟</p>
                    <p class="text-danger"><small>لا يمكن التراجع عن هذا الإجراء</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">حذف</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Preview image before upload
        function previewImage(input) {
            const preview = document.getElementById('previewImage');
            const placeholder = document.getElementById('imagePlaceholder');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    if (placeholder) placeholder.style.display = 'none';
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        // Search functionality
        let searchTimer;
        document.getElementById('searchInput').addEventListener('keyup', function() {
            clearTimeout(searchTimer);
            const searchValue = this.value;

            searchTimer = setTimeout(() => {
                const url = new URL(window.location.href);
                if (searchValue) {
                    url.searchParams.set('search', searchValue);
                } else {
                    url.searchParams.delete('search');
                }
                url.searchParams.delete('edit');
                window.location.href = url.toString();
            }, 500);
        });

        // Filter by status from stats cards
        function filterByStatus(status) {
            const url = new URL(window.location.href);
            if (status) {
                url.searchParams.set('status', status);
            } else {
                url.searchParams.delete('status');
            }
            url.searchParams.delete('search');
            url.searchParams.delete('edit');
            window.location.href = url.toString();
        }

        // Toggle service status
        function toggleStatus(serviceId, isActive) {
            const action = isActive ? 'تعطيل' : 'تفعيل';

            Swal.fire({
                title: `${action} الخدمة`,
                text: `هل أنت متأكد من ${action} هذه الخدمة؟`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: isActive ? '#dc3545' : '#198754',
                cancelButtonColor: '#3085d6',
                confirmButtonText: `نعم، ${action}`,
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/admin/services/${serviceId}/toggle-status`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        beforeSend: function() {
                            Swal.fire({
                                title: 'جاري التغيير...',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'تم التغيير!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ!',
                                text: xhr.responseJSON?.message || 'حدث خطأ أثناء تغيير الحالة'
                            });
                        }
                    });
                }
            });
        }

        // Delete service
        function deleteService(serviceId, serviceName) {
            document.getElementById('deleteServiceName').textContent = serviceName;
            document.getElementById('deleteForm').action = `/admin/services/${serviceId}`;
            $('#deleteServiceModal').modal('show');
        }

        // Show success message from session
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'تم بنجاح!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        // Auto-hide flash messages
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);

        // Scroll to form if in edit mode
        @if ($editService)
            window.location.hash = '#serviceForm';
        @endif
    </script>
@endsection
