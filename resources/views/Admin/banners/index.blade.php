@extends('Admin.layout.master')

@section('title', 'إدارة البانرات')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sortable/0.8.0/css/sortable-theme-bootstrap.min.css">
    <style>
        body {
            font-family: "Cairo", sans-serif !important;
        }

        .banner-preview {
            width: 100px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #f8f9fa;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .banner-preview-placeholder {
            width: 100px;
            height: 60px;
            border-radius: 8px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            border: 2px solid #f8f9fa;
        }

        .badge-custom {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-slider {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .badge-grid {
            background: #e7f5ff;
            color: #0c63e4;
        }

        .badge-static {
            background: #d4edda;
            color: #155724;
        }

        .badge-category {
            background: #fff3cd;
            color: #856404;
        }

        .badge-active {
            background: #d4edda;
            color: #155724;
        }

        .badge-inactive {
            background: #f8d7da;
            color: #721c24;
        }

        .badge-main {
            background: #cfe2ff;
            color: #084298;
        }

        .badge-cat {
            background: #d1e7dd;
            color: #0f5132;
        }

        .table-card {
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .table-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 25px;
        }

        .search-box {
            position: relative;
        }

        .search-box input {
            padding-right: 40px;
            border-radius: 25px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .search-box input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .search-box .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: white;
        }

        .filter-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .filter-tab {
            padding: 8px 20px;
            border-radius: 25px;
            background: #f8f9fa;
            color: #6c757d;
            border: 1px solid #dee2e6;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
            font-weight: 500;
        }

        .filter-tab:hover {
            background: #e9ecef;
        }

        .filter-tab.active {
            background: #696cff;
            color: white;
            border-color: #696cff;
        }

        .stats-card {
            background: #242f3b;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border-top: 4px solid #696cff;
            transition: transform 0.3s ease;
            margin-bottom: 20px;
        }

        .stats-card:hover {
            transform: translateY(-5px);
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

        .icon-banner {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .icon-slider {
            background: #e7f5ff;
            color: #0c63e4;
        }

        .icon-grid {
            background: #f8f9fa;
            color: #495057;
        }

        .icon-active {
            background: #d4edda;
            color: #155724;
        }

        .stats-number {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stats-label {
            color: #6c757d;
            font-size: 14px;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn-action {
            width: 32px;
            height: 32px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
        }

        .banner-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .banner-details h6 {
            margin-bottom: 5px;
            font-weight: 600;
        }

        .banner-details p {
            margin: 0;
            font-size: 13px;
            color: #6c757d;
        }

        .type-icons {
            display: flex;
            gap: 5px;
            margin-top: 5px;
        }

        .type-icon {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            color: white;
        }

        .icon-slider-type {
            background: #667eea;
        }

        .icon-grid-type {
            background: #0c63e4;
        }

        .icon-static-type {
            background: #28a745;
        }

        .toggle-switch {
            position: relative;
            width: 50px;
            height: 26px;
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
            height: 18px;
            width: 18px;
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
            transform: translateX(24px);
        }

        .empty-state {
            text-align: center;
            padding: 50px 20px;
        }

        .empty-state-icon {
            font-size: 60px;
            color: #dee2e6;
            margin-bottom: 20px;
        }

        .empty-state-text {
            color: #6c757d;
            margin-bottom: 20px;
        }

        .sort-dropdown {
            position: relative;
            display: inline-block;
        }

        .sort-btn {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 8px 15px;
            border-radius: 25px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sort-dropdown-content {
            display: none;
            position: absolute;
            background: white;
            min-width: 200px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            z-index: 1;
            padding: 10px 0;
            margin-top: 5px;
        }

        .sort-dropdown:hover .sort-dropdown-content {
            display: block;
        }

        .sort-item {
            padding: 10px 20px;
            cursor: pointer;
            transition: background 0.3s;
            color: #495057;
        }

        .sort-item:hover {
            background: #f8f9fa;
        }

        .sort-item.active {
            background: #696cff;
            color: white;
        }

        .category-badge {
            display: inline-block;
            padding: 4px 12px;
            background: #e7f5ff;
            color: #0c63e4;
            border-radius: 15px;
            font-size: 12px;
            margin-top: 5px;
        }

        .items-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            background: #696cff;
            color: white;
            border-radius: 50%;
            font-size: 12px;
            font-weight: 600;
        }

        .sortable-ghost {
            opacity: 0.4;
            background: #f8f9fa;
        }

        .sortable-drag {
            background: white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .order-handle {
            cursor: move;
            color: #6c757d;
            padding: 0 10px;
        }

        @media (max-width: 768px) {
            .filter-tabs {
                justify-content: center;
            }

            .banner-info {
                flex-direction: column;
                text-align: center;
                gap: 10px;
            }

            .banner-details {
                text-align: center;
            }

            .action-buttons {
                flex-wrap: wrap;
                justify-content: center;
            }
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
                <li class="breadcrumb-item active">البنرات</li>
            </ol>
        </nav>

        <!-- الإحصائيات -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stats-card">
                    <div class="stats-icon icon-banner">
                        <i class="fas fa-images"></i>
                    </div>
                    <div class="stats-number">
                        {{ $banners->total() }}
                    </div>
                    <div class="stats-label">إجمالي البنرات</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                @php
                    $activeBanners = $banners->where('is_active', true)->count();
                @endphp
                <div class="stats-card">
                    <div class="stats-icon icon-active">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stats-number">
                        {{ $activeBanners }}
                    </div>
                    <div class="stats-label">البنرات النشطة</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                @php
                    $sliderBanners = $banners->where('type.name', 'slider')->count();
                @endphp
                <div class="stats-card">
                    <div class="stats-icon icon-slider">
                        <i class="fas fa-sliders-h"></i>
                    </div>
                    <div class="stats-number">
                        {{ $sliderBanners }}
                    </div>
                    <div class="stats-label">بنرات السلايدر</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                @php
                    $gridBanners = $banners->where('type.name', 'grid')->count();
                @endphp
                <div class="stats-card">
                    <div class="stats-icon icon-grid">
                        <i class="fas fa-th-large"></i>
                    </div>
                    <div class="stats-number">
                        {{ $gridBanners }}
                    </div>
                    <div class="stats-label">بنرات الشبكة</div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="table-card">
                    <div class="table-header">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h5 class="mb-0">إدارة البنرات</h5>
                                <small class="opacity-75">عرض وإدارة جميع البنرات في النظام</small>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex justify-content-end align-items-center gap-3">
                                    <div class="search-box">
                                        <i class="fas fa-search search-icon"></i>
                                        <input type="text" class="form-control" placeholder="بحث بالعنوان، النوع..."
                                            id="searchInput" value="{{ request('search') }}" style="width: 250px;">
                                    </div>
                                    <div class="sort-dropdown">
                                        <button class="sort-btn">
                                            <i class="fas fa-sort-amount-down"></i>
                                            الترتيب
                                        </button>
                                        <div class="sort-dropdown-content">
                                            <div class="sort-item {{ request('sort_by') == 'section_order' && request('sort_direction') == 'asc' ? 'active' : '' }}"
                                                onclick="sortBy('section_order', 'asc')">
                                                الترتيب من الأقل للأعلى
                                            </div>
                                            <div class="sort-item {{ request('sort_by') == 'section_order' && request('sort_direction') == 'desc' ? 'active' : '' }}"
                                                onclick="sortBy('section_order', 'desc')">
                                                الترتيب من الأعلى للأقل
                                            </div>
                                            <div class="sort-item {{ request('sort_by') == 'created_at' && request('sort_direction') == 'desc' ? 'active' : '' }}"
                                                onclick="sortBy('created_at', 'desc')">
                                                الأحدث أولاً
                                            </div>
                                            <div class="sort-item {{ request('sort_by') == 'created_at' && request('sort_direction') == 'asc' ? 'active' : '' }}"
                                                onclick="sortBy('created_at', 'asc')">
                                                الأقدم أولاً
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{ route('admin.banners.create') }}" class="btn btn-light">
                                        <i class="fas fa-plus me-2"></i>إضافة بانر
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- فلاتر التبويب -->
                    <div class="px-4 pt-3">
                        <div class="filter-tabs">
                            <div class="filter-tab {{ !request('type') && !request('status') ? 'active' : '' }}"
                                onclick="filterBy('all')">
                                جميع البنرات
                            </div>
                            <div class="filter-tab {{ request('type') == 'slider' ? 'active' : '' }}"
                                onclick="filterByType('slider')">
                                <i class="fas fa-sliders-h me-2"></i>السلايدر
                            </div>
                            <div class="filter-tab {{ request('type') == 'grid' ? 'active' : '' }}"
                                onclick="filterByType('grid')">
                                <i class="fas fa-th-large me-2"></i>الشبكة
                            </div>
                            <div class="filter-tab {{ request('type') == 'static' ? 'active' : '' }}"
                                onclick="filterByType('static')">
                                <i class="fas fa-image me-2"></i>البنرات الثابتة
                            </div>
                            <div class="filter-tab {{ request('status') == 'active' ? 'active' : '' }}"
                                onclick="filterByStatus('active')">
                                <i class="fas fa-check-circle me-2"></i>النشطة فقط
                            </div>
                            <div class="filter-tab {{ request('status') == 'inactive' ? 'active' : '' }}"
                                onclick="filterByStatus('inactive')">
                                <i class="fas fa-times-circle me-2"></i>غير النشطة
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="sortable-table">
                            <thead>
                                <tr>
                                    <th width="50">الترتيب</th>
                                    <th width="80">#</th>
                                    <th>البانر</th>
                                    <th>النوع والتكوين</th>
                                    <th>الحالة</th>
                                    <th>الفترة</th>
                                    <th width="150">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="bannersTable">
                                @forelse($banners as $banner)
                                    <tr data-id="{{ $banner->id }}" class="sortable-row">
                                        <td>
                                            <div class="order-handle">
                                                <i class="fas fa-arrows-alt"></i>
                                            </div>
                                        </td>
                                        <td>{{ $loop->iteration + $banners->perPage() * ($banners->currentPage() - 1) }}
                                        </td>
                                        <td>
                                            <div class="banner-info">
                                                @if ($banner->items->count() > 0)
                                                    @php
                                                        $firstItem = $banner->items->sortBy('item_order')->first();
                                                    @endphp
                                                    @if ($firstItem && $firstItem->image_url)
                                                        <img src="{{ get_user_image($firstItem->image_url) }}"
                                                            alt="{{ $firstItem->image_alt }}" class="banner-preview">
                                                    @else
                                                        <div class="banner-preview-placeholder">
                                                            <i class="fas fa-image"></i>
                                                        </div>
                                                    @endif
                                                @else
                                                    <div class="banner-preview-placeholder">
                                                        <i class="fas fa-image"></i>
                                                    </div>
                                                @endif
                                                <div class="banner-details">
                                                    <h6 class="mb-1">{{ $banner->title }}</h6>
                                                    <p class="mb-0">ID: #{{ $banner->id }}</p>
                                                    <div class="d-flex align-items-center gap-2 mt-1">
                                                        <span
                                                            class="items-count">{{ $banner->items_count ?? $banner->items->count() }}</span>
                                                        <small class="text-muted">عنصر</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($banner->type)
                                                <span
                                                    class="badge-custom 
                                                {{ $banner->type->name == 'slider' ? 'badge-slider' : '' }}
                                                {{ $banner->type->name == 'grid' ? 'badge-grid' : '' }}
                                                {{ $banner->type->name == 'static' ? 'badge-static' : '' }}
                                                {{ $banner->type->name == 'category_slider' ? 'badge-category' : '' }}">
                                                    <i
                                                        class="fas 
                                                    {{ $banner->type->name == 'slider' ? 'fa-sliders-h' : '' }}
                                                    {{ $banner->type->name == 'grid' ? 'fa-th-large' : '' }}
                                                    {{ $banner->type->name == 'static' ? 'fa-image' : '' }}
                                                    {{ $banner->type->name == 'category_slider' ? 'fa-tags' : '' }}
                                                    me-1"></i>
                                                    {{ $banner->type->name == 'slider' ? 'سلايدر' : '' }}
                                                    {{ $banner->type->name == 'grid' ? 'شبكة' : '' }}
                                                    {{ $banner->type->name == 'static' ? 'ثابت' : '' }}
                                                    {{ $banner->type->name == 'category_slider' ? 'أقسام' : '' }}
                                                </span>
                                            @endif

                                            @if ($banner->category_id)
                                                <div class="category-badge">
                                                    <i class="fas fa-tag me-1"></i>
                                                    {{ $banner->category->name ?? 'قسم' }}
                                                </div>
                                            @else
                                                <div class="category-badge">
                                                    <i class="fas fa-home me-1"></i>
                                                    الرئيسية
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <label class="toggle-switch">
                                                    <input type="checkbox" class="status-toggle"
                                                        data-id="{{ $banner->id }}"
                                                        {{ $banner->is_active ? 'checked' : '' }}>
                                                    <span class="toggle-slider"></span>
                                                </label>
                                                <span
                                                    class="badge-custom {{ $banner->is_active ? 'badge-active' : 'badge-inactive' }}">
                                                    {{ $banner->is_active ? 'نشط' : 'غير نشط' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($banner->start_date)
                                                <div class="mb-1">
                                                    <small class="text-muted">
                                                        <i class="fas fa-play-circle me-1"></i>
                                                        {{ $banner->start_date->translatedFormat('d M Y') }}
                                                    </small>
                                                </div>
                                            @endif
                                            @if ($banner->end_date)
                                                <div class="mb-1">
                                                    <small class="text-muted">
                                                        <i class="fas fa-stop-circle me-1"></i>
                                                        {{ $banner->end_date->translatedFormat('d M Y') }}
                                                    </small>
                                                </div>
                                            @endif
                                            @if (!$banner->start_date && !$banner->end_date)
                                                <small class="text-muted">دائم</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="{{ route('admin.banners.show', $banner) }}"
                                                    class="btn btn-action btn-info" title="عرض التفاصيل">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.banners.edit', $banner) }}"
                                                    class="btn btn-action btn-warning" title="تعديل">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-action btn-danger delete-btn"
                                                    title="حذف" data-id="{{ $banner->id }}"
                                                    data-title="{{ $banner->title }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <a href="#" class="btn btn-action btn-secondary manage-items-btn"
                                                    title="إدارة العناصر" data-id="{{ $banner->id }}">
                                                    <i class="fas fa-layer-group"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">
                                            <div class="empty-state">
                                                <div class="empty-state-icon">
                                                    <i class="fas fa-images"></i>
                                                </div>
                                                <h5 class="empty-state-text">لا توجد بنرات</h5>
                                                <a href="{{ route('admin.banners.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus me-2"></i>إضافة بانر جديد
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    @if ($banners->hasPages())
                        <div class="m-3">
                            <nav>
                                <ul class="pagination">
                                    {{-- Previous Page Link --}}
                                    @if ($banners->onFirstPage())
                                        <li class="page-item disabled" aria-disabled="true">
                                            <span class="page-link waves-effect" aria-hidden="true">‹</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link waves-effect" href="{{ $banners->previousPageUrl() }}"
                                                rel="prev">‹</a>
                                        </li>
                                    @endif

                                    {{-- Pagination Elements --}}
                                    @foreach ($banners->links()->elements[0] as $page => $url)
                                        @if ($page == $banners->currentPage())
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
                                    @if ($banners->hasMorePages())
                                        <li class="page-item">
                                            <a class="page-link waves-effect" href="{{ $banners->nextPageUrl() }}"
                                                rel="next">›</a>
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
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Items Management -->
    <div class="modal fade" id="itemsModal" tabindex="-1" role="dialog" aria-labelledby="itemsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="itemsModalLabel">إدارة عناصر البانر</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="itemsContainer"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                    <button type="button" class="btn btn-primary" id="saveItemsOrder">حفظ الترتيب</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sortable/0.8.0/js/sortable.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize sortable
            const sortableTable = document.getElementById('sortable-table');
            if (sortableTable) {
                new Sortable(sortableTable.querySelector('tbody'), {
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    dragClass: 'sortable-drag',
                    handle: '.order-handle',
                    onEnd: function(evt) {
                        const items = [];
                        $('#sortable-table tbody tr').each(function(index) {
                            const bannerId = $(this).data('id');
                            items.push({
                                id: bannerId,
                                order: index + 1
                            });
                        });

                        // Update order via AJAX
                        $.ajax({
                            url: "{{ route('admin.banners.items.reorder') }}",
                            type: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                items: items
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'تم الحفظ',
                                        text: 'تم حفظ الترتيب بنجاح',
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                }
                            }
                        });
                    }
                });
            }

            // البحث مع تأخير
            let searchTimeout;
            $('#searchInput').on('keyup', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    updateUrl({
                        search: $(this).val()
                    });
                }, 500);
            });

            // تبديل الحالة
            $('.status-toggle').on('change', function() {
                const bannerId = $(this).data('id');
                const isChecked = $(this).is(':checked');

                $.ajax({
                    url: "{{ route('admin.banners.toggle-status', '') }}/" + bannerId,
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        _method: 'PATCH'
                    },
                    success: function(response) {
                        if (response.success) {
                            const statusBadge = $(`tr[data-id="${bannerId}"] .badge-custom`);
                            if (response.is_active) {
                                statusBadge.removeClass('badge-inactive').addClass(
                                    'badge-active').text('نشط');
                            } else {
                                statusBadge.removeClass('badge-active').addClass(
                                    'badge-inactive').text('غير نشط');
                            }

                            Swal.fire({
                                icon: 'success',
                                title: 'نجاح',
                                text: 'تم تغيير حالة البانر بنجاح',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: 'حدث خطأ أثناء تغيير الحالة',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                });
            });

            // حذف البانر
            $('.delete-btn').on('click', function() {
                const bannerId = $(this).data('id');
                const bannerTitle = $(this).data('title');

                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: `سيتم حذف البانر "${bannerTitle}" نهائياً`,
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
                            url: "{{ route('admin.banners.destroy', '') }}/" + bannerId,
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

            // إدارة العناصر
            $('.manage-items-btn').on('click', function(e) {
                e.preventDefault();
                const bannerId = $(this).data('id');

                $.ajax({
                    url: "{{ route('admin.banners.show', '') }}/" + bannerId,
                    type: 'GET',
                    success: function(response) {
                        $('#itemsContainer').html(response);
                        $('#itemsModal').modal('show');

                        // Initialize items sortable
                        new Sortable(document.getElementById('itemsList'), {
                            animation: 150,
                            ghostClass: 'sortable-ghost',
                            dragClass: 'sortable-drag',
                            handle: '.item-handle',
                            onEnd: function(evt) {
                                updateItemsOrder();
                            }
                        });
                    }
                });
            });

            // حفظ ترتيب العناصر
            $('#saveItemsOrder').on('click', function() {
                const items = [];
                $('#itemsList .item-row').each(function(index) {
                    const itemId = $(this).data('id');
                    items.push({
                        id: itemId,
                        order: index + 1
                    });
                });

                $.ajax({
                    url: "{{ route('admin.banners.items.reorder') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        items: items
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'تم الحفظ',
                                text: 'تم حفظ ترتيب العناصر بنجاح',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                $('#itemsModal').modal('hide');
                                location.reload();
                            });
                        }
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

        function filterBy(type) {
            updateUrl({
                type: null,
                status: null
            });
        }

        function filterByType(type) {
            updateUrl({
                type: type,
                status: null
            });
        }

        function filterByStatus(status) {
            updateUrl({
                type: null,
                status: status
            });
        }

        function sortBy(sortBy, sortDirection) {
            updateUrl({
                sort_by: sortBy,
                sort_direction: sortDirection
            });
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

        function updateItemsOrder() {
            const items = [];
            $('#itemsList .item-row').each(function(index) {
                const itemId = $(this).data('id');
                items.push({
                    id: itemId,
                    order: index + 1
                });
            });

            // You can update UI here or wait for save
        }
    </script>
@endsection
