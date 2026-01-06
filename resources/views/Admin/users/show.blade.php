@extends('Admin.layout.master')

@section('title', 'تفاصيل المستخدم: ' . $user->name)

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: "Cairo", sans-serif !important;
        }

        .detail-card {
            /* background: white; */
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .detail-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px 30px;
            position: relative;
        }

        .detail-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 5px solid rgba(255, 255, 255, 0.3);
            margin: 0 auto 20px;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: white;
        }

        .detail-avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        .detail-body {
            padding: 30px;
        }

        .info-section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .info-section h6 {
            color: #696cff;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f8f9fa;
        }

        .info-row {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .info-label {
            min-width: 150px;
            font-weight: 600;
            color: #495057;
        }

        .info-value {
            color: #2c3e50;
            font-weight: 500;
            flex-grow: 1;
        }

        .badge-custom {
            padding: 6px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 13px;
        }

        .badge-social {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .badge-google {
            background: #ea4335;
            color: white;
        }

        .badge-facebook {
            background: #1877f2;
            color: white;
        }

        .badge-apple {
            background: #000000;
            color: white;
        }

        .badge-email {
            background: #e7f5ff;
            color: #0c63e4;
        }

        .social-icons {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .social-icon {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: white;
        }

        .stats-card {
            background: #242f3b;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
            border-top: 4px solid #696cff;
            transition: transform 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .stats-icon {
            font-size: 32px;
            color: #696cff;
            margin-bottom: 15px;
        }

        .stats-number {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stats-label {
            color: #6c757d;
            font-size: 14px;
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
            border-radius: 10px;
            padding: 15px;
        }

        .timeline-date {
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 5px;
        }

        .timeline-text {
            color: #2c3e50;
            font-weight: 500;
        }

        .action-buttons {
            position: absolute;
            left: 30px;
            top: 30px;
            display: flex;
            gap: 10px;
        }

        .btn-action {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-action:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .recent-item {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }

        .recent-item:hover {
            transform: translateX(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .recent-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .recent-item-title {
            font-weight: 600;
            color: #2c3e50;
        }

        .recent-item-date {
            font-size: 12px;
            color: #6c757d;
        }

        .recent-item-content {
            color: #495057;
            font-size: 14px;
        }

        .empty-recent {
            text-align: center;
            padding: 30px 20px;
            color: #6c757d;
        }

        .empty-recent i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #dee2e6;
        }

        @media (max-width: 768px) {
            .action-buttons {
                position: relative;
                left: 0;
                top: 0;
                margin-bottom: 20px;
                justify-content: center;
            }

            .info-row {
                flex-direction: column;
                align-items: flex-start;
            }

            .info-label {
                min-width: auto;
                margin-bottom: 5px;
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
                <li class="breadcrumb-item active">تفاصيل</li>
            </ol>
        </nav>

        <div class="row" bis_skin_checked="1">
            <div class="col-12" bis_skin_checked="1">
                <div class="detail-card" bis_skin_checked="1">
                    <div class="detail-header" bis_skin_checked="1">
                        <div class="action-buttons" bis_skin_checked="1">
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn-action" title="تعديل">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ route('admin.users.index') }}" class="btn-action" title="رجوع">
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>

                        <div class="text-center" bis_skin_checked="1">
                            <div class="detail-avatar" bis_skin_checked="1">
                                @if ($user->image)
                                    <img src="{{ asset('storage/' . $user->image) }}" alt="{{ $user->name }}">
                                @else
                                    <i class="fas fa-user"></i>
                                @endif
                            </div>
                            <h4 class="mb-2">{{ $user->name }}</h4>
                            <p class="mb-0 opacity-75">{{ $user->email }}</p>
                            @if ($user->phone)
                                <p class="mb-0 opacity-75">{{ $user->phone }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="detail-body" bis_skin_checked="1">
                        <div class="row" bis_skin_checked="1">
                            <div class="col-lg-8" bis_skin_checked="1">
                                <!-- المعلومات الأساسية -->
                                <div class="info-section" bis_skin_checked="1">
                                    <h6><i class="fas fa-info-circle me-2"></i>المعلومات الأساسية</h6>

                                    <div class="info-row" bis_skin_checked="1">
                                        <div class="info-label" bis_skin_checked="1">الاسم الكامل:</div>
                                        <div class="info-value" bis_skin_checked="1">{{ $user->name }}</div>
                                    </div>

                                    <div class="info-row" bis_skin_checked="1">
                                        <div class="info-label" bis_skin_checked="1">البريد الإلكتروني:</div>
                                        <div class="info-value" bis_skin_checked="1">
                                            <a href="mailto:{{ $user->email }}" class="text-decoration-none">
                                                {{ $user->email }}
                                            </a>
                                        </div>
                                    </div>

                                    @if ($user->phone)
                                        <div class="info-row" bis_skin_checked="1">
                                            <div class="info-label" bis_skin_checked="1">رقم الهاتف:</div>
                                            <div class="info-value" bis_skin_checked="1">
                                                <a href="tel:{{ $user->phone }}" class="text-decoration-none">
                                                    {{ $user->phone }}
                                                </a>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="info-row" bis_skin_checked="1">
                                        <div class="info-label" bis_skin_checked="1">معرف المستخدم:</div>
                                        <div class="info-value" bis_skin_checked="1">#{{ $user->id }}</div>
                                    </div>
                                </div>

                                <!-- طريقة التسجيل -->
                                <div class="info-section" bis_skin_checked="1">
                                    <h6><i class="fas fa-sign-in-alt me-2"></i>طريقة التسجيل</h6>

                                    <div class="info-row" bis_skin_checked="1">
                                        <div class="info-label" bis_skin_checked="1">النوع:</div>
                                        <div class="info-value" bis_skin_checked="1">
                                            @if ($user->google_id || $user->facebook_id || $user->apple_id)
                                                <span class="badge-custom badge-social">
                                                    <i class="fas fa-share-alt me-1"></i>
                                                    التواصل الاجتماعي
                                                </span>
                                                <div class="social-icons" bis_skin_checked="1">
                                                    @if ($user->google_id)
                                                        <span class="social-icon" style="background: #ea4335;"
                                                            title="تسجيل الدخول بجوجل">
                                                            <i class="fab fa-google"></i>
                                                        </span>
                                                    @endif
                                                    @if ($user->facebook_id)
                                                        <span class="social-icon" style="background: #1877f2;"
                                                            title="تسجيل الدخول بفيسبوك">
                                                            <i class="fab fa-facebook-f"></i>
                                                        </span>
                                                    @endif
                                                    @if ($user->apple_id)
                                                        <span class="social-icon" style="background: #000000;"
                                                            title="تسجيل الدخول بأبل">
                                                            <i class="fab fa-apple"></i>
                                                        </span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="badge-custom badge-email">
                                                    <i class="fas fa-envelope me-1"></i>
                                                    البريد الإلكتروني
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="info-row" bis_skin_checked="1">
                                        <div class="info-label" bis_skin_checked="1">تاريخ التسجيل:</div>
                                        <div class="info-value" bis_skin_checked="1">
                                            {{ $user->created_at->translatedFormat('d M Y - h:i A') }}
                                        </div>
                                    </div>

                                    <div class="info-row" bis_skin_checked="1">
                                        <div class="info-label" bis_skin_checked="1">آخر تحديث:</div>
                                        <div class="info-value" bis_skin_checked="1">
                                            {{ $user->updated_at->translatedFormat('d M Y - h:i A') }}
                                        </div>
                                    </div>
                                </div>

                                <!-- الطلبات الحديثة -->
                                <div class="info-section" bis_skin_checked="1">
                                    <div class="d-flex justify-content-between align-items-center mb-3"
                                        bis_skin_checked="1">
                                        <h6 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>الطلبات الحديثة</h6>
                                        <a href="{{ route('admin.users.orders', $user) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            عرض الكل
                                        </a>
                                    </div>

                                    @forelse($user->orders as $order)
                                        <div class="recent-item" bis_skin_checked="1">
                                            <div class="recent-item-header" bis_skin_checked="1">
                                                <div class="recent-item-title" bis_skin_checked="1">
                                                    الطلب #{{ $order->order_number }}
                                                </div>
                                                <div class="recent-item-date" bis_skin_checked="1">
                                                    {{ $order->created_at->diffForHumans() }}
                                                </div>
                                            </div>
                                            <div class="recent-item-content" bis_skin_checked="1">
                                                <div class="mb-1" bis_skin_checked="1">
                                                    <span class="badge bg-info">{{ $order->status_label }}</span>
                                                    <span class="ms-2">{{ number_format($order->total_amount, 2) }}
                                                        ج.م</span>
                                                </div>
                                                <small class="text-muted">
                                                    {{ $order->items->count() }} منتج
                                                </small>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="empty-recent" bis_skin_checked="1">
                                            <i class="fas fa-shopping-cart"></i>
                                            <p>لا توجد طلبات حديثة</p>
                                        </div>
                                    @endforelse
                                </div>

                                <!-- التقييمات الحديثة -->
                                <div class="info-section" bis_skin_checked="1">
                                    <div class="d-flex justify-content-between align-items-center mb-3"
                                        bis_skin_checked="1">
                                        <h6 class="mb-0"><i class="fas fa-star me-2"></i>التقييمات الحديثة</h6>
                                        <a href="{{ route('admin.users.reviews', $user) }}"
                                            class="btn btn-sm btn-outline-info">
                                            عرض الكل
                                        </a>
                                    </div>

                                    @forelse($user->reviews as $review)
                                        <div class="recent-item" bis_skin_checked="1">
                                            <div class="recent-item-header" bis_skin_checked="1">
                                                <div class="recent-item-title" bis_skin_checked="1">
                                                    <div class="d-flex align-items-center gap-2" bis_skin_checked="1">
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            <i
                                                                class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                                        @endfor
                                                    </div>
                                                </div>
                                                <div class="recent-item-date" bis_skin_checked="1">
                                                    {{ $review->created_at->diffForHumans() }}
                                                </div>
                                            </div>
                                            <div class="recent-item-content" bis_skin_checked="1">
                                                <p class="mb-1">{{ Str::limit($review->comment, 100) }}</p>
                                                <small class="text-muted">
                                                    على منتج: {{ $review->product->name ?? 'غير معروف' }}
                                                </small>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="empty-recent" bis_skin_checked="1">
                                            <i class="fas fa-star"></i>
                                            <p>لا توجد تقييمات حديثة</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            <div class="col-lg-4" bis_skin_checked="1">
                                <!-- الإحصائيات -->
                                <div class="stats-card" bis_skin_checked="1">
                                    <div class="stats-icon" bis_skin_checked="1">
                                        <i class="fas fa-shopping-cart"></i>
                                    </div>
                                    <div class="stats-number" bis_skin_checked="1">
                                        {{ $user->orders()->count() }}
                                    </div>
                                    <div class="stats-label" bis_skin_checked="1">إجمالي الطلبات</div>
                                    <a href="{{ route('admin.users.orders', $user) }}"
                                        class="btn btn-sm btn-outline-primary mt-2">
                                        عرض التفاصيل
                                    </a>
                                </div>

                                <div class="stats-card" bis_skin_checked="1">
                                    <div class="stats-icon" bis_skin_checked="1">
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <div class="stats-number" bis_skin_checked="1">
                                        {{ $user->reviews()->count() }}
                                    </div>
                                    <div class="stats-label" bis_skin_checked="1">إجمالي التقييمات</div>
                                    <a href="{{ route('admin.users.reviews', $user) }}"
                                        class="btn btn-sm btn-outline-info mt-2">
                                        عرض التفاصيل
                                    </a>
                                </div>

                                <div class="stats-card" bis_skin_checked="1">
                                    <div class="stats-icon" bis_skin_checked="1">
                                        <i class="fas fa-heart"></i>
                                    </div>
                                    <div class="stats-number" bis_skin_checked="1">
                                        {{ $user->favouriteProducts()->count() }}
                                    </div>
                                    <div class="stats-label" bis_skin_checked="1">المنتجات المفضلة</div>
                                    <a href="{{ route('admin.users.favourites', $user) }}"
                                        class="btn btn-sm btn-outline-warning mt-2">
                                        عرض التفاصيل
                                    </a>
                                </div>

                                <div class="stats-card" bis_skin_checked="1">
                                    <div class="stats-icon" bis_skin_checked="1">
                                        <i class="fas fa-bell"></i>
                                    </div>
                                    <div class="stats-number" bis_skin_checked="1">
                                        {{ $user->notifications()->count() }}
                                    </div>
                                    <div class="stats-label" bis_skin_checked="1">الإشعارات</div>
                                </div>

                                <!-- الجدول الزمني -->
                                <div class="info-section" bis_skin_checked="1">
                                    <h6><i class="fas fa-history me-2"></i>آخر الأنشطة</h6>

                                    <div class="timeline" bis_skin_checked="1">
                                        @if ($user->updated_at->gt($user->created_at))
                                            <div class="timeline-item" bis_skin_checked="1">
                                                <div class="timeline-content" bis_skin_checked="1">
                                                    <div class="timeline-date" bis_skin_checked="1">
                                                        {{ $user->updated_at->diffForHumans() }}
                                                    </div>
                                                    <div class="timeline-text" bis_skin_checked="1">
                                                        آخر تحديث للمعلومات
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="timeline-item" bis_skin_checked="1">
                                            <div class="timeline-content" bis_skin_checked="1">
                                                <div class="timeline-date" bis_skin_checked="1">
                                                    {{ $user->created_at->diffForHumans() }}
                                                </div>
                                                <div class="timeline-text" bis_skin_checked="1">
                                                    انضمام المستخدم للنظام
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- إجراءات سريعة -->
                                <div class="info-section" bis_skin_checked="1">
                                    <h6><i class="fas fa-bolt me-2"></i>إجراءات سريعة</h6>

                                    <div class="d-grid gap-2" bis_skin_checked="1">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning">
                                            <i class="fas fa-edit me-2"></i>تعديل البيانات
                                        </a>

                                        <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST"
                                            style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-secondary w-100">
                                                <i class="fas fa-power-off me-2"></i>
                                                {{ isset($user->is_active) && $user->is_active ? 'تعطيل' : 'تفعيل' }}
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                            id="deleteForm" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-danger w-100"
                                                onclick="confirmDelete()">
                                                <i class="fas fa-trash me-2"></i>حذف المستخدم
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete() {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: 'سيتم حذف المستخدم "{{ $user->name }}" نهائياً',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('deleteForm').submit();
                }
            });
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
