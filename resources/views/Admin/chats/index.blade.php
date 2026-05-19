@extends('Admin.layout.master')

@section('title', 'إدارة المحادثات')

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
            --unread-bg: rgba(105, 108, 255, 0.1);
        }

        body {
            font-family: "Cairo", sans-serif !important;
            background: var(--dark-bg);
            color: #fff;
        }

        .chats-card {
            background: var(--dark-card);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .chats-header {
            background: var(--primary-gradient);
            color: white;
            padding: 25px 30px;
            border-radius: 15px 15px 0 0;
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

        .icon-messages {
            background: rgba(12, 99, 228, 0.2);
            color: #0c63e4;
            border: 1px solid rgba(12, 99, 228, 0.3);
        }

        .icon-unread {
            background: rgba(133, 100, 4, 0.2);
            color: #ffc107;
            border: 1px solid rgba(255, 193, 7, 0.3);
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

        .chat-item {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            border-right: 4px solid transparent;
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
        }

        .chat-item.unread {
            background: var(--unread-bg);
            border-color: var(--primary-color);
        }

        .chat-item:hover {
            transform: translateX(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .chat-item.active::before {
            content: '';
            position: absolute;
            left: -10px;
            top: 50%;
            transform: translateY(-50%);
            width: 8px;
            height: 8px;
            background: #4CAF50;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(76, 175, 80, 0.7);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(76, 175, 80, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(76, 175, 80, 0);
            }
        }

        .chat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .chat-title {
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
        }

        .chat-date {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.7);
        }

        .chat-participants {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }

        .participant-badge {
            background: rgba(255, 255, 255, 0.1);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .participant-badge.user {
            border-left: 3px solid #4CAF50;
        }

        .participant-badge.driver {
            border-left: 3px solid #2196F3;
        }

        .participant-avatar {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .online-dot {
            width: 8px;
            height: 8px;
            background: #4CAF50;
            border-radius: 50%;
            display: inline-block;
            margin-left: 5px;
        }

        .chat-preview {
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
            margin-bottom: 10px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .chat-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
        }

        .chat-type {
            font-size: 12px;
            padding: 3px 10px;
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.1);
        }

        .unread-badge {
            background: var(--primary-color);
            color: white;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: bold;
        }

        .chat-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
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

        .filter-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            border: 1px solid rgba(255, 255, 255, 0.1);
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

        .type-filter {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .type-filter-btn {
            padding: 8px 20px;
            border-radius: 25px;
            background: rgba(255, 255, 255, 0.05);
            color: rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.1);
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .type-filter-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .type-filter-btn.active {
            background: var(--primary-gradient);
            color: white;
            border-color: var(--primary-color);
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

        @media (max-width: 768px) {
            .chat-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .chat-meta {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
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
                <li class="breadcrumb-item active">المحادثات</li>
            </ol>
        </nav>

        <!-- الإحصائيات -->
        <div class="row mb-4" bis_skin_checked="1">
            <div class="col-lg-3 col-md-6 mb-4" bis_skin_checked="1">
                <div class="stats-card" bis_skin_checked="1">
                    <div class="stats-icon icon-total" bis_skin_checked="1">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div class="stats-number" bis_skin_checked="1">
                        {{ number_format($stats['total_chats']) }}
                    </div>
                    <div class="stats-label" bis_skin_checked="1">إجمالي المحادثات</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4" bis_skin_checked="1">
                <div class="stats-card" bis_skin_checked="1">
                    <div class="stats-icon icon-messages" bis_skin_checked="1">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="stats-number" bis_skin_checked="1">
                        {{ number_format($stats['total_messages']) }}
                    </div>
                    <div class="stats-label" bis_skin_checked="1">إجمالي الرسائل</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4" bis_skin_checked="1">
                <div class="stats-card" bis_skin_checked="1">
                    <div class="stats-icon icon-unread" bis_skin_checked="1">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stats-number" bis_skin_checked="1">
                        {{ number_format($stats['unread_messages']) }}
                    </div>
                    <div class="stats-label" bis_skin_checked="1">رسائل غير مقروءة</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4" bis_skin_checked="1">
                <div class="stats-card" bis_skin_checked="1">
                    <div class="stats-icon icon-active" bis_skin_checked="1">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <div class="stats-number" bis_skin_checked="1">
                        {{ number_format($stats['today_chats']) }}
                    </div>
                    <div class="stats-label" bis_skin_checked="1">محادثات اليوم</div>
                </div>
            </div>
        </div>

        <!-- فلترة حسب النوع -->
        <div class="type-filter" bis_skin_checked="1">
            <button class="type-filter-btn {{ !request('type') ? 'active' : '' }}" onclick="filterByType('all')">
                جميع المحادثات
            </button>
            <button class="type-filter-btn {{ request('type') == 'user_user' ? 'active' : '' }}"
                onclick="filterByType('user_user')">
                <i class="fas fa-user-friends me-2"></i>مستخدم - مستخدم
            </button>
            <button class="type-filter-btn {{ request('type') == 'user_driver' ? 'active' : '' }}"
                onclick="filterByType('user_driver')">
                <i class="fas fa-user-tie me-2"></i>مستخدم - سائق
            </button>
            <button class="type-filter-btn {{ request('type') == 'driver_driver' ? 'active' : '' }}"
                onclick="filterByType('driver_driver')">
                <i class="fas fa-truck me-2"></i>سائق - سائق
            </button>
            <button class="type-filter-btn {{ request('unread_only') ? 'active' : '' }}" onclick="toggleUnreadOnly()">
                <i class="fas fa-envelope me-2"></i>غير مقروء فقط
                @if (request('unread_only'))
                    <span class="badge bg-danger ms-1">{{ $stats['unread_messages'] }}</span>
                @endif
            </button>
        </div>

        <!-- فلترة متقدمة -->
        <div class="filter-card" bis_skin_checked="1">
            <h6 class="mb-3"><i class="fas fa-filter me-2"></i>فلترة متقدمة</h6>

            <div class="row g-3" bis_skin_checked="1">
                <div class="col-md-4" bis_skin_checked="1">
                    <div class="search-box" bis_skin_checked="1">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" class="form-control" placeholder="بحث بالمحادثة أو الرسائل..."
                            id="searchInput" value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-md-4" bis_skin_checked="1">
                    <select class="form-control" id="participantSelect">
                        <option value="">جميع المشاركين</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}"
                                {{ request('participant') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} (مستخدم)
                            </option>
                        @endforeach
                        @foreach ($drivers as $driver)
                            <option value="{{ $driver->id }}"
                                {{ request('participant') == $driver->id ? 'selected' : '' }}>
                                {{ $driver->name }} (سائق)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4" bis_skin_checked="1">
                    <div class="input-group" bis_skin_checked="1">
                        <input type="date" class="form-control" id="dateFrom" placeholder="من تاريخ"
                            value="{{ request('date_from') }}">
                        <span class="input-group-text">إلى</span>
                        <input type="date" class="form-control" id="dateTo" placeholder="إلى تاريخ"
                            value="{{ request('date_to') }}">
                    </div>
                </div>

                <div class="col-12" bis_skin_checked="1">
                    <div class="d-flex gap-3" bis_skin_checked="1">
                        <button class="btn btn-primary" onclick="applyFilters()">
                            <i class="fas fa-filter me-2"></i>تطبيق الفلاتر
                        </button>
                        <a href="{{ route('admin.chats.live') }}" class="btn btn-success">
                            <i class="fas fa-bolt me-2"></i>المحادثات الحية
                        </a>
                        <a href="{{ route('admin.chats.statistics') }}" class="btn btn-info">
                            <i class="fas fa-chart-bar me-2"></i>الإحصائيات
                        </a>
                        <button class="btn btn-outline-secondary" onclick="resetFilters()">
                            <i class="fas fa-redo me-2"></i>إعادة تعيين
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- قائمة المحادثات -->
        <div class="row" bis_skin_checked="1">
            <div class="col-12" bis_skin_checked="1">
                <div class="chats-card" bis_skin_checked="1">
                    <div class="chats-header" bis_skin_checked="1">
                        <div class="d-flex justify-content-between align-items-center" bis_skin_checked="1">
                            <div bis_skin_checked="1">
                                <h5 class="mb-0">قائمة المحادثات</h5>
                                <small class="opacity-75">إدارة جميع محادثات النظام</small>
                            </div>
                            <div class="badge bg-warning" bis_skin_checked="1">
                                <i class="fas fa-circle me-1"></i>
                                {{ $stats['today_messages'] }} رسالة جديدة اليوم
                            </div>
                        </div>
                    </div>

                    <div class="card-body" bis_skin_checked="1">
                        @if ($chats->isEmpty())
                            <div class="empty-state" bis_skin_checked="1">
                                <div class="empty-state-icon" bis_skin_checked="1">
                                    <i class="fas fa-comments"></i>
                                </div>
                                <h5 class="empty-state-text">لا توجد محادثات</h5>
                                <p class="text-muted">لم يتم إنشاء أي محادثات حتى الآن</p>
                            </div>
                        @else
                            @foreach ($chats as $chat)
                                <div class="chat-item {{ $chat->unread_count > 0 ? 'unread' : '' }} {{ $chat->is_active ? 'active' : '' }}"
                                    bis_skin_checked="1">
                                    <div class="chat-header" bis_skin_checked="1">
                                        <div class="chat-title" bis_skin_checked="1">
                                            <div class="d-flex align-items-center gap-3" bis_skin_checked="1">
                                                <span>محادثة #{{ $chat->id }}</span>
                                                <span class="badge-status">
                                                    {{ $chat->type_label }}
                                                </span>
                                                @if ($chat->is_active)
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-circle me-1"></i>نشطة الآن
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="chat-date" bis_skin_checked="1">
                                            <i class="far fa-clock me-1"></i>
                                            {{ $chat->last_message_at ? $chat->last_message_at->translatedFormat('d M Y - h:i A') : 'لا توجد رسائل' }}
                                        </div>
                                    </div>

                                    <div class="chat-participants" bis_skin_checked="1">
                                        @foreach ($chat->participants_info as $participant)
                                            <div class="participant-badge {{ $participant['type'] }}"
                                                bis_skin_checked="1">
                                                @if (isset($participant['avatar']) && $participant['avatar'])
                                                    <img src="{{ asset('storage/' . $participant['avatar']) }}" alt="avatar" class="participant-avatar">
                                                @else
                                                    <i class="fas fa-{{ $participant['type'] == 'user' ? 'user' : 'truck' }}"></i>
                                                @endif
                                                {{ $participant['name'] }}
                                                @if ($participant['is_online'])
                                                    <span class="online-dot"></span>
                                                @endif
                                                <small
                                                    class="text-muted ms-1">({{ $participant['type'] == 'user' ? 'مستخدم' : 'سائق' }})</small>
                                            </div>
                                        @endforeach
                                    </div>

                                    @if ($chat->last_message)
                                        <div class="chat-preview" bis_skin_checked="1">
                                            <i class="fas fa-message me-1"></i>
                                            {{ Str::limit($chat->last_message, 150) }}
                                        </div>
                                    @endif

                                    <div class="chat-meta" bis_skin_checked="1">
                                        <div class="chat-type" bis_skin_checked="1">
                                            <i class="fas fa-comment me-1"></i>
                                            {{ $chat->messages_count ?? 0 }} رسالة
                                        </div>

                                        @if ($chat->unread_count > 0)
                                            <div class="unread-badge" title="{{ $chat->unread_count }} رسالة غير مقروءة">
                                                {{ $chat->unread_count }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="chat-actions" bis_skin_checked="1">
                                        <a href="{{ route('admin.chats.show', $chat->id) }}"
                                            class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye me-1"></i>عرض المحادثة
                                        </a>
                                        @if ($chat->unread_count > 0)
                                            <button class="btn btn-sm btn-warning mark-read-btn"
                                                data-chat-id="{{ $chat->id }}">
                                                <i class="fas fa-check-circle me-1"></i>تحديد كمقروء
                                            </button>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-danger delete-chat-btn"
                                            data-id="{{ $chat->id }}"
                                            data-participants="{{ implode(', ', array_column($chat->participants_info, 'name')) }}">
                                            <i class="fas fa-trash me-1"></i>حذف
                                        </button>
                                    </div>
                                </div>
                            @endforeach

                            <!-- الترقيم -->
                            @if ($chats->hasPages())
                                <div class="m-3">
                                    <nav>
                                        <ul class="pagination">
                                            {{-- Previous Page Link --}}
                                            @if ($chats->onFirstPage())
                                                <li class="page-item disabled" aria-disabled="true">
                                                    <span class="page-link waves-effect" aria-hidden="true">‹</span>
                                                </li>
                                            @else
                                                <li class="page-item">
                                                    <a class="page-link waves-effect"
                                                        href="{{ $chats->previousPageUrl() }}" rel="prev">‹</a>
                                                </li>
                                            @endif

                                            {{-- Pagination Elements --}}
                                            @foreach ($chats->links()->elements[0] as $page => $url)
                                                @if ($page == $chats->currentPage())
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
                                            @if ($chats->hasMorePages())
                                                <li class="page-item">
                                                    <a class="page-link waves-effect" href="{{ $chats->nextPageUrl() }}"
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
            // البحث مع تأخير
            let searchTimeout;
            $('#searchInput').on('keyup', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    applyFilters();
                }, 500);
            });

            // حذف المحادثة
            $('.delete-chat-btn').on('click', function() {
                const chatId = $(this).data('id');
                const participants = $(this).data('participants');

                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    html: `سيتم حذف محادثة المشاركين:<br><strong>${participants}</strong><br>بشكل نهائي مع جميع الرسائل`,
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
                            url: "{{ route('admin.adminChats.destroy', '') }}/" + chatId,
                            type: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                _method: 'DELETE'
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'تم الحذف',
                                    text: response.success ||
                                        'تم حذف المحادثة بنجاح',
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
                                    text: 'حدث خطأ أثناء حذف المحادثة',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            }
                        });
                    }
                });
            });

            // تحديد الرسائل كمقروءة
            $('.mark-read-btn').on('click', function() {
                const chatId = $(this).data('chat-id');
                const button = $(this);

                $.ajax({
                    url: "{{ route('admin.chats.mark-read', '') }}/" + chatId,
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        button.closest('.chat-item').removeClass('unread');
                        button.remove();

                        // تحديث العداد
                        const unreadBadge = button.closest('.chat-meta').find('.unread-badge');
                        if (unreadBadge.length) {
                            unreadBadge.remove();
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'تم',
                            text: 'تم تحديد جميع الرسائل كمقروءة',
                            timer: 1000,
                            showConfirmButton: false
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

        function filterByType(type) {
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

        function toggleUnreadOnly() {
            const current = {{ request('unread_only') ? 'true' : 'false' }};
            updateUrl({
                unread_only: !current ? '1' : null
            });
        }

        function applyFilters() {
            const params = {
                search: $('#searchInput').val(),
                participant: $('#participantSelect').val(),
                date_from: $('#dateFrom').val(),
                date_to: $('#dateTo').val()
            };

            updateUrl(params);
        }

        function resetFilters() {
            window.location.href = "{{ route('admin.chats.index') }}";
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
