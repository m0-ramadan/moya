@extends('Admin.layout.master')

@section('title', 'تفاصيل السائق')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        body {
            font-family: "Cairo", sans-serif !important;
        }

        /* Driver Profile */
        .driver-profile {
            padding: 20px 0;
        }

        /* Profile Header */
        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            padding: 30px;
            color: white;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            position: relative;
            overflow: hidden;
        }

        .profile-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .profile-header::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 250px;
            height: 250px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .profile-header-content {
            display: flex;
            align-items: center;
            gap: 30px;
            flex-wrap: wrap;
            position: relative;
            z-index: 2;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.2);
            border: 4px solid rgba(255, 255, 255, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            font-weight: 600;
            overflow: hidden;
        }

        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-info {
            flex: 1;
        }

        .profile-info h2 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .profile-info .profile-meta {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 10px;
        }

        .profile-info .profile-meta span {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            opacity: 0.9;
        }

        .profile-badges {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .profile-badge {
            padding: 8px 20px;
            border-radius: 25px;
            background: rgba(255, 255, 255, 0.2);
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }

        .profile-stats {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }

        .profile-stat {
            text-align: center;
            min-width: 100px;
        }

        .profile-stat-value {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .profile-stat-label {
            font-size: 13px;
            opacity: 0.8;
        }

        .profile-actions {
            display: flex;
            gap: 10px;
            margin-right: auto;
        }

        .btn-profile-action {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-profile-action:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-3px);
        }

        /* Navigation Tabs */
        .profile-tabs {
            background: var(--bs-card-bg);
            border-radius: 15px;
            padding: 10px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }

        .profile-tab {
            flex: 1;
            min-width: 120px;
            padding: 12px 20px;
            border-radius: 10px;
            background: transparent;
            border: none;
            color: var(--bs-secondary-color);
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .profile-tab i {
            font-size: 16px;
        }

        .profile-tab:hover {
            background: var(--bs-light-bg-subtle);
            color: var(--bs-heading-color);
        }

        .profile-tab.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        /* Tab Content */
        .tab-content {
            background: var(--bs-card-bg);
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            min-height: 500px;
        }

        .tab-pane {
            display: none;
        }

        .tab-pane.active {
            display: block;
        }

        /* Section Header */
        .section-header {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--bs-border-color);
        }

        .section-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-left: 15px;
        }

        .section-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--bs-heading-color);
            margin-bottom: 5px;
        }

        .section-description {
            color: var(--bs-secondary-color);
            font-size: 14px;
        }

        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-card {
            background: var(--bs-light-bg-subtle);
            border-radius: 15px;
            padding: 20px;
        }

        .info-card-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--bs-border-color);
        }

        .info-card-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .info-card-title h6 {
            font-size: 16px;
            font-weight: 600;
            color: var(--bs-heading-color);
            margin-bottom: 0;
        }

        .info-row {
            display: flex;
            margin-bottom: 12px;
            font-size: 14px;
        }

        .info-label {
            width: 130px;
            color: var(--bs-secondary-color);
            font-weight: 500;
        }

        .info-value {
            flex: 1;
            color: var(--bs-heading-color);
            font-weight: 600;
        }

        .info-value .badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
        }

        .badge-citizenship.saudi {
            background: var(--bs-success-bg-subtle);
            color: var(--bs-success-text);
        }

        .badge-citizenship.resident {
            background: var(--bs-warning-bg-subtle);
            color: var(--bs-warning-text);
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

        /* Documents Grid */
        .documents-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .document-card {
            background: var(--bs-card-bg);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            cursor: pointer;
            border: 2px solid transparent;
        }

        .document-card:hover {
            transform: translateY(-5px);
            border-color: #696cff;
            box-shadow: 0 10px 20px rgba(105, 108, 255, 0.2);
        }

        .document-preview {
            height: 160px;
            background: var(--bs-light-bg-subtle);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .document-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .document-preview i {
            font-size: 48px;
            color: var(--bs-secondary-color);
        }

        .document-info {
            padding: 15px;
        }

        .document-name {
            font-size: 14px;
            font-weight: 600;
            color: var(--bs-heading-color);
            margin-bottom: 3px;
        }

        .document-type {
            font-size: 12px;
            color: var(--bs-secondary-color);
        }

        .document-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 5px 10px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.9);
            color: #333;
            font-size: 11px;
            font-weight: 600;
        }

        /* Orders Table */
        .orders-table {
            width: 100%;
            overflow-x: auto;
        }

        .orders-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .orders-table th {
            text-align: right;
            padding: 15px 10px;
            background: var(--bs-light-bg-subtle);
            color: var(--bs-heading-color);
            font-weight: 600;
            font-size: 14px;
            border-bottom: 2px solid var(--bs-border-color);
        }

        .orders-table td {
            padding: 15px 10px;
            border-bottom: 1px solid var(--bs-border-color);
        }

        .order-status {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .order-status.completed {
            background: var(--bs-success-bg-subtle);
            color: var(--bs-success-text);
        }

        .order-status.pending {
            background: var(--bs-warning-bg-subtle);
            color: var(--bs-warning-text);
        }

        .order-status.cancelled {
            background: var(--bs-danger-bg-subtle);
            color: var(--bs-danger-text);
        }

        .order-status.in_progress {
            background: var(--bs-info-bg-subtle);
            color: var(--bs-info-text);
        }

        /* Ratings */
        .ratings-summary {
            display: flex;
            gap: 30px;
            margin-bottom: 30px;
            padding: 20px;
            background: var(--bs-light-bg-subtle);
            border-radius: 15px;
            flex-wrap: wrap;
        }

        .average-rating {
            text-align: center;
            min-width: 150px;
        }

        .rating-number {
            font-size: 48px;
            font-weight: 700;
            color: #ffc107;
            line-height: 1;
        }

        .rating-stars {
            color: #ffc107;
            font-size: 18px;
            margin: 10px 0;
        }

        .rating-total {
            color: var(--bs-secondary-color);
            font-size: 13px;
        }

        .rating-bars {
            flex: 1;
        }

        .rating-bar-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
        }

        .rating-bar-label {
            width: 60px;
            font-size: 13px;
            color: var(--bs-secondary-color);
        }

        .rating-bar {
            flex: 1;
            height: 8px;
            background: var(--bs-border-color);
            border-radius: 4px;
            overflow: hidden;
        }

        .rating-bar-fill {
            height: 100%;
            background: #ffc107;
            border-radius: 4px;
        }

        .rating-bar-count {
            width: 40px;
            font-size: 12px;
            color: var(--bs-secondary-color);
        }

        .ratings-list {
            max-height: 400px;
            overflow-y: auto;
        }

        .rating-item {
            padding: 15px;
            border-bottom: 1px solid var(--bs-border-color);
        }

        .rating-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .rating-user {
            font-weight: 600;
            color: var(--bs-heading-color);
        }

        .rating-date {
            font-size: 12px;
            color: var(--bs-secondary-color);
        }

        .rating-stars-small {
            color: #ffc107;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .rating-comment {
            font-size: 14px;
            color: var(--bs-secondary-color);
            line-height: 1.6;
        }

        /* Wallet */
        .wallet-balance {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .balance-card {
            flex: 1;
            min-width: 200px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            padding: 25px;
            color: white;
        }

        .balance-label {
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 10px;
        }

        .balance-amount {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .balance-currency {
            font-size: 16px;
            opacity: 0.8;
        }

        .balance-card.held {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
        }

        .balance-card.total {
            background: linear-gradient(135deg, #00b894 0%, #00cec9 100%);
        }

        .transactions-table {
            width: 100%;
            overflow-x: auto;
        }

        .transactions-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .transactions-table th {
            text-align: right;
            padding: 15px 10px;
            background: var(--bs-light-bg-subtle);
            color: var(--bs-heading-color);
            font-weight: 600;
            font-size: 14px;
            border-bottom: 2px solid var(--bs-border-color);
        }

        .transactions-table td {
            padding: 15px 10px;
            border-bottom: 1px solid var(--bs-border-color);
        }

        .transaction-type {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .transaction-type.credit {
            background: var(--bs-success-bg-subtle);
            color: var(--bs-success-text);
        }

        .transaction-type.debit {
            background: var(--bs-danger-bg-subtle);
            color: var(--bs-danger-text);
        }

        /* Location History */
        .location-map {
            height: 400px;
            background: var(--bs-light-bg-subtle);
            border-radius: 15px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--bs-secondary-color);
        }

        .locations-list {
            max-height: 300px;
            overflow-y: auto;
        }

        .location-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            border-bottom: 1px solid var(--bs-border-color);
        }

        .location-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: #696cff;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .location-details {
            flex: 1;
        }

        .location-address {
            font-size: 14px;
            font-weight: 600;
            color: var(--bs-heading-color);
            margin-bottom: 3px;
        }

        .location-time {
            font-size: 12px;
            color: var(--bs-secondary-color);
        }

        .location-coords {
            font-size: 12px;
            color: var(--bs-secondary-color);
        }

        /* Timeline */
        .timeline {
            position: relative;
            padding-right: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            width: 2px;
            background: var(--bs-border-color);
        }

        .timeline-item {
            position: relative;
            padding-bottom: 25px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            right: -34px;
            top: 0;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #696cff;
            border: 2px solid var(--bs-card-bg);
        }

        .timeline-date {
            font-size: 12px;
            color: var(--bs-secondary-color);
            margin-bottom: 5px;
        }

        .timeline-title {
            font-size: 15px;
            font-weight: 600;
            color: var(--bs-heading-color);
            margin-bottom: 5px;
        }

        .timeline-description {
            font-size: 13px;
            color: var(--bs-secondary-color);
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .btn-action {
            padding: 12px 25px;
            border-radius: 10px;
            border: none;
            font-weight: 600;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-action.edit {
            background: linear-gradient(135deg, #fd7e14 0%, #ffc107 100%);
            color: white;
        }

        .btn-action.approve {
            background: linear-gradient(135deg, #198754 0%, #20c997 100%);
            color: white;
        }

        .btn-action.reject {
            background: linear-gradient(135deg, #dc3545 0%, #d63384 100%);
            color: white;
        }

        .btn-action.toggle {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            color: white;
        }

        .btn-action.wallet {
            background: linear-gradient(135deg, #6610f2 0%, #6f42c1 100%);
            color: white;
        }

        .btn-action.message {
            background: linear-gradient(135deg, #0dcaf0 0%, #0d6efd 100%);
            color: white;
        }

        .btn-action:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .profile-header-content {
                flex-direction: column;
                text-align: center;
            }

            .profile-meta {
                justify-content: center;
            }

            .profile-badges {
                justify-content: center;
            }

            .profile-stats {
                justify-content: center;
            }

            .profile-actions {
                margin-right: 0;
                justify-content: center;
                width: 100%;
            }

            .profile-tabs {
                flex-direction: column;
            }

            .profile-tab {
                width: 100%;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .info-row {
                flex-direction: column;
                gap: 5px;
            }

            .info-label {
                width: 100%;
            }

            .ratings-summary {
                flex-direction: column;
                align-items: center;
            }

            .wallet-balance {
                flex-direction: column;
            }

            .action-buttons {
                justify-content: center;
            }

            .btn-action {
                width: 100%;
                justify-content: center;
            }
        }

        /* Print Styles */
        @media print {
            .profile-actions,
            .profile-tabs,
            .action-buttons,
            .btn-profile-action {
                display: none !important;
            }

            .profile-header {
                background: #f8f9fa !important;
                color: #333 !important;
                box-shadow: none !important;
            }

            .profile-badge {
                background: #e9ecef !important;
                color: #333 !important;
            }

            .tab-pane {
                display: block !important;
            }

            .tab-content {
                box-shadow: none !important;
                border: 1px solid #dee2e6 !important;
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
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.drivers.index') }}">السائقين</a>
                </li>
                <li class="breadcrumb-item active">تفاصيل السائق: {{ $driver->user->name }}</li>
            </ol>
        </nav>

        <div class="driver-profile">
            <!-- Profile Header -->
            <div class="profile-header">
                <div class="profile-header-content">
                    <div class="profile-avatar d-flex align-items-center justify-content-center bg-secondary text-white">
                        @if($driver->personal_photo)
                            <img src="{{ asset('storage/' . $driver->personal_photo) }}" alt="{{ $driver->user->name }}">
                        @else
                            <i class="fas fa-truck" style="font-size: 24px;"></i>
                        @endif
                    </div>

                    <div class="profile-info">
                        <h2>{{ $driver->user->name }}</h2>
                        
                        <div class="profile-meta">
                            <span>
                                <i class="fas fa-phone"></i>
                                {{ $driver->user->full_phone ?? $driver->user->phone }}
                            </span>
                            <span>
                                <i class="fas fa-envelope"></i>
                                {{ $driver->user->email }}
                            </span>
                            <span>
                                <i class="fas fa-calendar-alt"></i>
                                تاريخ الانضمام: {{ $driver->created_at->format('Y-m-d') }}
                            </span>
                        </div>

                        <div class="profile-badges">
                            <span class="profile-badge">
                                <i class="fas {{ $driver->citizenship == 'saudi' ? 'fa-flag' : 'fa-passport' }}"></i>
                                {{ $driver->citizenship == 'saudi' ? 'سعودي' : 'مقيم' }}
                            </span>
                            
                            <span class="profile-badge">
                                <i class="fas fa-id-card"></i>
                                رخصة: {{ $driver->license_number }}
                            </span>
                            
                            <span class="profile-badge">
                                <i class="fas fa-car"></i>
                                {{ $driver->vehicle_plate_number }}
                            </span>
                        </div>

                        <div class="profile-stats">
                            <div class="profile-stat">
                                <div class="profile-stat-value">{{ $driver->orders_count ?? 0 }}</div>
                                <div class="profile-stat-label">طلبات</div>
                            </div>
                            <div class="profile-stat">
                                <div class="profile-stat-value">{{ number_format($driver->ratings_avg ?? 0, 1) }}</div>
                                <div class="profile-stat-label">تقييم</div>
                            </div>
                            <div class="profile-stat">
                                <div class="profile-stat-value">{{ $driver->driverWallet?->balance ?? 0 }}</div>
                                <div class="profile-stat-label">رصيد</div>
                            </div>
                        </div>
                    </div>

                    <div class="profile-actions">
                        <button class="btn-profile-action" onclick="window.print()" title="طباعة">
                            <i class="fas fa-print"></i>
                        </button>
                        <a href="{{ route('admin.drivers.edit', $driver->id) }}" class="btn-profile-action" title="تعديل">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button class="btn-profile-action" onclick="sendMessage({{ $driver->id }})" title="إرسال رسالة">
                            <i class="fas fa-envelope"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Navigation Tabs -->
            <div class="profile-tabs">
                <button class="profile-tab active" onclick="showTab('personal')">
                    <i class="fas fa-user"></i>
                    المعلومات الشخصية
                </button>
                <button class="profile-tab" onclick="showTab('documents')">
                    <i class="fas fa-file-alt"></i>
                    المستندات
                </button>
                <button class="profile-tab" onclick="showTab('vehicle')">
                    <i class="fas fa-truck"></i>
                    المركبة
                </button>
                <button class="profile-tab" onclick="showTab('orders')">
                    <i class="fas fa-shopping-cart"></i>
                    الطلبات
                </button>
                <button class="profile-tab" onclick="showTab('ratings')">
                    <i class="fas fa-star"></i>
                    التقييمات
                </button>
                <button class="profile-tab" onclick="showTab('wallet')">
                    <i class="fas fa-wallet"></i>
                    المحفظة
                </button>
                <button class="profile-tab" onclick="showTab('location')">
                    <i class="fas fa-map-marker-alt"></i>
                    المواقع
                </button>
                <button class="profile-tab" onclick="showTab('activity')">
                    <i class="fas fa-history"></i>
                    النشاطات
                </button>
            </div>

            <!-- Tab Content -->
            <div class="tab-content">
                <!-- Personal Information Tab -->
                <div class="tab-pane active" id="personal">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <h5 class="section-title">المعلومات الشخصية</h5>
                            <p class="section-description">بيانات السائق الأساسية ومعلومات الاتصال</p>
                        </div>
                    </div>

                    <div class="info-grid">
                        <!-- Basic Info Card -->
                        <div class="info-card">
                            <div class="info-card-title">
                                <div class="info-card-icon">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                <h6>معلومات أساسية</h6>
                            </div>

                            <div class="info-row">
                                <span class="info-label">الاسم الكامل:</span>
                                <span class="info-value">{{ $driver->user->name }}</span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">البريد الإلكتروني:</span>
                                <span class="info-value">{{ $driver->user->email }}</span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">رقم الجوال:</span>
                                <span class="info-value">{{ $driver->user->full_phone ?? $driver->user->phone }}</span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">الجنسية:</span>
                                <span class="info-value">
                                    <span class="badge-citizenship {{ $driver->citizenship }}">
                                        {{ $driver->citizenship == 'saudi' ? 'سعودي' : 'مقيم' }}
                                    </span>
                                </span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">تاريخ الميلاد:</span>
                                <span class="info-value">{{ $driver->date_of_birth ? $driver->date_of_birth->format('Y-m-d') : 'غير محدد' }}</span>
                            </div>
                        </div>

                        <!-- Identity Info Card -->
                        <div class="info-card">
                            <div class="info-card-title">
                                <div class="info-card-icon">
                                    <i class="fas fa-id-card"></i>
                                </div>
                                <h6>معلومات الهوية</h6>
                            </div>

                            @if($driver->citizenship == 'saudi')
                                <div class="info-row">
                                    <span class="info-label">رقم الهوية:</span>
                                    <span class="info-value">{{ $driver->national_id ?? 'غير محدد' }}</span>
                                </div>
                            @else
                                <div class="info-row">
                                    <span class="info-label">رقم الإقامة:</span>
                                    <span class="info-value">{{ $driver->iqama_number ?? 'غير محدد' }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">انتهاء الإقامة:</span>
                                    <span class="info-value">
                                        {{ $driver->iqama_expiry_date ? $driver->iqama_expiry_date->format('Y-m-d') : 'غير محدد' }}
                                        @if($driver->iqama_expiry_date && $driver->iqama_expiry_date->isPast())
                                            <span class="badge bg-danger me-2">منتهية</span>
                                        @endif
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- License Info Card -->
                        <div class="info-card">
                            <div class="info-card-title">
                                <div class="info-card-icon">
                                    <i class="fas fa-id-card"></i>
                                </div>
                                <h6>رخصة القيادة</h6>
                            </div>

                            <div class="info-row">
                                <span class="info-label">رقم الرخصة:</span>
                                <span class="info-value">{{ $driver->license_number }}</span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">انتهاء الرخصة:</span>
                                <span class="info-value">
                                    {{ $driver->license_expiry_date ? $driver->license_expiry_date->format('Y-m-d') : 'غير محدد' }}
                                    @if($driver->license_expiry_date && $driver->license_expiry_date->isPast())
                                        <span class="badge bg-danger me-2">منتهية</span>
                                    @endif
                                </span>
                            </div>
                        </div>

                        <!-- Status Card -->
                        <div class="info-card">
                            <div class="info-card-title">
                                <div class="info-card-icon">
                                    <i class="fas fa-toggle-on"></i>
                                </div>
                                <h6>الحالة والتحقق</h6>
                            </div>

                            <div class="info-row">
                                <span class="info-label">حالة التحقق:</span>
                                <span class="info-value">
                                    <span class="badge-verification {{ $driver->is_verified ? 'verified' : ($driver->rejection_reason ? 'rejected' : 'pending') }}">
                                        <i class="fas {{ $driver->is_verified ? 'fa-check-circle' : ($driver->rejection_reason ? 'fa-times-circle' : 'fa-clock') }}"></i>
                                        {{ $driver->is_verified ? 'موثق' : ($driver->rejection_reason ? 'مرفوض' : 'قيد المراجعة') }}
                                    </span>
                                </span>
                            </div>

                            @if($driver->verified_at)
                                <div class="info-row">
                                    <span class="info-label">تاريخ التوثيق:</span>
                                    <span class="info-value">{{ $driver->verified_at->format('Y-m-d H:i') }}</span>
                                </div>
                            @endif

                            @if($driver->rejection_reason)
                                <div class="info-row">
                                    <span class="info-label">سبب الرفض:</span>
                                    <span class="info-value">{{ $driver->rejection_reason }}</span>
                                </div>
                            @endif

                            <div class="info-row">
                                <span class="info-label">الحالة:</span>
                                <span class="info-value">
                                    <span class="badge-status {{ $driver->status }}">
                                        <i class="fas {{ $driver->status == 'active' ? 'fa-circle' : ($driver->status == 'suspended' ? 'fa-ban' : 'fa-circle') }}"></i>
                                        {{ $driver->status == 'active' ? 'نشط' : ($driver->status == 'suspended' ? 'موقوف' : 'غير نشط') }}
                                    </span>
                                </span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">آخر تحديث:</span>
                                <span class="info-value">{{ $driver->updated_at->format('Y-m-d H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Documents Tab -->
                <div class="tab-pane" id="documents">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div>
                            <h5 class="section-title">المستندات</h5>
                            <p class="section-description">جميع المستندات المرفوعة من السائق</p>
                        </div>
                    </div>

                    <div class="documents-grid">
                        <!-- Personal Photo -->
                        @if($driver->personal_photo)
                            <div class="document-card" onclick="viewDocument('{{ asset('storage/' . $driver->personal_photo) }}')">
                                <div class="document-preview">
                                    <img src="{{ asset('storage/' . $driver->personal_photo) }}" alt="Personal Photo">
                                </div>
                                <div class="document-info">
                                    <div class="document-name">الصورة الشخصية</div>
                                    <div class="document-type">صورة شخصية</div>
                                </div>
                            </div>
                        @endif

                        <!-- ID Front -->
                        @if($driver->id_image_front)
                            <div class="document-card" onclick="viewDocument('{{ asset('storage/' . $driver->id_image_front) }}')">
                                <div class="document-preview">
                                    <img src="{{ asset('storage/' . $driver->id_image_front) }}" alt="ID Front">
                                </div>
                                <div class="document-info">
                                    <div class="document-name">الهوية - وجه</div>
                                    <div class="document-type">{{ $driver->citizenship == 'saudi' ? 'هوية وطنية' : 'إقامة' }}</div>
                                </div>
                            </div>
                        @endif

                        <!-- ID Back -->
                        @if($driver->id_image_back)
                            <div class="document-card" onclick="viewDocument('{{ asset('storage/' . $driver->id_image_back) }}')">
                                <div class="document-preview">
                                    <img src="{{ asset('storage/' . $driver->id_image_back) }}" alt="ID Back">
                                </div>
                                <div class="document-info">
                                    <div class="document-name">الهوية - ظهر</div>
                                    <div class="document-type">{{ $driver->citizenship == 'saudi' ? 'هوية وطنية' : 'إقامة' }}</div>
                                </div>
                            </div>
                        @endif

                        <!-- License Front -->
                        @if($driver->license_image_front)
                            <div class="document-card" onclick="viewDocument('{{ asset('storage/' . $driver->license_image_front) }}')">
                                <div class="document-preview">
                                    <img src="{{ asset('storage/' . $driver->license_image_front) }}" alt="License Front">
                                </div>
                                <div class="document-info">
                                    <div class="document-name">رخصة القيادة - وجه</div>
                                    <div class="document-type">رخصة قيادة</div>
                                </div>
                            </div>
                        @endif

                        <!-- License Back -->
                        @if($driver->license_image_back)
                            <div class="document-card" onclick="viewDocument('{{ asset('storage/' . $driver->license_image_back) }}')">
                                <div class="document-preview">
                                    <img src="{{ asset('storage/' . $driver->license_image_back) }}" alt="License Back">
                                </div>
                                <div class="document-info">
                                    <div class="document-name">رخصة القيادة - ظهر</div>
                                    <div class="document-type">رخصة قيادة</div>
                                </div>
                            </div>
                        @endif

                        <!-- Vehicle Registration -->
                        @if($driver->vehicle_registration_image)
                            <div class="document-card" onclick="viewDocument('{{ asset('storage/' . $driver->vehicle_registration_image) }}')">
                                <div class="document-preview">
                                    <img src="{{ asset('storage/' . $driver->vehicle_registration_image) }}" alt="Vehicle Registration">
                                </div>
                                <div class="document-info">
                                    <div class="document-name">رخصة السير</div>
                                    <div class="document-type">استمارة مركبة</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Vehicle Tab -->
                <div class="tab-pane" id="vehicle">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-truck"></i>
                        </div>
                        <div>
                            <h5 class="section-title">معلومات المركبة</h5>
                            <p class="section-description">بيانات المركبة المسجلة باسم السائق</p>
                        </div>
                    </div>

                    <div class="info-grid">
                        <!-- Vehicle Info Card -->
                        <div class="info-card">
                            <div class="info-card-title">
                                <div class="info-card-icon">
                                    <i class="fas fa-car"></i>
                                </div>
                                <h6>بيانات المركبة</h6>
                            </div>

                            <div class="info-row">
                                <span class="info-label">حجم المركبة:</span>
                                <span class="info-value">
                                    {{ $driver->vehicle_size == 'small' ? 'صغيرة' : ($driver->vehicle_size == 'medium' ? 'متوسطة' : 'كبيرة') }}
                                </span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">رقم اللوحة:</span>
                                <span class="info-value">{{ $driver->vehicle_plate_number }}</span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">رقم التسجيل:</span>
                                <span class="info-value">{{ $driver->vehicle_registration_number }}</span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">رقم الاستمارة:</span>
                                <span class="info-value">{{ $driver->vehicle_residency_number }}</span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">ملكية المركبة:</span>
                                <span class="info-value">
                                    <span class="badge {{ $driver->is_vehicle_owner ? 'bg-success' : 'bg-warning' }}">
                                        {{ $driver->is_vehicle_owner ? 'مالك' : 'غير مالك' }}
                                    </span>
                                </span>
                            </div>
                        </div>

                        <!-- Additional Info -->
                        <div class="info-card">
                            <div class="info-card-title">
                                <div class="info-card-icon">
                                    <i class="fas fa-clipboard-list"></i>
                                </div>
                                <h6>معلومات إضافية</h6>
                            </div>

                            <div class="info-row">
                                <span class="info-label">حالة المركبة:</span>
                                <span class="info-value">
                                    <span class="badge bg-success">نشطة</span>
                                </span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">آخر تحديث:</span>
                                <span class="info-value">{{ $driver->updated_at->format('Y-m-d H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Orders Tab -->
                <div class="tab-pane" id="orders">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div>
                            <h5 class="section-title">الطلبات</h5>
                            <p class="section-description">جميع طلبات السائق</p>
                        </div>
                    </div>

                    <div class="orders-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>رقم الطلب</th>
                                    <th>العميل</th>
                                    <th>التاريخ</th>
                                    <th>المبلغ</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($driver->orders ?? [] as $order)
                                    <tr>
                                        <td>#{{ $order->id }}</td>
                                        <td>{{ $order->user->name }}</td>
                                        <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                                        <td>{{ $order->total_amount }} {{ $order->currency ?? 'SAR' }}</td>
                                        <td>
                                            <span class="order-status {{ $order->status }}">
                                                {{ $order->status == 'completed' ? 'مكتمل' : ($order->status == 'pending' ? 'قيد الانتظار' : ($order->status == 'cancelled' ? 'ملغي' : 'قيد التنفيذ')) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">لا توجد طلبات لهذا السائق</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Ratings Tab -->
                <div class="tab-pane" id="ratings">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <div>
                            <h5 class="section-title">التقييمات</h5>
                            <p class="section-description">تقييمات العملاء للسائق</p>
                        </div>
                    </div>

                    @php
                        $ratings = $driver->ratings ?? collect([]);
                        $avgRating = $ratings->avg('rating') ?? 0;
                        $ratingCounts = [
                            5 => $ratings->where('rating', 5)->count(),
                            4 => $ratings->where('rating', 4)->count(),
                            3 => $ratings->where('rating', 3)->count(),
                            2 => $ratings->where('rating', 2)->count(),
                            1 => $ratings->where('rating', 1)->count(),
                        ];
                    @endphp

                    <div class="ratings-summary">
                        <div class="average-rating">
                            <div class="rating-number">{{ number_format($avgRating, 1) }}</div>
                            <div class="rating-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= round($avgRating))
                                        <i class="fas fa-star"></i>
                                    @else
                                        <i class="far fa-star"></i>
                                    @endif
                                @endfor
                            </div>
                            <div class="rating-total">{{ $ratings->count() }} تقييم</div>
                        </div>

                        <div class="rating-bars">
                            @foreach(range(5, 1) as $star)
                                @php
                                    $count = $ratingCounts[$star] ?? 0;
                                    $percentage = $ratings->count() > 0 ? ($count / $ratings->count()) * 100 : 0;
                                @endphp
                                <div class="rating-bar-item">
                                    <span class="rating-bar-label">{{ $star }} نجوم</span>
                                    <div class="rating-bar">
                                        <div class="rating-bar-fill" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <span class="rating-bar-count">{{ $count }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="ratings-list">
                        @forelse($ratings as $rating)
                            <div class="rating-item">
                                <div class="rating-header">
                                    <span class="rating-user">{{ $rating->user->name }}</span>
                                    <span class="rating-date">{{ $rating->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="rating-stars-small">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $rating->rating)
                                            <i class="fas fa-star"></i>
                                        @else
                                            <i class="far fa-star"></i>
                                        @endif
                                    @endfor
                                </div>
                                @if($rating->comment)
                                    <div class="rating-comment">
                                        {{ $rating->comment }}
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="fas fa-star fa-3x text-muted mb-3"></i>
                                <p class="text-muted">لا توجد تقييمات لهذا السائق</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Wallet Tab -->
                <div class="tab-pane" id="wallet">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div>
                            <h5 class="section-title">المحفظة</h5>
                            <p class="section-description">رصيد السائق والمعاملات المالية</p>
                        </div>
                    </div>

                    <div class="wallet-balance">
                        <div class="balance-card">
                            <div class="balance-label">الرصيد الحالي</div>
                            <div class="balance-amount">{{ number_format($driver->driverWallet?->balance ?? 0, 2) }}</div>
                            <div class="balance-currency">{{ $driver->driverWallet?->currency ?? 'SAR' }}</div>
                        </div>

                        <div class="balance-card held">
                            <div class="balance-label">الرصيد المعلق</div>
                            <div class="balance-amount">{{ number_format($driver->driverWallet?->held_balance ?? 0, 2) }}</div>
                            <div class="balance-currency">{{ $driver->driverWallet?->currency ?? 'SAR' }}</div>
                        </div>

                        <div class="balance-card total">
                            <div class="balance-label">إجمالي الأرباح</div>
                            <div class="balance-amount">{{ number_format($totalEarnings ?? 0, 2) }}</div>
                            <div class="balance-currency">{{ $driver->driverWallet?->currency ?? 'SAR' }}</div>
                        </div>
                    </div>

                    <div class="transactions-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>التاريخ</th>
                                    <th>الوصف</th>
                                    <th>المبلغ</th>
                                    <th>النوع</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions ?? [] as $transaction)
                                    <tr>
                                        <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                                        <td>{{ $transaction->description }}</td>
                                        <td>{{ number_format($transaction->amount, 2) }} {{ $transaction->currency ?? 'SAR' }}</td>
                                        <td>
                                            <span class="transaction-type {{ $transaction->type }}">
                                                {{ $transaction->type == 'credit' ? 'إيداع' : 'سحب' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $transaction->status == 'completed' ? 'success' : ($transaction->status == 'pending' ? 'warning' : 'danger') }}">
                                                {{ $transaction->status == 'completed' ? 'مكتملة' : ($transaction->status == 'pending' ? 'معلقة' : 'فاشلة') }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <i class="fas fa-exchange-alt fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">لا توجد معاملات مالية</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Location Tab -->
                <div class="tab-pane" id="location">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h5 class="section-title">المواقع</h5>
                            <p class="section-description">سجل مواقع السائق</p>
                        </div>
                    </div>

                    <div class="location-map" id="map">
                        <div class="text-center">
                            <i class="fas fa-map-marked-alt fa-4x text-muted mb-3"></i>
                            <p class="text-muted">سيتم عرض الخريطة هنا</p>
                            <small class="text-muted">(يتطلب تفعيل خدمة الخرائط)</small>
                        </div>
                    </div>

                    <div class="locations-list">
                        @forelse($locations ?? [] as $location)
                            <div class="location-item">
                                <div class="location-icon">
                                    <i class="fas fa-map-pin"></i>
                                </div>
                                <div class="location-details">
                                    <div class="location-address">{{ $location->address ?? 'موقع غير معروف' }}</div>
                                    <div class="location-time">{{ $location->created_at->diffForHumans() }}</div>
                                    <div class="location-coords">
                                        خط: {{ $location->latitude }}, طول: {{ $location->longitude }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                                <p class="text-muted">لا توجد مواقع مسجلة</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Activity Tab -->
                <div class="tab-pane" id="activity">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-history"></i>
                        </div>
                        <div>
                            <h5 class="section-title">سجل النشاطات</h5>
                            <p class="section-description">آخر نشاطات السائق على المنصة</p>
                        </div>
                    </div>

                    <div class="timeline">
                        @forelse($activities ?? [] as $activity)
                            <div class="timeline-item">
                                <div class="timeline-date">{{ $activity->created_at->format('Y-m-d H:i') }}</div>
                                <div class="timeline-title">{{ $activity->title }}</div>
                                <div class="timeline-description">{{ $activity->description }}</div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                <p class="text-muted">لا توجد نشاطات حديثة</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="{{ route('admin.drivers.edit', $driver->id) }}" class="btn-action edit">
                    <i class="fas fa-edit"></i>
                    تعديل البيانات
                </a>

                @if(!$driver->is_verified && !$driver->rejection_reason)
                    <button class="btn-action approve" onclick="approveDriver({{ $driver->id }})">
                        <i class="fas fa-check"></i>
                        توثيق السائق
                    </button>
                    
                    <button class="btn-action reject" onclick="rejectDriver({{ $driver->id }})">
                        <i class="fas fa-times"></i>
                        رفض الطلب
                    </button>
                @endif

                @if($driver->is_verified || $driver->rejection_reason)
                    <button class="btn-action toggle" onclick="resetVerification({{ $driver->id }})">
                        <i class="fas fa-undo"></i>
                        إعادة تعيين التحقق
                    </button>
                @endif

                <button class="btn-action toggle" onclick="toggleStatus({{ $driver->id }}, '{{ $driver->status }}')">
                    <i class="fas {{ $driver->status == 'active' ? 'fa-pause' : 'fa-play' }}"></i>
                    {{ $driver->status == 'active' ? 'تعطيل' : 'تفعيل' }}
                </button>

                <button class="btn-action message" onclick="sendMessage({{ $driver->id }})">
                    <i class="fas fa-envelope"></i>
                    إرسال رسالة
                </button>
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
                <div class="modal-body text-center">
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();
        });

        // Tab switching
        function showTab(tabId) {
            // Hide all tabs
            $('.tab-pane').removeClass('active');
            $('.profile-tab').removeClass('active');
            
            // Show selected tab
            $('#' + tabId).addClass('active');
            $(`.profile-tab[onclick="showTab('${tabId}')"]`).addClass('active');
        }

        // View document
        function viewDocument(src) {
            $('#documentImage').attr('src', src);
            $('#downloadDocument').attr('href', src);
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
            const newStatus = currentStatus == 'active' ? 'inactive' : 'active';
            const action = newStatus == 'active' ? 'تفعيل' : 'تعطيل';
            
            Swal.fire({
                title: `${action} السائق`,
                text: `هل أنت متأكد من ${action} هذا السائق؟`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: newStatus == 'active' ? '#198754' : '#dc3545',
                cancelButtonColor: '#3085d6',
                confirmButtonText: `نعم، ${action}`,
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/admin/drivers/${driverId}/toggle-status`,
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

        // Reset verification
        function resetVerification(driverId) {
            Swal.fire({
                title: 'إعادة تعيين التحقق',
                text: 'هل أنت متأكد من إعادة تعيين حالة التحقق؟ سيصبح السائق في قائمة الانتظار',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#fd7e14',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'نعم، إعادة تعيين',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/admin/drivers/${driverId}/reset-verification`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        beforeSend: function() {
                            Swal.fire({
                                title: 'جاري إعادة التعيين...',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'تم إعادة التعيين!',
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
                                text: xhr.responseJSON?.message || 'حدث خطأ أثناء إعادة التعيين'
                            });
                        }
                    });
                }
            });
        }

        // View wallet
        function viewWallet(driverId) {
            window.location.href = `/admin/drivers/${driverId}/wallet`;
        }

        // Send message
        function sendMessage(driverId) {
            Swal.fire({
                title: 'إرسال رسالة',
                html: `
                    <input type="text" id="messageSubject" class="swal2-input" placeholder="عنوان الرسالة">
                    <textarea id="messageBody" class="swal2-textarea" placeholder="نص الرسالة" rows="4"></textarea>
                `,
                showCancelButton: true,
                confirmButtonColor: '#0dcaf0',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'إرسال',
                cancelButtonText: 'إلغاء',
                preConfirm: () => {
                    const subject = document.getElementById('messageSubject').value;
                    const body = document.getElementById('messageBody').value;
                    
                    if (!subject || !body) {
                        Swal.showValidationMessage('يرجى إدخال عنوان ونص الرسالة');
                        return false;
                    }
                    
                    return { subject, body };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/admin/drivers/${driverId}/send-message`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            subject: result.value.subject,
                            body: result.value.body
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
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ!',
                                text: xhr.responseJSON?.message || 'حدث خطأ أثناء الإرسال'
                            });
                        }
                    });
                }
            });
        }

        // Print driver profile
        window.print = function() {
            window.print();
        };
    </script>
@endsection