@extends('Admin.layout.master')

@section('title', 'تعديل المقال')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
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

        .form-control,
        .form-select {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            border-radius: 8px;
            padding: 10px 15px;
        }

        .form-control:focus,
        .form-select:focus {
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
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .current-image {
            position: relative;
            width: 200px;
            height: 150px;
            border-radius: 10px;
            overflow: hidden;
            border: 2px solid var(--primary-color);
            margin-bottom: 15px;
        }

        .current-image img {
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

        .current-image:hover .image-overlay {
            opacity: 1;
        }

        .remove-image {
            color: #fff;
            font-size: 24px;
            cursor: pointer;
        }

        .tags-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }

        .tag-item {
            background: rgba(105, 108, 255, 0.2);
            border: 1px solid var(--primary-color);
            color: #fff;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .tag-item i {
            cursor: pointer;
            color: rgba(255, 255, 255, 0.7);
            transition: color 0.3s;
        }

        .tag-item i:hover {
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

        input:checked+.slider {
            background: var(--primary-gradient);
        }

        input:checked+.slider:before {
            transform: translateX(26px);
        }

        .toggle-label {
            margin-left: 10px;
            color: rgba(255, 255, 255, 0.9);
        }

        .select2-container--default .select2-selection--multiple {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background: var(--primary-gradient);
            border: none;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: white;
            margin-right: 5px;
        }

        .select2-dropdown {
            background: var(--dark-card);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .select2-results__option {
            color: rgba(255, 255, 255, 0.9);
        }

        .select2-results__option--highlighted {
            background: var(--primary-color) !important;
        }

        .info-box {
            background: rgba(105, 108, 255, 0.1);
            border: 1px solid var(--primary-color);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 10px;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 8px;
        }

        .info-item i {
            color: var(--primary-color);
            width: 20px;
        }

        .related-articles {
            max-height: 300px;
            overflow-y: auto;
            padding: 10px;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 8px;
        }

        .related-article-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            margin-bottom: 8px;
        }

        .related-article-item img {
            width: 50px;
            height: 40px;
            object-fit: cover;
            border-radius: 5px;
        }

        @media (max-width: 768px) {
            .article-card {
                padding: 15px;
            }

            .form-section {
                padding: 15px;
            }
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
                <li class="breadcrumb-item active">تعديل: {{ $article->title }}</li>
            </ol>
        </nav>

        <form action="{{ route('admin.articles.update', $article) }}" method="POST" enctype="multipart/form-data"
            id="articleForm">
            @csrf
            @method('PUT')

            <div class="article-card">
                <div class="article-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">تعديل المقال</h5>
                            <small class="opacity-75">قم بتحديث بيانات المقال</small>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.articles.show', $article) }}" class="btn btn-light" target="_blank">
                                <i class="fas fa-eye me-2"></i>معاينة
                            </a>
                            <a href="{{ route('admin.articles.create') }}" class="btn btn-light">
                                <i class="fas fa-plus me-2"></i>جديد
                            </a>
                        </div>
                    </div>
                </div>

                <div class="p-4">
                    <!-- معلومات سريعة -->
                    <div class="info-box">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="info-item">
                                    <i class="fas fa-calendar"></i>
                                    <span>تاريخ الإنشاء: {{ $article->created_at->format('d M Y') }}</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-item">
                                    <i class="fas fa-eye"></i>
                                    <span>المشاهدات: {{ number_format($article->views_count) }}</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-item">
                                    <i class="fas fa-clock"></i>
                                    <span>وقت القراءة: {{ $article->reading_time }} دقائق</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-item">
                                    <i class="fas fa-link"></i>
                                    <span>Slug: {{ $article->slug }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- المعلومات الأساسية -->
                    <div class="form-section">
                        <h6 class="section-title">
                            <i class="fas fa-info-circle me-2"></i>
                            المعلومات الأساسية
                        </h6>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">عنوان المقال</label>
                                <input type="text" name="title"
                                    class="form-control @error('title') is-invalid @enderror"
                                    value="{{ old('title', $article->title) }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="meta-help">عنوان واضح وجذاب للمقال</small>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Slug (رابط المقال)</label>
                                <input type="text" name="slug" class="form-control"
                                    value="{{ old('slug', $article->slug) }}" placeholder="سيتم إنشاؤه تلقائياً">
                                <small class="meta-help">اتركه فارغاً للإنشاء التلقائي</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">نبذة مختصرة (Excerpt)</label>
                            <textarea name="excerpt" class="form-control @error('excerpt') is-invalid @enderror" rows="3">{{ old('excerpt', $article->excerpt) }}</textarea>
                            @error('excerpt')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="meta-help">ملخص قصير للمقال (يظهر في بطاقات المقالات)</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">التصنيف الرئيسي</label>
                                <select name="category_id" class="form-select @error('category_id') is-invalid @enderror"
                                    required>
                                    <option value="">اختر التصنيف</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('category_id', $article->category_id) == $category->id ? 'selected' : '' }}>
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
                                <select name="subcategory_id" class="form-select" id="subcategorySelect">
                                    <option value="">لا يوجد تصنيف فرعي</option>
                                    @if ($article->category)
                                        @foreach ($article->category->children as $sub)
                                            <option value="{{ $sub->id }}"
                                                {{ old('subcategory_id', $article->subcategory_id) == $sub->id ? 'selected' : '' }}>
                                                {{ $sub->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">الكاتب</label>
                                <select name="author_id" class="form-select @error('author_id') is-invalid @enderror"
                                    required>
                                    <option value="">اختر الكاتب</option>
                                    @foreach ($authors as $author)
                                        <option value="{{ $author->id }}"
                                            {{ old('author_id', $article->author_id) == $author->id ? 'selected' : '' }}>
                                            {{ $author->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('author_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">تاريخ النشر</label>
                                <input type="datetime-local" name="published_at" class="form-control"
                                    value="{{ old('published_at', $article->published_at ? $article->published_at->format('Y-m-d\TH:i') : '') }}">
                                <small class="meta-help">اتركه فارغاً لحفظ كمسودة</small>
                            </div>
                        </div>
                    </div>

                    <!-- محتوى المقال -->
                    <div class="form-section">
                        <h6 class="section-title">
                            <i class="fas fa-file-alt me-2"></i>
                            محتوى المقال
                        </h6>

                        <div class="mb-3">
                            <label class="form-label">المحتوى</label>
                            <textarea name="content" id="summernote" class="form-control @error('content') is-invalid @enderror" rows="15"
                                required>{{ old('content', $article->content) }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- الصور والوسائط -->
                    <div class="form-section">
                        <h6 class="section-title">
                            <i class="fas fa-images me-2"></i>
                            الصور والوسائط
                        </h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">الصورة الرئيسية</label>

                                @if ($article->featured_image)
                                    <div class="current-image">
                                        <img src="{{ Storage::url($article->featured_image) }}"
                                            alt="{{ $article->title }}">
                                        <div class="image-overlay">
                                            <i class="fas fa-times-circle remove-image" onclick="removeImage()"></i>
                                        </div>
                                    </div>
                                    <input type="hidden" name="remove_image" id="removeImage" value="0">
                                @endif

                                <input type="file" name="featured_image"
                                    class="form-control @error('featured_image') is-invalid @enderror" accept="image/*">
                                @error('featured_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="meta-help">الصيغ المسموحة: jpeg, png, jpg, gif, webp | الحجم الأقصى: 2
                                    ميجابايت</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">النص البديل للصورة (Alt Text)</label>
                                <input type="text" name="image_alt" class="form-control"
                                    value="{{ old('image_alt', $article->featured_image_alt ?? '') }}">
                                <small class="meta-help">نص وصفي للصورة لتحسين SEO</small>
                            </div>
                        </div>

                        <!-- صور إضافية -->
                        <div class="mt-4">
                            <label class="form-label">صور إضافية</label>
                            <div id="additional-images">
                                @foreach ($article->images as $index => $image)
                                    <div class="row mb-2" id="image-row-{{ $index }}">
                                        <div class="col-md-8">
                                            <input type="file" name="additional_images[]" class="form-control"
                                                accept="image/*">
                                        </div>
                                        <div class="col-md-3">
                                            <input type="text" name="image_captions[]" class="form-control"
                                                placeholder="تعليق على الصورة" value="{{ $image->caption }}">
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-danger"
                                                onclick="removeImageRow({{ $index }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-outline-secondary mt-2" onclick="addImageRow()">
                                <i class="fas fa-plus me-2"></i>إضافة صورة
                            </button>
                        </div>
                    </div>

                    <!-- التصنيفات والوسوم -->
                    <div class="form-section">
                        <h6 class="section-title">
                            <i class="fas fa-tags me-2"></i>
                            التصنيفات والوسوم
                        </h6>

                        <!-- وسوم مخصصة (Custom Tags) - الحل المناسب لك -->
                        <div class="mt-3">
                            <label class="form-label">الوسوم (Tags)</label>
                            <p class="text-muted small">أضف وسماً واضغط Enter أو زر الإضافة</p>

                            <div class="tags-container" id="customTags">
                                @if (!empty($article->tags) && is_array($article->tags))
                                    @foreach ($article->tags as $tag)
                                        <span class="tag-item">
                                            {{ $tag }}
                                            <i class="fas fa-times" onclick="removeCustomTag(this)"></i>
                                        </span>
                                    @endforeach
                                @endif
                            </div>

                            <input type="hidden" name="tags" id="tagsInput"
                                value="{{ !empty($article->tags) && is_array($article->tags) ? implode(',', $article->tags) : '' }}">

                            <div class="input-group mt-2">
                                <input type="text" id="newTagInput" class="form-control"
                                    placeholder="اكتب الوسم ثم اضغط Enter أو زر الإضافة"
                                    onkeypress="handleTagKeyPress(event)">
                                <button class="btn btn-primary" type="button" onclick="addCustomTag()">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <small class="meta-help">أمثلة: laravel, php, برمجة, تصميم</small>
                        </div>
                    </div>
                    <!-- إعدادات SEO -->
                    <div class="form-section">
                        <h6 class="section-title">
                            <i class="fas fa-chart-line me-2"></i>
                            إعدادات SEO
                        </h6>

                        <div class="mb-3">
                            <label class="form-label">Meta Title</label>
                            <input type="text" name="meta_title" class="form-control"
                                value="{{ old('meta_title', $article->meta_title) }}" maxlength="70">
                            <small class="meta-help">العنوان لتحسين محركات البحث (70 حرف كحد أقصى)</small>
                            <div class="text-muted mt-1"><span id="metaTitleCount">0</span>/70</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Meta Description</label>
                            <textarea name="meta_description" class="form-control" rows="3" maxlength="160">{{ old('meta_description', $article->meta_description) }}</textarea>
                            <small class="meta-help">وصف للمقال لمحركات البحث (160 حرف كحد أقصى)</small>
                            <div class="text-muted mt-1"><span id="metaDescCount">0</span>/160</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Meta Keywords</label>
                            <input type="text" name="meta_keywords" class="form-control"
                                value="{{ old('meta_keywords', is_array($article->meta_keywords) ? implode(', ', $article->meta_keywords) : $article->meta_keywords) }}">
                            <small class="meta-help">كلمات مفتاحية مفصولة بفواصل</small>
                        </div>
                    </div>

                    <!-- إعدادات إضافية -->
                    <div class="form-section">
                        <h6 class="section-title">
                            <i class="fas fa-cog me-2"></i>
                            إعدادات إضافية
                        </h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">حالة المقال</label>
                                <div class="d-flex align-items-center">
                                    <label class="switch">
                                        <input type="checkbox" name="is_active" value="1"
                                            {{ old('is_active', $article->is_active) ? 'checked' : '' }}>
                                        <span class="slider"></span>
                                    </label>
                                    <span class="toggle-label">{{ $article->is_active ? 'نشط' : 'غير نشط' }}</span>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">مقال مميز</label>
                                <div class="d-flex align-items-center">
                                    <label class="switch">
                                        <input type="checkbox" name="is_featured" value="1"
                                            {{ old('is_featured', $article->is_featured) ? 'checked' : '' }}>
                                        <span class="slider"></span>
                                    </label>
                                    <span class="toggle-label">{{ $article->is_featured ? 'مميز' : 'عادي' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">مقال برعاية</label>
                                <div class="d-flex align-items-center">
                                    <label class="switch">
                                        <input type="checkbox" name="is_sponsored" value="1"
                                            {{ old('is_sponsored', $article->is_sponsored) ? 'checked' : '' }}>
                                        <span class="slider"></span>
                                    </label>
                                    <span class="toggle-label">{{ $article->is_sponsored ? 'نعم' : 'لا' }}</span>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">السماح بالتعليقات</label>
                                <div class="d-flex align-items-center">
                                    <label class="switch">
                                        <input type="checkbox" name="allow_comments" value="1"
                                            {{ old('allow_comments', $article->allow_comments ?? true) ? 'checked' : '' }}>
                                        <span class="slider"></span>
                                    </label>
                                    <span class="toggle-label">نعم</span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">وقت القراءة (بالدقائق)</label>
                            <input type="number" name="reading_time" class="form-control"
                                value="{{ old('reading_time', $article->reading_time) }}" min="1">
                            <small class="meta-help">سيتم حسابه تلقائياً من المحتوى إذا تركته فارغاً</small>
                        </div>
                    </div>

                    <!-- مقالات ذات صلة -->
                    <div class="form-section">
                        <h6 class="section-title">
                            <i class="fas fa-link me-2"></i>
                            مقالات ذات صلة
                        </h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">اختر مقالات ذات صلة</label>
                                <select name="related_articles[]" id="relatedArticlesSelect" class="form-select"
                                    multiple>
                                    @foreach ($allArticles ?? [] as $relatedArticle)
                                        @if ($relatedArticle->id != $article->id)
                                            <option value="{{ $relatedArticle->id }}"
                                                {{ in_array($relatedArticle->id, old('related_articles', $article->related_articles ?? [])) ? 'selected' : '' }}>
                                                {{ $relatedArticle->title }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">المقالات المختارة</label>
                                <div class="related-articles" id="selectedRelatedArticles">
                                    <!-- سيتم إضافة المقالات المختارة هنا عبر JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- أزرار الإجراءات -->
                    <div class="d-flex gap-3 justify-content-end mt-4">
                        <button type="button" class="btn btn-outline-secondary" onclick="window.history.back()">
                            <i class="fas fa-arrow-right me-2"></i>
                            إلغاء
                        </button>
                        <button type="submit" name="action" value="save" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            حفظ التغييرات
                        </button>
                        <button type="submit" name="action" value="save_and_continue" class="btn btn-info">
                            <i class="fas fa-edit me-2"></i>
                            حفظ ومتابعة التعديل
                        </button>
                        <button type="button" class="btn btn-danger" onclick="confirmDelete({{ $article->id }})">
                            <i class="fas fa-trash me-2"></i>
                            حذف
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/lang/summernote-ar-AR.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // تفعيل Summernote
            $('#summernote').summernote({
                height: 400,
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
                callbacks: {
                    onChange: function(contents) {
                        // تحديث وقت القراءة عند تغيير المحتوى
                        updateReadingTime(contents);
                    }
                }
            });

            // تفعيل Select2
            $('#tagsSelect').select2({
                placeholder: 'اختر الوسوم',
                allowClear: true,
                width: '100%',
                dir: 'rtl'
            });

            $('#relatedArticlesSelect').select2({
                placeholder: 'اختر مقالات ذات صلة',
                allowClear: true,
                width: '100%',
                dir: 'rtl'
            }).on('change', function() {
                updateSelectedRelatedArticles();
            });

            // تحديث التصنيفات الفرعية عند تغيير التصنيف الرئيسي
            $('select[name="category_id"]').on('change', function() {
                const categoryId = $(this).val();
                const subcategorySelect = $('select[name="subcategory_id"]');

                if (categoryId) {
                    $.ajax({
                        url: '/admin/categories/' + categoryId + '/subcategories',
                        type: 'GET',
                        success: function(data) {
                            subcategorySelect.html(
                                '<option value="">لا يوجد تصنيف فرعي</option>');
                            data.forEach(function(sub) {
                                subcategorySelect.append('<option value="' + sub.id +
                                    '">' + sub.name + '</option>');
                            });
                        }
                    });
                } else {
                    subcategorySelect.html('<option value="">لا يوجد تصنيف فرعي</option>');
                }
            });

            // حساب عدد الأحرف في Meta Title
            $('input[name="meta_title"]').on('keyup', function() {
                const count = $(this).val().length;
                $('#metaTitleCount').text(count);
                if (count > 70) {
                    $('#metaTitleCount').css('color', 'red');
                } else {
                    $('#metaTitleCount').css('color', 'inherit');
                }
            });

            // حساب عدد الأحرف في Meta Description
            $('textarea[name="meta_description"]').on('keyup', function() {
                const count = $(this).val().length;
                $('#metaDescCount').text(count);
                if (count > 160) {
                    $('#metaDescCount').css('color', 'red');
                } else {
                    $('#metaDescCount').css('color', 'inherit');
                }
            });

            // عرض المقالات المختارة
            updateSelectedRelatedArticles();

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

        // إضافة صورة جديدة
        function addImageRow() {
            const index = $('#additional-images .row').length;
            const html = `
                <div class="row mb-2" id="image-row-${index}">
                    <div class="col-md-8">
                        <input type="file" name="additional_images[]" class="form-control" accept="image/*">
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="image_captions[]" class="form-control" placeholder="تعليق على الصورة">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger" onclick="removeImageRow(${index})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            $('#additional-images').append(html);
        }

        // حذف صف صورة
        function removeImageRow(index) {
            $(`#image-row-${index}`).remove();
        }

        // حذف الصورة الرئيسية
        function removeImage() {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "سيتم حذف الصورة الرئيسية",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('.current-image').hide();
                    $('#removeImage').val('1');
                }
            });
        }


        // دوال إدارة الوسوم (Tags)
        function addCustomTag() {
            const input = document.getElementById('newTagInput');
            const tag = input.value.trim();

            if (tag) {
                // تنظيف الوسم
                const cleanTag = tag.replace(/[,#]/g, '').trim();

                if (cleanTag) {
                    const currentTags = getCurrentTags();

                    if (!currentTags.includes(cleanTag)) {
                        currentTags.push(cleanTag);
                        updateTagsDisplay(currentTags);
                        input.value = '';
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'تنبيه',
                            text: 'هذا الوسم موجود بالفعل',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                }
            }
        }

        function handleTagKeyPress(event) {
            if (event.key === 'Enter' || event.key === ',') {
                event.preventDefault();
                addCustomTag();
            }
        }

        function removeCustomTag(element) {
            const tagElement = $(element).parent();
            const tag = tagElement.text().trim();

            tagElement.remove();

            const currentTags = getCurrentTags();
            const index = currentTags.indexOf(tag);

            if (index > -1) {
                currentTags.splice(index, 1);
                updateTagsDisplay(currentTags);
            }
        }

        function getCurrentTags() {
            const tagsInput = document.getElementById('tagsInput');
            return tagsInput.value ? tagsInput.value.split(',').filter(t => t.trim() !== '') : [];
        }

        function updateTagsDisplay(tags) {
            // تحديث hidden input
            document.getElementById('tagsInput').value = tags.join(',');

            // تحديث العرض
            const container = document.getElementById('customTags');
            container.innerHTML = '';

            tags.forEach(tag => {
                if (tag.trim()) {
                    container.innerHTML += `
                <span class="tag-item">
                    ${tag}
                    <i class="fas fa-times" onclick="removeCustomTag(this)"></i>
                </span>
            `;
                }
            });
        }

        // إضافة دالة لتهيئة الوسوم الموجودة
        function initializeTags() {
            const existingTags = getCurrentTags();
            updateTagsDisplay(existingTags);
        }

        // استدعاء التهيئة عند تحميل الصفحة
        $(document).ready(function() {
            initializeTags();
            // ... باقي الكود
        });
        // حذف وسم مخصص
        function removeCustomTag(element) {
            const tagElement = $(element).parent();
            const tag = tagElement.text().trim();

            tagElement.remove();

            const currentTags = $('#customTagsInput').val();
            const tagsArray = currentTags.split(',');
            const index = tagsArray.indexOf(tag);

            if (index > -1) {
                tagsArray.splice(index, 1);
                $('#customTagsInput').val(tagsArray.join(','));
            }
        }

        // تحديث وقت القراءة
        function updateReadingTime(content) {
            const text = $(content).text();
            const wordCount = text.split(/\s+/).length;
            const readingTime = Math.ceil(wordCount / 200);

            $('input[name="reading_time"]').val(readingTime);
        }

        // تحديث المقالات المختارة
        function updateSelectedRelatedArticles() {
            const selected = $('#relatedArticlesSelect').val() || [];
            const container = $('#selectedRelatedArticles');

            container.empty();

            if (selected.length > 0) {
                selected.forEach(function(id) {
                    const option = $('#relatedArticlesSelect option[value="' + id + '"]');
                    const title = option.text();

                    container.append(`
                        <div class="related-article-item">
                            <i class="fas fa-file-alt"></i>
                            <span>${title}</span>
                        </div>
                    `);
                });
            } else {
                container.append('<p class="text-muted">لم يتم اختيار مقالات ذات صلة</p>');
            }
        }

        // تأكيد الحذف
        function confirmDelete(articleId) {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "سيتم حذف المقال نهائياً ولا يمكن التراجع عن هذا الإجراء",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/admin/articles/' + articleId,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE'
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'تم الحذف',
                                text: response.success || 'تم حذف المقال بنجاح',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = '{{ route('admin.articles.index') }}';
                            });
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ',
                                text: 'حدث خطأ أثناء الحذف',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    });
                }
            });
        }
    </script>
@endsection
