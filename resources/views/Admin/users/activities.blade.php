@extends('Admin.layout.master')

@section('title', 'أنشطة المستخدم: ' . $user->name)

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: "Cairo", sans-serif !important;
        }

        .activities-card {
            /* background: white; */
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        .activities-header {
            border-bottom: 2px solid #f8f9fa;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .timeline {
            position: relative;
            padding-right: 30px;
        }

        .timeline:before {
            content: '';
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: #696cff;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 25px;
        }

        .timeline-item:before {
            content: '';
            position: absolute;
            right: -33px;
            top: 5px;
            width: 13px;
            height: 13px;
            border-radius: 50%;
            background: white;
            border: 3px solid #696cff;
        }

        .timeline-content {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            position: relative;
        }

        .timeline-icon {
            position: absolute;
            left: -50px;
            top: 50%;
            transform: translateY(-50%);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
        }

        .timeline-date {
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .timeline-title {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .timeline-description {
            color: #495057;
            font-size: 14px;
            line-height: 1.6;
        }

        .activity-type-1 .timeline-icon {
            background: #667eea;
        }

        .activity-type-2 .timeline-icon {
            background: #764ba2;
        }

        .activity-type-3 .timeline-icon {
            background: #ea4335;
        }

        .activity-type-4 .timeline-icon {
            background: #1877f2;
        }

        .activity-type-5 .timeline-icon {
            background: #2ecc71;
        }

        .activity-type-6 .timeline-icon {
            background: #f39c12;
        }

        .empty-state {
            text-align: center;
            padding: 50px 20px;
        }

        .empty-state-icon {
            font-size: 60px;
            color: #dee2e6;
            margin-bottom: 20px;
        }

        .empty-state-text {
            color: #6c757d;
            margin-bottom: 20px;
        }

        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border-top: 4px solid #696cff;
            margin-bottom: 20px;
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

        .icon-activities {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .icon-today {
            background: #e7f5ff;
            color: #0c63e4;
        }

        .icon-week {
            background: #d4edda;
            color: #155724;
        }

        .icon-month {
            background: #fff3cd;
            color: #856404;
        }

        .stats-number {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stats-label {
            color: #6c757d;
            font-size: 14px;
        }

        .user-info-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            border-right: 4px solid #696cff;
        }

        .filter-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 8px 20px;
            border-radius: 25px;
            background: #f8f9fa;
            color: #6c757d;
            border: 1px solid #dee2e6;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .filter-btn:hover {
            background: #e9ecef;
        }

        .filter-btn.active {
            background: #696cff;
            color: white;
            border-color: #696cff;
        }

        @media (max-width: 768px) {
            .timeline-icon {
                position: relative;
                left: 0;
                top: 0;
                transform: none;
                margin-bottom: 15px;
            }

            .filter-buttons {
                justify-content: center;
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
                    <a href="{{ route('admin.users.index') }}">المستخدمين</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.users.show', $user) }}">{{ $user->name }}</a>
                </li>
                <li class="breadcrumb-item active">الأنشطة</li>
            </ol>
        </nav>

        <!-- معلومات المستخدم -->
        <div class="user-info-card" bis_skin_checked="1">
            <div class="row align-items-center" bis_skin_checked="1">
                <div class="col" bis_skin_checked="1">
                    <h5 class="mb-1">{{ $user->name }}</h5>
                    <p class="mb-0">
                        <i class="fas fa-envelope me-2 text-muted"></i>
                        {{ $user->email }}
                    </p>
                </div>
                <div class="col-auto" bis_skin_checked="1">
                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للتفاصيل
                    </a>
                </div>
            </div>
        </div>

        <!-- الإحصائيات -->
        <div class="row mb-4" bis_skin_checked="1">
            <div class="col-lg-3 col-md-6 mb-4" bis_skin_checked="1">
                <div class="stats-card" bis_skin_checked="1">
                    <div class="stats-icon icon-activities" bis_skin_checked="1">
                        <i class="fas fa-history"></i>
                    </div>
                    <div class="stats-number" bis_skin_checked="1">
                        {{-- يمكنك استبدال هذا برقم حقيقي من قاعدة البيانات --}}
                        42
                    </div>
                    <div class="stats-label" bis_skin_checked="1">إجمالي الأنشطة</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4" bis_skin_checked="1">
                <div class="stats-card" bis_skin_checked="1">
                    <div class="stats-icon icon-today" bis_skin_checked="1">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="stats-number" bis_skin_checked="1">
                        {{-- يمكنك استبدال هذا برقم حقيقي من قاعدة البيانات --}}
                        5
                    </div>
                    <div class="stats-label" bis_skin_checked="1">أنشطة اليوم</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4" bis_skin_checked="1">
                <div class="stats-card" bis_skin_checked="1">
                    <div class="stats-icon icon-week" bis_skin_checked="1">
                        <i class="fas fa-calendar-week"></i>
                    </div>
                    <div class="stats-number" bis_skin_checked="1">
                        {{-- يمكنك استبدال هذا برقم حقيقي من قاعدة البيانات --}}
                        18
                    </div>
                    <div class="stats-label" bis_skin_checked="1">أنشطة الأسبوع</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4" bis_skin_checked="1">
                <div class="stats-card" bis_skin_checked="1">
                    <div class="stats-icon icon-month" bis_skin_checked="1">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stats-number" bis_skin_checked="1">
                        {{-- يمكنك استبدال هذا برقم حقيقي من قاعدة البيانات --}}
                        35
                    </div>
                    <div class="stats-label" bis_skin_checked="1">أنشطة الشهر</div>
                </div>
            </div>
        </div>

        <!-- أزرار التصفية -->
        <div class="filter-buttons" bis_skin_checked="1">
            <button class="filter-btn active" onclick="filterActivities('all')">
                جميع الأنشطة
            </button>
            <button class="filter-btn" onclick="filterActivities('orders')">
                <i class="fas fa-shopping-cart me-2"></i>الطلبات
            </button>
            <button class="filter-btn" onclick="filterActivities('reviews')">
                <i class="fas fa-star me-2"></i>التقييمات
            </button>
            <button class="filter-btn" onclick="filterActivities('favourites')">
                <i class="fas fa-heart me-2"></i>المفضلة
            </button>
            <button class="filter-btn" onclick="filterActivities('profile')">
                <i class="fas fa-user me-2"></i>الملف الشخصي
            </button>
        </div>

        <div class="row" bis_skin_checked="1">
            <div class="col-12" bis_skin_checked="1">
                <div class="activities-card" bis_skin_checked="1">
                    <div class="activities-header" bis_skin_checked="1">
                        <div class="d-flex justify-content-between align-items-center" bis_skin_checked="1">
                            <div bis_skin_checked="1">
                                <h5 class="mb-1">سجل الأنشطة</h5>
                                <p class="text-muted mb-0">عرض جميع أنشطة {{ $user->name }} على النظام</p>
                            </div>
                            <div class="btn-group" bis_skin_checked="1">
                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-info">
                                    <i class="fas fa-user me-2"></i>تفاصيل المستخدم
                                </a>
                                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-right me-2"></i>جميع المستخدمين
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- الجدول الزمني للأنشطة -->
                    <div class="timeline" id="activitiesTimeline" bis_skin_checked="1">
                        <!-- مثال لأنشطة الطلبات -->
                        <div class="timeline-item activity-type-1" data-type="orders">
                            <div class="timeline-content" bis_skin_checked="1">
                                <div class="timeline-icon" bis_skin_checked="1">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                                <div class="timeline-date" bis_skin_checked="1">
                                    <i class="far fa-clock me-1"></i>
                                    {{ now()->subHours(2)->translatedFormat('d M Y - h:i A') }}
                                </div>
                                <div class="timeline-title" bis_skin_checked="1">
                                    قام بإنشاء طلب جديد
                                </div>
                                <div class="timeline-description" bis_skin_checked="1">
                                    تم إنشاء طلب جديد برقم #ORD-2025-00123 بقيمة 250.00 ج.م
                                </div>
                            </div>
                        </div>

                        <!-- مثال لأنشطة التقييمات -->
                        <div class="timeline-item activity-type-2" data-type="reviews">
                            <div class="timeline-content" bis_skin_checked="1">
                                <div class="timeline-icon" bis_skin_checked="1">
                                    <i class="fas fa-star"></i>
                                </div>
                                <div class="timeline-date" bis_skin_checked="1">
                                    <i class="far fa-clock me-1"></i>
                                    {{ now()->subDays(1)->translatedFormat('d M Y - h:i A') }}
                                </div>
                                <div class="timeline-title" bis_skin_checked="1">
                                    أضاف تقييماً جديداً
                                </div>
                                <div class="timeline-description" bis_skin_checked="1">
                                    قام بتقييم منتج "تي شيرت رجالي" بـ 4.5/5 نجوم
                                </div>
                            </div>
                        </div>

                        <!-- مثال لأنشطة المفضلة -->
                        <div class="timeline-item activity-type-3" data-type="favourites">
                            <div class="timeline-content" bis_skin_checked="1">
                                <div class="timeline-icon" bis_skin_checked="1">
                                    <i class="fas fa-heart"></i>
                                </div>
                                <div class="timeline-date" bis_skin_checked="1">
                                    <i class="far fa-clock me-1"></i>
                                    {{ now()->subDays(2)->translatedFormat('d M Y - h:i A') }}
                                </div>
                                <div class="timeline-title" bis_skin_checked="1">
                                    أضاف منتجاً إلى المفضلة
                                </div>
                                <div class="timeline-description" bis_skin_checked="1">
                                    أضاف منتج "حذاء رياضي" إلى قائمة المنتجات المفضلة
                                </div>
                            </div>
                        </div>

                        <!-- مثال لأنشطة الملف الشخصي -->
                        <div class="timeline-item activity-type-4" data-type="profile">
                            <div class="timeline-content" bis_skin_checked="1">
                                <div class="timeline-icon" bis_skin_checked="1">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="timeline-date" bis_skin_checked="1">
                                    <i class="far fa-clock me-1"></i>
                                    {{ now()->subDays(3)->translatedFormat('d M Y - h:i A') }}
                                </div>
                                <div class="timeline-title" bis_skin_checked="1">
                                    قام بتحديث معلوماته الشخصية
                                </div>
                                <div class="timeline-description" bis_skin_checked="1">
                                    قام بتحديث رقم الهاتف وصورة الملف الشخصي
                                </div>
                            </div>
                        </div>

                        <!-- مثال لأنشطة تسجيل الدخول -->
                        <div class="timeline-item activity-type-5" data-type="login">
                            <div class="timeline-content" bis_skin_checked="1">
                                <div class="timeline-icon" bis_skin_checked="1">
                                    <i class="fas fa-sign-in-alt"></i>
                                </div>
                                <div class="timeline-date" bis_skin_checked="1">
                                    <i class="far fa-clock me-1"></i>
                                    {{ now()->subDays(4)->translatedFormat('d M Y - h:i A') }}
                                </div>
                                <div class="timeline-title" bis_skin_checked="1">
                                    قام بتسجيل الدخول
                                </div>
                                <div class="timeline-description" bis_skin_checked="1">
                                    قام بتسجيل الدخول إلى حسابه بنجاح
                                </div>
                            </div>
                        </div>

                        <!-- مثال لأنشطة أخرى -->
                        <div class="timeline-item activity-type-6" data-type="other">
                            <div class="timeline-content" bis_skin_checked="1">
                                <div class="timeline-icon" bis_skin_checked="1">
                                    <i class="fas fa-bell"></i>
                                </div>
                                <div class="timeline-date" bis_skin_checked="1">
                                    <i class="far fa-clock me-1"></i>
                                    {{ now()->subDays(5)->translatedFormat('d M Y - h:i A') }}
                                </div>
                                <div class="timeline-title" bis_skin_checked="1">
                                    تفاعل مع إشعار
                                </div>
                                <div class="timeline-description" bis_skin_checked="1">
                                    قام بقراءة إشعار حول عرض جديد على المنتجات
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="empty-state" id="emptyActivities" style="display: none;" bis_skin_checked="1">
                        <div class="empty-state-icon" bis_skin_checked="1">
                            <i class="fas fa-history"></i>
                        </div>
                        <h5 class="empty-state-text">لا توجد أنشطة</h5>
                        <p class="text-muted">لا توجد أنشطة مطابقة للفلتر المحدد</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        function filterActivities(type) {
            // تحديث أزرار الفلتر
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');

            // تصفية الأنشطة
            const timelineItems = document.querySelectorAll('.timeline-item');
            const emptyState = document.getElementById('emptyActivities');
            let visibleItems = 0;

            timelineItems.forEach(item => {
                if (type === 'all' || item.getAttribute('data-type') === type) {
                    item.style.display = 'block';
                    visibleItems++;
                } else {
                    item.style.display = 'none';
                }
            });

            // إظهار/إخفاء حالة الفراغ
            if (visibleItems === 0) {
                emptyState.style.display = 'block';
            } else {
                emptyState.style.display = 'none';
            }
        }

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
    </script>
@endsection
