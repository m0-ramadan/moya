@extends('Admin.layout.master')

@section('title', 'تفاصيل البانر')

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

        .detail-card {
            background: var(--dark-card);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .detail-header {
            background: var(--primary-gradient);
            color: white;
            padding: 25px;
        }

        .detail-content {
            padding: 30px;
        }

        .info-section {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.05);
        }

        .info-section h5 {
            color: var(--primary-color);
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-item {
            display: flex;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px dashed rgba(255, 255, 255, 0.1);
        }

        .info-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
            min-width: 150px;
        }

        .info-value {
            color: rgba(255, 255, 255, 0.7);
            flex: 1;
        }

        .badge-custom {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .badge-slider {
            background: var(--primary-gradient);
            color: white;
        }

        .badge-grid {
            background: rgba(12, 99, 228, 0.2);
            color: #0c63e4;
            border: 1px solid rgba(12, 99, 228, 0.3);
        }

        .badge-static {
            background: rgba(21, 87, 36, 0.2);
            color: #155724;
            border: 1px solid rgba(21, 87, 36, 0.3);
        }

        .badge-category {
            background: rgba(133, 100, 4, 0.2);
            color: #856404;
            border: 1px solid rgba(133, 100, 4, 0.3);
        }

        .badge-active {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }

        .badge-inactive {
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
            color: white;
        }

        .badge-main {
            background: rgba(8, 66, 152, 0.2);
            color: #084298;
            border: 1px solid rgba(8, 66, 152, 0.3);
        }

        .badge-cat {
            background: rgba(15, 81, 50, 0.2);
            color: #0f5132;
            border: 1px solid rgba(15, 81, 50, 0.3);
        }

        .preview-container {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.2);
        }

        .item-card {
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            background: rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
        }

        .item-card:hover {
            border-color: var(--primary-color);
            background: rgba(105, 108, 255, 0.1);
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.2);
        }

        .item-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 10px;
            border: 2px solid rgba(255, 255, 255, 0.1);
        }

        .item-mobile-image {
            width: 100px;
            height: 70px;
            object-fit: cover;
            border-radius: 6px;
            margin-left: 10px;
            border: 2px solid rgba(255, 255, 255, 0.1);
        }

        .item-tag {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: 600;
            margin-right: 5px;
            background: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.8);
        }

        .promo-badge {
            display: inline-block;
            padding: 2px 8px;
            background: rgba(133, 100, 4, 0.2);
            color: #856404;
            border-radius: 12px;
            font-size: 10px;
            margin: 2px;
            border: 1px solid rgba(133, 100, 4, 0.3);
        }

        .empty-items {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-muted);
        }

        .empty-items i {
            font-size: 48px;
            margin-bottom: 15px;
            color: rgba(255, 255, 255, 0.1);
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-action {
            min-width: 120px;
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

        @media (max-width: 768px) {
            .info-item {
                flex-direction: column;
            }

            .info-label {
                margin-bottom: 5px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn-action {
                width: 100%;
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
                    <a href="{{ route('admin.banners.index') }}">البنرات</a>
                </li>
                <li class="breadcrumb-item active">تفاصيل البانر</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-12">
                <div class="detail-card">
                    <div class="detail-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-2">{{ $banner->title }}</h4>
                                <small class="opacity-75">ID: #{{ $banner->id }}</small>
                            </div>
                            <div class="action-buttons">
                                <a href="{{ route('admin.banners.edit', $banner) }}" class="btn btn-light">
                                    <i class="fas fa-edit me-2"></i>تعديل
                                </a>
                                <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-light">
                                    <i class="fas fa-arrow-right me-2"></i>رجوع
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="detail-content">
                        <!-- معلومات البانر -->
                        <div class="info-section">
                            <h5><i class="fas fa-info-circle me-2"></i>معلومات البانر</h5>

                            <div class="info-item">
                                <span class="info-label">العنوان:</span>
                                <span class="info-value">{{ $banner->title }}</span>
                            </div>

                            <div class="info-item">
                                <span class="info-label">النوع:</span>
                                <span class="info-value">
                                    @if ($banner->type)
                                        <span
                                            class="badge-custom 
                                        {{ $banner->type->name == 'slider' ? 'badge-slider' : '' }}
                                        {{ $banner->type->name == 'grid' ? 'badge-grid' : '' }}
                                        {{ $banner->type->name == 'static' ? 'badge-static' : '' }}
                                        {{ $banner->type->name == 'category_slider' ? 'badge-category' : '' }}">
                                            <i
                                                class="fas 
                                            {{ $banner->type->name == 'slider' ? 'fa-sliders-h' : '' }}
                                            {{ $banner->type->name == 'grid' ? 'fa-th-large' : '' }}
                                            {{ $banner->type->name == 'static' ? 'fa-image' : '' }}
                                            {{ $banner->type->name == 'category_slider' ? 'fa-tags' : '' }}
                                            me-1"></i>
                                            {{ $banner->type->name == 'slider' ? 'سلايدر' : '' }}
                                            {{ $banner->type->name == 'grid' ? 'شبكة' : '' }}
                                            {{ $banner->type->name == 'static' ? 'ثابت' : '' }}
                                            {{ $banner->type->name == 'category_slider' ? 'أقسام' : '' }}
                                        </span>
                                    @endif
                                </span>
                            </div>

                            <div class="info-item">
                                <span class="info-label">القسم:</span>
                                <span class="info-value">
                                    @if ($banner->category_id)
                                        <span class="badge-custom badge-cat">
                                            <i class="fas fa-tag me-1"></i>
                                            {{ $banner->category->name ?? 'قسم' }}
                                        </span>
                                    @else
                                        <span class="badge-custom badge-main">
                                            <i class="fas fa-home me-1"></i>
                                            الرئيسية
                                        </span>
                                    @endif
                                </span>
                            </div>

                            <div class="info-item">
                                <span class="info-label">الترتيب:</span>
                                <span class="info-value">{{ $banner->section_order }}</span>
                            </div>

                            <div class="info-item">
                                <span class="info-label">الحالة:</span>
                                <span class="info-value">
                                    <span
                                        class="badge-custom {{ $banner->is_active ? 'badge-active' : 'badge-inactive' }}">
                                        {{ $banner->is_active ? 'نشط' : 'غير نشط' }}
                                    </span>
                                </span>
                            </div>

                            <div class="info-item">
                                <span class="info-label">عدد العناصر:</span>
                                <span class="info-value">{{ $banner->items->count() }} عنصر</span>
                            </div>
                        </div>

                        <!-- الفترة الزمنية -->
                        @if ($banner->start_date || $banner->end_date)
                            <div class="info-section">
                                <h5><i class="fas fa-calendar-alt me-2"></i>الفترة الزمنية</h5>

                                @if ($banner->start_date)
                                    <div class="info-item">
                                        <span class="info-label">تاريخ البدء:</span>
                                        <span class="info-value">
                                            {{ $banner->start_date->translatedFormat('d M Y h:i A') }}
                                        </span>
                                    </div>
                                @endif

                                @if ($banner->end_date)
                                    <div class="info-item">
                                        <span class="info-label">تاريخ الانتهاء:</span>
                                        <span class="info-value">
                                            {{ $banner->end_date->translatedFormat('d M Y h:i A') }}
                                        </span>
                                    </div>
                                @endif

                                @if (!$banner->start_date && !$banner->end_date)
                                    <div class="info-item">
                                        <span class="info-label">الفترة:</span>
                                        <span class="info-value">دائم</span>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- إعدادات الشبكة -->
                        @if ($banner->type && $banner->type->name == 'grid' && $banner->gridLayout)
                            <div class="info-section">
                                <h5><i class="fas fa-th-large me-2"></i>إعدادات الشبكة</h5>

                                <div class="info-item">
                                    <span class="info-label">نوع الشبكة:</span>
                                    <span class="info-value">{{ $banner->gridLayout->grid_type }}</span>
                                </div>

                                <div class="info-item">
                                    <span class="info-label">الأعمدة (كمبيوتر):</span>
                                    <span class="info-value">{{ $banner->gridLayout->desktop_columns }}</span>
                                </div>

                                <div class="info-item">
                                    <span class="info-label">الأعمدة (تابلت):</span>
                                    <span class="info-value">{{ $banner->gridLayout->tablet_columns }}</span>
                                </div>

                                <div class="info-item">
                                    <span class="info-label">الأعمدة (موبايل):</span>
                                    <span class="info-value">{{ $banner->gridLayout->mobile_columns }}</span>
                                </div>

                                <div class="info-item">
                                    <span class="info-label">المسافة بين الصفوف:</span>
                                    <span class="info-value">{{ $banner->gridLayout->row_gap }} بكسل</span>
                                </div>

                                <div class="info-item">
                                    <span class="info-label">المسافة بين الأعمدة:</span>
                                    <span class="info-value">{{ $banner->gridLayout->column_gap }} بكسل</span>
                                </div>
                            </div>
                        @endif

                        <!-- إعدادات السلايدر -->
                        @if ($banner->type && $banner->type->name == 'slider' && $banner->sliderSetting)
                            <div class="info-section">
                                <h5><i class="fas fa-sliders-h me-2"></i>إعدادات السلايدر</h5>

                                <div class="info-item">
                                    <span class="info-label">التشغيل التلقائي:</span>
                                    <span class="info-value">
                                        <span
                                            class="badge-custom {{ $banner->sliderSetting->autoplay ? 'badge-active' : 'badge-inactive' }}">
                                            {{ $banner->sliderSetting->autoplay ? 'مفعل' : 'معطل' }}
                                        </span>
                                    </span>
                                </div>

                                @if ($banner->sliderSetting->autoplay)
                                    <div class="info-item">
                                        <span class="info-label">سرعة التشغيل:</span>
                                        <span class="info-value">{{ $banner->sliderSetting->autoplay_speed }} ملي
                                            ثانية</span>
                                    </div>
                                @endif

                                <div class="info-item">
                                    <span class="info-label">أزرار التنقل:</span>
                                    <span class="info-value">
                                        <span
                                            class="badge-custom {{ $banner->sliderSetting->arrows ? 'badge-active' : 'badge-inactive' }}">
                                            {{ $banner->sliderSetting->arrows ? 'مفعل' : 'معطل' }}
                                        </span>
                                    </span>
                                </div>

                                <div class="info-item">
                                    <span class="info-label">النقاط:</span>
                                    <span class="info-value">
                                        <span
                                            class="badge-custom {{ $banner->sliderSetting->dots ? 'badge-active' : 'badge-inactive' }}">
                                            {{ $banner->sliderSetting->dots ? 'مفعل' : 'معطل' }}
                                        </span>
                                    </span>
                                </div>

                                <div class="info-item">
                                    <span class="info-label">لانهائي:</span>
                                    <span class="info-value">
                                        <span
                                            class="badge-custom {{ $banner->sliderSetting->infinite ? 'badge-active' : 'badge-inactive' }}">
                                            {{ $banner->sliderSetting->infinite ? 'مفعل' : 'معطل' }}
                                        </span>
                                    </span>
                                </div>
                            </div>
                        @endif

                        <!-- عناصر البانر -->
                        <div class="info-section">
                            <h5><i class="fas fa-layer-group me-2"></i>عناصر البانر ({{ $banner->items->count() }})</h5>

                            @if ($banner->items->count() > 0)
                                <div class="preview-container">
                                    @foreach ($banner->items->sortBy('item_order') as $item)
                                        <div class="item-card">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    @if ($item->image_url)
                                                        <img src="{{ Storage::url($item->image_url) }}"
                                                            alt="{{ $item->image_alt }}" class="item-image">
                                                    @endif
                                                </div>
                                                <div class="col-md-9">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <h6>{{ $item->image_alt ?? 'بدون عنوان' }}</h6>
                                                        <div>
                                                            <span
                                                                class="badge-custom {{ $item->is_active ? 'badge-active' : 'badge-inactive' }}">
                                                                {{ $item->is_active ? 'نشط' : 'غير نشط' }}
                                                            </span>
                                                            <span class="badge-custom">
                                                                الترتيب: {{ $item->item_order }}
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <div class="mb-2">
                                                        @if ($item->tag_text)
                                                            <span class="item-tag"
                                                                style="background-color: {{ $item->tag_bg_color ?? '#696cff' }}; 
                                                                     color: {{ $item->tag_color ?? '#ffffff' }};">
                                                                {{ $item->tag_text }}
                                                            </span>
                                                        @endif

                                                        @if ($item->promoCodes->count() > 0)
                                                            @foreach ($item->promoCodes as $promo)
                                                                <span class="promo-badge"
                                                                    title="{{ $promo->description }}">
                                                                    {{ $promo->code }}
                                                                </span>
                                                            @endforeach
                                                        @endif
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p class="mb-1">
                                                                <i class="fas fa-link text-muted me-2"></i>
                                                                @if ($item->is_link_active && $item->link_url)
                                                                    <a href="{{ $item->link_url }}"
                                                                        target="{{ $item->link_target }}"
                                                                        class="text-decoration-none">
                                                                        {{ $item->link_url }}
                                                                    </a>
                                                                @else
                                                                    <span class="text-muted">لا يوجد رابط</span>
                                                                @endif
                                                            </p>

                                                            @if ($item->product)
                                                                <p class="mb-1">
                                                                    <i class="fas fa-shopping-bag text-muted me-2"></i>
                                                                    <a href="{{ route('admin.products.show', $item->product->id) }}"
                                                                        class="text-decoration-none">
                                                                        المنتج: {{ $item->product->title }}
                                                                    </a>
                                                                </p>
                                                            @endif

                                                            @if ($item->category)
                                                                <p class="mb-1">
                                                                    <i class="fas fa-tag text-muted me-2"></i>
                                                                    <a href="{{ route('admin.categories.show', $item->category) }}"
                                                                        class="text-decoration-none">
                                                                        القسم: {{ $item->category->name }}
                                                                    </a>
                                                                </p>
                                                            @endif
                                                        </div>

                                                        <div class="col-md-6">
                                                            @if ($item->mobile_image_url)
                                                                <div class="d-flex align-items-center">
                                                                    <small class="text-muted me-2">صورة الموبايل:</small>
                                                                    <img src="{{ Storage::url($item->mobile_image_url) }}"
                                                                        alt="{{ $item->image_alt }}"
                                                                        class="item-mobile-image">
                                                                </div>
                                                            @endif

                                                            <p class="mb-1">
                                                                <i class="fas fa-eye text-muted me-2"></i>
                                                                <small class="text-muted">
                                                                    المشاهدات: {{ $item->analytics->sum('views_count') }} |
                                                                    النقرات: {{ $item->analytics->sum('clicks_count') }}
                                                                </small>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="empty-items">
                                    <i class="fas fa-image"></i>
                                    <p>لا توجد عناصر مضافة لهذا البانر</p>
                                </div>
                            @endif
                        </div>

                        <!-- الإحصائيات -->
                        @if ($banner->items->count() > 0)
                            <div class="info-section">
                                <h5><i class="fas fa-chart-bar me-2"></i>الإحصائيات</h5>

                                @php
                                    $totalViews = 0;
                                    $totalClicks = 0;
                                    foreach ($banner->items as $item) {
                                        $totalViews += $item->analytics->sum('views_count');
                                        $totalClicks += $item->analytics->sum('clicks_count');
                                    }
                                    $clickRate = $totalViews > 0 ? ($totalClicks / $totalViews) * 100 : 0;
                                @endphp

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="info-item flex-column">
                                            <span class="info-label">إجمالي المشاهدات</span>
                                            <span class="info-value" style="font-size: 24px; color: #696cff;">
                                                {{ number_format($totalViews) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <div class="info-item flex-column">
                                            <span class="info-label">إجمالي النقرات</span>
                                            <span class="info-value" style="font-size: 24px; color: #28a745;">
                                                {{ number_format($totalClicks) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <div class="info-item flex-column">
                                            <span class="info-label">معدل النقر</span>
                                            <span class="info-value" style="font-size: 24px; color: #ff6b6b;">
                                                {{ number_format($clickRate, 2) }}%
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Handle item status toggle
            $('.item-status-toggle').on('change', function() {
                const itemId = $(this).data('id');
                const isChecked = $(this).is(':checked');

                $.ajax({
                    url: "{{ route('admin.banners.items.toggle-status', '') }}/" + itemId,
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        _method: 'PATCH'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'نجاح',
                                text: 'تم تغيير حالة العنصر',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        }
                    }
                });
            });
        });
    </script>
@endsection
