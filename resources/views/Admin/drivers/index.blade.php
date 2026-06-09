@extends('Admin.layout.master')

@section('title', 'إدارة السائقين')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: "Cairo", sans-serif !important;
        }

        /* Drivers Dashboard */
        .drivers-dashboard {
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

        .stat-card.pending {
            border-left-color: #fd7e14;
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

        .stat-card.pending .stat-icon {
            background: linear-gradient(135deg, #fd7e14 0%, #ffc107 100%);
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

        .filter-select {
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

        .btn-add {
            background: linear-gradient(135deg, #198754 0%, #20c997 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: opacity 0.3s;
        }

        .btn-add:hover {
            opacity: 0.9;
            color: white;
        }

        /* Drivers Table */
        .drivers-table {
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

        /* Driver Info */
        .driver-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .driver-avatar {
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
        }

        .driver-avatar img {
            width: 100%;
            height: 100%;
            border-radius: 12px;
            object-fit: cover;
        }

        .driver-details h6 {
            font-size: 15px;
            font-weight: 600;
            color: var(--bs-heading-color);
            margin-bottom: 3px;
        }

        .driver-details span {
            font-size: 12px;
            color: var(--bs-secondary-color);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .driver-details i {
            font-size: 11px;
        }

        /* Badges */
        .badge-citizenship {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .badge-citizenship.saudi {
            background: var(--bs-success-bg-subtle);
            color: var(--bs-success-text);
        }

        .badge-citizenship.resident {
            background: var(--bs-warning-bg-subtle);
            color: var(--bs-warning-text);
        }

        .badge-verification {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .badge-verification.verified {
            background: var(--bs-success-bg-subtle);
            color: var(--bs-success-text);
        }

        .badge-verification.pending {
            background: var(--bs-warning-bg-subtle);
            color: var(--bs-warning-text);
        }

        .badge-verification.rejected {
            background: var(--bs-danger-bg-subtle);
            color: var(--bs-danger-text);
        }

        .badge-verification.inactive {
            background: var(--bs-secondary-bg-subtle);
            color: var(--bs-secondary-text);
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

        .badge-status.suspended {
            background: var(--bs-danger-bg-subtle);
            color: var(--bs-danger-text);
        }

        .badge-status.pending {
            background: var(--bs-warning-bg-subtle);
            color: var(--bs-warning-text);
        }

        /* Vehicle Info */
        .vehicle-info {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .vehicle-size {
            font-size: 13px;
            font-weight: 600;
            color: var(--bs-heading-color);
        }

        .vehicle-plate {
            font-size: 12px;
            color: var(--bs-secondary-color);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .vehicle-ownership {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            background: var(--bs-light-bg-subtle);
            color: var(--bs-secondary-color);
        }

        /* Document Icons */
        .doc-icons {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }

        .doc-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: white;
            cursor: pointer;
            transition: opacity 0.3s;
        }

        .doc-icon:hover {
            opacity: 0.8;
        }

        .doc-icon.id {
            background: linear-gradient(135deg, #696cff 0%, #764ba2 100%);
        }

        .doc-icon.license {
            background: linear-gradient(135deg, #198754 0%, #20c997 100%);
        }

        .doc-icon.vehicle {
            background: linear-gradient(135deg, #fd7e14 0%, #ffc107 100%);
        }

        .doc-icon.photo {
            background: linear-gradient(135deg, #0dcaf0 0%, #0d6efd 100%);
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

        .btn-action.approve {
            background: linear-gradient(135deg, #198754 0%, #20c997 100%);
        }

        .btn-action.reject {
            background: linear-gradient(135deg, #dc3545 0%, #d63384 100%);
        }

        .btn-action.toggle {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        }

        .btn-action.wallet {
            background: linear-gradient(135deg, #6610f2 0%, #6f42c1 100%);
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

        /* Driver Details Modal */
        .driver-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .info-section {
            background: #22234b;
            border-radius: 10px;
            padding: 15px;
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

        .documents-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .document-card {
            background: var(--bs-card-bg);
            border-radius: 10px;
            padding: 10px;
            text-align: center;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .document-card:hover {
            transform: translateY(-3px);
        }

        .document-preview {
            width: 100%;
            height: 120px;
            border-radius: 8px;
            background: var(--bs-light-bg-subtle);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
            overflow: hidden;
        }

        .document-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .document-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--bs-heading-color);
            margin-bottom: 3px;
        }

        .document-type {
            font-size: 11px;
            color: var(--bs-secondary-color);
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
                flex-direction: column;
            }

            .btn-add {
                width: 100%;
                justify-content: center;
            }

            .action-buttons {
                justify-content: center;
            }

            .driver-info {
                flex-direction: column;
                text-align: center;
            }

            .driver-details span {
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
                     <a href="{{ route('admin.home') }}">الرئيسية</a>
                </li>
                <li class="breadcrumb-item active">إدارة السائقين</li>
            </ol>
        </nav>

        <div class="drivers-dashboard">
            <!-- Welcome Card -->
            <div class="welcome-card">
                <div class="welcome-header">
                    <div class="welcome-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <div class="welcome-content">
                        <h3> إدارة السائقين</h3>
                        <p>من هنا يمكنك إدارة جميع السائقين المسجلين في النظام، وعرض بياناتهم وتفاصيلهم</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <p class="mb-0">يمكنك متابعة طلبات التسجيل، التحقق من الوثائق، وتفعيل أو تعطيل حسابات السائقين بكل
                            سهولة.</p>
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
                            <div class="stat-title">إجمالي السائقين</div>
                            <div class="stat-description">جميع السائقين المسجلين</div>
                        </div>
                    </div>
                    <div class="stat-value">{{ $totalDrivers ?? 0 }}</div>
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
                            <div class="stat-description">سائقون متاحون حالياً</div>
                        </div>
                    </div>
                    <div class="stat-value">{{ $activeDrivers ?? 0 }}</div>
                    <div class="stat-actions">
                        <span class="badge bg-success">نشط</span>
                        <span class="text-muted">{{ $availableNow ?? 0 }} متاح الآن</span>
                    </div>
                </div>

                <div class="stat-card pending">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <div class="stat-title">في انتظار التحقق</div>
                            <div class="stat-description">طلبات تسجيل جديدة</div>
                        </div>
                    </div>
                    <div class="stat-value">{{ $pendingDrivers ?? 0 }}</div>
                    <div class="stat-actions">
                        <span class="badge bg-warning text-dark">بإنتظار المراجعة</span>
                        <span class="text-muted">{{ $pendingThisWeek ?? 0 }} هذا الأسبوع</span>
                    </div>
                </div>

                <div class="stat-card inactive">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="fas fa-ban"></i>
                        </div>
                        <div>
                            <div class="stat-title">غير نشطين</div>
                            <div class="stat-description">موقوفين أو محظورين</div>
                        </div>
                    </div>
                    <div class="stat-value">{{ $inactiveDrivers ?? 0 }}</div>
                    <div class="stat-actions">
                        <span class="badge bg-danger">موقوف</span>
                        <span class="text-muted">{{ $suspendedToday ?? 0 }} اليوم</span>
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
                        <h5 class="filter-title">فلترة السائقين</h5>
                        <p class="filter-subtitle">تصفية النتائج حسب المعايير التالية</p>
                    </div>
                </div>

                <form method="GET" action="{{ route('admin.drivers.index') }}" class="filter-form">
                    <div class="filter-group">
                        <label class="filter-label">الجنسية</label>
                        <select name="citizenship" class="filter-select">
                            <option value="">الكل</option>
                            <option value="saudi" {{ request('citizenship') == 'saudi' ? 'selected' : '' }}>سعودي</option>
                            <option value="resident" {{ request('citizenship') == 'resident' ? 'selected' : '' }}>مقيم
                            </option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label class="filter-label">حالة التحقق</label>
                        <select name="is_verified" class="filter-select">
                            <option value="">الكل</option>
                            <option value="1" {{ request('is_verified') == '1' ? 'selected' : '' }}>موثق</option>
                            <option value="0" {{ request('is_verified') == '0' ? 'selected' : '' }}>غير موثق</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label class="filter-label">الحالة</label>
                        <select name="status" class="filter-select">
                            <option value="">الكل</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد المراجعة
                            </option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط
                            </option>
                            <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>موقوف
                            </option>
                        </select>
                    </div>
                    @php
                        // get all vehicle sizes from the database
                        $vehicle_sizes = DB::table('services')->get();
                    @endphp
                    <div class="filter-group">
                        <label class="filter-label">نوع الخدمة</label>
                           <select name="vehicle_size" class="filter-select">
                            <option value="">الكل</option>
                            @foreach ($vehicle_sizes as $vehicle_size)
                                <option value="{{ $vehicle_size->name }}"
                                    {{ request('vehicle_size') == $vehicle_size->name ? 'selected' : '' }}>{{ $vehicle_size->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label class="filter-label">ملكية المركبة</label>
                        <select name="is_vehicle_owner" class="filter-select">
                            <option value="">الكل</option>
                            <option value="1" {{ request('is_vehicle_owner') == '1' ? 'selected' : '' }}>مالك
                            </option>
                            <option value="0" {{ request('is_vehicle_owner') == '0' ? 'selected' : '' }}>غير مالك
                            </option>
                        </select>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn-filter">
                            <i class="fas fa-search me-2"></i> تطبيق الفلتر
                        </button>
                        <a href="{{ route('admin.drivers.index') }}" class="btn-reset">
                            <i class="fas fa-redo me-2"></i> إعادة تعيين
                        </a>
                    </div>
                </form>
            </div>

            <!-- Drivers Table -->
            <div class="table-card">
                <div class="table-header">
                    <div class="table-title">
                        <div class="table-icon">
                            <i class="fas fa-list"></i>
                        </div>
                        <div>
                            <h5>قائمة السائقين</h5>
                            <p>عرض جميع السائقين المسجلين في النظام</p>
                        </div>
                    </div>

                    <div class="table-actions">
                        <div class="search-box">
                            <input type="text" id="searchInput" placeholder="بحث عن سائق..."
                                value="{{ request('search') }}">
                            <i class="fas fa-search"></i>
                        </div>
                        <a href="{{ route('admin.drivers.create') }}" class="btn-add">
                            <i class="fas fa-plus"></i>
                            <span>إضافة سائق جديد</span>
                        </a>
                    </div>
                </div>

                <div class="drivers-table">
                    <table>
                        <thead>
                            <tr>
                                <th>السائق</th>
                                <th>الجنسية</th>
                                <th>الوثائق</th>
                                <th>المركبة</th>
                                <th>الحالة</th>
                                <th>تاريخ الانضمام</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($drivers as $driver)
                                <tr>
                                    <td>
                                        <div class="driver-info">
                                            <div class="driver-avatar d-flex align-items-center justify-content-center bg-secondary text-white">
                                                @if ($driver->personal_photo)
                                                    <img src="{{ asset('storage/' . $driver->personal_photo) }}"
                                                        alt="{{ $driver->user?->name ?? 'غير متوفر' }}">
                                                @else
                                                    <i class="fas fa-truck"></i>
                                                @endif
                                            </div>
                                            <div class="driver-details">
                                                <h6>{{ $driver->user?->name ?? 'غير متوفر' }}</h6>
                                                <span>
                                                    <i class="fas fa-phone"></i>
                                                    {{ $driver->user->full_phone ?? ($driver->user->phone ?? 'غير متوفر') }}
                                                </span>
                                                {{-- <span>
                                                    <i class="fas fa-envelope"></i>
                                                    {{ $driver->user->email }}
                                                </span> --}}
                                                <span>
                                                    <i class="fas fa-id-card"></i>
                                                    {{ $driver->national_id ?? $driver->iqama_number }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <span
                                            class="badge-citizenship {{ $driver->citizenship == 'saudi' ? 'saudi' : 'resident' }}">
                                            <i
                                                class="fas {{ $driver->citizenship == 'saudi' ? 'fa-flag' : 'fa-passport' }} me-1"></i>
                                            {{ $driver->citizenship == 'saudi' ? 'سعودي' : 'مقيم' }}
                                        </span>
                                        @if ($driver->citizenship == 'resident')
                                            <div class="mt-2 small">
                                                <i class="fas fa-calendar-alt"></i>
                                                انتهاء الإقامة:
                                                {{ $driver->iqama_expiry_date ? $driver->iqama_expiry_date->format('Y-m-d') : 'غير محدد' }}
                                            </div>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="doc-icons">
                                            @if ($driver->id_image_front || $driver->id_image_back)
                                                <div class="doc-icon id"
                                                    onclick="viewDocument('{{ $driver->id_image_front }}', 'id-front')"
                                                    title="عرض الهوية">
                                                    <i class="fas fa-id-card"></i>
                                                </div>
                                            @endif

                                            @if ($driver->license_image_front || $driver->license_image_back)
                                                <div class="doc-icon license"
                                                    onclick="viewDocument('{{ $driver->license_image_front }}', 'license-front')"
                                                    title="عرض الرخصة">
                                                    <i class="fas fa-id-card"></i>
                                                </div>
                                            @endif

                                            @if ($driver->vehicle_registration_image)
                                                <div class="doc-icon vehicle"
                                                    onclick="viewDocument('{{ $driver->vehicle_registration_image }}', 'vehicle-reg')"
                                                    title="عرض رخصة السير">
                                                    <i class="fas fa-file-alt"></i>
                                                </div>
                                            @endif

                                            @if ($driver->personal_photo)
                                                <div class="doc-icon photo"
                                                    onclick="viewDocument('{{ $driver->personal_photo }}', 'personal')"
                                                    title="عرض الصورة الشخصية">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="mt-2 small">
                                            <span
                                                class="badge {{ $driver->license_expiry_date && $driver->license_expiry_date?->isPast() ? 'bg-danger' : 'bg-success' }}">
                                                <i class="fas fa-id-card"></i>
                                                رخصة: {{ $driver->license_number ?? 'غير محدد' }}
                                            </span>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="vehicle-info">
                                            <span class="vehicle-size">
                                                <i class="fas fa-truck"></i>
                                                {{ $driver->vehicle_size ?? '--' }}
                                            </span>
                                            <span class="vehicle-plate">
                                                <i class="fas fa-car"></i>
                                                {{ $driver->vehicle_plate_number }}
                                            </span>
                                            <span class="vehicle-ownership">
                                                {{ $driver->is_vehicle_owner ? 'مالك' : 'غير مالك' }}
                                            </span>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="mb-2">
                                            <span
                                                class="badge-verification 
                                                {{ $driver->is_verified ? 'verified' : ($driver->rejection_reason ? 'rejected' : 'pending') }}">
                                                <i
                                                    class="fas {{ $driver->is_verified ? 'fa-check-circle' : ($driver->rejection_reason ? 'fa-times-circle' : 'fa-clock') }}"></i>
                                                {{ $driver->is_verified ? 'موثق' : ($driver->rejection_reason ? 'مرفوض' : 'قيد المراجعة') }}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="badge-status {{ ($driver->user?->status ?? 'active') === 'banned' ? 'suspended' : $driver->status }}">
                                                <i
                                                    class="fas {{ ($driver->user?->status ?? 'active') === 'banned' ? 'fa-ban' : ($driver->status == 'active' ? 'fa-circle' : ($driver->status == 'suspended' ? 'fa-ban' : ($driver->status == 'pending' ? 'fa-clock' : 'fa-circle'))) }}"></i>
                                                {{ ($driver->user?->status ?? 'active') === 'banned' ? 'محظور' : ($driver->status == 'active' ? 'نشط' : ($driver->status == 'suspended' ? 'موقوف' : ($driver->status == 'pending' ? 'قيد المراجعة' : 'غير نشط'))) }}
                                            </span>
                                        </div>
                                    </td>

                                    <td>
                                        <div>
                                            {{ $driver->created_at->format('Y-m-d') }}
                                        </div>
                                        <small class="text-muted">
                                            {{ $driver->created_at->diffForHumans() }}
                                        </small>
                                    </td>

                                    <td>
                                        <div class="action-buttons">
                                            <button type="button" class="btn-action view" onclick="viewDriver({{ $driver->id }})"
                                                title="عرض التفاصيل">
                                                <i class="fas fa-eye"></i>
                                            </button>

                                            <a href="{{ route('admin.drivers.edit', $driver->id) }}"
                                                class="btn-action edit" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            @if (!$driver->is_verified && !$driver->rejection_reason)
                                                <button type="button" class="btn-action approve"
                                                    onclick="approveDriver({{ $driver->id }})" title="توثيق السائق">
                                                    <i class="fas fa-check"></i>
                                                </button>

                                                <button type="button" class="btn-action reject"
                                                    onclick="rejectDriver({{ $driver->id }})" title="رفض الطلب">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif

                                            <button type="button" class="btn-action wallet" onclick="viewWallet({{ $driver->id }})"
                                                title="المحفظة">
                                                <i class="fas fa-wallet"></i>
                                            </button>

                                            <button type="button" class="btn-action toggle"
                                                onclick="toggleStatus({{ $driver->id }}, '{{ $driver->user?->status ?? 'active' }}')"
                                                title="{{ ($driver->user?->status ?? 'active') === 'banned' ? 'فك حظر السائق' : 'حظر السائق' }}">
                                                <i
                                                    class="fas {{ ($driver->user?->status ?? 'active') === 'banned' ? 'fa-user-check' : 'fa-user-slash' }}"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">
                                        <div class="empty-state">
                                            <div class="empty-icon">
                                                <i class="fas fa-users-slash"></i>
                                            </div>
                                            <h5 class="empty-title">لا يوجد سائقين</h5>
                                            <p class="empty-description">لم يتم إضافة أي سائقين حتى الآن. يمكنك إضافة أول
                                                سائق الآن.</p>
                                            <a href="{{ route('admin.drivers.create') }}" class="btn-add"
                                                style="display: inline-flex;">
                                                <i class="fas fa-plus"></i>
                                                <span>إضافة سائق جديد</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if (isset($drivers) && $drivers->hasPages())
                    <div class="pagination-info">
                        <div class="pagination-details">
                            عرض {{ $drivers->firstItem() }} - {{ $drivers->lastItem() }} من {{ $drivers->total() }} سائق
                        </div>
                        <div class="pagination-links">
                            {{ $drivers->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Driver Details Modal -->
    <div class="modal fade" id="driverDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">تفاصيل السائق</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="driverDetailsContent">
                    <!-- سيتم تعبئتها عبر JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    <a type="button" class="btn btn-primary" href="{{ route('admin.drivers.details', $driver->id) }}">
                        عرض صفحة التفاصيل </a>

                </div>
            </div>
        </div>
    </div>

    <!-- Document Viewer Modal -->
    <div class="modal fade" id="documentViewerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">عرض المستند</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center" id="documentViewerContent">
                    <img src="" alt="Document" class="img-fluid" id="documentImage">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    <a href="#" class="btn btn-primary" id="downloadDocument" download>
                        <i class="fas fa-download"></i> تحميل
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();

            // Search functionality
            let searchTimer;
            $('#searchInput').on('keyup', function() {
                clearTimeout(searchTimer);
                const searchValue = $(this).val();

                searchTimer = setTimeout(() => {
                    performSearch(searchValue);
                }, 500);
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

        // View driver details
        function viewDriver(driverId) {
            $.ajax({
                url: `/admin/drivers/${driverId}`,
                type: 'GET',
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
                        <div class="driver-info-grid">
                            <div class="info-section">
                                <div class="info-section-title">
                                    <div class="info-section-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <h6>معلومات شخصية</h6>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">الاسم:</span>
                                    <span class="info-value">${response.user.name}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">البريد:</span>
                                    <span class="info-value">${response.user.email}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">الجوال:</span>
                                    <span class="info-value">${response.user.full_phone || response.user.phone}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">الجنسية:</span>
                                    <span class="info-value">${response.citizenship == 'saudi' ? 'سعودي' : response.country.name_ar}</span>
                                </div>
                                ${response.citizenship == 'saudi' ? 
                                    `<div class="info-row">
                                                                        <span class="info-label">الهوية:</span>
                                                                        <span class="info-value">${response.national_id}</span>
                                                                    </div>` : 
                                    `<div class="info-row">
                                                                        <span class="info-label">رقم الإقامة:</span>
                                                                        <span class="info-value">${response.iqama_number}</span>
                                                                    </div>
                                                                    <div class="info-row">
                                                                        <span class="info-label">انتهاء الإقامة:</span>
                                                                        <span class="info-value">${response.iqama_expiry_date}</span>
                                                                    </div>`
                                }
                                <div class="info-row">
                                    <span class="info-label">تاريخ الميلاد:</span>
                                    <span class="info-value">${response.date_of_birth}</span>
                                </div>
                            </div>

                            <div class="info-section">
                                <div class="info-section-title">
                                    <div class="info-section-icon">
                                        <i class="fas fa-id-card"></i>
                                    </div>
                                    <h6>معلومات الرخصة</h6>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">رقم الرخصة:</span>
                                    <span class="info-value">${response.license_number}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">انتهاء الرخصة:</span>
                                    <span class="info-value">${response.license_expiry_date}</span>
                                </div>
                            </div>

                            <div class="info-section">
                                <div class="info-section-title">
                                    <div class="info-section-icon">
                                        <i class="fas fa-truck"></i>
                                    </div>
                                    <h6>معلومات المركبة</h6>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">حجم المركبة:</span>
                                    <span class="info-value">${response.vehicle_size == 'small' ? 'صغيرة' : (response.vehicle_size == 'medium' ? 'متوسطة' : 'كبيرة')}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">ملكية المركبة:</span>
                                    <span class="info-value">${response.is_vehicle_owner ? 'مالك' : 'غير مالك'}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">رقم اللوحة:</span>
                                    <span class="info-value">${response.vehicle_plate_number}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">رقم التسجيل:</span>
                                    <span class="info-value">${response.vehicle_registration_number}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">رقم الاستمارة:</span>
                                    <span class="info-value">${response.vehicle_residency_number}</span>
                                </div>
                            </div>

                            <div class="info-section">
                                <div class="info-section-title">
                                    <div class="info-section-icon">
                                        <i class="fas fa-image"></i>
                                    </div>
                                    <h6>المستندات</h6>
                                </div>
                                
                                <div class="documents-grid">
                                    ${response.personal_photo ? `
                                                                        <div class="document-card" onclick="viewDocument('${response.personal_photo}', 'personal')">
                                                                            <div class="document-preview">
                                                                                <img src="/storage/${response.personal_photo}" alt="Personal Photo">
                                                                            </div>
                                                                            <div class="document-name">الصورة الشخصية</div>
                                                                            <div class="document-type">صورة شخصية</div>
                                                                        </div>
                                                                    ` : ''}
                                    
                                    ${response.id_image_front ? `
                                                                        <div class="document-card" onclick="viewDocument('${response.id_image_front}', 'id-front')">
                                                                            <div class="document-preview">
                                                                                <img src="/storage/${response.id_image_front}" alt="ID Front">
                                                                            </div>
                                                                            <div class="document-name">الهوية - وجه</div>
                                                                            <div class="document-type">${response.citizenship == 'saudi' ? 'هوية وطنية' : 'إقامة'}</div>
                                                                        </div>
                                                                    ` : ''}
                                    
                                    ${response.id_image_back ? `
                                                                        <div class="document-card" onclick="viewDocument('${response.id_image_back}', 'id-back')">
                                                                            <div class="document-preview">
                                                                                <img src="/storage/${response.id_image_back}" alt="ID Back">
                                                                            </div>
                                                                            <div class="document-name">الهوية - ظهر</div>
                                                                            <div class="document-type">${response.citizenship == 'saudi' ? 'هوية وطنية' : 'إقامة'}</div>
                                                                        </div>
                                                                    ` : ''}
                                    
                                    ${response.license_image_front ? `
                                                                        <div class="document-card" onclick="viewDocument('${response.license_image_front}', 'license-front')">
                                                                            <div class="document-preview">
                                                                                <img src="/storage/${response.license_image_front}" alt="License Front">
                                                                            </div>
                                                                            <div class="document-name">رخصة القيادة - وجه</div>
                                                                            <div class="document-type">رخصة قيادة</div>
                                                                        </div>
                                                                    ` : ''}
                                    
                                    ${response.license_image_back ? `
                                                                        <div class="document-card" onclick="viewDocument('${response.license_image_back}', 'license-back')">
                                                                            <div class="document-preview">
                                                                                <img src="/storage/${response.license_image_back}" alt="License Back">
                                                                            </div>
                                                                            <div class="document-name">رخصة القيادة - ظهر</div>
                                                                            <div class="document-type">رخصة قيادة</div>
                                                                        </div>
                                                                    ` : ''}
                                    
                                    ${response.vehicle_registration_image ? `
                                                                        <div class="document-card" onclick="viewDocument('${response.vehicle_registration_image}', 'vehicle-reg')">
                                                                            <div class="document-preview">
                                                                                <img src="/storage/${response.vehicle_registration_image}" alt="Vehicle Registration">
                                                                            </div>
                                                                            <div class="document-name">رخصة السير</div>
                                                                            <div class="document-type">استمارة مركبة</div>
                                                                        </div>
                                                                    ` : ''}
                                </div>
                            </div>

                            <div class="info-section">
                                <div class="info-section-title">
                                    <div class="info-section-icon">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                    <h6>إحصائيات</h6>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">عدد الطلبات:</span>
                                    <span class="info-value">${response.orders_count || 0}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">التقييم:</span>
                                    <span class="info-value">
                                        ${response.ratings_avg ? response.ratings_avg.toFixed(1) : 0} 
                                        <i class="fas fa-star text-warning"></i>
                                    </span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">تاريخ الانضمام:</span>
                                    <span class="info-value">${response.created_at}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">آخر تحديث:</span>
                                    <span class="info-value">${response.updated_at}</span>
                                </div>
                            </div>
                        </div>
                    `;

                    $('#driverDetailsContent').html(content);
                    $('#driverDetailsModal').modal('show');
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

        // View document
        function viewDocument(path, type) {
            const fullPath = `/storage/${path}`;
            $('#documentImage').attr('src', fullPath);
            $('#downloadDocument').attr('href', fullPath);
            $('#documentViewerModal').modal('show');
        }

        // Approve driver
        function approveDriver(driverId) {
            Swal.fire({
                title: 'توثيق السائق',
                text: 'هل أنت متأكد من توثيق هذا السائق؟',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#d33',
                confirmButtonText: 'نعم، وثق',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/admin/drivers/${driverId}/approve`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        beforeSend: function() {
                            Swal.fire({
                                title: 'جاري التوثيق...',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'تم التوثيق!',
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
                                text: xhr.responseJSON?.message || 'حدث خطأ أثناء التوثيق'
                            });
                        }
                    });
                }
            });
        }

        // Reject driver
        function rejectDriver(driverId) {
            Swal.fire({
                title: 'رفض الطلب',
                text: 'الرجاء إدخال سبب الرفض',
                input: 'textarea',
                inputPlaceholder: 'اكتب سبب الرفض...',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'رفض',
                cancelButtonText: 'إلغاء',
                inputValidator: (value) => {
                    if (!value) {
                        return 'يجب إدخال سبب الرفض';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/admin/drivers/${driverId}/reject`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            rejection_reason: result.value
                        },
                        beforeSend: function() {
                            Swal.fire({
                                title: 'جاري الرفض...',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'تم الرفض!',
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
                                text: xhr.responseJSON?.message || 'حدث خطأ أثناء الرفض'
                            });
                        }
                    });
                }
            });
        }

        // Toggle driver status
        function toggleStatus(driverId, currentStatus) {
            const newStatus = currentStatus === 'banned' ? 'active' : 'banned';
            const action = newStatus === 'active' ? 'فك حظر' : 'حظر';

            Swal.fire({
                title: `${action} السائق`,
                text: `هل أنت متأكد من ${action} هذا السائق؟`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: newStatus === 'active' ? '#198754' : '#dc3545',
                cancelButtonColor: '#3085d6',
                confirmButtonText: `نعم، ${action}`,
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/admin/drivers/${driverId}/toggle-status`,
                        type: 'POST',
                        dataType: 'json',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
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
                                text: xhr.responseJSON?.message || xhr.responseText || 'حدث خطأ أثناء تغيير الحالة'
                            });
                        }
                    });
                }
            });
        }

        // View driver wallet
        function viewWallet(driverId) {
            $.ajax({
                url: `/admin/drivers/${driverId}/wallet`,
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
                            <h4 class="text-center mb-4">محفظة السائق</h4>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="info-card bg-primary text-white p-3 rounded">
                                        <h6>الرصيد الحالي</h6>
                                        <h3>${response.balance} ${response.currency}</h3>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-card bg-warning p-3 rounded">
                                        <h6>الرصيد المعلق</h6>
                                        <h3>${response.held_balance} ${response.currency}</h3>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-card bg-success text-white p-3 rounded">
                                        <h6>إجمالي الأرباح</h6>
                                        <h3>${response.total_earnings} ${response.currency}</h3>
                                    </div>
                                </div>
                            </div>
                            
                            <h5 class="mt-4">آخر المعاملات</h5>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>التاريخ</th>
                                        <th>النوع</th>
                                        <th>المبلغ</th>
                                        <th>الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;

                    if (response.transactions && response.transactions.length > 0) {
                        response.transactions.forEach(transaction => {
                            content += `
                                <tr>
                                    <td>${transaction.created_at}</td>
                                    <td>${transaction.type == 'credit' ? 'إيداع' : 'سحب'}</td>
                                    <td>${transaction.amount} ${response.currency}</td>
                                    <td>
                                        <span class="badge bg-${transaction.status == 'completed' ? 'success' : (transaction.status == 'pending' ? 'warning' : 'danger')}">
                                            ${transaction.status == 'completed' ? 'مكتملة' : (transaction.status == 'pending' ? 'معلقة' : 'فاشلة')}
                                        </span>
                                    </td>
                                </tr>
                            `;
                        });
                    } else {
                        content += `
                            <tr>
                                <td colspan="4" class="text-center">لا توجد معاملات</td>
                            </tr>
                        `;
                    }

                    content += `
                                </tbody>
                            </table>
                        </div>
                    `;

                    $('#driverDetailsContent').html(content);
                    $('#driverDetailsModal').modal('show');
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
    </script>
@endsection
