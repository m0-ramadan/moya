@extends('Admin.layout.master')

@section('title', 'إنشاء محادثة جديدة')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #696cff;
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --dark-bg: #1e1e2d;
            --dark-card: #2b3b4c;
        }

        body {
            font-family: "Cairo", sans-serif !important;
            background: var(--dark-bg);
            color: #fff;
        }

        .create-chat-card {
            background: var(--dark-card);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .step-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 25px;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .step-card.active {
            border-color: var(--primary-color);
            background: rgba(105, 108, 255, 0.1);
        }

        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-gradient);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .participant-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            margin-bottom: 15px;
        }

        .participant-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .participant-card.selected {
            border-color: var(--primary-color);
            background: rgba(105, 108, 255, 0.15);
        }

        .participant-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid transparent;
            transition: border-color 0.3s ease;
        }

        .participant-card.selected .participant-avatar {
            border-color: var(--primary-color);
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

        .type-badge {
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-user {
            background: rgba(76, 175, 80, 0.2);
            color: #4CAF50;
            border: 1px solid rgba(76, 175, 80, 0.3);
        }

        .badge-driver {
            background: rgba(33, 150, 243, 0.2);
            color: #2196F3;
            border: 1px solid rgba(33, 150, 243, 0.3);
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

        .type-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 15px;
        }

        .type-tab {
            padding: 8px 20px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.05);
            color: rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.1);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .type-tab:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .type-tab.active {
            background: var(--primary-gradient);
            color: white;
            border-color: var(--primary-color);
        }

        .message-preview {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
            display: none;
        }

        .message-preview.show {
            display: block;
        }

        .preview-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .chat-preview {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 15px;
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

        .empty-state {
            text-align: center;
            padding: 40px 20px;
        }

        .empty-state-icon {
            font-size: 60px;
            color: rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
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
                <li class="breadcrumb-item active">إنشاء محادثة جديدة</li>
            </ol>
        </nav>

        <div class="row justify-content-center" bis_skin_checked="1">
            <div class="col-lg-8" bis_skin_checked="1">
                <div class="create-chat-card" bis_skin_checked="1">
                    <div class="text-center mb-4" bis_skin_checked="1">
                        <h4>إنشاء محادثة جديدة</h4>
                        <p class="text-muted">اختر المستخدم أو السائق للبدء في محادثة مباشرة</p>
                    </div>

                    <!-- الخطوة 1: اختيار نوع المشارك -->
                    <div class="step-card active" id="step1" bis_skin_checked="1">
                        <div class="step-number" bis_skin_checked="1">1</div>
                        <h5 class="mb-3">اختر نوع المشارك</h5>

                        <div class="type-tabs" bis_skin_checked="1">
                            <div class="type-tab active" data-type="all">
                                <i class="fas fa-users me-2"></i>الجميع
                            </div>
                            <div class="type-tab" data-type="user">
                                <i class="fas fa-user me-2"></i>المستخدمين
                            </div>
                            <div class="type-tab" data-type="driver">
                                <i class="fas fa-truck me-2"></i>السائقين
                            </div>
                        </div>

                        <div class="search-box" bis_skin_checked="1">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" class="form-control" id="searchInput"
                                placeholder="ابحث بالاسم، البريد الإلكتروني، أو رقم الهاتف...">
                        </div>

                        <!-- نتائج البحث -->
                        <div id="searchResults">
                            <!-- سيتم ملؤها بالنتائج -->
                        </div>

                        <!-- المشاركون المختارون -->
                        <div id="selectedParticipant" style="display: none;">
                            <h6 class="mb-3">المشارك المختار:</h6>
                            <div class="chat-preview" bis_skin_checked="1">
                                <div class="d-flex align-items-center gap-3" bis_skin_checked="1">
                                    <div class="position-relative" id="selectedAvatarContainer" bis_skin_checked="1">
                                        <img id="selectedAvatar" src="" class="preview-avatar" alt="" style="display:none;">
                                        <div id="selectedAvatarIcon" class="preview-avatar d-flex align-items-center justify-content-center bg-secondary text-white" style="display:none;">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <span id="selectedOnline" class="online-dot" style="display: none;"></span>
                                    </div>
                                    <div class="flex-grow-1" bis_skin_checked="1">
                                        <h6 class="mb-1" id="selectedName"></h6>
                                        <p class="mb-1 text-muted" id="selectedEmail"></p>
                                        <span id="selectedType" class="type-badge"></span>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearSelection()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- الخطوة 2: كتابة الرسالة الأولى -->
                    <div class="step-card" id="step2" bis_skin_checked="1">
                        <div class="step-number" bis_skin_checked="1">2</div>
                        <h5 class="mb-3">اكتب رسالتك الأولى</h5>

                        <div class="mb-3" bis_skin_checked="1">
                            <label for="initialMessage" class="form-label">رسالة البداية (اختياري)</label>
                            <textarea class="form-control" id="initialMessage" rows="4" placeholder="اكتب رسالتك الأولى هنا..."></textarea>
                            <div class="form-text" bis_skin_checked="1">يمكنك تركها فارغة وبدء المحادثة بدون رسالة أولية
                            </div>
                        </div>
                    </div>

                    <!-- أزرار التنقل -->
                    <div class="d-flex justify-content-between mt-4" bis_skin_checked="1">
                        <a href="{{ route('admin.chats.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-right me-2"></i>إلغاء
                        </a>
                        <div class="d-flex gap-3" bis_skin_checked="1">
                            <button type="button" class="btn btn-outline-primary" id="prevBtn" style="display: none;">
                                <i class="fas fa-arrow-left me-2"></i>السابق
                            </button>
                            <button type="button" class="btn btn-primary" id="nextBtn">
                                <i class="fas fa-arrow-left me-2"></i>التالي
                            </button>
                            <button type="button" class="btn btn-success" id="createBtn" style="display: none;">
                                <i class="fas fa-comment me-2"></i>بدء المحادثة
                            </button>
                        </div>
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
            let currentStep = 1;
            let selectedParticipant = null;
            let searchTimeout;

            // تبديل بين الخطوات
            function goToStep(step) {
                // إخفاء جميع الخطوات
                $('.step-card').removeClass('active');

                // إظهار الخطوة الحالية
                $(`#step${step}`).addClass('active');

                // تحديث أزرار التنقل
                updateNavigationButtons(step);

                currentStep = step;
            }

            // تحديث أزرار التنقل
            function updateNavigationButtons(step) {
                const prevBtn = $('#prevBtn');
                const nextBtn = $('#nextBtn');
                const createBtn = $('#createBtn');

                if (step === 1) {
                    prevBtn.hide();
                    nextBtn.show();
                    createBtn.hide();

                    // تعطيل زر التالي إذا لم يتم اختيار مشارك
                    nextBtn.prop('disabled', !selectedParticipant);
                } else if (step === 2) {
                    prevBtn.show();
                    nextBtn.hide();
                    createBtn.show();
                }
            }

            // البحث عن المشاركين
            $('#searchInput').on('keyup', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    searchParticipants();
                }, 500);
            });

            // تغيير نوع المشارك
            $('.type-tab').on('click', function() {
                $('.type-tab').removeClass('active');
                $(this).addClass('active');
                searchParticipants();
            });

            // البحث عن المشاركين
            function searchParticipants() {
                const searchTerm = $('#searchInput').val();
                const selectedType = $('.type-tab.active').data('type');

                if (searchTerm.length < 2 && searchTerm !== '') {
                    return;
                }
                $.ajax({
                    url: "{{ route('admin.adminChats.search-participants') }}",
                    type: 'GET',
                    data: {
                        search: searchTerm,
                        type: selectedType
                    },
                    success: function(response) {
                        displayResults(response.results);
                    },
                    error: function(xhr, status, error) {
                        // طباعة الخطأ في الـ console
                        console.error("AJAX Error:", status, error);
                        console.log("Response Text:", xhr.responseText);

                        // لو عايز تظهر رسالة للمستخدم برضه
                        $('#searchResults').html(`
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <p class="text-muted">حدث خطأ أثناء البحث</p>
            </div>
        `);
                    }
                });

            }

            // عرض نتائج البحث
            function displayResults(results) {
                const container = $('#searchResults');

                if (results.length === 0) {
                    container.html(`
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="fas fa-user-slash"></i>
                            </div>
                            <p class="text-muted">لم يتم العثور على نتائج</p>
                        </div>
                    `);
                    return;
                }

                let html = '<div class="row">';

                results.forEach(participant => {
                    const isOnline = participant.is_online || false;
                    const avatarHtml = participant.avatar ? 
                        `<img src="/storage/${participant.avatar}" class="participant-avatar" alt="${participant.name}">` : 
                        `<div class="participant-avatar d-flex align-items-center justify-content-center bg-secondary text-white">
                            <i class="fas fa-${participant.type === 'user' ? 'user' : 'truck'} fa-lg"></i>
                        </div>`;

                    html += `
                        <div class="col-md-6 mb-3">
                            <div class="participant-card" data-id="${participant.id}" data-type="${participant.type}">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="position-relative">
                                        ${avatarHtml}
                                        ${isOnline ? '<span class="online-dot"></span>' : ''}
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">${participant.name}</h6>
                                        <p class="mb-1 text-muted">${participant.email}</p>
                                        <span class="type-badge badge-${participant.type}">
                                            ${participant.type_label}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });

                html += '</div>';
                container.html(html);

                // إضافة حدث النقر على المشارك
                $('.participant-card').on('click', function() {
                    selectParticipant($(this).data('id'), $(this).data('type'));
                });
            }

            // اختيار مشارك
            function selectParticipant(id, type) {
                // إزالة التحديد السابق
                $('.participant-card').removeClass('selected');
                $(`.participant-card[data-id="${id}"]`).addClass('selected');

                // جلب معلومات المشارك
                $.ajax({
                    url: "{{ route('admin.adminChats.get-participant-info') }}",
                    type: 'GET',
                    data: {
                        id: id,
                        type: type
                    },
                    success: function(response) {
                        selectedParticipant = response.data;
                        displaySelectedParticipant();

                        // تفعيل زر التالي
                        $('#nextBtn').prop('disabled', false);
                    }
                });
            }

            // عرض المشارك المختار
            function displaySelectedParticipant() {
                const container = $('#selectedParticipant');
                const participant = selectedParticipant;

                if (participant.avatar) {
                    $('#selectedAvatar').attr('src', '/storage/' + participant.avatar).show();
                    $('#selectedAvatarIcon').hide();
                } else {
                    $('#selectedAvatar').hide();
                    $('#selectedAvatarIcon').html(`<i class="fas fa-${participant.type === 'user' ? 'user' : 'truck'}"></i>`).show();
                }
                $('#selectedName').text(participant.name);
                $('#selectedEmail').text(participant.email);

                if (participant.is_online) {
                    $('#selectedOnline').show();
                } else {
                    $('#selectedOnline').hide();
                }

                $('#selectedType').text(participant.type === 'user' ? 'مستخدم' : 'سائق')
                    .removeClass('badge-user badge-driver')
                    .addClass(participant.type === 'user' ? 'badge-user' : 'badge-driver');

                container.show();
            }

            // مسح الاختيار
            function clearSelection() {
                selectedParticipant = null;
                $('.participant-card').removeClass('selected');
                $('#selectedParticipant').hide();
                $('#nextBtn').prop('disabled', true);
            }

            // التنقل بين الخطوات
            $('#nextBtn').on('click', function() {
                if (currentStep === 1 && selectedParticipant) {
                    goToStep(2);
                }
            });

            $('#prevBtn').on('click', function() {
                if (currentStep === 2) {
                    goToStep(1);
                }
            });

            // إنشاء المحادثة
            $('#createBtn').on('click', function() {
                const initialMessage = $('#initialMessage').val().trim();

                Swal.fire({
                    title: 'إنشاء المحادثة',
                    text: 'هل أنت متأكد من بدء محادثة جديدة؟',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#696cff',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'نعم، ابدأ المحادثة',
                    cancelButtonText: 'إلغاء',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        createChat(initialMessage);
                    }
                });
            });

            // إنشاء المحادثة في الخلفية
            function createChat(initialMessage) {
                const formData = new FormData();
                formData.append('participant_id', selectedParticipant.id);
                formData.append('participant_type', selectedParticipant.type);
                formData.append('initial_message', initialMessage);
                formData.append('_token', '{{ csrf_token() }}');

                $.ajax({
                    url: "{{ route('admin.adminChats.store') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'تم',
                            text: 'تم إنشاء المحادثة بنجاح',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = response.redirect ||
                                "{{ route('admin.chats.index') }}";
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: xhr.responseJSON?.message || 'حدث خطأ أثناء إنشاء المحادثة',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                });
            }

            // عرض بعض المشاركين عند التحميل
            searchParticipants();
        });
    </script>
@endsection
