@extends('Admin.layout.master')

@section('title', 'عرض القسم: ' . $category->name)

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css" />
    <style>
        body {
            font-family: "Cairo", sans-serif !important;
        }

        .category-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }

        .category-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: rgba(255, 255, 255, 0.1);
            transform: rotate(45deg);
        }

        .category-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 5px solid white;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .category-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .category-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }

        .stat-card {
            /* background: white; */
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 24px;
        }

        .stat-card.products .stat-icon {
            background: rgba(52, 152, 219, 0.1);
            color: #3498db;
        }

        .stat-card.subcategories .stat-icon {
            background: rgba(46, 204, 113, 0.1);
            color: #2ecc71;
        }

        .stat-card.status .stat-icon {
            background: rgba(241, 196, 15, 0.1);
            color: #f1c40f;
        }

        .stat-card.order .stat-icon {
            background: rgba(155, 89, 182, 0.1);
            color: #9b59b6;
        }

        .stat-number {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #7f8c8d;
            font-size: 14px;
        }

        .info-card {
            /* background: white; */
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .info-card h5 {
            color: #2c3e50;
            /* border-bottom: 2px solid #f8f9fa; */
            padding-bottom: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .info-card h5 i {
            margin-left: 10px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            /* border-bottom: 1px solid #f8f9fa; */
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #7f8c8d;
            font-weight: 500;
        }

        .info-value {
            color: #2c3e50;
            font-weight: 600;
        }

        .badge-status {
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
        }

        .badge-status.active {
            /* background: #d4edda; */
            color: #155724;
        }

        .badge-status.inactive {
            background: #f8d7da;
            color: #721c24;
        }

        .image-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .image-item {
            position: relative;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
            cursor: pointer;
        }

        .image-item img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .image-item:hover img {
            transform: scale(1.1);
        }

        .image-label {
            position: absolute;
            bottom: 0;
            right: 0;
            left: 0;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 5px;
            text-align: center;
            font-size: 12px;
        }

        .children-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 15px;
        }

        .child-card {
            /* background: white; */
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .child-card:hover {
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
            transform: translateY(-5px);
        }

        .child-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .child-body {
            padding: 15px;
        }

        .child-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #2c3e50;
        }

        .child-meta {
            font-size: 12px;
            color: #7f8c8d;
            margin-bottom: 10px;
        }

        .child-products {
            font-size: 12px;
            color: #3498db;
            font-weight: 500;
        }

        .timeline {
            position: relative;
            padding: 20px 0;
        }

        .timeline::before {
            content: '';
            position: absolute;
            right: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            /* background: #e0e0e0; */
        }

        .timeline-item {
            position: relative;
            padding-right: 40px;
            margin-bottom: 20px;
        }

        .timeline-item:last-child {
            margin-bottom: 0;
        }

        .timeline-dot {
            position: absolute;
            right: 8px;
            top: 5px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #3498db;
            border: 3px solid white;
            box-shadow: 0 0 0 3px #e0e0e0;
        }

        .timeline-content {
            /* background: #f8f9fa; */
            padding: 15px;
            border-radius: 10px;
        }

        .timeline-date {
            font-size: 12px;
            color: #7f8c8d;
            margin-bottom: 5px;
        }

        .timeline-title {
            font-weight: 600;
            margin-bottom: 5px;
            color: #2c3e50;
        }

        .action-buttons {
            position: fixed;
            bottom: 30px;
            left: 30px;
            z-index: 1000;
        }

        .action-buttons .btn {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .qr-code {
            width: 150px;
            height: 150px;
            margin: 0 auto;
            /* background: #f8f9fa; */
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2c3e50;
        }

        .seo-preview {
            /* background: #f8f9fa; */
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }

        .seo-preview .title {
            color: #1a0dab;
            font-size: 18px;
            font-weight: normal;
            margin-bottom: 5px;
            line-height: 1.3;
        }

        .seo-preview .url {
            color: #006621;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .seo-preview .description {
            color: #545454;
            font-size: 14px;
            line-height: 1.4;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y" bis_skin_checked="1">
        <!-- Category Header -->
        <div class="category-header" bis_skin_checked="1">
            <div class="row align-items-center" bis_skin_checked="1">
                <div class="col-auto" bis_skin_checked="1">
                    <div class="category-avatar" bis_skin_checked="1">
                        @if ($category->image)
                            <img src="{{ get_user_image($category->image) }} " alt="{{ $category->name }}">
                        @else
                            <div class="d-flex align-items-center justify-content-center h-100 bg-white"
                                bis_skin_checked="1">
                                <i class="fas fa-folder fa-3x text-primary"></i>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col" bis_skin_checked="1">
                    <div class="d-flex justify-content-between align-items-start" bis_skin_checked="1">
                        <div bis_skin_checked="1">
                            <h1 class="mb-2">{{ $category->name }}</h1>
                            <p class="mb-1 opacity-75">
                                <i class="fas fa-hashtag"></i> ID: {{ $category->id }}
                                @if ($category->isParent())
                                    <span class="mx-2">•</span>
                                    <i class="fas fa-layer-group"></i> قسم رئيسي
                                @else
                                    <span class="mx-2">•</span>
                                    <i class="fas fa-level-up-alt"></i> قسم فرعي لـ:
                                    <a href="{{ route('admin.categories.show', $category->parent_id) }}" class="text-white">
                                        {{ $category->parent->name ?? 'غير محدد' }}
                                    </a>
                                @endif
                            </p>
                            @if ($category->description)
                                <p class="mb-0 opacity-75">
                                    <i class="fas fa-align-right"></i> {{ Str::limit($category->description, 150) }}
                                </p>
                            @endif
                        </div>
                        <div class="btn-group" bis_skin_checked="1">
                            <button class="btn btn-light" onclick="window.print()">
                                <i class="fas fa-print"></i>
                            </button>
                            <button class="btn btn-light" onclick="shareCategory()">
                                <i class="fas fa-share-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="category-stats" bis_skin_checked="1">
            <div class="stat-card products" bis_skin_checked="1">
                <div class="stat-icon" bis_skin_checked="1">
                    <i class="fas fa-box"></i>
                </div>
                <div class="stat-number" bis_skin_checked="1">{{ $category->products_count }}</div>
                <div class="stat-label" bis_skin_checked="1">منتج</div>
            </div>

            @if ($category->isParent())
                <div class="stat-card subcategories" bis_skin_checked="1">
                    <div class="stat-icon" bis_skin_checked="1">
                        <i class="fas fa-sitemap"></i>
                    </div>
                    <div class="stat-number" bis_skin_checked="1">{{ $category->children_count }}</div>
                    <div class="stat-label" bis_skin_checked="1">قسم فرعي</div>
                </div>
            @endif

            <div class="stat-card status" bis_skin_checked="1">
                <div class="stat-icon" bis_skin_checked="1">
                    <i class="fas fa-circle"></i>
                </div>
                <div class="stat-number" bis_skin_checked="1">
                    @if ($category->status_id == 1)
                        <span class="badge-status active">نشط</span>
                    @else
                        <span class="badge-status inactive">غير نشط</span>
                    @endif
                </div>
                <div class="stat-label" bis_skin_checked="1">الحالة</div>
            </div>

            <div class="stat-card order" bis_skin_checked="1">
                <div class="stat-icon" bis_skin_checked="1">
                    <i class="fas fa-sort-numeric-up"></i>
                </div>
                <div class="stat-number" bis_skin_checked="1">{{ $category->order }}</div>
                <div class="stat-label" bis_skin_checked="1">ترتيب العرض</div>
            </div>
        </div>

        <div class="row" bis_skin_checked="1">
            <!-- Left Column - Basic Info & Images -->
            <div class="col-lg-8" bis_skin_checked="1">
                <!-- Basic Information -->
                <div class="info-card" bis_skin_checked="1">
                    <h5><i class="fas fa-info-circle"></i> المعلومات الأساسية</h5>
                    <div class="row" bis_skin_checked="1">
                        <div class="col-md-6" bis_skin_checked="1">
                            <div class="info-item" bis_skin_checked="1">
                                <span class="info-label">اسم القسم</span>
                                <span class="info-value">{{ $category->name }}</span>
                            </div>
                            <div class="info-item" bis_skin_checked="1">
                                <span class="info-label">الرابط (Slug)</span>
                                <span class="info-value">
                                    <a href="{{ url('/categories/' . $category->slug) }}" target="_blank">
                                        {{ $category->slug }}
                                        <i class="fas fa-external-link-alt ms-1"></i>
                                    </a>
                                </span>
                            </div>
                            <div class="info-item" bis_skin_checked="1">
                                <span class="info-label">الترتيب</span>
                                <span class="info-value">{{ $category->order }}</span>
                            </div>
                        </div>
                        <div class="col-md-6" bis_skin_checked="1">
                            <div class="info-item" bis_skin_checked="1">
                                <span class="info-label">الحالة</span>
                                <span class="info-value">
                                    @if ($category->status_id == 1)
                                        <span class="badge-status active">نشط</span>
                                    @else
                                        <span class="badge-status inactive">غير نشط</span>
                                    @endif
                                </span>
                            </div>
                            <div class="info-item" bis_skin_checked="1">
                                <span class="info-label">تاريخ الإنشاء</span>
                                <span class="info-value">{{ $category->created_at->format('Y/m/d h:i A') }}</span>
                            </div>
                            <div class="info-item" bis_skin_checked="1">
                                <span class="info-label">آخر تحديث</span>
                                <span class="info-value">{{ $category->updated_at->format('Y/m/d h:i A') }}</span>
                            </div>
                        </div>
                    </div>

                    @if ($category->description)
                        <div class="mt-4" bis_skin_checked="1">
                            <h6 class="mb-2">الوصف</h6>
                            <div class="bg-light p-3 rounded" bis_skin_checked="1">
                                {{ $category->description }}
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Images Gallery -->
                @if ($category->image || $category->sub_image)
                    <div class="info-card" bis_skin_checked="1">
                        <h5><i class="fas fa-images"></i> الصور</h5>
                        <div class="image-gallery" bis_skin_checked="1">
                            @if ($category->image)
                                <a href="{{ get_user_image($category->image) }} " class="image-item"
                                    data-fancybox="gallery">
                                    <img src="{{ get_user_image($category->image) }} " alt="صورة القسم">
                                    <div class="image-label" bis_skin_checked="1">صورة القسم</div>
                                </a>
                            @endif

                            @if ($category->sub_image)
                                <a href="{{ get_user_image($category->sub_image) }} " class="image-item"
                                    data-fancybox="gallery">
                                    <img src="{{ get_user_image($category->sub_image) }} " alt="صورة فرعية">
                                    <div class="image-label" bis_skin_checked="1">صورة فرعية</div>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- SEO Information -->
                <div class="info-card" bis_skin_checked="1">
                    <h5><i class="fas fa-search"></i> إعدادات SEO</h5>
                    <div class="seo-preview" bis_skin_checked="1">
                        <div class="title" bis_skin_checked="1">
                            {{ $category->meta_title ?: $category->name }}
                        </div>
                        <div class="url" bis_skin_checked="1">
                            {{ url('/categories/' . $category->slug) }}
                        </div>
                        <div class="description" bis_skin_checked="1">
                            {{ $category->meta_description ?: ($category->description ?: 'لا يوجد وصف متاح') }}
                        </div>
                    </div>

                    @if ($category->meta_keywords)
                        <div class="mt-3" bis_skin_checked="1">
                            <h6 class="mb-2">الكلمات المفتاحية</h6>
                            <div class="d-flex flex-wrap gap-2" bis_skin_checked="1">
                                @foreach (explode(',', $category->meta_keywords) as $keyword)
                                    <span class="badge bg-secondary">{{ trim($keyword) }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Column - Hierarchy & Activity -->
            <div class="col-lg-4" bis_skin_checked="1">
                <!-- Category Hierarchy -->
                <div class="info-card" bis_skin_checked="1">
                    <h5><i class="fas fa-sitemap"></i> الهيكل التنظيمي</h5>

                    @if ($category->isParent() && $category->children_count > 0)
                        <div class="children-grid" bis_skin_checked="1">
                            @foreach ($category->children as $child)
                                <div class="child-card" bis_skin_checked="1">
                                    @if ($child->image)
                                        <img src="{{ get_user_image($child->image) }} " alt="{{ $child->name }}"
                                            class="child-image">
                                    @else
                                        <div class="child-image bg-light d-flex align-items-center justify-content-center"
                                            bis_skin_checked="1">
                                            <i class="fas fa-folder fa-2x text-muted"></i>
                                        </div>
                                    @endif
                                    <div class="child-body" bis_skin_checked="1">
                                        <div class="child-title" bis_skin_checked="1">{{ $child->name }}</div>
                                        <div class="child-meta" bis_skin_checked="1">
                                            <i class="fas fa-sort-numeric-up"></i> الترتيب: {{ $child->order }}
                                            <span class="mx-1">•</span>
                                            <i class="fas fa-box"></i> {{ $child->products_count }} منتجات
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center"
                                            bis_skin_checked="1">
                                            <a href="{{ route('admin.categories.show', $child) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> عرض
                                            </a>
                                            @if ($child->status_id == 1)
                                                <span class="badge bg-success">نشط</span>
                                            @else
                                                <span class="badge bg-danger">غير نشط</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="text-center mt-3" bis_skin_checked="1">
                            <a href="{{ route('admin.categories.index', ['parent_id' => $category->id]) }}"
                                class="btn btn-outline-primary">
                                <i class="fas fa-list"></i> عرض جميع الأقسام الفرعية
                            </a>
                        </div>
                    @elseif($category->isParent())
                        <div class="empty-state" bis_skin_checked="1">
                            <i class="fas fa-sitemap"></i>
                            <h6>لا توجد أقسام فرعية</h6>
                            <p class="mb-3">يمكنك إضافة أقسام فرعية لهذا القسم</p>
                            <a href="{{ route('admin.categories.create', ['parent_id' => $category->id]) }}"
                                class="btn btn-primary">
                                <i class="fas fa-plus"></i> إضافة قسم فرعي
                            </a>
                        </div>
                    @else
                        <div class="text-center" bis_skin_checked="1">
                            <div class="mb-4" bis_skin_checked="1">
                                <i class="fas fa-level-up-alt fa-3x text-muted"></i>
                            </div>
                            <h6>قسم فرعي</h6>
                            <p class="text-muted mb-3">هذا قسم فرعي تابع لقسم رئيسي</p>
                            @if ($category->parent)
                                <div class="card mb-3" bis_skin_checked="1">
                                    <div class="card-body text-center" bis_skin_checked="1">
                                        <i class="fas fa-folder fa-2x text-primary mb-2"></i>
                                        <h6>{{ $category->parent->name }}</h6>
                                        <p class="text-muted mb-2">القسم الرئيسي</p>
                                        <a href="{{ route('admin.categories.show', $category->parent) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-external-link-alt"></i> الانتقال للقسم الرئيسي
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Recent Activity -->
                <div class="info-card" bis_skin_checked="1">
                    <h5><i class="fas fa-history"></i> النشاط الأخير</h5>
                    <div class="timeline" bis_skin_checked="1">
                        <div class="timeline-item" bis_skin_checked="1">
                            <div class="timeline-dot" bis_skin_checked="1"></div>
                            <div class="timeline-content" bis_skin_checked="1">
                                <div class="timeline-date" bis_skin_checked="1">آخر تحديث</div>
                                <div class="timeline-title" bis_skin_checked="1">تم تحديث القسم</div>
                                <p class="mb-0">{{ $category->updated_at->diffForHumans() }}</p>
                            </div>
                        </div>

                        <div class="timeline-item" bis_skin_checked="1">
                            <div class="timeline-dot" bis_skin_checked="1"></div>
                            <div class="timeline-content" bis_skin_checked="1">
                                <div class="timeline-date" bis_skin_checked="1">تاريخ الإنشاء</div>
                                <div class="timeline-title" bis_skin_checked="1">تم إنشاء القسم</div>
                                <p class="mb-0">{{ $category->created_at->diffForHumans() }}</p>
                            </div>
                        </div>

                        @if ($category->products_count > 0)
                            <div class="timeline-item" bis_skin_checked="1">
                                <div class="timeline-dot" bis_skin_checked="1"></div>
                                <div class="timeline-content" bis_skin_checked="1">
                                    <div class="timeline-date" bis_skin_checked="1">المحتوى</div>
                                    <div class="timeline-title" bis_skin_checked="1">عدد المنتجات</div>
                                    <p class="mb-0">{{ $category->products_count }} منتج في هذا القسم</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="info-card" bis_skin_checked="1">
                    <h5><i class="fas fa-bolt"></i> إجراءات سريعة</h5>
                    <div class="d-grid gap-2" bis_skin_checked="1">
                        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i> تعديل القسم
                        </a>

                        @if ($category->isParent())
                            <a href="{{ route('admin.categories.create', ['parent_id' => $category->id]) }}"
                                class="btn btn-success">
                                <i class="fas fa-plus-circle me-2"></i> إضافة قسم فرعي
                            </a>
                        @endif

                        <a href="{{ route('admin.products.create', ['category_id' => $category->id]) }}"
                            class="btn btn-warning">
                            <i class="fas fa-box me-2"></i> إضافة منتج
                        </a>

                        <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                            <i class="fas fa-trash me-2"></i> حذف القسم
                        </button>
                    </div>
                </div>

                <!-- QR Code (Optional) -->
                <div class="info-card" bis_skin_checked="1">
                    <h5><i class="fas fa-qrcode"></i> رمز QR</h5>
                    <div class="text-center" bis_skin_checked="1">
                        <div class="qr-code mb-3" id="qrCode" bis_skin_checked="1">
                            <!-- QR Code will be generated here -->
                            <div class="text-muted" bis_skin_checked="1">
                                <i class="fas fa-qrcode fa-3x"></i>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="generateQRCode()">
                            <i class="fas fa-sync-alt me-1"></i> إنشاء رمز QR
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons (Floating) -->
    <div class="action-buttons" bis_skin_checked="1">
        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-primary" title="تعديل">
            <i class="fas fa-edit"></i>
        </a>
        <a href="{{ route('admin.products.index', ['category_id' => $category->id]) }}" class="btn btn-info"
            title="المنتجات">
            <i class="fas fa-box"></i>
        </a>
        <button type="button" class="btn btn-success" onclick="copyCategoryLink()" title="نسخ الرابط">
            <i class="fas fa-link"></i>
        </button>
    </div>

    <!-- Delete Modal -->
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

                        @if ($category->products_count > 0)
                            <div class="alert alert-warning" bis_skin_checked="1">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                هذا القسم يحتوي على {{ $category->products_count }} منتج.
                            </div>
                        @endif

                        @if ($category->children_count > 0)
                            <div class="alert alert-danger" bis_skin_checked="1">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                هذا القسم يحتوي على {{ $category->children_count }} قسم فرعي.
                            </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" id="deleteForm">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i> حذف القسم
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Share Modal -->
    <div class="modal fade" id="shareModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">مشاركة القسم</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3" bis_skin_checked="1">
                        <label class="form-label">رابط القسم</label>
                        <div class="input-group" bis_skin_checked="1">
                            <input type="text" class="form-control" id="shareUrl" readonly
                                value="{{ url('/categories/' . $category->slug) }}">
                            <button class="btn btn-outline-secondary" type="button" onclick="copyShareUrl()">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3" bis_skin_checked="1">
                        <label class="form-label">مشاركة عبر</label>
                        <div class="d-flex justify-content-center gap-3" bis_skin_checked="1">
                            <button class="btn btn-primary" onclick="shareOnFacebook()">
                                <i class="fab fa-facebook-f"></i>
                            </button>
                            <button class="btn btn-info" onclick="shareOnTwitter()">
                                <i class="fab fa-twitter"></i>
                            </button>
                            <button class="btn btn-success" onclick="shareOnWhatsApp()">
                                <i class="fab fa-whatsapp"></i>
                            </button>
                            <button class="btn btn-danger" onclick="shareViaEmail()">
                                <i class="fas fa-envelope"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
    <script>
        // Initialize fancybox for image gallery
        $(document).ready(function() {
            $('[data-fancybox="gallery"]').fancybox({
                buttons: [
                    "zoom",
                    "share",
                    "slideShow",
                    "fullScreen",
                    "download",
                    "thumbs",
                    "close"
                ]
            });
        });

        // Delete confirmation
        function confirmDelete() {
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }

        // Share category
        function shareCategory() {
            const modal = new bootstrap.Modal(document.getElementById('shareModal'));
            modal.show();
        }

        // Copy share URL
        function copyShareUrl() {
            const urlInput = document.getElementById('shareUrl');
            urlInput.select();
            urlInput.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(urlInput.value).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'تم النسخ!',
                    text: 'تم نسخ الرابط إلى الحافظة',
                    timer: 1500,
                    showConfirmButton: false
                });
            });
        }

        // Share on social media
        function shareOnFacebook() {
            const url = encodeURIComponent('{{ url('/categories/' . $category->slug) }}');
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank');
        }

        function shareOnTwitter() {
            const url = encodeURIComponent('{{ url('/categories/' . $category->slug) }}');
            const text = encodeURIComponent('{{ $category->name }}');
            window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank');
        }

        function shareOnWhatsApp() {
            const url = encodeURIComponent('{{ url('/categories/' . $category->slug) }}');
            const text = encodeURIComponent('{{ $category->name }}');
            window.open(`https://wa.me/?text=${text}%20${url}`, '_blank');
        }

        function shareViaEmail() {
            const url = '{{ url('/categories/' . $category->slug) }}';
            const subject = encodeURIComponent('{{ $category->name }}');
            const body = encodeURIComponent(`تفضل بزيارة القسم: ${url}`);
            window.location.href = `mailto:?subject=${subject}&body=${body}`;
        }

        // Copy category link
        function copyCategoryLink() {
            const url = '{{ url('/categories/' . $category->slug) }}';
            navigator.clipboard.writeText(url).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'تم النسخ!',
                    text: 'تم نسخ رابط القسم إلى الحافظة',
                    timer: 1500,
                    showConfirmButton: false
                });
            });
        }

        // Generate QR Code
        function generateQRCode() {
            const qrCodeDiv = document.getElementById('qrCode');
            qrCodeDiv.innerHTML = '';

            const url = '{{ url('/categories/' . $category->slug) }}';

            // Generate QR code
            QRCode.toCanvas(url, {
                width: 150,
                margin: 1,
                color: {
                    dark: '#000000',
                    light: '#ffffff'
                }
            }, function(error, canvas) {
                if (error) {
                    console.error(error);
                    qrCodeDiv.innerHTML = '<div class="text-danger">خطأ في إنشاء رمز QR</div>';
                    return;
                }

                qrCodeDiv.appendChild(canvas);

                // Add download button
                const downloadBtn = document.createElement('button');
                downloadBtn.className = 'btn btn-sm btn-outline-primary mt-2';
                downloadBtn.innerHTML = '<i class="fas fa-download me-1"></i> تحميل';
                downloadBtn.onclick = function() {
                    const link = document.createElement('a');
                    link.download = 'qr-code-{{ $category->slug }}.png';
                    link.href = canvas.toDataURL('image/png');
                    link.click();
                };
                qrCodeDiv.appendChild(downloadBtn);
            });
        }

        // Print category details

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Generate QR code automatically
            generateQRCode();

            // Add print functionality to print button
            document.querySelector('[onclick="window.print()"]').addEventListener('click', function(e) {
                e.preventDefault();
                printCategory();
            });
        });
    </script>
@endsection
