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

        .status-shipped {
            background: rgba(12, 84, 96, 0.2);
            color: #0dcaf0;
            border: 1px solid rgba(13, 202, 240, 0.3);
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

        .status-refunded {
            background: rgba(56, 61, 65, 0.2);
            color: #adb5bd;
            border: 1px solid rgba(173, 181, 189, 0.3);
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

        .order-item.pending {
            border-color: #ffc107;
        }

        .order-item.processing {
            border-color: #0d6efd;
        }

        .order-item.shipped {
            border-color: #0dcaf0;
        }

        .order-item.delivered {
            border-color: #20c997;
        }

        .order-item.cancelled {
            border-color: #dc3545;
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
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
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

        .order-actions {
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
                <li class="breadcrumb-item active">الطلبات</li>
            </ol>
        </nav>

        <!-- الإحصائيات -->
        <div class="row mb-4" bis_skin_checked="1">
            <div class="col-lg-3 col-md-6 mb-4" bis_skin_checked="1">
                <div class="stats-card" bis_skin_checked="1">
                    <div class="stats-icon icon-total" bis_skin_checked="1">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stats-number" bis_skin_checked="1">
                        {{ number_format($stats['total']) }}
                    </div>
                    <div class="stats-label" bis_skin_checked="1">إجمالي الطلبات</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4" bis_skin_checked="1">
                <div class="stats-card" bis_skin_checked="1">
                    <div class="stats-icon icon-revenue" bis_skin_checked="1">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="stats-number" bis_skin_checked="1">
                        {{ number_format($stats['total_revenue'], 2) }} ج.م
                    </div>
                    <div class="stats-label" bis_skin_checked="1">إجمالي الإيرادات</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4" bis_skin_checked="1">
                <div class="stats-card" bis_skin_checked="1">
                    <div class="stats-icon icon-pending" bis_skin_checked="1">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stats-number" bis_skin_checked="1">
                        {{ number_format($stats['pending']) }}
                    </div>
                    <div class="stats-label" bis_skin_checked="1">طلبات قيد الانتظار</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4" bis_skin_checked="1">
                <div class="stats-card" bis_skin_checked="1">
                    <div class="stats-icon icon-delivered" bis_skin_checked="1">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stats-number" bis_skin_checked="1">
                        {{ number_format($stats['delivered']) }}
                    </div>
                    <div class="stats-label" bis_skin_checked="1">طلبات مكتملة</div>
                </div>
            </div>
        </div>

        <!-- فلترة حسب الحالة -->
        <div class="status-filter" bis_skin_checked="1">
            <button class="status-filter-btn {{ !request('status') ? 'active' : '' }}" onclick="filterByStatus('all')">
                جميع الطلبات
            </button>
            <button class="status-filter-btn {{ request('status') == 'pending' ? 'active' : '' }}"
                onclick="filterByStatus('pending')">
                <i class="fas fa-clock me-2"></i>قيد الانتظار
            </button>
            <button class="status-filter-btn {{ request('status') == 'processing' ? 'active' : '' }}"
                onclick="filterByStatus('processing')">
                <i class="fas fa-cog me-2"></i>تحت المعالجة
            </button>
            <button class="status-filter-btn {{ request('status') == 'shipped' ? 'active' : '' }}"
                onclick="filterByStatus('shipped')">
                <i class="fas fa-truck me-2"></i>تم الشحن
            </button>
            <button class="status-filter-btn {{ request('status') == 'delivered' ? 'active' : '' }}"
                onclick="filterByStatus('delivered')">
                <i class="fas fa-check-circle me-2"></i>تم التسليم
            </button>
            <button class="status-filter-btn {{ request('status') == 'cancelled' ? 'active' : '' }}"
                onclick="filterByStatus('cancelled')">
                <i class="fas fa-times-circle me-2"></i>ملغية
            </button>
        </div>

        <!-- فلترة متقدمة -->
        <div class="filter-card" bis_skin_checked="1">
            <h6 class="mb-3"><i class="fas fa-filter me-2"></i>فلترة متقدمة</h6>

            <div class="filter-row" bis_skin_checked="1">
                <div class="search-box" bis_skin_checked="1">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="form-control" placeholder="بحث برقم الطلب، الاسم، البريد..."
                        id="searchInput" value="{{ request('search') }}">
                </div>

                <div class="sort-dropdown" bis_skin_checked="1">
                    <button class="sort-btn">
                        <i class="fas fa-sort-amount-down"></i>
                        الترتيب حسب
                    </button>
                    <div class="sort-dropdown-content" bis_skin_checked="1">
                        <div class="sort-item {{ request('sort_by') == 'created_at' && request('sort_direction') == 'desc' ? 'active' : '' }}"
                            onclick="sortBy('created_at', 'desc')">
                            الأحدث أولاً
                        </div>
                        <div class="sort-item {{ request('sort_by') == 'created_at' && request('sort_direction') == 'asc' ? 'active' : '' }}"
                            onclick="sortBy('created_at', 'asc')">
                            الأقدم أولاً
                        </div>
                        <div class="sort-item {{ request('sort_by') == 'total_amount' && request('sort_direction') == 'desc' ? 'active' : '' }}"
                            onclick="sortBy('total_amount', 'desc')">
                            الأعلى سعراً
                        </div>
                        <div class="sort-item {{ request('sort_by') == 'total_amount' && request('sort_direction') == 'asc' ? 'active' : '' }}"
                            onclick="sortBy('total_amount', 'asc')">
                            الأقل سعراً
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
                <select class="form-select" id="paymentMethodFilter">
                    <option value="">جميع طرق الدفع</option>
                    <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>نقداً</option>
                    <option value="credit_card" {{ request('payment_method') == 'credit_card' ? 'selected' : '' }}>بطاقة
                        ائتمان</option>
                    <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>
                        تحويل بنكي</option>
                    <option value="wallet" {{ request('payment_method') == 'wallet' ? 'selected' : '' }}>محفظة إلكترونية
                    </option>
                </select>

                <div class="input-group" bis_skin_checked="1">
                    <input type="number" class="form-control" id="amountFrom" placeholder="من المبلغ"
                        value="{{ request('amount_from') }}">
                    <span class="input-group-text">إلى</span>
                    <input type="number" class="form-control" id="amountTo" placeholder="إلى المبلغ"
                        value="{{ request('amount_to') }}">
                </div>

                <button class="btn btn-primary" onclick="applyFilters()">
                    <i class="fas fa-filter me-2"></i>تطبيق الفلاتر
                </button>
                <button class="btn btn-outline-secondary" onclick="resetFilters()">
                    <i class="fas fa-redo me-2"></i>إعادة تعيين
                </button>
            </div>
        </div>

        <!-- قائمة الطلبات -->
        <div class="row" bis_skin_checked="1">
            <div class="col-12" bis_skin_checked="1">
                <div class="order-card" bis_skin_checked="1">
                    <div class="order-header" bis_skin_checked="1">
                        <div class="d-flex justify-content-between align-items-center" bis_skin_checked="1">
                            <div bis_skin_checked="1">
                                <h5 class="mb-0">قائمة الطلبات</h5>
                                <small class="opacity-75">إدارة جميع طلبات المتجر</small>
                            </div>
                            <div class="d-flex gap-3" bis_skin_checked="1">
                                <a href="{{ route('admin.orders.statistics') }}" class="btn btn-light">
                                    <i class="fas fa-chart-bar me-2"></i>الإحصائيات
                                </a>
                                <a href="#" class="btn btn-light">
                                    <i class="fas fa-plus me-2"></i>إضافة طلب جديد
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body" bis_skin_checked="1">
                        @if ($orders->isEmpty())
                            <div class="empty-state" bis_skin_checked="1">
                                <div class="empty-state-icon" bis_skin_checked="1">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                                <h5 class="empty-state-text">لا توجد طلبات</h5>
                                <p class="text-muted">لم يتم إنشاء أي طلبات حتى الآن</p>
                                <a href="{{ route('admin.orders.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>إنشاء طلب جديد
                                </a>
                            </div>
                        @else
                            @foreach ($orders as $order)
                                <div class="order-item {{ $order->status }}" bis_skin_checked="1">
                                    <div class="order-header-info" bis_skin_checked="1">
                                        <div class="order-title" bis_skin_checked="1">
                                            <div class="d-flex align-items-center gap-3" bis_skin_checked="1">
                                                <span>الطلب #{{ $order->order_number }}</span>
                                                <span class="badge-status status-{{ $order->status }}">
                                                    {{ $order->status_label }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="order-date" bis_skin_checked="1">
                                            <i class="far fa-clock me-1"></i>
                                            {{ $order->created_at->translatedFormat('d M Y - h:i A') }}
                                        </div>
                                    </div>

                                    <div class="order-details" bis_skin_checked="1">
                                        <div class="detail-item" bis_skin_checked="1">
                                            <span class="detail-label">العميل:</span>
                                            <span class="detail-value">
                                                {{ $order->customer_name }}
                                                @if ($order->user)
                                                    <small class="text-muted">({{ $order->user->email }})</small>
                                                @endif
                                            </span>
                                        </div>

                                        <div class="detail-item" bis_skin_checked="1">
                                            <span class="detail-label">المبلغ:</span>
                                            <span class="detail-value">
                                                {{ number_format($order->total_amount, 2) }} ج.م
                                            </span>
                                        </div>

                                        <div class="detail-item" bis_skin_checked="1">
                                            <span class="detail-label">طريقة الدفع:</span>
                                            <span class="detail-value">
                                                {{ $order->payment_method }}
                                            </span>
                                        </div>

                                        <div class="detail-item" bis_skin_checked="1">
                                            <span class="detail-label">العنوان:</span>
                                            <span class="detail-value">
                                                {{ Str::limit($order->shipping_address, 40) }}
                                            </span>
                                        </div>

                                        <div class="detail-item" bis_skin_checked="1">
                                            <span class="detail-label">عدد المنتجات:</span>
                                            <span class="detail-value">
                                                {{ $order->items->count() }} منتج
                                            </span>
                                        </div>

                                        <div class="detail-item" bis_skin_checked="1">
                                            <span class="detail-label">الهاتف:</span>
                                            <span class="detail-value">
                                                {{ $order->customer_phone }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="order-actions" bis_skin_checked="1">
                                        <a href="{{ route('admin.orders.show', $order->id) }}"
                                            class="btn btn-sm btn-info">
                                            <i class="fas fa-eye me-1"></i>عرض التفاصيل
                                        </a>
                                        <a href="{{ route('admin.orders.edit', $order) }}"
                                            class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit me-1"></i>تعديل
                                        </a>
                                        <a href="{{ route('admin.orders.print', $order) }}"
                                            class="btn btn-sm btn-secondary" target="_blank">
                                            <i class="fas fa-print me-1"></i>طباعة
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger delete-btn"
                                            data-id="{{ $order->id }}" data-name="{{ $order->order_number }}">
                                            <i class="fas fa-trash me-1"></i>حذف
                                        </button>
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
                                                    <a class="page-link waves-effect" href="{{ $orders->nextPageUrl() }}"
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
    <script>
        $(document).ready(function() {
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
                    text: `سيتم حذف الطلب "${orderName}" نهائياً`,
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
                date_from: $('#dateFrom').val(),
                date_to: $('#dateTo').val(),
                payment_method: $('#paymentMethodFilter').val(),
                amount_from: $('#amountFrom').val(),
                amount_to: $('#amountTo').val()
            };

            updateUrl(params);
        }

        function resetFilters() {
            // مسح جميع الحقول
            $('#searchInput').val('');
            $('#dateFrom').val('');
            $('#dateTo').val('');
            $('#paymentMethodFilter').val('');
            $('#amountFrom').val('');
            $('#amountTo').val('');

            // إعادة تحميل الصفحة بدون فلتر
            window.location.href = "{{ route('admin.orders.index') }}";
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
