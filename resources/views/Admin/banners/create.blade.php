@extends('Admin.layout.master')

@section('title', 'إضافة بانر جديد')

@section('css')
    @parent
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">

    <style>
        /* أنماط CSS - مرتبة حسب المكونات */
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

        .form-header h4 {
            margin-bottom: 5px;
            color: white;
        }

        .form-section {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.05);
        }

        .section-title {
            color: var(--primary-color);
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* بطاقات الأنواع */
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

        /* خيارات الأقسام */
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

        /* لوحات الإعدادات */
        .settings-panel {
            background: rgba(255, 255, 255, 0.05);
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            border: 2px dashed rgba(255, 255, 255, 0.1);
            display: none;
        }

        .settings-panel.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        /* رفع الصور */
        .image-upload-box {
            border: 2px dashed rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.05);
            position: relative;
        }

        .image-upload-box:hover {
            border-color: var(--primary-color);
            background: rgba(105, 108, 255, 0.1);
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

        /* الباجات */
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

        /* مفتاح التبديل */
        .toggle-switch {
            position: relative;
            width: 50px;
            height: 26px;
            display: inline-block;
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
            background-color: #666;
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

        /* خطوات النموذج */
        .form-step {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .step-number {
            width: 40px;
            height: 40px;
            background: var(--primary-gradient);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 15px;
            flex-shrink: 0;
        }

        /* التنبيهات */
        .form-alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }

        .alert-info {
            background: rgba(12, 99, 228, 0.2);
            border: 1px solid rgba(12, 99, 228, 0.5);
            color: #0c63e4;
        }

        .alert-warning {
            background: rgba(133, 100, 4, 0.2);
            border: 1px solid rgba(133, 100, 4, 0.5);
            color: #856404;
        }

        /* المعاينة */
        .preview-container {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 20px;
            margin-top: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .preview-wrapper {
            position: relative;
            overflow: hidden;
            border-radius: 10px;
            margin-bottom: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .preview-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 10px;
            font-size: 12px;
            text-align: center;
        }

        /* النص المساعد */
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

        /* تحسينات للنموذج */
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

        .form-label {
            color: rgba(255, 255, 255, 0.9);
        }

        /* الرسوم المتحركة */
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

        /* التجاوب */
        @media (max-width: 768px) {
            .category-options {
                flex-direction: column;
            }

            .type-card {
                margin-bottom: 15px;
            }

            .form-step {
                flex-direction: column;
                text-align: center;
            }

            .step-number {
                margin-right: 0;
                margin-bottom: 10px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- مسار التنقل */-->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.index') }}">الرئيسية</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.banners.index') }}">البنرات</a>
                </li>
                <li class="breadcrumb-item active">إضافة بانر جديد</li>
            </ol>
        </nav>

        <!-- خطوات النموذج -->
        <div class="form-step">
            <div class="step-number">1</div>
            <div class="step-content">
                <h6>المعلومات الأساسية</h6>
                <p>أدخل عنوان البانر وحدد القسم المناسب</p>
            </div>
        </div>

        <div class="form-step">
            <div class="step-number">2</div>
            <div class="step-content">
                <h6>اختيار النوع</h6>
                <p>اختر نوع البانر المناسب (سلايدر، شبكة، ثابت، أقسام)</p>
            </div>
        </div>

        <div class="form-step">
            <div class="step-number">3</div>
            <div class="step-content">
                <h6>الإعدادات</h6>
                <p>اضبط الإعدادات وفقاً لنوع البانر المختار</p>
            </div>
        </div>

        <div class="form-step">
            <div class="step-number">4</div>
            <div class="step-content">
                <h6>العناصر</h6>
                <p>أضف الصور والعناصر المطلوبة للبانر</p>
            </div>
        </div>

        <!-- تنبيه المعلومات -->
        <div class="form-alert alert-info">
            <i class="fas fa-info-circle fa-2x"></i>
            <div>
                <h6 class="mb-1">معلومات مهمة</h6>
                <p class="mb-0">• تأكد من اختيار القسم المناسب للبانر (الرئيسية أو قسم معين)</p>
                <p class="mb-0">• اختر النوع المناسب بناءً على الغرض من البانر</p>
                <p class="mb-0">• أضف الصور بدقة مناسبة للعرض على جميع الشاشات</p>
            </div>
        </div>

        <!-- النموذج الرئيسي -->
        <div class="row">
            <div class="col-12">
                <div class="form-card">
                    <div class="form-header">
                        <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i>إضافة بانر جديد</h4>
                        <small class="opacity-75">املأ النموذج لإضافة بانر جديد إلى النظام</small>
                    </div>

                    <form action="{{ route('admin.banners.store') }}" method="POST" id="bannerForm">
                        @csrf

                        <!-- قسم الموقع -->
                        <div class="form-section">
                            <h5 class="section-title"><i class="fas fa-map-marker-alt"></i>القسم والموقع</h5>

                            <div class="category-options">
                                <div class="category-option active" data-category-type="main">
                                    <i class="fas fa-home"></i>
                                    <h6>الرئيسية</h6>
                                    <p class="text-muted small">عرض البانر في الصفحة الرئيسية</p>
                                </div>
                                <div class="category-option" data-category-type="specific">
                                    <i class="fas fa-tag"></i>
                                    <h6>قسم محدد</h6>
                                    <p class="text-muted small">عرض البانر في قسم معين</p>
                                </div>
                            </div>

                            <input type="hidden" name="category_type" id="category_type" value="main">

                            <div class="category-select mt-3" id="specificCategorySelect">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="category_id" class="form-label required-field">
                                            اختر القسم
                                        </label>
                                        <select class="form-control select2" id="category_id" name="category_id">
                                            <option value="">-- اختر قسم من القائمة --</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="help-text">سيتم عرض البانر في صفحة هذا القسم فقط</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- قسم المعلومات الأساسية -->
                        <div class="form-section">
                            <h5 class="section-title"><i class="fas fa-info-circle"></i>المعلومات الأساسية</h5>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="title" class="form-label required-field">
                                        عنوان البانر
                                    </label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                                        id="title" name="title" value="{{ old('title') }}" required
                                        placeholder="أدخل عنوان واضح للبانر">
                                    @error('title')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <span class="help-text">العنوان سيساعدك في التعرف على البانر لاحقاً</span>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="section_order" class="form-label required-field">
                                        ترتيب العرض
                                    </label>
                                    <input type="number" class="form-control @error('section_order') is-invalid @enderror"
                                        id="section_order" name="section_order" value="{{ old('section_order', 1) }}"
                                        required min="1">
                                    @error('section_order')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <span class="help-text">الأرقام الأقل تظهر أولاً</span>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label required-field">
                                        حالة البانر
                                    </label>
                                    <div class="d-flex align-items-center mt-2">
                                        <label class="toggle-switch me-3">
                                            <input type="checkbox" name="is_active" value="1" checked>
                                            <span class="toggle-slider"></span>
                                        </label>
                                        <span class="badge-custom badge-active">نشط</span>
                                    </div>
                                    <span class="help-text">يمكنك تعطيل البانر لاحقاً</span>
                                </div>
                            </div>
                        </div>

                        <!-- قسم نوع البانر -->
                        <div class="form-section">
                            <h5 class="section-title"><i class="fas fa-sliders-h"></i>اختيار نوع البانر</h5>

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
                                        <div class="type-card" data-type-id="{{ $type->id }}"
                                            data-type-name="{{ $type->name }}">
                                            <div class="type-icon">
                                                <i class="fas {{ $typeIcons[$type->name] ?? 'fa-image' }}"></i>
                                            </div>
                                            <h6 class="mb-2">{{ $typeNames[$type->name] ?? $type->name }}</h6>
                                            <p class="text-muted small">{{ $type->description }}</p>
                                        </div>
                                        <input type="radio" name="banner_type_id" value="{{ $type->id }}"
                                            id="type_{{ $type->id }}"
                                            {{ old('banner_type_id') == $type->id ? 'checked' : '' }} class="d-none">
                                    </div>
                                @endforeach
                            </div>
                            <input type="hidden" id="selected_type" value="{{ old('banner_type_id', 1) }}">
                        </div>

                        <!-- لوحة إعدادات الشبكة -->
                        <div class="settings-panel" id="gridSettings">
                            <h6 class="section-title"><i class="fas fa-cog me-2"></i>إعدادات الشبكة</h6>

                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="grid_type" class="form-label">نوع الشبكة</label>
                                    <select class="form-control" id="grid_type" name="grid_type">
                                        <option value="responsive">متجاوب</option>
                                        <option value="fixed">ثابت</option>
                                    </select>
                                    <span class="help-text">المتجاوب يناسب جميع الشاشات</span>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="desktop_columns" class="form-label">الأعمدة (كمبيوتر)</label>
                                    <input type="number" class="form-control" id="desktop_columns"
                                        name="desktop_columns" value="3" min="1" max="6">
                                    <span class="help-text">عدد الأعمدة في شاشات الكمبيوتر</span>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="tablet_columns" class="form-label">الأعمدة (تابلت)</label>
                                    <input type="number" class="form-control" id="tablet_columns" name="tablet_columns"
                                        value="2" min="1" max="4">
                                    <span class="help-text">عدد الأعمدة في شاشات التابلت</span>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="mobile_columns" class="form-label">الأعمدة (موبايل)</label>
                                    <input type="number" class="form-control" id="mobile_columns" name="mobile_columns"
                                        value="1" min="1" max="2">
                                    <span class="help-text">عدد الأعمدة في شاشات الموبايل</span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="row_gap" class="form-label">المسافة بين الصفوف</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="row_gap" name="row_gap"
                                            value="20" min="0" max="100">
                                        <span class="input-group-text">بكسل</span>
                                    </div>
                                    <span class="help-text">المسافة الرأسية بين العناصر</span>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="column_gap" class="form-label">المسافة بين الأعمدة</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="column_gap" name="column_gap"
                                            value="20" min="0" max="100">
                                        <span class="input-group-text">بكسل</span>
                                    </div>
                                    <span class="help-text">المسافة الأفقية بين العناصر</span>
                                </div>
                            </div>

                            <!-- معاينة الشبكة -->
                            <div class="preview-wrapper mt-4">
                                <h6 class="section-title"><i class="fas fa-eye me-2"></i>معاينة الشبكة</h6>
                                <div id="gridPreview" class="row g-3">
                                    <!-- سيتم إدراج المعاينة الديناميكية هنا -->
                                </div>
                            </div>
                        </div>

                        <!-- لوحة إعدادات السلايدر -->
                        <div class="settings-panel" id="sliderSettings">
                            <h6 class="section-title"><i class="fas fa-cog me-2"></i>إعدادات السلايدر</h6>

                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="autoplay" name="autoplay"
                                            checked>
                                        <label class="form-check-label" for="autoplay">التشغيل التلقائي</label>
                                    </div>
                                    <span class="help-text">التقدم التلقائي بين الشرائح</span>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="arrows" name="arrows"
                                            checked>
                                        <label class="form-check-label" for="arrows">أزرار التنقل</label>
                                    </div>
                                    <span class="help-text">عرض أزرار التالي والسابق</span>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="dots" name="dots"
                                            checked>
                                        <label class="form-check-label" for="dots">النقاط</label>
                                    </div>
                                    <span class="help-text">عرض نقاط التنقل أسفل السلايدر</span>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="infinite" name="infinite"
                                            checked>
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
                                            name="autoplay_speed" value="3000" min="1000" max="10000"
                                            step="500">
                                        <span class="input-group-text">ملي ثانية</span>
                                    </div>
                                    <span class="help-text">المدة بين الشرائح (3000 = 3 ثواني)</span>
                                </div>
                            </div>
                        </div>

                        <!-- قسم الفترة الزمنية -->
                        <div class="form-section">
                            <h5 class="section-title"><i class="fas fa-calendar-alt"></i>الفترة الزمنية</h5>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="start_date" class="form-label">تاريخ البدء</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                        <input type="datetime-local"
                                            class="form-control @error('start_date') is-invalid @enderror" id="start_date"
                                            name="start_date" value="{{ old('start_date') }}">
                                    </div>
                                    @error('start_date')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <span class="help-text">تاريخ بدء عرض البانر (اختياري)</span>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="end_date" class="form-label">تاريخ الانتهاء</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                        <input type="datetime-local"
                                            class="form-control @error('end_date') is-invalid @enderror" id="end_date"
                                            name="end_date" value="{{ old('end_date') }}">
                                    </div>
                                    @error('end_date')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <span class="help-text">تاريخ إيقاف عرض البانر (اختياري)</span>
                                </div>
                            </div>

                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" id="permanent" name="permanent" checked>
                                <label class="form-check-label" for="permanent">
                                    دائم (بدون فترة محددة)
                                </label>
                            </div>
                        </div>

                        <!-- قسم العناصر -->
                        <div class="form-section">
                            <h5 class="section-title"><i class="fas fa-layer-group"></i>عناصر البانر</h5>

                            <div class="form-alert alert-warning">
                                <i class="fas fa-exclamation-triangle fa-2x"></i>
                                <div>
                                    <h6 class="mb-1">ملاحظة مهمة</h6>
                                    <p class="mb-0">• يمكنك إضافة عناصر البانر (الصور والروابط) بعد إنشاء البانر مباشرة
                                    </p>
                                    <p class="mb-0">• ستظهر صفحة خاصة لإدارة العناصر مباشرة بعد الحفظ</p>
                                </div>
                            </div>

                            <div class="text-center py-4">
                                <i class="fas fa-plus-circle fa-3x text-muted mb-3"></i>
                                <p class="text-muted">سيتم إضافة العناصر بعد إنشاء البانر</p>
                                <small class="help-text">يمكنك إضافة صور متعددة، روابط، وأكواد خصم</small>
                            </div>
                        </div>

                        <!-- إجراءات النموذج -->
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <div>
                                <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>إلغاء
                                </a>
                            </div>

                            <div>
                                <button type="button" class="btn btn-outline-primary me-2" id="previewBtn">
                                    <i class="fas fa-eye me-2"></i>معاينة
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>حفظ وإنشاء البانر
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- نافذة معاينة -->
    <div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">معاينة البانر</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                </div>
                <div class="modal-body">
                    <div class="preview-container">
                        <div id="previewContent"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        إغلاق
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    @parent
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        class BannerFormHandler {
            constructor() {
                this.init();
            }

            init() {
                this.initSelect2();
                this.bindEvents();
                this.selectDefaultType();
                this.selectDefaultCategory();
                this.updateGridPreview();
            }

            initSelect2() {
                $('.select2').select2({
                    placeholder: 'اختر من القائمة',
                    allowClear: true,
                    language: {
                        noResults: function() {
                            return "لا توجد نتائج";
                        }
                    }
                });
            }

            bindEvents() {
                // اختيار القسم
                $('.category-option').on('click', (e) => this.handleCategorySelection(e));

                // اختيار نوع البانر
                $('.type-card').on('click', (e) => this.handleTypeSelection(e));

                // مربع اختيار الدائم
                $('#permanent').on('change', () => this.handlePermanentCheckbox());

                // تحديثات إعدادات الشبكة
                $('#desktop_columns, #tablet_columns, #mobile_columns, #row_gap, #column_gap')
                    .on('input', () => this.updateGridPreview());

                // إرسال النموذج
                $('#bannerForm').on('submit', (e) => this.handleFormSubmit(e));

                // زر المعاينة
                $('#previewBtn').on('click', () => this.showPreview());
            }

            handleCategorySelection(e) {
                const $option = $(e.currentTarget);
                const categoryType = $option.data('category-type');

                $('.category-option').removeClass('active');
                $option.addClass('active');

                $('#category_type').val(categoryType);

                if (categoryType === 'specific') {
                    $('#specificCategorySelect').addClass('show');
                    $('#category_id').prop('required', true);
                } else {
                    $('#specificCategorySelect').removeClass('show');
                    $('#category_id').prop('required', false).val('');
                }
            }

            handleTypeSelection(e) {
                const $card = $(e.currentTarget);
                const typeId = $card.data('type-id');
                const typeName = $card.data('type-name');

                $('.type-card').removeClass('active');
                $card.addClass('active');

                $(`#type_${typeId}`).prop('checked', true);
                $('#selected_type').val(typeId);

                this.toggleSettings(typeName);
            }

            toggleSettings(typeName) {
                $('.settings-panel').removeClass('active');

                if (typeName === 'grid') {
                    $('#gridSettings').addClass('active');
                } else if (typeName === 'slider') {
                    $('#sliderSettings').addClass('active');
                }
            }

            handlePermanentCheckbox() {
                const isPermanent = $('#permanent').is(':checked');
                $('#start_date, #end_date').prop('disabled', isPermanent);

                if (isPermanent) {
                    $('#start_date, #end_date').val('');
                }
            }

            updateGridPreview() {
                const desktopColumns = parseInt($('#desktop_columns').val()) || 3;
                const tabletColumns = parseInt($('#tablet_columns').val()) || 2;
                const mobileColumns = parseInt($('#mobile_columns').val()) || 1;
                const rowGap = parseInt($('#row_gap').val()) || 20;
                const colGap = parseInt($('#column_gap').val()) || 20;

                const previewHtml = this.generateGridPreview(desktopColumns, tabletColumns, mobileColumns, rowGap,
                    colGap);
                $('#gridPreview').html(previewHtml);
            }

            generateGridPreview(desktop, tablet, mobile, rowGap, colGap) {
                return `
                    <div class="row g-${colGap}">
                        <div class="col-12 mb-3">
                            <small class="text-muted">معاينة الكمبيوتر (${desktop} أعمدة)</small>
                            <div class="border rounded p-3">
                                <div class="row g-${colGap}">
                                    ${this.generatePreviewItems(desktop, rowGap, '#696cff')}
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <small class="text-muted">معاينة التابلت (${tablet} أعمدة)</small>
                            <div class="border rounded p-3">
                                <div class="row g-${colGap}">
                                    ${this.generatePreviewItems(tablet, rowGap, '#0c63e4')}
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <small class="text-muted">معاينة الموبايل (${mobile} أعمدة)</small>
                            <div class="border rounded p-3">
                                <div class="row g-${colGap}">
                                    ${this.generatePreviewItems(mobile, rowGap, '#28a745')}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            generatePreviewItems(count, rowGap, color) {
                let items = '';
                for (let i = 0; i < count; i++) {
                    const opacity = 0.7 - (i * 0.1);
                    items += `
                        <div class="col">
                            <div style="height: ${100 - rowGap}px; background: ${color}; opacity: ${opacity}; border-radius: 5px;"></div>
                        </div>
                    `;
                }
                return items;
            }

            validateForm() {
                const title = $('#title').val().trim();
                const categoryType = $('#category_type').val();
                const categoryId = $('#category_id').val();

                if (!title) {
                    this.showError('يرجى إدخال عنوان البانر');
                    $('#title').focus();
                    return false;
                }

                if (categoryType === 'specific' && !categoryId) {
                    this.showError('يرجى اختيار قسم للبانر');
                    $('#category_id').select2('open');
                    return false;
                }

                return true;
            }

            handleFormSubmit(e) {
                e.preventDefault();

                if (!this.validateForm()) {
                    return;
                }

                Swal.fire({
                    title: 'جاري الإنشاء...',
                    text: 'يرجى الانتظار',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                const formData = new FormData(e.target);

                $.ajax({
                    url: $(e.target).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: (response) => {
                        Swal.fire({
                            icon: 'success',
                            title: 'تم!',
                            text: 'تم إنشاء البانر بنجاح',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = "{{ route('admin.banners.index') }}";
                        });
                    },
                    error: (xhr) => {
                        let errorMessage = 'حدث خطأ أثناء الحفظ';

                        if (xhr.responseJSON?.errors) {
                            const errors = xhr.responseJSON.errors;
                            errorMessage = Object.values(errors).flat().join('<br>');
                        }

                        this.showError(errorMessage);
                    }
                });
            }

            showPreview() {
                const title = $('#title').val();
                const categoryType = $('#category_type').val();
                const categoryId = $('#category_id').val();
                const typeName = $('.type-card.active h6').text();

                let previewHtml = this.generatePreviewContent(title, categoryType, categoryId, typeName);
                $('#previewContent').html(previewHtml);

                new bootstrap.Modal(document.getElementById('previewModal')).show();
            }

            generatePreviewContent(title, categoryType, categoryId, typeName) {
                let settingsHtml = '';
                const selectedType = $('#selected_type').val();

                if (selectedType == 2) {
                    settingsHtml = `
                        <div class="info-item mb-3">
                            <strong>إعدادات الشبكة:</strong>
                            <div class="text-muted">
                                ${$('#desktop_columns').val()} أعمدة (كمبيوتر) | 
                                ${$('#tablet_columns').val()} أعمدة (تابلت) | 
                                ${$('#mobile_columns').val()} أعمدة (موبايل)
                            </div>
                        </div>
                    `;
                } else if (selectedType == 1) {
                    settingsHtml = `
                        <div class="info-item mb-3">
                            <strong>إعدادات السلايدر:</strong>
                            <div class="text-muted">
                                ${$('#autoplay').is(':checked') ? 'تشغيل تلقائي' : 'بدون تشغيل تلقائي'} | 
                                ${$('#autoplay_speed').val()} ملي ثانية
                            </div>
                        </div>
                    `;
                }

                return `
                    <div class="preview-wrapper">
                        <h6 class="mb-3">معاينة البانر</h6>
                        
                        <div class="info-item mb-3">
                            <strong>عنوان البانر:</strong>
                            <div>${title || 'بدون عنوان'}</div>
                        </div>
                        
                        <div class="info-item mb-3">
                            <strong>الموقع:</strong>
                            <div>
                                ${categoryType === 'main' 
                                    ? 'الصفحة الرئيسية' 
                                    : 'قسم: ' + $('#category_id option:selected').text()}
                            </div>
                        </div>
                        
                        <div class="info-item mb-3">
                            <strong>النوع:</strong>
                            <div>${typeName}</div>
                        </div>
                        
                        <div class="info-item mb-3">
                            <strong>الحالة:</strong>
                            <div>
                                <span class="badge-custom badge-active">نشط</span>
                            </div>
                        </div>
                        
                        ${settingsHtml}
                        
                        ${this.generateTimePeriodPreview()}
                        
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle me-2"></i>
                            هذه معاينة أساسية. سيمكنك إضافة العناصر والصور بعد إنشاء البانر.
                        </div>
                    </div>
                `;
            }

            generateTimePeriodPreview() {
                if (!$('#permanent').is(':checked')) {
                    return `
                        <div class="info-item mb-3">
                            <strong>الفترة الزمنية:</strong>
                            <div class="text-muted">
                                ${$('#start_date').val() 
                                    ? 'من ' + $('#start_date').val() 
                                    : 'بدون تاريخ بدء'}
                                ${$('#end_date').val() 
                                    ? 'إلى ' + $('#end_date').val() 
                                    : 'بدون تاريخ انتهاء'}
                            </div>
                        </div>
                    `;
                }

                return `
                    <div class="info-item mb-3">
                        <strong>الفترة الزمنية:</strong>
                        <div class="text-muted">دائم</div>
                    </div>
                `;
            }

            showError(message) {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    html: message,
                    confirmButtonText: 'حسناً'
                });
            }

            selectDefaultType() {
                const defaultTypeId = $('#selected_type').val() || 1;
                $(`.type-card[data-type-id="${defaultTypeId}"]`).click();
            }

            selectDefaultCategory() {
                $('.category-option[data-category-type="main"]').click();
            }
        }

        // تهيئة عند تحميل المستند
        $(document).ready(function() {
            new BannerFormHandler();
        });
    </script>
@endsection
