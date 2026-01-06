@extends('Admin.layout.master')

@section('title', 'إدارة المنتجات')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/colreorder/1.7.0/css/colReorder.bootstrap5.min.css" rel="stylesheet">
    <style>
        body {
            font-family: "Cairo", sans-serif !important;
        }

        .product-card {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            overflow: hidden;
            transition: all 0.3s ease;
            height: 100%;
        }

        .product-card:hover {
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            transform: translateY(-5px);
        }

        .product-image {
            height: 180px;
            overflow: hidden;
            position: relative;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .product-card:hover .product-image img {
            transform: scale(1.05);
        }

        .product-badges {
            position: absolute;
            top: 10px;
            right: 10px;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .badge-new {
            background: #ff6b6b;
            color: white;
            font-size: 11px;
            padding: 3px 8px;
            border-radius: 5px;
        }

        .badge-discount {
            background: #28a745;
            color: white;
            font-size: 11px;
            padding: 3px 8px;
            border-radius: 5px;
        }

        .badge-out-of-stock {
            background: #6c757d;
            color: white;
            font-size: 11px;
            padding: 3px 8px;
            border-radius: 5px;
        }

        .badge-low-stock {
            background: #ffc107;
            color: #212529;
            font-size: 11px;
            padding: 3px 8px;
            border-radius: 5px;
        }

        .product-content {
            padding: 15px;
        }

        .product-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #2c3e50;
            line-height: 1.4;
            height: 45px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .product-category {
            font-size: 12px;
            color: #7f8c8d;
            margin-bottom: 8px;
        }

        .product-price {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .current-price {
            font-size: 18px;
            font-weight: 700;
            color: #2ecc71;
        }

        .old-price {
            font-size: 14px;
            color: #95a5a6;
            text-decoration: line-through;
        }

        .product-meta {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #7f8c8d;
            margin-bottom: 12px;
        }

        .product-rating {
            color: #f1c40f;
            font-size: 12px;
        }

        .product-stock {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .stock-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .stock-indicator.in-stock {
            background-color: #2ecc71;
        }

        .stock-indicator.low-stock {
            background-color: #f39c12;
        }

        .stock-indicator.out-of-stock {
            background-color: #e74c3c;
        }

        .product-actions {
            display: flex;
            gap: 8px;
            margin-top: 15px;
        }

        .product-actions .btn {
            flex: 1;
            padding: 5px 10px;
            font-size: 12px;
        }

        /* DataTable Custom Styles */
        .dataTables_wrapper {
            padding: 0;
        }

        .dataTables_length select {
            padding: 4px 8px;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }

        .dataTables_filter input {
            padding: 4px 8px;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }

        .dt-buttons .btn {
            padding: 5px 10px;
            font-size: 13px;
            margin-right: 5px;
        }

        /* View Toggle */
        .view-toggle {
            display: flex;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            overflow: hidden;
        }

        .view-toggle-btn {
            padding: 8px 15px;
            /* background: #f8f9fa; */
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .view-toggle-btn.active {
            background: #696cff;
            color: white;
        }

        .view-toggle-btn:not(:last-child) {
            border-left: 1px solid #dee2e6;
        }

        /* Quick Actions */
        .quick-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .quick-action-btn {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 8px 15px;
            border-radius: 5px;
            /* background: #f8f9fa; */
            border: 1px solid #dee2e6;
            color: #495057;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .quick-action-btn:hover {
            /* background: #696cff; */
            color: white;
            border-color: #696cff;
            text-decoration: none;
        }

        /* Status Badges */
        .status-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-active {
            /* background-color: #d1fae5; */
            color: #065f46;
        }

        .status-inactive {
            /* background-color: #fee2e2; */
            color: #991b1b;
        }

        .status-draft {
            /* background-color: #fef3c7; */
            color: #92400e;
        }

        /* Bulk Actions */
        .bulk-actions-container {
            /* background: #f8f9fa; */
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            display: none;
        }

        .bulk-actions-container.show {
            display: block;
        }

        .bulk-action-select {
            max-width: 200px;
            display: inline-block;
            margin-left: 10px;
        }

        /* Statistics Cards */
        .stats-card {
            background: #383669;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            text-align: center;
        }

        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 24px;
        }

        .stats-icon.total {
            /* background: rgba(105, 108, 255, 0.1); */
            color: #696cff;
        }

        .stats-icon.active {
            /* background: rgba(52, 152, 219, 0.1); */
            color: #3498db;
        }

        .stats-icon.inactive {
            /* background: rgba(231, 76, 60, 0.1); */
            color: #e74c3c;
        }

        .stats-icon.low-stock {
            /* background: rgba(241, 196, 15, 0.1); */
            color: #f1c40f;
        }

        .stats-number {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stats-label {
            color: #7f8c8d;
            font-size: 14px;
        }

        /* Advanced Filters */
        .advanced-filters {
            /* background: #f8f9fa; */
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            display: none;
        }

        .advanced-filters.show {
            display: block;
        }

        .filter-section {
            /* background: white; */
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid #e9ecef;
        }

        .filter-section-title {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e9ecef;
        }

        /* Product Table */
        .product-table-image {
            width: 60px;
            height: 60px;
            border-radius: 5px;
            object-fit: cover;
        }

        .product-table-name {
            max-width: 250px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .view-toggle {
                margin-top: 10px;
            }

            .quick-actions {
                margin-top: 10px;
            }

            .product-table-image {
                width: 40px;
                height: 40px;
            }
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 50px 20px;
        }

        .empty-state i {
            font-size: 60px;
            color: #dee2e6;
            margin-bottom: 20px;
        }

        .empty-state h5 {
            color: #6c757d;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: #adb5bd;
            margin-bottom: 20px;
        }


        .btn-outline-primary.active {
            background-color: #0d6efd !important;
            color: #fff !important;
        }

        .btn-outline-warning.active {
            background-color: #ffc107 !important;
            color: #000 !important;
        }

        .btn-outline-danger.active {
            background-color: #dc3545 !important;
            color: #fff !important;
        }

        .btn-outline-secondary.active {
            background-color: #6c757d !important;
            color: #fff !important;
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
                <li class="breadcrumb-item active">المنتجات</li>
            </ol>
        </nav>

        <!-- Statistics Cards -->
        <div class="row mb-4" bis_skin_checked="1">
            <div class="col-md-3 col-sm-6" bis_skin_checked="1">
                <div class="stats-card" bis_skin_checked="1">
                    <div class="stats-icon total" bis_skin_checked="1">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="stats-number" bis_skin_checked="1">{{ $totalProducts }}</div>
                    <div class="stats-label" bis_skin_checked="1">إجمالي المنتجات</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6" bis_skin_checked="1">
                <div class="stats-card" bis_skin_checked="1">
                    <div class="stats-icon active" bis_skin_checked="1">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stats-number" bis_skin_checked="1">{{ $activeProducts }}</div>
                    <div class="stats-label" bis_skin_checked="1">منتجات نشطة</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6" bis_skin_checked="1">
                <div class="stats-card" bis_skin_checked="1">
                    <div class="stats-icon inactive" bis_skin_checked="1">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="stats-number" bis_skin_checked="1">{{ $inactiveProducts }}</div>
                    <div class="stats-label" bis_skin_checked="1">منتجات غير نشطة</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6" bis_skin_checked="1">
                <div class="stats-card" bis_skin_checked="1">
                    <div class="stats-icon low-stock" bis_skin_checked="1">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stats-number" bis_skin_checked="1">{{ $lowStockProducts }}</div>
                    <div class="stats-label" bis_skin_checked="1">منخفضة المخزون</div>
                </div>
            </div>
        </div>

        <!-- Header Actions -->
        <div class="card mb-4" bis_skin_checked="1">
            <div class="card-body" bis_skin_checked="1">
                <div class="row" bis_skin_checked="1">
                    <div class="col-md-8">
                        <div class="d-flex flex-wrap align-items-center gap-3">
                            <!-- Search Form -->
                            <form method="GET" action="{{ route('admin.products.index') }}" id="searchForm"
                                class="d-flex">
                                <div class="position-relative" style="min-width: 300px;">
                                    <input type="text" class="form-control" name="search" id="globalSearch"
                                        placeholder="بحث في المنتجات..." value="{{ request('search') }}">
                                    <i class="fas fa-search position-absolute"
                                        style="left: 15px; top: 50%; transform: translateY(-50%); color: #adb5bd;"></i>

                                    @if (request('search'))
                                        <button type="button" id="clearSearch" class="btn position-absolute"
                                            style="right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none;">
                                            <i class="fas fa-times text-muted"></i>
                                        </button>
                                    @endif
                                </div>

                                <!-- Hidden inputs to preserve other filters -->
                                @if (request('category_id'))
                                    <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                                @endif
                                @if (request('status_id'))
                                    <input type="hidden" name="status_id" value="{{ request('status_id') }}">
                                @endif
                                @if (request('price_from'))
                                    <input type="hidden" name="price_from" value="{{ request('price_from') }}">
                                @endif
                                @if (request('price_to'))
                                    <input type="hidden" name="price_to" value="{{ request('price_to') }}">
                                @endif
                                @if (request('stock_from'))
                                    <input type="hidden" name="stock_from" value="{{ request('stock_from') }}">
                                @endif
                                @if (request('stock_to'))
                                    <input type="hidden" name="stock_to" value="{{ request('stock_to') }}">
                                @endif
                            </form>

                            <!-- Quick Filters -->
                            <div class="quick-actions">
                                <button type="button" class="btn btn-outline-primary btn-sm"
                                    onclick="applyFilter('status_id', '1')">
                                    <i class="fas fa-check-circle"></i> النشطة
                                </button>
                                <button type="button" class="btn btn-outline-warning btn-sm"
                                    onclick="applyFilter('has_discount', '1')">
                                    <i class="fas fa-percentage"></i> ذات الخصم
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm"
                                    onclick="applyFilter('stock', 'low')">
                                    <i class="fas fa-exclamation-triangle"></i> منخفضة المخزون
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearFilters()">
                                    <i class="fas fa-filter-circle-xmark"></i> إعادة التعيين
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4" bis_skin_checked="1">
                        <div class="d-flex justify-content-end align-items-center gap-3" bis_skin_checked="1">
                            <!-- View Toggle -->
                            <div class="view-toggle" bis_skin_checked="1">
                                <button class="view-toggle-btn active" onclick="toggleView('grid')">
                                    <i class="fas fa-th-large"></i>
                                </button>
                                <button class="view-toggle-btn" onclick="toggleView('table')">
                                    <i class="fas fa-list"></i>
                                </button>
                            </div>

                            <!-- Add Product Button -->
                            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus-circle me-1"></i> إضافة منتج
                            </a>

                            <!-- More Actions -->
                            <div class="dropdown" bis_skin_checked="1">
                                <button class="btn btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="#" onclick="toggleAdvancedFilters()">
                                            <i class="fas fa-filter me-2"></i> فلاتر متقدمة
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#" onclick="exportProducts()">
                                            <i class="fas fa-file-export me-2"></i> تصدير البيانات
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#" onclick="printProducts()">
                                            <i class="fas fa-print me-2"></i> طباعة
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#" onclick="showBulkActions()">
                                            <i class="fas fa-layer-group me-2"></i> إجراءات جماعية
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Advanced Filters -->
                <div class="advanced-filters mt-4" id="advancedFilters">
                    <form id="filterForm">
                        <div class="row" bis_skin_checked="1">
                            <div class="col-md-3" bis_skin_checked="1">
                                <div class="filter-section" bis_skin_checked="1">
                                    <h6 class="filter-section-title">التصنيفات</h6>
                                    <select class="form-select select2" id="categoryFilter" name="category_id">
                                        <option value="">جميع التصنيفات</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3" bis_skin_checked="1">
                                <div class="filter-section" bis_skin_checked="1">
                                    <h6 class="filter-section-title">الحالة</h6>
                                    <select class="form-select" id="statusFilter" name="status_id">
                                        <option value="">جميع الحالات</option>
                                        <option value="1">نشط</option>
                                        <option value="2">غير نشط</option>
                                        <option value="3">مسودة</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3" bis_skin_checked="1">
                                <div class="filter-section" bis_skin_checked="1">
                                    <h6 class="filter-section-title">نطاق السعر</h6>
                                    <div class="row g-2" bis_skin_checked="1">
                                        <div class="col-6" bis_skin_checked="1">
                                            <input type="number" class="form-control" placeholder="من"
                                                name="price_from" id="priceFrom">
                                        </div>
                                        <div class="col-6" bis_skin_checked="1">
                                            <input type="number" class="form-control" placeholder="إلى" name="price_to"
                                                id="priceTo">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3" bis_skin_checked="1">
                                <div class="filter-section" bis_skin_checked="1">
                                    <h6 class="filter-section-title">نطاق المخزون</h6>
                                    <div class="row g-2" bis_skin_checked="1">
                                        <div class="col-6" bis_skin_checked="1">
                                            <input type="number" class="form-control" placeholder="من"
                                                name="stock_from" id="stockFrom">
                                        </div>
                                        <div class="col-6" bis_skin_checked="1">
                                            <input type="number" class="form-control" placeholder="إلى" name="stock_to"
                                                id="stockTo">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row" bis_skin_checked="1">
                            <div class="col-md-3" bis_skin_checked="1">
                                <div class="filter-section" bis_skin_checked="1">
                                    <h6 class="filter-section-title">الألوان</h6>
                                    <select class="form-select select2" id="colorFilter" name="color_id" multiple>
                                        @foreach ($colors as $color)
                                            <option value="{{ $color->id }}">{{ $color->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3" bis_skin_checked="1">
                                <div class="filter-section" bis_skin_checked="1">
                                    <h6 class="filter-section-title">المواد</h6>
                                    <select class="form-select select2" id="materialFilter" name="material_id" multiple>
                                        @foreach ($materials as $material)
                                            <option value="{{ $material->id }}">{{ $material->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3" bis_skin_checked="1">
                                <div class="filter-section" bis_skin_checked="1">
                                    <h6 class="filter-section-title">طرق الطباعة</h6>
                                    <select class="form-select select2" id="printingMethodFilter"
                                        name="printing_method_id" multiple>
                                        @foreach ($printingMethods as $method)
                                            <option value="{{ $method->id }}">{{ $method->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3" bis_skin_checked="1">
                                <div class="filter-section" bis_skin_checked="1">
                                    <h6 class="filter-section-title">العروض</h6>
                                    <select class="form-select select2" id="offerFilter" name="offer_id" multiple>
                                        @foreach ($offers as $offer)
                                            <option value="{{ $offer->id }}">{{ $offer->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-3" bis_skin_checked="1">
                            <button type="button" class="btn btn-outline-secondary" onclick="clearAdvancedFilters()">
                                <i class="fas fa-redo me-1"></i> إعادة تعيين الفلاتر
                            </button>
                            <button type="button" class="btn btn-primary" onclick="applyAdvancedFilters()">
                                <i class="fas fa-filter me-1"></i> تطبيق الفلاتر
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Bulk Actions -->
                <div class="bulk-actions-container" id="bulkActions">
                    <div class="d-flex align-items-center justify-content-between" bis_skin_checked="1">
                        <div class="d-flex align-items-center" bis_skin_checked="1">
                            <div class="form-check me-3" bis_skin_checked="1">
                                <input class="form-check-input" type="checkbox" id="selectAllBulk">
                                <label class="form-check-label" for="selectAllBulk">
                                    تم تحديد <span id="selectedCount">0</span> منتج
                                </label>
                            </div>
                            <select class="form-select bulk-action-select" id="bulkActionSelect">
                                <option value="">اختر إجراء...</option>
                                <option value="activate">تفعيل</option>
                                <option value="deactivate">تعطيل</option>
                                <option value="move_to_category">نقل إلى تصنيف</option>
                                <option value="add_to_offer">إضافة إلى عرض</option>
                                <option value="remove_from_offer">إزالة من عرض</option>
                                <option value="delete">حذف</option>
                            </select>
                            <button type="button" class="btn btn-primary ms-2" onclick="applyBulkAction()">
                                تطبيق
                            </button>
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="hideBulkActions()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <!-- Additional options for certain bulk actions -->
                    <div id="bulkActionOptions" class="mt-3" style="display: none;">
                        <div id="categoryOptions" class="row g-3" style="display: none;">
                            <div class="col-md-6" bis_skin_checked="1">
                                <select class="form-select" id="bulkCategorySelect">
                                    <option value="">اختر التصنيف</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div id="offerOptions" class="row g-3" style="display: none;">
                            <div class="col-md-6" bis_skin_checked="1">
                                <select class="form-select" id="bulkOfferSelect">
                                    <option value="">اختر العرض</option>
                                    @foreach ($offers as $offer)
                                        <option value="{{ $offer->id }}">{{ $offer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grid View -->
        <div id="gridView" class="view-container">
            @if ($products->count() > 0)
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-4" id="productsGrid">
                    @foreach ($products as $product)
                        <div class="col">
                            <div class="product-card" data-product-id="{{ $product->id }}">
                                <div class="product-image">
                                    <img src="{{ $product->primaryImage ? get_user_image($product->primaryImage->path) : asset(env('DEFAULT_PRODUCT_IMAGE')) }}"
                                        alt="{{ $product->name }}">

                                    <div class="product-badges">
                                        @if ($product->created_at->gt(now()->subDays(7)))
                                            <span class="badge-new">جديد</span>
                                        @endif
                                        @if ($product->has_discount && $product->discount)
                                            <span class="badge-discount">
                                                @if ($product->discount->discount_type === 'percentage')
                                                    {{ $product->discount->discount_value }}%
                                                @else
                                                    خصم
                                                @endif
                                            </span>
                                        @endif
                                        @if ($product->stock == 0)
                                            <span class="badge-out-of-stock">نفذ من المخزون</span>
                                        @elseif($product->stock < 10)
                                            <span class="badge-low-stock">مخزون منخفض</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="product-content">
                                    <h6 class="product-title" title="{{ $product->name }}">
                                        {{ Str::limit($product->name, 50) }}
                                    </h6>

                                    <div class="product-category">
                                        <i class="fas fa-folder me-1"></i>
                                        {{ $product->category->name ?? 'غير مصنف' }}
                                    </div>

                                    <div class="product-price">
                                        <span class="current-price">
                                            {{ number_format($product->final_price, 2) }} ج.م
                                        </span>
                                        @if ($product->has_discount && $product->price > $product->final_price)
                                            <span class="old-price">
                                                {{ number_format($product->price, 2) }} ج.م
                                            </span>
                                        @endif
                                    </div>

                                    <div class="product-meta">
                                        <div class="product-rating" bis_skin_checked="1">
                                            <i class="fas fa-star"></i>
                                            {{ number_format($product->average_rating, 1) }}
                                        </div>
                                        <div class="product-stock" bis_skin_checked="1">
                                            <span
                                                class="stock-indicator {{ $product->stock == 0 ? 'out-of-stock' : ($product->stock < 10 ? 'low-stock' : 'in-stock') }}"></span>
                                            {{ $product->stock }} قطعة
                                        </div>
                                    </div>

                                    <div class="product-actions">
                                        <a href="{{ route('admin.products.show', $product->id) }}"
                                            class="btn btn-outline-info btn-sm" title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.products.edit', $product->id) }}"
                                            class="btn btn-outline-primary btn-sm" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-success btn-sm"
                                            onclick="duplicateProduct({{ $product->id }})" title="نسخ">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm delete-product"
                                            data-id="{{ $product->id }}" data-name="{{ $product->name }}"
                                            title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <div class="form-check" style="padding-top: 5px;">
                                            <input class="form-check-input product-checkbox" type="checkbox"
                                                value="{{ $product->id }}" id="product_{{ $product->id }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if ($products->hasPages())
                    <div class="m-3">
                        <nav>
                            <ul class="pagination">
                                {{-- Previous Page Link --}}
                                @if ($products->onFirstPage())
                                    <li class="page-item disabled" aria-disabled="true">
                                        <span class="page-link waves-effect" aria-hidden="true">‹</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link waves-effect" href="{{ $products->previousPageUrl() }}"
                                            rel="prev">‹</a>
                                    </li>
                                @endif

                                {{-- Pagination Elements --}}
                                @foreach ($products->links()->elements[0] as $page => $url)
                                    @if ($page == $products->currentPage())
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
                                @if ($products->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link waves-effect" href="{{ $products->nextPageUrl() }}"
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
            @else
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <h5>لا توجد منتجات</h5>
                    <p>ابدأ بإضافة منتجات جديدة إلى متجرك</p>
                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-1"></i> إضافة منتج جديد
                    </a>
                </div>
            @endif
        </div>

        <!-- Table View -->
        <div id="tableView" class="view-container" style="display: none;">
            <div class="card" bis_skin_checked="1">
                <div class="card-body" bis_skin_checked="1">
                    <div class="table-responsive" bis_skin_checked="1">
                        <table class="table table-hover" id="productsTable">
                            <thead>
                                <tr>
                                    <th width="50">
                                        <div class="form-check" bis_skin_checked="1">
                                            <input class="form-check-input" type="checkbox" id="selectAllTable">
                                        </div>
                                    </th>
                                    <th width="80">الصورة</th>
                                    <th>المنتج</th>
                                    <th>التصنيف</th>
                                    <th>السعر</th>
                                    <th>المخزون</th>
                                    <th>التقييم</th>
                                    <th>الحالة</th>
                                    <th>تاريخ الإضافة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $product)
                                    <tr data-product-id="{{ $product->id }}">
                                        <td>
                                            <div class="form-check" bis_skin_checked="1">
                                                <input class="form-check-input product-checkbox" type="checkbox"
                                                    value="{{ $product->id }}">
                                            </div>
                                        </td>
                                        <td>
                                            <img src="{{ $product->primaryImage ? get_user_image($product->primaryImage->path) : 'https://via.placeholder.com/60x60?text=No+Image' }}"
                                                alt="{{ $product->name }}" class="product-table-image">
                                        </td>
                                        <td>
                                            <div class="product-table-name" title="{{ $product->name }}"
                                                bis_skin_checked="1">
                                                {{ $product->name }}
                                            </div>
                                            <small class="text-muted">{{ Str::limit($product->description, 50) }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-label-secondary">
                                                {{ $product->category->name ?? 'غير مصنف' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div bis_skin_checked="1">
                                                <strong class="text-success">{{ number_format($product->final_price, 2) }}
                                                    ج.م</strong>
                                                @if ($product->has_discount && $product->price > $product->final_price)
                                                    <br>
                                                    <small class="text-muted text-decoration-line-through">
                                                        {{ number_format($product->price, 2) }} ج.م
                                                    </small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2" bis_skin_checked="1">
                                                <span
                                                    class="stock-indicator {{ $product->stock == 0 ? 'out-of-stock' : ($product->stock < 10 ? 'low-stock' : 'in-stock') }}"></span>
                                                {{ $product->stock }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="product-rating" bis_skin_checked="1">
                                                <i class="fas fa-star text-warning"></i>
                                                {{ number_format($product->average_rating, 1) }}
                                            </div>
                                        </td>
                                        <td>
                                            @if ($product->status_id == 1)
                                                <span class="status-badge status-active">نشط</span>
                                            @elseif($product->status_id == 2)
                                                <span class="status-badge status-inactive">غير نشط</span>
                                            @else
                                                <span class="status-badge status-draft">مسودة</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $product->created_at->format('Y/m/d') }}
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2" bis_skin_checked="1">
                                                <a href="{{ route('admin.products.show', $product->id) }}"
                                                    class="btn btn-sm btn-outline-info" title="عرض">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.products.edit', $product) }}"
                                                    class="btn btn-sm btn-outline-primary" title="تعديل">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-success"
                                                    onclick="duplicateProduct({{ $product->id }})" title="نسخ">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-danger delete-product"
                                                    data-id="{{ $product->id }}" data-name="{{ $product->name }}"
                                                    title="حذف">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($products->hasPages())
                        <div class="m-3">
                            <nav>
                                <ul class="pagination">
                                    {{-- Previous Page Link --}}
                                    @if ($products->onFirstPage())
                                        <li class="page-item disabled" aria-disabled="true">
                                            <span class="page-link waves-effect" aria-hidden="true">‹</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link waves-effect" href="{{ $products->previousPageUrl() }}"
                                                rel="prev">‹</a>
                                        </li>
                                    @endif

                                    {{-- Pagination Elements --}}
                                    @foreach ($products->links()->elements[0] as $page => $url)
                                        @if ($page == $products->currentPage())
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
                                    @if ($products->hasMorePages())
                                        <li class="page-item">
                                            <a class="page-link waves-effect" href="{{ $products->nextPageUrl() }}"
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

    <!-- Quick Edit Modal -->
    <div class="modal fade" id="quickEditModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">تعديل سريع</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="quickEditForm">
                        <div class="row" bis_skin_checked="1">
                            <div class="col-md-6 mb-3" bis_skin_checked="1">
                                <label class="form-label">السعر</label>
                                <input type="number" class="form-control" name="price" step="0.01">
                            </div>
                            <div class="col-md-6 mb-3" bis_skin_checked="1">
                                <label class="form-label">المخزون</label>
                                <input type="number" class="form-control" name="stock" min="0">
                            </div>
                            <div class="col-md-6 mb-3" bis_skin_checked="1">
                                <label class="form-label">الحالة</label>
                                <select class="form-select" name="status_id">
                                    <option value="1">نشط</option>
                                    <option value="2">غير نشط</option>
                                    <option value="3">مسودة</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3" bis_skin_checked="1">
                                <label class="form-label">التصنيف</label>
                                <select class="form-select" name="category_id">
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-primary" onclick="saveQuickEdit()">حفظ</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Modal -->
    <div class="modal fade" id="exportModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">تصدير المنتجات</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3" bis_skin_checked="1">
                        <label class="form-label">نوع الملف</label>
                        <select class="form-select" id="exportType">
                            <option value="excel">Excel</option>
                            <option value="csv">CSV</option>
                            <option value="pdf">PDF</option>
                        </select>
                    </div>
                    <div class="mb-3" bis_skin_checked="1">
                        <label class="form-label">الأعمدة</label>
                        <select class="form-select select2" id="exportColumns" multiple>
                            <option value="id" selected>رقم المنتج</option>
                            <option value="name" selected>اسم المنتج</option>
                            <option value="category" selected>التصنيف</option>
                            <option value="price" selected>السعر</option>
                            <option value="stock" selected>المخزون</option>
                            <option value="status" selected>الحالة</option>
                            <option value="created_at" selected>تاريخ الإضافة</option>
                            <option value="description">الوصف</option>
                            <option value="colors">الألوان</option>
                            <option value="materials">المواد</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-primary" onclick="performExport()">تصدير</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.datatables.net/colreorder/1.7.0/js/dataTables.colReorder.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                placeholder: 'اختر',
                allowClear: true
            });

            // Initialize DataTable with basic features only
            $('#productsTable').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json'
                },
                responsive: true,
                paging: false,
                searching: false,
                info: false,
                ordering: true,
                dom: '<"row"<"col-sm-12"tr>>',
            });

            // Debounce function لمنع طلبات كثيرة
            function debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }

            // البحث عن طريق الـ Form (يبحث في كل البيانات)
            $('#globalSearch').on('keyup', debounce(function() {
                const searchValue = $(this).val().trim();

                // إذا كان البحث فارغاً، أزل معامل البحث من الـ URL
                if (searchValue === '') {
                    const url = new URL(window.location.href);
                    url.searchParams.delete('search');
                    url.searchParams.delete('page'); // أزل رقم الصفحة للعودة للصفحة الأولى
                    window.location.href = url.toString();
                    return;
                }

                // إرسال الفورم للبحث في كل البيانات
                $('#searchForm').submit();
            }, 500));

            // Clear search when clicking clear button
            $(document).on('click', '#clearSearch', function() {
                $('#globalSearch').val('');
                $('#searchForm').submit();
            });

            // Product checkbox selection
            $(document).on('change', '.product-checkbox', function() {
                updateSelectedCount();
            });

            // Select all checkboxes
            $('#selectAllBulk, #selectAllTable').on('change', function() {
                const isChecked = $(this).is(':checked');
                $('.product-checkbox').prop('checked', isChecked);
                updateSelectedCount();
            });

            // Delete product
            $(document).on('click', '.delete-product', function() {
                const productId = $(this).data('id');
                const productName = $(this).data('name');

                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: `سيتم حذف المنتج "${productName}" بشكل دائم`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'نعم، احذف',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteProduct(productId);
                    }
                });
            });

            // View toggle buttons
            $('.view-toggle-btn').on('click', function() {
                const viewType = $(this).has('.fa-th-large').length ? 'grid' : 'table';
                toggleView(viewType);
            });

            // Quick filter buttons
            $('.quick-actions .btn').on('click', function() {
                const action = $(this).attr('onclick');
                if (action && action.includes('applyFilter')) {
                    const match = action.match(/applyFilter\('([^']+)',\s*'([^']+)'\)/);
                    if (match) {
                        applyFilter(match[1], match[2]);
                    }
                }
            });

            // Fill search input with current search value
            @if (request('search'))
                $('#globalSearch').val('{{ request('search') }}');
            @endif
        });

        // View Toggle Function
        window.toggleView = function(viewType) {
            $('.view-toggle-btn').removeClass('active').filter(function() {
                return (viewType === 'grid' && $(this).has('.fa-th-large').length) ||
                    (viewType === 'table' && $(this).has('.fa-list').length);
            }).addClass('active');

            $('.view-container').hide();
            if (viewType === 'grid') {
                $('#gridView').show();
            } else {
                $('#tableView').show();
                $('#productsTable').DataTable().columns.adjust().responsive.recalc();
            }
        }

        // Quick Filters Function
        window.applyFilter = function(filter, value) {
            const url = new URL(window.location.href);
            url.searchParams.delete('page'); // العودة للصفحة الأولى

            if (value === 'low') {
                url.searchParams.set('stock_from', '1');
                url.searchParams.set('stock_to', '10');
                url.searchParams.delete('stock');
            } else {
                url.searchParams.set(filter, value);
            }

            window.location.href = url.toString();
        }

        window.clearFilters = function() {
            // الحفاظ على البحث الحالي إذا كان موجوداً
            const searchValue = $('#globalSearch').val();
            let url = '{{ route('admin.products.index') }}';

            if (searchValue) {
                url += '?search=' + encodeURIComponent(searchValue);
            }

            window.location.href = url;
        }

        // Advanced Filters Functions
        window.toggleAdvancedFilters = function() {
            $('#advancedFilters').toggleClass('show');
        }

        window.clearAdvancedFilters = function() {
            $('#filterForm')[0].reset();
            $('.select2').val(null).trigger('change');
        }

        window.applyAdvancedFilters = function() {
            const formData = new FormData($('#filterForm')[0]);
            const params = new URLSearchParams();

            // إضافة البحث الحالي إذا كان موجوداً
            const searchValue = $('#globalSearch').val();
            if (searchValue) {
                params.append('search', searchValue);
            }

            for (let [key, value] of formData.entries()) {
                if (value) {
                    params.append(key, value);
                }
            }

            window.location.href = '{{ route('admin.products.index') }}?' + params.toString();
        }
        // View Toggle Function
        window.toggleView = function(viewType) {
            $('.view-toggle-btn').removeClass('active');
            $('.view-container').hide();

            if (viewType === 'grid') {
                $('#gridView').show();
                $('.view-toggle-btn:first').addClass('active');
            } else {
                $('#tableView').show();
                $('.view-toggle-btn:last').addClass('active');
            }
        }

        // Quick Filters Function
        window.applyFilter = function(filter, value) {
            let url = new URL(window.location.href);

            if (value === 'low') {
                url.searchParams.set('stock_from', '1');
                url.searchParams.set('stock_to', '10');
            } else {
                url.searchParams.set(filter, value);
            }

            window.location.href = url.toString();
        }

        window.clearFilters = function() {
            window.location.href = '{{ route('admin.products.index') }}';
        }

        // Advanced Filters Functions
        window.toggleAdvancedFilters = function() {
            $('#advancedFilters').toggleClass('show');
        }

        window.clearAdvancedFilters = function() {
            $('#filterForm')[0].reset();
            $('.select2').val(null).trigger('change');
        }

        window.applyAdvancedFilters = function() {
            const formData = new FormData($('#filterForm')[0]);
            const params = new URLSearchParams();

            for (let [key, value] of formData.entries()) {
                if (value) {
                    params.append(key, value);
                }
            }

            window.location.href = '{{ route('admin.products.index') }}?' + params.toString();
        }

        // Bulk Actions Functions
        window.showBulkActions = function() {
            $('#bulkActions').addClass('show');
        }

        window.hideBulkActions = function() {
            $('#bulkActions').removeClass('show');
            $('.product-checkbox').prop('checked', false);
            updateSelectedCount();
        }

        function updateSelectedCount() {
            const selectedCount = $('.product-checkbox:checked').length;
            $('#selectedCount').text(selectedCount);

            // Show/hide bulk actions based on selection
            if (selectedCount > 0) {
                $('#bulkActions').addClass('show');
            } else {
                $('#bulkActions').removeClass('show');
            }
        }

        $(document).on('change', '#bulkActionSelect', function() {
            const action = $(this).val();
            $('#bulkActionOptions').hide().find('> div').hide();

            if (action === 'move_to_category') {
                $('#bulkActionOptions').show();
                $('#categoryOptions').show();
            } else if (action === 'add_to_offer' || action === 'remove_from_offer') {
                $('#bulkActionOptions').show();
                $('#offerOptions').show();
            }
        });

        window.applyBulkAction = function() {
            const action = $('#bulkActionSelect').val();
            const selectedProducts = [];

            $('.product-checkbox:checked').each(function() {
                selectedProducts.push($(this).val());
            });

            if (selectedProducts.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'لم يتم الاختيار',
                    text: 'يرجى اختيار منتجات على الأقل',
                    confirmButtonText: 'حسناً'
                });
                return;
            }

            if (!action) {
                Swal.fire({
                    icon: 'warning',
                    title: 'لم يتم اختيار إجراء',
                    text: 'يرجى اختيار الإجراء المطلوب',
                    confirmButtonText: 'حسناً'
                });
                return;
            }

            let additionalData = {};

            switch (action) {
                case 'move_to_category':
                    const categoryId = $('#bulkCategorySelect').val();
                    if (!categoryId) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'تصنيف مطلوب',
                            text: 'يرجى اختيار التصنيف',
                            confirmButtonText: 'حسناً'
                        });
                        return;
                    }
                    additionalData.category_id = categoryId;
                    break;

                case 'add_to_offer':
                case 'remove_from_offer':
                    const offerId = $('#bulkOfferSelect').val();
                    if (!offerId) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'عرض مطلوب',
                            text: 'يرجى اختيار العرض',
                            confirmButtonText: 'حسناً'
                        });
                        return;
                    }
                    additionalData.offer_id = offerId;
                    break;
            }

            Swal.fire({
                title: 'تأكيد الإجراء',
                text: `سيتم تطبيق الإجراء على ${selectedProducts.length} منتج`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'تطبيق',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    performBulkAction(action, selectedProducts, additionalData);
                }
            });
        }

        function performBulkAction(action, productIds, additionalData = {}) {
            $.ajax({
                url: '{{ route('admin.products.bulk-action') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    action: action,
                    product_ids: productIds,
                    ...additionalData
                },
                beforeSend: function() {
                    Swal.fire({
                        title: 'جاري المعالجة...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'تم بنجاح!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ!',
                            text: response.message
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ!',
                        text: xhr.responseJSON?.message || 'حدث خطأ أثناء المعالجة'
                    });
                }
            });
        }

        // Product Operations Functions
        function deleteProduct(productId) {
            $.ajax({
                url: `/admin/products/${productId}`,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'تم الحذف!',
                            text: 'تم حذف المنتج بنجاح',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ!',
                            text: response.message
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ!',
                        text: xhr.responseJSON?.message || 'حدث خطأ أثناء الحذف'
                    });
                }
            });
        }

        window.duplicateProduct = function(productId) {
            Swal.fire({
                title: 'نسخ المنتج',
                input: 'text',
                inputLabel: 'أدخل اسم للمنتج المنسوخ:',
                showCancelButton: true,
                confirmButtonText: 'نسخ',
                cancelButtonText: 'إلغاء',
                showLoaderOnConfirm: true,
                preConfirm: (name) => {
                    if (!name) {
                        Swal.showValidationMessage('يجب إدخال اسم للمنتج');
                        return false;
                    }

                    return fetch(`/admin/products/${productId}/duplicate`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                name: name
                            })
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
                        text: 'تم نسخ المنتج بنجاح',
                        icon: 'success',
                        confirmButtonText: 'حسناً'
                    }).then(() => {
                        window.location.href = '/admin/products/' + result.value.data.id + '/edit';
                    });
                }
            });
        }

        // Export Functions
        window.exportProducts = function() {
            $('#exportModal').modal('show');
        }

        window.performExport = function() {
            const type = $('#exportType').val();
            const columns = $('#exportColumns').val();
            const filters = new URLSearchParams(window.location.search);

            let url = '{{ route('admin.products.export') }}?';
            url += `type=${type}`;
            url += `&columns=${columns.join(',')}`;
            url += `&${filters.toString()}`;

            window.open(url, '_blank');
            $('#exportModal').modal('hide');
        }
    </script>
@endsection
