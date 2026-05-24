@extends('Admin.layout.master')

@section('title', 'إنشاء مقال جديد')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css">
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

        .article-card {
            background: var(--dark-card);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .article-header {
            background: var(--primary-gradient);
            color: white;
            padding: 25px 30px;
            border-radius: 15px 15px 0 0;
            margin: -30px -30px 0 -30px;
        }

        .form-section {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .section-title {
            color: #fff;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--primary-color);
        }

        .form-label {
            color: rgba(255, 255, 255, 0.9);
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            border-radius: 8px;
            padding: 10px 15px;
            transition: all 0.3s ease;
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

        .form-control.is-invalid {
            border-color: var(--danger-color);
            background-image: none;
        }

        .invalid-feedback {
            color: #ff6b6b;
            font-size: 13px;
            margin-top: 5px;
        }

        .input-group-text {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .image-preview {
            position: relative;
            width: 200px;
            height: 150px;
            border-radius: 10px;
            overflow: hidden;
            border: 2px dashed var(--primary-color);
            margin-bottom: 15px;
            background: rgba(255, 255, 255, 0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .image-preview .placeholder {
            text-align: center;
            color: rgba(255, 255, 255, 0.5);
        }

        .image-preview .placeholder i {
            font-size: 40px;
            margin-bottom: 10px;
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

        .tags-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
            min-height: 50px;
            padding: 10px;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 8px;
        }

        .tag-item {
            background: rgba(105, 108, 255, 0.2);
            border: 1px solid var(--primary-color);
            color: #fff;
            padding: 5px 15px;
            border-radius: 25px;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            animation: tagAppear 0.3s ease;
        }

        @keyframes tagAppear {
            from {
                transform: scale(0);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .tag-item i {
            font-size: 12px;
            cursor: pointer;
            opacity: 0.7;
            transition: opacity 0.3s;
        }

        .tag-item i:hover {
            opacity: 1;
            color: var(--danger-color);
        }

        .meta-help {
            color: rgba(255, 255, 255, 0.5);
            font-size: 12px;
            margin-top: 5px;
        }

        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            padding: 10px 25px;
            font-weight: 600;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4a9a 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(105, 108, 255, 0.4);
        }

        .btn-outline-secondary {
            border-color: rgba(255, 255, 255, 0.2);
            color: rgba(255, 255, 255, 0.8);
        }

        .btn-outline-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.3);
            color: #fff;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
            margin-left: 10px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(255, 255, 255, 0.2);
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background: var(--primary-gradient);
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }

        .toggle-wrapper {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .toggle-label {
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
        }

        .character-count {
            text-align: left;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.5);
            margin-top: 5px;
        }

        .character-count.warning {
            color: #ffc107;
        }

        .character-count.danger {
            color: #dc3545;
        }

        .suggested-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }

        .suggested-tag {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.8);
            padding: 3px 12px;
            border-radius: 15px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .suggested-tag:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: #fff;
        }

        .progress-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            position: relative;
        }

        .progress-steps::before {
            content: '';
            position: absolute;
            top: 15px;
            left: 0;
            right: 0;
            height: 2px;
            background: rgba(255, 255, 255, 0.1);
            z-index: 1;
        }

        .step {
            position: relative;
            z-index: 2;
            background: var(--dark-card);
            padding: 0 10px;
            text-align: center;
        }

        .step-number {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 5px;
            font-weight: 600;
        }

        .step.active .step-number {
            background: var(--primary-gradient);
            color: #fff;
        }

        .step-title {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.5);
        }

        .step.active .step-title {
            color: #fff;
        }

        @media (max-width: 768px) {
            .article-card {
                padding: 15px;
            }
            
            .article-header {
                padding: 15px 20px;
                margin: -15px -15px 0 -15px;
            }
            
            .form-section {
                padding: 15px;
            }
        }

        /* تخصيص Summernote */
        .note-editor.note-frame {
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            background: rgba(255, 255, 255, 0.05) !important;
        }

        .note-toolbar {
            background: rgba(255, 255, 255, 0.1) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
        }

        .note-btn {
            background: rgba(255, 255, 255, 0.05) !important;
            color: #fff !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
        }

        .note-btn:hover {
            background: rgba(255, 255, 255, 0.1) !important;
        }

        .note-editable {
            color: #fff !important;
            background: rgba(255, 255, 255, 0.02) !important;
        }

        .note-dropdown-menu {
            background: var(--dark-card) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
        }

        .note-dropdown-item {
            color: #fff !important;
        }

        .note-dropdown-item:hover {
            background: rgba(105, 108, 255, 0.2) !important;
        }

        .note-modal-content {
            background: var(--dark-card) !important;
            color: #fff !important;
        }

        .note-modal-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
        }

        .note-form-label {
            color: #fff !important;
        }

        .note-input {
            background: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: #fff !important;
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
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.articles.index') }}">المقالات</a>
                </li>
                <li class="breadcrumb-item active">إنشاء مقال جديد</li>
            </ol>
        </nav>

        <!-- مؤشر التقدم -->
        <div class="progress-steps mb-4">
            <div class="step active">
                <div class="step-number">1</div>
                <div class="step-title">المعلومات الأساسية</div>
            </div>
            <div class="step">
                <div class="step-number">2</div>
                <div class="step-title">المحتوى</div>
            </div>
            <div class="step">
                <div class="step-number">3</div>
                <div class="step-title">الصور والوسائط</div>
            </div>
            <div class="step">
                <div class="step-number">4</div>
                <div class="step-title">الوسوم والإعدادات</div>
            </div>
            <div class="step">
                <div class="step-number">5</div>
                <div class="step-title">SEO</div>
            </div>
        </div>

        <form action="{{ route('admin.articles.store') }}" method="POST" enctype="multipart/form-data" id="articleForm">
            @csrf

            <div class="article-card">
                <div class="article-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">
                                <i class="fas fa-plus-circle me-2"></i>
                                إنشاء مقال جديد
                            </h5>
                            <small class="opacity-75">أضف مقالاً جديداً إلى الموقع</small>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-light" id="previewBtn">
                                <i class="fas fa-eye me-2"></i>معاينة
                            </button>
                            <button type="button" class="btn btn-light" id="clearForm">
                                <i class="fas fa-undo me-2"></i>تفريغ
                            </button>
                        </div>
                    </div>
                </div>

                <div class="p-4">
                    <!-- القسم 1: المعلومات الأساسية -->
                    <div class="form-section" id="basic-info-section">
                        <h6 class="section-title">
                            <i class="fas fa-info-circle me-2"></i>
                            المعلومات الأساسية
                        </h6>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">
                                    عنوان المقال <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       name="title" 
                                       id="title" 
                                       class="form-control @error('title') is-invalid @enderror" 
                                       value="{{ old('title') }}" 
                                       placeholder="أدخل عنوان المقال"
                                       required
                                       autofocus>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="character-count">
                                    <span id="titleCount">0</span>/255 حرف
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">تاريخ النشر</label>
                                <input type="datetime-local" 
                                       name="published_at" 
                                       class="form-control" 
                                       value="{{ old('published_at', now()->format('Y-m-d\TH:i')) }}">
                                <small class="meta-help">اتركه فارغاً لحفظ كمسودة</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    التصنيف الرئيسي <span class="text-danger">*</span>
                                </label>
                                <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                    <option value="">اختر التصنيف</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">التصنيف الفرعي (اختياري)</label>
                                <select name="subcategory_id" id="subcategory_id" class="form-select">
                                    <option value="">اختر التصنيف الفرعي</option>
                                </select>
                                <small class="meta-help">سيتم تحديث القائمة بعد اختيار التصنيف الرئيسي</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    الكاتب <span class="text-danger">*</span>
                                </label>
                                <select name="author_id" class="form-select @error('author_id') is-invalid @enderror" required>
                                    <option value="">اختر الكاتب</option>
                                    @foreach($authors as $author)
                                        <option value="{{ $author->id }}" {{ old('author_id') == $author->id ? 'selected' : '' }}>
                                            {{ $author->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('author_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">الملخص (Excerpt)</label>
                                <textarea name="excerpt" 
                                          id="excerpt" 
                                          class="form-control" 
                                          rows="3" 
                                          maxlength="500"
                                          placeholder="ملخص قصير للمقال">{{ old('excerpt') }}</textarea>
                                <div class="character-count">
                                    <span id="excerptCount">0</span>/500 حرف
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- القسم 2: محتوى المقال -->
                    <div class="form-section" id="content-section">
                        <h6 class="section-title">
                            <i class="fas fa-file-alt me-2"></i>
                            محتوى المقال
                        </h6>

                        <div class="mb-3">
                            <label class="form-label">
                                المحتوى <span class="text-danger">*</span>
                            </label>
                            <textarea name="content" 
                                      id="summernote" 
                                      class="form-control @error('content') is-invalid @enderror" 
                                      rows="15">{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="d-flex justify-content-between mt-2">
                                <small class="meta-help">يمكنك استخدام الأدوات أعلاه لتنسيق النص</small>
                                <small class="meta-help" id="readingTimeEstimate">وقت القراءة المقدر: -- دقائق</small>
                            </div>
                        </div>
                    </div>

                    <!-- القسم 3: الصور والوسائط -->
                    <div class="form-section" id="media-section">
                        <h6 class="section-title">
                            <i class="fas fa-images me-2"></i>
                            الصور والوسائط
                        </h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">الصورة الرئيسية</label>
                                
                                <div class="image-preview" id="imagePreview" onclick="document.getElementById('featured_image').click()">
                                    <img src="" id="previewImg" style="display: none;">
                                    <div class="placeholder" id="placeholderText">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <p>اضغط لرفع صورة</p>
                                        <small>JPEG, PNG, JPG, GIF, WebP (max 2MB)</small>
                                    </div>
                                    <div class="image-overlay" id="imageOverlay" style="display: none;">
                                        <i class="fas fa-camera"></i>
                                    </div>
                                </div>

                                <input type="file" 
                                       name="featured_image" 
                                       id="featured_image" 
                                       class="d-none" 
                                       accept="image/*"
                                       onchange="previewImage(this)">
                                @error('featured_image')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">النص البديل للصورة (Alt Text)</label>
                                <input type="text" 
                                       name="image_alt" 
                                       class="form-control" 
                                       value="{{ old('image_alt') }}"
                                       placeholder="وصف الصورة لتحسين SEO">
                                <small class="meta-help">نص وصفي يظهر عند عدم تحميل الصورة</small>
                            </div>
                        </div>

                        <!-- صور إضافية -->
                        <div class="mt-4">
                            <label class="form-label">صور إضافية (اختياري)</label>
                            <div id="additionalImages">
                                <!-- سيتم إضافة الصور هنا عبر JavaScript -->
                            </div>
                            <button type="button" class="btn btn-outline-secondary mt-2" onclick="addImageField()">
                                <i class="fas fa-plus me-2"></i>إضافة صورة
                            </button>
                        </div>
                    </div>

                    <!-- القسم 4: الوسوم والإعدادات -->
                    <div class="form-section" id="tags-section">
                        <h6 class="section-title">
                            <i class="fas fa-tags me-2"></i>
                            الوسوم والإعدادات
                        </h6>

                        <!-- الوسوم -->
                        <div class="mb-4">
                            <label class="form-label">الوسوم (Tags)</label>
                            <p class="text-muted small">أضف وسماً واضغط Enter أو زر الإضافة</p>
                            
                            <!-- وسوم مقترحة -->
                            <div class="suggested-tags mb-3">
                                <span class="suggested-tag" onclick="addSuggestedTag('laravel')">#laravel</span>
                                <span class="suggested-tag" onclick="addSuggestedTag('php')">#php</span>
                                <span class="suggested-tag" onclick="addSuggestedTag('javascript')">#javascript</span>
                                <span class="suggested-tag" onclick="addSuggestedTag('html')">#html</span>
                                <span class="suggested-tag" onclick="addSuggestedTag('css')">#css</span>
                                <span class="suggested-tag" onclick="addSuggestedTag('vuejs')">#vuejs</span>
                                <span class="suggested-tag" onclick="addSuggestedTag('react')">#react</span>
                            </div>
                            
                            <div class="tags-container" id="tagsContainer"></div>
                            
                            <input type="hidden" name="tags" id="tagsInput" value="{{ old('tags') }}">
                            
                            <div class="input-group mt-2">
                                <input type="text" 
                                       id="tagInput" 
                                       class="form-control" 
                                       placeholder="اكتب الوسم ثم اضغط Enter أو زر الإضافة"
                                       onkeypress="handleTagKeyPress(event)">
                                <button class="btn btn-primary" type="button" onclick="addTag()">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        <!-- الإعدادات -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="toggle-wrapper">
                                    <label class="switch">
                                        <input type="checkbox" name="is_active" value="1" checked>
                                        <span class="slider"></span>
                                    </label>
                                    <span class="toggle-label">مقال نشط</span>
                                </div>

                                <div class="toggle-wrapper">
                                    <label class="switch">
                                        <input type="checkbox" name="is_featured" value="1">
                                        <span class="slider"></span>
                                    </label>
                                    <span class="toggle-label">مقال مميز</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="toggle-wrapper">
                                    <label class="switch">
                                        <input type="checkbox" name="is_sponsored" value="1">
                                        <span class="slider"></span>
                                    </label>
                                    <span class="toggle-label">مقال برعاية</span>
                                </div>

                                <div class="toggle-wrapper">
                                    <label class="switch">
                                        <input type="checkbox" name="allow_comments" value="1" checked>
                                        <span class="slider"></span>
                                    </label>
                                    <span class="toggle-label">السماح بالتعليقات</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="form-label">وقت القراءة (بالدقائق)</label>
                            <input type="number" 
                                   name="reading_time" 
                                   id="readingTime" 
                                   class="form-control" 
                                   min="1"
                                   placeholder="سيتم حسابه تلقائياً">
                            <small class="meta-help">اتركه فارغاً للحساب التلقائي من المحتوى</small>
                        </div>
                    </div>

                    <!-- القسم 5: إعدادات SEO -->
                    <div class="form-section" id="seo-section">
                        <h6 class="section-title">
                            <i class="fas fa-chart-line me-2"></i>
                            إعدادات SEO
                        </h6>

                        <div class="mb-3">
                            <label class="form-label">Meta Title</label>
                            <input type="text" 
                                   name="meta_title" 
                                   id="metaTitle" 
                                   class="form-control" 
                                   value="{{ old('meta_title') }}"
                                   maxlength="70"
                                   placeholder="عنوان SEO للمقال">
                            <div class="character-count">
                                <span id="metaTitleCount">0</span>/70 حرف
                            </div>
                            <small class="meta-help">العنوان الذي يظهر في محركات البحث</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Meta Description</label>
                            <textarea name="meta_description" 
                                      id="metaDescription" 
                                      class="form-control" 
                                      rows="3" 
                                      maxlength="160"
                                      placeholder="وصف المقال لمحركات البحث">{{ old('meta_description') }}</textarea>
                            <div class="character-count">
                                <span id="metaDescCount">0</span>/160 حرف
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Meta Keywords</label>
                            <input type="text" 
                                   name="meta_keywords" 
                                   class="form-control" 
                                   value="{{ old('meta_keywords') }}"
                                   placeholder="كلمات مفتاحية مفصولة بفواصل">
                            <small class="meta-help">مثال: laravel, php, برمجة, تطوير ويب</small>
                        </div>
                    </div>

                    <!-- أزرار الإجراءات -->
                    <div class="d-flex gap-3 justify-content-end mt-4">
                        <button type="button" class="btn btn-outline-secondary" onclick="window.history.back()">
                            <i class="fas fa-arrow-right me-2"></i>
                            إلغاء
                        </button>
                        
                        <button type="button" class="btn btn-info" onclick="saveAsDraft()">
                            <i class="fas fa-save me-2"></i>
                            حفظ كمسودة
                        </button>
                        
                        <button type="submit" name="action" value="save" class="btn btn-primary">
                            <i class="fas fa-check-circle me-2"></i>
                            نشر المقال
                        </button>
                        
                        <button type="submit" name="action" value="save_and_new" class="btn btn-success">
                            <i class="fas fa-plus-circle me-2"></i>
                            نشر وإنشاء آخر
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- مودال المعاينة -->
    <div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="background: var(--dark-card);">
                <div class="modal-header">
                    <h5 class="modal-title text-white">معاينة المقال</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="previewContent"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/lang/summernote-ar-AR.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            // تهيئة Summernote
            $('#summernote').summernote({
                height: 400,
                lang: 'ar-AR',
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'italic', 'clear']],
                    ['fontname', ['fontname']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video', 'hr']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ],
                callbacks: {
                    onChange: function(contents) {
                        updateReadingTime(contents);
                    }
                }
            });

            // تحميل التصنيفات الفرعية عند تغيير التصنيف الرئيسي
            $('#category_id').on('change', function() {
                const categoryId = $(this).val();
                const subcategorySelect = $('#subcategory_id');

                subcategorySelect.html('<option value="">جاري التحميل...</option>');

                if (categoryId) {
                    $.ajax({
                        url: `/admin/categories/${categoryId}/subcategories`,
                        type: 'GET',
                        success: function(data) {
                            subcategorySelect.html('<option value="">لا يوجد تصنيف فرعي</option>');
                            data.forEach(function(sub) {
                                subcategorySelect.append(`<option value="${sub.id}">${sub.name}</option>`);
                            });
                        },
                        error: function() {
                            subcategorySelect.html('<option value="">لا توجد تصنيفات فرعية</option>');
                        }
                    });
                } else {
                    subcategorySelect.html('<option value="">اختر التصنيف الرئيسي أولاً</option>');
                }
            });

            // حساب عدد الأحرف في العنوان
            $('#title').on('input', function() {
                const count = $(this).val().length;
                $('#titleCount').text(count);
                if (count > 255) {
                    $('#titleCount').parent().addClass('danger');
                } else {
                    $('#titleCount').parent().removeClass('danger');
                }
            });

            // حساب عدد الأحرف في الملخص
            $('#excerpt').on('input', function() {
                const count = $(this).val().length;
                $('#excerptCount').text(count);
                if (count > 500) {
                    $('#excerptCount').parent().addClass('danger');
                } else if (count > 450) {
                    $('#excerptCount').parent().addClass('warning');
                } else {
                    $('#excerptCount').parent().removeClass('warning danger');
                }
            });

            // حساب عدد الأحرف في Meta Title
            $('#metaTitle').on('input', function() {
                const count = $(this).val().length;
                $('#metaTitleCount').text(count);
                if (count > 70) {
                    $('#metaTitleCount').parent().addClass('danger');
                } else if (count > 60) {
                    $('#metaTitleCount').parent().addClass('warning');
                } else {
                    $('#metaTitleCount').parent().removeClass('warning danger');
                }
            });

            // حساب عدد الأحرف في Meta Description
            $('#metaDescription').on('input', function() {
                const count = $(this).val().length;
                $('#metaDescCount').text(count);
                if (count > 160) {
                    $('#metaDescCount').parent().addClass('danger');
                } else if (count > 150) {
                    $('#metaDescCount').parent().addClass('warning');
                } else {
                    $('#metaDescCount').parent().removeClass('warning danger');
                }
            });

            // تحميل الوسوم المحفوظة من old input
            const savedTags = $('#tagsInput').val();
            if (savedTags) {
                savedTags.split(',').forEach(tag => {
                    if (tag.trim()) {
                        addTagToDisplay(tag.trim());
                    }
                });
            }

            // معاينة المقال
            $('#previewBtn').on('click', function() {
                const title = $('#title').val() || 'عنوان المقال';
                const content = $('#summernote').summernote('code') || '<p>محتوى المقال</p>';
                
                $('#previewContent').html(`
                    <article class="article-preview">
                        <h1 class="text-white mb-4">${title}</h1>
                        <div class="article-content text-white">${content}</div>
                    </article>
                `);
                
                $('#previewModal').modal('show');
            });

            // تفريغ النموذج
            $('#clearForm').on('click', function() {
                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: "سيتم مسح جميع البيانات المدخلة",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'نعم، تفريغ',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#articleForm')[0].reset();
                        $('#summernote').summernote('code', '');
                        $('#tagsContainer').empty();
                        $('#tagsInput').val('');
                        $('#imagePreview img').hide();
                        $('#placeholderText').show();
                        $('#imageOverlay').hide();
                        $('#additionalImages').empty();
                        updateReadingTime('');
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'تم',
                            text: 'تم تفريغ النموذج بنجاح',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                });
            });

            // التمرير للأقسام عند النقر على خطوات التقدم
            $('.step').on('click', function() {
                const index = $(this).index();
                const sections = ['basic-info-section', 'content-section', 'media-section', 'tags-section', 'seo-section'];
                
                $('html, body').animate({
                    scrollTop: $(`#${sections[index]}`).offset().top - 100
                }, 500);
            });

            // تحديث خطوات التقدم عند التمرير
            $(window).on('scroll', function() {
                const scrollPosition = $(window).scrollTop();
                
                $('.form-section').each(function(index) {
                    const sectionTop = $(this).offset().top - 150;
                    
                    if (scrollPosition >= sectionTop) {
                        $('.step').removeClass('active');
                        $('.step').eq(index).addClass('active');
                    }
                });
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
        });

        // دوال معالجة الصور
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    $('#previewImg').attr('src', e.target.result).show();
                    $('#placeholderText').hide();
                    $('#imageOverlay').show();
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }

        function addImageField() {
            const container = $('#additionalImages');
            const index = container.children().length;
            
            const html = `
                <div class="row mb-2" id="image-row-${index}">
                    <div class="col-md-8">
                        <input type="file" 
                               name="additional_images[]" 
                               class="form-control" 
                               accept="image/*"
                               onchange="previewAdditionalImage(this, ${index})">
                    </div>
                    <div class="col-md-3">
                        <input type="text" 
                               name="image_captions[]" 
                               class="form-control" 
                               placeholder="تعليق على الصورة">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger" onclick="removeImageField(${index})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="row mb-3" id="preview-row-${index}">
                    <div class="col-12">
                        <div class="image-preview-small" id="preview-${index}" style="display: none;">
                            <img id="preview-img-${index}" style="max-width: 100px; max-height: 60px;">
                        </div>
                    </div>
                </div>
            `;
            
            container.append(html);
        }

        function previewAdditionalImage(input, index) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    $(`#preview-img-${index}`).attr('src', e.target.result);
                    $(`#preview-${index}`).show();
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }

        function removeImageField(index) {
            $(`#image-row-${index}`).remove();
            $(`#preview-row-${index}`).remove();
        }

        // دوال إدارة الوسوم
        function addTag() {
            const input = document.getElementById('tagInput');
            const tag = input.value.trim();
            
            if (tag) {
                // تنظيف الوسم من الرموز غير المرغوب فيها
                const cleanTag = tag.replace(/[,#]/g, '').trim();
                
                if (cleanTag) {
                    const currentTags = getCurrentTags();
                    
                    if (!currentTags.includes(cleanTag)) {
                        addTagToDisplay(cleanTag);
                        updateTagsInput();
                        input.value = '';
                        
                        // تأثير بسيط
                        Swal.fire({
                            icon: 'success',
                            title: 'تم',
                            text: 'تم إضافة الوسم',
                            timer: 800,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'تنبيه',
                            text: 'هذا الوسم موجود بالفعل',
                            timer: 1500,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                    }
                }
            }
        }

        function handleTagKeyPress(event) {
            if (event.key === 'Enter' || event.key === ',') {
                event.preventDefault();
                addTag();
            }
        }

        function addSuggestedTag(tag) {
            const currentTags = getCurrentTags();
            
            if (!currentTags.includes(tag)) {
                addTagToDisplay(tag);
                updateTagsInput();
            }
        }

        function addTagToDisplay(tag) {
            const container = document.getElementById('tagsContainer');
            container.innerHTML += `
                <span class="tag-item">
                    ${tag}
                    <i class="fas fa-times" onclick="removeTag(this)"></i>
                </span>
            `;
        }

        function removeTag(element) {
            const tagElement = $(element).parent();
            tagElement.remove();
            updateTagsInput();
        }

        function getCurrentTags() {
            const tags = [];
            document.querySelectorAll('#tagsContainer .tag-item').forEach(element => {
                tags.push(element.textContent.trim());
            });
            return tags;
        }

        function updateTagsInput() {
            const tags = getCurrentTags();
            document.getElementById('tagsInput').value = tags.join(',');
        }

        // حساب وقت القراءة
        function updateReadingTime(content) {
            const text = $(content).text();
            const wordCount = text.split(/\s+/).length;
            const readingTime = Math.ceil(wordCount / 200);
            
            $('#readingTime').val(readingTime);
            $('#readingTimeEstimate').text(`وقت القراءة المقدر: ${readingTime} دقائق`);
        }

        // حفظ كمسودة
        function saveAsDraft() {
            // إفراغ تاريخ النشر لحفظه كمسودة
            $('input[name="published_at"]').val('');
            $('#articleForm').submit();
        }
    </script>
@endsection