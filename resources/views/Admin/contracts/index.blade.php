@extends('Admin.layout.master')

@section('title', 'إدارة العقود')

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

        .contract-card {
            background: var(--dark-card);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .contract-header {
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

        .status-expired {
            background: rgba(108, 117, 125, 0.2);
            color: #6c757d;
            border: 1px solid rgba(108, 117, 125, 0.3);
        }

        .status-pending {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }

        .status-cancelled {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
            border: 1px solid rgba(220, 53, 69, 0.3);
        }

        .contract-type-individual {
            background: rgba(23, 162, 184, 0.2);
            color: #17a2b8;
            border: 1px solid rgba(23, 162, 184, 0.3);
        }

        .contract-type-company {
            background: rgba(111, 66, 193, 0.2);
            color: #6f42c1;
            border: 1px solid rgba(111, 66, 193, 0.3);
        }

        .duration-badge {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            border-radius: 4px;
            padding: 3px 8px;
            font-size: 11px;
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

        .icon-expired {
            background: rgba(108, 117, 125, 0.2);
            color: #6c757d;
            border: 1px solid rgba(108, 117, 125, 0.3);
        }

        .icon-revenue {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
            border: 1px solid rgba(255, 193, 7, 0.3);
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

        .contract-item {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .contract-item:hover {
            transform: translateX(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            background: rgba(105, 108, 255, 0.1);
            border-color: var(--primary-color);
        }

        .contract-header-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .contract-number {
            font-weight: 700;
            color: var(--primary-color);
            font-size: 18px;
            direction: ltr;
            display: inline-block;
        }

        .contract-details {
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
            min-width: 100px;
        }

        .detail-value {
            color: rgba(255, 255, 255, 0.9);
        }

        .progress {
            height: 8px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-bar {
            background: var(--primary-gradient);
        }

        .contract-actions {
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

        .amount-text {
            font-weight: 600;
            color: #28a745;
        }

        .remaining-text {
            font-weight: 600;
            color: #dc3545;
        }

        .table {
            color: #fff;
        }

        .table thead th {
            color: rgba(255, 255, 255, 0.7);
            font-weight: 600;
        }

        .table td {
            vertical-align: middle;
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
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

        .bulk-actions {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-bottom: 20px;
        }

        .form-control, .form-select, .input-group-text {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .form-control:focus, .form-select:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--primary-color);
            color: #fff;
            box-shadow: 0 0 0 0.25rem rgba(105, 108, 255, 0.25);
        }

        .form-select option {
            background: var(--dark-card);
            color: #fff;
        }

        .input-group-text {
            color: rgba(255, 255, 255, 0.7);
        }

        .badge-payment-status {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
        }

        .payment-completed {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
        }

        .payment-partial {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
        }

        .payment-pending {
            background: rgba(108, 117, 125, 0.2);
            color: #6c757d;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .pagination {
            gap: 5px;
        }

        .page-link {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .page-link:hover {
            background: rgba(105, 108, 255, 0.1);
            border-color: var(--primary-color);
            color: #fff;
        }

        .page-item.active .page-link {
            background: var(--primary-gradient);
            border-color: var(--primary-color);
        }

        .page-item.disabled .page-link {
            background: rgba(255, 255, 255, 0.02);
            border-color: rgba(255, 255, 255, 0.05);
            color: rgba(255, 255, 255, 0.3);
        }

        @media (max-width: 768px) {
            .contract-header-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .contract-details {
                grid-template-columns: 1fr;
            }

            .contract-actions {
                flex-wrap: wrap;
            }

            .filter-row {
                grid-template-columns: 1fr;
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
                <li class="breadcrumb-item active">العقود</li>
            </ol>
        </nav>

        <!-- الإحصائيات -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stats-card">
                    <div class="stats-icon icon-total">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <div class="stats-number">
                        {{ number_format($stats['total'] ?? 0) }}
                    </div>
                    <div class="stats-label">إجمالي العقود</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stats-card">
                    <div class="stats-icon icon-active">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stats-number">
                        {{ number_format($stats['active'] ?? 0) }}
                    </div>
                    <div class="stats-label">عقود نشطة</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stats-card">
                    <div class="stats-icon icon-expired">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stats-number">
                        {{ number_format($stats['expiring_soon'] ?? 0) }}
                    </div>
                    <div class="stats-label">تنتهي قريباً</div>
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
        </div>

        <!-- فلترة حسب الحالة -->
        <div class="status-filter">
            <button class="status-filter-btn {{ !request('status') ? 'active' : '' }}" onclick="filterByStatus('all')">
                جميع العقود
            </button>
            <button class="status-filter-btn {{ request('status') == 'active' ? 'active' : '' }}"
                onclick="filterByStatus('active')">
                <i class="fas fa-check-circle me-2"></i>نشطة
            </button>
            <button class="status-filter-btn {{ request('status') == 'expired' ? 'active' : '' }}"
                onclick="filterByStatus('expired')">
                <i class="fas fa-times-circle me-2"></i>منتهية
            </button>
            <button class="status-filter-btn {{ request('status') == 'pending' ? 'active' : '' }}"
                onclick="filterByStatus('pending')">
                <i class="fas fa-hourglass-half me-2"></i>معلقة
            </button>
            <button class="status-filter-btn {{ request('status') == 'cancelled' ? 'active' : '' }}"
                onclick="filterByStatus('cancelled')">
                <i class="fas fa-ban me-2"></i>ملغية
            </button>
        </div>

        <!-- فلترة متقدمة -->
        <div class="filter-card">
            <h6 class="mb-3"><i class="fas fa-filter me-2"></i>فلترة متقدمة</h6>

            <form id="filterForm" method="GET" action="{{ route('admin.contracts.index') }}">
                <div class="filter-row">
                    <div class="search-box">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" class="form-control" name="search" placeholder="بحث برقم العقد أو اسم العميل..." 
                               value="{{ request('search') }}">
                    </div>

                    <select class="form-select" name="contract_type">
                        <option value="">نوع العقد</option>
                        <option value="individual" {{ request('contract_type') == 'individual' ? 'selected' : '' }}>فردي</option>
                        <option value="company" {{ request('contract_type') == 'company' ? 'selected' : '' }}>شركة</option>
                    </select>

                    <select class="form-select" name="duration_type">
                        <option value="">مدة العقد</option>
                        <option value="monthly" {{ request('duration_type') == 'monthly' ? 'selected' : '' }}>شهري</option>
                        <option value="quarterly" {{ request('duration_type') == 'quarterly' ? 'selected' : '' }}>ربع سنوي</option>
                        <option value="semi_annual" {{ request('duration_type') == 'semi_annual' ? 'selected' : '' }}>نصف سنوي</option>
                        <option value="annual" {{ request('duration_type') == 'annual' ? 'selected' : '' }}>سنوي</option>
                    </select>
                </div>

                <div class="filter-row">
                    <div class="input-group">
                        <input type="date" class="form-control" name="date_from" placeholder="من تاريخ" 
                               value="{{ request('date_from') }}">
                        <span class="input-group-text">إلى</span>
                        <input type="date" class="form-control" name="date_to" placeholder="إلى تاريخ" 
                               value="{{ request('date_to') }}">
                    </div>

                    <div class="input-group">
                        <input type="number" class="form-control" name="amount_from" placeholder="المبلغ من" 
                               value="{{ request('amount_from') }}" step="0.01">
                        <span class="input-group-text">إلى</span>
                        <input type="number" class="form-control" name="amount_to" placeholder="المبلغ إلى" 
                               value="{{ request('amount_to') }}" step="0.01">
                    </div>

                    <select class="form-select" name="user_id">
                        <option value="">جميع العملاء</option>
                        @foreach($users ?? [] as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} - {{ $user->phone }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-filter me-2"></i>تطبيق الفلاتر
                    </button>
                    <a href="{{ route('admin.contracts.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-redo me-2"></i>إعادة تعيين
                    </a>
                </div>
            </form>
        </div>

        <!-- قائمة العقود -->
        <div class="row">
            <div class="col-12">
                <div class="contract-card">
                    <div class="contract-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0">قائمة العقود</h5>
                                <small class="opacity-75">إدارة جميع عقود العملاء</small>
                            </div>
                            <div class="d-flex gap-3">
                                <a href="{{ route('admin.contracts.export') }}" class="btn btn-light" 
                                   onclick="event.preventDefault(); document.getElementById('export-form').submit();">
                                    <i class="fas fa-download me-2"></i>تصدير
                                </a>
                                <a href="{{ route('admin.contracts.create') }}" class="btn btn-light">
                                    <i class="fas fa-plus me-2"></i>عقد جديد
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- نموذج التصدير المخفي -->
                        <form id="export-form" action="{{ route('admin.contracts.export') }}" method="POST" class="d-none">
                            @csrf
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <input type="hidden" name="status" value="{{ request('status') }}">
                            <input type="hidden" name="contract_type" value="{{ request('contract_type') }}">
                            <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                            <input type="hidden" name="date_to" value="{{ request('date_to') }}">
                        </form>

                        <!-- إجراءات جماعية -->
                        <form id="bulkForm" method="POST" action="{{ route('admin.contracts.bulk-actions') }}">
                            @csrf
                            <div class="bulk-actions">
                                <select name="action" class="form-select" style="width: 200px;">
                                    <option value="">اختر إجراء</option>
                                    <option value="activate">تفعيل المحدد</option>
                                    <option value="deactivate">تعطيل المحدد</option>
                                    <option value="extend">تمديد المحدد</option>
                                    <option value="delete">حذف المحدد</option>
                                </select>
                                <button type="submit" class="btn btn-primary" onclick="return confirmBulkAction()">
                                    <i class="fas fa-play me-2"></i>تطبيق
                                </button>
                            </div>

                            @if($contracts->isEmpty())
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="fas fa-file-contract"></i>
                                    </div>
                                    <h5 class="empty-state-text">لا توجد عقود</h5>
                                    <p class="text-muted">لم يتم إنشاء أي عقود حتى الآن</p>
                                    <a href="{{ route('admin.contracts.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>إنشاء عقد جديد
                                    </a>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th width="50">
                                                    <input type="checkbox" id="selectAll" class="form-check-input">
                                                </th>
                                                <th>رقم العقد</th>
                                                <th>العميل</th>
                                                <th>النوع</th>
                                                <th>المدة</th>
                                                <th>المدة/الطلبات</th>
                                                <th>المبالغ</th>
                                                <th>الحالة</th>
                                                <th>تاريخ الانتهاء</th>
                                                <th>الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($contracts as $contract)
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" class="contract-checkbox form-check-input" 
                                                               name="ids[]" value="{{ $contract->id }}">
                                                    </td>
                                                    <td>
                                                        <strong class="contract-number">{{ $contract->contract_number }}</strong>
                                                    </td>
                                                    <td>
                                                        <div class="user-info">
                                                            @if($contract->user)
                                                                <div class="user-avatar d-flex align-items-center justify-content-center bg-secondary text-white">
                                                                    @if($contract->user->avatar)
                                                                        <img src="{{ asset('storage/' . $contract->user->avatar) }}" alt="{{ $contract->user->name }}" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                                                                    @else
                                                                        <i class="fas fa-user" style="font-size: 14px;"></i>
                                                                    @endif
                                                                </div>
                                                                <div>
                                                                    <strong class="d-block">{{ $contract->user->name }}</strong>
                                                                    <small class="text-muted">{{ $contract->user->phone }}</small>
                                                                </div>
                                                            @else
                                                                <span class="text-muted">محذوف</span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if($contract->contract_type == 'individual')
                                                            <span class="badge-status contract-type-individual">
                                                                <i class="fas fa-user me-1"></i>فردي
                                                            </span>
                                                        @else
                                                            <span class="badge-status contract-type-company">
                                                                <i class="fas fa-building me-1"></i>{{ $contract->company_name }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="duration-badge">
                                                            @switch($contract->duration_type)
                                                                @case('monthly')
                                                                    <i class="fas fa-calendar-alt me-1"></i>شهري
                                                                    @break
                                                                @case('quarterly')
                                                                    <i class="fas fa-calendar-alt me-1"></i>ربع سنوي
                                                                    @break
                                                                @case('semi_annual')
                                                                    <i class="fas fa-calendar-alt me-1"></i>نصف سنوي
                                                                    @break
                                                                @case('annual')
                                                                    <i class="fas fa-calendar-alt me-1"></i>سنوي
                                                                    @break
                                                            @endswitch
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex flex-column gap-1">
                                                            <div>
                                                                <small class="text-muted">الطلبات:</small>
                                                                <strong>{{ $contract->remaining_orders }}/{{ $contract->total_orders_limit }}</strong>
                                                            </div>
                                                            @if($contract->total_orders_limit > 0)
                                                                @php $percentage = ($contract->remaining_orders / $contract->total_orders_limit) * 100; @endphp
                                                                <div class="progress" style="width: 100px;">
                                                                    <div class="progress-bar" style="width: {{ $percentage }}%"></div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex flex-column gap-1">
                                                            <div>
                                                                <small class="text-muted">الإجمالي:</small>
                                                                <span class="amount-text">{{ number_format($contract->total_amount, 2) }} ر.س</span>
                                                            </div>
                                                            <div>
                                                                <small class="text-muted">المتبقي:</small>
                                                                <span class="remaining-text">{{ number_format($contract->remaining_amount, 2) }} ر.س</span>
                                                            </div>
                                                            @if($contract->paid_amount > 0)
                                                                @php $paidPercentage = ($contract->paid_amount / $contract->total_amount) * 100; @endphp
                                                                <div class="progress" style="width: 100px;">
                                                                    <div class="progress-bar bg-success" style="width: {{ $paidPercentage }}%"></div>
                                                                </div>
                                                                <small class="text-muted">{{ number_format($paidPercentage, 1) }}% مدفوع</small>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex flex-column gap-2">
                                                            <span class="badge-status status-{{ $contract->status }}">
                                                                @switch($contract->status)
                                                                    @case('active')
                                                                        <i class="fas fa-check-circle me-1"></i>نشط
                                                                        @break
                                                                    @case('expired')
                                                                        <i class="fas fa-clock me-1"></i>منتهي
                                                                        @break
                                                                    @case('pending')
                                                                        <i class="fas fa-hourglass-half me-1"></i>معلق
                                                                        @break
                                                                    @case('cancelled')
                                                                        <i class="fas fa-ban me-1"></i>ملغي
                                                                        @break
                                                                @endswitch
                                                            </span>
                                                            @if($contract->remaining_amount == 0)
                                                                <span class="badge-payment-status payment-completed">
                                                                    <i class="fas fa-check-circle me-1"></i>مدفوع بالكامل
                                                                </span>
                                                            @elseif($contract->paid_amount > 0)
                                                                <span class="badge-payment-status payment-partial">
                                                                    <i class="fas fa-clock me-1"></i>مدفوع جزئياً
                                                                </span>
                                                            @else
                                                                <span class="badge-payment-status payment-pending">
                                                                    <i class="fas fa-hourglass me-1"></i>غير مدفوع
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div>
                                                            <strong>{{ $contract->end_date?->format('Y-m-d') }}</strong>
                                                            @php
                                                                $daysLeft = $contract->end_date ? now()->diffInDays($contract->end_date, false) : 0;
                                                            @endphp
                                                            @if($contract->status == 'active' && $daysLeft > 0)
                                                                <br>
                                                                <small class="text-{{ $daysLeft <= 7 ? 'danger' : 'success' }}">
                                                                    متبقي {{ $daysLeft }} يوم
                                                                </small>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="contract-actions">
                                                            <a href="{{ route('admin.contracts.show', $contract->id) }}" 
                                                               class="btn btn-sm btn-info" title="عرض التفاصيل">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="{{ route('admin.contracts.edit', $contract->id) }}" 
                                                               class="btn btn-sm btn-warning" title="تعديل">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <a href="{{ route('admin.contracts.payments', $contract->id) }}" 
                                                               class="btn btn-sm btn-success" title="المدفوعات">
                                                                <i class="fas fa-money-bill"></i>
                                                            </a>
                                                            <a href="{{ route('admin.contracts.orders', $contract->id) }}" 
                                                               class="btn btn-sm btn-info" title="الطلبات">
                                                                <i class="fas fa-shopping-cart"></i>
                                                            </a>
                                                            <button type="button" 
                                                                    class="btn btn-sm {{ $contract->status == 'active' ? 'btn-secondary' : 'btn-success' }} toggle-status-btn"
                                                                    data-id="{{ $contract->id }}"
                                                                    title="{{ $contract->status == 'active' ? 'تعطيل' : 'تفعيل' }}">
                                                                <i class="fas fa-power-off"></i>
                                                            </button>
                                                            <button type="button" 
                                                                    class="btn btn-sm btn-primary extend-btn"
                                                                    data-id="{{ $contract->id }}"
                                                                    data-end-date="{{ $contract->end_date?->format('Y-m-d') }}"
                                                                    title="تمديد العقد">
                                                                <i class="fas fa-calendar-plus"></i>
                                                            </button>
                                                            <button type="button" 
                                                                    class="btn btn-sm btn-danger delete-btn"
                                                                    data-id="{{ $contract->id }}"
                                                                    data-number="{{ $contract->contract_number }}">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- ترتيب -->
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div class="sort-dropdown">
                                        <button class="sort-btn">
                                            <i class="fas fa-sort-amount-down"></i>
                                            الترتيب حسب
                                        </button>
                                        <div class="sort-dropdown-content">
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
                                                الأعلى مبلغاً
                                            </div>
                                            <div class="sort-item {{ request('sort_by') == 'total_amount' && request('sort_direction') == 'asc' ? 'active' : '' }}"
                                                 onclick="sortBy('total_amount', 'asc')">
                                                الأقل مبلغاً
                                            </div>
                                            <div class="sort-item {{ request('sort_by') == 'end_date' && request('sort_direction') == 'asc' ? 'active' : '' }}"
                                                 onclick="sortBy('end_date', 'asc')">
                                                الأقرب انتهاءً
                                            </div>
                                            <div class="sort-item {{ request('sort_by') == 'end_date' && request('sort_direction') == 'desc' ? 'active' : '' }}"
                                                 onclick="sortBy('end_date', 'desc')">
                                                الأبعد انتهاءً
                                            </div>
                                        </div>
                                    </div>

                                    <!-- معلومات العرض -->
                                    <div class="text-muted">
                                        عرض {{ $contracts->firstItem() ?? 0 }} - {{ $contracts->lastItem() ?? 0 }} من {{ $contracts->total() }} عقد
                                    </div>
                                </div>

                                <!-- Pagination -->
                                @if($contracts->hasPages())
                                    <div class="mt-4">
                                        <nav>
                                            <ul class="pagination justify-content-center">
                                                {{-- Previous Page Link --}}
                                                @if($contracts->onFirstPage())
                                                    <li class="page-item disabled">
                                                        <span class="page-link"><i class="fas fa-chevron-right"></i></span>
                                                    </li>
                                                @else
                                                    <li class="page-item">
                                                        <a class="page-link" href="{{ $contracts->previousPageUrl() }}" rel="prev">
                                                            <i class="fas fa-chevron-right"></i>
                                                        </a>
                                                    </li>
                                                @endif

                                                {{-- Pagination Elements --}}
                                                @foreach($contracts->getUrlRange(1, $contracts->lastPage()) as $page => $url)
                                                    @if($page == $contracts->currentPage())
                                                        <li class="page-item active">
                                                            <span class="page-link">{{ $page }}</span>
                                                        </li>
                                                    @else
                                                        <li class="page-item">
                                                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                                        </li>
                                                    @endif
                                                @endforeach

                                                {{-- Next Page Link --}}
                                                @if($contracts->hasMorePages())
                                                    <li class="page-item">
                                                        <a class="page-link" href="{{ $contracts->nextPageUrl() }}" rel="next">
                                                            <i class="fas fa-chevron-left"></i>
                                                        </a>
                                                    </li>
                                                @else
                                                    <li class="page-item disabled">
                                                        <span class="page-link"><i class="fas fa-chevron-left"></i></span>
                                                    </li>
                                                @endif
                                            </ul>
                                        </nav>
                                    </div>
                                @endif
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- مودال تمديد العقد -->
    <div class="modal fade" id="extendModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="background: var(--dark-card); color: #fff;">
                <div class="modal-header">
                    <h5 class="modal-title">تمديد العقد</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="extendForm" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">تاريخ الانتهاء الحالي</label>
                            <input type="text" class="form-control" id="currentEndDate" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">تاريخ الانتهاء الجديد <span class="text-danger">*</span></label>
                            <input type="date" name="new_end_date" class="form-control" required min="{{ now()->format('Y-m-d') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">سبب التمديد</label>
                            <textarea name="extension_reason" class="form-control" rows="3" placeholder="أدخل سبب التمديد..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary">تمديد العقد</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // اختيار الكل
            $('#selectAll').on('change', function() {
                $('.contract-checkbox').prop('checked', this.checked);
            });

            // البحث مع تأخير
            let searchTimeout;
            $('#searchInput').on('keyup', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    $('#filterForm').submit();
                }, 500);
            });

            // تبديل الحالة
            $('.toggle-status-btn').on('click', function() {
                const contractId = $(this).data('id');
                const btn = $(this);

                Swal.fire({
                    title: 'تغيير حالة العقد',
                    text: 'هل أنت متأكد من تغيير حالة هذا العقد؟',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'نعم، تغيير',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin.contracts.toggle-status', '') }}/" + contractId,
                            type: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                _method: 'PATCH'
                            },
                            success: function(response) {
                                if (response.success) {
                                    location.reload();
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'خطأ',
                                        text: response.message || 'حدث خطأ أثناء التحديث'
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'خطأ',
                                    text: xhr.responseJSON?.message || 'حدث خطأ أثناء التحديث'
                                });
                            }
                        });
                    }
                });
            });

            // تمديد العقد
            $('.extend-btn').on('click', function() {
                const contractId = $(this).data('id');
                const currentEndDate = $(this).data('end-date');
                
                $('#extendForm').attr('action', "{{ route('admin.contracts.extend', '') }}/" + contractId);
                $('#currentEndDate').val(currentEndDate);
                $('#extendModal').modal('show');
            });

            // حذف العقد
            $('.delete-btn').on('click', function() {
                const contractId = $(this).data('id');
                const contractNumber = $(this).data('number');

                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: `سيتم حذف العقد رقم "${contractNumber}" نهائياً`,
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
                            url: "{{ route('admin.contracts.destroy', '') }}/" + contractId,
                            type: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                _method: 'DELETE'
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'تم الحذف',
                                    text: response.success || 'تم حذف العقد بنجاح',
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
                                    text: xhr.responseJSON?.message || 'حدث خطأ أثناء الحذف'
                                });
                            }
                        });
                    }
                });
            });

            // رسائل التنبيه من الجلسة
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'نجاح',
                    text: "{{ session('success') }}",
                    timer: 2000,
                    showConfirmButton: false
                });
            @endif

            @if(session('error'))
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
            const checkedCount = document.querySelectorAll('.contract-checkbox:checked').length;

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
                    text: 'الرجاء اختيار عقود أولاً',
                    timer: 2000,
                    showConfirmButton: false
                });
                return false;
            }

            return confirm('هل أنت متأكد من تنفيذ هذا الإجراء على العقود المحددة؟');
        }

        function filterByStatus(status) {
            const url = new URL(window.location.href);
            if (status === 'all') {
                url.searchParams.delete('status');
            } else {
                url.searchParams.set('status', status);
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
    </script>
@endsection