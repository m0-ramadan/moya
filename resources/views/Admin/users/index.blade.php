@extends('Admin.layout.master')

@section('title', 'إدارة المستخدمين')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: "Cairo", sans-serif !important;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #f8f9fa;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .user-avatar-placeholder {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 600;
            border: 3px solid #f8f9fa;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .badge-custom {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-social {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .badge-email {
            background: #e7f5ff;
            color: #0c63e4;
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

        .badge-active {
            background: #d4edda;
            color: #155724;
        }

        .badge-inactive {
            background: #f8d7da;
            color: #721c24;
        }

        .table-card {
            /* background: white; */
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .table-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 25px;
        }

        .search-box {
            position: relative;
        }

        .search-box input {
            padding-right: 40px;
            border-radius: 25px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .search-box input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .search-box .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: white;
        }

        .filter-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .filter-tab {
            padding: 8px 20px;
            border-radius: 25px;
            background: #f8f9fa;
            color: #6c757d;
            border: 1px solid #dee2e6;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
            font-weight: 500;
        }

        .filter-tab:hover {
            background: #e9ecef;
        }

        .filter-tab.active {
            background: #696cff;
            color: white;
            border-color: #696cff;
        }

        .stats-card {
            background: #242f3b;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border-top: 4px solid #696cff;
            transition: transform 0.3s ease;
            margin-bottom: 20px;
        }

        .stats-card:hover {
            transform: translateY(-5px);
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

        .icon-users {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .icon-orders {
            background: #e7f5ff;
            color: #0c63e4;
        }

        .icon-reviews {
            background: #f8f9fa;
            color: #495057;
        }

        .icon-social {
            background: #ff6b6b;
            color: white;
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

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn-action {
            width: 32px;
            height: 32px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-details h6 {
            margin-bottom: 5px;
            font-weight: 600;
        }

        .user-details p {
            margin: 0;
            font-size: 13px;
            color: #6c757d;
        }

        .social-icons {
            display: flex;
            gap: 5px;
            margin-top: 5px;
        }

        .social-icon {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            color: white;
        }

        .icon-google {
            background: #ea4335;
        }

        .icon-facebook {
            background: #1877f2;
        }

        .icon-apple {
            background: #000000;
        }

        .toggle-switch {
            position: relative;
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
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.toggle-slider {
            background-color: #696cff;
        }

        input:checked+.toggle-slider:before {
            transform: translateX(24px);
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

        .sort-dropdown {
            position: relative;
            display: inline-block;
        }

        .sort-btn {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 8px 15px;
            border-radius: 25px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sort-dropdown-content {
            display: none;
            position: absolute;
            background: white;
            min-width: 200px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            z-index: 1;
            padding: 10px 0;
            margin-top: 5px;
        }

        .sort-dropdown:hover .sort-dropdown-content {
            display: block;
        }

        .sort-item {
            padding: 10px 20px;
            cursor: pointer;
            transition: background 0.3s;
            color: #495057;
        }

        .sort-item:hover {
            background: #f8f9fa;
        }

        .sort-item.active {
            background: #696cff;
            color: white;
        }

        @media (max-width: 768px) {
            .filter-tabs {
                justify-content: center;
            }

            .user-info {
                flex-direction: column;
                text-align: center;
                gap: 10px;
            }

            .user-details {
                text-align: center;
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
                <li class="breadcrumb-item active">المستخدمين</li>
            </ol>
        </nav>

        <!-- الإحصائيات -->
        <div class="row mb-4" bis_skin_checked="1">
            <div class="col-lg-3 col-md-6 mb-4" bis_skin_checked="1">
                <div class="stats-card" bis_skin_checked="1">
                    <div class="stats-icon icon-users" bis_skin_checked="1">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stats-number" bis_skin_checked="1">
                        {{ $users->total() }}
                    </div>
                    <div class="stats-label" bis_skin_checked="1">إجمالي المستخدمين</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4" bis_skin_checked="1">
                @php
                    $socialUsers = $users
                        ->filter(function ($user) {
                            return $user->google_id || $user->facebook_id || $user->apple_id;
                        })
                        ->count();
                @endphp
                <div class="stats-card" bis_skin_checked="1">
                    <div class="stats-icon icon-social" bis_skin_checked="1">
                        <i class="fas fa-share-alt"></i>
                    </div>
                    <div class="stats-number" bis_skin_checked="1">
                        {{ $socialUsers }}
                    </div>
                    <div class="stats-label" bis_skin_checked="1">مستخدمي التواصل الاجتماعي</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4" bis_skin_checked="1">
                <div class="stats-card" bis_skin_checked="1">
                    <div class="stats-icon icon-orders" bis_skin_checked="1">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stats-number" bis_skin_checked="1">
                        {{ \App\Models\Order::count() }}
                    </div>
                    <div class="stats-label" bis_skin_checked="1">إجمالي الطلبات</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4" bis_skin_checked="1">
                <div class="stats-card" bis_skin_checked="1">
                    <div class="stats-icon icon-reviews" bis_skin_checked="1">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stats-number" bis_skin_checked="1">
                        {{ \App\Models\Review::count() }}
                    </div>
                    <div class="stats-label" bis_skin_checked="1">إجمالي التقييمات</div>
                </div>
            </div>
        </div>

        <div class="row" bis_skin_checked="1">
            <div class="col-12" bis_skin_checked="1">
                <div class="table-card" bis_skin_checked="1">
                    <div class="table-header" bis_skin_checked="1">
                        <div class="row align-items-center" bis_skin_checked="1">
                            <div class="col-md-6" bis_skin_checked="1">
                                <h5 class="mb-0">إدارة المستخدمين</h5>
                                <small class="opacity-75">عرض وإدارة جميع مستخدمي النظام</small>
                            </div>
                            <div class="col-md-6" bis_skin_checked="1">
                                <div class="d-flex justify-content-end align-items-center gap-3" bis_skin_checked="1">
                                    <div class="search-box" bis_skin_checked="1">
                                        <i class="fas fa-search search-icon"></i>
                                        <input type="text" class="form-control"
                                            placeholder="بحث بالاسم، البريد، الهاتف..." id="searchInput"
                                            value="{{ request('search') }}" style="width: 250px;">
                                    </div>
                                    <div class="sort-dropdown" bis_skin_checked="1">
                                        <button class="sort-btn">
                                            <i class="fas fa-sort-amount-down"></i>
                                            الترتيب
                                        </button>
                                        <div class="sort-dropdown-content" bis_skin_checked="1">
                                            <div class="sort-item {{ request('sort_by') == 'created_at' && request('sort_direction') == 'desc' ? 'active' : '' }}"
                                                onclick="sortBy('created_at', 'desc')">
                                                الأحدث أولاً
                                            </div>
                                            <div class="sort-item {{ request('sort_by') == 'created_at' && request('sort_direction') == 'asc' ? 'active' : '' }}"
                                                onclick="sortBy('created_at', 'asc')">
                                                الأقدم أولاً
                                            </div>
                                            <div class="sort-item {{ request('sort_by') == 'name' && request('sort_direction') == 'asc' ? 'active' : '' }}"
                                                onclick="sortBy('name', 'asc')">
                                                الاسم من أ إلى ي
                                            </div>
                                            <div class="sort-item {{ request('sort_by') == 'name' && request('sort_direction') == 'desc' ? 'active' : '' }}"
                                                onclick="sortBy('name', 'desc')">
                                                الاسم من ي إلى أ
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{ route('admin.users.create') }}" class="btn btn-light">
                                        <i class="fas fa-user-plus me-2"></i>إضافة مستخدم
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- فلاتر التبويب -->
                    <div class="px-4 pt-3" bis_skin_checked="1">
                        <div class="filter-tabs" bis_skin_checked="1">
                            <div class="filter-tab {{ !request('type') ? 'active' : '' }}" onclick="filterBy('all')">
                                جميع المستخدمين
                            </div>
                            <div class="filter-tab {{ request('type') == 'social' ? 'active' : '' }}"
                                onclick="filterBy('social')">
                                <i class="fas fa-share-alt me-2"></i>مستخدمي السوشيال ميديا
                            </div>
                            <div class="filter-tab {{ request('type') == 'email' ? 'active' : '' }}"
                                onclick="filterBy('email')">
                                <i class="fas fa-envelope me-2"></i>مستخدمي البريد الإلكتروني
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive" bis_skin_checked="1">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th width="60">#</th>
                                    <th>المستخدم</th>
                                    <th>معلومات الاتصال</th>
                                    <th>طريقة التسجيل</th>
                                    <th>الحالة</th>
                                    <th>تاريخ التسجيل</th>
                                    <th width="120">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="usersTable">
                                @forelse($users as $user)
                                    <tr data-id="{{ $user->id }}">
                                        <td>{{ $loop->iteration + $users->perPage() * ($users->currentPage() - 1) }}</td>
                                        <td>
                                            <div class="user-info" bis_skin_checked="1">
                                                @if ($user->image)
                                                    <img src="{{ asset('storage/' . $user->image) }}"
                                                        alt="{{ $user->name }}" class="user-avatar">
                                                @else
                                                    <div class="user-avatar-placeholder" bis_skin_checked="1">
                                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                                    </div>
                                                @endif
                                                <div class="user-details" bis_skin_checked="1">
                                                    <h6 class="mb-1">{{ $user->name }}</h6>
                                                    <p class="mb-0">ID: #{{ $user->id }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="mb-1" bis_skin_checked="1">
                                                <i class="fas fa-envelope me-2 text-muted"></i>
                                                <small>{{ $user->email }}</small>
                                            </div>
                                            @if ($user->phone)
                                                <div class="mb-1" bis_skin_checked="1">
                                                    <i class="fas fa-phone me-2 text-muted"></i>
                                                    <small>{{ $user->phone }}</small>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($user->google_id || $user->facebook_id || $user->apple_id)
                                                <span class="badge-custom badge-social">
                                                    <i class="fas fa-share-alt me-1"></i>
                                                    التواصل الاجتماعي
                                                </span>
                                                <div class="social-icons" bis_skin_checked="1">
                                                    @if ($user->google_id)
                                                        <span class="social-icon icon-google" title="تسجيل الدخول بجوجل">
                                                            <i class="fab fa-google"></i>
                                                        </span>
                                                    @endif
                                                    @if ($user->facebook_id)
                                                        <span class="social-icon icon-facebook"
                                                            title="تسجيل الدخول بفيسبوك">
                                                            <i class="fab fa-facebook-f"></i>
                                                        </span>
                                                    @endif
                                                    @if ($user->apple_id)
                                                        <span class="social-icon icon-apple" title="تسجيل الدخول بأبل">
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
                                        </td>
                                        <td>
                                            @if (isset($user->is_active))
                                                <div class="d-flex align-items-center gap-2" bis_skin_checked="1">
                                                    <label class="toggle-switch">
                                                        <input type="checkbox" class="status-toggle"
                                                            data-id="{{ $user->id }}"
                                                            {{ $user->is_active ? 'checked' : '' }}>
                                                        <span class="toggle-slider"></span>
                                                    </label>
                                                    <span
                                                        class="badge-custom {{ $user->is_active ? 'badge-active' : 'badge-inactive' }}">
                                                        {{ $user->is_active ? 'نشط' : 'غير نشط' }}
                                                    </span>
                                                </div>
                                            @else
                                                <span class="badge-custom badge-active">
                                                    نشط
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="mb-1" bis_skin_checked="1">
                                                <small class="text-muted">
                                                    {{ $user->created_at->translatedFormat('d M Y') }}
                                                </small>
                                            </div>
                                            <div class="mb-1" bis_skin_checked="1">
                                                <small class="text-muted">
                                                    {{ $user->created_at->translatedFormat('h:i A') }}
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="action-buttons" bis_skin_checked="1">
                                                <a href="{{ route('admin.users.show', $user) }}"
                                                    class="btn btn-action btn-info" title="عرض التفاصيل">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.users.edit', $user) }}"
                                                    class="btn btn-action btn-warning" title="تعديل">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-action btn-danger delete-btn"
                                                    title="حذف" data-id="{{ $user->id }}"
                                                    data-name="{{ $user->name }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">
                                            <div class="empty-state" bis_skin_checked="1">
                                                <div class="empty-state-icon" bis_skin_checked="1">
                                                    <i class="fas fa-users"></i>
                                                </div>
                                                <h5 class="empty-state-text">لا توجد مستخدمين</h5>
                                                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-user-plus me-2"></i>إضافة مستخدم جديد
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    @if ($users->hasPages())
                        <div class="m-3">
                            <nav>
                                <ul class="pagination">
                                    {{-- Previous Page Link --}}
                                    @if ($users->onFirstPage())
                                        <li class="page-item disabled" aria-disabled="true">
                                            <span class="page-link waves-effect" aria-hidden="true">‹</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link waves-effect" href="{{ $users->previousPageUrl() }}"
                                                rel="prev">‹</a>
                                        </li>
                                    @endif

                                    {{-- Pagination Elements --}}
                                    @foreach ($users->links()->elements[0] as $page => $url)
                                        @if ($page == $users->currentPage())
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
                                    @if ($users->hasMorePages())
                                        <li class="page-item">
                                            <a class="page-link waves-effect" href="{{ $users->nextPageUrl() }}"
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
        </div>
    </div>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // البحث مع تأخير
            let searchTimeout;
            $('#searchInput').on('keyup', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    updateUrl({
                        search: $(this).val()
                    });
                }, 500);
            });

            // تبديل الحالة
            $('.status-toggle').on('change', function() {
                const userId = $(this).data('id');
                const isChecked = $(this).is(':checked');

                $.ajax({
                    url: "{{ route('admin.users.toggle-status', '') }}/" + userId,
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        _method: 'PATCH'
                    },
                    success: function(response) {
                        if (response.success) {
                            const statusBadge = $(`tr[data-id="${userId}"] .badge-custom`);
                            if (response.is_active) {
                                statusBadge.removeClass('badge-inactive').addClass(
                                    'badge-active').text('نشط');
                            } else {
                                statusBadge.removeClass('badge-active').addClass(
                                    'badge-inactive').text('غير نشط');
                            }

                            Swal.fire({
                                icon: 'success',
                                title: 'نجاح',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: 'حدث خطأ أثناء تغيير الحالة',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                });
            });

            // حذف المستخدم
            $('.delete-btn').on('click', function() {
                const userId = $(this).data('id');
                const userName = $(this).data('name');

                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: `سيتم حذف المستخدم "${userName}" نهائياً`,
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
                            url: "{{ route('admin.users.destroy', '') }}/" + userId,
                            type: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                _method: 'DELETE'
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'تم الحذف',
                                    text: response.success,
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
                                    text: 'حدث خطأ أثناء الحذف',
                                    timer: 1500,
                                    showConfirmButton: false
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

        function filterBy(type) {
            if (type === 'all') {
                updateUrl({
                    type: null
                });
            } else {
                updateUrl({
                    type: type
                });
            }
        }

        function sortBy(sortBy, sortDirection) {
            updateUrl({
                sort_by: sortBy,
                sort_direction: sortDirection
            });
        }

        function updateUrl(params) {
            const url = new URL(window.location.href);
            const searchParams = new URLSearchParams(url.search);

            // تحديث جميع الباراميترات
            Object.keys(params).forEach(key => {
                if (params[key] === null || params[key] === '') {
                    searchParams.delete(key);
                } else {
                    searchParams.set(key, params[key]);
                }
            });

            // إعادة التوجيه إلى الصفحة الأولى مع الباراميترات الجديدة
            searchParams.set('page', '1');
            url.search = searchParams.toString();
            window.location.href = url.toString();
        }
    </script>
@endsection
