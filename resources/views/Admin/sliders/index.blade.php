@extends('Admin.layout.master')

@section('title', 'إدارة السلايدرات')

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

        .slider-card {
            background: var(--dark-card);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .slider-header {
            background: var(--primary-gradient);
            color: white;
            padding: 25px 30px;
            border-radius: 15px 15px 0 0;
            margin: -30px -30px 20px -30px;
        }

        .stats-card {
            background: var(--dark-card);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            border-top: 4px solid var(--primary-color);
            transition: transform 0.3s ease;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
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

        .icon-total {
            background: var(--primary-gradient);
            color: white;
        }

        .icon-driver {
            background: rgba(111, 66, 193, 0.2);
            color: #6f42c1;
            border: 1px solid rgba(111, 66, 193, 0.3);
        }

        .icon-user {
            background: rgba(12, 99, 228, 0.2);
            color: #0c63e4;
            border: 1px solid rgba(12, 99, 228, 0.3);
        }

        .icon-active {
            background: linear-gradient(135deg, rgba(21, 87, 36, 0.2) 0%, rgba(32, 201, 151, 0.2) 100%);
            color: #20c997;
            border: 1px solid rgba(32, 201, 151, 0.3);
        }

        .stats-number {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
            color: #fff;
        }

        .stats-label {
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
        }

        .filter-card {
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

        .search-box {
            position: relative;
        }

        .search-box input {
            padding-right: 40px;
            border-radius: 25px;
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .search-box input:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--primary-color);
            color: #fff;
            box-shadow: 0 0 0 0.25rem rgba(105, 108, 255, 0.25);
        }

        .search-box .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.5);
        }

        .slider-item {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            border-right: 4px solid transparent;
            border: 1px solid rgba(255, 255, 255, 0.1);
            cursor: move;
        }

        .slider-item:hover {
            transform: translateX(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            background: rgba(105, 108, 255, 0.1);
            border-color: var(--primary-color);
        }

        .slider-item.sortable-ghost {
            opacity: 0.4;
            background: rgba(105, 108, 255, 0.2);
        }

        .slider-item.sortable-chosen {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        .slider-header-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .slider-title {
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
        }

        .slider-content {
            display: grid;
            grid-template-columns: 150px 1fr;
            gap: 20px;
            align-items: start;
        }

        .slider-image {
            width: 150px;
            height: 100px;
            border-radius: 10px;
            overflow: hidden;
            border: 2px solid rgba(255, 255, 255, 0.1);
        }

        .slider-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .slider-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .detail-item i {
            width: 20px;
            text-align: center;
            color: rgba(255, 255, 255, 0.5);
        }

        .detail-label {
            font-weight: 600;
            color: rgba(255, 255, 255, 0.8);
            min-width: 70px;
        }

        .detail-value {
            color: rgba(255, 255, 255, 0.9);
        }

        .slider-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .badge-type {
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
        }

        .badge-driver {
            background: rgba(111, 66, 193, 0.2);
            color: #ab8ce4;
            border: 1px solid rgba(171, 140, 228, 0.3);
        }

        .badge-user {
            background: rgba(12, 99, 228, 0.2);
            color: #0dcaf0;
            border: 1px solid rgba(13, 202, 240, 0.3);
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
        }

        .status-active {
            background: linear-gradient(135deg, rgba(21, 87, 36, 0.2) 0%, rgba(32, 201, 151, 0.2) 100%);
            color: #20c997;
            border: 1px solid rgba(32, 201, 151, 0.3);
        }

        .status-inactive {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
            border: 1px solid rgba(220, 53, 69, 0.3);
        }

        .empty-state {
            text-align: center;
            padding: 50px 20px;
        }

        .empty-state-icon {
            font-size: 60px;
            color: rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }

        .empty-state-text {
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 20px;
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

        .btn-outline-info {
            color: #0dcaf0;
            border-color: #0dcaf0;
        }

        .btn-outline-info:hover {
            background: #0dcaf0;
            color: white;
        }

        .btn-outline-success {
            color: #20c997;
            border-color: #20c997;
        }

        .btn-outline-success:hover {
            background: #20c997;
            color: white;
        }

        .btn-outline-danger {
            color: #dc3545;
            border-color: #dc3545;
        }

        .btn-outline-danger:hover {
            background: #dc3545;
            color: white;
        }

        .form-select {
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            border-radius: 25px;
        }

        .form-select:focus {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: var(--primary-color);
            color: #fff;
            box-shadow: 0 0 0 0.25rem rgba(105, 108, 255, 0.25);
        }

        .form-select option {
            background: var(--dark-card);
            color: #fff;
        }

        .pagination {
            justify-content: center;
            gap: 5px;
        }

        .page-link {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.8);
            border-radius: 8px !important;
            margin: 0 2px;
        }

        .page-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .page-item.active .page-link {
            background: var(--primary-gradient);
            border-color: var(--primary-color);
        }

        .page-item.disabled .page-link {
            background: rgba(255, 255, 255, 0.02);
            color: rgba(255, 255, 255, 0.3);
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 26px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(255, 255, 255, 0.2);
            transition: 0.4s;
            border-radius: 34px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: 0.4s;
            border-radius: 50%;
        }

        input:checked + .toggle-slider {
            background: var(--primary-gradient);
        }

        input:checked + .toggle-slider:before {
            transform: translateX(24px);
        }

        .drag-handle {
            cursor: move;
            color: rgba(255, 255, 255, 0.5);
            font-size: 20px;
            padding: 5px;
            transition: color 0.3s;
        }

        .drag-handle:hover {
            color: #fff;
        }

        @media (max-width: 768px) {
            .slider-content {
                grid-template-columns: 1fr;
            }

            .slider-image {
                width: 100%;
                height: 200px;
            }

            .slider-header-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .slider-details {
                grid-template-columns: 1fr;
            }

            .slider-actions {
                justify-content: flex-start;
            }

            .filter-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                     <a href="{{ route('admin.home') }}">الرئيسية</a>
                </li>
                <li class="breadcrumb-item active">السلايدرات</li>
            </ol>
        </nav>

        <!-- الإحصائيات -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stats-card">
                    <div class="stats-icon icon-total">
                        <i class="fas fa-images"></i>
                    </div>
                    <div class="stats-number">
                        {{ number_format($sliders->total()) }}
                    </div>
                    <div class="stats-label">إجمالي السلايدرات</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stats-card">
                    <div class="stats-icon icon-active">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stats-number" id="activeCount">
                        {{ $sliders->where('is_active', 1)->count() }}
                    </div>
                    <div class="stats-label">سلايدرات نشطة</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stats-card">
                    <div class="stats-icon icon-driver">
                        <i class="fas fa-truck"></i>
                    </div>
                    <div class="stats-number">
                        {{ $sliders->where('type', 'driver')->count() }}
                    </div>
                    <div class="stats-label">سلايدرات السائقين</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stats-card">
                    <div class="stats-icon icon-user">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="stats-number">
                        {{ $sliders->where('type', 'user')->count() }}
                    </div>
                    <div class="stats-label">سلايدرات المستخدمين</div>
                </div>
            </div>
        </div>

        <!-- فلترة -->
        <div class="filter-card">
            <h6 class="mb-3"><i class="fas fa-filter me-2"></i>فلترة السلايدرات</h6>

            <div class="filter-row">
                <div class="search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="form-control" placeholder="بحث في السلايدرات..."
                        id="searchInput" value="{{ request('search') }}">
                </div>

                <select class="form-select" id="typeFilter">
                    <option value="">جميع الأنواع</option>
                    <option value="driver" {{ request('type') == 'driver' ? 'selected' : '' }}>سائق</option>
                    <option value="user" {{ request('type') == 'user' ? 'selected' : '' }}>مستخدم</option>
                </select>

                <select class="form-select" id="statusFilter">
                    <option value="">جميع الحالات</option>
                    <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>نشط</option>
                    <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>غير نشط</option>
                </select>

                <div class="d-flex gap-2">
                    <button class="btn btn-primary flex-fill" onclick="applyFilters()">
                        <i class="fas fa-filter me-2"></i>تطبيق
                    </button>
                    <button class="btn btn-outline-secondary flex-fill" onclick="resetFilters()">
                        <i class="fas fa-redo me-2"></i>إعادة تعيين
                    </button>
                </div>
            </div>
        </div>

        <!-- قائمة السلايدرات -->
        <div class="row">
            <div class="col-12">
                <div class="slider-card">
                    <div class="slider-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0">قائمة السلايدرات</h5>
                                <small class="opacity-75">إدارة جميع السلايدرات المتحركة</small>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.banners.create') }}" class="btn btn-light">
                                    <i class="fas fa-plus me-2"></i>إضافة سلايدر جديد
                                </a>
                                <button class="btn btn-light" id="saveOrderBtn" style="display: none;" onclick="saveOrder()">
                                    <i class="fas fa-save me-2"></i>حفظ الترتيب
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        @if ($sliders->isEmpty())
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="fas fa-images"></i>
                                </div>
                                <h5 class="empty-state-text">لا توجد سلايدرات</h5>
                                <p class="text-muted">لم يتم إنشاء أي سلايدرات حتى الآن</p>
                                <a href="{{ route('admin.banners.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>إنشاء سلايدر جديد
                                </a>
                            </div>
                        @else
                            <div id="sortableList">
                                @foreach ($sliders as $slider)
                                    <div class="slider-item" data-id="{{ $slider->id }}">
                                        <div class="slider-header-info">
                                            <div class="d-flex align-items-center gap-3">
                                                <span class="drag-handle" title="سحب للترتيب">
                                                    <i class="fas fa-grip-vertical"></i>
                                                </span>
                                                <div class="slider-title">
                                                    <span class="fw-bold">{{ $slider->title }}</span>
                                                    <small class="text-muted d-block">#{{ $slider->id }}</small>
                                                </div>
                                                <span class="badge-type badge-{{ $slider->type }}">
                                                    <i class="fas fa-{{ $slider->type == 'driver' ? 'truck' : 'user' }} me-1"></i>
                                                    {{ $slider->type == 'driver' ? 'سائق' : 'مستخدم' }}
                                                </span>
                                                <span class="status-badge status-{{ $slider->is_active ? 'active' : 'inactive' }}">
                                                    {{ $slider->is_active ? 'نشط' : 'غير نشط' }}
                                                </span>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="text-muted small">
                                                    الترتيب: {{ $slider->order }}
                                                </span>
                                                <label class="toggle-switch" title="تغيير الحالة">
                                                    <input type="checkbox" 
                                                           {{ $slider->is_active ? 'checked' : '' }}
                                                           onchange="toggleStatus({{ $slider->id }}, this)">
                                                    <span class="toggle-slider"></span>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="slider-content">
                                            <div class="slider-image">
                                                @if($slider->image)
                                                    <img src="{{ asset('storage/' . $slider->image) }}" alt="{{ $slider->title }}">
                                                @else
                                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-secondary bg-opacity-25">
                                                        <i class="fas fa-image fa-2x text-muted"></i>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="slider-details">
                                                <div class="detail-item">
                                                    <i class="fas fa-link"></i>
                                                    <span class="detail-label">الرابط:</span>
                                                    <span class="detail-value">
                                                        @if($slider->link)
                                                            <a href="{{ $slider->link }}" target="_blank" class="text-info">
                                                                {{ Str::limit($slider->link, 40) }}
                                                            </a>
                                                        @else
                                                            <span class="text-muted">لا يوجد</span>
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="slider-actions">
                                            <a href="{{ route('admin.banners.edit', $slider) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit me-1"></i>تعديل
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-btn"
                                                data-id="{{ $slider->id }}" data-name="{{ $slider->title }}">
                                                <i class="fas fa-trash me-1"></i>حذف
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if ($sliders->hasPages())
                                <div class="m-3">
                                    <nav>
                                        <ul class="pagination">
                                            {{-- Previous Page Link --}}
                                            @if ($sliders->onFirstPage())
                                                <li class="page-item disabled">
                                                    <span class="page-link">‹</span>
                                                </li>
                                            @else
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $sliders->previousPageUrl() }}">‹</a>
                                                </li>
                                            @endif

                                            {{-- Pagination Elements --}}
                                            @foreach ($sliders->links()->elements[0] as $page => $url)
                                                @if ($page == $sliders->currentPage())
                                                    <li class="page-item active">
                                                        <span class="page-link">{{ $page }}</span>
                                                    </li>
                                                @else
                                                    <li class="page-item">
                                                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                                    </li>
                                                @endif
                                            @endforeach

                                            {{-- Next Page Link --}}
                                            @if ($sliders->hasMorePages())
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $sliders->nextPageUrl() }}">›</a>
                                                </li>
                                            @else
                                                <li class="page-item disabled">
                                                    <span class="page-link">›</span>
                                                </li>
                                            @endif
                                        </ul>
                                    </nav>
                                </div>
                            @endif
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
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('#typeFilter, #statusFilter').select2({
                theme: 'default',
                width: '100%',
                dropdownParent: $('body'),
                minimumResultsForSearch: Infinity
            });

            // البحث مع تأخير
            let searchTimeout;
            $('#searchInput').on('keyup', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    applyFilters();
                }, 500);
            });

            // تهيئة السحب والإفلات
            const sortableList = document.getElementById('sortableList');
            if (sortableList) {
                new Sortable(sortableList, {
                    handle: '.drag-handle',
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    dragClass: 'sortable-drag',
                    onEnd: function(evt) {
                        $('#saveOrderBtn').fadeIn();
                    }
                });
            }

            // حذف السلايدر
            $('.delete-btn').on('click', function() {
                const sliderId = $(this).data('id');
                const sliderName = $(this).data('name');

                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: `سيتم حذف "${sliderName}" نهائياً`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'نعم، احذف',
                    cancelButtonText: 'إلغاء',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin.banners.destroy', '') }}/" + sliderId,
                            type: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                _method: 'DELETE'
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'تم الحذف',
                                    text: response.message || 'تم حذف السلايدر بنجاح',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'خطأ',
                                    text: xhr.responseJSON?.message || 'حدث خطأ أثناء الحذف',
                                });
                            }
                        });
                    }
                });
            });

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

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: "{{ session('error') }}",
                    timer: 2000,
                    showConfirmButton: false
                });
            @endif
        });

        // تغيير حالة السلايدر
        function toggleStatus(sliderId, element) {
            $.ajax({
                url: "{{ route('admin.banners.toggle-status', '') }}/" + sliderId,
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم التحديث',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    
                    // تحديث حالة العنصر في الواجهة
                    const statusBadge = $(element).closest('.slider-item').find('.status-badge');
                    if (response.is_active) {
                        statusBadge.removeClass('status-inactive').addClass('status-active').text('نشط');
                    } else {
                        statusBadge.removeClass('status-active').addClass('status-inactive').text('غير نشط');
                    }
                },
                error: function(xhr) {
                    // إعادة التبديل في حالة الفشل
                    $(element).prop('checked', !$(element).prop('checked'));
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: xhr.responseJSON?.message || 'حدث خطأ أثناء تغيير الحالة',
                    });
                }
            });
        }

        // حفظ الترتيب الجديد
        function saveOrder() {
            const orders = [];
            $('#sortableList .slider-item').each(function(index) {
                orders.push({
                    id: $(this).data('id'),
                    order: index + 1
                });
            });

            $.ajax({
                url: "{{ route('admin.banners.items.reorder') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    orders: orders
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم الحفظ',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: xhr.responseJSON?.message || 'حدث خطأ أثناء حفظ الترتيب',
                    });
                }
            });
        }

        // تطبيق الفلاتر
        function applyFilters() {
            const url = new URL(window.location.href);
            
            const search = $('#searchInput').val();
            const type = $('#typeFilter').val();
            const isActive = $('#statusFilter').val();

            if (search) {
                url.searchParams.set('search', search);
            } else {
                url.searchParams.delete('search');
            }

            if (type) {
                url.searchParams.set('type', type);
            } else {
                url.searchParams.delete('type');
            }

            if (isActive !== '') {
                url.searchParams.set('is_active', isActive);
            } else {
                url.searchParams.delete('is_active');
            }

            url.searchParams.set('page', '1');
            window.location.href = url.toString();
        }

        // إعادة تعيين الفلاتر
        function resetFilters() {
            window.location.href = "{{ route('admin.banners.index') }}";
        }
    </script>
@endsection