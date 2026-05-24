@extends('Admin.layout.master')

@section('title', 'تفاصيل المحادثة')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #696cff;
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --dark-bg: #1e1e2d;
            --dark-card: #2b3b4c;
            --user-message-bg: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --other-message-bg: rgba(255, 255, 255, 0.1);
            --admin-message-bg: rgba(255, 193, 7, 0.2);
        }

        body {
            font-family: "Cairo", sans-serif !important;
            background: var(--dark-bg);
            color: #fff;
        }

        .chat-details-card {
            background: var(--dark-card);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .chat-header-section {
            background: var(--primary-gradient);
            color: white;
            padding: 25px 30px;
            border-radius: 15px 15px 0 0;
            margin-bottom: 30px;
        }

        .participant-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .participant-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .participant-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary-color);
        }

        .online-status {
            position: absolute;
            bottom: 5px;
            right: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #4CAF50;
            border: 2px solid var(--dark-card);
        }

        .stats-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            text-align: center;
            border-top: 3px solid var(--primary-color);
        }

        .stats-number {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 5px;
        }

        .stats-label {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.7);
            text-transform: uppercase;
        }

        /* منطقة الرسائل */
        .messages-container {
            max-height: 600px;
            overflow-y: auto;
            padding: 20px;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .message-wrapper {
            margin-bottom: 20px;
            display: flex;
        }

        .message-wrapper.sent {
            flex-direction: row-reverse;
        }

        .message-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 10px;
        }

        .message-content {
            max-width: 70%;
            position: relative;
        }

        .message-bubble {
            padding: 12px 15px;
            border-radius: 15px;
            position: relative;
            word-wrap: break-word;
        }

        .message-wrapper.received .message-bubble {
            background: var(--other-message-bg);
            border-bottom-right-radius: 5px;
        }

        .message-wrapper.sent .message-bubble {
            background: var(--user-message-bg);
            color: white;
            border-bottom-left-radius: 5px;
        }

        .message-wrapper.admin .message-bubble {
            background: var(--admin-message-bg);
            border-left: 3px solid var(--warning-color);
        }

        .message-time {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.6);
            margin-top: 5px;
            text-align: right;
        }

        .message-wrapper.sent .message-time {
            text-align: left;
        }

        .message-sender {
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 3px;
            color: rgba(255, 255, 255, 0.9);
        }

        /* الرسائل الصوتية */
        .voice-message {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .voice-message:hover {
            transform: scale(1.02);
        }

        .voice-play-btn {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: white;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .voice-duration {
            font-size: 12px;
            opacity: 0.8;
        }

        .voice-waveform {
            flex: 1;
            height: 20px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            overflow: hidden;
        }

        /* الرسائل المصورة */
        .image-message {
            max-width: 300px;
            border-radius: 10px;
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .image-message:hover {
            transform: scale(1.02);
        }

        .image-message img {
            width: 100%;
            height: auto;
            display: block;
        }

        /* منطقة إرسال الرسائل (للإدارة) */
        .send-message-box {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }

        .message-input {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: white;
            border-radius: 25px;
            padding: 12px 20px;
        }

        .message-input:focus {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            box-shadow: 0 0 0 0.25rem rgba(105, 108, 255, 0.25);
        }

        .message-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .action-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .action-btn:hover {
            background: var(--primary-color);
            transform: translateY(-2px);
        }

        /* تنبيهات الرسائل الجديدة */
        .new-message-alert {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: var(--success-color);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            display: none;
            z-index: 1000;
            animation: slideInUp 0.3s ease;
        }

        @keyframes slideInUp {
            from {
                transform: translateY(100px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* معلومات المحادثة */
        .chat-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .info-item {
            background: rgba(255, 255, 255, 0.05);
            padding: 15px;
            border-radius: 8px;
            border-left: 3px solid var(--primary-color);
        }

        .info-label {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 14px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
        }

        /* أزرار التحكم */
        .control-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
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

        /* رسائل النظام */
        .system-message {
            text-align: center;
            margin: 10px 0;
            color: rgba(255, 255, 255, 0.5);
            font-size: 12px;
            font-style: italic;
        }

        /* تخصيص شريط التمرير */
        .messages-container::-webkit-scrollbar {
            width: 6px;
        }

        .messages-container::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
        }

        .messages-container::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 3px;
        }

        /* رسائل محذوفة */
        .deleted-message {
            opacity: 0.6;
            font-style: italic;
            color: rgba(255, 255, 255, 0.5);
        }

        .deleted-message .message-bubble {
            background: rgba(255, 255, 255, 0.05);
            border: 1px dashed rgba(255, 255, 255, 0.2);
        }

        /* تمييز الرسائل غير المقروءة */
        .unread-message {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }

            100% {
                opacity: 1;
            }
        }

        /* تواريخ الفواصل */
        .date-separator {
            text-align: center;
            margin: 20px 0;
            position: relative;
        }

        .date-separator::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            width: 40%;
            height: 1px;
            background: rgba(255, 255, 255, 0.2);
        }

        .date-separator::after {
            content: '';
            position: absolute;
            right: 0;
            top: 50%;
            width: 40%;
            height: 1px;
            background: rgba(255, 255, 255, 0.2);
        }

        .date-separator span {
            background: var(--dark-card);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.7);
        }

        @media (max-width: 768px) {
            .message-content {
                max-width: 85%;
            }

            .image-message {
                max-width: 200px;
            }

            .chat-info-grid {
                grid-template-columns: 1fr;
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
                <li class="breadcrumb-item active">محادثة #{{ $chat->id }}</li>
            </ol>
        </nav>

        <!-- تنبيه الرسائل الجديدة -->
        <div class="new-message-alert" id="newMessageAlert">
            <i class="fas fa-bell me-2"></i>
            <span id="newMessageText">رسالة جديدة</span>
        </div>

        <div class="row" bis_skin_checked="1">
            <!-- العمود الأيسر: معلومات المحادثة -->
            <div class="col-lg-4 col-md-5 mb-4" bis_skin_checked="1">
                <div class="chat-details-card" bis_skin_checked="1">
                    <!-- معلومات المحادثة -->
                    <div class="chat-header-section" bis_skin_checked="1">
                        <div class="d-flex justify-content-between align-items-start" bis_skin_checked="1">
                            <div bis_skin_checked="1">
                                <h4 class="mb-2">محادثة #{{ $chat->id }}</h4>
                                <p class="mb-0 opacity-75">
                                    <i class="fas fa-hashtag me-1"></i>{{ $chat->chat_uuid }}
                                </p>
                            </div>
                            <div class="text-end" bis_skin_checked="1">
                                <span class="badge bg-light text-dark">
                                    {{ $chat->type_label }}
                                </span>
                                @if ($chat->is_active)
                                    <span class="badge bg-success d-block mt-1">
                                        <i class="fas fa-circle me-1"></i>نشطة
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- إحصائيات سريعة -->
                    <div class="row mb-4" bis_skin_checked="1">
                        <div class="col-6" bis_skin_checked="1">
                            <div class="stats-card" bis_skin_checked="1">
                                <div class="stats-number" bis_skin_checked="1">
                                    {{ $chatStats['total_messages'] }}
                                </div>
                                <div class="stats-label" bis_skin_checked="1">الرسائل</div>
                            </div>
                        </div>
                        <div class="col-6" bis_skin_checked="1">
                            <div class="stats-card" bis_skin_checked="1">
                                <div class="stats-number" bis_skin_checked="1">
                                    {{ $chatStats['unread_messages'] }}
                                </div>
                                <div class="stats-label" bis_skin_checked="1">غير مقروء</div>
                            </div>
                        </div>
                        <div class="col-6" bis_skin_checked="1">
                            <div class="stats-card" bis_skin_checked="1">
                                <div class="stats-number" bis_skin_checked="1">
                                    {{ $chatStats['voice_messages'] }}
                                </div>
                                <div class="stats-label" bis_skin_checked="1">صوتية</div>
                            </div>
                        </div>
                        <div class="col-6" bis_skin_checked="1">
                            <div class="stats-card" bis_skin_checked="1">
                                <div class="stats-number" bis_skin_checked="1">
                                    {{ $chatStats['image_messages'] }}
                                </div>
                                <div class="stats-label" bis_skin_checked="1">صور</div>
                            </div>
                        </div>
                    </div>

                    <!-- معلومات المحادثة -->
                    <div class="chat-info-grid mb-4" bis_skin_checked="1">
                        <div class="info-item" bis_skin_checked="1">
                            <div class="info-label" bis_skin_checked="1">تاريخ البدء</div>
                            <div class="info-value" bis_skin_checked="1">
                                {{ $chatStats['first_message_date'] ? $chatStats['first_message_date']->translatedFormat('d M Y - h:i A') : 'لا توجد رسائل' }}
                            </div>
                        </div>
                        <div class="info-item" bis_skin_checked="1">
                            <div class="info-label" bis_skin_checked="1">آخر رسالة</div>
                            <div class="info-value" bis_skin_checked="1">
                                {{ $chatStats['last_message_date'] ? $chatStats['last_message_date']->translatedFormat('d M Y - h:i A') : 'لا توجد رسائل' }}
                            </div>
                        </div>
                        <div class="info-item" bis_skin_checked="1">
                            <div class="info-label" bis_skin_checked="1">حالة المحادثة</div>
                            <div class="info-value" bis_skin_checked="1">
                                @if ($chat->is_active)
                                    <span class="badge bg-success">نشطة الآن</span>
                                @else
                                    <span class="badge bg-secondary">غير نشطة</span>
                                @endif
                            </div>
                        </div>
                        <div class="info-item" bis_skin_checked="1">
                            <div class="info-label" bis_skin_checked="1">مدة المحادثة</div>
                            <div class="info-value" bis_skin_checked="1">
                                @if ($chatStats['first_message_date'] && $chatStats['last_message_date'])
                                    {{ $chatStats['first_message_date']->diffForHumans($chatStats['last_message_date'], true) }}
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- المشاركون -->
                    <h6 class="mb-3"><i class="fas fa-users me-2"></i>المشاركون</h6>
                    @foreach ($participantsInfo as $participant)
                        <div class="participant-card" bis_skin_checked="1">
                            <div class="d-flex align-items-center gap-3" bis_skin_checked="1">
                                <div class="position-relative" bis_skin_checked="1">
                                    @if (isset($participant['avatar']) && $participant['avatar'])
                                        <img src="{{ asset('storage/' . $participant['avatar']) }}"
                                            class="participant-avatar" alt="{{ $participant['name'] }}">
                                    @else
                                        <div class="participant-avatar d-flex align-items-center justify-content-center bg-secondary text-white">
                                            <i class="fas fa-{{ ($participant['type'] ?? 'user') == 'user' ? 'user' : 'truck' }}"></i>
                                        </div>
                                    @endif
                                    @if ($participant['is_online'])
                                        <span class="online-status"></span>
                                    @endif
                                </div>
                                <div class="flex-grow-1" bis_skin_checked="1">
                                    <h6 class="mb-1">{{ $participant['name'] }}</h6>
                                    <small class="badge bg-{{ $participant['type'] == 'user' ? 'info' : 'warning' }}">
                                        {{ $participant['type'] == 'user' ? 'مستخدم' : 'سائق' }}
                                    </small>
                                    @if ($participant['is_online'])
                                        <small class="badge bg-success ms-2">
                                            <i class="fas fa-circle me-1"></i>متصل
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <!-- أزرار التحكم -->
                    <div class="control-buttons" bis_skin_checked="1">
                        <a href="{{ (str_contains($chat->type, 'admin_')) ? route('admin.adminChats.index') : route('admin.chats.index') }}" class="btn btn-secondary flex-grow-1">
                            <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                        </a>
                        <button class="btn btn-danger delete-chat-btn" data-id="{{ $chat->id }}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- العمود الأيمن: الرسائل -->
            <div class="col-lg-8 col-md-7" bis_skin_checked="1">
                <div class="chat-details-card" bis_skin_checked="1">
                    <div class="messages-container" id="messagesContainer" bis_skin_checked="1">
                        @if ($chat->messages->isEmpty())
                            <div class="text-center py-5" bis_skin_checked="1">
                                <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">لا توجد رسائل في هذه المحادثة</h5>
                                <p class="text-muted">ابدأ المحادثة بإرسال رسالة أولى</p>
                            </div>
                        @else
                            @php
                                $currentDate = null;
                            @endphp

                            @foreach ($chat->messages->sortBy('created_at') as $message)
                                @php
                                    $messageDate = $message->created_at->format('Y-m-d');
                                @endphp

                                @if ($currentDate != $messageDate)
                                    @php $currentDate = $messageDate @endphp
                                    <div class="date-separator" bis_skin_checked="1">
                                        <span>{{ $message->created_at->translatedFormat('d M Y') }}</span>
                                    </div>
                                @endif

                                <div class="message-wrapper {{ $message->sender_type == 'admin' ? 'admin' : ($message->sender_id == auth()->id() ? 'sent' : 'received') }} {{ $message->is_read ? '' : 'unread-message' }}"
                                    data-message-id="{{ $message->id }}" bis_skin_checked="1">

                                    @if ($message->sender_type != 'admin' && $message->sender_id != auth()->id())
                                        @if (isset($message->sender->avatar) && $message->sender->avatar)
                                            <img src="{{ asset('storage/' . $message->sender->avatar) }}"
                                                class="message-avatar" alt="{{ $message->sender?->name }}"
                                                title="{{ $message->sender?->name }}">
                                        @else
                                            <div class="message-avatar d-flex align-items-center justify-content-center bg-secondary text-white" title="{{ $message->sender?->name }}">
                                                <i class="fas fa-{{ $message->sender_type === 'App\Models\Driver' ? 'truck' : 'user' }}"></i>
                                            </div>
                                        @endif
                                    @endif

                                    <div class="message-content" bis_skin_checked="1">
                                        @if ($message->sender_type != 'admin' && $message->sender_id != auth()->id())
                                            <div class="message-sender" bis_skin_checked="1">
                                                {{ $message->sender?->name }}
                                                <small class="text-muted ms-2">
                                                    ({{ $message->sender_type == 'App\Models\User' ? 'مستخدم' : 'سائق' }})
                                                </small>
                                            </div>
                                        @endif

                                        <div class="message-bubble {{ $message->trashed() ? 'deleted-message' : '' }}"
                                            bis_skin_checked="1">
                                            @if ($message->trashed())
                                                <div class="deleted-message" bis_skin_checked="1">
                                                    <i class="fas fa-trash me-1"></i>
                                                    تم حذف هذه الرسالة
                                                </div>
                                            @else
                                                @switch($message->message_type)
                                                    @case('voice')
                                                        <div class="voice-message"
                                                            onclick="playVoiceMessage('{{ $message->file_url }}')">
                                                            <div class="voice-play-btn">
                                                                <i class="fas fa-play"></i>
                                                            </div>
                                                            <div class="voice-waveform">
                                                                <!-- يمكن إضافة موجة صوتية هنا -->
                                                            </div>
                                                            <div class="voice-duration">
                                                                {{ $message->duration }} ثانية
                                                            </div>
                                                        </div>
                                                    @break

                                                    @case('image')
                                                        <div class="image-message"
                                                            onclick="showImageModal('{{ $message->file_url }}')">
                                                            <img src="{{ $message->file_url }}" alt="صورة">
                                                            <div class="text-center mt-2" bis_skin_checked="1">
                                                                <small><i class="fas fa-image me-1"></i>صورة</small>
                                                            </div>
                                                        </div>
                                                    @break

                                                    @case('file')
                                                        <div class="file-message">
                                                            <a href="{{ $message->file_url }}" target="_blank"
                                                                class="text-white">
                                                                <i class="fas fa-file me-2"></i>
                                                                {{ $message->file_name ?? 'ملف' }}
                                                            </a>
                                                            <small class="d-block mt-1">{{ $message->file_size }}</small>
                                                        </div>
                                                    @break

                                                    @default
                                                        <div class="text-message">
                                                            {!! nl2br(e($message->message)) !!}
                                                        </div>
                                                @endswitch
                                            @endif
                                        </div>

                                        <div class="message-time" bis_skin_checked="1">
                                            <span>{{ $message->created_at->translatedFormat('h:i A') }}</span>
                                            @if ($message->is_read && $message->read_at)
                                                <span class="ms-2"
                                                    title="تمت القراءة في {{ $message->read_at->translatedFormat('d M Y - h:i A') }}">
                                                    <i class="fas fa-check-double text-success"></i>
                                                </span>
                                            @elseif($message->is_read)
                                                <span class="ms-2"><i class="fas fa-check text-muted"></i></span>
                                            @endif

                                            @if (!$message->trashed())
                                                <button class="btn btn-sm btn-link text-muted delete-message-btn ms-2"
                                                    data-message-id="{{ $message->id }}" title="حذف الرسالة">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>

                                    @if ($message->sender_type == 'admin')
                                        @if (isset(auth()->user()->avatar) && auth()->user()->avatar)
                                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}"
                                                class="message-avatar" alt="أنت" title="الدعم الفني">
                                        @else
                                            <div class="message-avatar d-flex align-items-center justify-content-center bg-primary text-white" title="أنت">
                                                <i class="fas fa-user-tie"></i>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <!-- منطقة إرسال الرسائل (للمشرف) -->
                    @if (auth()->user()->hasRole('admin'))
                        <div class="send-message-box" bis_skin_checked="1">
                            <form id="sendMessageForm">
                                @csrf
                                <input type="hidden" name="chat_id" value="{{ $chat->id }}">

                                <div class="mb-3" bis_skin_checked="1">
                                    <label class="form-label">إرسال رسالة كمشرف</label>
                                    <textarea name="message" class="form-control message-input" rows="3" placeholder="اكتب رسالتك هنا..."></textarea>
                                </div>

                                <div class="message-actions" bis_skin_checked="1">
                                    <button type="button" class="action-btn" title="إرسال صوتي">
                                        <i class="fas fa-microphone"></i>
                                    </button>
                                    <button type="button" class="action-btn" title="إرفاق صورة">
                                        <i class="fas fa-image"></i>
                                    </button>
                                    <button type="button" class="action-btn" title="إرفاق ملف">
                                        <i class="fas fa-paperclip"></i>
                                    </button>
                                    <div class="flex-grow-1" bis_skin_checked="1"></div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane me-2"></i>إرسال
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- مودال عرض الصورة -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-dark">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" alt="صورة" class="img-fluid rounded">
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script>
        $(document).ready(function() {
            // الانتقال لآخر رسالة
            scrollToBottom();

            // تهيئة Pusher للبث المباشر
            const pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
                cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
                forceTLS: true
            });

            // الاشتراك في قناة المحادثة الحالية
            const channel = pusher.subscribe('chat.{{ $chat->chat_uuid }}');

            // استقبال رسائل جديدة
            channel.bind('MessageSent', function(data) {
                addNewMessage(data.message);
            });

            // حذف المحادثة
            $('.delete-chat-btn').on('click', function() {
                const chatId = $(this).data('id');

                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    html: `سيتم حذف المحادثة #{{ $chat->id }} مع جميع رسائلها<br>هذا الإجراء لا يمكن التراجع عنه`,
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
                            url: "{{ route('admin.adminChats.destroy', $chat->id) }}",
                            type: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                _method: 'DELETE'
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'تم الحذف',
                                    text: 'تم حذف المحادثة بنجاح',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.href =
                                        "{{ route('admin.chats.index') }}";
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

            // حذف رسالة
            $(document).on('click', '.delete-message-btn', function(e) {
                e.preventDefault();
                const messageId = $(this).data('message-id');
                const messageElement = $(this).closest('.message-wrapper');

                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: 'سيتم حذف هذه الرسالة نهائياً',
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
                            url: "{{ route('admin.chats.destroy-message', '') }}/" +
                                messageId,
                            type: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                _method: 'DELETE'
                            },
                            success: function(response) {
                                if (response.success) {
                                    messageElement.find('.message-bubble').addClass(
                                        'deleted-message');
                                    messageElement.find('.message-bubble').html(`
                                        <div class="deleted-message">
                                            <i class="fas fa-trash me-1"></i>
                                            تم حذف هذه الرسالة
                                        </div>
                                    `);
                                    messageElement.find('.delete-message-btn').remove();

                                    Swal.fire({
                                        icon: 'success',
                                        title: 'تم',
                                        text: 'تم حذف الرسالة بنجاح',
                                        timer: 1000,
                                        showConfirmButton: false
                                    });
                                }
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'خطأ',
                                    text: 'حدث خطأ أثناء حذف الرسالة',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            }
                        });
                    }
                });
            });

            // إرسال رسالة جديدة (للمشرف)
            @if (auth()->user()->hasRole('admin'))
                $('#sendMessageForm').on('submit', function(e) {
                    e.preventDefault();

                    const formData = new FormData(this);
                    const messageInput = $(this).find('textarea[name="message"]');
                    const messageText = messageInput.val().trim();

                    if (!messageText) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'تنبيه',
                            text: 'يرجى كتابة رسالة',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        return;
                    }

                    $.ajax({
                        url: "{{ route('admin.chats.send-message', $chat->id) }}",
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                messageInput.val('');
                                addNewMessage(response.message);
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ',
                                text: xhr.responseJSON?.message ||
                                    'حدث خطأ أثناء إرسال الرسالة',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    });
                });
            @endif

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

        // دالة لإضافة رسالة جديدة
        function addNewMessage(message) {
            const messagesContainer = $('#messagesContainer');

            // تحويل التاريخ
            const date = new Date(message.created_at);
            const timeString = date.toLocaleTimeString('ar-EG', {
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            });

            // بناء اسم المرسل والصورة بأمان لتفادي الأخطاء عند استقبال رسائل من المشرف (والتي يكون فيها sender فارغاً)
            const senderName = message.sender ? message.sender.name : 'الدعم الفني';
            const isDriver = message.sender_type === 'App\\Models\\Driver';

            // بناء عنصر الرسالة
            const messageHtml = `
                <div class="message-wrapper ${message.sender_type === 'admin' ? 'admin' : 'received'}">
                    ${message.sender_type !== 'admin' ? `
                                    ${(message.sender && message.sender.avatar) ? 
                                        `<img src="/storage/${message.sender.avatar}" 
                                             class="message-avatar" 
                                             alt="${senderName}"
                                             title="${senderName}">` : 
                                        `<div class="message-avatar d-flex align-items-center justify-content-center bg-secondary text-white" title="${senderName}">
                                            <i class="fas fa-${isDriver ? 'truck' : 'user'}"></i>
                                         </div>`
                                    }
                                    ` : ''}
                    
                    <div class="message-content">
                        ${message.sender_type !== 'admin' ? `
                                            <div class="message-sender">
                                                ${senderName}
                                                <small class="text-muted ms-2">
                                                    (${isDriver ? 'سائق' : 'مستخدم'})
                                                </small>
                                            </div>
                                         ` : ''}
                        
                        <div class="message-bubble">
                            ${getMessageContent(message)}
                        </div>
                        
                        <div class="message-time">
                            <span>${timeString}</span>
                        </div>
                    </div>
                </div>
            `;

            // إضافة الرسالة
            messagesContainer.append(messageHtml);

            // الانتقال للرسالة الجديدة
            scrollToBottom();

            // عرض تنبيه
            showNewMessageAlert(senderName, message.message || 'رسالة جديدة');
        }

        // دالة للحصول على محتوى الرسالة
        function getMessageContent(message) {
            switch (message.message_type) {
                case 'voice':
                    return `
                        <div class="voice-message" onclick="playVoiceMessage('${message.file_url}')">
                            <div class="voice-play-btn">
                                <i class="fas fa-play"></i>
                            </div>
                            <div class="voice-waveform"></div>
                            <div class="voice-duration">${message.duration} ثانية</div>
                        </div>
                    `;
                case 'image':
                    return `
                        <div class="image-message" onclick="showImageModal('${message.file_url}')">
                            <img src="${message.file_url}" alt="صورة">
                            <div class="text-center mt-2">
                                <small><i class="fas fa-image me-1"></i>صورة</small>
                            </div>
                        </div>
                    `;
                case 'file':
                    return `
                        <div class="file-message">
                            <a href="${message.file_url}" target="_blank" class="text-white">
                                <i class="fas fa-file me-2"></i>
                                ${message.file_name || 'ملف'}
                            </a>
                            <small class="d-block mt-1">${message.file_size}</small>
                        </div>
                    `;
                default:
                    return `<div class="text-message">${message.message.replace(/\n/g, '<br>')}</div>`;
            }
        }

        // دالة للانتقال لآخر رسالة
        function scrollToBottom() {
            const container = document.getElementById('messagesContainer');
            container.scrollTop = container.scrollHeight;
        }

        // دالة لعرض تنبيه الرسائل الجديدة
        function showNewMessageAlert(sender, message) {
            const alert = $('#newMessageAlert');
            const text = $('#newMessageText');

            text.html(`<strong>${sender}:</strong> ${message.substring(0, 50)}${message.length > 50 ? '...' : ''}`);
            alert.fadeIn();

            setTimeout(() => {
                alert.fadeOut();
            }, 3000);
        }

        // دالة لتشغيل الرسائل الصوتية
        function playVoiceMessage(url) {
            const audio = new Audio(url);
            audio.play();

            // تحديث واجهة المستخدم
            Swal.fire({
                icon: 'info',
                title: 'جاري التشغيل',
                text: 'جاري تشغيل الرسالة الصوتية...',
                timer: 2000,
                showConfirmButton: false
            });
        }

        // دالة لعرض الصورة في مودال
        function showImageModal(imageUrl) {
            $('#modalImage').attr('src', imageUrl);
            $('#imageModal').modal('show');
        }

        // تحديث تلقائي للرسائل (تم إيقافه للاعتماد كلياً على Pusher)
    </script>
@endsection
