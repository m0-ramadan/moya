@extends('Admin.layout.master')

@section('title', 'تعديل المنتج: ' . $product->name)

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css" />
    <style>
        body {
            font-family: "Cairo", sans-serif !important;
        }

        .step-card {
            /* background: white; */
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #696cff;
        }

        .step-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f8f9fa;
        }

        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #696cff;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 18px;
            margin-left: 15px;
        }

        .step-title {
            font-size: 18px;
            font-weight: 600;
            color: white;
            margin-bottom: 5px;
        }

        .step-description {
            color: #7f8c8d;
            font-size: 14px;
        }

        /* Image Management */
        .image-manager {
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            /* background: #f8f9fa; */
            min-height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .image-manager:hover {
            border-color: #696cff;
            background: rgba(105, 108, 255, 0.05);
        }

        .image-manager i {
            font-size: 48px;
            color: #696cff;
            margin-bottom: 15px;
        }

        .image-preview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .image-preview-item {
            position: relative;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
            height: 150px;
        }

        .image-preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .image-actions {
            position: absolute;
            bottom: 0;
            right: 0;
            left: 0;
            background: rgba(0, 0, 0, 0.7);
            padding: 10px;
            display: flex;
            justify-content: center;
            gap: 5px;
            transform: translateY(100%);
            transition: transform 0.3s ease;
        }

        .image-preview-item:hover .image-actions {
            transform: translateY(0);
        }

        .image-actions .btn {
            width: 30px;
            height: 30px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .primary-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #696cff;
            color: white;
            padding: 3px 8px;
            border-radius: 5px;
            font-size: 11px;
            font-weight: 600;
        }

        /* Color Management */
        .color-management {
            margin-top: 20px;
        }

        .color-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(40px, 1fr));
            gap: 10px;
            margin-bottom: 15px;
        }

        .color-item {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            position: relative;
            transition: all 0.3s ease;
        }

        .color-item:hover {
            transform: scale(1.1);
        }

        .color-item.selected {
            border-color: #696cff;
            transform: scale(1.1);
        }

        .color-item.selected::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-weight: bold;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }

        /* Material Management */
        .material-item {
            /* background: white; */
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            position: relative;
        }

        .material-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #dee2e6;
        }

        .material-remove {
            color: #e74c3c;
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
        }

        /* Dynamic Fields */
        .dynamic-field {
            /* background: #f8f9fa; */
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            position: relative;
        }

        .dynamic-field-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #dee2e6;
        }

        .dynamic-field-title {
            font-weight: 600;
            color: #2c3e50;
        }

        .dynamic-field-remove {
            color: #e74c3c;
            cursor: pointer;
            background: none;
            border: none;
            font-size: 18px;
        }

        .add-more-btn {
            width: 100%;
            padding: 10px;
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            /* background: #f8f9fa; */
            color: #696cff;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .add-more-btn:hover {
            border-color: #696cff;
            background: rgba(105, 108, 255, 0.05);
        }

        /* Toggle Switch */
        .toggle-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .toggle-switch {
            position: relative;
            width: 60px;
            height: 30px;
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
            height: 22px;
            width: 22px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.toggle-slider {
            background-color: #696cff;
        }

        input:checked+.toggle-slider:before {
            transform: translateX(30px);
        }

        .toggle-label {
            font-weight: 500;
            color: #2c3e50;
        }

        /* Wizard Steps */
        .wizard-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            position: relative;
        }

        .wizard-steps::before {
            content: '';
            position: absolute;
            top: 20px;
            right: 0;
            left: 0;
            height: 2px;
            /* background: #e0e0e0; */
            z-index: 1;
        }

        .wizard-step {
            position: relative;
            z-index: 2;
            text-align: center;
            flex: 1;
        }

        .wizard-step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            /* background: #e0e0e0; */
            color: #7f8c8d;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-weight: bold;
            border: 4px solid white;
        }

        .wizard-step.active .wizard-step-circle {
            background: #696cff;
            color: white;
        }

        .wizard-step.completed .wizard-step-circle {
            background: #2ecc71;
            color: white;
        }

        .wizard-step-label {
            font-size: 14px;
            color: #7f8c8d;
            font-weight: 500;
        }

        .wizard-step.active .wizard-step-label {
            color: #696cff;
        }

        /* Required field indicator */
        .required::after {
            content: " *";
            color: #dc3545;
        }

        /* Alert Guide */
        .alert-guide {
            background: #26253d;
            border-right: 4px solid #696cff;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .alert-guide h6 {
            color: #696cff;
            margin-bottom: 10px;
        }

        .alert-guide ul {
            margin-bottom: 0;
            padding-left: 20px;
        }

        .alert-guide li {
            margin-bottom: 5px;
            font-size: 14px;
        }

        /* Price Input */
        .price-input-group {
            position: relative;
        }

        .price-input-group .form-control {
            padding-left: 40px;
        }

        .price-input-group .input-group-text {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            background: none;
            border: none;
            z-index: 3;
        }

        /* Tabs */
        .form-tabs {
            border-bottom: 2px solid #dee2e6;
            margin-bottom: 25px;
        }

        .form-tabs .nav-link {
            border: none;
            color: #7f8c8d;
            font-weight: 500;
            padding: 10px 20px;
            border-bottom: 3px solid transparent;
        }

        .form-tabs .nav-link.active {
            color: #696cff;
            border-bottom-color: #696cff;
            background: none;
        }

        /* Preview Card */
        .preview-card {
            /* background: white; */
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            border: 2px solid #e9ecef;
        }

        .preview-card h6 {
            color: #e9ecef;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e9ecef;
        }

        .preview-image {
            width: 100px;
            height: 100px;
            border-radius: 10px;
            object-fit: cover;
            margin-bottom: 10px;
        }

        .preview-price {
            font-size: 20px;
            font-weight: bold;
            color: #2ecc71;
        }

        .preview-old-price {
            font-size: 16px;
            color: #95a5a6;
            text-decoration: line-through;
        }

        /* Quick Add Modal */
        .quick-add-modal .modal-content {
            border-radius: 15px;
            /* overflow: hidden; */
        }

        .quick-add-modal .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-bottom: none;
            padding: 20px 30px;
        }

        .quick-add-modal .modal-body {
            padding: 30px;
        }

        .quick-add-modal .modal-footer {
            border-top: 1px solid #dee2e6;
            padding: 20px 30px;
        }

        /* Color Picker Input */
        .color-picker-input {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .color-picker-input input[type="color"] {
            width: 40px;
            height: 40px;
            padding: 0;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        /* Stock Warning */
        .stock-warning {
            /* background: #fff3cd; */
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .stock-warning i {
            color: #f39c12;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .wizard-steps {
                flex-direction: column;
                gap: 15px;
            }

            .wizard-steps::before {
                display: none;
            }

            .wizard-step {
                text-align: right;
                display: flex;
                align-items: center;
                gap: 15px;
            }

            .wizard-step-circle {
                margin: 0;
            }
        }

        /* Tab Content */
        .tab-content {
            padding: 20px 0;
        }

        /* Image Sortable */
        .sortable-image-list {
            position: relative;
        }

        .sortable-image-list .image-preview-item {
            cursor: move;
        }

        .sortable-image-list .image-preview-item.sortable-chosen {
            box-shadow: 0 0 20px rgba(105, 108, 255, 0.3);
        }

        /* Color Price Input */
        .color-price-input {
            width: 100px;
            padding: 5px 10px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            font-size: 14px;
        }

        /* Material Additional Price */
        .material-additional-price {
            /* background: #e7f7ff; */
            border: 1px dashed #3498db;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
        }
    </style>
    <style>
        /* تحسين تصميم حاوية الصورة الرئيسية */
        .image-upload-container {
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            background: #f8f9fa;
            min-height: 150px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 15px;
        }

        .image-upload-container:hover {
            border-color: #696cff;
            background: rgba(105, 108, 255, 0.05);
        }

        .image-upload-container i {
            font-size: 48px;
            color: #696cff;
            margin-bottom: 10px;
        }

        /* تحسين تنسيق معاينة الصور */
        #newMainImagePreview .image-preview-item {
            border: 2px solid #2ecc71;
        }

        #currentMainImageContainer .image-preview-item {
            border: 2px solid #696cff;
        }

        /* تحسين badge للصورة الجديدة */
        .primary-badge[style*="background: #2ecc71"] {
            background: #2ecc71 !important;
        }

        /* تحسين عرض الصورة في المنتج المصغر */
        .preview-image {
            width: 100px;
            height: 100px;
            border-radius: 10px;
            object-fit: cover;
            margin-bottom: 10px;
            border: 2px solid #dee2e6;
            transition: all 0.3s ease;
        }

        .preview-image:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        /* رسائل التنبيه */
        .alert-warning {
            background: #fff3cd;
            border-color: #ffeaa7;
            color: #856404;
        }

        .alert-danger {
            background: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y" bis_skin_checked="1">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.index') }}">الرئيسية</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.products.index') }}">المنتجات</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.products.show', $product->id) }}">{{ Str::limit($product->name, 30) }}</a>
                </li>
                <li class="breadcrumb-item active">تعديل</li>
            </ol>
        </nav>

        <div class="row" bis_skin_checked="1">
            <div class="col-12" bis_skin_checked="1">
                <div class="card mb-4" bis_skin_checked="1">
                    <div class="card-header d-flex justify-content-between align-items-center" bis_skin_checked="1">
                        <div bis_skin_checked="1">
                            <h5 class="mb-0">تعديل المنتج</h5>
                            <small class="text-muted">ID: #{{ $product->id }}</small>
                        </div>
                        <div class="btn-group" bis_skin_checked="1">
                            <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-outline-info btn-sm">
                                <i class="fas fa-eye me-1"></i> عرض
                            </a>
                            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-right me-1"></i> رجوع
                            </a>
                        </div>
                    </div>

                    <div class="card-body" bis_skin_checked="1">
                        <!-- Quick Guide -->
                        <div class="alert-guide" bis_skin_checked="1">
                            <h6><i class="fas fa-lightbulb me-2"></i>نصائح للتعديل:</h6>
                            <ul>
                                <li>يمكنك تحديث أي معلومات عن المنتج</li>
                                <li>يمكنك إضافة أو إزالة الصور</li>
                                <li>يمكنك تحديث الألوان والمواد والخيارات</li>
                                <li>تأكد من تحديث المخزون والسعر بدقة</li>
                                <li>احفظ التغييرات قبل الانتقال إلى قسم آخر</li>
                            </ul>
                        </div>

                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
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
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <!-- Product Preview -->
                        <div class="preview-card mb-4" bis_skin_checked="1">
                            <div class="row align-items-center" bis_skin_checked="1">
                                <div class="col-auto" bis_skin_checked="1">
                                    <img src="{{ $product->primaryImage ? get_user_image($product->primaryImage->path) : 'https://via.placeholder.com/100x100?text=No+Image' }}"
                                        alt="{{ $product->name }}" class="preview-image">
                                </div>
                                <div class="col" bis_skin_checked="1">
                                    <h6 class="mb-2">{{ $product->name }}</h6>
                                    <div class="mb-2" bis_skin_checked="1">
                                        <span class="preview-price">
                                            {{ number_format($product->final_price, 2) }} ج.م
                                        </span>
                                        @if ($product->has_discount && $product->price > $product->final_price)
                                            <span class="preview-old-price ms-2">
                                                {{ number_format($product->price, 2) }} ج.م
                                            </span>
                                        @endif
                                    </div>
                                    <div class="d-flex gap-3" bis_skin_checked="1">
                                        <small class="text-muted">
                                            <i class="fas fa-box me-1"></i> المخزون: {{ $product->stock }}
                                        </small>
                                        <small class="text-muted">
                                            <i class="fas fa-folder me-1"></i> {{ $product->category->name ?? 'غير مصنف' }}
                                        </small>
                                        <small class="text-muted">
                                            @if ($product->status_id == 1)
                                                <span class="badge bg-success">نشط</span>
                                            @elseif($product->status_id == 2)
                                                <span class="badge bg-danger">غير نشط</span>
                                            @else
                                                <span class="badge bg-warning">مسودة</span>
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Wizard Steps -->
                        <div class="wizard-steps" bis_skin_checked="1">
                            <div class="wizard-step active" id="step1">
                                <div class="wizard-step-circle">1</div>
                                <div class="wizard-step-label">المعلومات الأساسية</div>
                            </div>
                            <div class="wizard-step" id="step2">
                                <div class="wizard-step-circle">2</div>
                                <div class="wizard-step-label">الصور والتسعير</div>
                            </div>
                            <div class="wizard-step" id="step3">
                                <div class="wizard-step-circle">3</div>
                                <div class="wizard-step-label">المواصفات</div>
                            </div>
                            <div class="wizard-step" id="step4">
                                <div class="wizard-step-circle">4</div>
                                <div class="wizard-step-label">خيارات إضافية</div>
                            </div>
                        </div>

                        <form action="{{ route('admin.products.update', $product->id) }}" method="POST"
                            enctype="multipart/form-data" id="editProductForm">
                            @csrf
                            @method('POST')

                            <!-- Step 1: Basic Information -->
                            <div class="step-card step-1" bis_skin_checked="1">
                                <div class="step-header" bis_skin_checked="1">
                                    <div class="step-number" bis_skin_checked="1">1</div>
                                    <div bis_skin_checked="1">
                                        <h5 class="step-title">المعلومات الأساسية</h5>
                                        <p class="step-description">تحديث المعلومات الأساسية للمنتج</p>
                                    </div>
                                </div>

                                <div class="row" bis_skin_checked="1">
                                    <div class="col-md-8 mb-3" bis_skin_checked="1">
                                        <label for="name" class="form-label required">اسم المنتج</label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            value="{{ old('name', $product->name) }}" required>
                                        <small class="text-muted">اسم واضح ومعبر عن المنتج</small>
                                    </div>

                                    <div class="col-md-4 mb-3" bis_skin_checked="1">
                                        <label for="category_id" class="form-label required">القسم</label>
                                        <div class="input-group" bis_skin_checked="1">
                                            <select class="form-control select2" id="category_id" name="category_id"
                                                required>
                                                <option value="">اختر القسم</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}"
                                                        {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                        @if (!$category->isParent())
                                                            (تابع لـ: {{ $category->parent->name ?? '' }})
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="button" class="btn btn-outline-primary"
                                                onclick="openQuickAddModal('category')">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="col-12 mb-3" bis_skin_checked="1">
                                        <label for="description" class="form-label">الوصف</label>
                                        <textarea class="form-control summernote" id="description" name="description" rows="6">{{ old('description', $product->description) }}</textarea>
                                    </div>

                                    <div class="col-md-6 mb-3" bis_skin_checked="1">
                                        <label for="status_id" class="form-label required">الحالة</label>
                                        <select class="form-select" id="status_id" name="status_id" required>
                                            <option value="1"
                                                {{ old('status_id', $product->status_id) == 1 ? 'selected' : '' }}>نشط
                                            </option>
                                            <option value="2"
                                                {{ old('status_id', $product->status_id) == 2 ? 'selected' : '' }}>غير نشط
                                            </option>
                                            <option value="3"
                                                {{ old('status_id', $product->status_id) == 3 ? 'selected' : '' }}>قيد
                                                المراجعة</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3" bis_skin_checked="1">
                                        <label for="stock" class="form-label required">الكمية المتاحة</label>
                                        <input type="number" class="form-control" id="stock" name="stock"
                                            value="{{ old('stock', $product->stock) }}" min="0" required>
                                        @if ($product->stock < 10)
                                            <div class="stock-warning" bis_skin_checked="1">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                <span>المخزون منخفض! نوصي بإضافة المزيد</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-4" bis_skin_checked="1">
                                    <div></div>
                                    <button type="button" class="btn btn-primary next-step" data-next="2">
                                        التالي <i class="fas fa-arrow-left ms-1"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Step 2: Images & Pricing -->
                            <div class="step-card step-2" style="display: none;" bis_skin_checked="1">
                                <div class="step-header" bis_skin_checked="1">
                                    <div class="step-number" bis_skin_checked="1">2</div>
                                    <div bis_skin_checked="1">
                                        <h5 class="step-title">الصور والتسعير</h5>
                                        <p class="step-description">تحديث صور المنتج والتسعير</p>
                                    </div>
                                </div>

                                <!-- Main Image Section -->
                                <div class="mb-4" bis_skin_checked="1">
                                    <label class="form-label required">الصورة الرئيسية</label>

                                    <!-- Current Main Image -->
                                    @if ($product->image)
                                        <div class="mb-3">
                                            <p class="text-muted mb-2">الصورة الرئيسية الحالية:</p>
                                            <div class="image-preview-grid" id="currentMainImageContainer">
                                                <div class="image-preview-item">
                                                    <span class="primary-badge">رئيسية</span>
                                                    <img src="{{ get_product_image($product->image) }}"
                                                        alt="الصورة الرئيسية الحالية">
                                                    <div class="image-actions">
                                                        <button type="button" class="btn btn-info btn-sm"
                                                            onclick="viewImage('{{ get_product_image($product->image) }}')">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            onclick="removeCurrentMainImage()">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="alert alert-warning mb-3">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            لا توجد صورة رئيسية للمنتج
                                        </div>
                                    @endif

                                    <!-- Upload New Main Image -->
                                    <div class="mb-3">
                                        <p class="text-muted mb-2">تغيير الصورة الرئيسية:</p>
                                        <div class="image-upload-container"
                                            onclick="document.getElementById('image').click()">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                            <p class="mb-0">انقر لرفع صورة جديدة</p>
                                            <small class="text-muted">الحجم الموصى به: 800×800 بكسل</small>
                                        </div>
                                        <input type="file" id="image" name="image" accept="image/*"
                                            style="display: none;">
                                        <input type="hidden" id="remove_main_image" name="remove_main_image"
                                            value="0">
                                    </div>

                                    <!-- Preview of New Main Image -->
                                    <div id="newMainImagePreview" class="mt-3" style="display: none;">
                                        <p class="text-muted mb-2">الصورة الجديدة المختارة:</p>
                                        <div class="image-preview-grid">
                                            <div class="image-preview-item">
                                                <span class="primary-badge" style="background: #2ecc71;">جديدة</span>
                                                <img id="newMainImagePreviewImg" src=""
                                                    alt="الصورة الرئيسية الجديدة">
                                                <div class="image-actions">
                                                    <button type="button" class="btn btn-info btn-sm"
                                                        onclick="viewNewMainImage()">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm"
                                                        onclick="removeNewMainImage()">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <small class="text-muted d-block mt-1">سيتم استبدال الصورة الرئيسية الحالية بهذه
                                            الصورة</small>
                                    </div>
                                </div>


                                <!-- Additional Images -->
                                <div class="mb-4" bis_skin_checked="1">
                                    <label class="form-label">الصور الإضافية</label>

                                    @if ($product->images && $product->images->count() > 0)
                                        <div class="image-preview-grid sortable-image-list" id="existingImagesGrid">
                                            @foreach ($product->images as $image)
                                                <div class="image-preview-item" data-id="{{ $image->id }}">
                                                    @if ($image->is_primary)
                                                        <span class="primary-badge">رئيسية</span>
                                                    @endif
                                                    <img src="{{ get_user_image($image->path) }}" alt="صورة إضافية">
                                                    <div class="image-actions">
                                                        <button type="button" class="btn btn-info btn-sm"
                                                            onclick="viewImage('{{ get_user_image($image->path) }}')">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            onclick="removeAdditionalImage({{ $image->id }})">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                        @if (!$image->is_primary)
                                                            <button type="button" class="btn btn-primary btn-sm"
                                                                onclick="setAsPrimary({{ $image->id }})">
                                                                <i class="fas fa-star"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    <div class="image-manager mt-3"
                                        onclick="document.getElementById('additional_images').click()">
                                        <i class="fas fa-images"></i>
                                        <p class="mb-0">انقر لإضافة صور إضافية</p>
                                        <small class="text-muted">يمكنك رفع أكثر من صورة</small>
                                    </div>
                                    <input type="file" id="additional_images" name="additional_images[]"
                                        accept="image/*" multiple style="display: none;">
                                    <div id="newImagesPreview" class="image-preview-grid mt-3"></div>
                                    <input type="hidden" id="removed_images" name="removed_images" value="">
                                    <input type="hidden" id="primary_image_id" name="primary_image_id"
                                        value="{{ $product->images->where('is_primary', true)->first()->id ?? '' }}">
                                    <input type="hidden" id="images_order" name="images_order" value="">
                                </div>

                                <!-- Pricing -->
                                <div class="row" bis_skin_checked="1">
                                    <div class="col-md-4 mb-2" bis_skin_checked="1">
                                        <label for="price" class="form-label required">السعر الأساسي</label>
                                        <div class="price-input-group" bis_skin_checked="1">
                                            <span class="input-group-text">ج.م</span>
                                            <input type="number" class="form-control" id="price" name="price"
                                                step="0.01" value="{{ old('price', $product->price) }}" required>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-8 mb-4">
                                    <label for="price_text" class="form-label required"> نص السعر </label>
                                    <div class="price-input-group">
                                        <span class="input-group-text">ج.م</span>
                                        <input type="text" class="form-control" id="price_text" name="price_text"
                                            value="{{ old('price_text', $product->price_text) }}" required>
                                    </div>
                                </div>
                                <!-- Additional Pricing Options -->
                                <div class="row" bis_skin_checked="1">
                                    <div class="col-md-4 mb-3" bis_skin_checked="1">
                                        <div class="toggle-container mb-3" bis_skin_checked="1">
                                            <label class="toggle-switch">
                                                <input type="checkbox" id="has_discount" name="has_discount"
                                                    {{ old('has_discount', $product->has_discount) ? 'checked' : '' }}>
                                                <span class="toggle-slider"></span>
                                            </label>
                                            <span class="toggle-label">هل يحتوي المنتج على خصم؟</span>
                                        </div>

                                        <div id="discountSection"
                                            style="{{ old('has_discount', $product->has_discount) ? '' : 'display: none;' }}">
                                            <div class="row" bis_skin_checked="1">
                                                <div class="col-6" bis_skin_checked="1">
                                                    <label for="discount_type" class="form-label">نوع الخصم</label>
                                                    <select class="form-select" id="discount_type" name="discount_type">
                                                        <option value="percentage"
                                                            {{ old('discount_type', $product->discount->discount_type ?? '') == 'percentage' ? 'selected' : '' }}>
                                                            نسبة مئوية %</option>
                                                        <option value="fixed"
                                                            {{ old('discount_type', $product->discount->discount_type ?? '') == 'fixed' ? 'selected' : '' }}>
                                                            قيمة ثابتة</option>
                                                    </select>
                                                </div>
                                                <div class="col-6" bis_skin_checked="1">
                                                    <label for="discount_value" class="form-label">قيمة الخصم</label>
                                                    <input type="number" class="form-control" id="discount_value"
                                                        name="discount_value" step="0.01"
                                                        value="{{ old('discount_value', $product->discount->discount_value ?? '') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-3" bis_skin_checked="1">
                                        <div class="toggle-container" bis_skin_checked="1">
                                            <label class="toggle-switch">
                                                <input type="checkbox" id="includes_tax" name="includes_tax"
                                                    {{ old('includes_tax', $product->includes_tax) ? 'checked' : '' }}>
                                                <span class="toggle-slider"></span>
                                            </label>
                                            <span class="toggle-label">يشمل الضريبة</span>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-3" bis_skin_checked="1">
                                        <div class="toggle-container" bis_skin_checked="1">
                                            <label class="toggle-switch">
                                                <input type="checkbox" id="includes_shipping" name="includes_shipping"
                                                    {{ old('includes_shipping', $product->includes_shipping) ? 'checked' : '' }}>
                                                <span class="toggle-slider"></span>
                                            </label>
                                            <span class="toggle-label">يشمل الشحن</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-4" bis_skin_checked="1">
                                    <button type="button" class="btn btn-outline-secondary prev-step" data-prev="1">
                                        <i class="fas fa-arrow-right me-1"></i> السابق
                                    </button>
                                    <button type="button" class="btn btn-primary next-step" data-next="3">
                                        التالي <i class="fas fa-arrow-left ms-1"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Step 3: Specifications -->
                            <div class="step-card step-3" style="display: none;" bis_skin_checked="1">
                                <div class="step-header" bis_skin_checked="1">
                                    <div class="step-number" bis_skin_checked="1">3</div>
                                    <div bis_skin_checked="1">
                                        <h5 class="step-title">المواصفات والخصائص</h5>
                                        <p class="step-description">تحديث مواصفات المنتج وخياراته</p>
                                    </div>
                                </div>

                                <!-- Colors -->
                                <div class="mb-4" bis_skin_checked="1">
                                    <div class="d-flex justify-content-between align-items-center mb-3"
                                        bis_skin_checked="1">
                                        <label class="form-label mb-0">الألوان المتاحة</label>
                                        <button type="button" class="btn btn-outline-primary btn-sm"
                                            onclick="openQuickAddModal('color')">
                                            <i class="fas fa-plus me-1"></i> إضافة لون جديد
                                        </button>
                                    </div>

                                    <!-- Selected Colors -->
                                    <div class="mb-3" bis_skin_checked="1">
                                        <label class="form-label">الألوان المختارة:</label>
                                        <div id="selectedColorsContainer" class="d-flex flex-wrap gap-2 mb-3">
                                            @foreach ($product->colors as $color)
                                                <div class="color-swatch" data-id="{{ $color->id }}">
                                                    <div class="color-preview"
                                                        style="background-color: {{ $color->hex_code }};"></div>
                                                    <span>{{ $color->name }}</span>
                                                    <input type="hidden" name="colors[]" value="{{ $color->id }}">
                                                    <button type="button" class="btn btn-sm btn-outline-danger ms-2"
                                                        onclick="removeColor({{ $color->id }})">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Available Colors -->
                                    <div bis_skin_checked="1">
                                        <label class="form-label">اختر من الألوان المتاحة:</label>
                                        <div class="color-grid" id="availableColorsGrid">
                                            @foreach ($colors as $color)
                                                @if (!$product->colors->contains($color->id))
                                                    <div class="color-item"
                                                        style="background-color: {{ $color->hex_code }};"
                                                        data-id="{{ $color->id }}" data-name="{{ $color->name }}"
                                                        data-hex="{{ $color->hex_code }}" onclick="addColor(this)">
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <!-- Materials -->
                                <div class="mb-4" bis_skin_checked="1">
                                    <div class="d-flex justify-content-between align-items-center mb-3"
                                        bis_skin_checked="1">
                                        <label class="form-label mb-0">المواد المستخدمة</label>
                                        <button type="button" class="btn btn-outline-primary btn-sm"
                                            onclick="openQuickAddModal('material')">
                                            <i class="fas fa-plus me-1"></i> إضافة مادة جديدة
                                        </button>
                                    </div>

                                    <!-- Existing Materials -->
                                    <div id="materialsContainer">
                                        @foreach ($product->materials as $material)
                                            <div class="material-item" data-id="{{ $material->id }}">
                                                <div class="material-header">
                                                    <div class="material-title" bis_skin_checked="1">
                                                        <strong>{{ $material->name }}</strong>
                                                    </div>
                                                    <button type="button" class="material-remove"
                                                        onclick="removeMaterial(this)">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                                <div class="row" bis_skin_checked="1">
                                                    <div class="col-md-4 mb-2" bis_skin_checked="1">
                                                        <input type="hidden"
                                                            name="materials[{{ $loop->index }}][material_id]"
                                                            value="{{ $material->id }}">
                                                        <input type="number" class="form-control"
                                                            name="materials[{{ $loop->index }}][quantity]"
                                                            value="{{ $material->pivot->quantity }}" placeholder="الكمية"
                                                            min="0" step="0.01">
                                                    </div>
                                                    <div class="col-md-4 mb-2" bis_skin_checked="1">
                                                        <select class="form-select"
                                                            name="materials[{{ $loop->index }}][unit]" required>
                                                            <option value="piece"
                                                                {{ $material->pivot->unit == 'piece' ? 'selected' : '' }}>
                                                                قطعة</option>
                                                            <option value="meter"
                                                                {{ $material->pivot->unit == 'meter' ? 'selected' : '' }}>
                                                                متر</option>
                                                            <option value="kg"
                                                                {{ $material->pivot->unit == 'kg' ? 'selected' : '' }}>كجم
                                                            </option>
                                                            <option value="liter"
                                                                {{ $material->pivot->unit == 'liter' ? 'selected' : '' }}>
                                                                لتر</option>
                                                            <option value="gram"
                                                                {{ $material->pivot->unit == 'gram' ? 'selected' : '' }}>
                                                                جرام</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4 mb-2" bis_skin_checked="1">
                                                        <input type="number" class="form-control"
                                                            name="materials[{{ $loop->index }}][additional_price]"
                                                            value="{{ $material->pivot->additional_price ?? 0 }}"
                                                            placeholder="سعر إضافي" step="0.01" min="0">
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Add Material Button -->
                                    <button type="button" class="btn btn-outline-primary btn-sm mt-3"
                                        onclick="addMaterialField()">
                                        <i class="fas fa-plus me-1"></i> إضافة مادة أخرى
                                    </button>
                                </div>

                                <!-- Features -->
                                <div class="mb-4" bis_skin_checked="1">
                                    <div class="d-flex justify-content-between align-items-center mb-3"
                                        bis_skin_checked="1">
                                        <label class="form-label mb-0">المواصفات الإضافية</label>
                                        <button type="button" class="btn btn-outline-primary btn-sm"
                                            onclick="addFeatureField()">
                                            <i class="fas fa-plus me-1"></i> إضافة مواصفة
                                        </button>
                                    </div>

                                    <div id="featuresContainer">
                                        @foreach ($product->features as $index => $feature)
                                            <div class="dynamic-field">
                                                <div class="dynamic-field-header">
                                                    <div class="dynamic-field-title">مواصفة {{ $index + 1 }}</div>
                                                    <button type="button" class="dynamic-field-remove"
                                                        onclick="removeField(this)">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                                <div class="row" bis_skin_checked="1">
                                                    <div class="col-md-5 mb-2" bis_skin_checked="1">
                                                        <input type="text" class="form-control"
                                                            name="features[{{ $index }}][name]"
                                                            value="{{ $feature->name }}" placeholder="اسم المواصفة"
                                                            required>
                                                    </div>
                                                    <div class="col-md-7 mb-2" bis_skin_checked="1">
                                                        <input type="text" class="form-control"
                                                            name="features[{{ $index }}][value]"
                                                            value="{{ $feature->value }}" placeholder="القيمة" required>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Delivery Time -->
                                <div class="mb-4" bis_skin_checked="1">
                                    <label class="form-label">وقت التوصيل</label>
                                    <div class="row" bis_skin_checked="1">
                                        <div class="col-md-6 mb-3" bis_skin_checked="1">
                                            <label for="from_days" class="form-label">من (أيام)</label>
                                            <input type="number" class="form-control" id="from_days" name="from_days"
                                                value="{{ old('from_days', $product->deliveryTime->from_days ?? '') }}"
                                                min="0">
                                        </div>
                                        <div class="col-md-6 mb-3" bis_skin_checked="1">
                                            <label for="to_days" class="form-label">إلى (أيام)</label>
                                            <input type="number" class="form-control" id="to_days" name="to_days"
                                                value="{{ old('to_days', $product->deliveryTime->to_days ?? '') }}"
                                                min="0">
                                        </div>
                                    </div>
                                </div>

                                <!-- Warranty -->
                                <div class="mb-4" bis_skin_checked="1">
                                    <label for="warranty_months" class="form-label">الضمان (بالأشهر)</label>
                                    <input type="number" class="form-control" id="warranty_months"
                                        name="warranty_months"
                                        value="{{ old('warranty_months', $product->warranty->months ?? '') }}"
                                        min="0">
                                </div>

                                <div class="d-flex justify-content-between mt-4" bis_skin_checked="1">
                                    <button type="button" class="btn btn-outline-secondary prev-step" data-prev="2">
                                        <i class="fas fa-arrow-right me-1"></i> السابق
                                    </button>
                                    <button type="button" class="btn btn-primary next-step" data-next="4">
                                        التالي <i class="fas fa-arrow-left ms-1"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Step 4: Additional Options -->
                            <div class="step-card step-4" style="display: none;" bis_skin_checked="1">
                                <div class="step-header" bis_skin_checked="1">
                                    <div class="step-number" bis_skin_checked="1">4</div>
                                    <div bis_skin_checked="1">
                                        <h5 class="step-title">خيارات إضافية</h5>
                                        <p class="step-description">تحديث الخيارات الخاصة بالمنتج</p>
                                    </div>
                                </div>

                                <!-- Product Options -->
                                <div class="mb-4" bis_skin_checked="1">
                                    <div class="d-flex justify-content-between align-items-center mb-3"
                                        bis_skin_checked="1">
                                        <label class="form-label mb-0">خيارات المنتج</label>
                                        <button type="button" class="btn btn-outline-primary btn-sm"
                                            onclick="addProductOptionField()">
                                            <i class="fas fa-plus me-1"></i> إضافة خيار
                                        </button>
                                    </div>

                                    <div id="productOptionsContainer">
                                        @foreach ($product->options as $index => $option)
                                            <div class="dynamic-field">
                                                <div class="dynamic-field-header">
                                                    <div class="dynamic-field-title">خيار {{ $index + 1 }}</div>
                                                    <button type="button" class="dynamic-field-remove"
                                                        onclick="removeField(this)">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                                <div class="row" bis_skin_checked="1">
                                                    <div class="col-md-4 mb-2" bis_skin_checked="1">
                                                        <input type="text" class="form-control"
                                                            name="product_options[{{ $index }}][option_name]"
                                                            value="{{ $option->option_name }}" placeholder="اسم الخيار"
                                                            required>
                                                    </div>
                                                    <div class="col-md-4 mb-2" bis_skin_checked="1">
                                                        <input type="text" class="form-control"
                                                            name="product_options[{{ $index }}][option_value]"
                                                            value="{{ $option->option_value }}" placeholder="القيمة"
                                                            required>
                                                    </div>
                                                    <div class="col-md-2 mb-2" bis_skin_checked="1">
                                                        <input type="number" class="form-control"
                                                            name="product_options[{{ $index }}][additional_price]"
                                                            value="{{ $option->additional_price }}"
                                                            placeholder="السعر الإضافي" step="0.01">
                                                    </div>
                                                    <div class="col-md-2 mb-2" bis_skin_checked="1">
                                                        <div class="form-check" bis_skin_checked="1">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="product_options[{{ $index }}][is_required]"
                                                                value="1"
                                                                {{ $option->is_required ? 'checked' : '' }}>
                                                            <label class="form-check-label">مطلوب</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Printing Methods -->
                                <div class="mb-4" bis_skin_checked="1">
                                    <div class="d-flex justify-content-between align-items-center mb-3"
                                        bis_skin_checked="1">
                                        <label class="form-label mb-0">طرق الطباعة</label>
                                        <button type="button" class="btn btn-outline-primary btn-sm"
                                            onclick="openQuickAddModal('printing_method')">
                                            <i class="fas fa-plus me-1"></i> إضافة طريقة طباعة جديدة
                                        </button>
                                    </div>

                                    <div class="mb-3" bis_skin_checked="1">
                                        <label class="form-label">اختر طرق الطباعة:</label>
                                        <select class="form-select select2" id="printingMethodsSelect"
                                            name="printing_methods[]" multiple>
                                            @foreach ($printingMethods as $method)
                                                <option value="{{ $method->id }}"
                                                    data-price="{{ $method->base_price }}"
                                                    {{ $product->printingMethods->contains($method->id) ? 'selected' : '' }}>
                                                    {{ $method->name }} - {{ $method->base_price }} ج.م
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Print Locations -->
                                <div class="mb-4" bis_skin_checked="1">
                                    <div class="d-flex justify-content-between align-items-center mb-3"
                                        bis_skin_checked="1">
                                        <label class="form-label mb-0">أماكن الطباعة</label>
                                        <button type="button" class="btn btn-outline-primary btn-sm"
                                            onclick="openQuickAddModal('print_location')">
                                            <i class="fas fa-plus me-1"></i> إضافة مكان طباعة جديد
                                        </button>
                                    </div>

                                    <div class="mb-3" bis_skin_checked="1">
                                        <label class="form-label">اختر أماكن الطباعة:</label>
                                        <select class="form-select select2" id="printLocationsSelect"
                                            name="print_locations[]" multiple>
                                            @foreach ($printLocations as $location)
                                                <option value="{{ $location->id }}" data-type="{{ $location->type }}"
                                                    data-price="{{ $location->additional_price }}"
                                                    {{ $product->printLocations->contains($location->id) ? 'selected' : '' }}>
                                                    {{ $location->name }} ({{ $location->type }}) -
                                                    {{ $location->additional_price }} ج.م
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Offers -->
                                <div class="mb-4" bis_skin_checked="1">
                                    <div class="d-flex justify-content-between align-items-center mb-3"
                                        bis_skin_checked="1">
                                        <label class="form-label mb-0">العروض</label>
                                        <button type="button" class="btn btn-outline-primary btn-sm"
                                            onclick="openQuickAddModal('offer')">
                                            <i class="fas fa-plus me-1"></i> إضافة عرض جديد
                                        </button>
                                    </div>

                                    <div class="mb-3" bis_skin_checked="1">
                                        <label class="form-label">اختر العروض:</label>
                                        <select class="form-select select2" id="offersSelect" name="offers[]" multiple>
                                            @foreach ($offers as $offer)
                                                <option value="{{ $offer->id }}"
                                                    {{ $product->offers->contains($offer->id) ? 'selected' : '' }}>
                                                    {{ $offer->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Pricing Tiers -->
                                <div class="mb-4" bis_skin_checked="1">
                                    <div class="d-flex justify-content-between align-items-center mb-3"
                                        bis_skin_checked="1">
                                        <label class="form-label mb-0">التسعير حسب الكمية</label>
                                        <button type="button" class="btn btn-outline-primary btn-sm"
                                            onclick="addPricingTierField()">
                                            <i class="fas fa-plus me-1"></i> إضافة شرائح تسعير
                                        </button>
                                    </div>

                                    <div id="pricingTiersContainer">
                                        @foreach ($product->pricingTiers as $index => $tier)
                                            <div class="dynamic-field">
                                                <div class="dynamic-field-header">
                                                    <div class="dynamic-field-title">شريحة تسعير {{ $index + 1 }}</div>
                                                    <button type="button" class="dynamic-field-remove"
                                                        onclick="removeField(this)">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                                <div class="row" bis_skin_checked="1">
                                                    <div class="col-md-3 mb-2" bis_skin_checked="1">
                                                        <input type="number" class="form-control"
                                                            name="pricing_tiers[{{ $index }}][quantity]"
                                                            value="{{ $tier->quantity }}" placeholder="الكمية"
                                                            min="1" required>
                                                    </div>
                                                    <div class="col-md-3 mb-2" bis_skin_checked="1">
                                                        <input type="number" class="form-control"
                                                            name="pricing_tiers[{{ $index }}][price_per_unit]"
                                                            value="{{ $tier->price_per_unit }}"
                                                            placeholder="السعر للوحدة" step="0.01" required>
                                                    </div>
                                                    <div class="col-md-3 mb-2" bis_skin_checked="1">
                                                        <input type="number" class="form-control"
                                                            name="pricing_tiers[{{ $index }}][discount_percentage]"
                                                            value="{{ $tier->discount_percentage }}" placeholder="خصم %"
                                                            min="0" max="100" step="0.01">
                                                    </div>
                                                    <div class="col-md-3 mb-2" bis_skin_checked="1">
                                                        <div class="form-check" bis_skin_checked="1">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="pricing_tiers[{{ $index }}][is_sample]"
                                                                value="1" {{ $tier->is_sample ? 'checked' : '' }}>
                                                            <label class="form-check-label">عينة</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Size Tiers -->
                                <div class="mb-4" bis_skin_checked="1">
                                    <div class="d-flex justify-content-between align-items-center mb-3"
                                        bis_skin_checked="1">
                                        <label class="form-label mb-0">أسعار حسب المقاس والكمية</label>
                                        <button type="button" class="btn btn-outline-primary btn-sm"
                                            onclick="addSizeTierField()">
                                            <i class="fas fa-plus me-1"></i> إضافة سعر للمقاس
                                        </button>
                                    </div>

                                    <div id="sizeTiersContainer">
                                        @foreach ($product->sizeTiers as $index => $tier)
                                            <div class="dynamic-field">
                                                <div class="dynamic-field-header">
                                                    <div class="dynamic-field-title">سعر حسب المقاس {{ $index + 1 }}
                                                    </div>
                                                    <button type="button" class="dynamic-field-remove"
                                                        onclick="removeField(this)">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                                <div class="row" bis_skin_checked="1">
                                                    <div class="col-md-4 mb-2" bis_skin_checked="1">
                                                        <input type="text" class="form-control"
                                                            name="size_tiers[{{ $index }}][size_name]"
                                                            value="{{ $tier->size->name ?? '' }}"
                                                            placeholder="اسم المقاس" required>
                                                    </div>
                                                    <div class="col-md-3 mb-2" bis_skin_checked="1">
                                                        <input type="number" class="form-control"
                                                            name="size_tiers[{{ $index }}][quantity]"
                                                            value="{{ $tier->quantity }}" placeholder="الكمية"
                                                            min="1" required>
                                                    </div>
                                                    <div class="col-md-3 mb-2" bis_skin_checked="1">
                                                        <input type="number" class="form-control"
                                                            name="size_tiers[{{ $index }}][price_per_unit]"
                                                            value="{{ $tier->price_per_unit }}"
                                                            placeholder="السعر للوحدة" step="0.01" required>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- SEO Information -->
                                <div class="mb-4" bis_skin_checked="1">
                                    <label class="form-label mb-3">إعدادات SEO</label>
                                    <div class="row" bis_skin_checked="1">
                                        <div class="col-md-6 mb-3" bis_skin_checked="1">
                                            <label for="slug" class="form-label">الرابط (Slug)</label>
                                            <div class="input-group" bis_skin_checked="1">
                                                <input type="text" class="form-control" id="slug" name="slug"
                                                    value="{{ old('slug', $product->slug) }}">
                                                <button type="button" class="btn btn-outline-secondary"
                                                    onclick="generateSlug()">
                                                    <i class="fas fa-sync-alt"></i>
                                                </button>
                                            </div>
                                            <small class="text-muted">رابط SEO الخاص بالمنتج</small>
                                        </div>

                                        <div class="col-md-6 mb-3" bis_skin_checked="1">
                                            <label for="meta_title" class="form-label">عنوان الصفحة (Meta Title)</label>
                                            <input type="text" class="form-control" id="meta_title" name="meta_title"
                                                value="{{ old('meta_title', $product->meta_title) }}">
                                            <small class="text-muted">الطول الموصى به: 50-60 حرفاً</small>
                                        </div>

                                        <div class="col-md-12 mb-3" bis_skin_checked="1">
                                            <label for="meta_description" class="form-label">وصف الصفحة (Meta
                                                Description)</label>
                                            <textarea class="form-control" id="meta_description" name="meta_description" rows="3">{{ old('meta_description', $product->meta_description) }}</textarea>
                                            <small class="text-muted">الطول الموصى به: 150-160 حرفاً</small>
                                        </div>

                                        <div class="col-md-12 mb-3" bis_skin_checked="1">
                                            <label for="meta_keywords" class="form-label">الكلمات المفتاحية</label>
                                            <input type="text" class="form-control" id="meta_keywords"
                                                name="meta_keywords"
                                                value="{{ old('meta_keywords', $product->meta_keywords) }}">
                                            <small class="text-muted">كلمات مفتاحية مفصولة بفواصل (,)</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-4" bis_skin_checked="1">
                                    <button type="button" class="btn btn-outline-secondary prev-step" data-prev="3">
                                        <i class="fas fa-arrow-right me-1"></i> السابق
                                    </button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save me-1"></i> حفظ التعديلات
                                    </button>
                                    <button type="button" class="btn btn-primary" onclick="saveAndContinue()">
                                        <i class="fas fa-redo me-1"></i> حفظ ومتابعة التعديل
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Add Modals -->
    <div class="modal fade quick-add-modal" id="quickAddModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="quickAddModalTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="quickAddForm">
                        <div id="quickAddFormContent">
                            <!-- Form content will be dynamically loaded here -->
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-primary" onclick="saveQuickAdd()">إضافة</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Image View Modal -->
    <div class="modal fade" id="imageViewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <img src="" alt="صورة المنتج" id="viewedImage" class="img-fluid w-100">
                </div>
            </div>
        </div>
    </div>

    <!-- Templates -->
    <template id="materialFieldTemplate">
        <div class="material-item">
            <div class="material-header">
                <div class="material-title">
                    <strong>مادة جديدة</strong>
                </div>
                <button type="button" class="material-remove" onclick="removeMaterial(this)">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="row">
                <div class="col-md-4 mb-2">
                    <select class="form-select material-select" name="materials[][material_id]" required>
                        <option value="">اختر المادة</option>
                        @foreach ($materials as $material)
                            <option value="{{ $material->id }}">{{ $material->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-2">
                    <input type="number" class="form-control" name="materials[][quantity]" placeholder="الكمية"
                        min="0" step="0.01">
                </div>
                <div class="col-md-4 mb-2">
                    <select class="form-select" name="materials[][unit]">
                        <option value="piece">قطعة</option>
                        <option value="meter">متر</option>
                        <option value="kg">كجم</option>
                        <option value="liter">لتر</option>
                        <option value="gram">جرام</option>
                    </select>
                </div>
                <div class="col-md-12 mb-2">
                    <div class="material-additional-price">
                        <label class="form-label">سعر إضافي</label>
                        <input type="number" class="form-control" name="materials[][additional_price]"
                            placeholder="سعر إضافي" step="0.01" min="0">
                    </div>
                </div>
            </div>
        </div>
    </template>

    <template id="featureFieldTemplate">
        <div class="dynamic-field">
            <div class="dynamic-field-header">
                <div class="dynamic-field-title">مواصفة جديدة</div>
                <button type="button" class="dynamic-field-remove" onclick="removeField(this)">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="row">
                <div class="col-md-5 mb-2">
                    <input type="text" class="form-control" name="features[][name]" placeholder="اسم المواصفة"
                        required>
                </div>
                <div class="col-md-7 mb-2">
                    <input type="text" class="form-control" name="features[][value]" placeholder="القيمة" required>
                </div>
            </div>
        </div>
    </template>

    <template id="productOptionFieldTemplate">
        <div class="dynamic-field">
            <div class="dynamic-field-header">
                <div class="dynamic-field-title">خيار جديد</div>
                <button type="button" class="dynamic-field-remove" onclick="removeField(this)">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="row">
                <div class="col-md-4 mb-2">
                    <input type="text" class="form-control" name="product_options[][option_name]"
                        placeholder="اسم الخيار" required>
                </div>
                <div class="col-md-4 mb-2">
                    <input type="text" class="form-control" name="product_options[][option_value]"
                        placeholder="القيمة" required>
                </div>
                <div class="col-md-2 mb-2">
                    <input type="number" class="form-control" name="product_options[][additional_price]"
                        placeholder="السعر الإضافي" step="0.01">
                </div>
                <div class="col-md-2 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="product_options[][is_required]"
                            value="1">
                        <label class="form-check-label">مطلوب</label>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <template id="pricingTierFieldTemplate">
        <div class="dynamic-field">
            <div class="dynamic-field-header">
                <div class="dynamic-field-title">شريحة تسعير جديدة</div>
                <button type="button" class="dynamic-field-remove" onclick="removeField(this)">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="row">
                <div class="col-md-3 mb-2">
                    <input type="number" class="form-control" name="pricing_tiers[][quantity]" placeholder="الكمية"
                        min="1" required>
                </div>
                <div class="col-md-3 mb-2">
                    <input type="number" class="form-control" name="pricing_tiers[][price_per_unit]"
                        placeholder="السعر للوحدة" step="0.01" required>
                </div>
                <div class="col-md-3 mb-2">
                    <input type="number" class="form-control" name="pricing_tiers[][discount_percentage]"
                        placeholder="خصم %" min="0" max="100" step="0.01">
                </div>
                <div class="col-md-3 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="pricing_tiers[][is_sample]"
                            value="1">
                        <label class="form-check-label">عينة</label>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <template id="sizeTierFieldTemplate">
        <div class="dynamic-field">
            <div class="dynamic-field-header">
                <div class="dynamic-field-title">سعر حسب المقاس جديد</div>
                <button type="button" class="dynamic-field-remove" onclick="removeField(this)">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="row">
                <div class="col-md-4 mb-2">
                    <input type="text" class="form-control" name="size_tiers[][size_name]"
                        placeholder="اسم المقاس" required>
                </div>
                <div class="col-md-3 mb-2">
                    <input type="number" class="form-control" name="size_tiers[][quantity]" placeholder="الكمية"
                        min="1" required>
                </div>
                <div class="col-md-3 mb-2">
                    <input type="number" class="form-control" name="size_tiers[][price_per_unit]"
                        placeholder="السعر للوحدة" step="0.01" required>
                </div>
            </div>
        </div>
    </template>

    <template id="colorSwatchTemplate">
        <div class="color-swatch" data-id="{id}">
            <div class="color-preview" style="background-color: {hex};"></div>
            <span>{name}</span>
            <input type="hidden" name="colors[]" value="{id}">
            <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="removeColor({id})">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </template>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        // Global variables
        let materialCounter = {{ $product->materials->count() }};
        let featureCounter = {{ $product->features->count() }};
        let optionCounter = {{ $product->options->count() }};
        let pricingTierCounter = {{ $product->pricingTiers->count() }};
        let sizeTierCounter = {{ $product->sizeTiers->count() }};
        let removedImages = [];
        let currentQuickAddType = '';

        $(document).ready(function() {
            // Initialize Summernote
            $('.summernote').summernote({
                height: 200,
                lang: 'ar-AR',
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough', 'superscript', 'subscript']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });

            // Initialize Select2
            $('.select2').select2({
                placeholder: 'اختر الخيارات',
                allowClear: true
            });

            // Initialize image sortable
            if (document.getElementById('existingImagesGrid')) {
                Sortable.create(document.getElementById('existingImagesGrid'), {
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    onEnd: function() {
                        updateImagesOrder();
                    }
                });
            }

            // Step Navigation
            $('.next-step').click(function() {
                const nextStep = $(this).data('next');
                navigateToStep(nextStep);
            });

            $('.prev-step').click(function() {
                const prevStep = $(this).data('prev');
                navigateToStep(prevStep);
            });

            // Discount Toggle
            $('#has_discount').change(function() {
                if ($(this).is(':checked')) {
                    $('#discountSection').slideDown();
                } else {
                    $('#discountSection').slideUp();
                }
            });

            // Image Upload Handlers
            $('#main_image').change(function(e) {
                previewImage(e.target.files[0], 'main');
            });

            $('#additional_images').change(function(e) {
                previewMultipleImages(e.target.files);
            });

            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();

            // Auto-generate slug when name changes
            $('#name').on('blur', function() {
                if (!$('#slug').val()) {
                    generateSlug();
                }
            });

            // Initialize fancybox for image viewing
            $('[data-fancybox]').fancybox();
        });

        // Step Navigation
        function navigateToStep(step) {
            // Hide all steps
            $('.step-card').hide();

            // Show target step
            $(`.step-${step}`).show();

            // Update wizard steps
            $('.wizard-step').removeClass('active completed');

            for (let i = 1; i <= step; i++) {
                $(`#step${i}`).addClass(i === step ? 'active' : 'completed');
            }

            // Scroll to top
            $('html, body').animate({
                scrollTop: 0
            }, 300);
        }

        // تعديل دالة previewImage
        function previewImage(file, type) {
            if (!file.type.match('image.*')) {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: 'الملف يجب أن يكون صورة'
                });
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                if (type === 'main') {
                    // تحديث الصورة في العرض المباشر
                    $('.preview-image').attr('src', e.target.result);

                    // تحديث الصورة في المعاينة
                    const previewHtml = `
            <div class="image-preview-item">
                <span class="primary-badge">رئيسية جديدة</span>
                <img src="${e.target.result}" alt="الصورة الرئيسية الجديدة">
                <div class="image-actions">
                    <button type="button" class="btn btn-info btn-sm" onclick="viewImage('${e.target.result}')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            `;

                    // تأكد من وجود عنصر للمعاينة
                    let previewGrid = $('.step-2 .image-preview-grid').first();
                    if (previewGrid.length === 0) {
                        // إنشاء عنصر جديد
                        const parent = $('.step-2 .mb-4').first();
                        const newGrid = $('<div class="image-preview-grid"></div>');
                        newGrid.html(previewHtml);
                        parent.append(newGrid);
                    } else {
                        previewGrid.html(previewHtml);
                    }

                    $('#remove_main_image').val('0');
                }
            };
            reader.readAsDataURL(file);
        }

        function previewMultipleImages(files) {
            const container = $('#newImagesPreview');
            container.empty();

            Array.from(files).forEach((file, index) => {
                if (!file.type.match('image.*')) return;

                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewHtml = `
                    <div class="image-preview-item" data-new-index="${index}">
                        <span class="primary-badge">جديد</span>
                        <img src="${e.target.result}" alt="صورة جديدة">
                        <div class="image-actions">
                            <button type="button" class="btn btn-info btn-sm" onclick="viewImage('${e.target.result}')">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="removeNewImage(${index})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
                    container.append(previewHtml);
                };
                reader.readAsDataURL(file);
            });
        }

        function viewImage(src) {
            $('#viewedImage').attr('src', src);
            new bootstrap.Modal(document.getElementById('imageViewModal')).show();
        }

        function removeMainImage() {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: 'سيتم إزالة الصورة الرئيسية',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#remove_main_image').val('1');
                    $('.image-preview-grid').first().empty();
                    $('#main_image').val('');
                    Swal.fire('تم الحذف!', 'تم إزالة الصورة الرئيسية', 'success');
                }
            });
        }

        function removeNewMainImage() {
            $('#main_image').val('');
            $('.image-preview-grid').first().empty();
            $('#remove_main_image').val('0');
        }

        function removeAdditionalImage(imageId) {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: 'سيتم إزالة هذه الصورة',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    removedImages.push(imageId);
                    $('#removed_images').val(removedImages.join(','));
                    $(`.image-preview-item[data-id="${imageId}"]`).remove();
                    Swal.fire('تم الحذف!', 'تم إزالة الصورة', 'success');
                }
            });
        }

        function removeNewImage(index) {
            $(`.image-preview-item[data-new-index="${index}"]`).remove();

            // Remove file from input
            const files = Array.from($('#additional_images')[0].files);
            files.splice(index, 1);

            const dt = new DataTransfer();
            files.forEach(file => dt.items.add(file));
            $('#additional_images')[0].files = dt.files;
        }

        function setAsPrimary(imageId) {
            $('#primary_image_id').val(imageId);

            // Update UI
            $('.primary-badge').text('رئيسية');
            $(`.image-preview-item[data-id="${imageId}"] .primary-badge`).text('رئيسية');

            Swal.fire({
                icon: 'success',
                title: 'تم التحديث',
                text: 'تم تعيين الصورة كرئيسية',
                timer: 1500,
                showConfirmButton: false
            });
        }

        function updateImagesOrder() {
            const order = [];
            $('#existingImagesGrid .image-preview-item').each(function() {
                const id = $(this).data('id');
                if (id) order.push(id);
            });
            $('#images_order').val(order.join(','));
        }

        // Color Management Functions
        function addColor(element) {
            const colorId = $(element).data('id');
            const colorName = $(element).data('name');
            const colorHex = $(element).data('hex');

            // Check if color already selected
            if ($(`#selectedColorsContainer .color-swatch[data-id="${colorId}"]`).length > 0) {
                return;
            }

            // Create color swatch
            const template = $('#colorSwatchTemplate').html();
            const swatchHtml = template
                .replace(/{id}/g, colorId)
                .replace(/{name}/g, colorName)
                .replace(/{hex}/g, colorHex);

            $('#selectedColorsContainer').append(swatchHtml);
            $(element).remove();

            // Add color price input
            const priceInputHtml = `
            <div class="col-md-4 mb-3 color-price-${colorId}">
                <label class="form-label">سعر إضافي للون: ${colorName}</label>
                <input type="number" class="form-control color-price-input" 
                       name="color_prices[${colorId}]" 
                       placeholder="السعر الإضافي" step="0.01" min="0">
            </div>
        `;
            $('#colorPricesContainer').append(priceInputHtml);
        }

        function removeColor(colorId) {
            $(`.color-swatch[data-id="${colorId}"]`).remove();
            $(`.color-price-${colorId}`).remove();

            // Add color back to available colors
            const colorName = $(`.color-swatch[data-id="${colorId}"] span`).text();
            const colorHex = $(`.color-swatch[data-id="${colorId}"] .color-preview`).css('background-color');

            const colorItem = `
            <div class="color-item" 
                 style="background-color: ${colorHex};"
                 data-id="${colorId}"
                 data-name="${colorName}"
                 data-hex="${colorHex}"
                 onclick="addColor(this)">
            </div>
        `;
            $('#availableColorsGrid').append(colorItem);
        }

        // Dynamic Fields Functions
        function addMaterialField() {
            const template = $('#materialFieldTemplate').html();
            const newField = $(template);

            // Update indices
            newField.find('[name]').each(function() {
                const name = $(this).attr('name');
                $(this).attr('name', name.replace('[]', `[${materialCounter}]`));
            });

            // Initialize select2 for material select
            newField.find('.material-select').select2({
                placeholder: 'اختر المادة'
            });

            $('#materialsContainer').append(newField);
            materialCounter++;
        }

        function addFeatureField() {
            const template = $('#featureFieldTemplate').html();
            const newField = $(template);

            // Update indices
            newField.find('[name]').each(function() {
                const name = $(this).attr('name');
                $(this).attr('name', name.replace('[]', `[${featureCounter}]`));
            });

            $('#featuresContainer').append(newField);
            featureCounter++;
        }

        function addProductOptionField() {
            const template = $('#productOptionFieldTemplate').html();
            const newField = $(template);

            // Update indices
            newField.find('[name]').each(function() {
                const name = $(this).attr('name');
                $(this).attr('name', name.replace('[]', `[${optionCounter}]`));
            });

            $('#productOptionsContainer').append(newField);
            optionCounter++;
        }

        function addPricingTierField() {
            const template = $('#pricingTierFieldTemplate').html();
            const newField = $(template);

            // Update indices
            newField.find('[name]').each(function() {
                const name = $(this).attr('name');
                $(this).attr('name', name.replace('[]', `[${pricingTierCounter}]`));
            });

            $('#pricingTiersContainer').append(newField);
            pricingTierCounter++;
        }

        function addSizeTierField() {
            const template = $('#sizeTierFieldTemplate').html();
            const newField = $(template);

            // Update indices
            newField.find('[name]').each(function() {
                const name = $(this).attr('name');
                $(this).attr('name', name.replace('[]', `[${sizeTierCounter}]`));
            });

            $('#sizeTiersContainer').append(newField);
            sizeTierCounter++;
        }

        function removeMaterial(button) {
            $(button).closest('.material-item').remove();
        }
        // Image Management Functions
        function previewMainImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Show preview container
                    $('#newMainImagePreview').show();

                    // Set image source
                    $('#newMainImagePreviewImg').attr('src', e.target.result);

                    // Update product preview in step 1
                    $('.preview-image').attr('src', e.target.result);

                    // Reset remove main image flag
                    $('#remove_main_image').val('0');

                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'تم اختيار الصورة',
                        text: 'تم اختيار الصورة الرئيسية الجديدة بنجاح',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function removeCurrentMainImage() {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: 'سيتم إزالة الصورة الرئيسية الحالية',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Set remove flag
                    $('#remove_main_image').val('1');

                    // Hide current image
                    $('#currentMainImageContainer').slideUp();

                    // Show message
                    $('.preview-image').attr('src', 'https://via.placeholder.com/100x100?text=No+Image');

                    // Show notification
                    Swal.fire('تم الحذف!', 'سيتم إزالة الصورة الرئيسية بعد حفظ التعديلات', 'success');
                }
            });
        }

        function removeNewMainImage() {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: 'سيتم إلغاء اختيار الصورة الجديدة',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'نعم، ألغِ',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Hide preview
                    $('#newMainImagePreview').hide();

                    // Clear file input
                    $('#image').val('');

                    // Reset product preview if there's no current image
                    if ($('#remove_main_image').val() === '1' || !$('#currentMainImageContainer').is(':visible')) {
                        $('.preview-image').attr('src', 'https://via.placeholder.com/100x100?text=No+Image');
                    } else {
                        // Reset to current image
                        const currentImg = $('#currentMainImageContainer img').attr('src');
                        $('.preview-image').attr('src', currentImg);
                    }

                    Swal.fire('تم الإلغاء!', 'تم إلغاء اختيار الصورة الجديدة', 'success');
                }
            });
        }

        function viewNewMainImage() {
            const src = $('#newMainImagePreviewImg').attr('src');
            if (src) {
                $('#viewedImage').attr('src', src);
                new bootstrap.Modal(document.getElementById('imageViewModal')).show();
            }
        }

        // Initialize file input change event
        $(document).ready(function() {
            // Main image change handler
            $('#image').change(function(e) {
                if (e.target.files && e.target.files[0]) {
                    previewMainImage(this);
                }
            });

            // Prevent form submission if main image is required but not provided
            $('#editProductForm').on('submit', function(e) {
                // Check if main image is being removed but no new one is provided
                if ($('#remove_main_image').val() === '1' && !$('#image').val()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'تحذير',
                        text: 'لقد قمت بحذف الصورة الرئيسية ولم تختار صورة جديدة. هل تريد الاستمرار؟',
                        showCancelButton: true,
                        confirmButtonText: 'نعم، استمر',
                        cancelButtonText: 'إلغاء'
                    }).then((result) => {
                        if (!result.isConfirmed) {
                            e.preventDefault();
                        }
                    });
                }
            });
        });

        // تعديل دالة viewImage العامة
        function viewImage(src) {
            if (src) {
                $('#viewedImage').attr('src', src);
                new bootstrap.Modal(document.getElementById('imageViewModal')).show();
            }
        }

        // Additional Images functions remain the same
        // ... باقي الدوال كما هي
        function removeField(button) {
            $(button).closest('.dynamic-field').remove();
        }

        // Quick Add Modal Functions
        function openQuickAddModal(type) {
            currentQuickAddType = type;

            let title = '';
            let formContent = '';

            switch (type) {
                case 'category':
                    title = 'إضافة قسم جديد';
                    formContent = `
                    <div class="mb-3">
                        <label class="form-label">اسم القسم</label>
                        <input type="text" class="form-control" id="category_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">القسم الرئيسي</label>
                        <select class="form-control" id="parent_category_id">
                            <option value="">بدون قسم رئيسي</option>
                            @foreach ($categories as $category)
                                @if ($category->isParent())
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                `;
                    break;

                case 'color':
                    title = 'إضافة لون جديد';
                    formContent = `
                    <div class="mb-3">
                        <label class="form-label">اسم اللون</label>
                        <input type="text" class="form-control" id="color_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الكود اللوني</label>
                        <div class="color-picker-input">
                            <input type="color" id="color_hex" value="#696cff">
                            <input type="text" class="form-control" id="color_hex_code" value="#696cff" readonly>
                        </div>
                    </div>
                `;
                    break;

                case 'material':
                    title = 'إضافة مادة جديدة';
                    formContent = `
                    <div class="mb-3">
                        <label class="form-label">اسم المادة</label>
                        <input type="text" class="form-control" id="material_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الوصف</label>
                        <textarea class="form-control" id="material_description" rows="3"></textarea>
                    </div>
                `;
                    break;

                case 'printing_method':
                    title = 'إضافة طريقة طباعة جديدة';
                    formContent = `
                    <div class="mb-3">
                        <label class="form-label">الاسم</label>
                        <input type="text" class="form-control" id="printing_method_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الوصف</label>
                        <textarea class="form-control" id="printing_method_description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">السعر الأساسي</label>
                        <input type="number" class="form-control" id="printing_method_price" step="0.01" min="0" required>
                    </div>
                `;
                    break;

                case 'print_location':
                    title = 'إضافة مكان طباعة جديد';
                    formContent = `
                    <div class="mb-3">
                        <label class="form-label">الاسم</label>
                        <input type="text" class="form-control" id="print_location_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">النوع</label>
                        <select class="form-control" id="print_location_type">
                            <option value="front">أمامي</option>
                            <option value="back">خلفي</option>
                            <option value="side">جانبي</option>
                            <option value="sleeve">كم</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">السعر الإضافي</label>
                        <input type="number" class="form-control" id="print_location_price" step="0.01" min="0" required>
                    </div>
                `;
                    break;

                case 'offer':
                    title = 'إضافة عرض جديد';
                    formContent = `
                    <div class="mb-3">
                        <label class="form-label">اسم العرض</label>
                        <input type="text" class="form-control" id="offer_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الصورة</label>
                        <input type="file" class="form-control" id="offer_image" accept="image/*">
                    </div>
                `;
                    break;
            }

            $('#quickAddModalTitle').text(title);
            $('#quickAddFormContent').html(formContent);

            // Initialize color picker
            if (type === 'color') {
                $('#color_hex').change(function() {
                    $('#color_hex_code').val($(this).val());
                });
            }

            new bootstrap.Modal(document.getElementById('quickAddModal')).show();
        }

        async function saveQuickAdd() {
            const formData = new FormData();

            switch (currentQuickAddType) {
                case 'category':
                    formData.append('name', $('#category_name').val());
                    formData.append('parent_id', $('#parent_category_id').val());
                    break;

                case 'color':
                    formData.append('name', $('#color_name').val());
                    formData.append('hex_code', $('#color_hex_code').val());
                    break;

                case 'material':
                    formData.append('name', $('#material_name').val());
                    formData.append('description', $('#material_description').val());
                    break;

                case 'printing_method':
                    formData.append('name', $('#printing_method_name').val());
                    formData.append('description', $('#printing_method_description').val());
                    formData.append('base_price', $('#printing_method_price').val());
                    break;

                case 'print_location':
                    formData.append('name', $('#print_location_name').val());
                    formData.append('type', $('#print_location_type').val());
                    formData.append('additional_price', $('#print_location_price').val());
                    break;

                case 'offer':
                    formData.append('name', $('#offer_name').val());
                    if ($('#offer_image')[0].files[0]) {
                        formData.append('image', $('#offer_image')[0].files[0]);
                    }
                    break;
            }

            try {
                const response = await fetch(`/admin/quick-add/${currentQuickAddType}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok) {
                    // Close modal
                    bootstrap.Modal.getInstance(document.getElementById('quickAddModal')).hide();

                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'تمت الإضافة',
                        text: data.message || 'تم الإضافة بنجاح'
                    });

                    // Update UI based on type
                    updateUIAfterQuickAdd(data);

                } else {
                    throw new Error(data.message || 'حدث خطأ أثناء الإضافة');
                }

            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: error.message
                });
            }
        }

        function updateUIAfterQuickAdd(data) {
            switch (currentQuickAddType) {
                case 'category':
                    // Add new option to category select
                    const option = new Option(data.item.name, data.item.id);
                    $('#category_id').append(option).val(data.item.id).trigger('change');
                    break;

                case 'color':
                    // Add color to available colors grid
                    const colorItem = `
                    <div class="color-item" 
                         style="background-color: ${data.item.hex_code};"
                         data-id="${data.item.id}"
                         data-name="${data.item.name}"
                         data-hex="${data.item.hex_code}"
                         onclick="addColor(this)">
                    </div>
                `;
                    $('#availableColorsGrid').append(colorItem);
                    break;

                case 'material':
                    // Add option to material select in all material fields
                    $('.material-select').each(function() {
                        const option = new Option(data.item.name, data.item.id);
                        $(this).append(option);
                    });
                    break;

                case 'printing_method':
                    // Add option to printing methods select
                    const printingOption = new Option(
                        `${data.item.name} - ${data.item.base_price} ج.م`,
                        data.item.id
                    );
                    $('#printingMethodsSelect').append(printingOption);
                    break;

                case 'print_location':
                    // Add option to print locations select
                    const locationOption = new Option(
                        `${data.item.name} (${data.item.type}) - ${data.item.additional_price} ج.م`,
                        data.item.id
                    );
                    $('#printLocationsSelect').append(locationOption);
                    break;

                case 'offer':
                    // Add option to offers select
                    const offerOption = new Option(data.item.name, data.item.id);
                    $('#offersSelect').append(offerOption);
                    break;
            }
        }

        // Slug Generation
        function generateSlug() {
            const name = $('#name').val();
            if (!name) return;

            let slug = name
                .toLowerCase()
                .replace(/[^\u0600-\u06FF\w\s]/g, '')
                .replace(/\s+/g, '-')
                .replace(/--+/g, '-')
                .trim();

            // Add product ID to ensure uniqueness
            slug += '-' + {{ $product->id }};

            $('#slug').val(slug);
        }

        // Form Submission
        function saveAndContinue() {
            // Submit form via AJAX
            const formData = new FormData(document.getElementById('editProductForm'));

            fetch('{{ route('admin.products.update', $product->id) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'تم الحفظ',
                            text: 'تم حفظ التعديلات بنجاح',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            // Reload page to show updated data
                            window.location.reload();
                        });
                    } else {
                        throw new Error(data.message || 'حدث خطأ أثناء الحفظ');
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: error.message
                    });
                });
        }

        // Form Validation
        document.getElementById('editProductForm').addEventListener('submit', function(e) {
            // Basic validation
            const name = $('#name').val();
            const price = $('#price').val();
            const stock = $('#stock').val();

            if (!name || !price || !stock) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'بيانات ناقصة',
                    text: 'يرجى ملء جميع الحقول المطلوبة',
                    confirmButtonColor: '#3085d6'
                });
            }

            // Validate discount
            if ($('#has_discount').is(':checked')) {
                const discountValue = $('#discount_value').val();
                if (!discountValue || discountValue <= 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'قيمة خصم غير صالحة',
                        text: 'يرجى إدخال قيمة خصم صحيحة',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }
            }
        });
    </script>
@endsection
