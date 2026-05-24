@extends('Admin.layout.master')

@section('title', 'المحادثات الحية')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #696cff;
            --dark-bg: #1e1e2d;
            --dark-card: #2b3b4c;
        }

        body {
            font-family: "Cairo", sans-serif !important;
            background: var(--dark-bg);
            color: #fff;
        }

        .live-indicator {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #dc3545;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            z-index: 1000;
            animation: blink 1s infinite;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .live-message {
            border-right: 4px solid #4CAF50;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateX(-100px);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .message-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .message-content {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 10px 15px;
            max-width: 70%;
        }

        .voice-message {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .voice-message i {
            cursor: pointer;
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
                <li class="breadcrumb-item active">المحادثات الحية</li>
            </ol>
        </nav>

        <!-- مؤشر البث المباشر -->
        <div class="live-indicator">
            <i class="fas fa-circle me-1"></i> بث مباشر
        </div>

        <div class="row" bis_skin_checked="1">
            <!-- المحادثات النشطة -->
            <div class="col-lg-4 mb-4" bis_skin_checked="1">
                <div class="card" bis_skin_checked="1">
                    <div class="card-header bg-primary text-white" bis_skin_checked="1">
                        <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>المحادثات النشطة</h5>
                    </div>
                    <div class="card-body" bis_skin_checked="1">
                        @if ($recentChats->isEmpty())
                            <div class="text-center py-5" bis_skin_checked="1">
                                <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                                <p class="text-muted">لا توجد محادثات نشطة</p>
                            </div>
                        @else
                            <div class="list-group" bis_skin_checked="1">
                                @foreach ($recentChats as $chat)
                                    <a href="{{ route('admin.chats.show', $chat->id) }}"
                                        class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between" bis_skin_checked="1">
                                            <h6 class="mb-1">محادثة #{{ $chat->id }}</h6>
                                            <small>{{ $chat->last_message_at->diffForHumans() }}</small>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center" bis_skin_checked="1">
                                            <small>
                                                @foreach ($chat->participants_info as $participant)
                                                    {{ $participant['name'] }}
                                                    @if (!$loop->last)
                                                        -
                                                    @endif
                                                @endforeach
                                            </small>
                                            @if ($chat->unread_count > 0)
                                                <span class="badge bg-danger">{{ $chat->unread_count }}</span>
                                            @endif
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- الرسائل المباشرة -->
            <div class="col-lg-8" bis_skin_checked="1">
                <div class="card" bis_skin_checked="1">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center"
                        bis_skin_checked="1">
                        <h5 class="mb-0"><i class="fas fa-comment-dots me-2"></i>الرسائل المباشرة</h5>
                        <button class="btn btn-light btn-sm" onclick="refreshMessages()">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                    <div class="card-body" style="max-height: 600px; overflow-y: auto;" bis_skin_checked="1">
                        <div id="messagesContainer" bis_skin_checked="1">
                            @if ($recentMessages->isEmpty())
                                <div class="text-center py-5" bis_skin_checked="1">
                                    <i class="fas fa-envelope fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">لا توجد رسائل حديثة</p>
                                </div>
                            @else
                                @foreach ($recentMessages as $message)
                                    <div class="message-item mb-3 live-message" bis_skin_checked="1">
                                        <div class="d-flex gap-3" bis_skin_checked="1">
                                            @if (isset($message->sender->avatar) && $message->sender->avatar)
                                                <img src="{{ asset('storage/' . $message->sender->avatar) }}"
                                                    class="message-avatar" alt="{{ $message->sender->name }}">
                                            @else
                                                <div class="message-avatar d-flex align-items-center justify-content-center bg-secondary text-white">
                                                    <i class="fas fa-{{ $message->sender_type === 'App\Models\Driver' ? 'truck' : 'user' }}"></i>
                                                </div>
                                            @endif
                                            <div class="message-content" bis_skin_checked="1">
                                                <div class="d-flex justify-content-between align-items-start mb-2"
                                                    bis_skin_checked="1">
                                                    <strong>{{ $message->sender->name }}</strong>
                                                    <small
                                                        class="text-muted">{{ $message->created_at->diffForHumans() }}</small>
                                                </div>

                                                @if ($message->message_type === 'voice')
                                                    <div class="voice-message" bis_skin_checked="1">
                                                        <i class="fas fa-play"></i>
                                                        <span>رسالة صوتية</span>
                                                        <small class="ms-2">{{ $message->duration }} ثانية</small>
                                                    </div>
                                                @elseif($message->message_type === 'image')
                                                    <div class="image-message" bis_skin_checked="1">
                                                        <i class="fas fa-image"></i>
                                                        <span>صورة</span>
                                                    </div>
                                                @else
                                                    <p class="mb-0">{{ $message->message }}</p>
                                                @endif

                                                <div class="mt-2 text-muted" bis_skin_checked="1">
                                                    <small>
                                                        <i class="fas fa-comment me-1"></i>
                                                        محادثة #{{ $message->chat_id }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script>
        // تهيئة Pusher للبث المباشر
        const pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
            cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
            forceTLS: true
        });

        // الاشتراك في قناة المحادثات
        const channel = pusher.subscribe('admin-chats');

        // استقبال رسائل جديدة
        channel.bind('MessageSent', function(data) {
            addNewMessage(data.message);
        });

        // دالة لإضافة رسالة جديدة
        function addNewMessage(message) {
            const messagesContainer = document.getElementById('messagesContainer');

            // تحويل التاريخ
            const date = new Date(message.created_at);
            const timeAgo = timeSince(date);

            // بناء اسم المرسل والصورة بأمان لتفادي الأخطاء عند استقبال رسائل من المشرف (والتي يكون فيها sender فارغاً)
            const senderName = message.sender ? message.sender.name : 'الدعم الفني';
            const isDriver = message.sender_type === 'App\\Models\\Driver';

            // بناء عنصر الرسالة
            const messageHtml = `
                <div class="message-item mb-3 live-message">
                    <div class="d-flex gap-3">
                        ${(message.sender && message.sender.avatar) ? 
                            `<img src="/storage/${message.sender.avatar}" class="message-avatar" alt="${senderName}">` : 
                            `<div class="message-avatar d-flex align-items-center justify-content-center bg-secondary text-white">
                                <i class="fas fa-${isDriver ? 'truck' : 'user'}"></i>
                            </div>`
                        }
                        <div class="message-content">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <strong>${senderName}</strong>
                                <small class="text-muted">${timeAgo}</small>
                            </div>
                            
                            ${getMessageContent(message)}
                            
                            <div class="mt-2 text-muted">
                                <small>
                                    <i class="fas fa-comment me-1"></i>
                                    محادثة #${message.chat_id}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // إضافة الرسالة في الأعلى
            messagesContainer.insertAdjacentHTML('afterbegin', messageHtml);

            // عرض تنبيه
            showNotification(message);
        }

        // دالة للحصول على محتوى الرسالة
        function getMessageContent(message) {
            if (message.message_type === 'voice') {
                return `
                    <div class="voice-message">
                        <i class="fas fa-play"></i>
                        <span>رسالة صوتية</span>
                        <small class="ms-2">${message.duration} ثانية</small>
                    </div>
                `;
            } else if (message.message_type === 'image') {
                return `
                    <div class="image-message">
                        <i class="fas fa-image"></i>
                        <span>صورة</span>
                    </div>
                `;
            } else {
                return `<p class="mb-0">${message.message}</p>`;
            }
        }

        // دالة لعرض التنبيه
        function showNotification(message) {
            if (Notification.permission === 'granted') {
                const senderName = message.sender ? message.sender.name : 'الدعم الفني';
                new Notification('رسالة جديدة', {
                    body: `${senderName}: ${message.message || 'رسالة صوتية'}`,
                    icon: (message.sender && message.sender.avatar) ? message.sender.avatar : '/favicon.ico'
                });
            }
        }

        // دالة لحساب الوقت المنقضي
        function timeSince(date) {
            const seconds = Math.floor((new Date() - date) / 1000);

            let interval = Math.floor(seconds / 31536000);
            if (interval >= 1) return `منذ ${interval} سنة`;

            interval = Math.floor(seconds / 2592000);
            if (interval >= 1) return `منذ ${interval} شهر`;

            interval = Math.floor(seconds / 86400);
            if (interval >= 1) return `منذ ${interval} يوم`;

            interval = Math.floor(seconds / 3600);
            if (interval >= 1) return `منذ ${interval} ساعة`;

            interval = Math.floor(seconds / 60);
            if (interval >= 1) return `منذ ${interval} دقيقة`;

            return 'الآن';
        }

        // طلب إذن الإشعارات
        if (Notification.permission === 'default') {
            Notification.requestPermission();
        }

        // تحديث الرسائل يدوياً
        function refreshMessages() {
            window.location.reload();
        }

        // تحديث تلقائي كل 30 ثانية تم إيقافه للاعتماد كلياً على Pusher البث المباشر
        // setInterval(refreshMessages, 30000);
    </script>
@endsection
