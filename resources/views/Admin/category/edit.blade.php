@extends('Admin.layout.master')

@section('title', 'تعديل القسم')

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
            /* border-color: #696cff; */
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
            /* color: #6c757d; */
            border: none;
            padding: 10px 20px;
            font-weight: 500;
        }

        .nav-tabs .nav-link.active {
            /* color: #696cff;
            border-bottom: 2px solid #696cff; */
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
            /* color: #566a7f;
            border-bottom: 1px solid #dee2e6; */
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .required::after {
            content: " *";
            color: #dc3545;
        }

        .info-icon {
            /* color: #696cff; */
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

        .slug-preview a {
            /* color: #696cff; */
            text-decoration: none;
        }

        .slug-preview a:hover {
            text-decoration: underline;
        }

        .tab-content {
            padding: 20px 0;
        }

        .children-list {
            max-height: 400px;
            overflow-y: auto;
        }

        .child-category-item {
            /* background: white; */
            /* border: 1px solid #dee2e6; */
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
        }

        .child-category-item:hover {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .child-category-item .btn-sm {
            padding: 2px 8px;
            font-size: 12px;
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
            <li class="breadcrumb-item active">تعديل قسم: {{ $category->name }}</li>
        </ol>
    </nav>

    <div class="row" bis_skin_checked="1">
        <div class="col-md-8" bis_skin_checked="1">
            <div class="card mb-4" bis_skin_checked="1">
                <div class="card-header d-flex justify-content-between align-items-center" bis_skin_checked="1">
                    <h5 class="mb-0">تعديل القسم</h5>
                    <div class="btn-group" bis_skin_checked="1">
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-right me-1"></i> رجوع
                        </a>
                        @if(!$category->isParent())
                            <a href="{{ route('admin.categories.edit', $category->parent_id) }}" class="btn btn-outline-info btn-sm">
                                <i class="fas fa-level-up-alt"></i> القسم الرئيسي
                            </a>
                        @endif
                    </div>
                </div>
                
                <div class="card-body" bis_skin_checked="1">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

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

                    <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data" id="editCategoryForm">
                        @csrf
                        @method('PUT')

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
                            @if($category->isParent())
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="children-tab" data-bs-toggle="tab" data-bs-target="#children" type="button" role="tab">
                                    <i class="fas fa-sitemap me-2"></i>الأقسام الفرعية
                                    <span class="badge bg-primary ms-2">{{ $category->children->count() }}</span>
                                </button>
                            </li>
                            @endif
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
                                                   value="{{ old('name', $category->name) }}" required>
                                            <small class="text-muted">اسم واضح ومعبر عن محتوى القسم</small>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3" bis_skin_checked="1">
                                            <label for="parent_id" class="form-label">القسم الرئيسي</label>
                                            <select class="form-select" id="parent_id" name="parent_id">
                                                <option value="">قسم رئيسي (بدون أب)</option>
                                                @foreach($parentCategories as $parent)
                                                    @if($parent->id != $category->id)
                                                        <option value="{{ $parent->id }}" 
                                                            {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>
                                                            {{ $parent->name }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            <small class="text-muted">اختياري - لإنشاء هيكل هرمي للأقسام</small>
                                        </div>
                                        
                                        <div class="col-md-12 mb-3" bis_skin_checked="1">
                                            <label for="description" class="form-label">الوصف</label>
                                            <textarea class="form-control" id="description" name="description" 
                                                      rows="4">{{ old('description', $category->description) }}</textarea>
                                            <small class="text-muted">وصف مختصر يظهر في صفحات القسم</small>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3" bis_skin_checked="1">
                                            <label for="order" class="form-label">ترتيب العرض</label>
                                            <input type="number" class="form-control" id="order" name="order" 
                                                   value="{{ old('order', $category->order) }}" min="0">
                                            <small class="text-muted">رقم يحدد ترتيب ظهور القسم</small>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3" bis_skin_checked="1">
                                            <label for="status_id" class="form-label required">الحالة</label>
                                            <select class="form-select" id="status_id" name="status_id" required>
                                                <option value="1" {{ old('status_id', $category->status_id) == 1 ? 'selected' : '' }}>
                                                    نشط
                                                </option>
                                                <option value="2" {{ old('status_id', $category->status_id) == 2 ? 'selected' : '' }}>
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
                                                    @if($category->image)
                                                        <img src="{{ get_user_image($category->image) }}" 
                                                             alt="{{ $category->name }}" 
                                                             class="category-image-preview" 
                                                             id="imagePreview">
                                                    @else
                                                        <img src="https://via.placeholder.com/200x200?text=صورة+القسم" 
                                                             alt="صورة القسم" 
                                                             class="category-image-preview" 
                                                             id="imagePreview">
                                                    @endif
                                                    <div class="image-overlay" bis_skin_checked="1">
                                                        <i class="fas fa-camera"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3" bis_skin_checked="1">
                                                <label for="image" class="form-label">تغيير الصورة</label>
                                                <input type="file" class="form-control" id="image" name="image" 
                                                       accept="image/*" onchange="previewImage(this, 'imagePreview')">
                                            </div>
                                            
                                            @if($category->image)
                                            <div class="form-check mb-3" bis_skin_checked="1">
                                                <input class="form-check-input" type="checkbox" id="delete_image" name="delete_image">
                                                <label class="form-check-label" for="delete_image">
                                                    حذف الصورة الحالية
                                                </label>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6" bis_skin_checked="1">
                                        <div class="form-section" bis_skin_checked="1">
                                            <h5>
                                                <i class="fas fa-image me-2"></i>صورة فرعية
                                            </h5>
                                            
                                            <div class="text-center mb-3" bis_skin_checked="1">
                                                <div class="image-upload-container" bis_skin_checked="1">
                                                    @if($category->sub_image)
                                                        <img src="{{ asset('storage/' . $category->sub_image) }}" 
                                                             alt="{{ $category->name }} - صورة فرعية" 
                                                             class="category-image-preview" 
                                                             id="subImagePreview">
                                                    @else
                                                        <img src="https://via.placeholder.com/200x200?text=صورة+فرعية" 
                                                             alt="صورة فرعية" 
                                                             class="category-image-preview" 
                                                             id="subImagePreview">
                                                    @endif
                                                    <div class="image-overlay" bis_skin_checked="1">
                                                        <i class="fas fa-camera"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3" bis_skin_checked="1">
                                                <label for="sub_image" class="form-label">تغيير الصورة الفرعية</label>
                                                <input type="file" class="form-control" id="sub_image" name="sub_image" 
                                                       accept="image/*" onchange="previewImage(this, 'subImagePreview')">
                                            </div>
                                            
                                            @if($category->sub_image)
                                            <div class="form-check mb-3" bis_skin_checked="1">
                                                <input class="form-check-input" type="checkbox" id="delete_sub_image" name="delete_sub_image">
                                                <label class="form-check-label" for="delete_sub_image">
                                                    حذف الصورة الفرعية الحالية
                                                </label>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="alert alert-info mt-3" bis_skin_checked="1">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>ملاحظة:</strong> الصور المسموح بها: JPEG, PNG, JPG, GIF, SVG, WEBP. الحد الأقصى للحجم: 2 ميجابايت.
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
                                                   value="{{ old('slug', $category->slug) }}">
                                            <button type="button" class="btn btn-outline-secondary" onclick="generateSlug()">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                        </div>
                                        <small class="text-muted">رابط SEO الخاص بالقسم. اتركه فارغاً لإنشاء رابط تلقائي</small>
                                        
                                        <!-- Slug Preview -->
                                        <div class="slug-preview mt-2" bis_skin_checked="1" id="slugPreview">
                                            رابط القسم: 
                                            <a href="#" target="_blank">
                                                {{ url('/categories') }}/<span id="slugValue">{{ $category->slug }}</span>
                                            </a>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3" bis_skin_checked="1">
                                        <label for="meta_title" class="form-label">عنوان الصفحة (Meta Title)</label>
                                        <input type="text" class="form-control" id="meta_title" name="meta_title" 
                                               value="{{ old('meta_title', $category->meta_title) }}">
                                        <small class="text-muted">سيظهر في نتائج محركات البحث. الطول الموصى به: 50-60 حرفاً</small>
                                        <div class="progress mt-1" style="height: 5px;">
                                            <div class="progress-bar" id="titleProgress" role="progressbar"></div>
                                        </div>
                                        <small class="text-muted text-end d-block" id="titleCount">0/60</small>
                                    </div>
                                    
                                    <div class="mb-3" bis_skin_checked="1">
                                        <label for="meta_description" class="form-label">وصف الصفحة (Meta Description)</label>
                                        <textarea class="form-control" id="meta_description" name="meta_description" 
                                                  rows="3">{{ old('meta_description', $category->meta_description) }}</textarea>
                                        <small class="text-muted">وصف مختصر يظهر في نتائج البحث. الطول الموصى به: 150-160 حرفاً</small>
                                        <div class="progress mt-1" style="height: 5px;">
                                            <div class="progress-bar" id="descProgress" role="progressbar"></div>
                                        </div>
                                        <small class="text-muted text-end d-block" id="descCount">0/160</small>
                                    </div>
                                    
                                    <div class="mb-3" bis_skin_checked="1">
                                        <label for="meta_keywords" class="form-label">الكلمات المفتاحية</label>
                                        <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" 
                                               value="{{ old('meta_keywords', $category->meta_keywords) }}">
                                        <small class="text-muted">كلمات مفتاحية مفصولة بفواصل (,)</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Children Tab -->
                            @if($category->isParent())
                            <div class="tab-pane fade" id="children" role="tabpanel">
                                <div class="form-section" bis_skin_checked="1">
                                    <div class="d-flex justify-content-between align-items-center mb-3" bis_skin_checked="1">
                                        <h5 class="mb-0">
                                            <i class="fas fa-sitemap me-2"></i>الأقسام الفرعية
                                        </h5>
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addChildModal">
                                            <i class="fas fa-plus me-1"></i>إضافة قسم فرعي
                                        </button>
                                    </div>
                                    
                                    @if($category->children->count() > 0)
                                        <div class="children-list" bis_skin_checked="1">
                                            @foreach($category->children as $child)
                                                <div class="child-category-item" bis_skin_checked="1">
                                                    <div bis_skin_checked="1">
                                                        <strong>{{ $child->name }}</strong>
                                                        @if($child->description)
                                                            <p class="text-muted mb-0 small">{{ Str::limit($child->description, 50) }}</p>
                                                        @endif
                                                        <small class="text-muted">
                                                            <i class="fas fa-box"></i> {{ $child->products_count ?? 0 }} منتج | 
                                                            <i class="fas fa-sort-numeric-up"></i> الترتيب: {{ $child->order }} | 
                                                            <i class="fas fa-circle {{ $child->status_id == 1 ? 'text-success' : 'text-danger' }}"></i>
                                                            {{ $child->status_id == 1 ? 'نشط' : 'غير نشط' }}
                                                        </small>
                                                    </div>
                                                    <div class="btn-group" bis_skin_checked="1">
                                                        <a href="{{ route('admin.categories.edit', $child) }}" 
                                                           class="btn btn-outline-primary btn-sm">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button" 
                                                                class="btn btn-outline-danger btn-sm btn-delete-child"
                                                                data-id="{{ $child->id }}"
                                                                data-name="{{ $child->name }}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-4" bis_skin_checked="1">
                                            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">لا توجد أقسام فرعية</h5>
                                            <p class="text-muted">يمكنك إضافة أقسام فرعية باستخدام زر "إضافة قسم فرعي"</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between mt-4" bis_skin_checked="1">
                            <div bis_skin_checked="1">
                                <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                                    <i class="fas fa-trash me-1"></i>حذف القسم
                                </button>
                            </div>
                            <div class="btn-group" bis_skin_checked="1">
                                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i>إلغاء
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>حفظ التعديلات
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4" bis_skin_checked="1">
            <!-- Stats Card -->
            <div class="card mb-4" bis_skin_checked="1">
                <div class="card-header" bis_skin_checked="1">
                    <h5 class="mb-0">معلومات القسم</h5>
                </div>
                <div class="card-body" bis_skin_checked="1">
                    <div class="d-flex align-items-center mb-3" bis_skin_checked="1">
                        <div class="avatar avatar-lg me-3" bis_skin_checked="1">
                            @if($category->image)
                                <img src="{{ get_user_image($category->image) }}" 
                                     alt="{{ $category->name }}" 
                                     class="rounded-circle" 
                                     style="width: 60px; height: 60px; object-fit: cover;">
                            @else
                                <div class="avatar-initial bg-label-primary rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 60px; height: 60px;">
                                    <i class="fas fa-folder fa-lg"></i>
                                </div>
                            @endif
                        </div>
                        <div bis_skin_checked="1">
                            <h6 class="mb-0">{{ $category->name }}</h6>
                            <small class="text-muted">
                                @if($category->isParent())
                                    قسم رئيسي
                                @else
                                    قسم فرعي
                                @endif
                            </small>
                        </div>
                    </div>
                    
                    <div class="list-group list-group-flush" bis_skin_checked="1">
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0" bis_skin_checked="1">
                            <span><i class="fas fa-hashtag me-2"></i>رقم المعرف</span>
                            <span class="badge bg-label-primary">{{ $category->id }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0" bis_skin_checked="1">
                            <span><i class="fas fa-calendar-alt me-2"></i>تاريخ الإنشاء</span>
                            <span>{{ $category->created_at->format('Y/m/d') }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0" bis_skin_checked="1">
                            <span><i class="fas fa-history me-2"></i>آخر تحديث</span>
                            <span>{{ $category->updated_at->format('Y/m/d') }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0" bis_skin_checked="1">
                            <span><i class="fas fa-box me-2"></i>عدد المنتجات</span>
                            <span class="badge bg-label-info">{{ $category->products_count ?? 0 }}</span>
                        </div>
                        @if($category->isParent())
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0" bis_skin_checked="1">
                            <span><i class="fas fa-sitemap me-2"></i>الأقسام الفرعية</span>
                            <span class="badge bg-label-success">{{ $category->children->count() }}</span>
                        </div>
                        @endif
                        @if(!$category->isParent())
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0" bis_skin_checked="1">
                            <span><i class="fas fa-level-up-alt me-2"></i>القسم الرئيسي</span>
                            <span>
                                <a href="{{ route('admin.categories.edit', $category->parent_id) }}" 
                                   class="badge bg-label-warning">
                                    {{ $category->parent->name ?? 'غير محدد' }}
                                </a>
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="card" bis_skin_checked="1">
                <div class="card-header" bis_skin_checked="1">
                    <h5 class="mb-0">إجراءات سريعة</h5>
                </div>
                <div class="card-body" bis_skin_checked="1">
                    <div class="d-grid gap-2" bis_skin_checked="1">
                        <a href="{{ route('admin.categories.show', $category) }}" 
                           class="btn btn-outline-primary">
                            <i class="fas fa-eye me-2"></i>عرض القسم
                        </a>
                        @if($category->isParent())
                            <a href="{{ route('admin.categories.create', ['parent_id' => $category->id]) }}" 
                               class="btn btn-outline-success">
                                <i class="fas fa-plus-circle me-2"></i>إضافة قسم فرعي
                            </a>
                        @endif
                        <button type="button" class="btn btn-outline-info" onclick="duplicateCategory()">
                            <i class="fas fa-copy me-2"></i>نسخ القسم
                        </button>
                        <a href="{{ route('admin.products.create', ['category_id' => $category->id]) }}" 
                           class="btn btn-outline-warning">
                            <i class="fas fa-plus me-2"></i>إضافة منتج جديد
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Child Modal -->
<div class="modal fade" id="addChildModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إضافة قسم فرعي جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.categories.store') }}" method="POST" id="addChildForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="parent_id" value="{{ $category->id }}">
                    <div class="mb-3">
                        <label for="child_name" class="form-label">اسم القسم الفرعي</label>
                        <input type="text" class="form-control" id="child_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="child_order" class="form-label">ترتيب العرض</label>
                        <input type="number" class="form-control" id="child_order" name="order" value="0" min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إضافة</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تأكيد الحذف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3" bis_skin_checked="1">
                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                    <h5>هل أنت متأكد من حذف هذا القسم؟</h5>
                    <p class="text-muted">سيتم حذف القسم "<strong>{{ $category->name }}</strong>" بشكل دائم.</p>
                    
                    @if($category->products_count > 0)
                        <div class="alert alert-warning" bis_skin_checked="1">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            هذا القسم يحتوي على {{ $category->products_count }} منتج. سيتم إزالة القسم من هذه المنتجات.
                        </div>
                    @endif
                    
                    @if($category->children->count() > 0)
                        <div class="alert alert-danger" bis_skin_checked="1">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            هذا القسم يحتوي على {{ $category->children->count() }} قسم فرعي. لا يمكن حذفه إلا بعد حذف أو نقل الأقسام الفرعية.
                        </div>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" id="deleteForm">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" 
                            {{ $category->children->count() > 0 ? 'disabled' : '' }}>
                        <i class="fas fa-trash me-1"></i>حذف القسم
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Image Preview Function
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        const file = input.files[0];
        
        if (file) {
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
            document.getElementById('slugValue').textContent = slug;
        }
    }
    
    // Update slug preview when slug changes
    document.getElementById('slug').addEventListener('input', function() {
        const slugValue = this.value || document.getElementById('slugValue').textContent;
        document.getElementById('slugValue').textContent = slugValue;
    });
    
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
        // Initialize
        updateCounter(titleInput, titleProgress, titleCount, 60);
    }
    
    if (descInput) {
        descInput.addEventListener('input', function() {
            updateCounter(this, descProgress, descCount, 160);
        });
        // Initialize
        updateCounter(descInput, descProgress, descCount, 160);
    }
    
    // Auto-generate meta title from name
    document.getElementById('name').addEventListener('input', function() {
        if (titleInput && !titleInput.value) {
            titleInput.value = this.value + ' - متجرنا';
            updateCounter(titleInput, titleProgress, titleCount, 60);
        }
    });
    
    // Auto-generate meta description from description
    document.getElementById('description').addEventListener('input', function() {
        if (descInput && !descInput.value) {
            descInput.value = this.value;
            updateCounter(descInput, descProgress, descCount, 160);
        }
    });
    
    // Delete Child Category
    document.querySelectorAll('.btn-delete-child').forEach(button => {
        button.addEventListener('click', function() {
            const childId = this.getAttribute('data-id');
            const childName = this.getAttribute('data-name');
            
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: `سيتم حذف القسم الفرعي "${childName}"`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء',
                confirmButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/admin/categories/${childId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire(
                                'تم الحذف!',
                                'تم حذف القسم الفرعي بنجاح.',
                                'success'
                            ).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire(
                                'خطأ!',
                                data.message || 'حدث خطأ أثناء الحذف.',
                                'error'
                            );
                        }
                    })
                    .catch(error => {
                        Swal.fire(
                            'خطأ!',
                            'حدث خطأ أثناء الحذف.',
                            'error'
                        );
                    });
                }
            });
        });
    });
    
    // Delete Category Confirmation
    function confirmDelete() {
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }
    
    // Duplicate Category
    function duplicateCategory() {
        Swal.fire({
            title: 'نسخ القسم',
            text: 'أدخل اسم للقسم المنسوخ:',
            input: 'text',
            inputValue: '{{ $category->name }} - نسخة',
            showCancelButton: true,
            confirmButtonText: 'نسخ',
            cancelButtonText: 'إلغاء',
            showLoaderOnConfirm: true,
            preConfirm: (name) => {
                if (!name) {
                    Swal.showValidationMessage('يجب إدخال اسم للقسم');
                    return false;
                }
                
                return fetch('{{ route("admin.categories.duplicate", $category) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ name: name })
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        throw new Error(data.message || 'حدث خطأ أثناء النسخ');
                    }
                    return data;
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'تم النسخ!',
                    text: 'تم نسخ القسم بنجاح.',
                    icon: 'success',
                    confirmButtonText: 'حسناً'
                }).then(() => {
                    window.location.href = '/admin/categories/' + result.value.data.id + '/edit';
                });
            }
        });
    }
    
    // Form Validation
    document.getElementById('editCategoryForm').addEventListener('submit', function(e) {
        const name = document.getElementById('name').value.trim();
        if (!name) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'يرجى إدخال اسم القسم'
            });
            return false;
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
    
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
    
    // Tab switching with form validation
    const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
    tabButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const target = this.getAttribute('data-bs-target');
            const currentTab = document.querySelector('.tab-pane.active');
            
            // You can add validation logic here before switching tabs
            console.log('Switching from tab:', currentTab.id, 'to tab:', target.substring(1));
        });
    });
</script>
@endsection