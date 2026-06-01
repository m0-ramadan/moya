@extends('Admin.layout.master')

@section('title', 'إدارة الطلبات')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

        .order-card {
            background: var(--dark-card);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .order-header {
            background: var(--primary-gradient);
            color: white;
            padding: 25px 30px;
            border-radius: 15px 15px 0 0;
            margin: -30px -30px 20px -30px;
        }

        .badge-status {
            padding: 6px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 13px;
        }

        .status-pending {
            background: rgba(133, 100, 4, 0.2);
            color: #ffc107;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }

        .status-processing {
            background: rgba(0, 64, 133, 0.2);
            color: #0dcaf0;
            border: 1px solid rgba(13, 202, 240, 0.3);
        }

        .status-in-road {
            background: rgba(12, 84, 96, 0.2);
            color: #0dcaf0;
            border: 1px solid rgba(13, 202, 240, 0.3);
        }

        .status-scheduled {
            background: rgba(103, 58, 183, 0.2);
            color: #ab8ce4;
            border: 1px solid rgba(171, 140, 228, 0.3);
        }

        .status-delivered {
            background: linear-gradient(135deg, rgba(21, 87, 36, 0.2) 0%, rgba(32, 201, 151, 0.2) 100%);
            color: #20c997;
            border: 1px solid rgba(32, 201, 151, 0.3);
        }

        .status-cancelled {
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.2) 0%, rgba(253, 126, 20, 0.2) 100%);
            color: #fd7e14;
            border: 1px solid rgba(253, 126, 20, 0.3);
        }

        .payment-status {
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }

        .payment-pending {
            background: rgba(133, 100, 4, 0.2);
            color: #ffc107;
        }

        .payment-processing {
            background: rgba(0, 64, 133, 0.2);
            color: #0dcaf0;
        }

        .payment-paid {
            background: rgba(21, 87, 36, 0.2);
            color: #20c997;
        }

        .payment-failed {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
        }

        .payment-refunded {
            background: rgba(56, 61, 65, 0.2);
            color: #adb5bd;
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

        .icon-revenue {
            background: rgba(12, 99, 228, 0.2);
            color: #0c63e4;
            border: 1px solid rgba(12, 99, 228, 0.3);
        }

        .icon-pending {
            background: rgba(133, 100, 4, 0.2);
            color: #ffc107;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }

        .icon-delivered {
            background: linear-gradient(135deg, rgba(21, 87, 36, 0.2) 0%, rgba(32, 201, 151, 0.2) 100%);
            color: #20c997;
            border: 1px solid rgba(32, 201, 151, 0.3);
        }

        .icon-driver {
            background: rgba(111, 66, 193, 0.2);
            color: #6f42c1;
            border: 1px solid rgba(111, 66, 193, 0.3);
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

        .order-item {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            border-right: 4px solid transparent;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .order-item:hover {
            transform: translateX(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            background: rgba(105, 108, 255, 0.1);
            border-color: var(--primary-color);
        }

        .order-header-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .order-title {
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
        }

        .order-date {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.7);
        }

        .order-details {
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
            /* min-width: 90px; */
        }

        .detail-value {
            color: rgba(255, 255, 255, 0.9);
        }

        .driver-info {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .driver-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: white;
        }

        .order-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            flex-wrap: wrap;
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
            width: 100%;
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

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.3);
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            color: white;
        }

        .btn-outline-info {
            color: #0dcaf0;
            border-color: #0dcaf0;
        }

        .btn-outline-info:hover {
            background: #0dcaf0;
            color: white;
        }

        .btn-outline-success {
            color: #20c997;
            border-color: #20c997;
        }

        .btn-outline-success:hover {
            background: #20c997;
            color: white;
        }

        .pagination {
            justify-content: center;
            gap: 5px;
        }

        .page-link {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.8);
            border-radius: 8px !important;
            margin: 0 2px;
        }

        .page-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .page-item.active .page-link {
            background: var(--primary-gradient);
            border-color: var(--primary-color);
        }

        .page-item.disabled .page-link {
            background: rgba(255, 255, 255, 0.02);
            color: rgba(255, 255, 255, 0.3);
        }

        .badge {
            font-size: 11px;
            padding: 5px 8px;
        }

        .select2-container--default .select2-selection--single {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 25px;
            height: 40px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #fff;
            line-height: 40px;
            padding-right: 20px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px;
        }

        .select2-dropdown {
            background: var(--dark-card);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .select2-results__option {
            color: #fff;
        }

        .select2-results__option--highlighted {
            background: var(--primary-gradient) !important;
        }

        @media (max-width: 768px) {
            .order-header-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .order-details {
                grid-template-columns: 1fr;
            }

            .order-actions {
                flex-wrap: wrap;
            }

            .filter-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
    @php($adminUser = auth('admin')->user())
    <div class="container-xxl flex-grow-1 container-p-y" bis_skin_checked="1">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                     <a href="{{ route('admin.home') }}">الرئيسية</a>
                </li>
                <li class="breadcrumb-item active">الطلبات</li>
            </ol>
        </nav>

        <!-- الإحصائيات -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stats-card">
                    <div class="stats-icon icon-total">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stats-number">
                        {{ number_format($stats['total_orders'] ?? $stats['total']) }}
                    </div>
                    <div class="stats-label">إجمالي الطلبات</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stats-card">
                    <div class="stats-icon icon-revenue">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="stats-number">
                        {{ number_format($stats['total_revenue'] ?? 0, 2) }} ر.س
                    </div>
                    <div class="stats-label">إجمالي الإيرادات</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stats-card">
                    <div class="stats-icon icon-pending">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stats-number">
                        {{ number_format($stats['pending'] ?? 0) }}
                    </div>
                    <div class="stats-label">طلبات قيد الانتظار</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stats-card">
                    <div class="stats-icon icon-delivered">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stats-number">
                        {{ number_format($stats['delivered'] ?? 0) }}
                    </div>
                    <div class="stats-label">طلبات مكتملة</div>
                </div>
            </div>
        </div>

        <!-- فلترة حسب الحالة -->
        <div class="status-filter">
            <button class="status-filter-btn {{ !request('order_status_id') ? 'active' : '' }}" onclick="filterByStatus('all')">
                جميع الطلبات
            </button>
            @foreach($orderStatuses as $status)
                <button class="status-filter-btn {{ request('order_status_id') == $status->id ? 'active' : '' }}"
                    onclick="filterByStatus({{ $status->id }})">
                    @if($status->name == 'pending')
                        <i class="fas fa-clock me-2"></i>
                    @elseif($status->name == 'in-road')
                        <i class="fas fa-truck me-2"></i>
                    @elseif($status->name == 'scheduled')
                        <i class="fas fa-calendar-check me-2"></i>
                    @elseif($status->name == 'delivered')
                        <i class="fas fa-check-circle me-2"></i>
                    @elseif($status->name == 'cancelled')
                        <i class="fas fa-times-circle me-2"></i>
                    @endif
                    {{ $status->label }}
                </button>
            @endforeach
        </div>

        <!-- فلترة متقدمة -->
        <div class="filter-card">
            <h6 class="mb-3"><i class="fas fa-filter me-2"></i>فلترة متقدمة</h6>

            <div class="filter-row">
                <div class="search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="form-control" placeholder="بحث برقم الطلب، اسم العميل، رقم الجوال..."
                        id="searchInput" value="{{ request('search') }}">
                </div>

                <select class="form-select" id="serviceFilter">
                    <option value="">جميع الخدمات</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}" {{ request('service_id') == $service->id ? 'selected' : '' }}>
                            {{ $service->name }}
                        </option>
                    @endforeach
                </select>

                <select class="form-select" id="waterTypeFilter">
                    <option value="">جميع أنواع المياه</option>
                    @foreach($waterTypes as $type)
                        <option value="{{ $type->id }}" {{ request('water_type_id') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-row">
                <select class="form-select" id="paymentStatusFilter">
                    <option value="">حالة الدفع</option>
                    <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                    <option value="processing" {{ request('payment_status') == 'processing' ? 'selected' : '' }}>قيد المعالجة</option>
                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>مدفوع</option>
                    <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>فشل الدفع</option>
                    <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>مسترد</option>
                </select>

                <select class="form-select" id="driverFilter">
                    <option value="">جميع السائقين</option>
                    @foreach($drivers as $driver)
                        <option value="{{ $driver->id }}" {{ request('driver_id') == $driver->id ? 'selected' : '' }}>
                            {{ $driver->user->name ?? 'سائق #' . $driver->id }}
                        </option>
                    @endforeach
                </select>

                <div class="input-group">
                    <input type="date" class="form-control" id="dateFrom" placeholder="من تاريخ"
                        value="{{ request('date_from') }}">
                    <span class="input-group-text bg-transparent text-white border">إلى</span>
                    <input type="date" class="form-control" id="dateTo" placeholder="إلى تاريخ"
                        value="{{ request('date_to') }}">
                </div>
            </div>

            <div class="filter-row">
                <div class="input-group">
                    <input type="number" class="form-control" id="priceFrom" placeholder="من السعر"
                        value="{{ request('price_from') }}">
                    <span class="input-group-text bg-transparent text-white border">إلى</span>
                    <input type="number" class="form-control" id="priceTo" placeholder="إلى السعر"
                        value="{{ request('price_to') }}">
                </div>

                <div class="sort-dropdown">
                    <button class="sort-btn">
                        <i class="fas fa-sort-amount-down"></i>
                        {{ $sortLabels[request('sort_by', 'order_date') . '_' . request('sort_direction', 'desc')] ?? 'الترتيب حسب' }}
                    </button>
                    <div class="sort-dropdown-content">
                        <div class="sort-item {{ request('sort_by') == 'order_date' && request('sort_direction') == 'desc' ? 'active' : '' }}"
                            onclick="sortBy('order_date', 'desc')">
                            الأحدث أولاً
                        </div>
                        <div class="sort-item {{ request('sort_by') == 'order_date' && request('sort_direction') == 'asc' ? 'active' : '' }}"
                            onclick="sortBy('order_date', 'asc')">
                            الأقدم أولاً
                        </div>
                        <div class="sort-item {{ request('sort_by') == 'price' && request('sort_direction') == 'desc' ? 'active' : '' }}"
                            onclick="sortBy('price', 'desc')">
                            الأعلى سعراً
                        </div>
                        <div class="sort-item {{ request('sort_by') == 'price' && request('sort_direction') == 'asc' ? 'active' : '' }}"
                            onclick="sortBy('price', 'asc')">
                            الأقل سعراً
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button class="btn btn-primary flex-fill" onclick="applyFilters()">
                        <i class="fas fa-filter me-2"></i>تطبيق الفلاتر
                    </button>
                    <button class="btn btn-outline-secondary flex-fill" onclick="resetFilters()">
                        <i class="fas fa-redo me-2"></i>إعادة تعيين
                    </button>
                </div>
            </div>
        </div>

        <!-- قائمة الطلبات -->
        <div class="row">
            <div class="col-12">
                <div class="order-card">
                    <div class="order-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0">قائمة الطلبات</h5>
                                <small class="opacity-75">إدارة جميع طلبات التوصيل</small>
                            </div>
                            <div class="d-flex gap-2">
                                @if (admin_can_access_module('orders', 'view', $adminUser))
                                <a href="{{ route('admin.orders.statistics') }}" class="btn btn-light">
                                    <i class="fas fa-chart-bar me-2"></i>الإحصائيات
                                </a>
                                @endif
                                @if (admin_can_access_module('orders', 'create', $adminUser))
                                <a href="{{ route('admin.orders.create') }}" class="btn btn-light">
                                    <i class="fas fa-plus me-2"></i>إضافة طلب جديد
                                </a>
                                @endif
                                @if (admin_can_access_module('orders', 'view', $adminUser))
                                <button class="btn btn-light" onclick="exportOrders()">
                                    <i class="fas fa-download me-2"></i>تصدير
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        @if ($orders->isEmpty())
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="fas fa-truck"></i>
                                </div>
                                <h5 class="empty-state-text">لا توجد طلبات</h5>
                                <p class="text-muted">لم يتم إنشاء أي طلبات حتى الآن</p>
                                @if (admin_can_access_module('orders', 'create', $adminUser))
                                <a href="{{ route('admin.orders.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>إنشاء طلب جديد
                                </a>
                                @endif
                            </div>
                        @else
                            @foreach ($orders as $order)
                                <div class="order-item">
                                    <div class="order-header-info">
                                        <div class="order-title">
                                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                                <span class="fw-bold">طلب #{{ $order->id }}</span>
                                                @if($order->status)
                                                    <span class="badge-status status-{{ $order->status->name }}">
                                                        {{ $order->status->label }}
                                                    </span>
                                                @endif
                                                <span class="payment-status payment-{{ $order->payment_status }}">
                                                    @switch($order->payment_status)
                                                        @case('pending')
                                                            قيد الانتظار
                                                            @break
                                                        @case('processing')
                                                            قيد المعالجة
                                                            @break
                                                        @case('paid')
                                                            مدفوع
                                                            @break
                                                        @case('failed')
                                                            فشل الدفع
                                                            @break
                                                        @case('refunded')
                                                            مسترد
                                                            @break
                                                        @default
                                                            {{ $order->payment_status }}
                                                    @endswitch
                                                </span>
                                            </div>
                                        </div>
                                        <div class="order-date">
                                            <i class="far fa-clock me-1"></i>
                                            {{ $order->order_date ? $order->order_date->translatedFormat('d M Y - h:i A') : $order->created_at->translatedFormat('d M Y - h:i A') }}
                                        </div>
                                    </div>

                                    <div class="order-details">
                                        <div class="detail-item">
                                            <span class="detail-label">العميل:</span>
                                            <span class="detail-value">
                                                <i class="fas fa-user me-1"></i>
                                                {{ $order->user->name ?? 'غير محدد' }}
                                                @if($order->user && $order->user->full_phone)
                                                    <br>
                                                    <small class="text-muted">
                                                        <i class="fas fa-phone me-1"></i>{{ $order->user->full_phone }}
                                                    </small>
                                                @endif
                                            </span>
                                        </div>

                                        <div class="detail-item">
                                            <span class="detail-label">السائق:</span>
                                            <span class="detail-value">
                                                @if($order->driver && $order->driver->user)
                                                    <div class="driver-info">
                                                        <div class="driver-avatar d-flex align-items-center justify-content-center bg-secondary text-white">
                                                            @if($order->driver && $order->driver->personal_photo)
                                                                <img src="{{ asset('storage/' . $order->driver->personal_photo) }}" alt="{{ $order->driver->user->name }}" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                                                            @else
                                                                <i class="fas fa-truck" style="font-size: 12px;"></i>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            {{ $order->driver->user->name }}
                                                            @if($order->driver->vehicle_plate_number)
                                                                <br>
                                                                <small class="text-muted">
                                                                    <i class="fas fa-car me-1"></i>{{ $order->driver->vehicle_plate_number }}
                                                                </small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-muted">لم يتم التعيين</span>
                                                @endif
                                            </span>
                                        </div>

                                        <div class="detail-item">
                                            <span class="detail-label">الخدمة:</span>
                                            <span class="detail-value">
                                                <i class="fas fa-cog me-1"></i>
                                                {{ $order->service->name ?? 'غير محدد' }}
                                            </span>
                                        </div>

                                        <div class="detail-item">
                                            <span class="detail-label">نوع المياه:</span>
                                            <span class="detail-value">
                                                <i class="fas fa-water me-1"></i>
                                                {{ $order->waterType->name ?? 'غير محدد' }}
                                            </span>
                                        </div>

                                        <div class="detail-item">
                                            <span class="detail-label">السعر:</span>
                                            <span class="detail-value fw-bold text-success">
                                                {{ number_format($order->acceptedOffer->price ?? 0, 2) }} ر.س
                                            </span>
                                        </div>

                                        <div class="detail-item">
                                            <span class="detail-label">طريقة الدفع:</span>
                                            <span class="detail-value">
                                                @switch($order->payment_method)
                                                    @case('wallet')
                                                        <i class="fas fa-wallet me-1"></i>محفظة
                                                        @break
                                                    @case('credit_card')
                                                        <i class="fas fa-credit-card me-1"></i>بطاقة ائتمان
                                                        @break
                                                    @case('mada')
                                                        <i class="fas fa-credit-card me-1"></i>مدى
                                                        @break
                                                    @case('apple_pay')
                                                        <i class="fab fa-apple-pay me-1"></i>Apple Pay
                                                        @break
                                                    @default
                                                        {{ $order->payment_method }}
                                                @endswitch
                                            </span>
                                        </div>

                                        @if($order->location)
                                        <div class="detail-item">
                                            <span class="detail-label">العنوان:</span>
                                            <span class="detail-value">
                                                <i class="fas fa-map-marker-alt me-1"></i>
                                                {{ Str::limit($order->location->address_details ?? 'عنوان محفوظ', 40) }}
                                            </span>
                                        </div>
                                        @endif
                                    </div>

                                    <div class="order-actions">
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye me-1"></i>تفاصيل
                                        </a>
                                        @if (admin_can_access_module('orders', 'edit', $adminUser))
                                        <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit me-1"></i>تعديل
                                        </a>
                                        @endif
                                        <a href="{{ route('admin.orders.print', $order) }}" class="btn btn-sm btn-secondary" target="_blank">
                                            <i class="fas fa-print me-1"></i>طباعة
                                        </a>
                                        @if($order->driver)
                                        <button type="button" class="btn btn-sm btn-outline-info" onclick="trackOrder({{ $order->id }})">
                                            <i class="fas fa-map-marked-alt me-1"></i>تتبع
                                        </button>
                                        @endif
                                        @if(!in_array($order->payment_status, ['paid', 'refunded']) && admin_can_access_module('orders', 'edit', $adminUser))
                                        <button type="button" class="btn btn-sm btn-outline-success" onclick="updatePaymentStatus({{ $order->id }})">
                                            <i class="fas fa-credit-card me-1"></i>تحديث الدفع
                                        </button>
                                        @endif
                                        @if(!$order->driver && $order->status->name == 'pending' && admin_can_access_module('orders', 'edit', $adminUser))
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="assignDriver({{ $order->id }})">
                                            <i class="fas fa-user-plus me-1"></i>تعيين سائق
                                        </button>
                                        @endif
                                        @if (admin_can_access_module('orders', 'delete', $adminUser))
                                        <button type="button" class="btn btn-sm btn-outline-danger delete-btn"
                                            data-id="{{ $order->id }}" data-name="طلب #{{ $order->id }}">
                                            <i class="fas fa-trash me-1"></i>حذف
                                        </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                            @if ($orders->hasPages())
                                <div class="m-3">
                                    <nav>
                                        <ul class="pagination">
                                            {{-- Previous Page Link --}}
                                            @if ($orders->onFirstPage())
                                                <li class="page-item disabled" aria-disabled="true">
                                                    <span class="page-link waves-effect" aria-hidden="true">‹</span>
                                                </li>
                                            @else
                                                <li class="page-item">
                                                    <a class="page-link waves-effect"
                                                        href="{{ $orders->previousPageUrl() }}" rel="prev">‹</a>
                                                </li>
                                            @endif

                                            {{-- Pagination Elements --}}
                                            @foreach ($orders->links()->elements[0] as $page => $url)
                                                @if ($page == $orders->currentPage())
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
                                            @if ($orders->hasMorePages())
                                                <li class="page-item">
                                                    <a class="page-link waves-effect"
                                                        href="{{ $orders->nextPageUrl() }}" rel="next">›</a>
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('#serviceFilter, #waterTypeFilter, #paymentStatusFilter, #driverFilter').select2({
                theme: 'default',
                width: '100%',
                dropdownParent: $('body')
            });

            // البحث مع تأخير
            let searchTimeout;
            $('#searchInput').on('keyup', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    applyFilters();
                }, 500);
            });

            // حذف الطلب
            $('.delete-btn').on('click', function() {
                const orderId = $(this).data('id');
                const orderName = $(this).data('name');

                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: `سيتم حذف ${orderName} نهائياً`,
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
                            url: "{{ route('admin.orders.destroy', '') }}/" + orderId,
                            type: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                _method: 'DELETE'
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'تم الحذف',
                                    text: response.message || 'تم حذف الطلب بنجاح',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'خطأ',
                                    text: xhr.responseJSON?.message || 'حدث خطأ أثناء الحذف',
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

        function filterByStatus(status) {
            const url = new URL(window.location.href);
            if (status === 'all') {
                url.searchParams.delete('order_status_id');
            } else {
                url.searchParams.set('order_status_id', status);
            }
            url.searchParams.set('page', '1');
            window.location.href = url.toString();
        }

        function sortBy(sortBy, sortDirection) {
            const url = new URL(window.location.href);
            url.searchParams.set('sort_by', sortBy);
            url.searchParams.set('sort_direction', sortDirection);
            url.searchParams.set('page', '1');
            window.location.href = url.toString();
        }

        function applyFilters() {
            const params = {
                search: $('#searchInput').val(),
                service_id: $('#serviceFilter').val(),
                water_type_id: $('#waterTypeFilter').val(),
                payment_status: $('#paymentStatusFilter').val(),
                driver_id: $('#driverFilter').val(),
                date_from: $('#dateFrom').val(),
                date_to: $('#dateTo').val(),
                price_from: $('#priceFrom').val(),
                price_to: $('#priceTo').val()
            };

            updateUrl(params);
        }

        function resetFilters() {
            window.location.href = "{{ route('admin.orders.index') }}";
        }

        function updateUrl(params) {
            const url = new URL(window.location.href);
            
            Object.keys(params).forEach(key => {
                if (params[key] === null || params[key] === '') {
                    url.searchParams.delete(key);
                } else {
                    url.searchParams.set(key, params[key]);
                }
            });

            url.searchParams.set('page', '1');
            window.location.href = url.toString();
        }

        function trackOrder(orderId) {
            window.open("{{ route('admin.orders.tracking', '') }}/" + orderId, '_blank');
        }

        function updatePaymentStatus(orderId) {
            Swal.fire({
                title: 'تحديث حالة الدفع',
                html: `
                    <select id="paymentStatus" class="form-control">
                        <option value="pending">قيد الانتظار</option>
                        <option value="processing">قيد المعالجة</option>
                        <option value="paid">مدفوع</option>
                        <option value="failed">فشل الدفع</option>
                        <option value="refunded">مسترد</option>
                    </select>
                `,
                showCancelButton: true,
                confirmButtonText: 'تحديث',
                cancelButtonText: 'إلغاء',
                reverseButtons: true,
                preConfirm: () => {
                    const status = $('#paymentStatus').val();
                    return $.ajax({
                        url: "{{ route('admin.orders.update-payment-status', '') }}/" + orderId,
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            payment_status: status
                        }
                    });
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم التحديث',
                        text: 'تم تحديث حالة الدفع بنجاح',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                }
            });
        }

        function assignDriver(orderId) {
            Swal.fire({
                title: 'تعيين سائق',
                html: `
                    <select id="driverSelect" class="form-control">
                        <option value="">اختر سائق...</option>
                        @foreach($drivers as $driver)
                            <option value="{{ $driver->id }}">
                                {{ $driver->user->name ?? 'سائق #' . $driver->id }}
                                @if($driver->vehicle_plate_number) - {{ $driver->vehicle_plate_number }} @endif
                            </option>
                        @endforeach
                    </select>
                    <input type="number" id="offerPrice" class="form-control mt-2" placeholder="السعر">
                    <input type="number" id="deliveryDuration" class="form-control mt-2" placeholder="مدة التوصيل (دقائق)">
                `,
                showCancelButton: true,
                confirmButtonText: 'تعيين',
                cancelButtonText: 'إلغاء',
                reverseButtons: true,
                preConfirm: () => {
                    const driverId = $('#driverSelect').val();
                    const price = $('#offerPrice').val();
                    const duration = $('#deliveryDuration').val();

                    if (!driverId) {
                        Swal.showValidationMessage('يرجى اختيار سائق');
                        return false;
                    }
                    if (!price || price <= 0) {
                        Swal.showValidationMessage('يرجى إدخال سعر صحيح');
                        return false;
                    }
                    if (!duration || duration <= 0) {
                        Swal.showValidationMessage('يرجى إدخال مدة توصيل صحيحة');
                        return false;
                    }

                    return $.ajax({
                        url: "{{ route('admin.orders.assign-driver', '') }}/" + orderId,
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            driver_id: driverId,
                            price: price,
                            delivery_duration_minutes: duration
                        }
                    });
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم التعيين',
                        text: result.value.message || 'تم تعيين السائق بنجاح',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                }
            });
        }

        function exportOrders() {
            const params = new URLSearchParams(window.location.search);
            window.location.href = "{{ route('admin.orders.export') }}?" + params.toString();
        }
    </script>
@endsection
