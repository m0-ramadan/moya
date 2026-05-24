@extends('Admin.layout.master')

@section('title', 'إحصائيات المحادثات')

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

        .icon-messages {
            background: rgba(12, 99, 228, 0.2);
            color: #0c63e4;
            border: 1px solid rgba(12, 99, 228, 0.3);
        }

        .icon-average {
            background: rgba(21, 87, 36, 0.2);
            color: #20c997;
            border: 1px solid rgba(32, 201, 151, 0.3);
        }

        .icon-today {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }

        .icon-week {
            background: rgba(13, 202, 240, 0.2);
            color: #0dcaf0;
            border: 1px solid rgba(13, 202, 240, 0.3);
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
            margin-bottom: 0;
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
            font-weight: bold;
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
            font-weight: bold;
        }

        .user-item {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .user-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .user-rank {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
        }

        .rank-1 {
            background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
            box-shadow: 0 0 10px rgba(255, 215, 0, 0.4);
        }

        .rank-2 {
            background: linear-gradient(135deg, #c0c0c0 0%, #e0e0e0 100%);
            box-shadow: 0 0 10px rgba(192, 192, 192, 0.4);
        }

        .rank-3 {
            background: linear-gradient(135deg, #cd7f32 0%, #e89c4e 100%);
            box-shadow: 0 0 10px rgba(205, 127, 50, 0.4);
        }

        .rank-default {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .user-avatar-container {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .user-avatar-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .user-info {
            flex-grow: 1;
        }

        .user-name {
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-stats {
            color: rgba(255, 255, 255, 0.7);
            font-size: 13px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .user-stats span i {
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
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Breadcrumbs -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                         <a href="{{ route('admin.home') }}">الرئيسية</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.chats.index') }}">المحادثات</a>
                    </li>
                    <li class="breadcrumb-item active">الإحصائيات</li>
                </ol>
            </nav>

            <div class="d-flex gap-2">
                <a href="{{ route('admin.chats.live') }}" class="btn btn-danger">
                    <i class="fas fa-broadcast-tower me-2"></i>البث الحي للمحادثات
                </a>
                <a href="{{ route('admin.chats.index') }}" class="btn btn-secondary">
                    <i class="fas fa-list me-2"></i>عرض المحادثات
                </a>
            </div>
        </div>

        <!-- General Stats Cards -->
        <div class="row mb-4">
            <div class="col-lg-2-5 col-md-4 col-sm-6 mb-4">
                <div class="stats-card">
                    <div class="stats-icon icon-total">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div class="stats-number">
                        {{ number_format($generalStats['total_chats'] ?? 0) }}
                    </div>
                    <div class="stats-label">إجمالي المحادثات</div>
                </div>
            </div>

            <div class="col-lg-2-5 col-md-4 col-sm-6 mb-4">
                <div class="stats-card" style="border-top-color: #0c63e4;">
                    <div class="stats-icon icon-messages">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                    <div class="stats-number">
                        {{ number_format($generalStats['total_messages'] ?? 0) }}
                    </div>
                    <div class="stats-label">إجمالي الرسائل</div>
                </div>
            </div>

            <div class="col-lg-2-5 col-md-4 col-sm-6 mb-4">
                <div class="stats-card" style="border-top-color: #20c997;">
                    <div class="stats-icon icon-average">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <div class="stats-number">
                        {{ $generalStats['avg_messages_per_chat'] ?? 0 }}
                    </div>
                    <div class="stats-label">متوسط الرسائل/المحادثة</div>
                </div>
            </div>

            <div class="col-lg-2-5 col-md-6 col-sm-6 mb-4">
                <div class="stats-card" style="border-top-color: #ffc107;">
                    <div class="stats-icon icon-today">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="stats-number">
                        {{ number_format($generalStats['active_chats_today'] ?? 0) }}
                    </div>
                    <div class="stats-label">النشطة اليوم</div>
                </div>
            </div>

            <div class="col-lg-2-5 col-md-6 col-sm-12 mb-4">
                <div class="stats-card" style="border-top-color: #0dcaf0;">
                    <div class="stats-icon icon-week">
                        <i class="fas fa-calendar-week"></i>
                    </div>
                    <div class="stats-number">
                        {{ number_format($generalStats['active_chats_week'] ?? 0) }}
                    </div>
                    <div class="stats-label">النشطة هذا الأسبوع</div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row mb-4">
            <!-- Messages Activity Chart -->
            <div class="col-lg-8 mb-4">
                <div class="chart-card">
                    <div class="chart-header">
                        <h6><i class="fas fa-chart-line me-2"></i>حركة الرسائل اليومية (آخر 7 أيام)</h6>
                        <div class="chart-controls">
                            <button class="chart-control active" onclick="changeActivityChartType('line')">
                                خطي
                            </button>
                            <button class="chart-control" onclick="changeActivityChartType('bar')">
                                أعمدة
                            </button>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="messagesActivityChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Message Types Doughnut Chart -->
            <div class="col-lg-4 mb-4">
                <div class="chart-card">
                    <div class="chart-header">
                        <h6><i class="fas fa-chart-pie me-2"></i>توزيع أنواع الرسائل</h6>
                    </div>
                    <div class="chart-container">
                        @if(empty($messageTypeStats))
                            <div class="empty-chart">
                                <i class="fas fa-comments-dollar"></i>
                                <p>لا توجد بيانات لأنواع الرسائل بعد</p>
                            </div>
                        @else
                            <canvas id="messageTypeChart"></canvas>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Chat Types Statistics Breakdown -->
            <div class="col-lg-6 mb-4">
                <div class="table-card h-100">
                    <div class="table-header">
                        <h6 class="mb-0 text-primary"><i class="fas fa-filter me-2"></i>تحليل تصنيفات المحادثات</h6>
                    </div>

                    <div class="row">
                        <!-- User to User -->
                        <div class="col-md-12 mb-3">
                            <div class="stats-card" style="border-top-color: #696cff;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-1 fw-bold text-white">محادثات العملاء (مستخدم إلى مستخدم)</h5>
                                        <p class="text-muted mb-0 small">محادثات عامة وتبادل رسائل بين المستخدمين</p>
                                    </div>
                                    <div class="text-end">
                                        <div class="fs-4 fw-bold text-primary">{{ number_format($typeStats['user_user']['count'] ?? 0) }} محادثة</div>
                                        <div class="text-muted small">{{ number_format($typeStats['user_user']['messages'] ?? 0) }} رسالة</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- User to Driver -->
                        <div class="col-md-12 mb-3">
                            <div class="stats-card" style="border-top-color: #20c997;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-1 fw-bold text-white">محادثات التوصيل (مستخدم إلى سائق)</h5>
                                        <p class="text-muted mb-0 small">تنسيق طلبات التوصيل وخط سير العمل الحقيقي</p>
                                    </div>
                                    <div class="text-end">
                                        <div class="fs-4 fw-bold text-success">{{ number_format($typeStats['user_driver']['count'] ?? 0) }} محادثة</div>
                                        <div class="text-muted small">{{ number_format($typeStats['user_driver']['messages'] ?? 0) }} رسالة</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Driver to Driver -->
                        <div class="col-md-12 mb-3">
                            <div class="stats-card" style="border-top-color: #fd7e14;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-1 fw-bold text-white">تنسيق اللوجستيات (سائق إلى سائق)</h5>
                                        <p class="text-muted mb-0 small">المحادثات والتنسيقات البينية لفرقة السائقين</p>
                                    </div>
                                    <div class="text-end">
                                        <div class="fs-4 fw-bold text-warning">{{ number_format($typeStats['driver_driver']['count'] ?? 0) }} محادثة</div>
                                        <div class="text-muted small">{{ number_format($typeStats['driver_driver']['messages'] ?? 0) }} رسالة</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Leaderboard: Active Chat Participants -->
            <div class="col-lg-6 mb-4">
                <div class="table-card h-100">
                    <div class="table-header">
                        <h6 class="mb-0 text-primary"><i class="fas fa-trophy me-2"></i>الأعضاء الأكثر نشاطاً في المحادثات</h6>
                    </div>

                    @if($topUsers->isEmpty())
                        <div class="empty-chart">
                            <i class="fas fa-users-slash"></i>
                            <p>لا توجد بيانات للأعضاء النشطين حالياً</p>
                        </div>
                    @else
                        @foreach($topUsers as $index => $participant)
                            @php
                                $avatarUrl = null;
                                if ($participant->sender_type === 'App\Models\User') {
                                    $u = \App\Models\User::find($participant->sender_id);
                                    if ($u && $u->avatar) {
                                        $avatarUrl = asset('storage/' . $u->avatar);
                                    }
                                } else {
                                    $d = \App\Models\Driver::find($participant->sender_id);
                                    if ($d && $d->personal_photo) {
                                        $avatarUrl = asset('storage/' . $d->personal_photo);
                                    }
                                }
                            @endphp
                            <div class="user-item">
                                <!-- Rank -->
                                <div class="user-rank rank-{{ $index < 3 ? $index + 1 : 'default' }}">
                                    {{ $index + 1 }}
                                </div>

                                <!-- Avatar with dynamic local fallbacks -->
                                <div class="user-avatar-container">
                                    @if($avatarUrl)
                                        <img src="{{ $avatarUrl }}" alt="{{ $participant->name }}">
                                    @else
                                        <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-secondary text-white">
                                            @if($participant->type === 'Driver')
                                                <i class="fas fa-truck" style="font-size: 14px;"></i>
                                            @else
                                                <i class="fas fa-user" style="font-size: 14px;"></i>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                <!-- Information -->
                                <div class="user-info">
                                    <div class="user-name">
                                        {{ $participant->name }}
                                        <span class="badge {{ $participant->type === 'Driver' ? 'bg-label-warning' : 'bg-label-primary' }} fs-tiny">
                                            {{ $participant->type === 'Driver' ? 'سائق' : 'عميل' }}
                                        </span>
                                    </div>
                                    <div class="user-stats">
                                        <span>
                                            <i class="fas fa-envelope"></i>
                                            {{ number_format($participant->message_count ?? 0) }} رسالة مرسلة
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        let messagesActivityChart, messageTypeChart;
        let currentActivityChartType = 'line';

        $(document).ready(function() {
            loadCharts();
        });

        function loadCharts() {
            // 1. Daily Messages Activity Dataset
            const activityDates = {!! json_encode($weekMessages->pluck('date')->map(function($date) {
                return \Carbon\Carbon::parse($date)->format('d M');
            })) !!};
            const activityCounts = {!! json_encode($weekMessages->pluck('count')) !!};

            const activityData = {
                labels: activityDates.length > 0 ? activityDates : ['لا توجد بيانات'],
                datasets: [{
                    label: 'عدد الرسائل',
                    data: activityCounts.length > 0 ? activityCounts : [0],
                    backgroundColor: 'rgba(105, 108, 255, 0.2)',
                    borderColor: 'rgba(105, 108, 255, 1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true
                }]
            };

            const activityCtx = document.getElementById('messagesActivityChart').getContext('2d');
            if (messagesActivityChart) messagesActivityChart.destroy();
            messagesActivityChart = new Chart(activityCtx, {
                type: currentActivityChartType,
                data: activityData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: {
                                color: '#fff',
                                font: { family: 'Cairo', size: 12 }
                            }
                        },
                        tooltip: {
                            rtl: true,
                            bodyFont: { family: 'Cairo' },
                            callbacks: {
                                label: function(context) {
                                    return context.raw + ' رسالة';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(255, 255, 255, 0.1)' },
                            ticks: {
                                color: 'rgba(255, 255, 255, 0.7)',
                                font: { family: 'Cairo' }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: {
                                color: 'rgba(255, 255, 255, 0.7)',
                                font: { family: 'Cairo' }
                            }
                        }
                    }
                }
            });

            // 2. Message Types Doughnut Dataset
            @if(!empty($messageTypeStats))
                @php
                    $translatedMessageTypes = [
                        'text' => 'نصي',
                        'image' => 'صورة',
                        'file' => 'ملف/مستند',
                        'location' => 'موقع جغرافي',
                        'voice' => 'رسالة صوتية',
                        'video' => 'فيديو'
                    ];

                    $jsLabels = [];
                    $jsValues = [];
                    foreach($messageTypeStats as $type => $count) {
                        $jsLabels[] = $translatedMessageTypes[$type] ?? $type;
                        $jsValues[] = $count;
                    }
                @endphp

                const typeData = {
                    labels: {!! json_encode($jsLabels) !!},
                    datasets: [{
                        data: {!! json_encode($jsValues) !!},
                        backgroundColor: [
                            '#696cff',
                            '#20c997',
                            '#ffc107',
                            '#0dcaf0',
                            '#fd7e14',
                            '#6f42c1'
                        ],
                        borderWidth: 1,
                        borderColor: 'rgba(255, 255, 255, 0.1)'
                    }]
                };

                const typeCtx = document.getElementById('messageTypeChart').getContext('2d');
                if (messageTypeChart) messageTypeChart.destroy();
                messageTypeChart = new Chart(typeCtx, {
                    type: 'doughnut',
                    data: typeData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                rtl: true,
                                labels: {
                                    font: { family: 'Cairo', size: 11 },
                                    color: '#fff',
                                    padding: 15
                                }
                            },
                            tooltip: {
                                rtl: true,
                                bodyFont: { family: 'Cairo' },
                                callbacks: {
                                    label: function(context) {
                                        return context.label + ': ' + context.raw + ' رسالة';
                                    }
                                }
                            }
                        }
                    }
                });
            @endif
        }

        function changeActivityChartType(type) {
            currentActivityChartType = type;
            loadCharts();

            // Update Control UI Buttons
            $('.chart-control').removeClass('active');
            $(`.chart-control[onclick="changeActivityChartType('${type}')"]`).addClass('active');
        }
    </script>
@endsection
