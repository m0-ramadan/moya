@extends('Admin.layout.master')

@section('title', 'إحصائيات الطلبات')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.css">
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

        .stats-card {
            background: var(--dark-card);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            border-top: 4px solid var(--primary-color);
            transition: transform 0.3s ease;
            margin-bottom: 20px;
            height: 100%;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
        }

        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
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

        .icon-average {
            background: rgba(21, 87, 36, 0.2);
            color: #20c997;
            border: 1px solid rgba(32, 201, 151, 0.3);
        }

        .icon-pending {
            background: rgba(133, 100, 4, 0.2);
            color: #ffc107;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }

        .icon-delivered {
            background: rgba(12, 84, 96, 0.2);
            color: #0dcaf0;
            border: 1px solid rgba(13, 202, 240, 0.3);
        }

        .icon-cancelled {
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.2) 0%, rgba(253, 126, 20, 0.2) 100%);
            color: #fd7e14;
            border: 1px solid rgba(253, 126, 20, 0.3);
        }

        .icon-today {
            background: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .icon-week {
            background: rgba(255, 255, 255, 0.05);
            color: rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .icon-driver {
            background: rgba(111, 66, 193, 0.2);
            color: #6f42c1;
            border: 1px solid rgba(111, 66, 193, 0.3);
        }

        .stats-number {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
            color: #fff;
        }

        .stats-label {
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
            margin-bottom: 10px;
        }

        .stats-change {
            font-size: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .change-up {
            color: #20c997;
        }

        .change-down {
            color: #fd7e14;
        }

        .chart-card {
            background: var(--dark-card);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
            padding: 25px;
            margin-bottom: 25px;
            height: 100%;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .chart-header h6 {
            color: var(--primary-color);
            margin-bottom: 0;
        }

        .chart-controls {
            display: flex;
            gap: 10px;
        }

        .chart-control {
            padding: 6px 15px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.05);
            color: rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.1);
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 13px;
        }

        .chart-control:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .chart-control.active {
            background: var(--primary-gradient);
            color: white;
            border-color: var(--primary-color);
        }

        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }

        .table-card {
            background: var(--dark-card);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
            padding: 25px;
            margin-bottom: 25px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .table-header {
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .driver-item {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .driver-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .driver-rank {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
        }

        .rank-1 {
            background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
        }

        .rank-2 {
            background: linear-gradient(135deg, #c0c0c0 0%, #e0e0e0 100%);
        }

        .rank-3 {
            background: linear-gradient(135deg, #cd7f32 0%, #e89c4e 100%);
        }

        .rank-default {
            background: var(--primary-gradient);
        }

        .driver-info {
            flex-grow: 1;
        }

        .driver-name {
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 5px;
        }

        .driver-stats {
            color: rgba(255, 255, 255, 0.7);
            font-size: 13px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .driver-stats span i {
            margin-left: 5px;
            color: var(--primary-color);
        }

        .empty-chart {
            text-align: center;
            padding: 50px 20px;
            color: rgba(255, 255, 255, 0.7);
        }

        .empty-chart i {
            font-size: 60px;
            margin-bottom: 20px;
            color: rgba(255, 255, 255, 0.1);
        }

        .date-filter {
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

        .form-control, .form-select {
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

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .form-select option {
            background: var(--dark-card);
            color: #fff;
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

        .badge-gateway {
            background: rgba(105, 108, 255, 0.2);
            color: var(--primary-color);
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
            border: 1px solid rgba(105, 108, 255, 0.3);
        }

        @media (max-width: 768px) {
            .chart-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .chart-controls {
                width: 100%;
                justify-content: center;
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
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.orders.index') }}">الطلبات</a>
                </li>
                <li class="breadcrumb-item active">الإحصائيات</li>
            </ol>
        </nav>

        <!-- فلترة التاريخ -->
        <div class="date-filter">
            <h6 class="mb-3"><i class="fas fa-calendar-alt me-2"></i>فلترة حسب التاريخ</h6>

            <div class="filter-row">
                <div class="input-group">
                    <span class="input-group-text bg-transparent text-white border">من</span>
                    <input type="date" class="form-control" id="dateFrom"
                        value="{{ request('date_from', now()->subDays(30)->format('Y-m-d')) }}">
                    <span class="input-group-text bg-transparent text-white border">إلى</span>
                    <input type="date" class="form-control" id="dateTo" 
                        value="{{ request('date_to', now()->format('Y-m-d')) }}">
                </div>

                <select class="form-select" id="chartType">
                    <option value="daily">يومي</option>
                    <option value="weekly" {{ request('chart_type') == 'weekly' ? 'selected' : '' }}>أسبوعي</option>
                    <option value="monthly" {{ request('chart_type') == 'monthly' ? 'selected' : '' }}>شهري</option>
                </select>

                <button class="btn btn-primary" onclick="loadStatistics()">
                    <i class="fas fa-filter me-2"></i>تطبيق الفلتر
                </button>
                <a href="{{ route('admin.orders.statistics') }}" class="btn btn-secondary">
                    <i class="fas fa-redo me-2"></i>إعادة تعيين
                </a>
            </div>
        </div>

        <!-- الإحصائيات الرئيسية -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stats-card">
                    <div class="stats-icon icon-total">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stats-number" id="totalOrders">
                        {{ number_format($stats['total_orders'] ?? 0) }}
                    </div>
                    <div class="stats-label">إجمالي الطلبات</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stats-card">
                    <div class="stats-icon icon-revenue">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="stats-number" id="totalRevenue">
                        {{ number_format($stats['total_revenue'] ?? 0, 2) }} ر.س
                    </div>
                    <div class="stats-label">إجمالي الإيرادات</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stats-card">
                    <div class="stats-icon icon-average">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stats-number" id="averageOrder">
                        {{ number_format($stats['average_order_value'] ?? 0, 2) }} ر.س
                    </div>
                    <div class="stats-label">متوسط قيمة الطلب</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stats-card">
                    <div class="stats-icon icon-today">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="stats-number" id="todayOrders">
                        {{ number_format($stats['today_orders'] ?? 0) }}
                    </div>
                    <div class="stats-label">طلبات اليوم</div>
                </div>
            </div>
        </div>

        <!-- المخططات -->
        <div class="row">
            <!-- مخطط الطلبات حسب حالة الدفع -->
            <div class="col-lg-6 mb-4">
                <div class="chart-card">
                    <div class="chart-header">
                        <h6><i class="fas fa-chart-pie me-2"></i>توزيع الطلبات حسب حالة الدفع</h6>
                        <div class="chart-controls">
                            <button class="chart-control active" onclick="changePaymentChartType('pie')">
                                دائري
                            </button>
                            <button class="chart-control" onclick="changePaymentChartType('doughnut')">
                                حلقي
                            </button>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="paymentStatusChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- مخطط الإيرادات -->
            <div class="col-lg-6 mb-4">
                <div class="chart-card">
                    <div class="chart-header">
                        <h6><i class="fas fa-chart-bar me-2"></i>الإيرادات خلال الشهر</h6>
                        <div class="chart-controls">
                            <button class="chart-control active" onclick="changeRevenueChart('bar')">
                                أعمدة
                            </button>
                            <button class="chart-control" onclick="changeRevenueChart('line')">
                                خطي
                            </button>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- إحصائيات الحالة -->
            <div class="col-lg-6 mb-4">
                <div class="table-card">
                    <div class="table-header">
                        <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>إحصائيات حالات الطلبات</h6>
                    </div>

                    <div class="row">
                        @php
                            $statusColors = [
                                'pending' => '#ffc107',
                                'in-road' => '#0dcaf0',
                                'scheduled' => '#ab8ce4',
                                'delivered' => '#198754',
                                'cancelled' => '#dc3545',
                            ];

                            $statusIcons = [
                                'pending' => 'clock',
                                'in-road' => 'truck',
                                'scheduled' => 'calendar-check',
                                'delivered' => 'check-circle',
                                'cancelled' => 'times-circle',
                            ];
                        @endphp

                        @foreach($orderStatuses ?? [] as $status)
                            @php
                                $count = $stats['status_counts'][$status->name] ?? 0;
                                $percentage = ($stats['total_orders'] ?? 0) > 0 ? ($count / $stats['total_orders'] * 100) : 0;
                                $color = $statusColors[$status->name] ?? '#6c757d';
                                $icon = $statusIcons[$status->name] ?? 'question-circle';
                            @endphp
                            <div class="col-md-6 mb-3">
                                <div class="stats-card" style="border-top-color: {{ $color }};">
                                    <div class="stats-icon" style="background: {{ $color }}; color: white;">
                                        <i class="fas fa-{{ $icon }}"></i>
                                    </div>
                                    <div class="stats-number">{{ number_format($count) }}</div>
                                    <div class="stats-label">{{ $status->label }}</div>
                                    <div class="stats-change">
                                        <span>{{ number_format($percentage, 1) }}%</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- إحصائيات بوابات الدفع -->
            <div class="col-lg-6 mb-4">
                <div class="table-card">
                    <div class="table-header">
                        <h6 class="mb-0"><i class="fas fa-credit-card me-2"></i>إحصائيات طرق الدفع</h6>
                    </div>

                    <div class="row">
                        @php
                            $gatewayColors = [
                                'wallet' => '#696cff',
                                'paymob' => '#20c997',
                                'tamara' => '#fd7e14',
                                'tabby' => '#0dcaf0',
                                'credit_card' => '#0d6efd',
                                'mada' => '#dc3545',
                                'apple_pay' => '#6f42c1',
                            ];

                            $gatewayIcons = [
                                'wallet' => 'wallet',
                                'paymob' => 'money-bill',
                                'tamara' => 'calendar',
                                'tabby' => 'cat',
                                'credit_card' => 'credit-card',
                                'mada' => 'credit-card',
                                'apple_pay' => 'apple-pay',
                            ];
                        @endphp

                        @foreach($paymentGateways ?? [] as $gateway => $count)
                            @if($count > 0)
                                @php
                                    $percentage = ($stats['total_orders'] ?? 0) > 0 ? ($count / $stats['total_orders'] * 100) : 0;
                                    $color = $gatewayColors[$gateway] ?? '#6c757d';
                                    $icon = $gatewayIcons[$gateway] ?? 'money-bill';
                                @endphp
                                <div class="col-md-6 mb-3">
                                    <div class="stats-card" style="border-top-color: {{ $color }};">
                                        <div class="stats-icon" style="background: {{ $color }}; color: white;">
                                            <i class="fas fa-{{ $icon }}"></i>
                                        </div>
                                        <div class="stats-number">{{ number_format($count) }}</div>
                                        <div class="stats-label">
                                            @switch($gateway)
                                                @case('wallet')محفظة@break
                                                @case('paymob')Paymob@break
                                                @case('tamara')Tamara@break
                                                @case('tabby')Tabby@break
                                                @case('credit_card')بطاقة ائتمان@break
                                                @case('mada')مدى@break
                                                @case('apple_pay')Apple Pay@break
                                                @default{{ $gateway }}
                                            @endswitch
                                        </div>
                                        <div class="stats-change">
                                            <span>{{ number_format($percentage, 1) }}%</span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- السائقين الأكثر نشاطاً -->
            <div class="col-lg-6 mb-4">
                <div class="table-card">
                    <div class="table-header">
                        <h6 class="mb-0"><i class="fas fa-trophy me-2"></i>السائقين الأكثر نشاطاً</h6>
                    </div>

                    @if(($topDrivers ?? collect())->isEmpty())
                        <div class="empty-chart">
                            <i class="fas fa-users"></i>
                            <p>لا توجد بيانات عن السائقين</p>
                        </div>
                    @else
                        @foreach($topDrivers ?? [] as $index => $driver)
                            <div class="driver-item">
                                <div class="driver-rank rank-{{ $index < 3 ? $index + 1 : 'default' }}">
                                    {{ $index + 1 }}
                                </div>
                                <div class="driver-info">
                                    <div class="driver-name">
                                        {{ $driver->user->name ?? 'سائق #' . $driver->id }}
                                        @if($driver->is_verified)
                                            <i class="fas fa-check-circle text-success ms-1" title="موثق"></i>
                                        @endif
                                    </div>
                                    <div class="driver-stats">
                                        <span>
                                            <i class="fas fa-shopping-cart"></i>
                                            {{ $driver->orders_count ?? 0 }} طلب
                                        </span>
                                        @if($driver->vehicle_plate_number)
                                            <span>
                                                <i class="fas fa-car"></i>
                                                {{ $driver->vehicle_plate_number }}
                                            </span>
                                        @endif
                                        @if(isset($driver->total_revenue))
                                            <span>
                                                <i class="fas fa-money-bill-wave"></i>
                                                {{ number_format($driver->total_revenue, 2) }} ر.س
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- العملاء الأكثر طلباً -->
            <div class="col-lg-6 mb-4">
                <div class="table-card">
                    <div class="table-header">
                        <h6 class="mb-0"><i class="fas fa-users me-2"></i>العملاء الأكثر طلباً</h6>
                    </div>

                    @if(($topUsers ?? collect())->isEmpty())
                        <div class="empty-chart">
                            <i class="fas fa-user"></i>
                            <p>لا توجد بيانات عن العملاء</p>
                        </div>
                    @else
                        @foreach($topUsers ?? [] as $index => $user)
                            <div class="driver-item">
                                <div class="driver-rank rank-{{ $index < 3 ? $index + 1 : 'default' }}">
                                    {{ $index + 1 }}
                                </div>
                                <div class="driver-info">
                                    <div class="driver-name">
                                        {{ $user->name }}
                                        @if($user->phone_verified_at)
                                            <i class="fas fa-check-circle text-success ms-1" title="موثق"></i>
                                        @endif
                                    </div>
                                    <div class="driver-stats">
                                        <span>
                                            <i class="fas fa-shopping-cart"></i>
                                            {{ $user->orders_count ?? 0 }} طلب
                                        </span>
                                        <span>
                                            <i class="fas fa-phone"></i>
                                            {{ $user->full_phone ?? $user->phone }}
                                        </span>
                                        @if(isset($user->total_spent))
                                            <span>
                                                <i class="fas fa-money-bill-wave"></i>
                                                {{ number_format($user->total_spent, 2) }} ر.س
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            <!-- إحصائيات إضافية -->
            <div class="col-lg-12 mb-4">
                <div class="table-card">
                    <div class="table-header">
                        <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>إحصائيات إضافية</h6>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="stats-card">
                                <div class="stats-number">{{ number_format($stats['weekly_orders'] ?? 0) }}</div>
                                <div class="stats-label">طلبات هذا الأسبوع</div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stats-card">
                                <div class="stats-number">{{ number_format($stats['weekly_revenue'] ?? 0, 2) }} ر.س</div>
                                <div class="stats-label">إيرادات هذا الأسبوع</div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stats-card">
                                <div class="stats-number">{{ number_format($stats['monthly_orders'] ?? 0) }}</div>
                                <div class="stats-label">طلبات هذا الشهر</div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stats-card">
                                <div class="stats-number">{{ number_format($stats['monthly_revenue'] ?? 0, 2) }} ر.س</div>
                                <div class="stats-label">إيرادات هذا الشهر</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        let paymentStatusChart, revenueChart;
        let currentPaymentChartType = 'pie';
        let currentRevenueChartType = 'bar';

        $(document).ready(function() {
            loadCharts();
        });

        function loadStatistics() {
            const dateFrom = $('#dateFrom').val();
            const dateTo = $('#dateTo').val();
            const chartType = $('#chartType').val();

            const url = new URL(window.location.href);
            url.searchParams.set('date_from', dateFrom);
            url.searchParams.set('date_to', dateTo);
            url.searchParams.set('chart_type', chartType);
            window.location.href = url.toString();
        }

        function loadCharts() {
            // بيانات مخطط حالة الدفع
            const paymentStatusData = {
                labels: [
                    'قيد الانتظار',
                    'قيد المعالجة',
                    'مدفوع',
                    'فشل الدفع',
                    'مسترد'
                ],
                datasets: [{
                    data: [
                        {{ $stats['payment_status_counts']['pending'] ?? 0 }},
                        {{ $stats['payment_status_counts']['processing'] ?? 0 }},
                        {{ $stats['payment_status_counts']['paid'] ?? 0 }},
                        {{ $stats['payment_status_counts']['failed'] ?? 0 }},
                        {{ $stats['payment_status_counts']['refunded'] ?? 0 }}
                    ],
                    backgroundColor: [
                        '#ffc107',
                        '#0dcaf0',
                        '#198754',
                        '#dc3545',
                        '#6c757d'
                    ],
                    borderWidth: 1
                }]
            };

            // بيانات مخطط الإيرادات من الـ controller
            const revenueData = {
                labels: {!! json_encode($ordersByDay->pluck('date')->map(function($date) { 
                    return \Carbon\Carbon::parse($date)->format('d M'); 
                })) !!},
                datasets: [{
                    label: 'الإيرادات (ر.س)',
                    data: {!! json_encode($revenueByDay->pluck('revenue')) !!},
                    backgroundColor: 'rgba(105, 108, 255, 0.2)',
                    borderColor: 'rgba(105, 108, 255, 1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            };

            // إنشاء مخطط حالة الدفع
            const paymentCtx = document.getElementById('paymentStatusChart').getContext('2d');
            if (paymentStatusChart) paymentStatusChart.destroy();
            paymentStatusChart = new Chart(paymentCtx, {
                type: currentPaymentChartType,
                data: paymentStatusData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            rtl: true,
                            labels: {
                                font: {
                                    family: 'Cairo',
                                    size: 12
                                },
                                padding: 20,
                                color: '#fff'
                            }
                        },
                        tooltip: {
                            rtl: true,
                            bodyFont: {
                                family: 'Cairo'
                            },
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += context.formattedValue + ' طلب';
                                    return label;
                                }
                            }
                        }
                    }
                }
            });

            // إنشاء مخطط الإيرادات
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            if (revenueChart) revenueChart.destroy();
            revenueChart = new Chart(revenueCtx, {
                type: currentRevenueChartType,
                data: revenueData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: {
                                color: '#fff'
                            }
                        },
                        tooltip: {
                            rtl: true,
                            bodyFont: {
                                family: 'Cairo'
                            },
                            callbacks: {
                                label: function(context) {
                                    return context.raw ? context.raw.toLocaleString() + ' ر.س' : '0 ر.س';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            },
                            ticks: {
                                color: 'rgba(255, 255, 255, 0.7)',
                                callback: function(value) {
                                    return value.toLocaleString() + ' ر.س';
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: 'rgba(255, 255, 255, 0.7)'
                            }
                        }
                    }
                }
            });
        }

        function changePaymentChartType(type) {
            currentPaymentChartType = type;
            loadCharts();

            // تحديث أزرار التحكم
            $('.chart-control').removeClass('active');
            $(`.chart-control[onclick="changePaymentChartType('${type}')"]`).addClass('active');
        }

        function changeRevenueChart(type) {
            currentRevenueChartType = type;
            loadCharts();

            // تحديث أزرار التحكم
            $('.chart-control').removeClass('active');
            $(`.chart-control[onclick="changeRevenueChart('${type}')"]`).addClass('active');
        }
    </script>
@endsection