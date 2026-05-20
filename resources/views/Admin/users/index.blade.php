@extends('Admin.layout.master')

@section('title', 'إدارة المستخدمين')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: "Cairo", sans-serif !important;
        }

        /* Users Dashboard */
        .users-dashboard {
            padding: 20px 0;
        }

        /* Welcome Card */
        .welcome-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            padding: 30px;
            color: white;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .welcome-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .welcome-icon {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-left: 20px;
        }

        .welcome-content h3 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .welcome-content p {
            opacity: 0.9;
            margin-bottom: 0;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--bs-card-bg);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-left: 5px solid;
            cursor: pointer;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .stat-card.total {
            border-left-color: #696cff;
        }

        .stat-card.active {
            border-left-color: #198754;
        }

        .stat-card.inactive {
            border-left-color: #dc3545;
        }

        .stat-card.verified {
            border-left-color: #0dcaf0;
        }

        .stat-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-left: 15px;
            color: white;
        }

        .stat-card.total .stat-icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .stat-card.active .stat-icon {
            background: linear-gradient(135deg, #198754 0%, #20c997 100%);
        }

        .stat-card.inactive .stat-icon {
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
        }

        .stat-card.verified .stat-icon {
            background: linear-gradient(135deg, #0dcaf0 0%, #0d6efd 100%);
        }

        .stat-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--bs-heading-color);
            margin-bottom: 5px;
        }

        .stat-description {
            font-size: 13px;
            color: var(--bs-secondary-color);
        }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 10px;
        }

        /* Filter Card */
        .filter-card {
            background: var(--bs-card-bg);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .filter-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--bs-border-color);
        }

        .filter-icon {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-left: 15px;
        }

        .filter-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--bs-heading-color);
            margin-bottom: 5px;
        }

        .filter-subtitle {
            color: var(--bs-secondary-color);
            font-size: 14px;
        }

        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .filter-group {
            flex: 1 1 200px;
        }

        .filter-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--bs-heading-color);
            margin-bottom: 5px;
        }

        .filter-select, .filter-input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid var(--bs-border-color);
            border-radius: 8px;
            background: var(--bs-card-bg);
            color: var(--bs-heading-color);
        }

        .filter-actions {
            display: flex;
            gap: 10px;
            align-items: flex-end;
            flex: 1 1 200px;
        }

        .btn-filter {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: opacity 0.3s;
        }

        .btn-filter:hover {
            opacity: 0.9;
            color: white;
        }

        .btn-reset {
            background: var(--bs-secondary-bg);
            color: var(--bs-heading-color);
            border: 1px solid var(--bs-border-color);
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-reset:hover {
            background: var(--bs-border-color);
        }

        /* Table Card */
        .table-card {
            background: var(--bs-card-bg);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .table-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .table-title {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .table-icon {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .table-title h5 {
            font-size: 18px;
            font-weight: 600;
            color: var(--bs-heading-color);
            margin-bottom: 5px;
        }

        .table-title p {
            color: var(--bs-secondary-color);
            font-size: 14px;
            margin-bottom: 0;
        }

        .table-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .search-box {
            position: relative;
            width: 250px;
        }

        .search-box input {
            width: 100%;
            padding: 10px 40px 10px 15px;
            border: 1px solid var(--bs-border-color);
            border-radius: 25px;
            background: var(--bs-card-bg);
            color: var(--bs-heading-color);
        }

        .search-box i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--bs-secondary-color);
        }

        /* Users Table */
        .users-table {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: right;
            padding: 15px 10px;
            background: var(--bs-light-bg-subtle);
            color: var(--bs-heading-color);
            font-weight: 600;
            font-size: 14px;
            border-bottom: 2px solid var(--bs-border-color);
        }

        td {
            padding: 15px 10px;
            border-bottom: 1px solid var(--bs-border-color);
            vertical-align: middle;
        }

        /* User Info */
        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 18px;
            flex-shrink: 0;
            overflow: hidden;
        }

        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .user-details h6 {
            font-size: 15px;
            font-weight: 600;
            color: var(--bs-heading-color);
            margin-bottom: 3px;
        }

        .user-details span {
            font-size: 12px;
            color: var(--bs-secondary-color);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .user-details i {
            font-size: 11px;
        }

        /* Badges */
        .badge-phone-verified {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .badge-phone-verified.verified {
            background: var(--bs-success-bg-subtle);
            color: var(--bs-success-text);
        }

        .badge-phone-verified.unverified {
            background: var(--bs-warning-bg-subtle);
            color: var(--bs-warning-text);
        }

        .badge-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-status.active {
            background: var(--bs-success-bg-subtle);
            color: var(--bs-success-text);
        }

        .badge-status.inactive {
            background: var(--bs-secondary-bg-subtle);
            color: var(--bs-secondary-text);
        }

        .badge-driver {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .badge-driver.yes {
            background: var(--bs-info-bg-subtle);
            color: var(--bs-info-text);
        }

        .badge-driver.no {
            background: var(--bs-secondary-bg-subtle);
            color: var(--bs-secondary-text);
        }

        /* Auth Methods */
        .auth-methods {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }

        .auth-badge {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: white;
        }

        .auth-badge.email {
            background: #dc3545;
        }

        .auth-badge.google {
            background: #db4437;
        }

        .auth-badge.facebook {
            background: #4267B2;
        }

        .auth-badge.apple {
            background: #000000;
        }

        .auth-badge.phone {
            background: #198754;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-action {
            width: 35px;
            height: 35px;
            border-radius: 8px;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
            color: white;
        }

        .btn-action.view {
            background: linear-gradient(135deg, #0dcaf0 0%, #0d6efd 100%);
        }

        .btn-action.edit {
            background: linear-gradient(135deg, #fd7e14 0%, #ffc107 100%);
        }

        .btn-action.toggle {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        }

        .btn-action.wallet {
            background: linear-gradient(135deg, #6610f2 0%, #6f42c1 100%);
        }

        .btn-action.contracts {
            background: linear-gradient(135deg, #198754 0%, #20c997 100%);
        }

        .btn-action.orders {
            background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%);
        }

        .btn-action.notifications {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        }

        /* Pagination */
        .pagination-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--bs-border-color);
            flex-wrap: wrap;
            gap: 15px;
        }

        .pagination-details {
            color: var(--bs-secondary-color);
            font-size: 14px;
        }

        .pagination-links {
            display: flex;
            gap: 5px;
        }

        .page-link {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bs-card-bg);
            border: 1px solid var(--bs-border-color);
            color: var(--bs-heading-color);
            text-decoration: none;
            transition: all 0.3s;
        }

        .page-link:hover,
        .page-link.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: transparent;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 50px 20px;
        }

        .empty-icon {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: var(--bs-light-bg-subtle);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            color: var(--bs-secondary-color);
            margin: 0 auto 20px;
        }

        .empty-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--bs-heading-color);
            margin-bottom: 10px;
        }

        .empty-description {
            color: var(--bs-secondary-color);
            font-size: 14px;
            margin-bottom: 20px;
        }

        /* User Details Modal */
        .user-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .info-section {
            background: var(--bs-card-bg);
            border-radius: 10px;
            padding: 15px;
            border: 1px solid var(--bs-border-color);
        }

        .info-section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--bs-border-color);
        }

        .info-section-icon {
            width: 35px;
            height: 35px;
            border-radius: 8px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .info-section-title h6 {
            font-size: 16px;
            font-weight: 600;
            color: var(--bs-heading-color);
            margin-bottom: 0;
        }

        .info-row {
            display: flex;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .info-label {
            width: 120px;
            color: var(--bs-secondary-color);
        }

        .info-value {
            flex: 1;
            color: var(--bs-heading-color);
            font-weight: 500;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .welcome-header {
                flex-direction: column;
                text-align: center;
            }

            .welcome-icon {
                margin-left: 0;
                margin-bottom: 15px;
            }

            .stat-header {
                flex-direction: column;
                text-align: center;
            }

            .stat-icon {
                margin-left: 0;
                margin-bottom: 10px;
            }

            .table-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .search-box {
                width: 100%;
            }

            .table-actions {
                width: 100%;
            }

            .action-buttons {
                justify-content: center;
            }

            .user-info {
                flex-direction: column;
                text-align: center;
            }

            .user-details span {
                justify-content: center;
            }

            td {
                min-width: 200px;
            }

            td:first-child {
                min-width: 250px;
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
                <li class="breadcrumb-item active">إدارة المستخدمين</li>
            </ol>
        </nav>

        <div class="users-dashboard">
            <!-- Welcome Card -->
            <div class="welcome-card">
                <div class="welcome-header">
                    <div class="welcome-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="welcome-content">
                        <h3>إدارة المستخدمين</h3>
                        <p>من هنا يمكنك إدارة جميع المستخدمين المسجلين في النظام، وعرض بياناتهم وتفاصيلهم</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <p class="mb-0">يمكنك متابعة المستخدمين، تفعيل أو تعطيل حساباتهم، وإدارة محافظهم وعقودهم بكل سهولة.</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="mt-3">
                            <span class="badge bg-light text-dark me-2">
                                <i class="fas fa-clock me-1"></i> {{ now()->format('H:i') }}
                            </span>
                            <span class="badge bg-light text-dark">
                                <i class="fas fa-calendar me-1"></i> {{ now()->translatedFormat('l، d F Y') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card total">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <div class="stat-title">إجمالي المستخدمين</div>
                            <div class="stat-description">جميع المستخدمين المسجلين</div>
                        </div>
                    </div>
                    <div class="stat-value">{{ $totalUsers ?? 0 }}</div>
                    <div class="stat-actions">
                        <span class="badge bg-primary">إجمالي</span>
                        <span class="text-muted">{{ $newThisMonth ?? 0 }} هذا الشهر</span>
                    </div>
                </div>

                <div class="stat-card active">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <div class="stat-title">نشطون</div>
                            <div class="stat-description">حسابات نشطة حالياً</div>
                        </div>
                    </div>
                    <div class="stat-value">{{ $activeUsers ?? 0 }}</div>
                    <div class="stat-actions">
                        <span class="badge bg-success">نشط</span>
                        <span class="text-muted">{{ $activeToday ?? 0 }} نشط اليوم</span>
                    </div>
                </div>

                <div class="stat-card inactive">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="fas fa-ban"></i>
                        </div>
                        <div>
                            <div class="stat-title">غير نشطين</div>
                            <div class="stat-description">حسابات موقوفة أو محظورة</div>
                        </div>
                    </div>
                    <div class="stat-value">{{ $inactiveUsers ?? 0 }}</div>
                    <div class="stat-actions">
                        <span class="badge bg-danger">موقوف</span>
                        <span class="text-muted">{{ $suspendedToday ?? 0 }} اليوم</span>
                    </div>
                </div>

                <div class="stat-card verified">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div>
                            <div class="stat-title">محققين</div>
                            <div class="stat-description">رقم جوال موثق</div>
                        </div>
                    </div>
                    <div class="stat-value">{{ $verifiedUsers ?? 0 }}</div>
                    <div class="stat-actions">
                        <span class="badge bg-info">موثق</span>
                        <span class="text-muted">{{ $verifiedThisMonth ?? 0 }} هذا الشهر</span>
                    </div>
                </div>
            </div>

            <!-- Filter Card -->
            <div class="filter-card">
                <div class="filter-header">
                    <div class="filter-icon">
                        <i class="fas fa-filter"></i>
                    </div>
                    <div>
                        <h5 class="filter-title">فلترة المستخدمين</h5>
                        <p class="filter-subtitle">تصفية النتائج حسب المعايير التالية</p>
                    </div>
                </div>

                <form method="GET" action="{{ route('admin.users.index') }}" class="filter-form">
                    <div class="filter-group">
                        <label class="filter-label">الحالة</label>
                        <select name="status" class="filter-select">
                            <option value="">الكل</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label class="filter-label">توثيق الجوال</label>
                        <select name="phone_verified" class="filter-select">
                            <option value="">الكل</option>
                            <option value="1" {{ request('phone_verified') == '1' ? 'selected' : '' }}>موثق</option>
                            <option value="0" {{ request('phone_verified') == '0' ? 'selected' : '' }}>غير موثق</option>
                        </select>
                    </div>


                    <div class="filter-group">
                        <label class="filter-label">الإشعارات</label>
                        <select name="notifications" class="filter-select">
                            <option value="">الكل</option>
                            <option value="1" {{ request('notifications') == '1' ? 'selected' : '' }}>مفعلة</option>
                            <option value="0" {{ request('notifications') == '0' ? 'selected' : '' }}>معطلة</option>
                        </select>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn-filter">
                            <i class="fas fa-search me-2"></i> تطبيق الفلتر
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn-reset">
                            <i class="fas fa-redo me-2"></i> إعادة تعيين
                        </a>
                    </div>
                </form>
            </div>

            <!-- Users Table -->
            <div class="table-card">
                <div class="table-header">
                    <div class="table-title">
                        <div class="table-icon">
                            <i class="fas fa-list"></i>
                        </div>
                        <div>
                            <h5>قائمة المستخدمين</h5>
                            <p>عرض جميع المستخدمين المسجلين في النظام</p>
                        </div>
                    </div>

                    <div class="table-actions">
                        <div class="search-box">
                            <input type="text" id="searchInput" placeholder="بحث عن مستخدم..." value="{{ request('search') }}">
                            <i class="fas fa-search" id="searchBtn" style="cursor: pointer;"></i>
                        </div>
                    </div>
                </div>

                <div class="users-table">
                    <table>
                        <thead>
                            <tr>
                                <th>المستخدم</th>
                                <th>معلومات الاتصال</th>
                                <th>الحالة</th>
                                <th>السائق</th>
                                <th>الإشعارات</th>
                                <th>تاريخ التسجيل</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>
                                        <div class="user-info">
                                            <div class="user-avatar">
                                                @if($user->avatar)
                                                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}">
                                                @else
                                                    {{ substr($user->name, 0, 1) }}
                                                @endif
                                            </div>
                                            <div class="user-details">
                                                <h6>{{ $user->name }}</h6>
                                                <span>
                                                    <i class="fas fa-envelope"></i>
                                                    {{ $user->email ?? 'لا يوجد بريد' }}
                                                </span>
                                                @if($user->isPhoneVerified())
                                                    <span class="text-success">
                                                        <i class="fas fa-check-circle"></i> جوال موثق
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="contact-info">
                                            <div>
                                                <i class="fas fa-phone"></i>
                                                {{ $user->full_phone ?? $user->phone ?? 'غير محدد' }}
                                            </div>
                                            <div class="mt-1">
                                                <span class="badge-phone-verified {{ $user->isPhoneVerified() ? 'verified' : 'unverified' }}">
                                                    <i class="fas {{ $user->isPhoneVerified() ? 'fa-check' : 'fa-times' }}"></i>
                                                    {{ $user->isPhoneVerified() ? 'موثق' : 'غير موثق' }}
                                                </span>
                                            </div>
                                            @if($user->phone_verified_at)
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar"></i>
                                                    {{ $user->phone_verified_at->format('Y-m-d') }}
                                                </small>
                                            @endif
                                        </div>
                                    </td>

                                    <td>
                                        <span class="badge-status {{ $user->status }}">
                                            <i class="fas {{ $user->status == 'active' ? 'fa-circle' : 'fa-circle' }}"></i>
                                            {{ $user->status == 'active' ? 'نشط' : 'غير نشط' }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="badge-driver {{ $user->driver ? 'yes' : 'no' }}">
                                            <i class="fas {{ $user->driver ? 'fa-check' : 'fa-times' }}"></i>
                                            {{ $user->driver ? 'سائق' : 'عميل' }}
                                        </span>
                                        @if($user->driver)
                                            <div class="mt-1">
                                                <small class="text-muted">
                                                    <i class="fas fa-truck"></i>
                                                    {{ $user->driver->vehicle_size ?? 'غير محدد' }}
                                                </small>
                                            </div>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="text-center">
                                            <i class="fas {{ $user->allow_notifications ? 'fa-bell text-success' : 'fa-bell-slash text-danger' }}"></i>
                                            <span class="badge bg-{{ $user->allow_notifications ? 'success' : 'secondary' }}">
                                                {{ $user->allow_notifications ? 'مفعلة' : 'معطلة' }}
                                            </span>
                                        </div>
                                        <div class="mt-1">
                                            <small class="text-muted">
                                                <i class="fas fa-mobile"></i>
                                                {{ $user->deviceTokens->count() ?? 0 }} جهاز
                                            </small>
                                        </div>
                                    </td>

                                    <td>
                                        <div>
                                            {{ $user->created_at->format('Y-m-d') }}
                                        </div>
                                        <small class="text-muted">
                                            {{ $user->created_at->diffForHumans() }}
                                        </small>
                                        @if($user->email_verified_at)
                                            <div>
                                                <small class="text-success">
                                                    <i class="fas fa-check-circle"></i> بريد موثق
                                                </small>
                                            </div>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-action view" onclick="viewUser({{ $user->id }})" title="عرض التفاصيل">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            
                                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn-action edit" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <button class="btn-action toggle" onclick="toggleUserStatus({{ $user->id }}, '{{ $user->status }}')" title="تغيير الحالة">
                                                <i class="fas {{ $user->status == 'active' ? 'fa-pause' : 'fa-play' }}"></i>
                                            </button>
                                            
                                            <button class="btn-action wallet" onclick="viewUserWallet({{ $user->id }})" title="المحفظة">
                                                <i class="fas fa-wallet"></i>
                                            </button>
                                            
                                            @if($user->driver)
                                                <a href="{{ route('admin.drivers.details', $user->driver->id) }}" class="btn-action contracts" title="بيانات السائق">
                                                    <i class="fas fa-truck"></i>
                                                </a>
                                            @endif
                                            
                                            <button class="btn-action orders" onclick="viewUserOrders({{ $user->id }})" title="الطلبات">
                                                <i class="fas fa-shopping-cart"></i>
                                            </button>
                                            
                                            <button class="btn-action notifications" onclick="sendNotification({{ $user->id }})" title="إرسال إشعار">
                                                <i class="fas fa-bell"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8">
                                        <div class="empty-state">
                                            <div class="empty-icon">
                                                <i class="fas fa-users-slash"></i>
                                            </div>
                                            <h5 class="empty-title">لا يوجد مستخدمين</h5>
                                            <p class="empty-description">لم يتم تسجيل أي مستخدمين حتى الآن.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if(isset($users) && $users->hasPages())
                    <div class="pagination-info">
                        <div class="pagination-details">
                            عرض {{ $users->firstItem() }} - {{ $users->lastItem() }} من {{ $users->total() }} مستخدم
                        </div>
                        <div class="pagination-links">
                            {{ $users->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- User Details Modal -->
    <div class="modal fade" id="userDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">تفاصيل المستخدم</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="userDetailsContent">
                    <!-- سيتم تعبئتها عبر JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Send Notification Modal -->
    <div class="modal fade" id="sendNotificationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">إرسال إشعار</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="notificationForm">
                        @csrf
                        <input type="hidden" id="notificationUserId" name="user_id">
                        
                        <div class="mb-3">
                            <label class="form-label">عنوان الإشعار</label>
                            <input type="text" class="form-control" id="notificationTitle" name="title" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">نص الإشعار</label>
                            <textarea class="form-control" id="notificationBody" name="body" rows="3" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">بيانات إضافية (JSON)</label>
                            <textarea class="form-control" id="notificationData" name="data" rows="2" placeholder='{"key": "value"}'></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-primary" onclick="submitNotification()">إرسال</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();

            $('#searchInput').on('keypress', function(e) {
                if (e.which == 13) { // Enter key
                    const searchValue = $(this).val();
                    performSearch(searchValue);
                }
            });

            $('#searchBtn').on('click', function() {
                const searchValue = $('#searchInput').val();
                performSearch(searchValue);
            });

            function performSearch(search) {
                const url = new URL(window.location.href);
                if (search) {
                    url.searchParams.set('search', search);
                } else {
                    url.searchParams.delete('search');
                }
                window.location.href = url.toString();
            }
        });

        // View user details
        function viewUser(userId) {
            $.ajax({
                url: `/admin/users/${userId}`,
                type: 'GET',
                dataType: 'json',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                beforeSend: function() {
                    Swal.fire({
                        title: 'جاري التحميل...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(response) {
                    Swal.close();
                    
                    let content = `
                        <div class="user-info-grid">

                            <!-- المعلومات الشخصية -->
                            <div class="info-section">
                                <div class="info-section-title">
                                    <div class="info-section-icon"><i class="fas fa-user"></i></div>
                                    <h6>معلومات شخصية</h6>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">الاسم:</span>
                                    <span class="info-value">${response.name}</span>
                                </div>
      
                                <div class="info-row">
                                    <span class="info-label">رقم الجوال:</span>
                                    <span class="info-value">${response.full_phone || response.phone || '<span class="text-muted">غير محدد</span>'}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">نوع الحساب:</span>
                                    <span class="info-value"><span class="badge bg-secondary">${response.type || 'مستخدم'}</span></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">حالة الحساب:</span>
                                    <span class="info-value">
                                        <span class="badge ${response.status == 'active' ? 'bg-success' : 'bg-danger'}">
                                            ${response.status == 'active' ? '<i class="fas fa-check-circle me-1"></i>نشط' : '<i class="fas fa-times-circle me-1"></i>غير نشط'}
                                        </span>
                                    </span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">الإشعارات:</span>
                                    <span class="info-value">
                                        <i class="fas ${response.allow_notifications ? 'fa-bell text-success' : 'fa-bell-slash text-danger'}"></i>
                                        ${response.allow_notifications ? 'مفعلة' : 'معطلة'}
                                    </span>
                                </div>
                            </div>

                            <!-- التوثيق والتواريخ -->
                            <div class="info-section">
                                <div class="info-section-title">
                                    <div class="info-section-icon"><i class="fas fa-shield-halved"></i></div>
                                    <h6>التوثيق والتواريخ</h6>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">توثيق الجوال:</span>
                                    <span class="info-value">
                                        ${response.is_phone_verified
                                            ? `<span class="badge bg-success"><i class="fas fa-check me-1"></i>موثق</span> <small class="text-muted">${response.phone_verified_at}</small>`
                                            : '<span class="badge bg-warning text-dark">غير موثق</span>'}
                                    </span>
                                </div>


                                <div class="info-row">
                                    <span class="info-label">تاريخ التسجيل:</span>
                                    <span class="info-value">${response.created_at} <small class="text-muted">(${response.created_at_human})</small></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">آخر تحديث:</span>
                                    <span class="info-value">${response.updated_at}</span>
                                </div>
                            </div>

                            <!-- إحصائيات الطلبات -->
                            <div class="info-section">
                                <div class="info-section-title">
                                    <div class="info-section-icon"><i class="fas fa-chart-bar"></i></div>
                                    <h6>إحصائيات</h6>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">إجمالي الطلبات:</span>
                                    <span class="info-value"><strong>${response.orders_count || 0}</strong></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">مكتملة:</span>
                                    <span class="info-value"><span class="badge bg-success">${response.orders_completed || 0}</span></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">جارية:</span>
                                    <span class="info-value"><span class="badge bg-warning text-dark">${response.orders_pending || 0}</span></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">ملغاة:</span>
                                    <span class="info-value"><span class="badge bg-danger">${response.orders_cancelled || 0}</span></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">العقود:</span>
                                    <span class="info-value">${response.contracts_count || 0}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">المدفوعات:</span>
                                    <span class="info-value">${response.payments_count || 0}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">الأجهزة المسجلة:</span>
                                    <span class="info-value">${response.device_tokens_count || 0}</span>
                                </div>
                            </div>

                            <!-- المحفظة -->
                            ${response.wallet ? `
                            <div class="info-section">
                                <div class="info-section-title">
                                    <div class="info-section-icon"><i class="fas fa-wallet"></i></div>
                                    <h6>المحفظة</h6>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">الرصيد:</span>
                                    <span class="info-value"><strong class="text-success">${response.wallet.balance} ${response.wallet.currency}</strong></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">رصيد محجوز:</span>
                                    <span class="info-value"><span class="text-warning">${response.wallet.held_balance} ${response.wallet.currency}</span></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">حالة المحفظة:</span>
                                    <span class="info-value">
                                        <span class="badge ${response.wallet.status == 'active' ? 'bg-success' : 'bg-danger'}">
                                            ${response.wallet.status == 'active' ? 'نشطة' : 'معطلة'}
                                        </span>
                                    </span>
                                </div>
                            </div>
                            ` : ''}

                            <!-- بيانات السائق -->
                            ${response.driver ? `
                            <div class="info-section">
                                <div class="info-section-title">
                                    <div class="info-section-icon"><i class="fas fa-truck"></i></div>
                                    <h6>بيانات السائق</h6>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">نوع المركبة:</span>
                                    <span class="info-value">${response.driver.vehicle_size || '--'}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">رقم اللوحة:</span>
                                    <span class="info-value">${response.driver.vehicle_plate_number || '--'}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">توثيق السائق:</span>
                                    <span class="info-value">
                                        <span class="badge ${response.driver.is_verified ? 'bg-success' : 'bg-warning text-dark'}">
                                            ${response.driver.is_verified ? 'موثق' : 'غير موثق'}
                                        </span>
                                    </span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">حالة السائق:</span>
                                    <span class="info-value">
                                        <span class="badge ${response.driver.is_active ? 'bg-success' : 'bg-danger'}">
                                            ${response.driver.is_active ? 'نشط' : 'غير نشط'}
                                        </span>
                                    </span>
                                </div>
                            </div>
                            ` : ''}
                    `;
                    
                    $('#userDetailsContent').html(content);
                    $('#userDetailsModal').modal('show');
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ!',
                        text: xhr.responseJSON?.message || 'حدث خطأ أثناء تحميل البيانات'
                    });
                }
            });
        }

        // Toggle user status
        function toggleUserStatus(userId, currentStatus) {
            const newStatus = currentStatus == 'active' ? 'inactive' : 'active';
            const action = newStatus == 'active' ? 'تفعيل' : 'تعطيل';
            
            Swal.fire({
                title: `${action} المستخدم`,
                text: `هل أنت متأكد من ${action} هذا المستخدم؟`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: newStatus == 'active' ? '#198754' : '#dc3545',
                cancelButtonColor: '#3085d6',
                confirmButtonText: `نعم، ${action}`,
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/admin/users/${userId}/toggle-status`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            status: newStatus
                        },
                        beforeSend: function() {
                            Swal.fire({
                                title: 'جاري التغيير...',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'تم التغيير!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ!',
                                text: xhr.responseJSON?.message || 'حدث خطأ أثناء تغيير الحالة'
                            });
                        }
                    });
                }
            });
        }

        // View user wallet
        function viewUserWallet(userId) {
            $.ajax({
                url: `/admin/users/${userId}/wallet`,
                type: 'GET',
                beforeSend: function() {
                    Swal.fire({
                        title: 'جاري تحميل المحفظة...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(response) {
                    Swal.close();
                    
                    let content = `
                        <div class="wallet-info">
                            <h4 class="text-center mb-4">محفظة المستخدم</h4>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="info-section bg-primary text-white p-3 rounded">
                                        <h6>الرصيد الحالي</h6>
                                        <h3>${response.balance || 0} ${response.currency || 'SAR'}</h3>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-section bg-info text-white p-3 rounded">
                                        <h6>الرصيد المتاح</h6>
                                        <h3>${response.available_balance || 0} ${response.currency || 'SAR'}</h3>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-section bg-warning p-3 rounded">
                                        <h6>الرصيد المعلق</h6>
                                        <h3>${response.held_balance || 0} ${response.currency || 'SAR'}</h3>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-section bg-success text-white p-3 rounded">
                                        <h6>حالة المحفظة</h6>
                                        <h3>
                                            <span class="badge ${response.status == 'active' ? 'bg-light text-dark' : 'bg-secondary'}">
                                                ${response.status == 'active' ? 'نشطة' : 'معطلة'}
                                            </span>
                                        </h3>
                                    </div>
                                </div>
                            </div>

                            <div class="info-section mt-4">
                                <div class="info-section-title">
                                    <div class="info-section-icon"><i class="fas fa-sliders-h"></i></div>
                                    <h6>تحكم الإدارة في المحفظة</h6>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">نوع العملية</label>
                                        <select class="form-control" id="walletActionType">
                                            <option value="deposit">إضافة رصيد</option>
                                            <option value="withdrawal">سحب من الرصيد</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">المبلغ</label>
                                        <input type="number" min="0.01" step="0.01" class="form-control" id="walletActionAmount" placeholder="0.00">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">الوصف</label>
                                        <input type="text" class="form-control" id="walletActionDescription" placeholder="سبب العملية">
                                    </div>
                                </div>
                                <div class="mt-3 text-end">
                                    <button class="btn btn-primary" onclick="submitWalletTransaction(${response.user_id})">
                                        <i class="fas fa-check-circle"></i>
                                        تنفيذ العملية
                                    </button>
                                </div>
                            </div>
                            
                            <h5 class="mt-4">آخر المعاملات</h5>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>التاريخ</th>
                                            <th>النوع</th>
                                            <th>المبلغ</th>
                                            <th>الوصف</th>
                                            <th>الحالة</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                    `;
                    
                    if (response.ledger_entries && response.ledger_entries.length > 0) {
                        response.ledger_entries.forEach(entry => {
                            content += `
                                <tr>
                                    <td>${entry.formatted_date || entry.created_at}</td>
                                    <td>
                                        <span class="badge bg-${entry.direction == 'credit' ? 'success' : 'danger'}">
                                            ${entry.type_label || (entry.direction == 'credit' ? 'إيداع' : 'سحب')}
                                        </span>
                                    </td>
                                    <td>${entry.amount} ${response.currency || 'SAR'}</td>
                                    <td>${entry.description || '---'}</td>
                                    <td>
                                        <span class="badge bg-${entry.status == 'completed' ? 'success' : (entry.status == 'pending' ? 'warning text-dark' : (entry.status == 'processing' ? 'info text-dark' : 'danger'))}">
                                            ${entry.status_label || entry.status}
                                        </span>
                                        ${entry.can_review ? `
                                            <div class="mt-2 action-buttons justify-content-center">
                                                <button class="btn btn-sm btn-success" onclick="approveTransaction(${response.user_id || entry.owner_id}, ${entry.id})" title="موافقة"><i class="fas fa-check"></i></button>
                                                <button class="btn btn-sm btn-danger" onclick="rejectTransaction(${response.user_id || entry.owner_id}, ${entry.id})" title="رفض"><i class="fas fa-times"></i></button>
                                            </div>
                                        ` : ''}
                                    </td>
                                </tr>
                            `;
                        });
                    } else {
                        content += `
                            <tr>
                                <td colspan="5" class="text-center">لا توجد معاملات</td>
                            </tr>
                        `;
                    }
                    
                    content += `
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    `;
                    
                    $('#userDetailsContent').html(content);
                    $('#userDetailsModal').modal('show');
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ!',
                        text: xhr.responseJSON?.message || 'حدث خطأ أثناء تحميل المحفظة'
                    });
                }
            });
        }

        // Approve transaction
        function approveTransaction(userId, transactionId) {
            Swal.fire({
                title: 'موافقة على العملية',
                text: 'هل أنت متأكد من الموافقة على هذه العملية؟',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'نعم، أوافق',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    processTransactionAction(userId, transactionId, 'approve');
                }
            });
        }

        // Reject transaction
        function rejectTransaction(userId, transactionId) {
            Swal.fire({
                title: 'رفض العملية',
                text: 'هل أنت متأكد من رفض هذه العملية؟',
                icon: 'warning',
                input: 'text',
                inputLabel: 'سبب الرفض (اختياري)',
                inputPlaceholder: 'اكتب سبب الرفض',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'نعم، أرفض',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    processTransactionAction(userId, transactionId, 'reject', result.value || '');
                }
            });
        }

        function processTransactionAction(userId, transactionId, action, reason = '') {
            $.ajax({
                url: `/admin/users/${userId}/wallet/transaction/${transactionId}/${action}`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    reason: reason
                },
                beforeSend: function() {
                    Swal.fire({
                        title: 'جاري المعالجة...',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'تمت العملية بنجاح!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    // Reload the wallet view
                    viewUserWallet(userId);
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ!',
                        text: xhr.responseJSON?.message || 'حدث خطأ أثناء معالجة العملية'
                    });
                }
            });
        }

        function submitWalletTransaction(userId) {
            const type = $('#walletActionType').val();
            const amount = $('#walletActionAmount').val();
            const description = $('#walletActionDescription').val();

            if (!amount || parseFloat(amount) <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'تنبيه',
                    text: 'من فضلك أدخل مبلغًا صحيحًا'
                });
                return;
            }

            if (!description) {
                Swal.fire({
                    icon: 'warning',
                    title: 'تنبيه',
                    text: 'من فضلك أدخل وصف العملية'
                });
                return;
            }

            $.ajax({
                url: `/admin/users/${userId}/wallet/transaction`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    type: type,
                    amount: amount,
                    description: description
                },
                beforeSend: function() {
                    Swal.fire({
                        title: 'جاري تنفيذ العملية...',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'تمت العملية بنجاح!',
                        text: response.message,
                        timer: 1800,
                        showConfirmButton: false
                    });

                    viewUserWallet(userId);
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ!',
                        text: xhr.responseJSON?.message || 'حدث خطأ أثناء تنفيذ العملية'
                    });
                }
            });
        }

        // View user orders
        function viewUserOrders(userId) {
            window.location.href = `/admin/orders?user_id=${userId}`;
        }

        // Send notification
        function sendNotification(userId) {
            $('#notificationUserId').val(userId);
            $('#sendNotificationModal').modal('show');
        }

        function submitNotification() {
            const userId = $('#notificationUserId').val();
            const title = $('#notificationTitle').val();
            const body = $('#notificationBody').val();
            const data = $('#notificationData').val();

            if (!title || !body) {
                Swal.fire({
                    icon: 'warning',
                    title: 'تنبيه!',
                    text: 'الرجاء إدخال عنوان ونص الإشعار'
                });
                return;
            }

            let parsedData = {};
            if (data) {
                try {
                    parsedData = JSON.parse(data);
                } catch (e) {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ!',
                        text: 'البيانات الإضافية يجب أن تكون بصيغة JSON صحيحة'
                    });
                    return;
                }
            }

            $.ajax({
                url: `/admin/users/${userId}/send-notification`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    title: title,
                    body: body,
                    data: parsedData
                },
                beforeSend: function() {
                    Swal.fire({
                        title: 'جاري الإرسال...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم الإرسال!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    
                    $('#sendNotificationModal').modal('hide');
                    $('#notificationForm')[0].reset();
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ!',
                        text: xhr.responseJSON?.message || 'حدث خطأ أثناء إرسال الإشعار'
                    });
                }
            });
        }
    </script>
@endsection
