@extends('Admin.layout.master')

@section('title', 'تعديل: ' . $staticPage->title)

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

        .history-item {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            border-right: 3px solid var(--primary-color);
        }

        .history-date {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.6);
        }

        .history-action {
            font-weight: 600;
            color: #fff;
        }

        .history-user {
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
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.static-pages.show', $staticPage) }}">{{ $staticPage->title }}</a>
                </li>
                <li class="breadcrumb-item active">تعديل</li>
            </ol>
        </nav>

        <div class="row" bis_skin_checked="1">
            <div class="col-lg-8" bis_skin_checked="1">
                <div class="card" bis_skin_checked="1">
                    <div class="card-header" bis_skin_checked="1">
                        <div class="d-flex justify-content-between align-items-center" bis_skin_checked="1">
                            <div bis_skin_checked="1">
                                <h5 class="mb-0">تعديل الصفحة: {{ $staticPage->title }}</h5>
                                <small class="opacity-75">تعديل محتوى ومعلومات الصفحة</small>
                            </div>
                            <div bis_skin_checked="1">
                                <a href="{{ route('admin.static-pages.show', $staticPage) }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-right me-2"></i>عودة للعرض
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body" bis_skin_checked="1">
                        <form action="{{ route('admin.static-pages.update', $staticPage) }}" method="POST"
                            id="editPageForm">
                            @csrf
                            @method('PUT')

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
                                                id="title" name="title" value="{{ old('title', $staticPage->title) }}"
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
                                                id="slug" name="slug"
                                                value="{{ old('slug', $staticPage->slug) }}"
                                                placeholder="أدخل رابط الصفحة" required>
                                            <div class="slug-preview" bis_skin_checked="1">
                                                الرابط الكامل: <code id="slugPreview">/page/{{ $staticPage->slug }}</code>
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
                                                <option value="active"
                                                    {{ old('status', $staticPage->status) == 'active' ? 'selected' : '' }}>
                                                    <i class="fas fa-check-circle me-2"></i>نشط
                                                </option>
                                                <option value="inactive"
                                                    {{ old('status', $staticPage->status) == 'inactive' ? 'selected' : '' }}>
                                                    <i class="fas fa-times-circle me-2"></i>غير نشط
                                                </option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3" bis_skin_checked="1">
                                            <label class="form-label">
                                                <i class="fas fa-calendar me-2"></i>تاريخ الإنشاء
                                            </label>
                                            <div class="form-control" style="background: rgba(255,255,255,0.03)"
                                                bis_skin_checked="1">
                                                {{ $staticPage->created_at->translatedFormat('d M Y - h:i A') }}
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
                                        <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="12">{{ old('content', $staticPage->content) }}</textarea>
                                        @error('content')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="character-count" id="contentCount">
                                            عدد الأحرف: <span
                                                id="contentChars">{{ strlen(strip_tags($staticPage->content)) }}</span>
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
                                            name="meta_title" value="{{ old('meta_title', $staticPage->meta_title) }}"
                                            placeholder="عنوان الصفحة لمحركات البحث">
                                        <div class="character-count" id="metaTitleCount">
                                            عدد الأحرف: <span
                                                id="metaTitleChars">{{ strlen($staticPage->meta_title ?: $staticPage->title) }}</span>
                                            (مثالي: 50-60 حرف)
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
                                            name="meta_description" rows="4" placeholder="وصف الصفحة لمحركات البحث">{{ old('meta_description', $staticPage->meta_description) }}</textarea>
                                        <div class="character-count" id="metaDescCount">
                                            عدد الأحرف: <span
                                                id="metaDescChars">{{ strlen($staticPage->meta_description ?: '') }}</span>
                                            (مثالي: 150-160 حرف)
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
                                            id="meta_keywords" name="meta_keywords"
                                            value="{{ old('meta_keywords', $staticPage->meta_keywords) }}"
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
                                        {!! $staticPage->content !!}
                                    </div>

                                    <div class="mt-3" bis_skin_checked="1">
                                        <h6>معلومات إضافية:</h6>
                                        <div class="row" bis_skin_checked="1">
                                            <div class="col-md-6" bis_skin_checked="1">
                                                <div class="mb-2" bis_skin_checked="1">
                                                    <strong>الرابط:</strong>
                                                    <span id="previewSlug"
                                                        class="text-muted">/page/{{ $staticPage->slug }}</span>
                                                </div>
                                                <div class="mb-2" bis_skin_checked="1">
                                                    <strong>الحالة:</strong>
                                                    <span id="previewStatus" class="text-muted">
                                                        {{ $staticPage->status == 'active' ? 'نشط' : 'غير نشط' }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-6" bis_skin_checked="1">
                                                <div class="mb-2" bis_skin_checked="1">
                                                    <strong>Meta Title:</strong>
                                                    <span id="previewMetaTitle" class="text-muted">
                                                        {{ $staticPage->meta_title ?: $staticPage->title }}
                                                    </span>
                                                </div>
                                                <div class="mb-2" bis_skin_checked="1">
                                                    <strong>Meta Description:</strong>
                                                    <span id="previewMetaDesc" class="text-muted">
                                                        {{ $staticPage->meta_description ?: 'لا يوجد وصف' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 d-flex justify-content-between" bis_skin_checked="1">
                                <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                    <i class="fas fa-redo me-2"></i>إعادة تعيين التغييرات
                                </button>
                                <div class="d-flex gap-3" bis_skin_checked="1">
                                    <a href="{{ route('admin.static-pages.show', $staticPage) }}"
                                        class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-2"></i>إلغاء
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>حفظ التغييرات
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4" bis_skin_checked="1">
                <!-- معلومات الصفحة -->
                <div class="card mb-4" bis_skin_checked="1">
                    <div class="card-header" bis_skin_checked="1">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>معلومات الصفحة الحالية
                        </h5>
                    </div>
                    <div class="card-body" bis_skin_checked="1">
                        <div class="mb-3" bis_skin_checked="1">
                            <div class="info-label" bis_skin_checked="1">تاريخ الإنشاء:</div>
                            <div class="info-value" bis_skin_checked="1">
                                {{ $staticPage->created_at->translatedFormat('d M Y - h:i A') }}
                            </div>
                        </div>

                        <div class="mb-3" bis_skin_checked="1">
                            <div class="info-label" bis_skin_checked="1">آخر تحديث:</div>
                            <div class="info-value" bis_skin_checked="1">
                                {{ $staticPage->updated_at->translatedFormat('d M Y - h:i A') }}
                            </div>
                        </div>

                        <div class="mb-3" bis_skin_checked="1">
                            <div class="info-label" bis_skin_checked="1">عدد الأحرف:</div>
                            <div class="info-value" bis_skin_checked="1">
                                {{ strlen(strip_tags($staticPage->content)) }}
                            </div>
                        </div>

                        <div class="mb-3" bis_skin_checked="1">
                            <div class="info-label" bis_skin_checked="1">عدد الكلمات:</div>
                            <div class="info-value" bis_skin_checked="1">
                                {{ count(explode(' ', strip_tags($staticPage->content))) }}
                            </div>
                        </div>

                        <div class="mb-3" bis_skin_checked="1">
                            <div class="info-label" bis_skin_checked="1">الرابط العام:</div>
                            <div class="info-value" bis_skin_checked="1">
                                <a href="/page/{{ $staticPage->slug }}" target="_blank"
                                    class="btn btn-sm btn-outline-primary w-100">
                                    <i class="fas fa-external-link-alt me-2"></i>عرض في الموقع
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- الإجراءات السريعة -->
                <div class="card" bis_skin_checked="1">
                    <div class="card-header" bis_skin_checked="1">
                        <h5 class="mb-0">
                            <i class="fas fa-bolt me-2"></i>إجراءات سريعة
                        </h5>
                    </div>
                    <div class="card-body" bis_skin_checked="1">
                        <div class="d-grid gap-2" bis_skin_checked="1">
                            <button type="button" class="btn btn-success" onclick="copyContent()">
                                <i class="fas fa-copy me-2"></i>نسخ المحتوى
                            </button>

                            <button type="button" class="btn btn-info" onclick="previewChanges()">
                                <i class="fas fa-eye me-2"></i>معاينة التغييرات
                            </button>

                            <button type="button" class="btn btn-warning" onclick="restoreDefaults()">
                                <i class="fas fa-undo me-2"></i>استعادة النسخة السابقة
                            </button>

                            @if (!in_array($staticPage->slug, ['syas-alkhsosy', 'syas-alastrgaaa', 'aldman', 'mn-nhn', 'alshrot-oalahkam']))
                                <button type="button" class="btn btn-danger"
                                    onclick="deletePage({{ $staticPage->id }}, '{{ $staticPage->title }}')">
                                    <i class="fas fa-trash me-2"></i>حذف الصفحة
                                </button>
                            @endif
                        </div>
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

            // تحديث preview عند تغيير الحقول
            $('#title, #slug, #status, #meta_title, #meta_description').on('keyup change', function() {
                updatePreview();
                updateCharCount($(this).attr('id'), $(this).val());
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
            $('#editPageForm').on('submit', function(e) {
                const title = $('#title').val();
                const slug = $('#slug').val();

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

                if (!slug.trim()) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: 'الرجاء إدخال رابط الصفحة',
                        confirmButtonText: 'حسناً'
                    });
                    return false;
                }

                return true;
            });
        });

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
            const title = $('#title').val() || '{{ $staticPage->title }}';
            const content = $('#content').val() || '{{ addslashes($staticPage->content) }}';
            const slug = $('#slug').val() || '{{ $staticPage->slug }}';
            const status = $('#status').val();
            const metaTitle = $('#meta_title').val() || '{{ addslashes($staticPage->meta_title ?: $staticPage->title) }}';
            const metaDesc = $('#meta_description').val() || '{{ addslashes($staticPage->meta_description ?: '') }}';

            // تحديث معاينة المحتوى
            $('#pagePreview').html(content || 'محتوى الصفحة...');

            // تحديث المعلومات الإضافية
            $('#slugPreview').text(`/page/${slug}`);
            $('#previewSlug').text(`/page/${slug}`);
            $('#previewStatus').text(status === 'active' ? 'نشط' : 'غير نشط');
            $('#previewMetaTitle').text(metaTitle);
            $('#previewMetaDesc').text(metaDesc);
        }

        function resetForm() {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: 'سيتم إعادة تعيين جميع التغييرات التي لم تحفظ',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'نعم، أعد التعيين',
                cancelButtonText: 'إلغاء',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // إعادة تعيين النموذج إلى القيم الأصلية
                    $('#title').val('{{ addslashes($staticPage->title) }}');
                    $('#slug').val('{{ addslashes($staticPage->slug) }}');
                    $('#status').val('{{ $staticPage->status }}');
                    $('#meta_title').val('{{ addslashes($staticPage->meta_title) }}');
                    $('#meta_description').val('{{ addslashes($staticPage->meta_description) }}');
                    $('#meta_keywords').val('{{ addslashes($staticPage->meta_keywords) }}');
                    $('#content').summernote('code', `{!! addslashes($staticPage->content) !!}`);

                    updatePreview();
                    updateCharCount('meta_title', '{{ $staticPage->meta_title ?: $staticPage->title }}');
                    updateCharCount('meta_description', '{{ $staticPage->meta_description ?: '' }}');
                    updateCharCount('content', '{{ $staticPage->content }}');

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

        function copyContent() {
            const content = $('#content').val();
            navigator.clipboard.writeText(content).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'تم النسخ',
                    text: 'تم نسخ محتوى الصفحة إلى الحافظة',
                    timer: 1500,
                    showConfirmButton: false
                });
            });
        }

        function previewChanges() {
            const newTab = window.open('', '_blank');
            newTab.document.write(`
                <!DOCTYPE html>
                <html dir="rtl">
                <head>
                    <title>معاينة: ${$('#title').val()}</title>
                    <meta charset="UTF-8">
                    <style>
                        body {
                            font-family: "Cairo", sans-serif;
                            padding: 20px;
                            background: #f5f5f5;
                        }
                        .container {
                            max-width: 800px;
                            margin: 0 auto;
                            background: white;
                            padding: 30px;
                            border-radius: 10px;
                            box-shadow: 0 0 20px rgba(0,0,0,0.1);
                        }
                        h1 { color: #333; }
                        p { color: #666; line-height: 1.8; }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <h1>${$('#title').val()}</h1>
                        ${$('#content').val()}
                        <hr>
                        <p style="color: #999; font-size: 12px; text-align: center;">
                            هذه معاينة للتغييرات - الصفحة غير محفوظة بعد
                        </p>
                    </div>
                </body>
                </html>
            `);
            newTab.document.close();
        }

        function restoreDefaults() {
            Swal.fire({
                title: 'استعادة النسخة السابقة',
                text: 'سيتم استعادة النسخة المحفوظة الأخيرة من الصفحة',
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'نعم، استرجع',
                cancelButtonText: 'إلغاء',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // هنا يمكن إضافة استدعاء AJAX لاستعادة نسخة سابقة
                    Swal.fire({
                        icon: 'info',
                        title: 'قريباً',
                        text: 'ستتوفر هذه الميزة في الإصدارات القادمة',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        }

        function deletePage(pageId, pageName) {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: `سيتم حذف الصفحة "${pageName}" نهائياً`,
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
                        url: "{{ route('admin.static-pages.destroy', '') }}/" + pageId,
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            _method: 'DELETE'
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'تم الحذف',
                                text: response.success,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href =
                                    "{{ route('admin.static-pages.index') }}";
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ',
                                text: xhr.responseJSON?.error || 'حدث خطأ أثناء الحذف',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    });
                }
            });
        }

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
    </script>
@endsection
