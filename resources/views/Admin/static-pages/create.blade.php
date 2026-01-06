@extends('Admin.layout.master')

@section('title', 'إنشاء صفحة ثابتة جديدة')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #696cff;
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --dark-bg: #1e1e2d;
            --dark-card: #2b3b4c;
        }

        body {
            font-family: "Cairo", sans-serif !important;
            background: var(--dark-bg);
            color: #fff;
        }

        .card {
            background: var(--dark-card);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .card-header {
            background: var(--primary-gradient);
            color: white;
            padding: 25px 30px;
            border-radius: 15px 15px 0 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .form-control,
        .form-select {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .form-control:focus,
        .form-select:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--primary-color);
            color: #fff;
            box-shadow: 0 0 0 0.25rem rgba(105, 108, 255, 0.25);
        }

        .form-label {
            color: rgba(255, 255, 255, 0.9);
            font-weight: 600;
            margin-bottom: 8px;
        }

        .slug-preview {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 10px 15px;
            margin-top: 5px;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.7);
        }

        .slug-preview code {
            color: #20c997;
            font-weight: 600;
        }

        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
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

        .note-editor {
            background: rgba(255, 255, 255, 0.05) !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
            border-radius: 8px !important;
        }

        .note-toolbar {
            background: rgba(255, 255, 255, 0.08) !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
            border-radius: 8px 8px 0 0 !important;
        }

        .note-editing-area {
            background: rgba(255, 255, 255, 0.05) !important;
        }

        .note-codable {
            background: rgba(255, 255, 255, 0.05) !important;
            color: #fff !important;
        }

        .note-btn {
            background: rgba(255, 255, 255, 0.1) !important;
            border-color: rgba(255, 255, 255, 0.2) !important;
            color: rgba(255, 255, 255, 0.9) !important;
        }

        .note-btn:hover {
            background: rgba(105, 108, 255, 0.2) !important;
            border-color: var(--primary-color) !important;
        }

        .note-dropdown-menu {
            background: var(--dark-card) !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
        }

        .note-dropdown-item {
            color: rgba(255, 255, 255, 0.9) !important;
        }

        .note-dropdown-item:hover {
            background: rgba(105, 108, 255, 0.1) !important;
            color: #fff !important;
        }

        .character-count {
            text-align: left;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.6);
            margin-top: 5px;
        }

        .character-count.limit-exceeded {
            color: #dc3545;
        }

        .tab-content {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 0 0 8px 8px;
            padding: 20px;
            margin-top: -1px;
        }

        .nav-tabs {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .nav-tabs .nav-link {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.7);
            border-radius: 8px 8px 0 0;
            margin-right: 5px;
            padding: 10px 20px;
        }

        .nav-tabs .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .nav-tabs .nav-link.active {
            background: var(--primary-gradient);
            color: white;
            border-color: var(--primary-color);
        }

        .preview-box {
            background: rgba(255, 255, 255, 0.05);
            border: 1px dashed rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            padding: 20px;
            min-height: 200px;
            max-height: 400px;
            overflow-y: auto;
        }

        .preview-box h1,
        .preview-box h2,
        .preview-box h3 {
            color: #fff;
        }

        .preview-box p {
            color: rgba(255, 255, 255, 0.8);
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
                    <a href="{{ route('admin.static-pages.index') }}">الصفحات الثابتة</a>
                </li>
                <li class="breadcrumb-item active">إنشاء صفحة جديدة</li>
            </ol>
        </nav>

        <div class="row" bis_skin_checked="1">
            <div class="col-12" bis_skin_checked="1">
                <div class="card" bis_skin_checked="1">
                    <div class="card-header" bis_skin_checked="1">
                        <div class="d-flex justify-content-between align-items-center" bis_skin_checked="1">
                            <div bis_skin_checked="1">
                                <h5 class="mb-0">إنشاء صفحة ثابتة جديدة</h5>
                                <small class="opacity-75">أضف صفحة جديدة لموقعك</small>
                            </div>
                            <div bis_skin_checked="1">
                                <a href="{{ route('admin.static-pages.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-right me-2"></i>رجوع للقائمة
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body" bis_skin_checked="1">
                        <form action="{{ route('admin.static-pages.store') }}" method="POST" id="createPageForm">
                            @csrf

                            <ul class="nav nav-tabs mb-4" id="pageTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="general-tab" data-bs-toggle="tab"
                                        data-bs-target="#general" type="button" role="tab">
                                        <i class="fas fa-info-circle me-2"></i>المعلومات الأساسية
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="content-tab" data-bs-toggle="tab" data-bs-target="#content"
                                        type="button" role="tab">
                                        <i class="fas fa-edit me-2"></i>المحتوى
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="seo-tab" data-bs-toggle="tab" data-bs-target="#seo"
                                        type="button" role="tab">
                                        <i class="fas fa-search me-2"></i>تحسين محركات البحث
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="preview-tab" data-bs-toggle="tab" data-bs-target="#preview"
                                        type="button" role="tab">
                                        <i class="fas fa-eye me-2"></i>معاينة
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content" id="pageTabsContent">
                                <!-- General Tab -->
                                <div class="tab-pane fade show active" id="general" role="tabpanel">
                                    <div class="row" bis_skin_checked="1">
                                        <div class="col-md-6 mb-3" bis_skin_checked="1">
                                            <label for="title" class="form-label">
                                                <i class="fas fa-heading me-2"></i>عنوان الصفحة
                                            </label>
                                            <input type="text" class="form-control @error('title') is-invalid @enderror"
                                                id="title" name="title" value="{{ old('title') }}"
                                                placeholder="أدخل عنوان الصفحة" required>
                                            @error('title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3" bis_skin_checked="1">
                                            <label for="slug" class="form-label">
                                                <i class="fas fa-link me-2"></i>الرابط (Slug)
                                            </label>
                                            <input type="text" class="form-control @error('slug') is-invalid @enderror"
                                                id="slug" name="slug" value="{{ old('slug') }}"
                                                placeholder="سيتم إنشاؤه تلقائياً">
                                            <div class="slug-preview" bis_skin_checked="1">
                                                الرابط الكامل: <code id="slugPreview">/page/</code>
                                            </div>
                                            @error('slug')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3" bis_skin_checked="1">
                                            <label for="status" class="form-label">
                                                <i class="fas fa-toggle-on me-2"></i>حالة الصفحة
                                            </label>
                                            <select class="form-select @error('status') is-invalid @enderror"
                                                id="status" name="status" required>
                                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>
                                                    <i class="fas fa-check-circle me-2"></i>نشط
                                                </option>
                                                <option value="inactive"
                                                    {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                                    <i class="fas fa-times-circle me-2"></i>غير نشط
                                                </option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3" bis_skin_checked="1">
                                            <label class="form-label">
                                                <i class="fas fa-tags me-2"></i>الصفحات الأساسية المشابهة
                                            </label>
                                            <div class="d-flex flex-wrap gap-2" bis_skin_checked="1">
                                                <button type="button" class="btn btn-outline-primary btn-sm template-btn"
                                                    data-title="سياسة الخصوصية">
                                                    سياسة الخصوصية
                                                </button>
                                                <button type="button" class="btn btn-outline-primary btn-sm template-btn"
                                                    data-title="الشروط والأحكام">
                                                    الشروط والأحكام
                                                </button>
                                                <button type="button" class="btn btn-outline-primary btn-sm template-btn"
                                                    data-title="من نحن">
                                                    من نحن
                                                </button>
                                                <button type="button" class="btn btn-outline-primary btn-sm template-btn"
                                                    data-title="الضمان">
                                                    الضمان
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Content Tab -->
                                <div class="tab-pane fade" id="content" role="tabpanel">
                                    <div class="mb-3" bis_skin_checked="1">
                                        <label for="content" class="form-label">
                                            <i class="fas fa-edit me-2"></i>محتوى الصفحة
                                        </label>
                                        <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="12"
                                            placeholder="أدخل محتوى الصفحة هنا..." required>{{ old('content') }}</textarea>
                                        @error('content')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="character-count" id="contentCount">
                                            عدد الأحرف: <span id="contentChars">0</span>
                                        </div>
                                    </div>

                                    <div class="row" bis_skin_checked="1">
                                        <div class="col-md-6" bis_skin_checked="1">
                                            <label class="form-label">
                                                <i class="fas fa-magic me-2"></i>محرر النصوص
                                            </label>
                                            <div class="d-flex flex-wrap gap-2 mb-3" bis_skin_checked="1">
                                                <button type="button" class="btn btn-outline-secondary btn-sm format-btn"
                                                    data-format="<h1>عنوان رئيسي</h1>">
                                                    H1
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary btn-sm format-btn"
                                                    data-format="<h2>عنوان فرعي</h2>">
                                                    H2
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary btn-sm format-btn"
                                                    data-format="<p>فقرة نصية</p>">
                                                    فقرة
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary btn-sm format-btn"
                                                    data-format="<ul><li>عنصر قائمة</li></ul>">
                                                    قائمة
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- SEO Tab -->
                                <div class="tab-pane fade" id="seo" role="tabpanel">
                                    <div class="mb-3" bis_skin_checked="1">
                                        <label for="meta_title" class="form-label">
                                            <i class="fas fa-search me-2"></i>Meta Title
                                        </label>
                                        <input type="text"
                                            class="form-control @error('meta_title') is-invalid @enderror" id="meta_title"
                                            name="meta_title" value="{{ old('meta_title') }}"
                                            placeholder="عنوان الصفحة لمحركات البحث">
                                        <div class="character-count" id="metaTitleCount">
                                            عدد الأحرف: <span id="metaTitleChars">0</span> (مثالي: 50-60 حرف)
                                        </div>
                                        @error('meta_title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3" bis_skin_checked="1">
                                        <label for="meta_description" class="form-label">
                                            <i class="fas fa-align-left me-2"></i>Meta Description
                                        </label>
                                        <textarea class="form-control @error('meta_description') is-invalid @enderror" id="meta_description"
                                            name="meta_description" rows="4" placeholder="وصف الصفحة لمحركات البحث">{{ old('meta_description') }}</textarea>
                                        <div class="character-count" id="metaDescCount">
                                            عدد الأحرف: <span id="metaDescChars">0</span> (مثالي: 150-160 حرف)
                                        </div>
                                        @error('meta_description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3" bis_skin_checked="1">
                                        <label for="meta_keywords" class="form-label">
                                            <i class="fas fa-tags me-2"></i>Meta Keywords
                                        </label>
                                        <input type="text"
                                            class="form-control @error('meta_keywords') is-invalid @enderror"
                                            id="meta_keywords" name="meta_keywords" value="{{ old('meta_keywords') }}"
                                            placeholder="كلمات مفتاحية مفصولة بفواصل">
                                        <small class="text-muted">مثال: خصوصية, بيانات, سياسة الخصوصية</small>
                                        @error('meta_keywords')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Preview Tab -->
                                <div class="tab-pane fade" id="preview" role="tabpanel">
                                    <div class="preview-box" id="pagePreview">
                                        <h4>معاينة الصفحة</h4>
                                        <p>سيظهر محتوى الصفحة هنا بعد ملء الحقول...</p>
                                    </div>

                                    <div class="mt-3" bis_skin_checked="1">
                                        <h6>معلومات إضافية:</h6>
                                        <div class="row" bis_skin_checked="1">
                                            <div class="col-md-6" bis_skin_checked="1">
                                                <div class="mb-2" bis_skin_checked="1">
                                                    <strong>الرابط:</strong>
                                                    <span id="previewSlug" class="text-muted">/page/</span>
                                                </div>
                                                <div class="mb-2" bis_skin_checked="1">
                                                    <strong>الحالة:</strong>
                                                    <span id="previewStatus" class="text-muted">غير محدد</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6" bis_skin_checked="1">
                                                <div class="mb-2" bis_skin_checked="1">
                                                    <strong>Meta Title:</strong>
                                                    <span id="previewMetaTitle" class="text-muted">غير محدد</span>
                                                </div>
                                                <div class="mb-2" bis_skin_checked="1">
                                                    <strong>Meta Description:</strong>
                                                    <span id="previewMetaDesc" class="text-muted">غير محدد</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 d-flex justify-content-between" bis_skin_checked="1">
                                <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                    <i class="fas fa-redo me-2"></i>إعادة تعيين
                                </button>
                                <div class="d-flex gap-3" bis_skin_checked="1">
                                    <a href="{{ route('admin.static-pages.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-2"></i>إلغاء
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>حفظ الصفحة
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script>
        $(document).ready(function() {
            // تهيئة Summernote
            $('#content').summernote({
                height: 300,
                lang: 'ar-AR',
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['fontname', ['fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ],
                placeholder: 'اكتب محتوى الصفحة هنا...',
                dialogsInBody: true,
                callbacks: {
                    onChange: function(contents) {
                        updatePreview();
                        updateCharCount('content', contents);
                    }
                }
            });

            // توليد slug من العنوان
            $('#title').on('keyup blur', function() {
                const title = $(this).val();
                if (title && !$('#slug').val()) {
                    const slug = generateSlug(title);
                    $('#slug').val(slug);
                    updateSlugPreview(slug);
                }
                updatePreview();
            });

            // تحديث preview عند تغيير slug
            $('#slug').on('keyup', function() {
                updateSlugPreview($(this).val());
                updatePreview();
            });

            // تحديث preview عند تغيير الحالة
            $('#status').on('change', function() {
                updatePreview();
            });

            // تحديث تعداد الأحرف
            $('#meta_title').on('keyup', function() {
                updateCharCount('meta_title', $(this).val());
                updatePreview();
            });

            $('#meta_description').on('keyup', function() {
                updateCharCount('meta_description', $(this).val());
                updatePreview();
            });

            // محتوى قالب
            $('.template-btn').on('click', function() {
                const title = $(this).data('title');
                $('#title').val(title);

                // توليد slug
                const slug = generateSlug(title);
                $('#slug').val(slug);
                updateSlugPreview(slug);

                // تحديث المحتوى حسب القالب
                updateContentByTemplate(title);

                updatePreview();

                Swal.fire({
                    icon: 'success',
                    title: 'تم التحميل',
                    text: `تم تحميل قالب ${title}`,
                    timer: 1500,
                    showConfirmButton: false
                });
            });

            // أزرار التنسيق
            $('.format-btn').on('click', function() {
                const format = $(this).data('format');
                $('#content').summernote('editor.saveRange');
                $('#content').summernote('editor.restoreRange');
                $('#content').summernote('editor.focus');
                $('#content').summernote('editor.pasteHTML', format);
            });

            // التحقق من النموذج
            $('#createPageForm').on('submit', function(e) {
                const title = $('#title').val();
                const content = $('#content').val();

                if (!title.trim()) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: 'الرجاء إدخال عنوان الصفحة',
                        confirmButtonText: 'حسناً'
                    });
                    return false;
                }

                if (!content.trim()) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: 'الرجاء إدخال محتوى الصفحة',
                        confirmButtonText: 'حسناً'
                    });
                    return false;
                }

                return true;
            });
        });

        function generateSlug(text) {
            return text
                .toLowerCase()
                .replace(/[^\u0600-\u06FF\w\s-]/g, '') // إزالة الرموز الغير مرغوب فيها
                .replace(/\s+/g, '-') // استبدال المسافات بشرطات
                .replace(/--+/g, '-') // إزالة الشرطات المزدوجة
                .trim();
        }

        function updateSlugPreview(slug) {
            const fullUrl = `/page/${slug || ''}`;
            $('#slugPreview').text(fullUrl);
            $('#previewSlug').text(fullUrl);
        }

        function updateCharCount(field, text) {
            const chars = text ? text.length : 0;
            let limit = 0;

            switch (field) {
                case 'meta_title':
                    limit = 60;
                    $('#metaTitleChars').text(chars);
                    if (chars > limit) {
                        $('#metaTitleCount').addClass('limit-exceeded');
                    } else {
                        $('#metaTitleCount').removeClass('limit-exceeded');
                    }
                    break;

                case 'meta_description':
                    limit = 160;
                    $('#metaDescChars').text(chars);
                    if (chars > limit) {
                        $('#metaDescCount').addClass('limit-exceeded');
                    } else {
                        $('#metaDescCount').removeClass('limit-exceeded');
                    }
                    break;

                case 'content':
                    $('#contentChars').text(chars);
                    break;
            }
        }

        function updatePreview() {
            const title = $('#title').val() || 'عنوان الصفحة';
            const content = $('#content').val() || 'محتوى الصفحة...';
            const slug = $('#slug').val() || 'slug';
            const status = $('#status').val();
            const metaTitle = $('#meta_title').val() || title;
            const metaDesc = $('#meta_description').val() || 'وصف الصفحة...';

            // تحديث معاينة المحتوى
            const previewHtml = `
                <h2>${title}</h2>
                <div>${content}</div>
            `;
            $('#pagePreview').html(previewHtml);

            // تحديث المعلومات الإضافية
            $('#previewSlug').text(`/page/${slug}`);
            $('#previewStatus').text(status === 'active' ? 'نشط' : 'غير نشط');
            $('#previewMetaTitle').text(metaTitle);
            $('#previewMetaDesc').text(metaDesc);
        }

        function updateContentByTemplate(title) {
            let content = '';

            switch (title) {
                case 'سياسة الخصوصية':
                    content = `
                        <h1>سياسة الخصوصية</h1>
                        <p>نحن نحترم خصوصيتك ونلتزم بحماية معلوماتك الشخصية...</p>
                        
                        <h2>جمع المعلومات</h2>
                        <p>نقوم بجمع المعلومات التالية:</p>
                        <ul>
                            <li>الاسم والبريد الإلكتروني</li>
                            <li>معلومات الاتصال</li>
                            <li>عنوان التسليم</li>
                        </ul>
                        
                        <h2>استخدام المعلومات</h2>
                        <p>نستخدم معلوماتك لتقديم وتحسين خدماتنا...</p>
                    `;
                    break;

                case 'الشروط والأحكام':
                    content = `
                        <h1>الشروط والأحكام</h1>
                        <p>باستخدامك لهذا الموقع أو خدماتنا، فإنك توافق على الالتزام بهذه الشروط والأحكام...</p>
                        
                        <h2>استخدام الموقع</h2>
                        <p>يجب أن يكون استخدامك للموقع قانونياً وأخلاقياً...</p>
                        
                        <h2>الطلبات والدفع</h2>
                        <p>جميع الأسعار معروضة بالعملة المحلية وتشمل الضريبة المضافة...</p>
                    `;
                    break;

                case 'من نحن':
                    content = `
                        <h1>من نحن</h1>
                        <p>نحن شركة/منصة متخصصة في تقديم حلول مبتكرة وعالية الجودة...</p>
                        
                        <h2>رسالتنا</h2>
                        <p>نسعى لتقديم أفضل الخدمات والمنتجات لعملائنا...</p>
                        
                        <h2>رؤيتنا</h2>
                        <p>أن نكون الخيار الأول في مجالنا...</p>
                    `;
                    break;

                case 'الضمان':
                    content = `
                        <h1>سياسة الضمان</h1>
                        <p>نقدّم ضمانًا على بعض المنتجات لضمان رضاك وسلامة منتجاتنا...</p>
                        
                        <h2>مدة الضمان</h2>
                        <p>تختلف مدة الضمان حسب المنتج وتتراوح بين 6 أشهر إلى سنتين...</p>
                        
                        <h2>شروط الضمان</h2>
                        <p>يشمل الضمان عيوب الصناعة ولا يشمل الأضرار الناتجة عن سوء الاستخدام...</p>
                    `;
                    break;
            }

            $('#content').summernote('code', content);
            updateCharCount('content', content);
        }

        function resetForm() {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: 'سيتم مسح جميع الحقول وإعادة تعيين النموذج',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'نعم، أعد التعيين',
                cancelButtonText: 'إلغاء',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#createPageForm')[0].reset();
                    $('#content').summernote('code', '');
                    updateSlugPreview('');
                    updatePreview();
                    updateCharCount('meta_title', '');
                    updateCharCount('meta_description', '');
                    updateCharCount('content', '');

                    Swal.fire({
                        icon: 'success',
                        title: 'تمت الإعادة',
                        text: 'تم إعادة تعيين النموذج بنجاح',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        }
    </script>
@endsection
