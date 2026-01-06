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

        .product-item {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .product-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .product-rank {
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

        .product-info {
            flex-grow: 1;
        }

        .product-name {
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 5px;
        }

        .product-sales {
            color: rgba(255, 255, 255, 0.7);
            font-size: 13px;
        }

        .sales-count {
            font-weight: 700;
            color: #20c997;
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

        .form-control {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--primary-color);
            color: #fff;
            box-shadow: 0 0 0 0.25rem rgba(105, 108, 255, 0.25);
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
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
    <div class="container-xxl flex-grow-1 container-p-y" bis_skin_checked="1">
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
        <div class="date-filter" bis_skin_checked="1">
            <h6 class="mb-3"><i class="fas fa-calendar-alt me-2"></i>فلترة حسب التاريخ</h6>

            <div class="filter-row" bis_skin_checked="1">
                <div class="input-group" bis_skin_checked="1">
                    <span class="input-group-text">من</span>
                    <input type="date" class="form-control" id="dateFrom"
                        value="{{ now()->subDays(30)->format('Y-m-d') }}">
                    <span class="input-group-text">إلى</span>
                    <input type="date" class="form-control" id="dateTo" value="{{ now()->format('Y-m-d') }}">
                </div>

                <select class="form-select" id="chartType">
                    <option value="daily">يومي</option>
                    <option value="weekly">أسبوعي</option>
                    <option value="monthly">شهري</option>
                </select>

                <button class="btn btn-primary" onclick="loadStatistics()">
                    <i class="fas fa-filter me-2"></i>تطبيق الفلتر
                </button>
            </div>
        </div>

        <!-- الإحصائيات الرئيسية -->
        <div class="row mb-4" bis_skin_checked="1">
            <div class="col-lg-3 col-md-6 mb-4" bis_skin_checked="1">
                <div class="stats-card" bis_skin_checked="1">
                    <div class="stats-icon icon-total" bis_skin_checked="1">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stats-number" id="totalOrders">
                        {{ number_format($stats['total_orders']) }}
                    </div>
                    <div class="stats-label">إجمالي الطلبات</div>
                    <div class="stats-change change-up">
                        <i class="fas fa-arrow-up"></i>
                        <span>12% عن الشهر الماضي</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4" bis_skin_checked="1">
                <div class="stats-card" bis_skin_checked="1">
                    <div class="stats-icon icon-revenue" bis_skin_checked="1">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="stats-number" id="totalRevenue">
                        {{ number_format($stats['total_revenue'], 2) }} ج.م
                    </div>
                    <div class="stats-label">إجمالي الإيرادات</div>
                    <div class="stats-change change-up">
                        <i class="fas fa-arrow-up"></i>
                        <span>18% عن الشهر الماضي</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4" bis_skin_checked="1">
                <div class="stats-card" bis_skin_checked="1">
                    <div class="stats-icon icon-average" bis_skin_checked="1">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stats-number" id="averageOrder">
                        {{ number_format($stats['average_order_value'], 2) }} ج.م
                    </div>
                    <div class="stats-label">متوسط قيمة الطلب</div>
                    <div class="stats-change change-up">
                        <i class="fas fa-arrow-up"></i>
                        <span>5% عن الشهر الماضي</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4" bis_skin_checked="1">
                <div class="stats-card" bis_skin_checked="1">
                    <div class="stats-icon icon-today" bis_skin_checked="1">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="stats-number" id="todayOrders">
                        {{ number_format($stats['today_orders']) }}
                    </div>
                    <div class="stats-label">طلبات اليوم</div>
                    <div class="stats-change change-up">
                        <i class="fas fa-arrow-up"></i>
                        <span>3 عن الأمس</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- المخططات -->
        <div class="row" bis_skin_checked="1">
            <!-- مخطط الطلبات حسب الحالة -->
            <div class="col-lg-6 mb-4" bis_skin_checked="1">
                <div class="chart-card" bis_skin_checked="1">
                    <div class="chart-header" bis_skin_checked="1">
                        <h6><i class="fas fa-chart-pie me-2"></i>توزيع الطلبات حسب الحالة</h6>
                        <div class="chart-controls" bis_skin_checked="1">
                            <button class="chart-control active" onclick="changeChartType('pie')">
                                دائري
                            </button>
                            <button class="chart-control" onclick="changeChartType('doughnut')">
                                حلقي
                            </button>
                        </div>
                    </div>
                    <div class="chart-container" bis_skin_checked="1">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- مخطط الإيرادات -->
            <div class="col-lg-6 mb-4" bis_skin_checked="1">
                <div class="chart-card" bis_skin_checked="1">
                    <div class="chart-header" bis_skin_checked="1">
                        <h6><i class="fas fa-chart-bar me-2"></i>الإيرادات خلال الشهر</h6>
                        <div class="chart-controls" bis_skin_checked="1">
                            <button class="chart-control active" onclick="changeRevenueChart('bar')">
                                أعمدة
                            </button>
                            <button class="chart-control" onclick="changeRevenueChart('line')">
                                خطي
                            </button>
                        </div>
                    </div>
                    <div class="chart-container" bis_skin_checked="1">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- المنتجات الأكثر مبيعاً -->
            <div class="col-lg-6 mb-4" bis_skin_checked="1">
                <div class="table-card" bis_skin_checked="1">
                    <div class="table-header" bis_skin_checked="1">
                        <h6 class="mb-0"><i class="fas fa-trophy me-2"></i>المنتجات الأكثر مبيعاً</h6>
                    </div>

                    @if ($stats['top_products']->isEmpty())
                        <div class="empty-chart" bis_skin_checked="1">
                            <i class="fas fa-box"></i>
                            <p>لا توجد بيانات عن المبيعات</p>
                        </div>
                    @else
                        @foreach ($stats['top_products'] as $index => $product)
                            <div class="product-item" bis_skin_checked="1">
                                <div class="product-rank rank-{{ $index + 1 }}">
                                    {{ $index + 1 }}
                                </div>
                                <div class="product-info" bis_skin_checked="1">
                                    <div class="product-name" bis_skin_checked="1">
                                        {{ $product->name }}
                                    </div>
                                    <div class="product-sales" bis_skin_checked="1">
                                        تم بيع <span class="sales-count">{{ $product->total_quantity }}</span> وحدة
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- إحصائيات الحالة -->
            <div class="col-lg-6 mb-4" bis_skin_checked="1">
                <div class="table-card" bis_skin_checked="1">
                    <div class="table-header" bis_skin_checked="1">
                        <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>إحصائيات الحالات</h6>
                    </div>

                    <div class="row" bis_skin_checked="1">
                        @php
                            $statusColors = [
                                'pending' => '#ffc107',
                                'processing' => '#0d6efd',
                                'shipped' => '#0dcaf0',
                                'delivered' => '#198754',
                                'cancelled' => '#dc3545',
                                'refunded' => '#6c757d',
                            ];

                            $statusLabels = [
                                'pending' => 'قيد الانتظار',
                                'processing' => 'تحت المعالجة',
                                'shipped' => 'تم الشحن',
                                'delivered' => 'تم التسليم',
                                'cancelled' => 'ملغي',
                                'refunded' => 'مسترجع',
                            ];
                        @endphp

                        @foreach ($stats['status_counts'] as $status => $count)
                            @if (isset($statusColors[$status]))
                                <div class="col-md-6 mb-3" bis_skin_checked="1">
                                    <div class="stats-card" style="border-top-color: {{ $statusColors[$status] }};"
                                        bis_skin_checked="1">
                                        <div class="stats-icon"
                                            style="background: {{ $statusColors[$status] }}; color: white;">
                                            @switch($status)
                                                @case('pending')
                                                    <i class="fas fa-clock"></i>
                                                @break

                                                @case('processing')
                                                    <i class="fas fa-cog"></i>
                                                @break

                                                @case('shipped')
                                                    <i class="fas fa-truck"></i>
                                                @break

                                                @case('delivered')
                                                    <i class="fas fa-check-circle"></i>
                                                @break

                                                @case('cancelled')
                                                    <i class="fas fa-times-circle"></i>
                                                @break

                                                @case('refunded')
                                                    <i class="fas fa-redo"></i>
                                                @break

                                                @default
                                                    <i class="fas fa-question-circle"></i>
                                            @endswitch
                                        </div>
                                        <div class="stats-number">{{ number_format($count) }}</div>
                                        <div class="stats-label">{{ $statusLabels[$status] ?? $status }}</div>
                                        <div class="stats-change">
                                            @php
                                                $percentage =
                                                    $stats['total_orders'] > 0
                                                        ? ($count / $stats['total_orders']) * 100
                                                        : 0;
                                            @endphp
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
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <script>
        let statusChart, revenueChart;
        let currentChartType = 'pie';
        let currentRevenueChartType = 'bar';

        $(document).ready(function() {
            // تحميل الإحصائيات الأولية
            loadCharts();

            // تحميل الإحصائيات عند تغيير التاريخ
            $('#dateFrom, #dateTo, #chartType').on('change', function() {
                loadStatistics();
            });
        });

        function loadStatistics() {
            const dateFrom = $('#dateFrom').val();
            const dateTo = $('#dateTo').val();
            const chartType = $('#chartType').val();

            // هنا يمكنك إضافة AJAX لجلب الإحصائيات المحدثة
            console.log('جلب الإحصائيات:', {
                dateFrom,
                dateTo,
                chartType
            });

            // في التطبيق الحقيقي، ستقوم بإرسال طلب AJAX وجلب البيانات
            // ثم تحديث المخططات والإحصائيات
        }

        function loadCharts() {
            // بيانات مخطط الحالة
            const statusData = {
                labels: ['قيد الانتظار', 'تحت المعالجة', 'تم الشحن', 'تم التسليم', 'ملغي'],
                datasets: [{
                    data: [
                        {{ $stats['status_counts']['pending'] ?? 0 }},
                        {{ $stats['status_counts']['processing'] ?? 0 }},
                        {{ $stats['status_counts']['shipped'] ?? 0 }},
                        {{ $stats['status_counts']['delivered'] ?? 0 }},
                        {{ $stats['status_counts']['cancelled'] ?? 0 }}
                    ],
                    backgroundColor: [
                        '#ffc107',
                        '#0d6efd',
                        '#0dcaf0',
                        '#198754',
                        '#dc3545'
                    ],
                    borderWidth: 1
                }]
            };

            // بيانات مخطط الإيرادات (بيانات وهمية للعرض)
            const revenueData = {
                labels: ['1', '5', '10', '15', '20', '25', '30'],
                datasets: [{
                    label: 'الإيرادات (ج.م)',
                    data: [1200, 1900, 3000, 2500, 2200, 3000, 4000],
                    backgroundColor: 'rgba(105, 108, 255, 0.2)',
                    borderColor: 'rgba(105, 108, 255, 1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            };

            // إنشاء مخطط الحالة
            const statusCtx = document.getElementById('statusChart').getContext('2d');
            statusChart = new Chart(statusCtx, {
                type: currentChartType,
                data: statusData,
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
                                padding: 20
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
            revenueChart = new Chart(revenueCtx, {
                type: currentRevenueChartType,
                data: revenueData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            rtl: true,
                            bodyFont: {
                                family: 'Cairo'
                            },
                            callbacks: {
                                label: function(context) {
                                    return context.formattedValue + ' ج.م';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value + ' ج.م';
                                }
                            }
                        }
                    }
                }
            });
        }

        function changeChartType(type) {
            currentChartType = type;
            statusChart.destroy();
            loadCharts();

            // تحديث أزرار التحكم
            $('.chart-control').removeClass('active');
            $(`.chart-control[onclick="changeChartType('${type}')"]`).addClass('active');
        }

        function changeRevenueChart(type) {
            currentRevenueChartType = type;
            revenueChart.destroy();
            loadCharts();

            // تحديث أزرار التحكم
            $('.chart-control').removeClass('active');
            $(`.chart-control[onclick="changeRevenueChart('${type}')"]`).addClass('active');
        }
    </script>
@endsection
