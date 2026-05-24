@extends('Admin.layout.master')

@section('title', 'إدارة رسائل التواصل')

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

        .contact-card {
            background: var(--dark-card);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .contact-header {
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

        .icon-unread {
            background: rgba(133, 100, 4, 0.2);
            color: #ffc107;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }

        .icon-read {
            background: linear-gradient(135deg, rgba(21, 87, 36, 0.2) 0%, rgba(32, 201, 151, 0.2) 100%);
            color: #20c997;
            border: 1px solid rgba(32, 201, 151, 0.3);
        }

        .icon-today {
            background: rgba(12, 99, 228, 0.2);
            color: #0c63e4;
            border: 1px solid rgba(12, 99, 228, 0.3);
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

        .message-item {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            border-right: 4px solid transparent;
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
        }

        .message-item:hover {
            transform: translateX(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            background: rgba(105, 108, 255, 0.1);
            border-color: var(--primary-color);
        }

        .message-item.unread {
            border-right: 4px solid #ffc107;
            background: rgba(255, 193, 7, 0.05);
        }

        .message-item.read {
            border-right: 4px solid #20c997;
            opacity: 0.85;
        }

        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .message-sender {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sender-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: white;
        }

        .sender-info h6 {
            margin: 0;
            color: #fff;
            font-weight: 600;
        }

        .sender-info small {
            color: rgba(255, 255, 255, 0.6);
        }

        .message-date {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.5);
        }

        .message-subject {
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 10px;
            font-size: 15px;
        }

        .message-body {
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 15px;
            line-height: 1.6;
        }

        .message-meta {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 15px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.5);
        }

        .meta-item i {
            font-size: 14px;
        }

        .message-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .status-filter {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .status-filter-btn {
            padding: 8px 20px;
            border-radius: 25px;
            background: rgba(255, 255, 255, 0.05);
            color: rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.1);
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .status-filter-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .status-filter-btn.active {
            background: var(--primary-gradient);
            color: white;
            border-color: var(--primary-color);
        }

        .sort-dropdown {
            position: relative;
            display: inline-block;
        }

        .sort-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.8);
            padding: 8px 15px;
            border-radius: 25px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
        }

        .sort-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .sort-dropdown-content {
            display: none;
            position: absolute;
            background: var(--dark-card);
            min-width: 200px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            border-radius: 10px;
            z-index: 1;
            padding: 10px 0;
            margin-top: 5px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sort-dropdown:hover .sort-dropdown-content {
            display: block;
        }

        .sort-item {
            padding: 10px 20px;
            cursor: pointer;
            transition: background 0.3s;
            color: rgba(255, 255, 255, 0.8);
        }

        .sort-item:hover {
            background: rgba(105, 108, 255, 0.1);
            color: #fff;
        }

        .sort-item.active {
            background: var(--primary-gradient);
            color: white;
        }

        .badge-status {
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 11px;
        }

        .badge-unread {
            background: rgba(133, 100, 4, 0.2);
            color: #ffc107;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }

        .badge-read {
            background: rgba(21, 87, 36, 0.2);
            color: #20c997;
            border: 1px solid rgba(32, 201, 151, 0.3);
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

        .form-select, .form-control {
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            border-radius: 25px;
        }

        .form-select:focus, .form-control:focus {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: var(--primary-color);
            color: #fff;
            box-shadow: 0 0 0 0.25rem rgba(105, 108, 255, 0.25);
        }

        .form-select option {
            background: var(--dark-card);
            color: #fff;
        }

        .modal-content {
            background: var(--dark-card);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .modal-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .modal-footer {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        .input-group-text {
            border-radius: 0 25px 25px 0;
        }

        .input-group .form-control:first-child {
            border-radius: 25px 0 0 25px;
        }

        .input-group .form-control:last-child {
            border-radius: 0 25px 25px 0;
        }

        @media (max-width: 768px) {
            .message-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .message-actions {
                flex-wrap: wrap;
            }

            .filter-row {
                grid-template-columns: 1fr;
            }

            .message-meta {
                flex-direction: column;
                gap: 5px;
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
                <li class="breadcrumb-item active">رسائل التواصل</li>
            </ol>
        </nav>

        <!-- الإحصائيات -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stats-card">
                    <div class="stats-icon icon-total">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="stats-number">
                        {{ number_format($stats['total'] ?? 0) }}
                    </div>
                    <div class="stats-label">إجمالي الرسائل</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stats-card">
                    <div class="stats-icon icon-unread">
                        <i class="fas fa-envelope-open-text"></i>
                    </div>
                    <div class="stats-number">
                        {{ number_format($stats['unread'] ?? 0) }}
                    </div>
                    <div class="stats-label">رسائل غير مقروءة</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stats-card">
                    <div class="stats-icon icon-read">
                        <i class="fas fa-check-double"></i>
                    </div>
                    <div class="stats-number">
                        {{ number_format($stats['read'] ?? 0) }}
                    </div>
                    <div class="stats-label">رسائل مقروءة</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stats-card">
                    <div class="stats-icon icon-today">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="stats-number">
                        {{ number_format($stats['today'] ?? 0) }}
                    </div>
                    <div class="stats-label">رسائل اليوم</div>
                </div>
            </div>
        </div>

        <!-- فلترة حسب الحالة -->
        <div class="status-filter">
            <button class="status-filter-btn {{ !request('status') ? 'active' : '' }}" onclick="filterByStatus('all')">
                جميع الرسائل
            </button>
            <button class="status-filter-btn {{ request('status') == 'unread' ? 'active' : '' }}" onclick="filterByStatus('unread')">
                <i class="fas fa-envelope me-2"></i>غير مقروءة
            </button>
            <button class="status-filter-btn {{ request('status') == 'read' ? 'active' : '' }}" onclick="filterByStatus('read')">
                <i class="fas fa-check-double me-2"></i>مقروءة
            </button>
        </div>

        <!-- فلترة متقدمة -->
        <div class="filter-card">
            <h6 class="mb-3"><i class="fas fa-filter me-2"></i>فلترة متقدمة</h6>

            <div class="filter-row">
                <div class="search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="form-control" placeholder="بحث بالاسم، البريد، الموضوع..."
                        id="searchInput" value="{{ request('search') }}">
                </div>

                <select class="form-select" id="statusFilter">
                    <option value="">جميع الحالات</option>
                    <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>غير مقروءة</option>
                    <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>مقروءة</option>
                </select>

                <div class="sort-dropdown">
                    <button class="sort-btn">
                        <i class="fas fa-sort-amount-down"></i>
                        @if(request('sort') == 'oldest')
                            الأقدم أولاً
                        @elseif(request('sort') == 'name')
                            حسب الاسم
                        @else
                            الأحدث أولاً
                        @endif
                    </button>
                    <div class="sort-dropdown-content">
                        <div class="sort-item {{ request('sort') == 'latest' || !request('sort') ? 'active' : '' }}" onclick="applySort('latest')">
                            الأحدث أولاً
                        </div>
                        <div class="sort-item {{ request('sort') == 'oldest' ? 'active' : '' }}" onclick="applySort('oldest')">
                            الأقدم أولاً
                        </div>
                        <div class="sort-item {{ request('sort') == 'name' ? 'active' : '' }}" onclick="applySort('name')">
                            حسب الاسم
                        </div>
                    </div>
                </div>
            </div>

            <div class="filter-row">
                <div class="input-group">
                    <input type="date" class="form-control" id="dateFrom" placeholder="من تاريخ"
                        value="{{ request('date_from') }}">
                    <span class="input-group-text bg-transparent text-white border">إلى</span>
                    <input type="date" class="form-control" id="dateTo" placeholder="إلى تاريخ"
                        value="{{ request('date_to') }}">
                </div>

                <div class="d-flex gap-2">
                    <button class="btn btn-primary flex-fill" onclick="applyFilters()">
                        <i class="fas fa-filter me-2"></i>تطبيق الفلاتر
                    </button>
                    <button class="btn btn-secondary flex-fill" onclick="resetFilters()">
                        <i class="fas fa-redo me-2"></i>إعادة تعيين
                    </button>
                </div>
            </div>
        </div>

        <!-- قائمة الرسائل -->
        <div class="row">
            <div class="col-12">
                <div class="contact-card">
                    <div class="contact-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0">رسائل التواصل</h5>
                                <small class="opacity-75">إدارة رسائل المستخدمين والاستفسارات</small>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-light" onclick="markAllAsRead()" id="markAllBtn">
                                    <i class="fas fa-check-double me-2"></i>تحديد الكل كمقروء
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        @if ($contacts->isEmpty())
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="fas fa-inbox"></i>
                                </div>
                                <h5 class="empty-state-text">لا توجد رسائل</h5>
                                <p class="text-muted">لم يتم استلام أي رسائل حتى الآن</p>
                            </div>
                        @else
                            @foreach ($contacts as $contact)
                                <div class="message-item {{ $contact->is_read ? 'read' : 'unread' }}" id="message-{{ $contact->id }}">
                                    <div class="message-header">
                                        <div class="message-sender">
                                            <div class="sender-avatar d-flex align-items-center justify-content-center bg-secondary text-white">
                                                <i class="fas fa-user" style="font-size: 14px;"></i>
                                            </div>
                                            <div class="sender-info">
                                                <h6>
                                                    {{ $contact->name }}
                                                    @if(!$contact->is_read)
                                                        <span class="badge-status badge-unread ms-2">جديد</span>
                                                    @else
                                                        <span class="badge-status badge-read ms-2">مقروء</span>
                                                    @endif
                                                </h6>
                                                <small>
                                                    <i class="fas fa-envelope me-1"></i>{{ $contact->email }}
                                                    @if($contact->phone)
                                                        <span class="mx-2">|</span>
                                                        <i class="fas fa-phone me-1"></i>{{ $contact->phone }}
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                        <div class="message-date">
                                            <i class="far fa-clock me-1"></i>
                                            {{ $contact->created_at->translatedFormat('d M Y - h:i A') }}
                                            @if($contact->is_read && $contact->read_at)
                                                <br>
                                                <small class="text-success">
                                                    <i class="fas fa-check me-1"></i>
                                                    قُرأت {{ $contact->read_at->translatedFormat('d M Y - h:i A') }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="message-subject">
                                        <i class="fas fa-tag me-2"></i>
                                        {{ $contact->subject ?? 'بدون موضوع' }}
                                    </div>

                                    <div class="message-body">
                                        {{ Str::limit($contact->message, 200) }}
                                        @if(strlen($contact->message) > 200)
                                            <a href="#" class="text-primary" onclick="viewMessage({{ $contact->id }})">
                                                عرض المزيد...
                                            </a>
                                        @endif
                                    </div>

                                    <div class="message-meta">
                                        @if($contact->ip_address)
                                        <div class="meta-item">
                                            <i class="fas fa-globe"></i>
                                            IP: {{ $contact->ip_address }}
                                        </div>
                                        @endif
                                        @if($contact->user_agent)
                                        <div class="meta-item">
                                            <i class="fas fa-desktop"></i>
                                            {{ Str::limit($contact->user_agent, 50) }}
                                        </div>
                                        @endif
                                    </div>

                                    <div class="message-actions">
                                        <button class="btn btn-sm btn-info" onclick="viewMessage({{ $contact->id }})" style="background: var(--info-color); border: none; color: white;">
                                            <i class="fas fa-eye me-1"></i>عرض التفاصيل
                                        </button>
                                        @if(!$contact->is_read)
                                        <button class="btn btn-sm btn-outline-success" onclick="markAsRead({{ $contact->id }})">
                                            <i class="fas fa-check me-1"></i>تحديد كمقروء
                                        </button>
                                        @endif
                                        <button class="btn btn-sm btn-outline-danger delete-btn"
                                            data-id="{{ $contact->id }}" data-name="{{ $contact->name }}">
                                            <i class="fas fa-trash me-1"></i>حذف
                                        </button>
                                    </div>
                                </div>
                            @endforeach

                            @if ($contacts->hasPages())
                                <div class="m-3">
                                    <nav>
                                        <ul class="pagination">
                                            @if ($contacts->onFirstPage())
                                                <li class="page-item disabled">
                                                    <span class="page-link waves-effect" aria-hidden="true">‹</span>
                                                </li>
                                            @else
                                                <li class="page-item">
                                                    <a class="page-link waves-effect" href="{{ $contacts->previousPageUrl() }}" rel="prev">‹</a>
                                                </li>
                                            @endif

                                            @foreach ($contacts->links()->elements[0] as $page => $url)
                                                @if ($page == $contacts->currentPage())
                                                    <li class="page-item active">
                                                        <span class="page-link waves-effect">{{ $page }}</span>
                                                    </li>
                                                @else
                                                    <li class="page-item">
                                                        <a class="page-link waves-effect" href="{{ $url }}">{{ $page }}</a>
                                                    </li>
                                                @endif
                                            @endforeach

                                            @if ($contacts->hasMorePages())
                                                <li class="page-item">
                                                    <a class="page-link waves-effect" href="{{ $contacts->nextPageUrl() }}" rel="next">›</a>
                                                </li>
                                            @else
                                                <li class="page-item disabled">
                                                    <span class="page-link waves-effect" aria-hidden="true">›</span>
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

    <!-- Modal عرض تفاصيل الرسالة -->
    <div class="modal fade" id="messageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-envelope-open-text me-2"></i>تفاصيل الرسالة
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="messageModalBody">
                    <!-- Content loaded via AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    <button type="button" class="btn btn-primary" id="modalMarkAsRead" style="display: none;">
                        <i class="fas fa-check me-1"></i>تحديد كمقروء
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('#statusFilter').select2({
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

            // حذف الرسالة
            $('.delete-btn').on('click', function() {
                const contactId = $(this).data('id');
                const contactName = $(this).data('name');

                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: `سيتم حذف رسالة "${contactName}" نهائياً`,
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
                            url: "{{ route('admin.contact.destroy', '') }}/" + contactId,
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'تم الحذف',
                                    text: response.message || 'تم حذف الرسالة بنجاح',
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
        });

        // عرض تفاصيل الرسالة في Modal
        function viewMessage(contactId) {
            $.ajax({
                url: "{{ route('admin.contact.show', '') }}/" + contactId,
                type: 'GET',
                success: function(response) {
                    const isRead = response.is_read;
                    
                    $('#messageModalBody').html(`
                        <div class="mb-4">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="sender-avatar d-flex align-items-center justify-content-center bg-secondary text-white" style="width:50px;height:50px;">
                                    <i class="fas fa-user" style="font-size: 20px;"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">
                                        ${response.name || 'غير محدد'}
                                        ${!isRead ? '<span class="badge-status badge-unread ms-2">جديد</span>' : '<span class="badge-status badge-read ms-2">مقروء</span>'}
                                    </h6>
                                    <small class="text-muted">
                                        <i class="fas fa-envelope me-1"></i>${response.email || 'غير محدد'}
                                        ${response.phone ? `<span class="mx-2">|</span><i class="fas fa-phone me-1"></i>${response.phone}` : ''}
                                    </small>
                                </div>
                            </div>
                            <hr>
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-tag me-2"></i>${response.subject || 'بدون موضوع'}
                            </h6>
                            <div class="p-3 rounded" style="background:rgba(255,255,255,0.05);line-height:1.8;">
                                ${response.message || 'لا يوجد محتوى'}
                            </div>
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="far fa-clock me-1"></i>تاريخ الإرسال: ${response.created_at || ''}
                                </small>
                                ${response.read_at ? `<br><small class="text-success"><i class="fas fa-check me-1"></i>قُرأت: ${response.read_at}</small>` : ''}
                                ${response.ip_address ? `<br><small class="text-muted"><i class="fas fa-globe me-1"></i>IP: ${response.ip_address}</small>` : ''}
                                ${response.user_agent ? `<br><small class="text-muted"><i class="fas fa-desktop me-1"></i>المتصفح: ${response.user_agent}</small>` : ''}
                            </div>
                        </div>
                    `);

                    // زر تحديد كمقروء
                    if (!isRead) {
                        $('#modalMarkAsRead').show();
                        $('#modalMarkAsRead').attr('onclick', `markAsReadFromModal(${contactId})`);
                    } else {
                        $('#modalMarkAsRead').hide();
                    }

                    $('#messageModal').modal('show');
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: 'حدث خطأ أثناء تحميل تفاصيل الرسالة',
                    });
                }
            });
        }

        // تحديد الرسالة كمقروءة من القائمة
        function markAsRead(contactId) {
            $.ajax({
                url: "{{ route('admin.contact.read', '') }}/" + contactId,
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم',
                        text: response.message || 'تم تحديد الرسالة كمقروءة',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: 'حدث خطأ أثناء تحديث حالة الرسالة',
                    });
                }
            });
        }

        // تحديد الرسالة كمقروءة من الـ Modal
        function markAsReadFromModal(contactId) {
            $.ajax({
                url: "{{ route('admin.contact.read', '') }}/" + contactId,
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                },
                success: function(response) {
                    $('#messageModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'تم',
                        text: response.message || 'تم تحديد الرسالة كمقروءة',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: 'حدث خطأ أثناء تحديث حالة الرسالة',
                    });
                }
            });
        }

        // تحديد الكل كمقروء
        function markAllAsRead() {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: 'سيتم تحديد جميع الرسائل كمقروءة',
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'نعم، تحديد الكل كمقروء',
                cancelButtonText: 'إلغاء',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.contact.mark-all-read') }}",
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'تم',
                                text: response.message || 'تم تحديد جميع الرسائل كمقروءة',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ',
                                text: 'حدث خطأ أثناء تحديث حالة الرسائل',
                            });
                        }
                    });
                }
            });
        }

        function filterByStatus(status) {
            const url = new URL(window.location.href);
            if (status === 'all') {
                url.searchParams.delete('status');
            } else {
                url.searchParams.set('status', status);
            }
            url.searchParams.set('page', '1');
            window.location.href = url.toString();
        }

        function applySort(sort) {
            const url = new URL(window.location.href);
            url.searchParams.set('sort', sort);
            url.searchParams.set('page', '1');
            window.location.href = url.toString();
        }

        function applyFilters() {
            const params = {
                search: $('#searchInput').val(),
                status: $('#statusFilter').val(),
                date_from: $('#dateFrom').val(),
                date_to: $('#dateTo').val()
            };

            updateUrl(params);
        }

        function resetFilters() {
            window.location.href = "{{ route('admin.contact.index') }}";
        }

        function updateUrl(params) {
            const url = new URL(window.location.href);
            
            Object.keys(params).forEach(key => {
                if (params[key] === null || params[key] === '') {
                    url.searchParams.delete(key);
                } else {
                    url.searchParams.set(key, params[key]);
                }
            });

            url.searchParams.set('page', '1');
            window.location.href = url.toString();
        }
    </script>
@endsection