@extends('Admin.layout.master')

@section('title', 'عرض المنتج: ' . $product->name)

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css" />
    <style>
        body {
            font-family: "Cairo", sans-serif !important;
        }

        .product-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }

        .product-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: rgba(255, 255, 255, 0.1);
            transform: rotate(45deg);
        }

        .product-main-image {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            height: 400px;
            position: relative;
        }

        .product-main-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .product-main-image:hover img {
            transform: scale(1.05);
        }

        .product-gallery {
            margin-top: 15px;
        }

        .gallery-thumb {
            width: 80px;
            height: 80px;
            border-radius: 10px;
            overflow: hidden;
            cursor: pointer;
            border: 3px solid transparent;
            transition: all 0.3s ease;
        }

        .gallery-thumb.active {
            border-color: #696cff;
        }

        .gallery-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-info-card {
            /* background: white; */
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            height: 100%;
        }

        .product-price-section {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .original-price {
            font-size: 18px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: line-through;
        }

        .current-price {
            font-size: 32px;
            font-weight: bold;
            margin: 5px 0;
        }

        .discount-badge {
            background: #ff6b6b;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            display: inline-block;
            margin-top: 10px;
        }

        .stock-status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 500;
        }

        .stock-status.in-stock {
            /* background: #d1fae5; */
            color: #065f46;
        }

        .stock-status.low-stock {
            /* background: #fef3c7; */
            color: #92400e;
        }

        .stock-status.out-of-stock {
            /* background: #fee2e2; */
            color: #991b1b;
        }

        .stock-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .stock-indicator.in-stock {
            background-color: #10b981;
        }

        .stock-indicator.low-stock {
            background-color: #f59e0b;
        }

        .stock-indicator.out-of-stock {
            background-color: #ef4444;
        }

        .status-badge {
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
        }

        .status-active {
            /* background: #d1fae5; */
            color: #065f46;
        }

        .status-inactive {
            /* background: #fee2e2; */
            color: #991b1b;
        }

        .status-draft {
            /* background: #fef3c7; */
            color: #92400e;
        }

        .detail-card {
            /* background: white; */
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        .detail-card h5 {
            /* color: #f8f9fa; */
            border-bottom: 2px solid #f8f9fa;
            padding-bottom: 10px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }

        .detail-card h5 i {
            margin-left: 10px;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f8f9fa;
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: #7f8c8d;
            font-weight: 500;
        }

        .detail-value {
            color: #2c3e50;
            font-weight: 600;
            text-align: left;
        }

        .color-swatch {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin: 5px;
            padding: 8px 15px;
            background: #f8f9fa;
            border-radius: 20px;
        }

        .color-preview {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .material-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin: 5px;
            padding: 8px 15px;
            /* background: #e7f5ff; */
            border-radius: 20px;
            color: #1864ab;
        }

        .feature-badge {
            display: inline-block;
            margin: 5px;
            padding: 8px 15px;
            /* background: #fff3bf; */
            border-radius: 20px;
            color: #5c940d;
        }

        .option-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin: 5px;
            padding: 8px 15px;
            /* background: #e3fafc; */
            border-radius: 20px;
            color: #0b7285;
        }

        .option-price {
            background: #20c997;
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 12px;
        }

        .pricing-tier {
            /* background: #f8f9fa; */
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
        }

        .tier-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .tier-quantity {
            font-weight: bold;
            color: #2c3e50;
        }

        .tier-price {
            font-size: 18px;
            font-weight: bold;
            color: #2ecc71;
        }

        .tier-discount {
            color: #e74c3c;
            font-size: 14px;
        }

        .tier-sample {
            background: #3498db;
            color: white;
            padding: 3px 10px;
            border-radius: 5px;
            font-size: 12px;
        }

        .rating-stars {
            color: #f1c40f;
            font-size: 18px;
        }

        .review-card {
            /* background: #f8f9fa; */
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .reviewer-name {
            font-weight: bold;
            color: #2c3e50;
        }

        .review-date {
            color: #7f8c8d;
            font-size: 12px;
        }

        .review-rating {
            color: #f1c40f;
        }

        .timeline {
            position: relative;
            padding: 20px 0;
        }

        .timeline::before {
            content: '';
            position: absolute;
            right: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            /* background: #e0e0e0; */
        }

        .timeline-item {
            position: relative;
            padding-right: 40px;
            margin-bottom: 20px;
        }

        .timeline-item:last-child {
            margin-bottom: 0;
        }

        .timeline-dot {
            position: absolute;
            right: 8px;
            top: 5px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #3498db;
            border: 3px solid white;
            box-shadow: 0 0 0 3px #e0e0e0;
        }

        .timeline-content {
            /* background: white; */
            padding: 15px;
            border-radius: 10px;
            border: 1px solid #e9ecef;
        }

        .timeline-date {
            font-size: 12px;
            color: #7f8c8d;
            margin-bottom: 5px;
        }

        .timeline-title {
            font-weight: 600;
            margin-bottom: 5px;
            color: #2c3e50;
        }

        .action-buttons {
            position: fixed;
            bottom: 30px;
            left: 30px;
            z-index: 1000;
        }

        .action-buttons .btn {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
        }

        .qr-code {
            width: 150px;
            height: 150px;
            margin: 0 auto;
            /* background: #f8f9fa; */
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2c3e50;
        }

        .social-share {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 15px;
        }

        .social-share .btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .product-description {
            line-height: 1.8;
            color: #495057;
        }

        .product-description img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            margin: 10px 0;
        }

        .product-description table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .product-description table th,
        .product-description table td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: right;
        }

        .product-description table th {
            /* background-color: #f8f9fa; */
        }

        .empty-state {
            text-align: center;
            padding: 30px;
            color: #7f8c8d;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        .meta-tag {
            display: inline-block;
            padding: 5px 10px;
            /* background: #e9ecef; */
            border-radius: 5px;
            margin: 3px;
            font-size: 12px;
            color: #495057;
        }

        .badge-new {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 12px;
            position: absolute;
            top: 15px;
            right: 15px;
            z-index: 2;
        }

        .image-overlay-buttons {
            position: absolute;
            bottom: 15px;
            right: 15px;
            display: flex;
            gap: 10px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .product-main-image:hover .image-overlay-buttons {
            opacity: 1;
        }

        .image-overlay-buttons .btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y" bis_skin_checked="1">
        <!-- Product Header -->
        <div class="product-header" bis_skin_checked="1">
            <div class="row align-items-center" bis_skin_checked="1">
                <div class="col-auto" bis_skin_checked="1">
                    @if ($product->created_at->gt(now()->subDays(7)))
                        <span class="badge-new">جديد</span>
                    @endif
                </div>
                <div class="col" bis_skin_checked="1">
                    <div class="d-flex justify-content-between align-items-start" bis_skin_checked="1">
                        <div bis_skin_checked="1">
                            <h1 class="mb-2">{{ $product->name }}</h1>
                            <p class="mb-1 opacity-75">
                                <i class="fas fa-hashtag"></i> ID: {{ $product->id }}
                                <span class="mx-2">•</span>
                                <i class="fas fa-folder"></i> {{ $product->category->name ?? 'غير مصنف' }}
                                <span class="mx-2">•</span>
                                <i class="fas fa-calendar-alt"></i> {{ $product->created_at->format('Y/m/d') }}
                            </p>
                        </div>
                        <div class="btn-group" bis_skin_checked="1">
                            <button class="btn btn-light" onclick="window.print()">
                                <i class="fas fa-print"></i>
                            </button>
                            <button class="btn btn-light" onclick="shareProduct()">
                                <i class="fas fa-share-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" bis_skin_checked="1">
            <!-- Left Column - Images & Basic Info -->
            <div class="col-lg-8" bis_skin_checked="1">
                <!-- Main Image & Gallery -->
                <div class="detail-card" bis_skin_checked="1">
                    <div class="row" bis_skin_checked="1">
                        <div class="col-md-8" bis_skin_checked="1">
                            <div class="product-main-image" bis_skin_checked="1">
                                <img src="{{ $product->primaryImage ? get_user_image($product->primaryImage->path) : 'https://via.placeholder.com/600x400?text=No+Image' }}"
                                    alt="{{ $product->name }}" id="mainProductImage">
                                <div class="image-overlay-buttons" bis_skin_checked="1">
                                    <button class="btn btn-primary" onclick="zoomImage()">
                                        <i class="fas fa-search-plus"></i>
                                    </button>
                                    <button class="btn btn-info" onclick="changeProductImage()">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Gallery Thumbs -->
                            @if ($product->images && $product->images->count() > 0)
                                <div class="product-gallery" bis_skin_checked="1">
                                    <div class="d-flex flex-wrap gap-3" bis_skin_checked="1">
                                        @foreach ($product->images as $image)
                                            <div class="gallery-thumb {{ $loop->first ? 'active' : '' }}"
                                                onclick="changeMainImage('{{ get_user_image($image->path) }}', this)">
                                                <img src="{{ get_user_image($image->path) }}" alt="صورة المنتج">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="col-md-4" bis_skin_checked="1">
                            <div class="product-info-card" bis_skin_checked="1">
                                <!-- Price Section -->
                                <div class="product-price-section" bis_skin_checked="1">
                                    @if ($product->has_discount && $product->discount)
                                        <div class="original-price" bis_skin_checked="1">
                                            {{ number_format($product->price, 2) }} ج.م
                                        </div>
                                        <div class="current-price" bis_skin_checked="1">
                                            {{ number_format($product->final_price, 2) }} ج.م
                                        </div>
                                        <div class="discount-badge" bis_skin_checked="1">
                                            @if ($product->discount->discount_type === 'percentage')
                                                خصم {{ $product->discount->discount_value }}%
                                            @else
                                                خصم {{ number_format($product->discount->discount_value, 2) }} ج.م
                                            @endif
                                        </div>
                                    @else
                                        <div class="current-price" bis_skin_checked="1">
                                            {{ number_format($product->price, 2) }} ج.م
                                        </div>
                                    @endif
                                </div>

                                <!-- Stock Status -->
                                <div class="mb-4" bis_skin_checked="1">
                                    <span
                                        class="stock-status {{ $product->stock == 0 ? 'out-of-stock' : ($product->stock < 10 ? 'low-stock' : 'in-stock') }}">
                                        <span
                                            class="stock-indicator {{ $product->stock == 0 ? 'out-of-stock' : ($product->stock < 10 ? 'low-stock' : 'in-stock') }}"></span>
                                        @if ($product->stock == 0)
                                            نفذ من المخزون
                                        @elseif($product->stock < 10)
                                            مخزون منخفض ({{ $product->stock }} قطعة)
                                        @else
                                            متوفر في المخزون ({{ $product->stock }} قطعة)
                                        @endif
                                    </span>
                                </div>

                                <!-- Product Status -->
                                <div class="mb-4" bis_skin_checked="1">
                                    <span
                                        class="status-badge {{ $product->status_id == 1 ? 'status-active' : ($product->status_id == 2 ? 'status-inactive' : 'status-draft') }}">
                                        @if ($product->status_id == 1)
                                            <i class="fas fa-check-circle me-1"></i> نشط
                                        @elseif($product->status_id == 2)
                                            <i class="fas fa-times-circle me-1"></i> غير نشط
                                        @else
                                            <i class="fas fa-file-alt me-1"></i> مسودة
                                        @endif
                                    </span>
                                </div>

                                <!-- Quick Stats -->
                                <div class="row text-center mb-4" bis_skin_checked="1">
                                    <div class="col-4" bis_skin_checked="1">
                                        <div class="h4 mb-1" bis_skin_checked="1">{{ $product->average_rating }}</div>
                                        <small class="text-muted">التقييم</small>
                                    </div>
                                    <div class="col-4" bis_skin_checked="1">
                                        <div class="h4 mb-1" bis_skin_checked="1">{{ $product->reviews_count ?? 0 }}
                                        </div>
                                        <small class="text-muted">التقييمات</small>
                                    </div>
                                    <div class="col-4" bis_skin_checked="1">
                                        <div class="h4 mb-1" bis_skin_checked="1">
                                            {{ $product->favouritedBy->count() ?? 0 }}</div>
                                        <small class="text-muted">المفضلة</small>
                                    </div>
                                </div>

                                <!-- Quick Actions -->
                                <div class="d-grid gap-2" bis_skin_checked="1">
                                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary">
                                        <i class="fas fa-edit me-2"></i> تعديل المنتج
                                    </a>
                                    <button type="button" class="btn btn-success" onclick="duplicateProduct()">
                                        <i class="fas fa-copy me-2"></i> نسخ المنتج
                                    </button>
                                    <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                                        <i class="fas fa-trash me-2"></i> حذف المنتج
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                @if ($product->description)
                    <div class="detail-card" bis_skin_checked="1">
                        <h5><i class="fas fa-align-right"></i> وصف المنتج</h5>
                        <div class="product-description" bis_skin_checked="1">
                            {!! $product->description !!}
                        </div>
                    </div>
                @endif

                <!-- Specifications -->
                <div class="detail-card" bis_skin_checked="1">
                    <h5><i class="fas fa-cogs"></i> المواصفات والخصائص</h5>

                    <!-- Colors -->
                    @if ($product->colors && $product->colors->count() > 0)
                        <div class="mb-4" bis_skin_checked="1">
                            <h6 class="mb-3">الألوان المتاحة:</h6>
                            <div class="d-flex flex-wrap" bis_skin_checked="1">
                                @foreach ($product->colors as $color)
                                    <div class="color-swatch">
                                        <div class="color-preview" style="background-color: {{ $color->hex_code }};">
                                        </div>
                                        <span>{{ $color->name }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Materials -->
                    @if ($product->materials && $product->materials->count() > 0)
                        <div class="mb-4" bis_skin_checked="1">
                            <h6 class="mb-3">المواد المستخدمة:</h6>
                            <div class="d-flex flex-wrap" bis_skin_checked="1">
                                @foreach ($product->materials as $material)
                                    <div class="material-badge">
                                        <i class="fas fa-cube"></i>
                                        <span>{{ $material->name }}</span>
                                        <small class="text-muted">
                                            ({{ $material->pivot->quantity }} {{ $material->pivot->unit }})
                                        </small>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Features -->
                    @if ($product->features && $product->features->count() > 0)
                        <div class="mb-4" bis_skin_checked="1">
                            <h6 class="mb-3">المواصفات الإضافية:</h6>
                            <div class="d-flex flex-wrap" bis_skin_checked="1">
                                @foreach ($product->features as $feature)
                                    <div class="feature-badge">
                                        <strong>{{ $feature->name }}:</strong> {{ $feature->value }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Product Options -->
                    @if ($product->options && $product->options->count() > 0)
                        <div class="mb-4" bis_skin_checked="1">
                            <h6 class="mb-3">خيارات المنتج:</h6>
                            <div class="d-flex flex-wrap" bis_skin_checked="1">
                                @foreach ($product->options as $option)
                                    <div class="option-chip">
                                        <strong>{{ $option->option_name }}:</strong> {{ $option->option_value }}
                                        @if ($option->additional_price > 0)
                                            <span class="option-price">+{{ $option->additional_price }} ج.م</span>
                                        @endif
                                        @if ($option->is_required)
                                            <span class="badge bg-danger">مطلوب</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Sizes -->
                    @if ($product->sizes && $product->sizes->count() > 0)
                        <div class="mb-4" bis_skin_checked="1">
                            <h6 class="mb-3">المقاسات المتاحة:</h6>
                            <div class="d-flex flex-wrap gap-2" bis_skin_checked="1">
                                @foreach ($product->sizes as $size)
                                    <span class="badge bg-secondary">{{ $size->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Delivery Time -->
                    @if ($product->deliveryTime)
                        <div class="mb-4" bis_skin_checked="1">
                            <h6 class="mb-3">وقت التوصيل:</h6>
                            <div class="alert alert-info" bis_skin_checked="1">
                                <i class="fas fa-shipping-fast me-2"></i>
                                من {{ $product->deliveryTime->from_days }} إلى {{ $product->deliveryTime->to_days }} يوم
                                عمل
                            </div>
                        </div>
                    @endif

                    <!-- Warranty -->
                    @if ($product->warranty)
                        <div class="mb-4" bis_skin_checked="1">
                            <h6 class="mb-3">الضمان:</h6>
                            <div class="alert alert-success" bis_skin_checked="1">
                                <i class="fas fa-shield-alt me-2"></i>
                                ضمان {{ $product->warranty->months }} شهر
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Pricing Tiers -->
                @if ($product->pricingTiers && $product->pricingTiers->count() > 0)
                    <div class="detail-card" bis_skin_checked="1">
                        <h5><i class="fas fa-chart-line"></i> التسعير حسب الكمية</h5>
                        <div class="row" bis_skin_checked="1">
                            @foreach ($product->pricingTiers->sortBy('quantity') as $tier)
                                <div class="col-md-6 mb-3" bis_skin_checked="1">
                                    <div class="pricing-tier" bis_skin_checked="1">
                                        <div class="tier-header" bis_skin_checked="1">
                                            <div class="tier-quantity" bis_skin_checked="1">
                                                <i class="fas fa-box me-2"></i> {{ $tier->quantity }} قطعة فما فوق
                                            </div>
                                            @if ($tier->is_sample)
                                                <span class="tier-sample">عينة</span>
                                            @endif
                                        </div>
                                        <div class="tier-price" bis_skin_checked="1">
                                            {{ number_format($tier->price_per_unit, 2) }} ج.م للقطعة
                                        </div>
                                        @if ($tier->discount_percentage > 0)
                                            <div class="tier-discount" bis_skin_checked="1">
                                                <i class="fas fa-percentage"></i> خصم {{ $tier->discount_percentage }}%
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Size Tiers -->
                @if ($product->sizeTiers && $product->sizeTiers->count() > 0)
                    <div class="detail-card" bis_skin_checked="1">
                        <h5><i class="fas fa-ruler-combined"></i> أسعار حسب المقاس والكمية</h5>
                        <div class="table-responsive" bis_skin_checked="1">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>المقاس</th>
                                        <th>الكمية</th>
                                        <th>السعر للوحدة</th>
                                        <th>السعر الإجمالي</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($product->sizeTiers as $tier)
                                        <tr>
                                            <td>{{ $tier->size->name ?? 'غير محدد' }}</td>
                                            <td>{{ $tier->quantity }}</td>
                                            <td>{{ number_format($tier->price_per_unit, 2) }} ج.م</td>
                                            <td>{{ number_format($tier->price_per_unit * $tier->quantity, 2) }} ج.م</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column - Details & Actions -->
            <div class="col-lg-4" bis_skin_checked="1">
                <!-- Product Details -->
                <div class="detail-card" bis_skin_checked="1">
                    <h5><i class="fas fa-info-circle"></i> تفاصيل المنتج</h5>
                    <div class="detail-item" bis_skin_checked="1">
                        <span class="detail-label">رقم المنتج</span>
                        <span class="detail-value">#{{ $product->id }}</span>
                    </div>
                    <div class="detail-item" bis_skin_checked="1">
                        <span class="detail-label">التصنيف</span>
                        <span class="detail-value">
                            <a href="{{ route('admin.categories.show', $product->category_id) }}" class="text-primary">
                                {{ $product->category->name ?? 'غير مصنف' }}
                            </a>
                        </span>
                    </div>
                    <div class="detail-item" bis_skin_checked="1">
                        <span class="detail-label">تاريخ الإنشاء</span>
                        <span class="detail-value">{{ $product->created_at->format('Y/m/d h:i A') }}</span>
                    </div>
                    <div class="detail-item" bis_skin_checked="1">
                        <span class="detail-label">آخر تحديث</span>
                        <span class="detail-value">{{ $product->updated_at->format('Y/m/d h:i A') }}</span>
                    </div>
                    <div class="detail-item" bis_skin_checked="1">
                        <span class="detail-label">يشمل الضريبة</span>
                        <span class="detail-value">
                            @if ($product->includes_tax)
                                <span class="badge bg-success">نعم</span>
                            @else
                                <span class="badge bg-secondary">لا</span>
                            @endif
                        </span>
                    </div>
                    <div class="detail-item" bis_skin_checked="1">
                        <span class="detail-label">يشمل الشحن</span>
                        <span class="detail-value">
                            @if ($product->includes_shipping)
                                <span class="badge bg-success">نعم</span>
                            @else
                                <span class="badge bg-secondary">لا</span>
                            @endif
                        </span>
                    </div>
                </div>

                <!-- Printing Methods -->
                @if ($product->printingMethods && $product->printingMethods->count() > 0)
                    <div class="detail-card" bis_skin_checked="1">
                        <h5><i class="fas fa-print"></i> طرق الطباعة</h5>
                        <div class="d-flex flex-wrap gap-2" bis_skin_checked="1">
                            @foreach ($product->printingMethods as $method)
                                <span class="badge bg-info">
                                    {{ $method->name }} - {{ $method->base_price }} ج.م
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Print Locations -->
                @if ($product->printLocations && $product->printLocations->count() > 0)
                    <div class="detail-card" bis_skin_checked="1">
                        <h5><i class="fas fa-map-marker-alt"></i> أماكن الطباعة</h5>
                        <div class="d-flex flex-wrap gap-2" bis_skin_checked="1">
                            @foreach ($product->printLocations as $location)
                                <span class="badge bg-warning text-dark">
                                    {{ $location->name }} ({{ $location->type }}) - {{ $location->additional_price }} ج.م
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Offers -->
                @if ($product->offers && $product->offers->count() > 0)
                    <div class="detail-card" bis_skin_checked="1">
                        <h5><i class="fas fa-tag"></i> العروض</h5>
                        <div class="d-flex flex-wrap gap-2" bis_skin_checked="1">
                            @foreach ($product->offers as $offer)
                                <span class="badge bg-danger">{{ $offer->name }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- SEO Information -->
                <div class="detail-card" bis_skin_checked="1">
                    <h5><i class="fas fa-search"></i> معلومات SEO</h5>
                    <div class="mb-3" bis_skin_checked="1">
                        <label class="form-label">الرابط (Slug)</label>
                        <div class="input-group" bis_skin_checked="1">
                            <input type="text" class="form-control" value="{{ $product->slug }}" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="copySlug()">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    @if ($product->meta_title)
                        <div class="mb-3" bis_skin_checked="1">
                            <label class="form-label">عنوان الصفحة</label>
                            <div class="bg-light p-2 rounded" bis_skin_checked="1">
                                {{ $product->meta_title }}
                            </div>
                        </div>
                    @endif

                    @if ($product->meta_description)
                        <div class="mb-3" bis_skin_checked="1">
                            <label class="form-label">وصف الصفحة</label>
                            <div class="bg-light p-2 rounded" bis_skin_checked="1">
                                {{ $product->meta_description }}
                            </div>
                        </div>
                    @endif

                    @if ($product->meta_keywords)
                        <div class="mb-3" bis_skin_checked="1">
                            <label class="form-label">الكلمات المفتاحية</label>
                            <div class="d-flex flex-wrap gap-1" bis_skin_checked="1">
                                @foreach (explode(',', $product->meta_keywords) as $keyword)
                                    <span class="meta-tag">{{ trim($keyword) }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Reviews -->
                @if ($product->reviews && $product->reviews->count() > 0)
                    <div class="detail-card" bis_skin_checked="1">
                        <h5><i class="fas fa-star"></i> آخر التقييمات</h5>
                        <div class="rating-stars mb-3" bis_skin_checked="1">
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= floor($product->average_rating))
                                    <i class="fas fa-star"></i>
                                @elseif($i - 0.5 <= $product->average_rating)
                                    <i class="fas fa-star-half-alt"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                            <span class="ms-2">{{ number_format($product->average_rating, 1) }}
                                ({{ $product->reviews_count ?? 0 }} تقييم)</span>
                        </div>

                        @foreach ($product->reviews->take(3) as $review)
                            <div class="review-card" bis_skin_checked="1">
                                <div class="review-header" bis_skin_checked="1">
                                    <div class="reviewer-name" bis_skin_checked="1">{{ $review->user->name ?? 'مستخدم' }}
                                    </div>
                                    <div class="review-rating" bis_skin_checked="1">
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= $review->rating)
                                                <i class="fas fa-star"></i>
                                            @else
                                                <i class="far fa-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                                <div class="review-date mb-2" bis_skin_checked="1">
                                    {{ $review->created_at->diffForHumans() }}
                                </div>
                                <p class="mb-0">{{ $review->comment }}</p>
                            </div>
                        @endforeach

                        @if ($product->reviews->count() > 3)
                            <div class="text-center mt-3" bis_skin_checked="1">
                                <a href="#" class="btn btn-outline-primary btn-sm">
                                    عرض جميع التقييمات ({{ $product->reviews->count() }})
                                </a>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Activity Timeline -->
                <div class="detail-card" bis_skin_checked="1">
                    <h5><i class="fas fa-history"></i> سجل النشاط</h5>
                    <div class="timeline" bis_skin_checked="1">
                        <div class="timeline-item" bis_skin_checked="1">
                            <div class="timeline-dot" bis_skin_checked="1"></div>
                            <div class="timeline-content" bis_skin_checked="1">
                                <div class="timeline-date" bis_skin_checked="1">آخر تحديث</div>
                                <div class="timeline-title" bis_skin_checked="1">تم تحديث المنتج</div>
                                <p class="mb-0">{{ $product->updated_at->diffForHumans() }}</p>
                            </div>
                        </div>

                        <div class="timeline-item" bis_skin_checked="1">
                            <div class="timeline-dot" bis_skin_checked="1"></div>
                            <div class="timeline-content" bis_skin_checked="1">
                                <div class="timeline-date" bis_skin_checked="1">تاريخ الإنشاء</div>
                                <div class="timeline-title" bis_skin_checked="1">تم إنشاء المنتج</div>
                                <p class="mb-0">{{ $product->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="detail-card" bis_skin_checked="1">
                    <h5><i class="fas fa-bolt"></i> روابط سريعة</h5>
                    <div class="d-grid gap-2" bis_skin_checked="1">
                        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i> تعديل المنتج
                        </a>
                        <button type="button" class="btn btn-success" onclick="duplicateProduct()">
                            <i class="fas fa-copy me-2"></i> نسخ المنتج
                        </button>
                        <a href="{{ route('admin.products.create') }}?duplicate={{ $product->id }}"
                            class="btn btn-outline-primary">
                            <i class="fas fa-plus-circle me-2"></i> إضافة منتج مشابه
                        </a>
                        <button type="button" class="btn btn-outline-info" onclick="previewProduct()">
                            <i class="fas fa-eye me-2"></i> معاينة في المتجر
                        </button>
                    </div>
                </div>

                <!-- QR Code -->
                <div class="detail-card" bis_skin_checked="1">
                    <h5><i class="fas fa-qrcode"></i> رمز QR</h5>
                    <div class="text-center" bis_skin_checked="1">
                        <div class="qr-code mb-3" id="qrCode" bis_skin_checked="1">
                            <!-- QR Code will be generated here -->
                            <div class="text-muted" bis_skin_checked="1">
                                <i class="fas fa-qrcode fa-3x"></i>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="generateQRCode()">
                            <i class="fas fa-sync-alt me-1"></i> إنشاء رمز QR
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons (Floating) -->
    <div class="action-buttons" bis_skin_checked="1">
        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary" title="تعديل">
            <i class="fas fa-edit"></i>
        </a>
        <button type="button" class="btn btn-info" onclick="copyProductLink()" title="نسخ الرابط">
            <i class="fas fa-link"></i>
        </button>
        <button type="button" class="btn btn-success" onclick="shareProduct()" title="مشاركة">
            <i class="fas fa-share-alt"></i>
        </button>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">تأكيد الحذف</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3" bis_skin_checked="1">
                        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                        <h5>هل أنت متأكد من حذف هذا المنتج؟</h5>
                        <p class="text-muted">سيتم حذف المنتج "<strong>{{ $product->name }}</strong>" بشكل دائم.</p>

                        <div class="alert alert-danger mt-3" bis_skin_checked="1">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <strong>تحذير:</strong> هذا الإجراء لا يمكن التراجع عنه
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" id="deleteForm">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i> حذف المنتج
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Share Modal -->
    <div class="modal fade" id="shareModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">مشاركة المنتج</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3" bis_skin_checked="1">
                        <label class="form-label">رابط المنتج</label>
                        <div class="input-group" bis_skin_checked="1">
                            <input type="text" class="form-control" id="shareUrl" readonly
                                value="{{ url('/products/' . $product->slug) }}">
                            <button class="btn btn-outline-secondary" type="button" onclick="copyShareUrl()">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3" bis_skin_checked="1">
                        <label class="form-label">مشاركة عبر</label>
                        <div class="social-share" bis_skin_checked="1">
                            <button class="btn btn-primary" onclick="shareOnFacebook()">
                                <i class="fab fa-facebook-f"></i>
                            </button>
                            <button class="btn btn-info" onclick="shareOnTwitter()">
                                <i class="fab fa-twitter"></i>
                            </button>
                            <button class="btn btn-success" onclick="shareOnWhatsApp()">
                                <i class="fab fa-whatsapp"></i>
                            </button>
                            <button class="btn btn-danger" onclick="shareViaEmail()">
                                <i class="fas fa-envelope"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3" bis_skin_checked="1">
                        <label class="form-label">نسخ HTML</label>
                        <div class="input-group" bis_skin_checked="1">
                            <input type="text" class="form-control" id="htmlCode" readonly
                                value='<a href="{{ url('/products/' . $product->slug) }}">{{ $product->name }}</a>'>
                            <button class="btn btn-outline-secondary" type="button" onclick="copyHtmlCode()">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Zoom Modal -->
    <div class="modal fade" id="imageZoomModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <img src="" alt="صورة مكبرة" id="zoomedImage" class="img-fluid w-100">
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Image Gallery
        function changeMainImage(src, element) {
            $('#mainProductImage').attr('src', src);
            $('.gallery-thumb').removeClass('active');
            $(element).addClass('active');
        }

        function zoomImage() {
            const src = $('#mainProductImage').attr('src');
            $('#zoomedImage').attr('src', src);
            $('#imageZoomModal').modal('show');
        }

        function changeProductImage() {
            // Simulate changing product image (in real app, this would open a file dialog)
            Swal.fire({
                title: 'تغيير صورة المنتج',
                text: 'هذه الميزة تحت التطوير',
                icon: 'info',
                confirmButtonText: 'حسناً'
            });
        }

        // Delete confirmation
        function confirmDelete() {
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }

        // Share product
        function shareProduct() {
            const modal = new bootstrap.Modal(document.getElementById('shareModal'));
            modal.show();
        }

        // Copy share URL
        function copyShareUrl() {
            const urlInput = document.getElementById('shareUrl');
            urlInput.select();
            urlInput.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(urlInput.value).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'تم النسخ!',
                    text: 'تم نسخ الرابط إلى الحافظة',
                    timer: 1500,
                    showConfirmButton: false
                });
            });
        }

        // Copy HTML code
        function copyHtmlCode() {
            const htmlInput = document.getElementById('htmlCode');
            htmlInput.select();
            htmlInput.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(htmlInput.value).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'تم النسخ!',
                    text: 'تم نسخ كود HTML إلى الحافظة',
                    timer: 1500,
                    showConfirmButton: false
                });
            });
        }

        // Copy slug
        function copySlug() {
            const slug = '{{ $product->slug }}';
            navigator.clipboard.writeText(slug).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'تم النسخ!',
                    text: 'تم نسخ الرابط إلى الحافظة',
                    timer: 1500,
                    showConfirmButton: false
                });
            });
        }

        // Copy product link
        function copyProductLink() {
            const url = '{{ url('/products/' . $product->slug) }}';
            navigator.clipboard.writeText(url).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'تم النسخ!',
                    text: 'تم نسخ رابط المنتج إلى الحافظة',
                    timer: 1500,
                    showConfirmButton: false
                });
            });
        }

        // Share on social media
        function shareOnFacebook() {
            const url = encodeURIComponent('{{ url('/products/' . $product->slug) }}');
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank');
        }

        function shareOnTwitter() {
            const url = encodeURIComponent('{{ url('/products/' . $product->slug) }}');
            const text = encodeURIComponent('{{ $product->name }}');
            window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank');
        }

        function shareOnWhatsApp() {
            const url = encodeURIComponent('{{ url('/products/' . $product->slug) }}');
            const text = encodeURIComponent('{{ $product->name }}');
            window.open(`https://wa.me/?text=${text}%20${url}`, '_blank');
        }

        function shareViaEmail() {
            const url = '{{ url('/products/' . $product->slug) }}';
            const subject = encodeURIComponent('{{ $product->name }}');
            const body = encodeURIComponent(`تفضل بزيارة المنتج: ${url}`);
            window.location.href = `mailto:?subject=${subject}&body=${body}`;
        }

        // Generate QR Code
        function generateQRCode() {
            const qrCodeDiv = document.getElementById('qrCode');
            qrCodeDiv.innerHTML = '';

            const url = '{{ url('/products/' . $product->slug) }}';

            // Generate QR code
            QRCode.toCanvas(url, {
                width: 150,
                margin: 1,
                color: {
                    dark: '#000000',
                    light: '#ffffff'
                }
            }, function(error, canvas) {
                if (error) {
                    console.error(error);
                    qrCodeDiv.innerHTML = '<div class="text-danger">خطأ في إنشاء رمز QR</div>';
                    return;
                }

                qrCodeDiv.appendChild(canvas);

                // Add download button
                const downloadBtn = document.createElement('button');
                downloadBtn.className = 'btn btn-sm btn-outline-primary mt-2';
                downloadBtn.innerHTML = '<i class="fas fa-download me-1"></i> تحميل';
                downloadBtn.onclick = function() {
                    const link = document.createElement('a');
                    link.download = 'qr-code-{{ $product->slug }}.png';
                    link.href = canvas.toDataURL('image/png');
                    link.click();
                };
                qrCodeDiv.appendChild(downloadBtn);
            });
        }

        // Duplicate product
        function duplicateProduct() {
            Swal.fire({
                title: 'نسخ المنتج',
                input: 'text',
                inputLabel: 'أدخل اسم للمنتج المنسوخ:',
                inputValue: '{{ $product->name }} - نسخة',
                showCancelButton: true,
                confirmButtonText: 'نسخ',
                cancelButtonText: 'إلغاء',
                showLoaderOnConfirm: true,
                preConfirm: (name) => {
                    if (!name) {
                        Swal.showValidationMessage('يجب إدخال اسم للمنتج');
                        return false;
                    }

                    return fetch('{{ route('admin.products.duplicate', $product) }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                name: name
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (!data.success) {
                                throw new Error(data.message || 'حدث خطأ أثناء النسخ');
                            }
                            return data;
                        });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'تم النسخ!',
                        text: 'تم نسخ المنتج بنجاح',
                        icon: 'success',
                        confirmButtonText: 'حسناً'
                    }).then(() => {
                        window.location.href = '/admin/products/' + result.value.data.id + '/edit';
                    });
                }
            });
        }

        // Preview product in store
        function previewProduct() {
            const url = 'https://talaaljazeera.com/product/{{ $product->id }}';
            window.open(url, '_blank');
        }



        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Generate QR code automatically
            generateQRCode();

            // Initialize fancybox for image gallery
            $('[data-fancybox="gallery"]').fancybox({
                buttons: [
                    "zoom",
                    "share",
                    "slideShow",
                    "fullScreen",
                    "download",
                    "thumbs",
                    "close"
                ]
            });

            // Add print functionality to print button
            document.querySelector('[onclick="window.print()"]').addEventListener('click', function(e) {
                e.preventDefault();
                printProduct();
            });
        });
    </script>
@endsection
