@extends('Admin.layout.master')

@section('title', isset($banner) ? 'تعديل البانر' : 'إضافة بانر جديد')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">

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

        .form-card {
            background: var(--dark-card);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .form-header {
            background: var(--primary-gradient);
            color: white;
            padding: 20px;
            border-radius: 15px 15px 0 0;
            margin: -30px -30px 30px -30px;
        }

        .form-section {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.05);
        }

        .form-section h5 {
            color: var(--primary-color);
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .image-upload-box {
            border: 2px dashed rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.05);
        }

        .image-upload-box:hover {
            border-color: var(--primary-color);
            background: rgba(105, 108, 255, 0.1);
        }

        .image-upload-box i {
            font-size: 48px;
            color: var(--text-muted);
            margin-bottom: 15px;
        }

        .image-upload-box input {
            display: none;
        }

        .image-preview {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            margin-top: 15px;
            display: none;
            border: 2px solid rgba(255, 255, 255, 0.1);
        }

        .mobile-image-preview {
            width: 150px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            margin-top: 10px;
            border: 2px solid rgba(255, 255, 255, 0.1);
        }

        .badge-custom {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .badge-active {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }

        .badge-inactive {
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
            color: white;
        }

        .type-card {
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            background: rgba(255, 255, 255, 0.05);
            height: 100%;
        }

        .type-card:hover {
            border-color: var(--primary-color);
            transform: translateY(-5px);
            background: rgba(105, 108, 255, 0.1);
        }

        .type-card.active {
            border-color: var(--primary-color);
            background: rgba(105, 108, 255, 0.2);
            box-shadow: 0 5px 15px rgba(105, 108, 255, 0.3);
        }

        .type-icon {
            font-size: 32px;
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .type-desc {
            color: var(--text-muted);
            font-size: 12px;
            margin-top: 10px;
        }

        .toggle-switch {
            position: relative;
            width: 50px;
            height: 26px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.toggle-slider {
            background-color: var(--primary-color);
        }

        input:checked+.toggle-slider:before {
            transform: translateX(24px);
        }

        .date-input-group {
            position: relative;
        }

        .date-input-group .form-control {
            padding-left: 40px;
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .date-input-group .form-control:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--primary-color);
            color: #fff;
        }

        .date-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            z-index: 4;
        }

        .preview-container {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 20px;
            margin-top: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .preview-item {
            width: 100%;
            height: 150px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .preview-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .items-container {
            margin-top: 30px;
        }

        .item-card {
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            background: rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
        }

        .item-card:hover {
            border-color: var(--primary-color);
            background: rgba(105, 108, 255, 0.1);
        }

        .item-image {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid rgba(255, 255, 255, 0.1);
        }

        .item-actions {
            display: flex;
            gap: 10px;
        }

        .btn-action {
            width: 32px;
            height: 32px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
        }

        .empty-items {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-muted);
        }

        .empty-items i {
            font-size: 48px;
            margin-bottom: 15px;
            color: rgba(255, 255, 255, 0.1);
        }

        .select2-container--default .select2-selection--multiple,
        .select2-container--default .select2-selection--single {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 0.375rem;
            min-height: 38px;
            color: #fff;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 15px;
            padding: 2px 10px;
        }

        .select2-container--default .select2-selection__rendered {
            color: #fff !important;
        }

        .grid-settings {
            background: rgba(255, 255, 255, 0.05);
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            border: 2px dashed rgba(255, 255, 255, 0.1);
        }

        .slider-settings {
            background: rgba(255, 255, 255, 0.05);
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            border: 2px dashed rgba(255, 255, 255, 0.1);
        }

        .help-text {
            color: var(--text-muted);
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }

        .required-field::after {
            content: " *";
            color: var(--danger-color);
        }

        .form-control {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--primary-color);
            color: #fff;
            box-shadow: 0 0 0 0.25rem rgba(105, 108, 255, 0.25);
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .input-group-text {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .form-check-input {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.3);
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .form-check-label {
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

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.3);
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            color: white;
        }

        .breadcrumb {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 15px;
        }

        .breadcrumb-item a {
            color: var(--primary-color);
            text-decoration: none;
        }

        .breadcrumb-item.active {
            color: rgba(255, 255, 255, 0.7);
        }

        @media (max-width: 768px) {
            .type-card {
                margin-bottom: 15px;
            }

            .item-card {
                flex-direction: column;
            }

            .item-image {
                margin-bottom: 15px;
            }

            .item-actions {
                justify-content: center;
            }
        }

        /* Modal Styles */
        .modal-content {
            background: var(--dark-card);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .modal-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .modal-title {
            color: #fff;
        }

        .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        .modal-footer {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Category Options */
        .category-options {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .category-option {
            flex: 1;
            text-align: center;
            padding: 15px;
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.05);
        }

        .category-option:hover {
            border-color: var(--primary-color);
            background: rgba(105, 108, 255, 0.1);
        }

        .category-option.active {
            border-color: var(--primary-color);
            background: rgba(105, 108, 255, 0.2);
        }

        .category-option i {
            font-size: 24px;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .category-select {
            display: none;
        }

        .category-select.show {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
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
                    <a href="{{ route('admin.banners.index') }}">البنرات</a>
                </li>
                <li class="breadcrumb-item active">{{ isset($banner) ? 'تعديل' : 'إضافة' }}</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-12">
                <div class="form-card">
                    <div class="form-header">
                        <h4 class="mb-0">{{ isset($banner) ? 'تعديل البانر' : 'إضافة بانر جديد' }}</h4>
                        <small>املأ النموذج {{ isset($banner) ? 'لتعديل البانر' : 'لإضافة بانر جديد' }} إلى النظام</small>
                    </div>

                    <form
                        action="{{ isset($banner) ? route('admin.banners.update', $banner) : route('admin.banners.store') }}"
                        method="POST" id="bannerForm" enctype="multipart/form-data">
                        @csrf
                        @if (isset($banner))
                            @method('PUT')
                        @endif

                        <!-- القسم والموقع -->
                        <div class="form-section">
                            <h5><i class="fas fa-map-marker-alt me-2"></i>القسم والموقع</h5>

                            <div class="category-options">
                                <div class="category-option {{ old('category_type', isset($banner) && $banner->category_id ? 'specific' : 'main') == 'main' ? 'active' : '' }}"
                                    data-category-type="main">
                                    <i class="fas fa-home"></i>
                                    <h6 class="mb-2">الرئيسية</h6>
                                    <p class="type-desc">عرض البانر في الصفحة الرئيسية</p>
                                </div>
                                <div class="category-option {{ old('category_type', isset($banner) && $banner->category_id ? 'specific' : 'main') == 'specific' ? 'active' : '' }}"
                                    data-category-type="specific">
                                    <i class="fas fa-tag"></i>
                                    <h6 class="mb-2">قسم محدد</h6>
                                    <p class="type-desc">عرض البانر في قسم معين</p>
                                </div>
                            </div>

                            <input type="hidden" name="category_type" id="category_type"
                                value="{{ old('category_type', isset($banner) && $banner->category_id ? 'specific' : 'main') }}">

                            <div class="category-select {{ old('category_type', isset($banner) && $banner->category_id ? 'specific' : 'main') == 'specific' ? 'show' : '' }}"
                                id="specificCategorySelect">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="category_id" class="form-label required-field">اختر القسم</label>
                                        <select class="form-control select2" id="category_id" name="category_id">
                                            <option value="">-- اختر قسم من القائمة --</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ old('category_id', $banner->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="help-text">سيتم عرض البانر في صفحة هذا القسم فقط</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- المعلومات الأساسية -->
                        <div class="form-section">
                            <h5><i class="fas fa-info-circle me-2"></i>المعلومات الأساسية</h5>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="title" class="form-label required-field">عنوان البانر</label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                                        id="title" name="title" value="{{ old('title', $banner->title ?? '') }}"
                                        required placeholder="أدخل عنوان واضح للبانر">
                                    @error('title')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <span class="help-text">العنوان سيساعدك في التعرف على البانر لاحقاً</span>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="section_order" class="form-label required-field">ترتيب العرض</label>
                                    <input type="number" class="form-control @error('section_order') is-invalid @enderror"
                                        id="section_order" name="section_order"
                                        value="{{ old('section_order', $banner->section_order ?? 1) }}" required
                                        min="1">
                                    @error('section_order')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <span class="help-text">الأرقام الأقل تظهر أولاً</span>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label required-field">حالة البانر</label>
                                    <div class="d-flex align-items-center mt-2">
                                        <label class="toggle-switch me-3">
                                            <input type="hidden" name="is_active" value="0">
                                            <input type="checkbox" name="is_active" value="1"
                                                {{ old('is_active', $banner->is_active ?? true) ? 'checked' : '' }}>
                                            <span class="toggle-slider"></span>
                                        </label>
                                        <span
                                            class="badge-custom {{ old('is_active', $banner->is_active ?? true) ? 'badge-active' : 'badge-inactive' }}">
                                            {{ old('is_active', $banner->is_active ?? true) ? 'نشط' : 'غير نشط' }}
                                        </span>
                                    </div>
                                    <span class="help-text">يمكنك تعطيل البانر لاحقاً</span>
                                </div>
                            </div>
                        </div>

                        <!-- نوع البانر -->
                        <div class="form-section">
                            <h5><i class="fas fa-sliders-h me-2"></i>نوع البانر</h5>

                            <div class="row">
                                @foreach ($bannerTypes as $type)
                                    @php
                                        $typeNames = [
                                            'slider' => 'سلايدر',
                                            'grid' => 'شبكة',
                                            'static' => 'ثابت',
                                            'category_slider' => 'أقسام',
                                        ];

                                        $typeIcons = [
                                            'slider' => 'fa-sliders-h',
                                            'grid' => 'fa-th-large',
                                            'static' => 'fa-image',
                                            'category_slider' => 'fa-tags',
                                        ];
                                    @endphp

                                    <div class="col-md-3 mb-3">
                                        <div class="type-card {{ old('banner_type_id', $banner->banner_type_id ?? '') == $type->id ? 'active' : '' }}"
                                            data-type-id="{{ $type->id }}" data-type-name="{{ $type->name }}">
                                            <div class="type-icon">
                                                <i class="fas {{ $typeIcons[$type->name] ?? 'fa-image' }}"></i>
                                            </div>
                                            <h6 class="mb-2">{{ $typeNames[$type->name] ?? $type->name }}</h6>
                                            <p class="type-desc">{{ $type->description }}</p>
                                        </div>
                                        <input type="radio" name="banner_type_id" value="{{ $type->id }}"
                                            id="type_{{ $type->id }}"
                                            {{ old('banner_type_id', $banner->banner_type_id ?? '') == $type->id ? 'checked' : '' }}
                                            class="d-none">
                                    </div>
                                @endforeach
                            </div>
                            <input type="hidden" id="selected_type"
                                value="{{ old('banner_type_id', $banner->banner_type_id ?? '') }}">
                        </div>

                        <!-- إعدادات الشبكة (تظهر فقط للنوع grid) -->
                        <div class="grid-settings" id="gridSettings" style="display: none;">
                            <h6><i class="fas fa-cog me-2"></i>إعدادات الشبكة</h6>

                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="desktop_columns" class="form-label">عدد الأعمدة (كمبيوتر)</label>
                                    <input type="number" class="form-control" id="desktop_columns"
                                        name="desktop_columns"
                                        value="{{ old('desktop_columns', $banner->gridLayout->desktop_columns ?? 3) }}"
                                        min="1" max="6">
                                    <span class="help-text">عدد الأعمدة في شاشات الكمبيوتر</span>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="tablet_columns" class="form-label">عدد الأعمدة (تابلت)</label>
                                    <input type="number" class="form-control" id="tablet_columns" name="tablet_columns"
                                        value="{{ old('tablet_columns', $banner->gridLayout->tablet_columns ?? 2) }}"
                                        min="1" max="4">
                                    <span class="help-text">عدد الأعمدة في شاشات التابلت</span>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="mobile_columns" class="form-label">عدد الأعمدة (موبايل)</label>
                                    <input type="number" class="form-control" id="mobile_columns" name="mobile_columns"
                                        value="{{ old('mobile_columns', $banner->gridLayout->mobile_columns ?? 1) }}"
                                        min="1" max="2">
                                    <span class="help-text">عدد الأعمدة في شاشات الموبايل</span>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="grid_type" class="form-label">نوع الشبكة</label>
                                    <select class="form-control" id="grid_type" name="grid_type">
                                        <option value="responsive"
                                            {{ old('grid_type', $banner->gridLayout->grid_type ?? '') == 'responsive' ? 'selected' : '' }}>
                                            متجاوب</option>
                                        <option value="fixed"
                                            {{ old('grid_type', $banner->gridLayout->grid_type ?? '') == 'fixed' ? 'selected' : '' }}>
                                            ثابت</option>
                                    </select>
                                    <span class="help-text">المتجاوب يناسب جميع الشاشات</span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="row_gap" class="form-label">المسافة بين الصفوف</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="row_gap" name="row_gap"
                                            value="{{ old('row_gap', $banner->gridLayout->row_gap ?? 20) }}"
                                            min="0" max="100">
                                        <span class="input-group-text">بكسل</span>
                                    </div>
                                    <span class="help-text">المسافة الرأسية بين العناصر</span>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="column_gap" class="form-label">المسافة بين الأعمدة</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="column_gap" name="column_gap"
                                            value="{{ old('column_gap', $banner->gridLayout->column_gap ?? 20) }}"
                                            min="0" max="100">
                                        <span class="input-group-text">بكسل</span>
                                    </div>
                                    <span class="help-text">المسافة الأفقية بين العناصر</span>
                                </div>
                            </div>
                        </div>

                        <!-- إعدادات السلايدر (تظهر فقط للنوع slider) -->
                        <div class="slider-settings" id="sliderSettings" style="display: none;">
                            <h6><i class="fas fa-cog me-2"></i>إعدادات السلايدر</h6>

                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="autoplay" value="0">
                                        <input class="form-check-input" type="checkbox" id="autoplay" name="autoplay"
                                            value="1"
                                            {{ old('autoplay', $banner->sliderSetting->autoplay ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="autoplay">التشغيل التلقائي</label>
                                    </div>
                                    <span class="help-text">التقدم التلقائي بين الشرائح</span>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="arrows" value="0">
                                        <input class="form-check-input" type="checkbox" id="arrows" name="arrows"
                                            value="1"
                                            {{ old('arrows', $banner->sliderSetting->arrows ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="arrows">أزرار التنقل</label>
                                    </div>
                                    <span class="help-text">عرض أزرار التالي والسابق</span>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="dots" value="0">
                                        <input class="form-check-input" type="checkbox" id="dots" name="dots"
                                            value="1"
                                            {{ old('dots', $banner->sliderSetting->dots ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="dots">النقاط</label>
                                    </div>
                                    <span class="help-text">عرض نقاط التنقل أسفل السلايدر</span>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="infinite" value="0">
                                        <input class="form-check-input" type="checkbox" id="infinite" name="infinite"
                                            value="1"
                                            {{ old('infinite', $banner->sliderSetting->infinite ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="infinite">لانهائي</label>
                                    </div>
                                    <span class="help-text">التنقل اللانهائي بين الشرائح</span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="autoplay_speed" class="form-label">سرعة التشغيل</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="autoplay_speed"
                                            name="autoplay_speed"
                                            value="{{ old('autoplay_speed', $banner->sliderSetting->autoplay_speed ?? 3000) }}"
                                            min="1000" max="10000" step="500">
                                        <span class="input-group-text">ملي ثانية</span>
                                    </div>
                                    <span class="help-text">المدة بين الشرائح (3000 = 3 ثواني)</span>
                                </div>
                            </div>
                        </div>

                        <!-- الفترة الزمنية -->
                        <div class="form-section">
                            <h5><i class="fas fa-calendar-alt me-2"></i>الفترة الزمنية</h5>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="start_date" class="form-label">تاريخ البدء</label>
                                    <div class="date-input-group">
                                        <i class="fas fa-calendar date-icon"></i>
                                        <input type="datetime-local"
                                            class="form-control @error('start_date') is-invalid @enderror" id="start_date"
                                            name="start_date"
                                            value="{{ old('start_date', isset($banner->start_date) ? $banner->start_date->format('Y-m-d\TH:i') : '') }}">
                                    </div>
                                    @error('start_date')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <span class="help-text">تاريخ بدء عرض البانر (اختياري)</span>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="end_date" class="form-label">تاريخ الانتهاء</label>
                                    <div class="date-input-group">
                                        <i class="fas fa-calendar date-icon"></i>
                                        <input type="datetime-local"
                                            class="form-control @error('end_date') is-invalid @enderror" id="end_date"
                                            name="end_date"
                                            value="{{ old('end_date', isset($banner->end_date) ? $banner->end_date->format('Y-m-d\TH:i') : '') }}">
                                    </div>
                                    @error('end_date')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <span class="help-text">تاريخ إيقاف عرض البانر (اختياري)</span>
                                </div>
                            </div>

                            <div class="form-check mt-3">
                                <input type="hidden" name="permanent" value="0">
                                <input class="form-check-input" type="checkbox" id="permanent" name="permanent"
                                    value="1"
                                    {{ old('permanent') || (!isset($banner->start_date) && !isset($banner->end_date)) ? 'checked' : '' }}>
                                <label class="form-check-label" for="permanent">
                                    دائم (بدون فترة محددة)
                                </label>
                            </div>
                        </div>

                        <!-- العناصر (للأنواع التي تدعم عناصر متعددة) -->
                        <div class="form-section" id="itemsSection">
                            <h5><i class="fas fa-layer-group me-2"></i>عناصر البانر</h5>

                            <div id="itemsContainer" class="items-container">
                                @if (isset($banner) && $banner->items->count() > 0)
                                    @foreach ($banner->items->sortBy('item_order') as $item)
                                        <div class="item-card d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center">
                                                @if ($item->image_url)
                                                    <img src="{{ get_user_image($item->image_url) }}"
                                                        alt="{{ $item->image_alt }}" class="item-image me-3">
                                                @endif
                                                <div>
                                                    <h6 class="mb-1">{{ $item->image_alt ?? 'بدون عنوان' }}</h6>
                                                    <small class="text-muted">
                                                        الترتيب: {{ $item->item_order }} |
                                                        {{ $item->is_active ? 'نشط' : 'غير نشط' }}
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="item-actions">
                                                <button type="button" class="btn btn-action btn-warning edit-item"
                                                    data-id="{{ $item->id }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-action btn-danger delete-item"
                                                    data-id="{{ $item->id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="empty-items">
                                        <i class="fas fa-image"></i>
                                        <p>لا توجد عناصر مضافة</p>
                                        <small class="help-text">يمكنك إضافة عناصر بعد حفظ البانر</small>
                                    </div>
                                @endif
                            </div>

                            @if (isset($banner))
                                <button type="button" class="btn btn-primary mt-3" id="addItemBtn">
                                    <i class="fas fa-plus me-2"></i>إضافة عنصر جديد
                                </button>
                            @else
                                <div class="alert alert-info mt-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    يمكنك إضافة عناصر البانر بعد حفظ البانر أولاً
                                </div>
                            @endif
                        </div>

                        <!-- أزرار التحكم -->
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <div>
                                <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>إلغاء
                                </a>
                            </div>

                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>{{ isset($banner) ? 'تحديث' : 'حفظ' }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- نافذة إضافة/تعديل العناصر -->
    <div class="modal fade" id="itemModal" tabindex="-1" role="dialog" aria-labelledby="itemModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="itemModalLabel">إضافة عنصر جديد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                </div>
                <div class="modal-body">
                    <form id="itemForm">
                        @csrf
                        <input type="hidden" name="banner_id" value="{{ $banner->id ?? '' }}">
                        <input type="hidden" name="item_id" id="item_id">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="item_order" class="form-label required-field">ترتيب العنصر</label>
                                <input type="number" class="form-control" id="item_order" name="item_order"
                                    value="{{ old('item_order', isset($banner) ? $banner->items->count() + 1 : 1) }}"
                                    required min="1">
                                <span class="help-text">الترتيب في العرض</span>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">حالة العنصر</label>
                                <div class="d-flex align-items-center mt-2">
                                    <label class="toggle-switch me-3">
                                        <input type="hidden" name="is_active" value="0">
                                        <input type="checkbox" name="is_active" id="item_is_active" value="1"
                                            checked>
                                        <span class="toggle-slider"></span>
                                    </label>
                                    <span class="badge-custom badge-active">نشط</span>
                                </div>
                                <span class="help-text">تفعيل أو تعطيل العنصر</span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="image" class="form-label required-field">صورة العنصر</label>
                                <div class="image-upload-box" id="imageUploadBox">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <p>انقر لرفع صورة</p>
                                    <p class="text-muted">الحجم المقترح: 1200x400 بكسل</p>
                                    <img id="imagePreview" class="image-preview" alt="صورة المعاينة">
                                    <input type="file" id="image" name="image" accept="image/*">
                                </div>
                                <span class="help-text">الصورة الأساسية للعرض على جميع الشاشات</span>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="mobile_image" class="form-label">صورة الموبايل (اختياري)</label>
                                <div class="image-upload-box" id="mobileImageUploadBox">
                                    <i class="fas fa-mobile-alt"></i>
                                    <p>انقر لرفع صورة للموبايل</p>
                                    <p class="text-muted">الحجم المقترح: 600x200 بكسل</p>
                                    <img id="mobileImagePreview" class="image-preview" alt="صورة الموبايل المعاينة">
                                    <input type="file" id="mobile_image" name="mobile_image" accept="image/*">
                                </div>
                                <span class="help-text">صورة خاصة للعرض على الشاشات الصغيرة</span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="image_alt" class="form-label">النص البديل للصورة</label>
                                <input type="text" class="form-control" id="image_alt" name="image_alt"
                                    placeholder="وصف مختصر للصورة">
                                <span class="help-text">يظهر عند عدم تحميل الصورة</span>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="link_url" class="form-label">رابط التوجيه (URL)</label>
                                <input type="url" class="form-control" id="link_url" name="link_url"
                                    placeholder="https://example.com">
                                <span class="help-text">الرابط الذي سيتم توجيه المستخدم إليه عند النقر</span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="link_target" class="form-label">فتح الرابط في</label>
                                <select class="form-control" id="link_target" name="link_target">
                                    <option value="_self">نفس النافذة</option>
                                    <option value="_blank">نافذة جديدة</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch mt-4">
                                    <input type="hidden" name="is_link_active" value="0">
                                    <input class="form-check-input" type="checkbox" id="is_link_active"
                                        name="is_link_active" value="1" checked>
                                    <label class="form-check-label" for="is_link_active">تفعيل الرابط</label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="product_id" class="form-label">ربط بمنتج (اختياري)</label>
                                <select class="form-control select2" id="product_id" name="product_id">
                                    <option value="">-- اختر منتج --</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="category_id_item" class="form-label">ربط بقسم (اختياري)</label>
                                <select class="form-control select2" id="category_id_item" name="category_id">
                                    <option value="">-- اختر قسم --</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="tag_text" class="form-label">نص الوسم (اختياري)</label>
                                <input type="text" class="form-control" id="tag_text" name="tag_text"
                                    placeholder="مثال: جديد، مميز">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="tag_color" class="form-label">لون النص</label>
                                <input type="color" class="form-control" id="tag_color" name="tag_color"
                                    value="#ffffff">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="tag_bg_color" class="form-label">لون الخلفية</label>
                                <input type="color" class="form-control" id="tag_bg_color" name="tag_bg_color"
                                    value="#696cff">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="promo_codes" class="form-label">رموز التخفيض (اختياري)</label>
                                <select class="form-control select2" id="promo_codes" name="promo_codes[]" multiple>
                                    @foreach ($promoCodes as $promo)
                                        <option value="{{ $promo->id }}">{{ $promo->code }} -
                                            {{ $promo->discount_percentage }}%</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-primary" id="saveItemBtn">حفظ العنصر</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        // ملف JavaScript منفصل لتفادي المشاكل
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Banner form loaded - All local resources');

            // تعطيل أي محاولات جلب من روابط خارجية
            blockExternalRequests();

            // تهيئة Select2 مع تأخير بسيط لضمان تحميل DOM
            setTimeout(function() {
                initSelect2();
            }, 100);

            // تهيئة الأحداث بعد التأكد من تحميل DOM
            setTimeout(function() {
                initEvents();
                initPageState();
            }, 200);
        });

        // دالة لحظر الطلبات الخارجية
        function blockExternalRequests() {
            if (typeof window.fetch === 'function') {
                const originalFetch = window.fetch;
                window.fetch = function() {
                    const url = arguments[0];
                    if (typeof url === 'string' && url.includes('seda.codeella.com')) {
                        console.warn('Blocked external request to:', url);
                        return Promise.reject(new Error('External requests are blocked'));
                    }
                    return originalFetch.apply(this, arguments);
                };
            }

            // منع XMLHttpRequest كذلك
            if (typeof XMLHttpRequest !== 'undefined') {
                const originalOpen = XMLHttpRequest.prototype.open;
                XMLHttpRequest.prototype.open = function() {
                    const url = arguments[1];
                    if (typeof url === 'string' && url.includes('seda.codeella.com')) {
                        console.warn('Blocked XMLHttpRequest to:', url);
                        throw new Error('External requests are blocked');
                    }
                    return originalOpen.apply(this, arguments);
                };
            }
        }

        // تهيئة Select2
        function initSelect2() {
            const select2Elements = document.querySelectorAll('.select2');
            if (select2Elements.length && typeof $.fn.select2 !== 'undefined') {
                select2Elements.forEach(function(element) {
                    $(element).select2({
                        placeholder: 'اختر من القائمة',
                        allowClear: true,
                        language: {
                            noResults: function() {
                                return "لا توجد نتائج";
                            }
                        },
                        dropdownParent: $(element).closest('.modal').length ? $('#itemModal') : document
                            .body
                    });
                });
            }
        }

        // تهيئة الأحداث
        function initEvents() {
            // اختيار القسم - باستخدام event delegation
            document.addEventListener('click', function(e) {
                // اختيار القسم
                if (e.target.closest('.category-option')) {
                    const categoryOption = e.target.closest('.category-option');
                    const categoryType = categoryOption.dataset.categoryType;
                    handleCategorySelection(categoryType);
                }

                // اختيار نوع البانر
                if (e.target.closest('.type-card')) {
                    const typeCard = e.target.closest('.type-card');
                    const typeId = typeCard.dataset.typeId;
                    const typeName = typeCard.dataset.typeName;
                    handleTypeSelection(typeId, typeName);
                }

                // معاينة الصور
                if (e.target.closest('#imageUploadBox')) {
                    document.getElementById('image').click();
                }

                if (e.target.closest('#mobileImageUploadBox')) {
                    document.getElementById('mobile_image').click();
                }

                // إضافة عنصر جديد
                if (e.target.id === 'addItemBtn' || e.target.closest('#addItemBtn')) {
                    openItemModal();
                }

                // حفظ العنصر
                if (e.target.id === 'saveItemBtn') {
                    saveItem();
                }

                // تعديل العنصر
                if (e.target.closest('.edit-item')) {
                    const button = e.target.closest('.edit-item');
                    const itemId = button.dataset.id;
                    editItem(itemId);
                }

                // حذف العنصر
                if (e.target.closest('.delete-item')) {
                    const button = e.target.closest('.delete-item');
                    const itemId = button.dataset.id;
                    deleteItem(itemId);
                }
            });

            // تغييرات في الحقول
            document.getElementById('permanent')?.addEventListener('change', handlePermanentChange);
            document.querySelector('input[name="is_active"]')?.addEventListener('change', handleBannerStatusChange);

            // تغييرات في ملفات الصور
            document.getElementById('image')?.addEventListener('change', function(e) {
                previewImage(this, '#imagePreview');
            });

            document.getElementById('mobile_image')?.addEventListener('change', function(e) {
                previewImage(this, '#mobileImagePreview');
            });

            // إرسال النموذج الرئيسي
            const bannerForm = document.getElementById('bannerForm');
            if (bannerForm) {
                bannerForm.addEventListener('submit', handleBannerFormSubmit);
            }

            // إغلاق المودال
            const itemModal = document.getElementById('itemModal');
            if (itemModal) {
                itemModal.addEventListener('hidden.bs.modal', function() {
                    resetItemForm();
                });
            }
        }

        // معالجة اختيار القسم
        function handleCategorySelection(categoryType) {
            const options = document.querySelectorAll('.category-option');
            const categoryTypeInput = document.getElementById('category_type');
            const categorySelect = document.getElementById('specificCategorySelect');
            const categoryIdSelect = document.getElementById('category_id');

            options.forEach(option => option.classList.remove('active'));
            event.target.closest('.category-option').classList.add('active');
            categoryTypeInput.value = categoryType;

            if (categoryType === 'specific') {
                categorySelect.classList.add('show');
                if (categoryIdSelect) categoryIdSelect.required = true;
            } else {
                categorySelect.classList.remove('show');
                if (categoryIdSelect) {
                    categoryIdSelect.required = false;
                    categoryIdSelect.value = '';
                }
            }
        }

        // معالجة اختيار النوع
        function handleTypeSelection(typeId, typeName) {
            const typeCards = document.querySelectorAll('.type-card');
            const typeInput = document.querySelector(`#type_${typeId}`);
            const selectedTypeInput = document.getElementById('selected_type');
            const gridSettings = document.getElementById('gridSettings');
            const sliderSettings = document.getElementById('sliderSettings');

            typeCards.forEach(card => card.classList.remove('active'));
            event.target.closest('.type-card').classList.add('active');

            if (typeInput) typeInput.checked = true;
            if (selectedTypeInput) selectedTypeInput.value = typeId;

            // إظهار/إخفاء الإعدادات حسب النوع
            if (gridSettings) gridSettings.style.display = 'none';
            if (sliderSettings) sliderSettings.style.display = 'none';

            if (typeName === 'grid' && gridSettings) {
                gridSettings.style.display = 'block';
            } else if (typeName === 'slider' && sliderSettings) {
                sliderSettings.style.display = 'block';
            }
        }

        // معالجة تغيير الحالة الدائمة
        function handlePermanentChange() {
            const isPermanent = this.checked;
            const startDate = document.getElementById('start_date');
            const endDate = document.getElementById('end_date');

            if (startDate) startDate.disabled = isPermanent;
            if (endDate) endDate.disabled = isPermanent;

            if (isPermanent) {
                if (startDate) startDate.value = '';
                if (endDate) endDate.value = '';
            }
        }

        // معالجة تغيير حالة البانر
        function handleBannerStatusChange() {
            const isChecked = this.checked;
            const badge = this.closest('.d-flex').querySelector('.badge-custom');

            if (badge) {
                badge.classList.remove(isChecked ? 'badge-inactive' : 'badge-active');
                badge.classList.add(isChecked ? 'badge-active' : 'badge-inactive');
                badge.textContent = isChecked ? 'نشط' : 'غير نشط';
            }
        }

        // معاينة الصور
        function previewImage(input, previewSelector) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.querySelector(previewSelector);
                    if (preview) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    }
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // إرسال النموذج الرئيسي
        function handleBannerFormSubmit(e) {
            e.preventDefault();

            // التحقق من الحقول المطلوبة
            const title = document.getElementById('title')?.value.trim();
            const categoryType = document.getElementById('category_type')?.value;
            const categoryId = document.getElementById('category_id')?.value;

            if (!title) {
                showError('يرجى إدخال عنوان البانر');
                document.getElementById('title')?.focus();
                return false;
            }

            if (categoryType === 'specific' && !categoryId) {
                showError('يرجى اختيار قسم للبانر');
                return false;
            }

            // إظهار تحميل
            Swal.fire({
                title: 'جاري الحفظ...',
                text: 'يرجى الانتظار',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            // إرسال النموذج باستخدام fetch بدلاً من jQuery
            const formData = new FormData(this);

            fetch(this.action, {
                    method: this.method,
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json().then(data => ({
                    ok: response.ok,
                    data
                })))
                .then(({
                    ok,
                    data
                }) => {
                    if (ok) {
                        Swal.fire({
                            icon: 'success',
                            title: 'تم!',
                            text: data.message || 'تم حفظ البانر بنجاح',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(function() {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                window.location.href = window.bannerIndexUrl || '/admin/banners';
                            }
                        });
                    } else {
                        throw data;
                    }
                })
                .catch(error => {
                    let errorMessage = 'حدث خطأ أثناء الحفظ';

                    if (error && error.errors) {
                        errorMessage = '';
                        for (const key in error.errors) {
                            errorMessage += error.errors[key].join('<br>') + '<br>';
                        }
                    } else if (error && error.message) {
                        errorMessage = error.message;
                    }

                    showError(errorMessage);
                });
        }

        // فتح مودال العنصر
        function openItemModal() {
            const modalTitle = document.getElementById('itemModalLabel');
            const itemForm = document.getElementById('itemForm');
            const imagePreview = document.getElementById('imagePreview');
            const mobileImagePreview = document.getElementById('mobileImagePreview');

            if (modalTitle) modalTitle.textContent = 'إضافة عنصر جديد';
            if (itemForm) itemForm.reset();

            // إعادة تعيين الحقول المخفية
            const itemIdInput = document.getElementById('item_id');
            if (itemIdInput) itemIdInput.value = '';

            // إعادة تعيين الـ hidden fields
            const hiddenFields = itemForm.querySelectorAll('input[type="hidden"]');
            hiddenFields.forEach(field => {
                if (field.name === 'is_active' || field.name === 'is_link_active') {
                    field.value = '0';
                }
            });

            // إعادة تعيين checkboxes
            const checkboxes = itemForm.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
                if (checkbox.name === 'is_active' || checkbox.name === 'is_link_active') {
                    checkbox.value = '1';
                }
            });

            // إخفاء معاينات الصور
            if (imagePreview) {
                imagePreview.style.display = 'none';
                imagePreview.src = '';
            }
            if (mobileImagePreview) {
                mobileImagePreview.style.display = 'none';
                mobileImagePreview.src = '';
            }

            // إعادة تعيين Select2
            const select2Elements = document.querySelectorAll('#itemForm .select2');
            select2Elements.forEach(element => {
                if ($(element).data('select2')) {
                    $(element).val(null).trigger('change');
                }
            });

            // تعيين ترتيب العنصر
            const itemOrderInput = document.getElementById('item_order');
            if (itemOrderInput) {
                const itemCount = parseInt(document.getElementById('itemsContainer')?.querySelectorAll('.item-card')
                    .length) || 0;
                itemOrderInput.value = itemCount + 1;
            }

            // فتح المودال باستخدام Bootstrap
            const itemModal = new bootstrap.Modal(document.getElementById('itemModal'));
            itemModal.show();
        }

        // حفظ العنصر
        function saveItem() {
            const formElement = document.getElementById('itemForm');
            if (!formElement) return;

            const formData = new FormData(formElement);
            const itemId = document.getElementById('item_id')?.value;
            const imageInput = document.getElementById('image');

            // التحقق من الصورة (فقط عند الإضافة)
            if (!itemId && (!imageInput || !imageInput.files || imageInput.files.length === 0)) {
                showError('يرجى رفع صورة للعنصر');
                return false;
            }

            Swal.fire({
                title: 'جاري الحفظ...',
                text: 'يرجى الانتظار',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            const url = itemId ?
                window.itemUpdateUrl.replace(':id', itemId) :
                window.itemStoreUrl;
            const method = itemId ? 'PUT' : 'POST';

            // إضافة method override للـ PUT
            if (method === 'PUT') {
                formData.append('_method', 'PUT');
            }

            fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json().then(data => ({
                    ok: response.ok,
                    data
                })))
                .then(({
                    ok,
                    data
                }) => {
                    if (ok) {
                        Swal.fire({
                            icon: 'success',
                            title: 'نجاح',
                            text: data.message || 'تم حفظ العنصر بنجاح',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(function() {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('itemModal'));
                            if (modal) modal.hide();
                            location.reload();
                        });
                    } else {
                        throw data;
                    }
                })
                .catch(error => {
                    let errorMessage = 'حدث خطأ أثناء الحفظ';

                    if (error && error.errors) {
                        errorMessage = '';
                        for (const key in error.errors) {
                            errorMessage += error.errors[key].join('<br>') + '<br>';
                        }
                    } else if (error && error.message) {
                        errorMessage = error.message;
                    }

                    showError(errorMessage);
                });
        }

        // تعديل العنصر
        function editItem(itemId) {
            fetch(`/admin/banners/items/${itemId}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    }
                })
                .then(response => response.json())
                .then(response => {
                    const modalTitle = document.getElementById('itemModalLabel');
                    if (modalTitle) modalTitle.textContent = 'تعديل العنصر';

                    // تعبئة الحقول
                    const fields = {
                        'item_id': response.id,
                        'item_order': response.item_order,
                        'image_alt': response.image_alt,
                        'link_url': response.link_url,
                        'tag_text': response.tag_text,
                        'tag_color': response.tag_color || '#ffffff',
                        'tag_bg_color': response.tag_bg_color || '#696cff'
                    };

                    Object.keys(fields).forEach(fieldId => {
                        const field = document.getElementById(fieldId);
                        if (field) field.value = fields[fieldId];
                    });

                    // تعيين القيم المنطقية
                    const itemIsActive = document.getElementById('item_is_active');
                    if (itemIsActive) {
                        itemIsActive.checked = response.is_active == 1;
                        itemIsActive.value = response.is_active == 1 ? '1' : '0';
                        // تعيين hidden field
                        const isActiveHidden = document.querySelector('input[name="is_active"][type="hidden"]');
                        if (isActiveHidden) {
                            isActiveHidden.value = response.is_active == 1 ? '0' : '0';
                        }
                    }

                    const linkTarget = document.getElementById('link_target');
                    if (linkTarget) linkTarget.value = response.link_target || '_self';

                    const isLinkActive = document.getElementById('is_link_active');
                    if (isLinkActive) {
                        isLinkActive.checked = response.is_link_active == 1;
                        isLinkActive.value = response.is_link_active == 1 ? '1' : '0';
                        // تعيين hidden field
                        const isLinkActiveHidden = document.querySelector(
                            'input[name="is_link_active"][type="hidden"]');
                        if (isLinkActiveHidden) {
                            isLinkActiveHidden.value = response.is_link_active == 1 ? '0' : '0';
                        }
                    }

                    // تعيين Select2
                    setTimeout(() => {
                        const productSelect = document.getElementById('product_id');
                        if (productSelect && response.product_id) {
                            $(productSelect).val(response.product_id).trigger('change');
                        }

                        const categorySelect = document.getElementById('category_id_item');
                        if (categorySelect && response.category_id) {
                            $(categorySelect).val(response.category_id).trigger('change');
                        }

                        const promoSelect = document.getElementById('promo_codes');
                        if (promoSelect && response.promo_codes) {
                            const promoIds = Array.isArray(response.promo_codes) ? response.promo_codes : [
                                response.promo_codes
                            ];
                            $(promoSelect).val(promoIds).trigger('change');
                        }
                    }, 100);

                    // عرض الصور
                    const imagePreview = document.getElementById('imagePreview');
                    if (imagePreview && response.image_url) {
                        imagePreview.src = response.image_url;
                        imagePreview.style.display = 'block';
                    }

                    const mobileImagePreview = document.getElementById('mobileImagePreview');
                    if (mobileImagePreview && response.mobile_image) {
                        mobileImagePreview.src = response.mobile_image;
                        mobileImagePreview.style.display = 'block';
                    }

                    // فتح المودال
                    const itemModal = new bootstrap.Modal(document.getElementById('itemModal'));
                    itemModal.show();
                })
                .catch(() => {
                    showError('تعذر تحميل بيانات العنصر');
                });
        }

        // حذف العنصر - حل CSRF token mismatch
        function deleteItem(itemId) {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: 'سيتم حذف العنصر نهائياً',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء',
                reverseButtons: true
            }).then(function(result) {
                if (result.isConfirmed) {
                    // الحصول على CSRF token
                    const token = document.querySelector('meta[name="csrf-token"]')?.content ||
                        document.querySelector('input[name="_token"]')?.value ||
                        '';

                    // الطريقة الأولى: استخدام FormData (الأفضل)
                    const formData = new FormData();
                    formData.append('_method', 'DELETE');
                    formData.append('_token', token);

                    fetch(`/admin/banners/items/${itemId}`, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token // إضافة في الـ headers أيضاً
                            },
                            body: formData
                        })
                        .then(response => {
                            // التحقق من حالة الرد
                            if (!response.ok) {
                                return response.json().then(err => {
                                    throw err;
                                });
                            }
                            return response.json();
                        })
                        .then(response => {
                            Swal.fire({
                                icon: 'success',
                                title: 'تم الحذف',
                                text: response.message || 'تم حذف العنصر بنجاح',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(function() {
                                // تحديث القائمة دون إعادة تحميل الصفحة كاملة
                                const itemElement = document.querySelector(
                                    `.delete-item[data-id="${itemId}"]`)?.closest('.item-card');
                                if (itemElement) {
                                    itemElement.remove();

                                    // إذا لم يتبق عناصر، إظهار رسالة فارغة
                                    const itemsContainer = document.getElementById('itemsContainer');
                                    if (itemsContainer.querySelectorAll('.item-card').length === 0) {
                                        itemsContainer.innerHTML = `
                                <div class="empty-items">
                                    <i class="fas fa-image"></i>
                                    <p>لا توجد عناصر مضافة</p>
                                    <small class="help-text">يمكنك إضافة عناصر بعد حفظ البانر</small>
                                </div>
                            `;
                                    }
                                } else {
                                    // إعادة تحميل الصفحة إذا لم نتمكن من إزالة العنصر ديناميكياً
                                    location.reload();
                                }
                            });
                        })
                        .catch((error) => {
                            console.error('Delete error:', error);
                            let errorMessage = 'حدث خطأ أثناء حذف العنصر';

                            if (error && error.message) {
                                if (error.message.includes('CSRF token')) {
                                    errorMessage =
                                        'خطأ في التحقق من الأمان. يرجى تحديث الصفحة والمحاولة مرة أخرى.';
                                } else {
                                    errorMessage = error.message;
                                }
                            }

                            showError(errorMessage);
                        });
                }
            });
        }


        // إعادة تعيين نموذج العنصر
        function resetItemForm() {
            const form = document.getElementById('itemForm');
            if (form) form.reset();

            const previews = document.querySelectorAll('#imagePreview, #mobileImagePreview');
            previews.forEach(preview => {
                preview.style.display = 'none';
                preview.src = '';
            });

            const select2Elements = document.querySelectorAll('#itemForm .select2');
            select2Elements.forEach(element => {
                if ($(element).data('select2')) {
                    $(element).val(null).trigger('change');
                }
            });
        }

        // تهيئة حالة الصفحة
        function initPageState() {
            // Set initial category selection
            const categoryType = document.getElementById('category_type')?.value || 'main';
            const initialCategoryOption = document.querySelector(`.category-option[data-category-type="${categoryType}"]`);
            if (initialCategoryOption) {
                initialCategoryOption.classList.add('active');
                if (categoryType === 'specific') {
                    document.getElementById('specificCategorySelect')?.classList.add('show');
                }
            }

            // Set initial type selection
            const selectedType = document.getElementById('selected_type')?.value || 1;
            const initialTypeCard = document.querySelector(`.type-card[data-type-id="${selectedType}"]`);
            if (initialTypeCard) {
                initialTypeCard.classList.add('active');
                const typeName = initialTypeCard.dataset.typeName;

                // إظهار الإعدادات المناسبة
                if (typeName === 'grid') {
                    document.getElementById('gridSettings').style.display = 'block';
                } else if (typeName === 'slider') {
                    document.getElementById('sliderSettings').style.display = 'block';
                }
            }

            // Set initial permanent checkbox state
            const permanentCheckbox = document.getElementById('permanent');
            if (permanentCheckbox) {
                permanentCheckbox.dispatchEvent(new Event('change'));
            }
        }

        // وظيفة لعرض الأخطاء
        function showError(message) {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                html: message,
                confirmButtonText: 'حسناً'
            });
        }

        // تهيئة المتغيرات العالمية
        window.bannerIndexUrl = "{{ route('admin.banners.index') }}";
        window.itemStoreUrl = "{{ route('admin.banners.items.store') }}";
        window.itemUpdateUrl = "{{ route('admin.banners.items.update', ':id') }}";
    </script>
@endsection
