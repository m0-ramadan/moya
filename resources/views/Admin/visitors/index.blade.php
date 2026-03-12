@extends('Admin.layout.master')

@section('title', 'إحصائيات الزوار')

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
            --dark-bg: #1e1e2d;
            --dark-card: #2b3b4c;
            --darker-card: #1e2a3a;
        }

        body {
            font-family: "Cairo", sans-serif !important;
            background: var(--dark-bg);
            color: #fff;
        }

        /* بطاقة الإحصائيات */
        .stats-card {
            background: var(--dark-card);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: transform 0.3s ease;
            height: 100%;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            border-color: var(--primary-color);
        }

        .stats-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: rgba(105, 108, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .stats-value {
            font-size: 28px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 5px;
        }

        .stats-label {
            color: rgba(255, 255, 255, 0.6);
            font-size: 14px;
        }

        .stats-change {
            font-size: 13px;
            margin-top: 10px;
        }

        .stats-change.positive {
            color: #28a745;
        }

        .stats-change.negative {
            color: #dc3545;
        }

        /* بطاقة الرسم البياني */
        .chart-card {
            background: var(--dark-card);
            border-radius: 15px;
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 25px;
            margin-top: 25px;
            height: 50vh !important;
        }

        .chart-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--primary-color);
        }

        /* بطاقة الأجهزة */
        .device-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .device-card:hover {
            background: rgba(105, 108, 255, 0.1);
            border-color: var(--primary-color);
        }

        .device-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-bottom: 10px;
        }

        .device-icon.desktop {
            background: rgba(23, 162, 184, 0.2);
            color: #17a2b8;
        }

        .device-icon.mobile {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
        }

        .device-icon.tablet {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
        }

        .device-icon.bot {
            background: rgba(108, 117, 125, 0.2);
            color: #6c757d;
        }

        .device-name {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 5px;
        }

        .device-count {
            font-size: 18px;
            font-weight: 600;
            color: #fff;
        }

        .device-percent {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.5);
        }

        /* قائمة الزوار */
        .visitors-table {
            background: var(--dark-card);
            border-radius: 15px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .visitors-table table {
            margin-bottom: 0;
        }

        .visitors-table th {
            background: rgba(0, 0, 0, 0.3);
            color: rgba(255, 255, 255, 0.9);
            font-weight: 600;
            padding: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .visitors-table td {
            padding: 15px;
            color: rgba(255, 255, 255, 0.8);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            vertical-align: middle;
        }

        .visitors-table tr:hover td {
            background: rgba(105, 108, 255, 0.05);
        }

        .badge-device {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .badge-desktop {
            background: rgba(23, 162, 184, 0.2);
            color: #17a2b8;
            border: 1px solid rgba(23, 162, 184, 0.3);
        }

        .badge-mobile {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }

        .badge-tablet {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }

        .badge-bot {
            background: rgba(108, 117, 125, 0.2);
            color: #6c757d;
            border: 1px solid rgba(108, 117, 125, 0.3);
        }

        .flag-icon {
            width: 24px;
            height: 16px;
            border-radius: 4px;
            margin-left: 5px;
        }

        .country-info {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .ip-address {
            font-family: monospace;
            background: rgba(0, 0, 0, 0.3);
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
        }

        .path-info {
            max-width: 250px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .time-info {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.5);
        }

        /* فلترة */
        .filter-card {
            background: var(--dark-card);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .filter-label {
            color: rgba(255, 255, 255, 0.8);
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .filter-input {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            border-radius: 8px;
            padding: 8px 12px;
            width: 100%;
        }

        .filter-input:focus {
            border-color: var(--primary-color);
            outline: none;
        }

        .filter-btn {
            background: var(--primary-gradient);
            border: none;
            color: #fff;
            padding: 8px 20px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .filter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(105, 108, 255, 0.4);
        }

        .reset-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            padding: 8px 20px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .reset-btn:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        /* تنسيقات إضافية */
        .detail-link {
            color: var(--primary-color);
            cursor: pointer;
            text-decoration: none;
        }

        .detail-link:hover {
            text-decoration: underline;
        }

        .toolbar {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .export-btn {
            background: rgba(40, 167, 69, 0.2);
            border: 1px solid #28a745;
            color: #28a745;
            padding: 8px 15px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .export-btn:hover {
            background: #28a745;
            color: #fff;
        }

        .clear-btn {
            background: rgba(220, 53, 69, 0.2);
            border: 1px solid #dc3545;
            color: #dc3545;
            padding: 8px 15px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .clear-btn:hover {
            background: #dc3545;
            color: #fff;
        }

        /* تذييل الجدول */
        .table-footer {
            padding: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .pagination {
            margin-bottom: 0;
        }

        .pagination .page-link {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .pagination .page-item.active .page-link {
            background: var(--primary-gradient);
            border-color: var(--primary-color);
        }

        @media (max-width: 768px) {
            .visitors-table {
                overflow-x: auto;
            }

            .toolbar {
                flex-direction: column;
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
                <li class="breadcrumb-item active">إحصائيات الزوار</li>
            </ol>
        </nav>

        <!-- الإحصائيات الرئيسية -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card">
                    <div class="stats-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stats-value">{{ number_format($stats['total_visitors']) }}</div>
                    <div class="stats-label">إجمالي الزيارات</div>
                    <div class="stats-change positive">
                        <i class="fas fa-arrow-up"></i>
                        {{ number_format($stats['unique_visitors']) }} زائر فريد
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card">
                    <div class="stats-icon">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="stats-value">{{ number_format($stats['today_visitors']) }}</div>
                    <div class="stats-label">زيارات اليوم</div>
                    <div class="stats-change">
                        <i class="fas fa-user-check"></i>
                        {{ number_format($stats['unique_today']) }} فريد
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card">
                    <div class="stats-icon">
                        <i class="fas fa-calendar-week"></i>
                    </div>
                    <div class="stats-value">{{ number_format($stats['this_week']) }}</div>
                    <div class="stats-label">هذا الأسبوع</div>
                    <div class="stats-change">
                        <i class="fas fa-chart-line"></i>
                        آخر 7 أيام
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card">
                    <div class="stats-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stats-value">{{ number_format($stats['this_month']) }}</div>
                    <div class="stats-label">هذا الشهر</div>
                    <div class="stats-change">
                        <i class="fas fa-percent"></i>
                        {{ round(($stats['this_month'] / max($stats['total_visitors'], 1)) * 100, 1) }}% من الإجمالي
                    </div>
                </div>
            </div>
        </div>

        <!-- الرسم البياني -->
        <div class="chart-card">
            <h6 class="chart-title">
                <i class="fas fa-chart-line me-2"></i>
                الزيارات اليومية (آخر 30 يوم)
            </h6>
            <canvas id="visitsChart" height="100vh" style="height: fit-content !important;"></canvas>
        </div>

        <!-- إحصائيات إضافية -->
        <div class="row my-4 mt-5">
            <!-- الأجهزة -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card">
                    <h6 class="mb-3">الأجهزة</h6>

                    <div class="device-card mb-2">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <div class="device-icon desktop">
                                    <i class="fas fa-desktop"></i>
                                </div>
                                <div>
                                    <div class="device-name">Desktop</div>
                                    <div class="device-count">{{ number_format($deviceStats['desktop']) }}</div>
                                </div>
                            </div>
                            <div class="device-percent">
                                {{ round(($deviceStats['desktop'] / max($stats['total_visitors'], 1)) * 100, 1) }}%
                            </div>
                        </div>
                    </div>

                    <div class="device-card mb-2">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <div class="device-icon mobile">
                                    <i class="fas fa-mobile-alt"></i>
                                </div>
                                <div>
                                    <div class="device-name">Mobile</div>
                                    <div class="device-count">{{ number_format($deviceStats['mobile']) }}</div>
                                </div>
                            </div>
                            <div class="device-percent">
                                {{ round(($deviceStats['mobile'] / max($stats['total_visitors'], 1)) * 100, 1) }}%
                            </div>
                        </div>
                    </div>

                    <div class="device-card mb-2">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <div class="device-icon tablet">
                                    <i class="fas fa-tablet-alt"></i>
                                </div>
                                <div>
                                    <div class="device-name">Tablet</div>
                                    <div class="device-count">{{ number_format($deviceStats['tablet']) }}</div>
                                </div>
                            </div>
                            <div class="device-percent">
                                {{ round(($deviceStats['tablet'] / max($stats['total_visitors'], 1)) * 100, 1) }}%
                            </div>
                        </div>
                    </div>

                    <div class="device-card">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <div class="device-icon bot">
                                    <i class="fas fa-robot"></i>
                                </div>
                                <div>
                                    <div class="device-name">Bots</div>
                                    <div class="device-count">{{ number_format($deviceStats['bot']) }}</div>
                                </div>
                            </div>
                            <div class="device-percent">
                                {{ round(($deviceStats['bot'] / max($stats['total_visitors'], 1)) * 100, 1) }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- المتصفحات -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card">
                    <h6 class="mb-3">أشهر المتصفحات</h6>

                    @foreach ($browserStats as $browser)
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fab fa-{{ strtolower($browser->browser) }} fa-fw"
                                    style="color: var(--primary-color);"></i>
                                <span>{{ $browser->browser }}</span>
                            </div>
                            <div>
                                <span class="fw-bold">{{ number_format($browser->total) }}</span>
                                <span
                                    class="text-muted small">({{ round(($browser->total / max($stats['total_visitors'], 1)) * 100, 1) }}%)</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- أنظمة التشغيل -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card">
                    <h6 class="mb-3">أنظمة التشغيل</h6>

                    @foreach ($platformStats as $platform)
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fab fa-{{ strtolower($platform->platform) }} fa-fw"
                                    style="color: var(--primary-color);"></i>
                                <span>{{ $platform->platform }}</span>
                            </div>
                            <div>
                                <span class="fw-bold">{{ number_format($platform->total) }}</span>
                                <span
                                    class="text-muted small">({{ round(($platform->total / max($stats['total_visitors'], 1)) * 100, 1) }}%)</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- الدول -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card">
                    <h6 class="mb-3">أشهر الدول</h6>

                    @foreach ($countryStats as $country)
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="country-info">
                                @if ($country->country)
                                    <img src="https://flagcdn.com/16x12/{{ strtolower($country->country_iso ?? 'unknown') }}.png"
                                        class="flag-icon" onerror="this.style.display='none'">
                                    <span>{{ $country->country }}</span>
                                @else
                                    <span>غير معروف</span>
                                @endif
                            </div>
                            <div>
                                <span class="fw-bold">{{ number_format($country->total) }}</span>
                                <span
                                    class="text-muted small">({{ round(($country->total / max($stats['total_visitors'], 1)) * 100, 1) }}%)</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- المسارات الأكثر زيارة -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="stats-card">
                    <h6 class="mb-3">المسارات الأكثر زيارة</h6>

                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>المسار</th>
                                    <th>عدد الزيارات</th>
                                    <th>النسبة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pathStats as $path)
                                    <tr>
                                        <td class="path-info">{{ $path->path }}</td>
                                        <td>{{ number_format($path->total) }}</td>
                                        <td>
                                            <div class="progress"
                                                style="height: 20px; background: rgba(255,255,255,0.1);">
                                                <div class="progress-bar"
                                                    style="width: {{ ($path->total / max($pathStats->first()->total, 1)) * 100 }}%; 
                                                            background: var(--primary-gradient);">
                                                    {{ round(($path->total / max($stats['total_visitors'], 1)) * 100, 1) }}%
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- شريط الأدوات -->
        <div class="toolbar">
            <button class="export-btn" onclick="exportData()">
                <i class="fas fa-file-export me-2"></i>
                تصدير البيانات
            </button>
            <button class="clear-btn" onclick="clearOldData()">
                <i class="fas fa-trash-alt me-2"></i>
                حذف الزيارات القديمة
            </button>
            <button class="reset-btn" onclick="resetFilters()">
                <i class="fas fa-redo-alt me-2"></i>
                إعادة تعيين
            </button>
        </div>

        <!-- فلترة -->
        <div class="filter-card">
            <form method="GET" action="{{ route('admin.visitors.index') }}" id="filterForm">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="filter-label">بحث</div>
                        <input type="text" name="search" class="filter-input" value="{{ request('search') }}"
                            placeholder="IP, مسار, دولة, متصفح...">
                    </div>

                    <div class="col-md-2 mb-3">
                        <div class="filter-label">الجهاز</div>
                        <select name="device" class="filter-input">
                            <option value="">الكل</option>
                            <option value="desktop" {{ request('device') == 'desktop' ? 'selected' : '' }}>Desktop
                            </option>
                            <option value="mobile" {{ request('device') == 'mobile' ? 'selected' : '' }}>Mobile</option>
                            <option value="tablet" {{ request('device') == 'tablet' ? 'selected' : '' }}>Tablet</option>
                            <option value="bot" {{ request('device') == 'bot' ? 'selected' : '' }}>Bot</option>
                        </select>
                    </div>

                    <div class="col-md-2 mb-3">
                        <div class="filter-label">الدولة</div>
                        <input type="text" name="country" class="filter-input" value="{{ request('country') }}"
                            placeholder="مثال: Egypt">
                    </div>

                    <div class="col-md-2 mb-3">
                        <div class="filter-label">من تاريخ</div>
                        <input type="date" name="date_from" class="filter-input" value="{{ request('date_from') }}">
                    </div>

                    <div class="col-md-2 mb-3">
                        <div class="filter-label">إلى تاريخ</div>
                        <input type="date" name="date_to" class="filter-input" value="{{ request('date_to') }}">
                    </div>

                    <div class="col-md-1 mb-3 d-flex align-items-end">
                        <button type="submit" class="filter-btn w-100">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- جدول الزوار -->
        <div class="visitors-table">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>IP</th>
                        <th>المسار</th>
                        <th>الدولة</th>
                        <th>المتصفح</th>
                        <th>الجهاز</th>
                        <th>المنصة</th>
                        <th>الوقت</th>
                        <th>التفاصيل</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($visitors as $index => $visitor)
                        <tr>
                            <td>{{ $visitors->firstItem() + $index }}</td>
                            <td>
                                <span class="ip-address">{{ $visitor->ip }}</span>
                            </td>
                            <td>
                                <div class="path-info" title="{{ $visitor->path }}">
                                    {{ $visitor->path }}
                                </div>
                                <small class="text-muted">{{ $visitor->method }}</small>
                            </td>
                            <td>
                                <div class="country-info">
                                    @if ($visitor->country)
                                        <img src="https://flagcdn.com/16x12/{{ strtolower($visitor->country_iso ?? 'unknown') }}.png"
                                            class="flag-icon" onerror="this.style.display='none'">
                                        <span>{{ $visitor->country }}</span>
                                    @else
                                        -
                                    @endif
                                </div>
                                @if ($visitor->city)
                                    <small class="text-muted d-block">{{ $visitor->city }}</small>
                                @endif
                            </td>
                            <td>
                                <i class="fab fa-{{ strtolower($visitor->browser) }} me-1"></i>
                                {{ $visitor->browser }}
                                @if ($visitor->browser_version)
                                    <small class="text-muted">v{{ $visitor->browser_version }}</small>
                                @endif
                            </td>
                            <td>
                                @if ($visitor->is_desktop)
                                    <span class="badge-device badge-desktop">
                                        <i class="fas fa-desktop"></i> Desktop
                                    </span>
                                @elseif($visitor->is_mobile)
                                    <span class="badge-device badge-mobile">
                                        <i class="fas fa-mobile-alt"></i> Mobile
                                    </span>
                                @elseif($visitor->is_tablet)
                                    <span class="badge-device badge-tablet">
                                        <i class="fas fa-tablet-alt"></i> Tablet
                                    </span>
                                @elseif($visitor->is_bot)
                                    <span class="badge-device badge-bot">
                                        <i class="fas fa-robot"></i> Bot
                                    </span>
                                @endif
                            </td>
                            <td>{{ $visitor->platform ?? '-' }}</td>
                            <td>
                                <div>{{ $visitor->created_at->format('Y-m-d') }}</div>
                                <small class="time-info">{{ $visitor->created_at->format('H:i:s') }}</small>
                            </td>
                            <td>
                                <a href="javascript:void(0)" class="detail-link"
                                    onclick="viewDetails({{ $visitor->id }})">
                                    <i class="fas fa-info-circle"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="fas fa-database fa-2x mb-3 d-block" style="color: rgba(255,255,255,0.3);"></i>
                                <p class="mb-0">لا توجد بيانات زيارات</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if ($visitors->hasPages())
                <div class="m-3">
                    <nav>
                        <ul class="pagination">
                            {{-- Previous Page Link --}}
                            @if ($visitors->onFirstPage())
                                <li class="page-item disabled" aria-disabled="true">
                                    <span class="page-link waves-effect" aria-hidden="true">‹</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link waves-effect" href="{{ $visitors->previousPageUrl() }}"
                                        rel="prev">‹</a>
                                </li>
                            @endif

                            {{-- Pagination Elements --}}
                            @foreach ($visitors->links()->elements[0] as $page => $url)
                                @if ($page == $visitors->currentPage())
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
                            @if ($visitors->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link waves-effect" href="{{ $visitors->nextPageUrl() }}"
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

    <!-- Modal لعرض التفاصيل -->
    <div class="modal fade" id="visitorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="background: var(--dark-card);">
                <div class="modal-header">
                    <h5 class="modal-title text-white">تفاصيل الزائر</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row" id="visitorDetails"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">إغلاق</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // بيانات الرسم البياني
        const dailyVisits = @json($dailyVisits);

        // رسم بياني للزيارات
        const ctx = document.getElementById('visitsChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: dailyVisits.map(v => v.date).reverse(),
                datasets: [{
                        label: 'إجمالي الزيارات',
                        data: dailyVisits.map(v => v.total).reverse(),
                        borderColor: '#696cff',
                        backgroundColor: 'rgba(105, 108, 255, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'زوار فريدين',
                        data: dailyVisits.map(v => v.unique_visitors).reverse(),
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            color: '#fff'
                        }
                    }
                },
                scales: {
                    y: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#fff'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#fff'
                        }
                    }
                }
            }
        });

        // عرض تفاصيل الزائر
        function viewDetails(id) {
            $.ajax({
                url: `/admin/visitors/${id}`,
                type: 'GET',
                success: function(visitor) {
                    let html = '';

                    for (let [key, value] of Object.entries(visitor)) {
                        if (value && !['id', 'created_at', 'updated_at'].includes(key)) {
                            if (typeof value === 'object') {
                                value = JSON.stringify(value, null, 2);
                            }

                            html += `
                                <div class="col-md-6 mb-3">
                                    <div class="p-3" style="background: rgba(255,255,255,0.05); border-radius: 8px;">
                                        <small class="text-white-50 d-block mb-1">${key}</small>
                                        <div class="text-white" style="word-break: break-word;">${value}</div>
                                    </div>
                                </div>
                            `;
                        }
                    }

                    $('#visitorDetails').html(html);
                    $('#visitorModal').modal('show');
                }
            });
        }

        // تصدير البيانات
        function exportData() {
            window.location.href = '{{ route('admin.visitors.export') }}' + window.location.search;
        }

        // حذف البيانات القديمة
        function clearOldData() {
            Swal.fire({
                title: 'حذف الزيارات القديمة',
                input: 'number',
                inputLabel: 'عدد الأيام',
                inputPlaceholder: 'مثال: 30',
                inputAttributes: {
                    min: 1,
                    max: 365
                },
                showCancelButton: true,
                confirmButtonText: 'حذف',
                cancelButtonText: 'إلغاء',
                confirmButtonColor: '#dc3545',
                inputValidator: (value) => {
                    if (!value) {
                        return 'الرجاء إدخال عدد الأيام';
                    }
                    if (value < 1 || value > 365) {
                        return 'عدد الأيام يجب أن يكون بين 1 و 365';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('admin.visitors.clear-old') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            days: result.value
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'تم الحذف',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        }
                    });
                }
            });
        }

        // إعادة تعيين الفلاتر
        function resetFilters() {
            window.location.href = '{{ route('admin.visitors.index') }}';
        }

        // تحديث تلقائي كل 30 ثانية
        setInterval(function() {
            location.reload();
        }, 30000);

        // رسائل التنبيه
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
    </script>
@endsection
