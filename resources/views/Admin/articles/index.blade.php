@extends('Admin.layout.master')

@section('title', 'إدارة المقالات')

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
        }

        .badge-status {
            padding: 6px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 13px;
        }

        .status-active {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }

        .status-inactive {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
            border: 1px solid rgba(220, 53, 69, 0.3);
        }

        .status-featured {
            background: linear-gradient(135deg, rgba(255, 193, 7, 0.2) 0%, rgba(253, 126, 20, 0.2) 100%);
            color: #fd7e14;
            border: 1px solid rgba(253, 126, 20, 0.3);
        }

        .status-published {
            background: rgba(32, 201, 151, 0.2);
            color: #20c997;
            border: 1px solid rgba(32, 201, 151, 0.3);
        }

        .status-draft {
            background: rgba(108, 117, 125, 0.2);
            color: #6c757d;
            border: 1px solid rgba(108, 117, 125, 0.3);
        }

        .stats-card {
            background: var(--dark-card);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            border-top: 4px solid var(--primary-color);
            transition: transform 0.3s ease;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
        }

        .stats-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 15px;
        }

        .icon-total {
            background: var(--primary-gradient);
            color: white;
        }

        .icon-active {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }

        .icon-featured {
            background: linear-gradient(135deg, rgba(255, 193, 7, 0.2) 0%, rgba(253, 126, 20, 0.2) 100%);
            color: #fd7e14;
            border: 1px solid rgba(253, 126, 20, 0.3);
        }

        .icon-views {
            background: rgba(13, 202, 240, 0.2);
            color: #0dcaf0;
            border: 1px solid rgba(13, 202, 240, 0.3);
        }

        .stats-number {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
            color: #fff;
        }

        .stats-label {
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
        }

        .filter-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .filter-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }

        .search-box {
            position: relative;
        }

        .search-box input {
            padding-right: 40px;
            border-radius: 25px;
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .search-box input:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--primary-color);
            color: #fff;
            box-shadow: 0 0 0 0.25rem rgba(105, 108, 255, 0.25);
        }

        .search-box .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.5);
        }

        .article-item {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .article-item:hover {
            transform: translateX(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            background: rgba(105, 108, 255, 0.1);
            border-color: var(--primary-color);
        }

        .article-header-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .article-title {
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
        }

        .article-date {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.7);
        }

        .article-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .detail-label {
            font-weight: 600;
            color: rgba(255, 255, 255, 0.8);
            min-width: 80px;
        }

        .detail-value {
            color: rgba(255, 255, 255, 0.9);
        }

        .article-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .empty-state {
            text-align: center;
            padding: 50px 20px;
        }

        .empty-state-icon {
            font-size: 60px;
            color: rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }

        .empty-state-text {
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 20px;
        }

        .status-filter {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .status-filter-btn {
            padding: 8px 20px;
            border-radius: 25px;
            background: rgba(255, 255, 255, 0.05);
            color: rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.1);
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .status-filter-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .status-filter-btn.active {
            background: var(--primary-gradient);
            color: white;
            border-color: var(--primary-color);
        }

        .sort-dropdown {
            position: relative;
            display: inline-block;
        }

        .sort-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.8);
            padding: 8px 15px;
            border-radius: 25px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sort-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .sort-dropdown-content {
            display: none;
            position: absolute;
            background: var(--dark-card);
            min-width: 200px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            border-radius: 10px;
            z-index: 1;
            padding: 10px 0;
            margin-top: 5px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sort-dropdown:hover .sort-dropdown-content {
            display: block;
        }

        .sort-item {
            padding: 10px 20px;
            cursor: pointer;
            transition: background 0.3s;
            color: rgba(255, 255, 255, 0.8);
        }

        .sort-item:hover {
            background: rgba(105, 108, 255, 0.1);
            color: #fff;
        }

        .sort-item.active {
            background: var(--primary-gradient);
            color: white;
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

        .article-image {
            width: 100px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }

        .featured-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
            color: white;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 11px;
        }

        .bulk-actions {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-bottom: 20px;
        }

        .form-check {
            margin: 0;
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        @media (max-width: 768px) {
            .article-header-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .article-details {
                grid-template-columns: 1fr;
            }

            .article-actions {
                flex-wrap: wrap;
            }
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
                <li class="breadcrumb-item active">المقالات</li>
            </ol>
        </nav>

        <!-- الإحصائيات -->
        <div class="row mb-4" bis_skin_checked="1">
            <div class="col-lg-3 col-md-6 mb-4" bis_skin_checked="1">
                <div class="stats-card" bis_skin_checked="1">
                    <div class="stats-icon icon-total" bis_skin_checked="1">
                        <i class="fas fa-newspaper"></i>
                    </div>
                    <div class="stats-number" bis_skin_checked="1">
                        {{ number_format($stats['total']) }}
                    </div>
                    <div class="stats-label" bis_skin_checked="1">إجمالي المقالات</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4" bis_skin_checked="1">
                <div class="stats-card" bis_skin_checked="1">
                    <div class="stats-icon icon-active" bis_skin_checked="1">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stats-number" bis_skin_checked="1">
                        {{ number_format($stats['active']) }}
                    </div>
                    <div class="stats-label" bis_skin_checked="1">مقالات نشطة</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4" bis_skin_checked="1">
                <div class="stats-card" bis_skin_checked="1">
                    <div class="stats-icon icon-featured" bis_skin_checked="1">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stats-number" bis_skin_checked="1">
                        {{ number_format($stats['featured']) }}
                    </div>
                    <div class="stats-label" bis_skin_checked="1">مقالات مميزة</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4" bis_skin_checked="1">
                <div class="stats-card" bis_skin_checked="1">
                    <div class="stats-icon icon-views" bis_skin_checked="1">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="stats-number" bis_skin_checked="1">
                        {{ number_format($stats['total_views']) }}
                    </div>
                    <div class="stats-label" bis_skin_checked="1">إجمالي المشاهدات</div>
                </div>
            </div>
        </div>

        <!-- فلترة حسب الحالة -->
        <div class="status-filter" bis_skin_checked="1">
            <button class="status-filter-btn {{ !request('status') ? 'active' : '' }}" onclick="filterByStatus('all')">
                جميع المقالات
            </button>
            <button class="status-filter-btn {{ request('status') == 'active' ? 'active' : '' }}"
                onclick="filterByStatus('active')">
                <i class="fas fa-check-circle me-2"></i>نشطة
            </button>
            <button class="status-filter-btn {{ request('status') == 'inactive' ? 'active' : '' }}"
                onclick="filterByStatus('inactive')">
                <i class="fas fa-times-circle me-2"></i>غير نشطة
            </button>
            <button class="status-filter-btn {{ request('status') == 'featured' ? 'active' : '' }}"
                onclick="filterByStatus('featured')">
                <i class="fas fa-star me-2"></i>مميزة
            </button>
            <button class="status-filter-btn {{ request('status') == 'published' ? 'active' : '' }}"
                onclick="filterByStatus('published')">
                <i class="fas fa-globe me-2"></i>منشورة
            </button>
            <button class="status-filter-btn {{ request('status') == 'draft' ? 'active' : '' }}"
                onclick="filterByStatus('draft')">
                <i class="fas fa-pen me-2"></i>مسودات
            </button>
        </div>

        <!-- فلترة متقدمة -->
        <div class="filter-card" bis_skin_checked="1">
            <h6 class="mb-3"><i class="fas fa-filter me-2"></i>فلترة متقدمة</h6>

            <form id="bulkForm" method="POST" action="{{ route('admin.articles.bulk-actions') }}">
                @csrf
                <div class="bulk-actions mb-3" bis_skin_checked="1">
                    <select name="action" class="form-select" style="width: 200px;">
                        <option value="">اختر إجراء</option>
                        <option value="activate">تفعيل المحدد</option>
                        <option value="deactivate">تعطيل المحدد</option>
                        <option value="feature">تمييز المحدد</option>
                        <option value="unfeature">إلغاء التمييز</option>
                        <option value="delete">حذف المحدد</option>
                    </select>
                    <button type="submit" class="btn btn-primary" onclick="return confirmBulkAction()">
                        <i class="fas fa-play me-2"></i>تطبيق
                    </button>
                </div>

                <div class="filter-row" bis_skin_checked="1">
                    <div class="search-box" bis_skin_checked="1">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" class="form-control" placeholder="بحث في المقالات..." id="searchInput"
                            value="{{ request('search') }}">
                    </div>

                    <div class="sort-dropdown" bis_skin_checked="1">
                        <button class="sort-btn">
                            <i class="fas fa-sort-amount-down"></i>
                            الترتيب حسب
                        </button>
                        <div class="sort-dropdown-content" bis_skin_checked="1">
                            <div class="sort-item {{ request('sort_by') == 'published_at' && request('sort_direction') == 'desc' ? 'active' : '' }}"
                                onclick="sortBy('published_at', 'desc')">
                                الأحدث أولاً
                            </div>
                            <div class="sort-item {{ request('sort_by') == 'published_at' && request('sort_direction') == 'asc' ? 'active' : '' }}"
                                onclick="sortBy('published_at', 'asc')">
                                الأقدم أولاً
                            </div>
                            <div class="sort-item {{ request('sort_by') == 'views_count' && request('sort_direction') == 'desc' ? 'active' : '' }}"
                                onclick="sortBy('views_count', 'desc')">
                                الأكثر مشاهدة
                            </div>
                            <div class="sort-item {{ request('sort_by') == 'views_count' && request('sort_direction') == 'asc' ? 'active' : '' }}"
                                onclick="sortBy('views_count', 'asc')">
                                الأقل مشاهدة
                            </div>
                            <div class="sort-item {{ request('sort_by') == 'reading_time' && request('sort_direction') == 'desc' ? 'active' : '' }}"
                                onclick="sortBy('reading_time', 'desc')">
                                الأطول قراءة
                            </div>
                            <div class="sort-item {{ request('sort_by') == 'reading_time' && request('sort_direction') == 'asc' ? 'active' : '' }}"
                                onclick="sortBy('reading_time', 'asc')">
                                الأقصر قراءة
                            </div>
                        </div>
                    </div>

                    <div class="input-group" bis_skin_checked="1">
                        <input type="date" class="form-control" id="dateFrom" placeholder="من تاريخ"
                            value="{{ request('date_from') }}">
                        <span class="input-group-text">إلى</span>
                        <input type="date" class="form-control" id="dateTo" placeholder="إلى تاريخ"
                            value="{{ request('date_to') }}">
                    </div>
                </div>

                <div class="filter-row" bis_skin_checked="1">
                    <select class="form-select" id="categoryFilter">
                        <option value="">جميع التصنيفات</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>

                    <select class="form-select" id="authorFilter">
                        <option value="">جميع الكتاب</option>
                        @foreach ($authors as $author)
                            <option value="{{ $author->id }}"
                                {{ request('author_id') == $author->id ? 'selected' : '' }}>
                                {{ $author->name }}
                            </option>
                        @endforeach
                    </select>

                    <div class="input-group" bis_skin_checked="1">
                        <input type="number" class="form-control" id="viewsFrom" placeholder="من المشاهدات"
                            value="{{ request('views_from') }}">
                        <span class="input-group-text">إلى</span>
                        <input type="number" class="form-control" id="viewsTo" placeholder="إلى المشاهدات"
                            value="{{ request('views_to') }}">
                    </div>
                </div>

                <div class="d-flex gap-2 mt-3" bis_skin_checked="1">
                    <button class="btn btn-primary" type="button" onclick="applyFilters()">
                        <i class="fas fa-filter me-2"></i>تطبيق الفلاتر
                    </button>
                    <button class="btn btn-outline-secondary" type="button" onclick="resetFilters()">
                        <i class="fas fa-redo me-2"></i>إعادة تعيين
                    </button>
                </div>
            </form>
        </div>

        <!-- قائمة المقالات -->
        <div class="row" bis_skin_checked="1">
            <div class="col-12" bis_skin_checked="1">
                <div class="article-card" bis_skin_checked="1">
                    <div class="article-header" bis_skin_checked="1">
                        <div class="d-flex justify-content-between align-items-center" bis_skin_checked="1">
                            <div bis_skin_checked="1">
                                <h5 class="mb-0">قائمة المقالات</h5>
                                <small class="opacity-75">إدارة جميع مقالات الموقع</small>
                            </div>
                            <div class="d-flex gap-3" bis_skin_checked="1">
                                <a href="{{ route('admin.articles.statistics') }}" class="btn btn-light">
                                    <i class="fas fa-chart-bar me-2"></i>الإحصائيات
                                </a>
                                <a href="{{ route('admin.articles.create') }}" class="btn btn-light">
                                    <i class="fas fa-plus me-2"></i>مقال جديد
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body" bis_skin_checked="1">
                        @if ($articles->isEmpty())
                            <div class="empty-state" bis_skin_checked="1">
                                <div class="empty-state-icon" bis_skin_checked="1">
                                    <i class="fas fa-newspaper"></i>
                                </div>
                                <h5 class="empty-state-text">لا توجد مقالات</h5>
                                <p class="text-muted">لم يتم إنشاء أي مقالات حتى الآن</p>
                                <a href="{{ route('admin.articles.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>إنشاء مقال جديد
                                </a>
                            </div>
                        @else
                            <div class="table-responsive" bis_skin_checked="1">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th width="50">
                                                <input type="checkbox" id="selectAll">
                                            </th>
                                            <th>المقال</th>
                                            <th>التصنيف</th>
                                            <th>الكاتب</th>
                                            <th>المشاهدات</th>
                                            <th>الحالة</th>
                                            <th>التاريخ</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($articles as $article)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="article-checkbox" name="ids[]"
                                                        value="{{ $article->id }}">
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-3" bis_skin_checked="1">
                                                        @if ($article->image)
                                                            <img src="{{ Storage::url($article->image) }}"
                                                                alt="{{ $article->image_alt }}" class="article-image">
                                                        @endif
                                                        <div bis_skin_checked="1">
                                                            <strong
                                                                class="d-block">{{ Str::limit($article->title, 40) }}</strong>
                                                            <small class="text-muted">
                                                                <i class="fas fa-clock me-1"></i>
                                                                {{ $article->reading_time }} دقائق قراءة
                                                            </small>
                                                            @if ($article->is_featured)
                                                                <span class="badge bg-warning text-dark ms-2">
                                                                    <i class="fas fa-star"></i> مميز
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if ($article->category)
                                                        <span class="badge bg-info">
                                                            {{ $article->category->name }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">بدون تصنيف</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $article->author->name ?? 'غير معروف' }}
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2" bis_skin_checked="1">
                                                        <i class="fas fa-eye text-info"></i>
                                                        {{ number_format($article->views_count) }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column gap-1" bis_skin_checked="1">
                                                        <span
                                                            class="badge-status status-{{ $article->is_active ? 'active' : 'inactive' }}">
                                                            {{ $article->is_active ? 'نشط' : 'غير نشط' }}
                                                        </span>
                                                        <small class="text-muted">
                                                            @if ($article->published_at && $article->published_at <= now())
                                                                منشور
                                                            @else
                                                                مسودة
                                                            @endif
                                                        </small>
                                                    </div>
                                                </td>
                                                <td>
                                                    {{ $article->published_at?->translatedFormat('d M Y') ?? 'غير محدد' }}
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ $article->created_at->diffForHumans() }}
                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="article-actions" bis_skin_checked="1">
                                                        <a href="{{ route('admin.articles.show', $article->id) }}"
                                                            class="btn btn-sm btn-info" title="عرض">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('admin.articles.edit', $article) }}"
                                                            class="btn btn-sm btn-warning" title="تعديل">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="{{ route('articles.show', $article->slug) }}"
                                                            class="btn btn-sm btn-success" target="_blank"
                                                            title="عرض في الموقع">
                                                            <i class="fas fa-external-link-alt"></i>
                                                        </a>
                                                        <button type="button"
                                                            class="btn btn-sm {{ $article->is_active ? 'btn-secondary' : 'btn-success' }} toggle-status-btn"
                                                            data-id="{{ $article->id }}"
                                                            title="{{ $article->is_active ? 'تعطيل' : 'تفعيل' }}">
                                                            <i class="fas fa-power-off"></i>
                                                        </button>
                                                        <button type="button"
                                                            class="btn btn-sm {{ $article->is_featured ? 'btn-warning' : 'btn-outline-warning' }} toggle-featured-btn"
                                                            data-id="{{ $article->id }}"
                                                            title="{{ $article->is_featured ? 'إلغاء التمييز' : 'تمييز' }}">
                                                            <i class="fas fa-star"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-danger delete-btn"
                                                            data-id="{{ $article->id }}"
                                                            data-title="{{ $article->title }}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if ($articles->hasPages())
                                <div class="m-3">
                                    <nav>
                                        <ul class="pagination">
                                            {{-- Previous Page Link --}}
                                            @if ($articles->onFirstPage())
                                                <li class="page-item disabled" aria-disabled="true">
                                                    <span class="page-link waves-effect" aria-hidden="true">‹</span>
                                                </li>
                                            @else
                                                <li class="page-item">
                                                    <a class="page-link waves-effect"
                                                        href="{{ $articles->previousPageUrl() }}" rel="prev">‹</a>
                                                </li>
                                            @endif

                                            {{-- Pagination Elements --}}
                                            @foreach ($articles->links()->elements[0] as $page => $url)
                                                @if ($page == $articles->currentPage())
                                                    <li class="page-item active" aria-current="page">
                                                        <span class="page-link waves-effect">{{ $page }}</span>
                                                    </li>
                                                @else
                                                    <li class="page-item">
                                                        <a class="page-link waves-effect"
                                                            href="{{ $url }}">{{ $page }}</a>
                                                    </li>
                                                @endif
                                            @endforeach

                                            {{-- Next Page Link --}}
                                            @if ($articles->hasMorePages())
                                                <li class="page-item">
                                                    <a class="page-link waves-effect"
                                                        href="{{ $articles->nextPageUrl() }}" rel="next">›</a>
                                                </li>
                                            @else
                                                <li class="page-item disabled" aria-disabled="true">
                                                    <span class="page-link waves-effect" aria-hidden="true">›</span>
                                                </li>
                                            @endif
                                        </ul>
                                    </nav>
                                </div>
                            @endif
                        @endif
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
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/lang/summernote-ar-AR.min.js"></script>
    <script>
        $(document).ready(function() {
            // اختيار الكل
            $('#selectAll').on('change', function() {
                $('.article-checkbox').prop('checked', this.checked);
            });

            // البحث مع تأخير
            let searchTimeout;
            $('#searchInput').on('keyup', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    applyFilters();
                }, 500);
            });

            // تبديل الحالة
            $('.toggle-status-btn').on('click', function() {
                const articleId = $(this).data('id');
                const btn = $(this);

                $.ajax({
                    url: "{{ route('admin.articles.toggle-status', '') }}/" + articleId,
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        _method: 'PATCH'
                    },
                    success: function(response) {
                        if (response.success) {
                            btn.toggleClass('btn-secondary btn-success');
                            btn.find('i').toggleClass('fa-toggle-on fa-toggle-off');

                            // تحديث البادج
                            const statusBadge = btn.closest('tr').find('.badge-status');
                            if (response.is_active) {
                                statusBadge.removeClass('status-inactive').addClass(
                                    'status-active').text('نشط');
                            } else {
                                statusBadge.removeClass('status-active').addClass(
                                    'status-inactive').text('غير نشط');
                            }

                            Swal.fire({
                                icon: 'success',
                                title: 'تم التغيير',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: 'حدث خطأ أثناء التحديث',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                });
            });

            // تبديل التمييز
            $('.toggle-featured-btn').on('click', function() {
                const articleId = $(this).data('id');
                const btn = $(this);

                $.ajax({
                    url: "{{ route('admin.articles.toggle-featured', '') }}/" + articleId,
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        _method: 'PATCH'
                    },
                    success: function(response) {
                        if (response.success) {
                            btn.toggleClass('btn-warning btn-outline-warning');

                            // تحديث البادج
                            const featuredBadge = btn.closest('tr').find('.badge.bg-warning');
                            if (response.is_featured) {
                                if (featuredBadge.length === 0) {
                                    btn.closest('tr').find('td:nth-child(2)').append(
                                        '<span class="badge bg-warning text-dark ms-2"><i class="fas fa-star"></i> مميز</span>'
                                    );
                                }
                            } else {
                                featuredBadge.remove();
                            }

                            Swal.fire({
                                icon: 'success',
                                title: 'تم التغيير',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: 'حدث خطأ أثناء التحديث',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                });
            });

            // حذف المقال
            $('.delete-btn').on('click', function() {
                const articleId = $(this).data('id');
                const articleTitle = $(this).data('title');

                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: `سيتم حذف المقال "${articleTitle}" نهائياً`,
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
                            url: "{{ route('admin.articles.destroy', '') }}/" + articleId,
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
                                    location.reload();
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

        function confirmBulkAction() {
            const action = document.querySelector('select[name="action"]').value;
            const checkedCount = document.querySelectorAll('.article-checkbox:checked').length;

            if (!action) {
                Swal.fire({
                    icon: 'warning',
                    title: 'تحذير',
                    text: 'الرجاء اختيار إجراء أولاً',
                    timer: 2000,
                    showConfirmButton: false
                });
                return false;
            }

            if (checkedCount === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'تحذير',
                    text: 'الرجاء اختيار مقالات أولاً',
                    timer: 2000,
                    showConfirmButton: false
                });
                return false;
            }

            return true;
        }

        function filterByStatus(status) {
            if (status === 'all') {
                updateUrl({
                    status: null
                });
            } else {
                updateUrl({
                    status: status
                });
            }
        }

        function sortBy(sortBy, sortDirection) {
            updateUrl({
                sort_by: sortBy,
                sort_direction: sortDirection
            });
        }

        function applyFilters() {
            const params = {
                search: $('#searchInput').val(),
                category_id: $('#categoryFilter').val(),
                author_id: $('#authorFilter').val(),
                date_from: $('#dateFrom').val(),
                date_to: $('#dateTo').val(),
                views_from: $('#viewsFrom').val(),
                views_to: $('#viewsTo').val()
            };

            updateUrl(params);
        }

        function resetFilters() {
            // مسح جميع الحقول
            $('#searchInput').val('');
            $('#categoryFilter').val('');
            $('#authorFilter').val('');
            $('#dateFrom').val('');
            $('#dateTo').val('');
            $('#viewsFrom').val('');
            $('#viewsTo').val('');

            // إعادة تحميل الصفحة بدون فلتر
            window.location.href = "{{ route('admin.articles.index') }}";
        }

        function updateUrl(params) {
            const url = new URL(window.location.href);
            const searchParams = new URLSearchParams(url.search);

            // تحديث جميع الباراميترات
            Object.keys(params).forEach(key => {
                if (params[key] === null || params[key] === '') {
                    searchParams.delete(key);
                } else {
                    searchParams.set(key, params[key]);
                }
            });

            // إعادة التوجيه إلى الصفحة الأولى مع الباراميترات الجديدة
            searchParams.set('page', '1');
            url.search = searchParams.toString();
            window.location.href = url.toString();
        }
    </script>
@endsection
