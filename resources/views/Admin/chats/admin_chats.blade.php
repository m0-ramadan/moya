@extends('Admin.layout.master')

@section('title', 'محادثاتي المباشرة')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #696cff;
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --dark-bg: #1e1e2d;
            --dark-card: #2b3b4c;
            --unread-bg: rgba(105, 108, 255, 0.15);
        }

        body {
            font-family: "Cairo", sans-serif !important;
            background: var(--dark-bg);
            color: #fff;
        }

        .admin-chats-card {
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

        .chat-list-item {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
            cursor: pointer;
            position: relative;
        }

        .chat-list-item:hover {
            transform: translateX(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            background: rgba(255, 255, 255, 0.1);
        }

        .chat-list-item.unread {
            background: var(--unread-bg);
            border-color: var(--primary-color);
        }

        .chat-list-item.active-chat {
            background: rgba(105, 108, 255, 0.2);
            border-color: var(--primary-color);
        }

        .chat-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary-color);
        }

        .online-dot {
            width: 12px;
            height: 12px;
            background: #4CAF50;
            border-radius: 50%;
            position: absolute;
            bottom: 5px;
            right: 5px;
            border: 2px solid var(--dark-card);
        }

        .chat-info {
            flex: 1;
        }

        .chat-name {
            font-weight: 600;
            font-size: 16px;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 5px;
        }

        .chat-preview {
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .chat-time {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.5);
            text-align: right;
        }

        .chat-type {
            font-size: 11px;
            padding: 3px 10px;
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.1);
            display: inline-block;
            margin-top: 5px;
        }

        .badge-user {
            background: rgba(76, 175, 80, 0.2);
            color: #4CAF50;
        }

        .badge-driver {
            background: rgba(33, 150, 243, 0.2);
            color: #2196F3;
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

        .empty-state {
            text-align: center;
            padding: 50px 20px;
        }

        .empty-state-icon {
            font-size: 60px;
            color: rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }

        .new-chat-btn {
            position: fixed;
            bottom: 30px;
            left: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--primary-gradient);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 5px 15px rgba(105, 108, 255, 0.4);
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .new-chat-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 25px rgba(105, 108, 255, 0.6);
            color: white;
        }

        .search-box {
            position: relative;
            margin-bottom: 20px;
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

        .filter-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .filter-tab {
            padding: 8px 20px;
            border-radius: 25px;
            background: rgba(255, 255, 255, 0.05);
            color: rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.1);
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .filter-tab:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .filter-tab.active {
            background: var(--primary-gradient);
            color: white;
            border-color: var(--primary-color);
        }

        @media (max-width: 768px) {
            .chat-list-item {
                flex-direction: column;
                gap: 15px;
            }

            .chat-time {
                text-align: left;
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
                    <a href="{{ route('admin.chats.index') }}">المحادثات</a>
                </li>
                <li class="breadcrumb-item active">محادثاتي المباشرة</li>
            </ol>
        </nav>

        <!-- زر إنشاء محادثة جديدة -->
        <a href="{{ route('admin.adminChats.create') }}" class="new-chat-btn" title="إنشاء محادثة جديدة">
            <i class="fas fa-plus"></i>
        </a>

        <div class="row" bis_skin_checked="1">
            <div class="col-12" bis_skin_checked="1">
                <div class="admin-chats-card" bis_skin_checked="1">
                    <div class="chats-header" bis_skin_checked="1">
                        <div class="d-flex justify-content-between align-items-center" bis_skin_checked="1">
                            <div bis_skin_checked="1">
                                <h5 class="mb-0">محادثاتي المباشرة</h5>
                                <small class="opacity-75">محادثاتي مع المستخدمين والسائقين</small>
                            </div>
                            <div class="badge bg-warning" bis_skin_checked="1">
                                <i class="fas fa-comment me-1"></i>
                                {{ $chats->total() }} محادثة
                            </div>
                        </div>
                    </div>

                    <!-- الفلترة -->
                    <div class="filter-tabs" bis_skin_checked="1">
                        <button class="filter-tab {{ !request('chat_type') ? 'active' : '' }}"
                            onclick="filterByType('all')">
                            جميع المحادثات
                        </button>
                        <button class="filter-tab {{ request('chat_type') == 'admin_user' ? 'active' : '' }}"
                            onclick="filterByType('admin_user')">
                            <i class="fas fa-user me-2"></i>مع المستخدمين
                        </button>
                        <button class="filter-tab {{ request('chat_type') == 'admin_driver' ? 'active' : '' }}"
                            onclick="filterByType('admin_driver')">
                            <i class="fas fa-truck me-2"></i>مع السائقين
                        </button>
                        <button class="filter-tab {{ request('unread_only') ? 'active' : '' }}"
                            onclick="toggleUnreadOnly()">
                            <i class="fas fa-envelope me-2"></i>غير مقروء فقط
                        </button>
                    </div>

                    <!-- البحث -->
                    <div class="search-box" bis_skin_checked="1">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" class="form-control" id="searchInput" placeholder="ابحث في المحادثات..."
                            value="{{ request('search') }}">
                    </div>

                    <!-- قائمة المحادثات -->
                    <div class="card-body" bis_skin_checked="1">
                        @if ($chats->isEmpty())
                            <div class="empty-state" bis_skin_checked="1">
                                <div class="empty-state-icon" bis_skin_checked="1">
                                    <i class="fas fa-comments"></i>
                                </div>
                                <h5 class="empty-state-text">لا توجد محادثات</h5>
                                <p class="text-muted">ابدأ محادثة جديدة مع مستخدم أو سائق</p>
                                <a href="{{ route('admin.adminChats.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>إنشاء محادثة جديدة
                                </a>
                            </div>
                        @else
                            @foreach ($chats as $chat)
                                <div class="chat-list-item {{ $chat->unread_count > 0 ? 'unread' : '' }} 
                                    {{ request('active_chat') == $chat->id ? 'active-chat' : '' }}"
                                    onclick="openChat('{{ $chat->id }}')" bis_skin_checked="1">

                                    <div class="d-flex align-items-center gap-3" bis_skin_checked="1">
                                        <!-- صورة المشارك -->
                                        <div class="position-relative" bis_skin_checked="1">
                                            <img src="{{ $chat->other_participant['avatar'] ?? 'https://via.placeholder.com/50' }}"
                                                class="chat-avatar"
                                                alt="{{ $chat->other_participant['name'] ?? 'مستخدم' }}">
                                            @if ($chat->other_participant['is_online'] ?? false)
                                                <span class="online-dot"></span>
                                            @endif
                                        </div>

                                        <!-- معلومات المحادثة -->
                                        <div class="chat-info" bis_skin_checked="1">
                                            <div class="d-flex justify-content-between align-items-start"
                                                bis_skin_checked="1">
                                                <div class="chat-name" bis_skin_checked="1">
                                                    {{ $chat->other_participant['name'] ?? 'مستخدم' }}
                                                    <span
                                                        class="chat-type badge-{{ $chat->other_participant['type'] ?? 'user' }}">
                                                        {{ ($chat->other_participant['type'] ?? 'user') == 'user' ? 'مستخدم' : 'سائق' }}
                                                    </span>
                                                </div>
                                                <div class="chat-time" bis_skin_checked="1">
                                                    {{ $chat->last_message_at ? $chat->last_message_at->diffForHumans() : '' }}
                                                </div>
                                            </div>

                                            <div class="chat-preview" bis_skin_checked="1">
                                                {{ Str::limit($chat->last_message, 100) ?? 'بداية المحادثة' }}
                                            </div>
                                        </div>

                                        <!-- عدد الرسائل غير المقروءة -->
                                        @if ($chat->unread_count > 0)
                                            <div class="unread-badge" title="{{ $chat->unread_count }} رسالة غير مقروءة">
                                                {{ $chat->unread_count }}
                                            </div>
                                        @endif
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
            let searchTimeout;

            // البحث مع تأخير
            $('#searchInput').on('keyup', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    applyFilters();
                }, 500);
            });

            // فتح المحادثة
            window.openChat = function(chatId) {
                window.location.href = "{{ route('admin.adminChats.show', '') }}/" + chatId;
            };

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
                    chat_type: null
                });
            } else {
                updateUrl({
                    chat_type: type
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
                search: $('#searchInput').val()
            };

            updateUrl(params);
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
