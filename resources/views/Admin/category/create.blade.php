@extends('Admin.layout.master')

@section('title', 'إضافة قسم جديد')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: "Cairo", sans-serif !important;
        }

        .category-image-preview {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            /* border: 2px dashed #dee2e6; */
            padding: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .category-image-preview:hover {
            border-color: #696cff;
            transform: scale(1.02);
        }

        .image-upload-container {
            position: relative;
            display: inline-block;
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
            border-radius: 10px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .image-upload-container:hover .image-overlay {
            opacity: 1;
        }

        .image-overlay i {
            /* color: white; */
            font-size: 24px;
        }

        .slug-input-group {
            position: relative;
        }

        .slug-input-group .form-control {
            padding-right: 40px;
        }

        .slug-input-group .btn {
            position: absolute;
            right: 0;
            top: 0;
            height: 100%;
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }

        .nav-tabs .nav-link {
            color: #6c757d;
            border: none;
            padding: 10px 20px;
            font-weight: 500;
        }

        .nav-tabs .nav-link.active {
            color: #696cff;
            border-bottom: 2px solid #696cff;
            background-color: transparent;
        }

        .form-section {
            /* background: #f8f9fa; */
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            /* border: 1px solid #e9ecef; */
        }

        .form-section h5 {
            color: #566a7f;
            /* border-bottom: 1px solid #dee2e6; */
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .required::after {
            content: " *";
            color: #dc3545;
        }

        .info-icon {
            color: #696cff;
            cursor: help;
        }

        .slug-preview {
            /* background: #e9ecef; */
            padding: 8px 12px;
            border-radius: 5px;
            margin-top: 5px;
            font-family: monospace;
            font-size: 14px;
        }

        .tab-content {
            padding: 20px 0;
        }

        .type-selector {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .type-card {
            flex: 1;
            /* border: 2px solid #dee2e6; */
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .type-card:hover {
            border-color: #696cff;
            transform: translateY(-2px);
        }

        .type-card.active {
            border-color: #696cff;
            background-color: rgba(105, 108, 255, 0.1);
        }

        .type-card i {
            font-size: 40px;
            margin-bottom: 10px;
            color: #696cff;
        }

        .type-card h6 {
            margin-bottom: 5px;
            font-weight: 600;
        }

        .type-card p {
            font-size: 12px;
            /* color: #6c757d; */
            margin-bottom: 0;
        }

        .parent-category-selection {
            max-height: 300px;
            overflow-y: auto;
            /* border: 1px solid #dee2e6; */
            border-radius: 5px;
            padding: 10px;
        }

        .parent-category-item {
            padding: 8px 12px;
            border-radius: 5px;
            margin-bottom: 5px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .parent-category-item:hover {
            /* background-color: #e9ecef; */
        }

        .parent-category-item.selected {
            background-color: #696cff;
            /* color: white; */
        }

        .quick-tips {
            /* background: #e7f7ff; */
            border-right: 4px solid #696cff;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .quick-tips h6 {
            color: #696cff;
            margin-bottom: 10px;
        }

        .quick-tips ul {
            margin-bottom: 0;
            padding-left: 20px;
        }

        .quick-tips li {
            margin-bottom: 5px;
            font-size: 14px;
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
                <a href="{{ route('admin.categories.index') }}">الأقسام</a>
            </li>
            <li class="breadcrumb-item active">إضافة قسم جديد</li>
        </ol>
    </nav>

    <div class="row" bis_skin_checked="1">
        <div class="col-md-8" bis_skin_checked="1">
            <div class="card mb-4" bis_skin_checked="1">
                <div class="card-header d-flex justify-content-between align-items-center" bis_skin_checked="1">
                    <h5 class="mb-0">إضافة قسم جديد</h5>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-right me-1"></i> رجوع للقائمة
                    </a>
                </div>
                
                <div class="card-body" bis_skin_checked="1">
                    <!-- Quick Tips -->
                    <div class="quick-tips" bis_skin_checked="1">
                        <h6><i class="fas fa-lightbulb me-2"></i>نصائح سريعة:</h6>
                        <ul>
                            <li>اختر نوع القسم المناسب (رئيسي أو فرعي)</li>
                            <li>استخدم أسماء واضحة ومعبرة عن محتوى القسم</li>
                            <li>أضف وصفاً مختصراً لتحسين تجربة المستخدم</li>
                            <li>اختر صورة مميزة تمثل القسم بشكل واضح</li>
                            <li>اضبط إعدادات SEO لتحسين ظهور القسم في محركات البحث</li>
                        </ul>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" id="createCategoryForm">
                        @csrf

                        <!-- Category Type Selection -->
                        <div class="form-section" bis_skin_checked="1">
                            <h5><i class="fas fa-layer-group me-2"></i>نوع القسم</h5>
                            <div class="type-selector" bis_skin_checked="1">
                                <div class="type-card active" id="parentType" onclick="selectType('parent')">
                                    <i class="fas fa-folder"></i>
                                    <h6>قسم رئيسي</h6>
                                    <p>قسم مستقل يمكن أن يحتوي على أقسام فرعية</p>
                                </div>
                                <div class="type-card" id="childType" onclick="selectType('child')">
                                    <i class="fas fa-folder-plus"></i>
                                    <h6>قسم فرعي</h6>
                                    <p>قسم تابع لقسم رئيسي</p>
                                </div>
                            </div>
                            
                            <!-- Parent Category Selection (Hidden by default) -->
                            <div id="parentSelection" style="display: none;">
                                <label class="form-label required">اختر القسم الرئيسي</label>
                                @if($parentCategories->count() > 0)
                                    <div class="parent-category-selection" bis_skin_checked="1">
                                        @foreach($parentCategories as $parent)
                                            <div class="parent-category-item" 
                                                 onclick="selectParent({{ $parent->id }}, '{{ $parent->name }}')"
                                                 id="parent-{{ $parent->id }}">
                                                <i class="fas fa-folder me-2"></i>
                                                {{ $parent->name }}
                                                @if($parent->children_count > 0)
                                                    <span class="badge bg-info float-end">{{ $parent->children_count }} فرعي</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="alert alert-warning" bis_skin_checked="1">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        لا توجد أقسام رئيسية متاحة. يجب إنشاء قسم رئيسي أولاً.
                                    </div>
                                @endif
                                <input type="hidden" name="parent_id" id="selected_parent_id">
                            </div>
                        </div>

                        <!-- Tabs Navigation -->
                        <ul class="nav nav-tabs mb-4" id="categoryTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic" type="button" role="tab">
                                    <i class="fas fa-info-circle me-2"></i>البيانات الأساسية
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="images-tab" data-bs-toggle="tab" data-bs-target="#images" type="button" role="tab">
                                    <i class="fas fa-images me-2"></i>الصور
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="seo-tab" data-bs-toggle="tab" data-bs-target="#seo" type="button" role="tab">
                                    <i class="fas fa-search me-2"></i>SEO
                                </button>
                            </li>
                        </ul>

                        <!-- Tabs Content -->
                        <div class="tab-content" id="categoryTabsContent">
                            <!-- Basic Information Tab -->
                            <div class="tab-pane fade show active" id="basic" role="tabpanel">
                                <div class="form-section" bis_skin_checked="1">
                                    <h5>
                                        <i class="fas fa-info-circle me-2"></i>معلومات القسم
                                    </h5>
                                    
                                    <div class="row" bis_skin_checked="1">
                                        <div class="col-md-6 mb-3" bis_skin_checked="1">
                                            <label for="name" class="form-label required">اسم القسم</label>
                                            <input type="text" class="form-control" id="name" name="name" 
                                                   value="{{ old('name') }}" required>
                                            <small class="text-muted">اسم واضح ومعبر عن محتوى القسم</small>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3" bis_skin_checked="1">
                                            <label for="order" class="form-label">ترتيب العرض</label>
                                            <input type="number" class="form-control" id="order" name="order" 
                                                   value="{{ old('order', 0) }}" min="0">
                                            <small class="text-muted">رقم يحدد ترتيب ظهور القسم (الأصغر أولاً)</small>
                                        </div>
                                        
                                        <div class="col-md-12 mb-3" bis_skin_checked="1">
                                            <label for="description" class="form-label">الوصف</label>
                                            <textarea class="form-control" id="description" name="description" 
                                                      rows="4">{{ old('description') }}</textarea>
                                            <small class="text-muted">وصف مختصر يظهر في صفحات القسم</small>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3" bis_skin_checked="1">
                                            <label for="status_id" class="form-label required">الحالة</label>
                                            <select class="form-select" id="status_id" name="status_id" required>
                                                <option value="1" {{ old('status_id', 1) == 1 ? 'selected' : '' }}>
                                                    نشط
                                                </option>
                                                <option value="2" {{ old('status_id') == 2 ? 'selected' : '' }}>
                                                    غير نشط
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Images Tab -->
                            <div class="tab-pane fade" id="images" role="tabpanel">
                                <div class="row" bis_skin_checked="1">
                                    <div class="col-md-6" bis_skin_checked="1">
                                        <div class="form-section" bis_skin_checked="1">
                                            <h5>
                                                <i class="fas fa-image me-2"></i>صورة القسم الرئيسية
                                            </h5>
                                            
                                            <div class="text-center mb-3" bis_skin_checked="1">
                                                <div class="image-upload-container" bis_skin_checked="1">
                                                    <img src="https://via.placeholder.com/200x200?text=صورة+القسم" 
                                                         alt="صورة القسم" 
                                                         class="category-image-preview" 
                                                         id="imagePreview">
                                                    <div class="image-overlay" bis_skin_checked="1">
                                                        <i class="fas fa-camera"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3" bis_skin_checked="1">
                                                <label for="image" class="form-label">اختر صورة</label>
                                                <input type="file" class="form-control" id="image" name="image" 
                                                       accept="image/*" onchange="previewImage(this, 'imagePreview')">
                                                <small class="text-muted">الحجم الأمثل: 800×800 بكسل. الحد الأقصى: 2MB</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6" bis_skin_checked="1">
                                        <div class="form-section" bis_skin_checked="1">
                                            <h5>
                                                <i class="fas fa-image me-2"></i>صورة فرعية
                                                <span class="badge bg-secondary">اختياري</span>
                                            </h5>
                                            
                                            <div class="text-center mb-3" bis_skin_checked="1">
                                                <div class="image-upload-container" bis_skin_checked="1">
                                                    <img src="https://via.placeholder.com/200x200?text=صورة+فرعية" 
                                                         alt="صورة فرعية" 
                                                         class="category-image-preview" 
                                                         id="subImagePreview">
                                                    <div class="image-overlay" bis_skin_checked="1">
                                                        <i class="fas fa-camera"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3" bis_skin_checked="1">
                                                <label for="sub_image" class="form-label">اختر صورة فرعية</label>
                                                <input type="file" class="form-control" id="sub_image" name="sub_image" 
                                                       accept="image/*" onchange="previewImage(this, 'subImagePreview')">
                                                <small class="text-muted">الحجم الأمثل: 800×800 بكسل. الحد الأقصى: 2MB</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="alert alert-info mt-3" bis_skin_checked="1">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>ملاحظة:</strong> الصور المسموح بها: JPEG, PNG, JPG, GIF, SVG, WEBP.
                                </div>
                            </div>

                            <!-- SEO Tab -->
                            <div class="tab-pane fade" id="seo" role="tabpanel">
                                <div class="form-section" bis_skin_checked="1">
                                    <h5>
                                        <i class="fas fa-search me-2"></i>إعدادات SEO
                                    </h5>
                                    
                                    <div class="mb-3" bis_skin_checked="1">
                                        <label for="slug" class="form-label">الرابط (Slug)</label>
                                        <div class="slug-input-group" bis_skin_checked="1">
                                            <input type="text" class="form-control" id="slug" name="slug" 
                                                   value="{{ old('slug') }}">
                                            <button type="button" class="btn btn-outline-secondary" onclick="generateSlug()">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                        </div>
                                        <small class="text-muted">رابط SEO الخاص بالقسم. اتركه فارغاً لإنشاء رابط تلقائي</small>
                                        
                                        <!-- Slug Preview -->
                                        <div class="slug-preview mt-2" bis_skin_checked="1" id="slugPreview">
                                            رابط القسم: 
                                            <span id="slugPreviewText">سيظهر هنا بعد إدخال الاسم</span>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3" bis_skin_checked="1">
                                        <label for="meta_title" class="form-label">عنوان الصفحة (Meta Title)</label>
                                        <input type="text" class="form-control" id="meta_title" name="meta_title" 
                                               value="{{ old('meta_title') }}">
                                        <small class="text-muted">سيظهر في نتائج محركات البحث. الطول الموصى به: 50-60 حرفاً</small>
                                        <div class="progress mt-1" style="height: 5px;">
                                            <div class="progress-bar" id="titleProgress" role="progressbar"></div>
                                        </div>
                                        <small class="text-muted text-end d-block" id="titleCount">0/60</small>
                                    </div>
                                    
                                    <div class="mb-3" bis_skin_checked="1">
                                        <label for="meta_description" class="form-label">وصف الصفحة (Meta Description)</label>
                                        <textarea class="form-control" id="meta_description" name="meta_description" 
                                                  rows="3">{{ old('meta_description') }}</textarea>
                                        <small class="text-muted">وصف مختصر يظهر في نتائج البحث. الطول الموصى به: 150-160 حرفاً</small>
                                        <div class="progress mt-1" style="height: 5px;">
                                            <div class="progress-bar" id="descProgress" role="progressbar"></div>
                                        </div>
                                        <small class="text-muted text-end d-block" id="descCount">0/160</small>
                                    </div>
                                    
                                    <div class="mb-3" bis_skin_checked="1">
                                        <label for="meta_keywords" class="form-label">الكلمات المفتاحية</label>
                                        <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" 
                                               value="{{ old('meta_keywords') }}">
                                        <small class="text-muted">كلمات مفتاحية مفصولة بفواصل (,) - اختياري</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between mt-4" bis_skin_checked="1">
                            <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                <i class="fas fa-redo me-1"></i>إعادة تعيين
                            </button>
                            <div class="btn-group" bis_skin_checked="1">
                                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i>إلغاء
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>حفظ القسم
                                </button>
                                <button type="button" class="btn btn-success" onclick="saveAndAddAnother()">
                                    <i class="fas fa-plus-circle me-1"></i>حفظ وإضافة جديد
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4" bis_skin_checked="1">
            <!-- Quick Guide Card -->
            <div class="card mb-4" bis_skin_checked="1">
                <div class="card-header" bis_skin_checked="1">
                    <h5 class="mb-0"><i class="fas fa-question-circle me-2"></i>دليل سريع</h5>
                </div>
                <div class="card-body" bis_skin_checked="1">
                    <div class="accordion" id="guideAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#guide1">
                                    ما هو الفرق بين القسم الرئيسي والفرعي؟
                                </button>
                            </h2>
                            <div id="guide1" class="accordion-collapse collapse" data-bs-parent="#guideAccordion">
                                <div class="accordion-body">
                                    <strong>القسم الرئيسي:</strong> قسم مستقل يمكن أن يحتوي على أقسام فرعية. مثال: "إلكترونيات".
                                    <br><br>
                                    <strong>القسم الفرعي:</strong> قسم تابع لقسم رئيسي. مثال: "هواتف ذكية" تابع لـ"إلكترونيات".
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#guide2">
                                    كيف أختار ترتيب القسم؟
                                </button>
                            </h2>
                            <div id="guide2" class="accordion-collapse collapse" data-bs-parent="#guideAccordion">
                                <div class="accordion-body">
                                    الرقم الأصغر يظهر أولاً. مثال:
                                    <ul class="mt-2">
                                        <li>ترتيب 1 → يظهر أولاً</li>
                                        <li>ترتيب 2 → يظهر ثانياً</li>
                                        <li>ترتيب 0 → يظهر أخيراً</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#guide3">
                                    نصائح لإعدادات SEO
                                </button>
                            </h2>
                            <div id="guide3" class="accordion-collapse collapse" data-bs-parent="#guideAccordion">
                                <div class="accordion-body">
                                    1. استخدم كلمات مفتاحية في الاسم والوصف<br>
                                    2. اجعل العنوان واضحاً ومختصراً (50-60 حرف)<br>
                                    3. اكتب وصفاً جذاباً (150-160 حرف)<br>
                                    4. استخدم روابط واضحة وبدون رموز غريبة
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Categories -->
            <div class="card" bis_skin_checked="1">
                <div class="card-header" bis_skin_checked="1">
                    <div class="d-flex justify-content-between align-items-center" bis_skin_checked="1">
                        <h5 class="mb-0"><i class="fas fa-history me-2"></i>آخر الأقسام المضافة</h5>
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-outline-primary">عرض الكل</a>
                    </div>
                </div>
                <div class="card-body" bis_skin_checked="1">
                    @if($recentCategories->count() > 0)
                        <div class="list-group list-group-flush" bis_skin_checked="1">
                            @foreach($recentCategories as $recent)
                                <div class="list-group-item px-0 d-flex justify-content-between align-items-center" bis_skin_checked="1">
                                    <div bis_skin_checked="1">
                                        <h6 class="mb-1">{{ $recent->name }}</h6>
                                        <small class="text-muted">
                                            {{ $recent->created_at->diffForHumans() }}
                                            @if(!$recent->isParent())
                                                <span class="badge bg-secondary ms-2">فرعي</span>
                                            @endif
                                        </small>
                                    </div>
                                    <a href="{{ route('admin.categories.edit', $recent) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3" bis_skin_checked="1">
                            <i class="fas fa-folder-open fa-2x text-muted mb-3"></i>
                            <p class="text-muted mb-0">لا توجد أقسام مضافة حديثاً</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Save and Add Another Modal -->
<div class="modal fade" id="saveAndAddModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تم الحفظ بنجاح</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                <h5>تم إضافة القسم بنجاح</h5>
                <p class="text-muted">ماذا تريد أن تفعل بعد ذلك؟</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-primary" onclick="window.location.href='{{ route('admin.categories.create') }}'">
                    <i class="fas fa-plus me-1"></i>إضافة قسم جديد
                </button>
                <button type="button" class="btn btn-secondary" onclick="window.location.href='{{ route('admin.categories.index') }}'">
                    <i class="fas fa-list me-1"></i>عرض جميع الأقسام
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Category Type Selection
    function selectType(type) {
        const parentCard = document.getElementById('parentType');
        const childCard = document.getElementById('childType');
        const parentSelection = document.getElementById('parentSelection');
        const parentIdInput = document.getElementById('selected_parent_id');
        
        if (type === 'parent') {
            parentCard.classList.add('active');
            childCard.classList.remove('active');
            parentSelection.style.display = 'none';
            parentIdInput.value = ''; // Clear parent ID
        } else {
            parentCard.classList.remove('active');
            childCard.classList.add('active');
            parentSelection.style.display = 'block';
            
            // Check if there are parent categories
            const parentItems = document.querySelectorAll('.parent-category-item');
            if (parentItems.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'لا توجد أقسام رئيسية',
                    text: 'يجب إنشاء قسم رئيسي أولاً قبل إنشاء قسم فرعي.',
                    confirmButtonText: 'حسناً'
                });
                // Revert to parent type
                selectType('parent');
            }
        }
    }
    
    // Select Parent Category
    function selectParent(parentId, parentName) {
        // Remove selected class from all items
        document.querySelectorAll('.parent-category-item').forEach(item => {
            item.classList.remove('selected');
        });
        
        // Add selected class to clicked item
        const selectedItem = document.getElementById('parent-' + parentId);
        selectedItem.classList.add('selected');
        
        // Set hidden input value
        document.getElementById('selected_parent_id').value = parentId;
        
        // Update category name placeholder
        const nameInput = document.getElementById('name');
        nameInput.placeholder = `مثال: ${parentName} - فرعي`;
    }
    
    // Image Preview Function
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        const file = input.files[0];
        
        if (file) {
            // Check file size (2MB max)
            const maxSize = 2 * 1024 * 1024; // 2MB in bytes
            if (file.size > maxSize) {
                Swal.fire({
                    icon: 'error',
                    title: 'ملف كبير جداً',
                    text: 'حجم الملف يجب أن يكون أقل من 2 ميجابايت',
                    confirmButtonText: 'حسناً'
                });
                input.value = ''; // Clear the file input
                return;
            }
            
            // Check file type
            const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/svg+xml'];
            if (!validTypes.includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'نوع ملف غير مدعوم',
                    text: 'الرجاء اختيار صورة من نوع: JPEG, PNG, JPG, GIF, SVG, WEBP',
                    confirmButtonText: 'حسناً'
                });
                input.value = ''; // Clear the file input
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    }
    
    // Slug Generation
    function generateSlug() {
        const name = document.getElementById('name').value;
        if (name) {
            let slug = name
                .toLowerCase()
                .replace(/[^\u0600-\u06FF\w\s-]/g, '') // Remove special chars except Arabic and English
                .replace(/\s+/g, '-')
                .replace(/[-\s]+/g, '-')
                .trim();
            
            document.getElementById('slug').value = slug;
            updateSlugPreview();
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'يرجى إدخال اسم القسم أولاً',
                text: 'يجب إدخال اسم القسم قبل إنشاء الرابط',
                confirmButtonText: 'حسناً'
            });
        }
    }
    
    // Update Slug Preview
    function updateSlugPreview() {
        const slug = document.getElementById('slug').value;
        const name = document.getElementById('name').value;
        const parentId = document.getElementById('selected_parent_id').value;
        
        let previewText = '';
        
        if (slug) {
            previewText = slug;
        } else if (name) {
            // Generate temporary slug for preview
            previewText = name.toLowerCase()
                .replace(/[^\u0600-\u06FF\w\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/[-\s]+/g, '-')
                .trim();
        }
        
        document.getElementById('slugPreviewText').textContent = 
            parentId ? `/${previewText}` : previewText;
    }
    
    // SEO Character Counters
    const titleInput = document.getElementById('meta_title');
    const descInput = document.getElementById('meta_description');
    const titleProgress = document.getElementById('titleProgress');
    const descProgress = document.getElementById('descProgress');
    const titleCount = document.getElementById('titleCount');
    const descCount = document.getElementById('descCount');
    
    function updateCounter(input, progress, countElement, max) {
        const length = input.value.length;
        const percentage = Math.min((length / max) * 100, 100);
        
        // Update progress bar
        progress.style.width = percentage + '%';
        
        // Update color based on length
        if (length <= max) {
            progress.className = 'progress-bar bg-success';
        } else {
            progress.className = 'progress-bar bg-danger';
        }
        
        // Update count text
        countElement.textContent = length + '/' + max;
    }
    
    if (titleInput) {
        titleInput.addEventListener('input', function() {
            updateCounter(this, titleProgress, titleCount, 60);
        });
    }
    
    if (descInput) {
        descInput.addEventListener('input', function() {
            updateCounter(this, descProgress, descCount, 160);
        });
    }
    
    // Auto-generate meta title from name
    document.getElementById('name').addEventListener('input', function() {
        if (titleInput && !titleInput.value) {
            titleInput.value = this.value;
            updateCounter(titleInput, titleProgress, titleCount, 60);
        }
        updateSlugPreview();
    });
    
    // Auto-generate meta description from description
    document.getElementById('description').addEventListener('input', function() {
        if (descInput && !descInput.value) {
            descInput.value = this.value;
            updateCounter(descInput, descProgress, descCount, 160);
        }
    });
    
    // Auto-update slug preview when slug changes
    document.getElementById('slug').addEventListener('input', updateSlugPreview);
    
    // Save and Add Another
    let saveAndAdd = false;
    
    function saveAndAddAnother() {
        saveAndAdd = true;
        document.getElementById('createCategoryForm').submit();
    }
    
    // Form Validation
    document.getElementById('createCategoryForm').addEventListener('submit', function(e) {
        const name = document.getElementById('name').value.trim();
        const parentId = document.getElementById('selected_parent_id').value;
        const isChildType = document.getElementById('childType').classList.contains('active');
        
        if (!name) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'يرجى إدخال اسم القسم'
            });
            return false;
        }
        
        if (isChildType && !parentId) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'يرجى اختيار قسم رئيسي'
            });
            return false;
        }
        
        // Check image file sizes
        const imageInput = document.getElementById('image');
        const subImageInput = document.getElementById('sub_image');
        
        if (imageInput.files.length > 0) {
            const imageFile = imageInput.files[0];
            if (imageFile.size > 2 * 1024 * 1024) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'ملف كبير جداً',
                    text: 'حجم صورة القسم يجب أن يكون أقل من 2 ميجابايت'
                });
                return false;
            }
        }
        
        if (subImageInput.files.length > 0) {
            const subImageFile = subImageInput.files[0];
            if (subImageFile.size > 2 * 1024 * 1024) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'ملف كبير جداً',
                    text: 'حجم الصورة الفرعية يجب أن يكون أقل من 2 ميجابايت'
                });
                return false;
            }
        }
        
        // Show loading
        Swal.fire({
            title: 'جاري الحفظ...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    });
    
    // Reset Form
    function resetForm() {
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: 'سيتم مسح جميع البيانات المدخلة',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'نعم، امسح',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('createCategoryForm').reset();
                document.getElementById('selected_parent_id').value = '';
                document.querySelectorAll('.parent-category-item').forEach(item => {
                    item.classList.remove('selected');
                });
                document.getElementById('imagePreview').src = 'https://via.placeholder.com/200x200?text=صورة+القسم';
                document.getElementById('subImagePreview').src = 'https://via.placeholder.com/200x200?text=صورة+فرعية';
                updateCounter(titleInput, titleProgress, titleCount, 60);
                updateCounter(descInput, descProgress, descCount, 160);
                updateSlugPreview();
                
                Swal.fire({
                    icon: 'success',
                    title: 'تم المسح',
                    text: 'تم مسح جميع البيانات بنجاح',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        });
    }
    
    // Handle form submission response
    @if(session('success') && request()->has('add_another'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modal = new bootstrap.Modal(document.getElementById('saveAndAddModal'));
                modal.show();
            });
        </script>
    @endif
</script>
@endsection