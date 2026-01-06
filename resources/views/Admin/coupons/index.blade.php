@extends('Admin.layout.master')

@section('title', 'إدارة الكوبونات')

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

    .coupon-card {
        border: 1px solid #dee2e6;
        border-radius: 10px;
        overflow: hidden;
        transition: all 0.3s ease;
        height: 100%;
    }

    .coupon-card:hover {
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        transform: translateY(-5px);
    }

    .coupon-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 15px;
        text-align: center;
    }

    .coupon-code {
        font-size: 24px;
        font-weight: bold;
        letter-spacing: 2px;
        margin-bottom: 5px;
    }

    .coupon-type {
        font-size: 12px;
        opacity: 0.9;
        text-transform: uppercase;
    }

    .coupon-content {
        padding: 15px;
    }

    .coupon-title {
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

    .coupon-description {
        font-size: 13px;
        color: #7f8c8d;
        margin-bottom: 10px;
        height: 40px;
        overflow: hidden;
    }

    .coupon-details {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 10px;
        margin-bottom: 15px;
    }

    .coupon-value {
        font-size: 22px;
        font-weight: 700;
        color: #2ecc71;
        text-align: center;
        margin-bottom: 5px;
    }

    .coupon-value.type-percentage::after {
        content: '%';
        font-size: 16px;
        margin-right: 2px;
    }

    .coupon-value.type-fixed::before {
        content: 'ج.م';
        font-size: 14px;
        margin-left: 5px;
    }

    .coupon-meta {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        color: #7f8c8d;
        margin-bottom: 12px;
    }

    .coupon-actions {
        display: flex;
        gap: 8px;
        margin-top: 15px;
    }

    .coupon-actions .btn {
        flex: 1;
        padding: 5px 10px;
        font-size: 12px;
    }

    .badge-status {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-active {
        background-color: #d1fae5;
        color: #065f46;
    }

    .badge-inactive {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .badge-expired {
        background-color: #f3f4f6;
        color: #374151;
    }

    .badge-type {
        padding: 3px 8px;
        border-radius: 5px;
        font-size: 11px;
    }

    .badge-type-percentage {
        background-color: #dbeafe;
        color: #1e40af;
    }

    .badge-type-fixed {
        background-color: #fef3c7;
        color: #92400e;
    }

    .stats-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        margin-bottom: 20px;
        text-align: center;
        border: 1px solid #e9ecef;
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
        background: rgba(105, 108, 255, 0.1);
        color: #696cff;
    }

    .stats-icon.active {
        background: rgba(52, 152, 219, 0.1);
        color: #3498db;
    }

    .stats-icon.inactive {
        background: rgba(231, 76, 60, 0.1);
        color: #e74c3c;
    }

    .stats-icon.expired {
        background: rgba(149, 165, 166, 0.1);
        color: #95a5a6;
    }

    .stats-icon.percentage {
        background: rgba(46, 204, 113, 0.1);
        color: #2ecc71;
    }

    .stats-icon.fixed {
        background: rgba(155, 89, 182, 0.1);
        color: #9b59b6;
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

    .quick-actions .btn.active {
        background-color: #0d6efd;
        color: white;
    }

    .advanced-filters {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        display: none;
    }

    .advanced-filters.show {
        display: block;
    }

    .filter-section {
        background: white;
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

    .coupon-table-code {
        font-family: 'Courier New', monospace;
        font-weight: bold;
        letter-spacing: 1px;
    }

    .usage-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #e9ecef;
        font-size: 12px;
        font-weight: bold;
    }

    .usage-count.high {
        background: #fee2e2;
        color: #991b1b;
    }

    .usage-count.medium {
        background: #fef3c7;
        color: #92400e;
    }

    .usage-count.low {
        background: #d1fae5;
        color: #065f46;
    }

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
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y" bis_skin_checked="1">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.index') }}">الرئيسية</a>
            </li>
            <li class="breadcrumb-item active">الكوبونات</li>
        </ol>
    </nav>

    <!-- Statistics Cards -->
    <div class="row mb-4" bis_skin_checked="1">
        <div class="col-md-2 col-sm-4" bis_skin_checked="1">
            <div class="stats-card" bis_skin_checked="1">
                <div class="stats-icon total" bis_skin_checked="1">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div class="stats-number" bis_skin_checked="1">{{ $totalCoupons }}</div>
                <div class="stats-label" bis_skin_checked="1">إجمالي الكوبونات</div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4" bis_skin_checked="1">
            <div class="stats-card" bis_skin_checked="1">
                <div class="stats-icon active" bis_skin_checked="1">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stats-number" bis_skin_checked="1">{{ $activeCoupons }}</div>
                <div class="stats-label" bis_skin_checked="1">كوبونات نشطة</div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4" bis_skin_checked="1">
            <div class="stats-card" bis_skin_checked="1">
                <div class="stats-icon inactive" bis_skin_checked="1">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stats-number" bis_skin_checked="1">{{ $inactiveCoupons }}</div>
                <div class="stats-label" bis_skin_checked="1">غير نشطة</div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4" bis_skin_checked="1">
            <div class="stats-card" bis_skin_checked="1">
                <div class="stats-icon expired" bis_skin_checked="1">
                    <i class="fas fa-calendar-times"></i>
                </div>
                <div class="stats-number" bis_skin_checked="1">{{ $expiredCoupons }}</div>
                <div class="stats-label" bis_skin_checked="1">منتهية</div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4" bis_skin_checked="1">
            <div class="stats-card" bis_skin_checked="1">
                <div class="stats-icon percentage" bis_skin_checked="1">
                    <i class="fas fa-percentage"></i>
                </div>
                <div class="stats-number" bis_skin_checked="1">{{ $percentageCoupons }}</div>
                <div class="stats-label" bis_skin_checked="1">نسبة مئوية</div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4" bis_skin_checked="1">
            <div class="stats-card" bis_skin_checked="1">
                <div class="stats-icon fixed" bis_skin_checked="1">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stats-number" bis_skin_checked="1">{{ $fixedCoupons }}</div>
                <div class="stats-label" bis_skin_checked="1">مبلغ ثابت</div>
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
                        <form method="GET" action="{{ route('admin.coupons.index') }}" id="searchForm"
                            class="d-flex">
                            <div class="position-relative" style="min-width: 300px;">
                                <input type="text" class="form-control" name="search" id="globalSearch"
                                    placeholder="بحث في الكوبونات..." value="{{ request('search') }}">
                                <i class="fas fa-search position-absolute"
                                    style="left: 15px; top: 50%; transform: translateY(-50%); color: #adb5bd;"></i>

                                @if (request('search'))
                                    <button type="button" id="clearSearch" class="btn position-absolute"
                                        style="right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none;">
                                        <i class="fas fa-times text-muted"></i>
                                    </button>
                                @endif
                            </div>
                        </form>

                        <!-- Quick Filters -->
                        <div class="quick-actions">
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="applyFilter('status', 'active')">
                                <i class="fas fa-check-circle"></i> النشطة
                            </button>
                            <button type="button" class="btn btn-outline-warning btn-sm" onclick="applyFilter('type', 'percentage')">
                                <i class="fas fa-percentage"></i> نسبة مئوية
                            </button>
                            <button type="button" class="btn btn-outline-info btn-sm" onclick="applyFilter('type', 'fixed')">
                                <i class="fas fa-money-bill"></i> مبلغ ثابت
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

                        <!-- Add Coupon Button -->
                        <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-1"></i> إضافة كوبون
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
                                    <a class="dropdown-item" href="#" onclick="generateMultipleCoupons()">
                                        <i class="fas fa-copy me-2"></i> إنشاء كوبونات متعددة
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="exportCoupons()">
                                        <i class="fas fa-file-export me-2"></i> تصدير البيانات
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
                                <h6 class="filter-section-title">نوع الكوبون</h6>
                                <select class="form-select" id="typeFilter" name="type">
                                    <option value="">جميع الأنواع</option>
                                    <option value="percentage">نسبة مئوية</option>
                                    <option value="fixed">مبلغ ثابت</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3" bis_skin_checked="1">
                            <div class="filter-section" bis_skin_checked="1">
                                <h6 class="filter-section-title">الحالة</h6>
                                <select class="form-select" id="statusFilter" name="status">
                                    <option value="">جميع الحالات</option>
                                    <option value="active">نشط</option>
                                    <option value="inactive">غير نشط</option>
                                    <option value="expired">منتهي</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3" bis_skin_checked="1">
                            <div class="filter-section" bis_skin_checked="1">
                                <h6 class="filter-section-title">نطاق القيمة</h6>
                                <div class="row g-2" bis_skin_checked="1">
                                    <div class="col-6" bis_skin_checked="1">
                                        <input type="number" class="form-control" placeholder="من"
                                            name="value_from" id="valueFrom" step="0.01">
                                    </div>
                                    <div class="col-6" bis_skin_checked="1">
                                        <input type="number" class="form-control" placeholder="إلى" name="value_to"
                                            id="valueTo" step="0.01">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3" bis_skin_checked="1">
                            <div class="filter-section" bis_skin_checked="1">
                                <h6 class="filter-section-title">حد أدنى للطلب</h6>
                                <input type="number" class="form-control" placeholder="حد أدنى للطلب"
                                    name="min_order_amount" step="0.01">
                            </div>
                        </div>
                    </div>

                    <div class="row" bis_skin_checked="1">
                        <div class="col-md-3" bis_skin_checked="1">
                            <div class="filter-section" bis_skin_checked="1">
                                <h6 class="filter-section-title">تاريخ البدء</h6>
                                <div class="row g-2" bis_skin_checked="1">
                                    <div class="col-6" bis_skin_checked="1">
                                        <input type="date" class="form-control" name="starts_from" id="startsFrom">
                                    </div>
                                    <div class="col-6" bis_skin_checked="1">
                                        <input type="date" class="form-control" name="starts_to" id="startsTo">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3" bis_skin_checked="1">
                            <div class="filter-section" bis_skin_checked="1">
                                <h6 class="filter-section-title">تاريخ الانتهاء</h6>
                                <div class="row g-2" bis_skin_checked="1">
                                    <div class="col-6" bis_skin_checked="1">
                                        <input type="date" class="form-control" name="expires_from" id="expiresFrom">
                                    </div>
                                    <div class="col-6" bis_skin_checked="1">
                                        <input type="date" class="form-control" name="expires_to" id="expiresTo">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3" bis_skin_checked="1">
                            <div class="filter-section" bis_skin_checked="1">
                                <h6 class="filter-section-title">الاستخدامات</h6>
                                <div class="row g-2" bis_skin_checked="1">
                                    <div class="col-6" bis_skin_checked="1">
                                        <input type="number" class="form-control" placeholder="الحد الأقصى"
                                            name="max_uses" min="1">
                                    </div>
                                    <div class="col-6" bis_skin_checked="1">
                                        <input type="number" class="form-control" placeholder="لكل مستخدم"
                                            name="max_uses_per_user" min="1">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3" bis_skin_checked="1">
                            <div class="filter-section" bis_skin_checked="1">
                                <h6 class="filter-section-title">الترتيب</h6>
                                <select class="form-select" name="order_by">
                                    <option value="created_at">تاريخ الإنشاء</option>
                                    <option value="starts_at">تاريخ البدء</option>
                                    <option value="expires_at">تاريخ الانتهاء</option>
                                    <option value="value">القيمة</option>
                                    <option value="code">الكود</option>
                                </select>
                                <select class="form-select mt-2" name="order_dir">
                                    <option value="desc">تنازلي</option>
                                    <option value="asc">تصاعدي</option>
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
                                تم تحديد <span id="selectedCount">0</span> كوبون
                            </label>
                        </div>
                        <select class="form-select bulk-action-select" id="bulkActionSelect">
                            <option value="">اختر إجراء...</option>
                            <option value="activate">تفعيل</option>
                            <option value="deactivate">تعطيل</option>
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
            </div>
        </div>
    </div>

    <!-- Grid View -->
    <div id="gridView" class="view-container">
        @if ($coupons->count() > 0)
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4" id="couponsGrid">
                @foreach ($coupons as $coupon)
                    <div class="col">
                        <div class="coupon-card" data-coupon-id="{{ $coupon->id }}">
                            <div class="coupon-header">
                                <div class="coupon-code">{{ $coupon->code }}</div>
                                <div class="coupon-type">{{ $coupon->type === 'percentage' ? 'نسبة مئوية' : 'مبلغ ثابت' }}</div>
                            </div>

                            <div class="coupon-content">
                                <h6 class="coupon-title" title="{{ $coupon->name }}">
                                    {{ $coupon->name }}
                                </h6>

                                <p class="coupon-description">
                                    {{ $coupon->description ? Str::limit($coupon->description, 80) : 'لا يوجد وصف' }}
                                </p>

                                <div class="coupon-details">
                                    <div class="coupon-value type-{{ $coupon->type }}">
                                        {{ number_format($coupon->value, $coupon->type === 'percentage' ? 0 : 2) }}
                                    </div>
                                    
                                    @if ($coupon->min_order_amount)
                                        <div class="text-center text-muted small mb-2">
                                            الحد الأدنى: {{ number_format($coupon->min_order_amount, 2) }} ج.م
                                        </div>
                                    @endif
                                    
                                    <div class="coupon-meta">
                                        <div>
                                            <i class="fas fa-calendar-alt me-1"></i>
                                            {{ $coupon->starts_at ? $coupon->starts_at->format('Y/m/d') : 'لا يبدأ' }}
                                        </div>
                                        <div>
                                            <i class="fas fa-calendar-times me-1"></i>
                                            {{ $coupon->expires_at ? $coupon->expires_at->format('Y/m/d') : 'لا ينتهي' }}
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="usage-count 
                                            @if($coupon->max_uses && $coupon->usages()->count() >= $coupon->max_uses) high
                                            @elseif($coupon->max_uses && $coupon->usages()->count() >= $coupon->max_uses * 0.7) medium
                                            @else low @endif">
                                            {{ $coupon->usages()->count() }}
                                        </span>
                                        
                                        <span class="badge-status 
                                            @if(!$coupon->is_active) badge-inactive
                                            @elseif($coupon->expires_at && $coupon->expires_at->lt(now())) badge-expired
                                            @else badge-active @endif">
                                            @if(!$coupon->is_active) غير نشط
                                            @elseif($coupon->expires_at && $coupon->expires_at->lt(now())) منتهي
                                            @else نشط @endif
                                        </span>
                                    </div>
                                </div>

                                <div class="coupon-actions">
                                    <a href="{{ route('admin.coupons.show', $coupon->id) }}"
                                        class="btn btn-outline-info btn-sm" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.coupons.edit', $coupon->id) }}"
                                        class="btn btn-outline-primary btn-sm" title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-success btn-sm"
                                        onclick="copyCouponCode('{{ $coupon->code }}')" title="نسخ الكود">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm delete-coupon"
                                        data-id="{{ $coupon->id }}" data-name="{{ $coupon->name }}"
                                        title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <div class="form-check" style="padding-top: 5px;">
                                        <input class="form-check-input coupon-checkbox" type="checkbox"
                                            value="{{ $coupon->id }}" id="coupon_{{ $coupon->id }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if ($coupons->hasPages())
                <div class="m-3">
                    <nav>
                        <ul class="pagination">
                            {{-- Previous Page Link --}}
                            @if ($coupons->onFirstPage())
                                <li class="page-item disabled" aria-disabled="true">
                                    <span class="page-link waves-effect" aria-hidden="true">‹</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link waves-effect" href="{{ $coupons->previousPageUrl() }}"
                                        rel="prev">‹</a>
                                </li>
                            @endif

                            {{-- Pagination Elements --}}
                            @foreach ($coupons->links()->elements[0] as $page => $url)
                                @if ($page == $coupons->currentPage())
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
                            @if ($coupons->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link waves-effect" href="{{ $coupons->nextPageUrl() }}"
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
                <i class="fas fa-ticket-alt"></i>
                <h5>لا توجد كوبونات</h5>
                <p>ابدأ بإضافة كوبونات جديدة</p>
                <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> إضافة كوبون جديد
                </a>
            </div>
        @endif
    </div>

    <!-- Table View -->
    <div id="tableView" class="view-container" style="display: none;">
        <div class="card" bis_skin_checked="1">
            <div class="card-body" bis_skin_checked="1">
                <div class="table-responsive" bis_skin_checked="1">
                    <table class="table table-hover" id="couponsTable">
                        <thead>
                            <tr>
                                <th width="50">
                                    <div class="form-check" bis_skin_checked="1">
                                        <input class="form-check-input" type="checkbox" id="selectAllTable">
                                    </div>
                                </th>
                                <th width="120">الكود</th>
                                <th>الاسم</th>
                                <th>النوع</th>
                                <th>القيمة</th>
                                <th>الاستخدامات</th>
                                <th>تاريخ البدء</th>
                                <th>تاريخ الانتهاء</th>
                                <th>الحالة</th>
                                <th>تاريخ الإنشاء</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($coupons as $coupon)
                                <tr data-coupon-id="{{ $coupon->id }}">
                                    <td>
                                        <div class="form-check" bis_skin_checked="1">
                                            <input class="form-check-input coupon-checkbox" type="checkbox"
                                                value="{{ $coupon->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <span class="coupon-table-code">{{ $coupon->code }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold" bis_skin_checked="1">{{ $coupon->name }}</div>
                                        <small class="text-muted">{{ Str::limit($coupon->description, 50) }}</small>
                                    </td>
                                    <td>
                                        <span class="badge-type badge-type-{{ $coupon->type }}">
                                            {{ $coupon->type === 'percentage' ? 'نسبة مئوية' : 'مبلغ ثابت' }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong class="text-success">
                                            {{ number_format($coupon->value, $coupon->type === 'percentage' ? 0 : 2) }}
                                            {{ $coupon->type === 'percentage' ? '%' : 'ج.م' }}
                                        </strong>
                                        @if ($coupon->min_order_amount)
                                            <br>
                                            <small class="text-muted">
                                                حد أدنى: {{ number_format($coupon->min_order_amount, 2) }} ج.م
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2" bis_skin_checked="1">
                                            <span class="usage-count 
                                                @if($coupon->max_uses && $coupon->usages()->count() >= $coupon->max_uses) high
                                                @elseif($coupon->max_uses && $coupon->usages()->count() >= $coupon->max_uses * 0.7) medium
                                                @else low @endif">
                                                {{ $coupon->usages()->count() }}
                                            </span>
                                            @if ($coupon->max_uses)
                                                <small class="text-muted">/{{ $coupon->max_uses }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        {{ $coupon->starts_at ? $coupon->starts_at->format('Y/m/d') : '-' }}
                                    </td>
                                    <td>
                                        {{ $coupon->expires_at ? $coupon->expires_at->format('Y/m/d') : '-' }}
                                    </td>
                                    <td>
                                        @if(!$coupon->is_active)
                                            <span class="badge-status badge-inactive">غير نشط</span>
                                        @elseif($coupon->expires_at && $coupon->expires_at->lt(now()))
                                            <span class="badge-status badge-expired">منتهي</span>
                                        @else
                                            <span class="badge-status badge-active">نشط</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $coupon->created_at->format('Y/m/d') }}
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2" bis_skin_checked="1">
                                            <a href="{{ route('admin.coupons.show', $coupon->id) }}"
                                                class="btn btn-sm btn-outline-info" title="عرض">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.coupons.edit', $coupon) }}"
                                                class="btn btn-sm btn-outline-primary" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-success"
                                                onclick="copyCouponCode('{{ $coupon->code }}')" title="نسخ الكود">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                            <button type="button"
                                                class="btn btn-sm btn-outline-danger delete-coupon"
                                                data-id="{{ $coupon->id }}" data-name="{{ $coupon->name }}"
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
                @if ($coupons->hasPages())
                    <div class="m-3">
                        <nav>
                            <ul class="pagination">
                                {{-- Previous Page Link --}}
                                @if ($coupons->onFirstPage())
                                    <li class="page-item disabled" aria-disabled="true">
                                        <span class="page-link waves-effect" aria-hidden="true">‹</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link waves-effect" href="{{ $coupons->previousPageUrl() }}"
                                            rel="prev">‹</a>
                                    </li>
                                @endif

                                {{-- Pagination Elements --}}
                                @foreach ($coupons->links()->elements[0] as $page => $url)
                                    @if ($page == $coupons->currentPage())
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
                                @if ($coupons->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link waves-effect" href="{{ $coupons->nextPageUrl() }}"
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

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تصدير الكوبونات</h5>
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
                        <option value="id" selected>رقم الكوبون</option>
                        <option value="code" selected>الكود</option>
                        <option value="name" selected>الاسم</option>
                        <option value="type" selected>النوع</option>
                        <option value="value" selected>القيمة</option>
                        <option value="min_order_amount">الحد الأدنى</option>
                        <option value="max_uses">الحد الأقصى للاستخدام</option>
                        <option value="usages_count">عدد الاستخدامات</option>
                        <option value="starts_at">تاريخ البدء</option>
                        <option value="expires_at">تاريخ الانتهاء</option>
                        <option value="is_active">الحالة</option>
                        <option value="created_at">تاريخ الإنشاء</option>
                        <option value="description">الوصف</option>
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

        // Initialize DataTable
        $('#couponsTable').DataTable({
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

        // البحث عن طريق الـ Form
        $('#globalSearch').on('keyup', debounce(function() {
            const searchValue = $(this).val().trim();

            if (searchValue === '') {
                const url = new URL(window.location.href);
                url.searchParams.delete('search');
                url.searchParams.delete('page');
                window.location.href = url.toString();
                return;
            }

            $('#searchForm').submit();
        }, 500));

        // Clear search when clicking clear button
        $(document).on('click', '#clearSearch', function() {
            $('#globalSearch').val('');
            $('#searchForm').submit();
        });

        // Coupon checkbox selection
        $(document).on('change', '.coupon-checkbox', function() {
            updateSelectedCount();
        });

        // Select all checkboxes
        $('#selectAllBulk, #selectAllTable').on('change', function() {
            const isChecked = $(this).is(':checked');
            $('.coupon-checkbox').prop('checked', isChecked);
            updateSelectedCount();
        });

        // Delete coupon
        $(document).on('click', '.delete-coupon', function() {
            const couponId = $(this).data('id');
            const couponName = $(this).data('name');

            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: `سيتم حذف الكوبون "${couponName}" بشكل دائم`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteCoupon(couponId);
                }
            });
        });

        // View toggle buttons
        $('.view-toggle-btn').on('click', function() {
            const viewType = $(this).has('.fa-th-large').length ? 'grid' : 'table';
            toggleView(viewType);
        });

        // Fill search input with current search value
        @if (request('search'))
            $('#globalSearch').val('{{ request('search') }}');
        @endif
    });

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
        const url = new URL(window.location.href);
        url.searchParams.delete('page');

        url.searchParams.set(filter, value);
        window.location.href = url.toString();
    }

    window.clearFilters = function() {
        window.location.href = '{{ route('admin.coupons.index') }}';
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

        window.location.href = '{{ route('admin.coupons.index') }}?' + params.toString();
    }

    // Copy coupon code to clipboard
    window.copyCouponCode = function(code) {
        navigator.clipboard.writeText(code).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'تم النسخ!',
                text: `تم نسخ الكود: ${code}`,
                timer: 1500,
                showConfirmButton: false
            });
        });
    }

    // Bulk Actions Functions
    window.showBulkActions = function() {
        $('#bulkActions').addClass('show');
    }

    window.hideBulkActions = function() {
        $('#bulkActions').removeClass('show');
        $('.coupon-checkbox').prop('checked', false);
        updateSelectedCount();
    }

    function updateSelectedCount() {
        const selectedCount = $('.coupon-checkbox:checked').length;
        $('#selectedCount').text(selectedCount);

        if (selectedCount > 0) {
            $('#bulkActions').addClass('show');
        } else {
            $('#bulkActions').removeClass('show');
        }
    }

    window.applyBulkAction = function() {
        const action = $('#bulkActionSelect').val();
        const selectedCoupons = [];

        $('.coupon-checkbox:checked').each(function() {
            selectedCoupons.push($(this).val());
        });

        if (selectedCoupons.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'لم يتم الاختيار',
                text: 'يرجى اختيار كوبونات على الأقل',
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

        Swal.fire({
            title: 'تأكيد الإجراء',
            text: `سيتم تطبيق الإجراء على ${selectedCoupons.length} كوبون`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'تطبيق',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                performBulkAction(action, selectedCoupons);
            }
        });
    }

    function performBulkAction(action, couponIds) {
        $.ajax({
            url: '{{ route('admin.coupons.bulk-action') }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                action: action,
                coupon_ids: couponIds
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

    // Delete coupon function
    function deleteCoupon(couponId) {
        $.ajax({
            url: `/admin/coupons/${couponId}`,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم الحذف!',
                        text: 'تم حذف الكوبون بنجاح',
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

    // Export Functions
    window.exportCoupons = function() {
        $('#exportModal').modal('show');
    }

    window.performExport = function() {
        const type = $('#exportType').val();
        const columns = $('#exportColumns').val();
        const filters = new URLSearchParams(window.location.search);

        let url = '{{ route('admin.coupons.export') }}?';
        url += `type=${type}`;
        url += `&columns=${columns.join(',')}`;
        url += `&${filters.toString()}`;

        window.open(url, '_blank');
        $('#exportModal').modal('hide');
    }

    // Generate multiple coupons
    window.generateMultipleCoupons = function() {
        Swal.fire({
            title: 'إنشاء كوبونات متعددة',
            html: `
                <div class="mb-3">
                    <label class="form-label">عدد الكوبونات</label>
                    <input type="number" id="couponCount" class="form-control" min="1" max="100" value="5">
                </div>
                <div class="mb-3">
                    <label class="form-label">القيمة</label>
                    <input type="number" id="couponValue" class="form-control" step="0.01" min="0" value="10">
                </div>
                <div class="mb-3">
                    <label class="form-label">النوع</label>
                    <select id="couponType" class="form-select">
                        <option value="percentage">نسبة مئوية</option>
                        <option value="fixed">مبلغ ثابت</option>
                    </select>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'إنشاء',
            cancelButtonText: 'إلغاء',
            preConfirm: () => {
                const count = parseInt(document.getElementById('couponCount').value);
                const value = parseFloat(document.getElementById('couponValue').value);
                const type = document.getElementById('couponType').value;

                if (!count || count < 1 || count > 100) {
                    Swal.showValidationMessage('الرجاء إدخال عدد صحيح بين 1 و 100');
                    return false;
                }

                if (!value || value < 0) {
                    Swal.showValidationMessage('الرجاء إدخال قيمة صحيحة');
                    return false;
                }

                return { count, value, type };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: 'info',
                    title: 'قيد التطوير',
                    text: 'هذه الميزة قيد التطوير حالياً',
                    confirmButtonText: 'حسناً'
                });
            }
        });
    }
</script>
@endsection