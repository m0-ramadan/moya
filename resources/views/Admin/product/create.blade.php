@extends('Admin.layout.master')

@section('title', 'إضافة منتج جديد')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <style>
        body {
            font-family: "Cairo", sans-serif !important;
        }

        .step-card {
            background: var(--bs-card-bg);
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
            border-bottom: 2px solid var(--bs-border-color);
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
            color: var(--bs-heading-color);
            margin-bottom: 5px;
        }

        .step-description {
            color: var(--bs-secondary-color);
            font-size: 14px;
        }

        /* Image Management */
        .image-upload-container {
            border: 2px dashed var(--bs-border-color);
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            background: var(--bs-light-bg-subtle);
            min-height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .image-upload-container:hover {
            border-color: #696cff;
            background: rgba(105, 108, 255, 0.05);
        }

        .image-upload-container i {
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
        .color-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 15px;
        }

        .color-item {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 3px solid var(--bs-card-bg);
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

        /* Selected Items */
        .selected-items-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }

        .selected-item {
            background: var(--bs-light-bg-subtle);
            border: 1px solid var(--bs-border-color);
            border-radius: 8px;
            padding: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
            min-width: 150px;
        }

        .selected-item-remove {
            position: absolute;
            top: -5px;
            left: -5px;
            width: 20px;
            height: 20px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            cursor: pointer;
            z-index: 1;
        }

        .selected-item-content {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
        }

        .selected-item-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: var(--bs-primary-border-subtle);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #696cff;
        }

        .selected-item-title {
            font-size: 14px;
            font-weight: 500;
        }

        .selected-item-price {
            font-size: 12px;
            color: var(--bs-success);
            background: var(--bs-success-bg-subtle);
            padding: 2px 6px;
            border-radius: 4px;
        }

        .color-preview {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 1px solid var(--bs-border-color);
        }

        /* Price Input Group */
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
            color: var(--bs-body-color);
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
            background: var(--bs-border-color);
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
            background: var(--bs-border-color);
            color: var(--bs-secondary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-weight: bold;
            border: 4px solid var(--bs-card-bg);
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
            color: var(--bs-secondary-color);
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
            background: var(--bs-info-bg-subtle);
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

        /* Dynamic Fields */
        .dynamic-field {
            background: var(--bs-light-bg-subtle);
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
            border-bottom: 1px solid var(--bs-border-color);
        }

        .dynamic-field-title {
            font-weight: 600;
            color: var(--bs-heading-color);
        }

        .dynamic-field-remove {
            color: #e74c3c;
            cursor: pointer;
            background: none;
            border: none;
            font-size: 18px;
        }

        /* Material Item */
        .material-item {
            background: var(--bs-light-bg-subtle);
            border: 1px solid var(--bs-border-color);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .material-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--bs-border-color);
        }

        .material-title {
            font-weight: 600;
            color: var(--bs-heading-color);
        }

        .material-remove {
            color: #e74c3c;
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
        }

        /* Additional Price Input */
        .additional-price-input {
            background: var(--bs-info-bg-subtle);
            border: 1px dashed var(--bs-info-border-subtle);
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
        }

        /* Price Editor Modal */
        .price-editor .modal-dialog {
            max-width: 400px;
        }

        .price-editor .modal-content {
            border-radius: 15px;
            overflow: hidden;
        }

        .price-editor .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-bottom: none;
            padding: 20px 30px;
        }

        .price-editor .modal-body {
            padding: 30px;
        }

        /* Preview Image */
        .preview-image {
            width: 100px;
            height: 100px;
            border-radius: 10px;
            object-fit: cover;
            margin-bottom: 10px;
        }

        /* Stock Warning */
        .stock-warning {
            background: var(--bs-warning-bg-subtle);
            border: 1px solid var(--bs-warning-border-subtle);
            color: var(--bs-warning-text);
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
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

        /* Image Preview Styles */
        .image-preview {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            height: 150px;
        }

        .image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .image-preview:hover .image-overlay {
            opacity: 1;
        }

        .main-image-preview {
            max-width: 300px;
            position: relative;
        }

        .main-image-preview .image-overlay {
            flex-direction: column;
            gap: 10px;
        }

        /* Drag and Drop */
        .sortable-item {
            cursor: move;
        }

        .sortable-item.sortable-ghost {
            opacity: 0.4;
        }

        .sortable-item.sortable-chosen {
            box-shadow: 0 0 20px rgba(105, 108, 255, 0.3);
        }

        /* Color with price */
        .color-with-price {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .color-price-input {
            width: 120px;
        }

        /* Image Preview Grid */
        .preview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 10px;
            margin-top: 15px;
        }

        .grid-item {
            position: relative;
            height: 120px;
            border-radius: 8px;
            overflow: hidden;
        }

        .grid-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .grid-item-actions {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.7);
            padding: 5px;
            display: flex;
            justify-content: center;
            gap: 5px;
        }
    </style>
    <style>
        /* Text Ads Styles */
        .text-ad-field {
            background: var(--bs-light-bg-subtle);
            border: 1px solid var(--bs-border-color);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            position: relative;
        }

        .text-ad-field textarea {
            border: none;
            background: transparent;
            resize: vertical;
            min-height: 80px;
        }

        .text-ad-remove {
            position: absolute;
            top: -10px;
            left: -10px;
            width: 28px;
            height: 28px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            cursor: pointer;
            z-index: 1;
            border: 2px solid white;
        }

        .text-ad-remove:hover {
            background: #c82333;
            transform: scale(1.1);
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
                    <a href="{{ route('admin.products.index') }}">المنتجات</a>
                </li>
                <li class="breadcrumb-item active">إضافة منتج جديد</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">إضافة منتج جديد</h5>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-right me-1"></i> رجوع للقائمة
                        </a>
                    </div>

                    <div class="card-body">
                        <!-- Quick Guide -->
                        <div class="alert-guide">
                            <h6><i class="fas fa-lightbulb me-2"></i>نصائح سريعة:</h6>
                            <ul>
                                <li>يمكنك إضافة ألوان، مواد، طرق طباعة، وأماكن طباعة جديدة مباشرة من النموذج</li>
                                <li>يمكنك تحديد سعر إضافي لكل عنصر تضيفه</li>
                                <li>يمكنك تعديل الأسعار بعد إضافة العناصر</li>
                                <li>الصورة الرئيسية ضرورية، الصور الإضافية اختيارية</li>
                            </ul>
                        </div>

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

                        <!-- Wizard Steps -->
                        <div class="wizard-steps">
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
                                <div class="wizard-step-label">الخيارات والإضافات</div>
                            </div>
                            <div class="wizard-step" id="step4">
                                <div class="wizard-step-circle">4</div>
                                <div class="wizard-step-label">التأكيد والإرسال</div>
                            </div>
                        </div>

                        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data"
                            id="productForm">
                            @csrf

                            <!-- Step 1: Basic Information -->
                            <div class="step-card step-1">
                                <div class="step-header">
                                    <div class="step-number">1</div>
                                    <div>
                                        <h5 class="step-title">المعلومات الأساسية</h5>
                                        <p class="step-description">أدخل المعلومات الأساسية للمنتج</p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-8 mb-3">
                                        <label for="name" class="form-label required">اسم المنتج</label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            value="{{ old('name') }}" required>
                                        <small class="text-muted">اسم واضح ومعبر عن المنتج</small>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="category_id" class="form-label required">القسم</label>
                                        <div class="input-group">
                                            <select class="form-control select2" id="category_id" name="category_id"
                                                required>
                                                <option value="">اختر القسم</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}"
                                                        {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                        @if (!$category->isParent())
                                                            ({{ $category->parent->name ?? '' }})
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

                                    <div class="col-12 mb-3">
                                        <label for="description" class="form-label">الوصف</label>
                                        <textarea class="form-control summernote" id="description" name="description" rows="6">{{ old('description') }}</textarea>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="status_id" class="form-label required">الحالة</label>
                                        <select class="form-select" id="status_id" name="status_id" required>
                                            <option value="1" {{ old('status_id', 1) == 1 ? 'selected' : '' }}>نشط
                                            </option>
                                            <option value="2" {{ old('status_id') == 2 ? 'selected' : '' }}>غير نشط
                                            </option>
                                            <option value="3" {{ old('status_id') == 3 ? 'selected' : '' }}>مسودة
                                            </option>
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="stock" class="form-label required">الكمية المتاحة</label>
                                        <input type="number" class="form-control" id="stock" name="stock"
                                            value="{{ old('stock', 0) }}" min="0" required>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <div></div>
                                    <button type="button" class="btn btn-primary next-step" data-next="2">
                                        التالي <i class="fas fa-arrow-left ms-1"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Step 2: Images & Pricing -->
                            <div class="step-card step-2" style="display: none;">
                                <div class="step-header">
                                    <div class="step-number">2</div>
                                    <div>
                                        <h5 class="step-title">الصور والتسعير</h5>
                                        <p class="step-description">أضف صور المنتج وحدد التسعير الأساسي</p>
                                    </div>
                                </div>

                                <!-- Main Image -->
                                <div class="mb-4">
                                    <label class="form-label required">الصورة الرئيسية</label>
                                    <div class="image-upload-container"
                                        onclick="document.getElementById('image').click()">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <p class="mb-0">انقر لرفع الصورة الرئيسية</p>
                                        <small class="text-muted">الحجم الموصى به: 800×800 بكسل</small>
                                    </div>
                                    <input type="file" id="image" name="image" accept="image/*"
                                        style="display: none;" onchange="previewMainImage(this)" required>

                                    <div id="mainImagePreview" class="main-image-preview mt-3" style="display: none;">
                                        <div class="image-preview">
                                            <img id="mainImagePreviewImg" src="" alt="الصورة الرئيسية">
                                            <div class="image-overlay">
                                                <button type="button" class="btn btn-info btn-sm"
                                                    onclick="viewMainImage()">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm"
                                                    onclick="removeMainImage()">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Additional Images -->
                                <div class="mb-4">
                                    <label class="form-label">صور إضافية</label>
                                    <div class="image-upload-container"
                                        onclick="document.getElementById('additional_images').click()">
                                        <i class="fas fa-images"></i>
                                        <p class="mb-0">انقر لرفع صور إضافية</p>
                                        <small class="text-muted">يمكنك رفع أكثر من صورة</small>
                                    </div>
                                    <input type="file" id="additional_images" name="additional_images[]"
                                        accept="image/*" multiple style="display: none;"
                                        onchange="previewAdditionalImages(this)">

                                    <div id="additionalImagesPreview" class="preview-grid mt-3"></div>
                                </div>

                                <!-- Basic Pricing -->
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <label for="price" class="form-label required">السعر الأساسي</label>
                                        <div class="price-input-group">
                                            <span class="input-group-text">ج.م</span>
                                            <input type="number" class="form-control" id="price" name="price"
                                                step="0.01" value="{{ old('price') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-8 mb-4">
                                        <label for="price_text" class="form-label required"> نص السعر </label>
                                        <div class="price-input-group">
                                            <span class="input-group-text">ج.م</span>
                                            <input type="text" class="form-control" id="price_text" name="price_text"
                                                value="{{ old('price_text') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="toggle-container">
                                            <label class="toggle-switch">
                                                <input type="checkbox" id="has_discount" name="has_discount"
                                                    {{ old('has_discount') ? 'checked' : '' }}>
                                                <span class="toggle-slider"></span>
                                            </label>
                                            <span class="toggle-label">هل يحتوي على خصم؟</span>
                                        </div>

                                        <div id="discountSection"
                                            style="{{ old('has_discount') ? '' : 'display: none;' }}">
                                            <div class="row mt-3">
                                                <div class="col-md-6 mb-3">
                                                    <label for="discount_type" class="form-label">نوع الخصم</label>
                                                    <select class="form-select" id="discount_type" name="discount_type">
                                                        <option value="percentage"
                                                            {{ old('discount_type') == 'percentage' ? 'selected' : '' }}>
                                                            نسبة مئوية %</option>
                                                        <option value="fixed"
                                                            {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>قيمة
                                                            ثابتة</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="discount_value" class="form-label">قيمة الخصم</label>
                                                    <input type="number" class="form-control" id="discount_value"
                                                        name="discount_value" step="0.01"
                                                        value="{{ old('discount_value') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Additional Options -->
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="toggle-container">
                                            <label class="toggle-switch">
                                                <input type="checkbox" id="includes_tax" name="includes_tax"
                                                    {{ old('includes_tax') ? 'checked' : '' }}>
                                                <span class="toggle-slider"></span>
                                            </label>
                                            <span class="toggle-label">يشمل الضريبة</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="toggle-container">
                                            <label class="toggle-switch">
                                                <input type="checkbox" id="includes_shipping" name="includes_shipping"
                                                    {{ old('includes_shipping') ? 'checked' : '' }}>
                                                <span class="toggle-slider"></span>
                                            </label>
                                            <span class="toggle-label">يشمل الشحن</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-outline-secondary prev-step" data-prev="1">
                                        <i class="fas fa-arrow-right me-1"></i> السابق
                                    </button>
                                    <button type="button" class="btn btn-primary next-step" data-next="3">
                                        التالي <i class="fas fa-arrow-left ms-1"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Step 3: Options & Additions -->
                            <div class="step-card step-3" style="display: none;">
                                <div class="step-header">
                                    <div class="step-number">3</div>
                                    <div>
                                        <h5 class="step-title">الخيارات والإضافات</h5>
                                        <p class="step-description">حدد الخيارات والإضافات المتاحة للمنتج</p>
                                    </div>
                                </div>

                                <!-- Colors with Prices -->
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <label class="form-label mb-0">الألوان المتاحة (يمكن تعديل الأسعار)</label>
                                        <button type="button" class="btn btn-outline-primary btn-sm"
                                            onclick="openQuickAddModal('color')">
                                            <i class="fas fa-plus me-1"></i> إضافة لون جديد
                                        </button>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">اختر الألوان:</label>
                                        <div class="color-grid" id="colorsGrid">
                                            @foreach ($colors as $color)
                                                <div class="color-item" data-id="{{ $color->id }}"
                                                    data-name="{{ $color->name }}" data-hex="{{ $color->hex_code }}"
                                                    data-price="0" onclick="toggleColorSelection(this)">
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div id="selectedColorsContainer" class="selected-items-container">
                                        <!-- Selected colors with prices will appear here -->
                                    </div>
                                </div>

                                <!-- Materials with Prices -->
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <label class="form-label mb-0">المواد المستخدمة</label>
                                        <button type="button" class="btn btn-outline-primary btn-sm"
                                            onclick="openQuickAddModal('material')">
                                            <i class="fas fa-plus me-1"></i> إضافة مادة جديدة
                                        </button>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">اختر المواد:</label>
                                        <select class="form-select select2" id="materialsSelect" multiple>
                                            @foreach ($materials as $material)
                                                <option value="{{ $material->id }}" data-name="{{ $material->name }}">
                                                    {{ $material->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div id="materialsContainer" class="row g-3 mt-3">
                                        <!-- Material fields with prices will be added here -->
                                    </div>

                                    <button type="button" class="btn btn-outline-primary btn-sm mt-3"
                                        onclick="addMaterialField()">
                                        <i class="fas fa-plus me-1"></i> إضافة مادة
                                    </button>
                                </div>

                                <!-- Printing Methods -->
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <label class="form-label mb-0">طرق الطباعة</label>
                                        <button type="button" class="btn btn-outline-primary btn-sm"
                                            onclick="openQuickAddModal('printing_method')">
                                            <i class="fas fa-plus me-1"></i> إضافة طريقة طباعة جديدة
                                        </button>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">اختر طرق الطباعة:</label>
                                        <select class="form-select select2" id="printingMethodsSelect" multiple>
                                            @foreach ($printingMethods as $method)
                                                <option value="{{ $method->id }}"
                                                    data-price="{{ $method->base_price }}"
                                                    data-name="{{ $method->name }}">
                                                    {{ $method->name }} - {{ $method->base_price }} ج.م
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div id="selectedPrintingMethods" class="selected-items-container">
                                        <!-- Selected printing methods will appear here -->
                                    </div>
                                </div>

                                <!-- Print Locations -->
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <label class="form-label mb-0">أماكن الطباعة</label>
                                        <button type="button" class="btn btn-outline-primary btn-sm"
                                            onclick="openQuickAddModal('print_location')">
                                            <i class="fas fa-plus me-1"></i> إضافة مكان طباعة جديد
                                        </button>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">اختر أماكن الطباعة:</label>
                                        <select class="form-select select2" id="printLocationsSelect" multiple>
                                            @foreach ($printLocations as $location)
                                                <option value="{{ $location->id }}" data-type="{{ $location->type }}"
                                                    data-price="{{ $location->additional_price }}"
                                                    data-name="{{ $location->name }}">
                                                    {{ $location->name }} ({{ $location->type }}) -
                                                    {{ $location->additional_price }} ج.م
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div id="selectedPrintLocations" class="selected-items-container">
                                        <!-- Selected print locations will appear here -->
                                    </div>
                                </div>

                                <!-- Offers -->
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <label class="form-label mb-0">العروض</label>
                                        <button type="button" class="btn btn-outline-primary btn-sm"
                                            onclick="openQuickAddModal('offer')">
                                            <i class="fas fa-plus me-1"></i> إضافة عرض جديد
                                        </button>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">اختر العروض:</label>
                                        <select class="form-select select2" id="offersSelect" multiple>
                                            @foreach ($offers as $offer)
                                                <option value="{{ $offer->id }}" data-name="{{ $offer->name }}">
                                                    {{ $offer->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div id="selectedOffers" class="selected-items-container">
                                        <!-- Selected offers will appear here -->
                                    </div>
                                </div>

                                <!-- Delivery Time & Warranty -->
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">وقت التوصيل</label>
                                        <div class="row">
                                            <div class="col-6">
                                                <label for="from_days" class="form-label">من (أيام)</label>
                                                <input type="number" class="form-control" id="from_days"
                                                    name="from_days" value="{{ old('from_days') }}" min="0">
                                            </div>
                                            <div class="col-6">
                                                <label for="to_days" class="form-label">إلى (أيام)</label>
                                                <input type="number" class="form-control" id="to_days" name="to_days"
                                                    value="{{ old('to_days') }}" min="0">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label for="warranty_months" class="form-label">الضمان (بالأشهر)</label>
                                        <input type="number" class="form-control" id="warranty_months"
                                            name="warranty_months" value="{{ old('warranty_months') }}" min="0">
                                    </div>
                                </div>
                                <p class="mb-1"><strong>النصوص الإعلانية:</strong> <span id="summary_text_ads"></span>
                                </p>
                                <!-- Text Ads Section -->
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <label class="form-label mb-0 fw-bold">النصوص الإعلانية للمنتج</label>
                                            <small class="text-muted d-block">أضف نصوصاً إعلانية تظهر مع المنتج لجذب
                                                العملاء</small>
                                        </div>
                                        <button type="button" class="btn btn-outline-primary btn-sm"
                                            onclick="addTextAdField()">
                                            <i class="fas fa-plus me-1"></i> إضافة نص إعلاني
                                        </button>
                                    </div>

                                    <div id="textAdsContainer" class="row g-3 mt-2">
                                        <!-- Text ad fields will be added here -->
                                        @if (old('text_ads'))
                                            @foreach (old('text_ads') as $index => $ad)
                                                <div class="col-md-12 mb-3">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <div class="row align-items-center">
                                                                <div class="col-md-10 mb-2">
                                                                    <textarea class="form-control" name="text_ads[{{ $index }}][name]" placeholder="أدخل النص الإعلاني"
                                                                        rows="3" required>{{ $ad['name'] ?? '' }}</textarea>
                                                                </div>
                                                                <div class="col-md-2 mb-2 text-center">
                                                                    <button type="button"
                                                                        class="btn btn-outline-danger btn-sm"
                                                                        onclick="removeTextAdField(this)">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>

                                    <div class="alert alert-info mt-2">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <small>النصوص الإعلانية تظهر بشكل بارز مع المنتج لجذب انتباه العملاء وتعزيز
                                            المبيعات</small>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-outline-secondary prev-step" data-prev="2">
                                        <i class="fas fa-arrow-right me-1"></i> السابق
                                    </button>
                                    <button type="button" class="btn btn-primary next-step" data-next="4">
                                        التالي <i class="fas fa-arrow-left ms-1"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Step 4: Confirmation -->
                            <div class="step-card step-4" style="display: none;">
                                <div class="step-header">
                                    <div class="step-number">4</div>
                                    <div>
                                        <h5 class="step-title">التأكيد والإرسال</h5>
                                        <p class="step-description">راجع المعلومات ثم أرسل النموذج</p>
                                    </div>
                                </div>

                                <!-- Summary -->
                                <div class="alert alert-info mb-4">
                                    <h6 class="alert-heading mb-2"><i class="fas fa-info-circle me-2"></i>ملخص المنتج</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>اسم المنتج:</strong> <span id="summary_name"></span>
                                            </p>
                                            <p class="mb-1"><strong>السعر الأساسي:</strong> <span
                                                    id="summary_price"></span> ج.م</p>
                                            <p class="mb-1"><strong>الكمية:</strong> <span id="summary_stock"></span>
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>الحالة:</strong> <span id="summary_status"></span>
                                            </p>
                                            <p class="mb-1"><strong>الألوان:</strong> <span id="summary_colors"></span>
                                            </p>
                                            <p class="mb-1"><strong>المواد:</strong> <span
                                                    id="summary_materials"></span></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Hidden Inputs -->
                                <div id="hiddenInputsContainer">
                                    <!-- Hidden inputs will be added here -->
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-outline-secondary prev-step" data-prev="3">
                                        <i class="fas fa-arrow-right me-1"></i> السابق
                                    </button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save me-1"></i> حفظ المنتج
                                    </button>
                                    <button type="button" class="btn btn-primary" onclick="saveAsDraft()">
                                        <i class="fas fa-file-alt me-1"></i> حفظ كمسودة
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Add Modal -->
    <div class="modal fade quick-add-modal" id="quickAddModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="quickAddModalTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="quickAddForm">
                        <div id="quickAddFormContent"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-primary" onclick="saveQuickAdd()">إضافة</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Price Editor Modal -->
    <div class="modal fade price-editor" id="priceEditorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">تعديل السعر</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">اسم العنصر</label>
                        <input type="text" class="form-control" id="priceEditorName" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">السعر الإضافي (ج.م)</label>
                        <input type="number" class="form-control" id="priceEditorPrice" step="0.01" min="0">
                    </div>
                    <input type="hidden" id="priceEditorType">
                    <input type="hidden" id="priceEditorId">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-primary" onclick="savePriceEdit()">حفظ</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Image View Modal -->
    <div class="modal fade" id="imageViewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <img id="viewedImage" src="" alt="صورة المنتج" class="img-fluid w-100">
                </div>
            </div>
        </div>
    </div>

    <!-- Templates -->
    <template id="colorItemTemplate">
        <div class="selected-item color-item" data-id="{id}">
            <div class="selected-item-remove" onclick="removeSelectedItem(this, 'color')">×</div>
            <div class="selected-item-content">
                <div class="color-preview" style="background-color: {hex};"></div>
                <div class="selected-item-title">{name}</div>
                <div class="selected-item-price">{price} ج.م</div>
                <button type="button" class="btn btn-sm btn-outline-secondary"
                    onclick="editPrice('{id}', 'color', '{name}', {price})">
                    <i class="fas fa-edit"></i>
                </button>
                <input type="hidden" name="colors[]" value="{id}">
                <input type="hidden" name="color_prices[{id}]" value="{price}">
            </div>
        </div>
    </template>



    <template id="materialFieldTemplate">
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-4 mb-2">
                            <select class="form-select material-select" name="materials[{index}][material_id]" required>
                                <option value="">اختر المادة</option>
                                @foreach ($materials as $material)
                                    <option value="{{ $material->id }}">{{ $material->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <input type="number" class="form-control" name="materials[{index}][quantity]"
                                placeholder="الكمية" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-2 mb-2">
                            <select class="form-select" name="materials[{index}][unit]" required>
                                <option value="piece">قطعة</option>
                                <option value="meter">متر</option>
                                <option value="kg">كجم</option>
                                <option value="liter">لتر</option>
                                <option value="gram">جرام</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <input type="number" class="form-control material-price-input"
                                name="materials[{index}][additional_price]" placeholder="السعر الإضافي" step="0.01"
                                min="0">
                        </div>
                        <div class="col-md-2 mb-2">
                            <button type="button" class="btn btn-outline-danger btn-sm"
                                onclick="removeMaterialField(this)">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <template id="printingMethodItemTemplate">
        <div class="selected-item" data-id="{id}">
            <div class="selected-item-remove" onclick="removeSelectedItem(this, 'printing_method')">×</div>
            <div class="selected-item-content">
                <div class="selected-item-icon">
                    <i class="fas fa-print"></i>
                </div>
                <div class="selected-item-title">{name}</div>
                <div class="selected-item-price">{price} ج.م</div>
                <button type="button" class="btn btn-sm btn-outline-secondary"
                    onclick="editPrice('{id}', 'printing_method', '{name}', {price})">
                    <i class="fas fa-edit"></i>
                </button>
                <input type="hidden" name="printing_methods[]" value="{id}">
                <input type="hidden" name="printing_method_prices[{id}]" value="{price}">
            </div>
        </div>
    </template>

    <template id="printLocationItemTemplate">
        <div class="selected-item" data-id="{id}">
            <div class="selected-item-remove" onclick="removeSelectedItem(this, 'print_location')">×</div>
            <div class="selected-item-content">
                <div class="selected-item-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div class="selected-item-title">{name}</div>
                <div class="selected-item-price">{price} ج.م</div>
                <button type="button" class="btn btn-sm btn-outline-secondary"
                    onclick="editPrice('{id}', 'print_location', '{name}', {price})">
                    <i class="fas fa-edit"></i>
                </button>
                <input type="hidden" name="print_locations[]" value="{id}">
                <input type="hidden" name="print_location_prices[{id}]" value="{price}">
            </div>
        </div>
    </template>

    <template id="offerItemTemplate">
        <div class="selected-item" data-id="{id}">
            <div class="selected-item-remove" onclick="removeSelectedItem(this, 'offer')">×</div>
            <div class="selected-item-content">
                <div class="selected-item-icon">
                    <i class="fas fa-tag"></i>
                </div>
                <div class="selected-item-title">{name}</div>
                <input type="hidden" name="offers[]" value="{id}">
            </div>
        </div>
    </template>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        // Global variables
        let selectedColors = new Map();
        let selectedPrintingMethods = new Map();
        let selectedPrintLocations = new Map();
        let selectedOffers = new Map();
        let materialCounter = 0;
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

            // Wizard Navigation
            $('.next-step').on('click', function() {
                const currentStep = $(this).closest('.step-card');
                const nextStepNum = $(this).data('next');
                const nextStep = $('.step-' + nextStepNum);

                if (!validateStep(currentStep)) {
                    return;
                }

                if (nextStepNum == 4) {
                    updateSummary();
                }

                currentStep.hide();
                nextStep.show();
                updateWizardSteps(nextStepNum);
            });

            $('.prev-step').on('click', function() {
                const currentStep = $(this).closest('.step-card');
                const prevStepNum = $(this).data('prev');
                const prevStep = $('.step-' + prevStepNum);

                currentStep.hide();
                prevStep.show();
                updateWizardSteps(prevStepNum);
            });

            // Discount toggle
            $('#has_discount').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#discountSection').slideDown();
                } else {
                    $('#discountSection').slideUp();
                }
            });

            // Initialize select2 change events
            $('#printingMethodsSelect').on('change', function() {
                updateSelectedPrintingMethods();
            });

            $('#printLocationsSelect').on('change', function() {
                updateSelectedPrintLocations();
            });

            $('#offersSelect').on('change', function() {
                updateSelectedOffers();
            });

            // Form submission
            $('#productForm').on('submit', function(e) {
                collectFormData();

                // Validate all steps
                for (let i = 1; i <= 4; i++) {
                    const step = $('.step-' + i);
                    if (step.is(':visible') && !validateStep(step)) {
                        e.preventDefault();
                        return;
                    }
                }

                // Show loading
                Swal.fire({
                    title: 'جاري حفظ المنتج...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            });
        });

        function validateStep(step) {
            let isValid = true;
            step.find('input[required], select[required], textarea[required]').each(function() {
                if (!$(this).val().trim()) {
                    $(this).addClass('is-invalid');
                    isValid = false;

                    if (!$(this).next('.invalid-feedback').length) {
                        $(this).after('<div class="invalid-feedback">هذا الحقل مطلوب</div>');
                    }
                } else {
                    $(this).removeClass('is-invalid');
                    $(this).next('.invalid-feedback').remove();
                }
            });

            if (!isValid) {
                Swal.fire({
                    icon: 'error',
                    title: 'بيانات ناقصة',
                    text: 'يرجى ملء جميع الحقول المطلوبة',
                    confirmButtonText: 'حسناً'
                });
            }

            return isValid;
        }

        function updateWizardSteps(activeStep) {
            $('.wizard-step').removeClass('active completed');

            for (let i = 1; i <= 4; i++) {
                const step = $('#step' + i);
                if (i < activeStep) {
                    step.addClass('completed');
                } else if (i == activeStep) {
                    step.addClass('active');
                }
            }
        }

        // Image Management
        function previewMainImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#mainImagePreviewImg').attr('src', e.target.result);
                    $('#mainImagePreview').show();
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function removeMainImage() {
            $('#image').val('');
            $('#mainImagePreview').hide();
        }

        function viewMainImage() {
            const src = $('#mainImagePreviewImg').attr('src');
            $('#viewedImage').attr('src', src);
            new bootstrap.Modal(document.getElementById('imageViewModal')).show();
        }

        function previewAdditionalImages(input) {
            const previewContainer = $('#additionalImagesPreview');
            previewContainer.empty();

            if (input.files) {
                Array.from(input.files).forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = `
                <div class="grid-item">
                    <img src="${e.target.result}" alt="صورة إضافية">
                    <div class="grid-item-actions">
                        <button type="button" class="btn btn-info btn-sm" onclick="viewImage('${e.target.result}')">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeAdditionalImage(this)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                `;
                        previewContainer.append(preview);
                    }
                    reader.readAsDataURL(file);
                });
            }
        }

        function removeAdditionalImage(button) {
            $(button).closest('.grid-item').remove();
        }

        function viewImage(src) {
            $('#viewedImage').attr('src', src);
            new bootstrap.Modal(document.getElementById('imageViewModal')).show();
        }

        // Color Selection with Price
        function toggleColorSelection(element) {
            const colorId = $(element).data('id');
            const colorName = $(element).data('name');
            const colorHex = $(element).data('hex');
            const currentPrice = $(element).data('price') || 0;

            if (selectedColors.has(colorId)) {
                // Remove color
                selectedColors.delete(colorId);
                $(element).removeClass('selected');
                $(`.selected-item[data-id="${colorId}"]`).remove();
            } else {
                // Ask for price
                Swal.fire({
                    title: 'إضافة سعر للون',
                    input: 'number',
                    inputLabel: `السعر الإضافي للون ${colorName}`,
                    inputPlaceholder: '0.00',
                    inputValue: currentPrice,
                    showCancelButton: true,
                    confirmButtonText: 'إضافة',
                    cancelButtonText: 'إلغاء',
                    inputValidator: (value) => {
                        if (value === '' || parseFloat(value) < 0) {
                            return 'يرجى إدخال سعر صحيح';
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const additionalPrice = parseFloat(result.value) || 0;

                        selectedColors.set(colorId, {
                            id: colorId,
                            name: colorName,
                            hex: colorHex,
                            price: additionalPrice
                        });

                        $(element).addClass('selected');
                        $(element).data('price', additionalPrice);

                        // Add to selected colors container
                        const template = document.getElementById('colorItemTemplate').innerHTML;
                        const html = template
                            .replace(/{id}/g, colorId)
                            .replace(/{name}/g, colorName)
                            .replace(/{hex}/g, colorHex)
                            .replace(/{price}/g, additionalPrice);

                        $('#selectedColorsContainer').append(html);
                    }
                });
            }
        }

        // Material Management
        function addMaterialField() {
            const template = document.getElementById('materialFieldTemplate').content.cloneNode(true);
            const newField = $(template);
            const index = materialCounter++;

            newField.html(newField.html().replace(/{index}/g, index));
            newField.find('.material-select').select2();

            $('#materialsContainer').append(newField);
        }

        function removeMaterialField(button) {
            $(button).closest('.col-md-12').remove();
        }

        // Update Selected Items
        function updateSelectedPrintingMethods() {
            const selected = $('#printingMethodsSelect').select2('data');
            const container = $('#selectedPrintingMethods');
            container.empty();
            selectedPrintingMethods.clear();

            selected.forEach(item => {
                const id = item.id;
                const name = $(item.element).data('name') || item.text.split(' - ')[0];
                const price = $(item.element).data('price') || 0;

                selectedPrintingMethods.set(id, {
                    id,
                    name,
                    price
                });

                const template = document.getElementById('printingMethodItemTemplate').innerHTML;
                const html = template
                    .replace(/{id}/g, id)
                    .replace(/{name}/g, name)
                    .replace(/{price}/g, price);

                container.append(html);
            });
        }

        function updateSelectedPrintLocations() {
            const selected = $('#printLocationsSelect').select2('data');
            const container = $('#selectedPrintLocations');
            container.empty();
            selectedPrintLocations.clear();

            selected.forEach(item => {
                const id = item.id;
                const name = $(item.element).data('name') || item.text.split(' - ')[0];
                const price = $(item.element).data('price') || 0;

                selectedPrintLocations.set(id, {
                    id,
                    name,
                    price
                });

                const template = document.getElementById('printLocationItemTemplate').innerHTML;
                const html = template
                    .replace(/{id}/g, id)
                    .replace(/{name}/g, name)
                    .replace(/{price}/g, price);

                container.append(html);
            });
        }

        function updateSelectedOffers() {
            const selected = $('#offersSelect').select2('data');
            const container = $('#selectedOffers');
            container.empty();
            selectedOffers.clear();

            selected.forEach(item => {
                const id = item.id;
                const name = $(item.element).data('name') || item.text;

                selectedOffers.set(id, {
                    id,
                    name
                });

                const template = document.getElementById('offerItemTemplate').innerHTML;
                const html = template
                    .replace(/{id}/g, id)
                    .replace(/{name}/g, name);

                container.append(html);
            });
        }

        // Text Ads Management
        let textAdCounter = {{ old('text_ads') ? count(old('text_ads')) : 0 }};

        function addTextAdField() {
            const container = $('#textAdsContainer');
            const index = textAdCounter++;

            const field = `
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-10 mb-2">
                            <textarea 
                                class="form-control" 
                                name="text_ads[${index}][name]" 
                                placeholder="أدخل النص الإعلاني هنا..." 
                                rows="3"
                                required></textarea>
                            <small class="text-muted">يمكن إضافة أكثر من نص إعلاني لتظهر مع المنتج</small>
                        </div>
                        <div class="col-md-2 mb-2 text-center">
                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeTextAdField(this)" title="حذف النص الإعلاني">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

            container.append(field);
        }

        function removeTextAdField(button) {
            $(button).closest('.col-md-12').remove();
            textAdCounter--;
        }
        // Remove Selected Item
        function removeSelectedItem(button, type) {
            const item = $(button).closest('.selected-item');
            const id = item.data('id');

            switch (type) {
                case 'color':
                    selectedColors.delete(id);
                    $(`.color-item[data-id="${id}"]`).removeClass('selected');
                    break;
                case 'printing_method':
                    selectedPrintingMethods.delete(parseInt(id));
                    $('#printingMethodsSelect').val($('#printingMethodsSelect').val().filter(v => v != id)).trigger(
                        'change');
                    break;
                case 'print_location':
                    selectedPrintLocations.delete(parseInt(id));
                    $('#printLocationsSelect').val($('#printLocationsSelect').val().filter(v => v != id)).trigger('change');
                    break;
                case 'offer':
                    selectedOffers.delete(parseInt(id));
                    $('#offersSelect').val($('#offersSelect').val().filter(v => v != id)).trigger('change');
                    break;
            }

            item.remove();
        }

        // Price Editor
        function editPrice(id, type, name, currentPrice) {
            $('#priceEditorId').val(id);
            $('#priceEditorType').val(type);
            $('#priceEditorName').val(name);
            $('#priceEditorPrice').val(currentPrice);

            const modal = new bootstrap.Modal(document.getElementById('priceEditorModal'));
            modal.show();
        }

        function savePriceEdit() {
            const id = $('#priceEditorId').val();
            const type = $('#priceEditorType').val();
            const newPrice = parseFloat($('#priceEditorPrice').val()) || 0;
            const name = $('#priceEditorName').val();

            switch (type) {
                case 'color':
                    if (selectedColors.has(id)) {
                        const color = selectedColors.get(id);
                        color.price = newPrice;
                        selectedColors.set(id, color);

                        // Update UI
                        $(`.color-item[data-id="${id}"]`).data('price', newPrice);
                        const item = $(`.selected-item[data-id="${id}"]`);
                        item.find('.selected-item-price').text(newPrice + ' ج.م');
                        item.find('input[name^="color_prices"]').val(newPrice);
                    }
                    break;
                case 'printing_method':
                    if (selectedPrintingMethods.has(parseInt(id))) {
                        const method = selectedPrintingMethods.get(parseInt(id));
                        method.price = newPrice;
                        selectedPrintingMethods.set(parseInt(id), method);

                        // Update UI
                        const item = $(`.selected-item[data-id="${id}"]`);
                        item.find('.selected-item-price').text(newPrice + ' ج.م');
                        item.find('input[name^="printing_method_prices"]').val(newPrice);
                    }
                    break;
                case 'print_location':
                    if (selectedPrintLocations.has(parseInt(id))) {
                        const location = selectedPrintLocations.get(parseInt(id));
                        location.price = newPrice;
                        selectedPrintLocations.set(parseInt(id), location);

                        // Update UI
                        const item = $(`.selected-item[data-id="${id}"]`);
                        item.find('.selected-item-price').text(newPrice + ' ج.م');
                        item.find('input[name^="print_location_prices"]').val(newPrice);
                    }
                    break;
            }

            $('#priceEditorModal').modal('hide');
        }

        // Quick Add Modal
        function openQuickAddModal(type) {
            currentQuickAddType = type;
            const modal = $('#quickAddModal');
            const formContent = $('#quickAddFormContent');
            formContent.empty();

            let title = '';
            let formHtml = '';

            switch (type) {
                case 'color':
                    title = 'إضافة لون جديد';
                    formHtml = `
                <div class="mb-3">
                    <label class="form-label">اسم اللون</label>
                    <input type="text" class="form-control" id="quick_color_name" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">الكود اللوني (Hex)</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="quick_color_hex" placeholder="#000000" required>
                        <input type="color" class="form-control form-control-color" id="quick_color_picker">
                    </div>
                </div>
            `;
                    break;

                case 'material':
                    title = 'إضافة مادة جديدة';
                    formHtml = `
                <div class="mb-3">
                    <label class="form-label">اسم المادة</label>
                    <input type="text" class="form-control" id="quick_material_name" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">الوصف</label>
                    <textarea class="form-control" id="quick_material_description" rows="3"></textarea>
                </div>
            `;
                    break;

                case 'printing_method':
                    title = 'إضافة طريقة طباعة جديدة';
                    formHtml = `
                <div class="mb-3">
                    <label class="form-label">اسم طريقة الطباعة</label>
                    <input type="text" class="form-control" id="quick_printing_name" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">الوصف</label>
                    <textarea class="form-control" id="quick_printing_description" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">السعر الأساسي</label>
                    <input type="number" class="form-control" id="quick_printing_price" step="0.01" min="0" required>
                </div>
            `;
                    break;

                case 'print_location':
                    title = 'إضافة مكان طباعة جديد';
                    formHtml = `
                <div class="mb-3">
                    <label class="form-label">اسم مكان الطباعة</label>
                    <input type="text" class="form-control" id="quick_location_name" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">النوع</label>
                    <select class="form-select" id="quick_location_type">
                        <option value="front">أمامي</option>
                        <option value="back">خلفي</option>
                        <option value="side">جانبي</option>
                        <option value="sleeve">كم</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">السعر الإضافي</label>
                    <input type="number" class="form-control" id="quick_location_price" step="0.01" min="0" required>
                </div>
            `;
                    break;

                case 'offer':
                    title = 'إضافة عرض جديد';
                    formHtml = `
                <div class="mb-3">
                    <label class="form-label">اسم العرض</label>
                    <input type="text" class="form-control" id="quick_offer_name" required>
                </div>
            `;
                    break;

                case 'category':
                    title = 'إضافة قسم جديد';
                    formHtml = `
                <div class="mb-3">
                    <label class="form-label">اسم القسم</label>
                    <input type="text" class="form-control" id="quick_category_name" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">القسم الرئيسي</label>
                    <select class="form-select" id="quick_category_parent">
                        <option value="">قسم رئيسي</option>
                        @foreach ($categories as $category)
                            @if ($category->isParent())
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            `;
                    break;
            }

            $('#quickAddModalTitle').text(title);
            formContent.html(formHtml);

            // Initialize color picker
            if (type === 'color') {
                $('#quick_color_picker').on('change', function() {
                    $('#quick_color_hex').val($(this).val());
                });
            }

            modal.modal('show');
        }

        function saveQuickAdd() {
            const modal = $('#quickAddModal');
            const type = currentQuickAddType;

            // Validate
            let isValid = true;
            modal.find('input[required], select[required]').each(function() {
                if (!$(this).val().trim()) {
                    $(this).addClass('is-invalid');
                    isValid = false;
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            if (!isValid) {
                Swal.fire({
                    icon: 'error',
                    title: 'بيانات ناقصة',
                    text: 'يرجى ملء جميع الحقول المطلوبة',
                    confirmButtonText: 'حسناً'
                });
                return;
            }

            // Prepare data
            const data = new FormData();
            data.append('_token', '{{ csrf_token() }}');
            data.append('type', type);

            switch (type) {
                case 'color':
                    data.append('name', $('#quick_color_name').val());
                    data.append('hex_code', $('#quick_color_hex').val());
                    break;

                case 'material':
                    data.append('name', $('#quick_material_name').val());
                    data.append('description', $('#quick_material_description').val());
                    break;

                case 'printing_method':
                    data.append('name', $('#quick_printing_name').val());
                    data.append('description', $('#quick_printing_description').val());
                    data.append('base_price', $('#quick_printing_price').val());
                    break;

                case 'print_location':
                    data.append('name', $('#quick_location_name').val());
                    data.append('type', $('#quick_location_type').val());
                    data.append('additional_price', $('#quick_location_price').val());
                    break;

                case 'offer':
                    data.append('name', $('#quick_offer_name').val());
                    break;

                case 'category':
                    data.append('name', $('#quick_category_name').val());
                    data.append('parent_id', $('#quick_category_parent').val());
                    break;
            }

            // Send AJAX request
            $.ajax({
                url: '{{ route('admin.products.quick-add') }}',
                type: 'POST',
                data: data,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    Swal.fire({
                        title: 'جاري الإضافة...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'تمت الإضافة!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });

                        // Update the appropriate select
                        updateSelectAfterQuickAdd(type, response.data);

                        modal.modal('hide');
                        modal.find('input, textarea, select').val('');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ!',
                            text: response.message
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ!',
                        text: xhr.responseJSON?.message || 'حدث خطأ أثناء الإضافة'
                    });
                }
            });
        }

        function updateSelectAfterQuickAdd(type, data) {
            switch (type) {
                case 'color':
                    // Add to colors grid
                    const colorItem = `
                <div class="color-item" 
                     style="background-color: ${data.hex_code};"
                     data-id="${data.id}"
                     data-name="${data.name}"
                     data-hex="${data.hex_code}"
                     data-price="0"
                     onclick="toggleColorSelection(this)">
                </div>
            `;
                    $('#colorsGrid').append(colorItem);
                    break;

                case 'material':
                    // Add to materials select
                    const materialOption = new Option(data.name, data.id, false, false);
                    $('#materialsSelect').append(materialOption).trigger('change');
                    break;

                case 'printing_method':
                    // Add to printing methods select
                    const printingOption = new Option(`${data.name} - ${data.base_price} ج.م`, data.id, false, false);
                    $(printingOption).data('name', data.name);
                    $(printingOption).data('price', data.base_price);
                    $('#printingMethodsSelect').append(printingOption).trigger('change');
                    break;

                case 'print_location':
                    // Add to print locations select
                    const locationOption = new Option(`${data.name} (${data.type}) - ${data.additional_price} ج.م`, data.id,
                        false, false);
                    $(locationOption).data('name', data.name);
                    $(locationOption).data('price', data.additional_price);
                    $('#printLocationsSelect').append(locationOption).trigger('change');
                    break;

                case 'offer':
                    // Add to offers select
                    const offerOption = new Option(data.name, data.id, false, false);
                    $(offerOption).data('name', data.name);
                    $('#offersSelect').append(offerOption).trigger('change');
                    break;

                case 'category':
                    // Add to category select
                    const categoryOption = new Option(data.name, data.id, false, false);
                    $('#category_id').append(categoryOption).trigger('change');
                    break;
            }
        }

        // Collect Form Data
        function collectFormData() {
            const container = $('#hiddenInputsContainer');
            container.empty();

            // Collect colors with prices
            selectedColors.forEach((color) => {
                container.append(`<input type="hidden" name="colors[]" value="${color.id}">`);
                container.append(`<input type="hidden" name="color_prices[${color.id}]" value="${color.price}">`);
            });

            // Collect printing methods with prices
            selectedPrintingMethods.forEach((method) => {
                container.append(`<input type="hidden" name="printing_methods[]" value="${method.id}">`);
                container.append(
                    `<input type="hidden" name="printing_method_prices[${method.id}]" value="${method.price}">`);
            });

            // Collect print locations with prices
            selectedPrintLocations.forEach((location) => {
                container.append(`<input type="hidden" name="print_locations[]" value="${location.id}">`);
                container.append(
                    `<input type="hidden" name="print_location_prices[${location.id}]" value="${location.price}">`
                );
            });

            // Collect offers
            selectedOffers.forEach((offer) => {
                container.append(`<input type="hidden" name="offers[]" value="${offer.id}">`);
            });
        }

        // Update Summary
        function updateSummary() {
            $('#summary_name').text($('#name').val());
            $('#summary_price').text($('#price').val());
            $('#summary_stock').text($('#stock').val());

            // Status
            const statusText = {
                '1': 'نشط',
                '2': 'غير نشط',
                '3': 'مسودة'
            } [$('#status_id').val()];
            $('#summary_status').text(statusText);

            // Colors
            const colors = Array.from(selectedColors.values()).map(c => c.name);
            $('#summary_colors').text(colors.join(', ') || 'لا يوجد');

            // Materials
            const materials = [];
            $('#materialsContainer .material-select').each(function() {
                const selectedOption = $(this).find('option:selected');
                if (selectedOption.val()) {
                    materials.push(selectedOption.text());
                }
            });
            $('#summary_materials').text(materials.join(', ') || 'لا يوجد');

            // Text Ads
            const textAds = [];
            $('#textAdsContainer textarea[name^="text_ads"], #textAdsContainer input[name^="text_ads"]').each(function() {
                if ($(this).val().trim()) {
                    textAds.push($(this).val());
                }
            });
            $('#summary_text_ads').text(textAds.length > 0 ? textAds.length + ' نص إعلاني' : 'لا يوجد');
        }

        // Save as Draft
        function saveAsDraft() {
            $('#status_id').val('3');
            collectFormData();
            $('#productForm').submit();
        }
    </script>
@endsection
