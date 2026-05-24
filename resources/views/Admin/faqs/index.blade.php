@extends('Admin.layout.master')

@section('title', 'إدارة الأسئلة الشائعة')

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
            --dark-bg: #1e1e2d;
            --dark-card: #2b3b4c;
        }

        body {
            font-family: "Cairo", sans-serif !important;
            background: var(--dark-bg);
            color: #fff;
        }

        .faq-card {
            background: var(--dark-card);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .faq-header {
            background: var(--primary-gradient);
            color: white;
            padding: 25px 30px;
            border-radius: 15px 15px 0 0;
            margin: -30px -30px 0 -30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .section-title {
            color: #fff;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--primary-color);
        }

        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            padding: 10px 20px;
            font-weight: 600;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4a9a 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(105, 108, 255, 0.4);
        }

        .btn-outline-primary {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background: var(--primary-gradient);
            color: #fff;
        }

        .faq-item {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            cursor: move;
        }

        .faq-item:hover {
            border-color: var(--primary-color);
            background: rgba(105, 108, 255, 0.05);
            transform: translateX(-5px);
        }

        .faq-item.dragging {
            opacity: 0.5;
            transform: scale(0.95);
        }

        .faq-question {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .drag-handle {
            color: rgba(255, 255, 255, 0.3);
            cursor: move;
            font-size: 20px;
        }

        .drag-handle:hover {
            color: var(--primary-color);
        }

        .question-text {
            font-weight: 600;
            color: #fff;
            font-size: 16px;
            flex: 1;
        }

        .badge-status {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-active {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }

        .badge-inactive {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
            border: 1px solid rgba(220, 53, 69, 0.3);
        }

        .answer-preview {
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
            margin-top: 10px;
            margin-right: 45px;
            padding: 10px;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 8px;
            max-height: 60px;
            overflow: hidden;
            position: relative;
        }

        .answer-preview::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 30px;
            background: linear-gradient(to top, rgba(43, 59, 76, 0.9), transparent);
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .action-btn {
            width: 35px;
            height: 35px;
            border-radius: 8px;
            border: none;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.1);
        }

        .action-btn:hover {
            transform: translateY(-2px);
        }

        .action-btn.edit {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
        }

        .action-btn.edit:hover {
            background: #ffc107;
            color: #000;
        }

        .action-btn.delete {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
        }

        .action-btn.delete:hover {
            background: #dc3545;
            color: #fff;
        }

        .action-btn.view {
            background: rgba(23, 162, 184, 0.2);
            color: #17a2b8;
        }

        .action-btn.view:hover {
            background: #17a2b8;
            color: #fff;
        }

        .action-btn.toggle {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
        }

        .action-btn.toggle:hover {
            background: #28a745;
            color: #fff;
        }

        .action-btn.toggle.inactive {
            background: rgba(108, 117, 125, 0.2);
            color: #6c757d;
        }

        .action-btn.toggle.inactive:hover {
            background: #6c757d;
            color: #fff;
        }

        /* Modal Styles */
        .modal-content {
            background: var(--dark-card);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
        }

        .modal-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 20px 25px;
        }

        .modal-title {
            color: #fff;
            font-weight: 600;
        }

        .modal-body {
            padding: 25px;
        }

        .modal-footer {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 20px 25px;
        }

        .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        .form-label {
            color: rgba(255, 255, 255, 0.9);
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            border-radius: 8px;
            padding: 10px 15px;
        }

        .form-control:focus, .form-select:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--primary-color);
            color: #fff;
            box-shadow: 0 0 0 0.25rem rgba(105, 108, 255, 0.25);
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
            margin-left: 10px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(255, 255, 255, 0.2);
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background: var(--primary-gradient);
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }

        .toggle-wrapper {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }

        .toggle-label {
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state-icon {
            font-size: 80px;
            color: rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }

        .empty-state-text {
            color: rgba(255, 255, 255, 0.7);
            font-size: 18px;
            margin-bottom: 20px;
        }

        .stats-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }

        .stats-number {
            font-size: 28px;
            font-weight: 700;
            color: #fff;
        }

        .stats-label {
            color: rgba(255, 255, 255, 0.6);
            font-size: 14px;
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

        .search-box i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.5);
        }

        .filter-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 5px 15px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.8);
            cursor: pointer;
            transition: all 0.3s;
        }

        .filter-btn:hover {
            background: rgba(105, 108, 255, 0.2);
            border-color: var(--primary-color);
        }

        .filter-btn.active {
            background: var(--primary-gradient);
            color: #fff;
        }

        .order-badge {
            background: var(--primary-color);
            color: #fff;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .faq-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .faq-question {
                flex-wrap: wrap;
            }

            .action-buttons {
                margin-right: auto;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css">
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                     <a href="{{ route('admin.home') }}">الرئيسية</a>
                </li>
                <li class="breadcrumb-item active">الأسئلة الشائعة</li>
            </ol>
        </nav>

        <div class="faq-card">
            <div class="faq-header">
                <div>
                    <h5 class="mb-0">
                        <i class="fas fa-question-circle me-2"></i>
                        إدارة الأسئلة الشائعة
                    </h5>
                    <small class="opacity-75">إضافة، تعديل، وحذف الأسئلة الشائعة</small>
                </div>
                <button class="btn btn-light" onclick="openAddModal()">
                    <i class="fas fa-plus me-2"></i>
                    إضافة سؤال جديد
                </button>
            </div>

            <div class="p-4">
                <!-- الإحصائيات -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="stats-card">
                            <div class="stats-number">{{ $faqs->count() }}</div>
                            <div class="stats-label">إجمالي الأسئلة</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card">
                            <div class="stats-number">{{ $faqs->where('status', 1)->count() }}</div>
                            <div class="stats-label">أسئلة نشطة</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card">
                            <div class="stats-number">{{ $faqs->where('status', 0)->count() }}</div>
                            <div class="stats-label">أسئلة غير نشطة</div>
                        </div>
                    </div>
                </div>

                <!-- البحث والتصفية -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" class="form-control" id="searchInput" placeholder="بحث في الأسئلة...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="filter-buttons">
                            <button class="filter-btn active" onclick="filterFaqs('all')">الكل</button>
                            <button class="filter-btn" onclick="filterFaqs('active')">نشط</button>
                            <button class="filter-btn" onclick="filterFaqs('inactive')">غير نشط</button>
                        </div>
                    </div>
                </div>

                <!-- قائمة الأسئلة -->
                <div id="faqsList" class="faqs-container">
                    @forelse($faqs as $faq)
                        <div class="faq-item" data-id="{{ $faq->id }}" data-status="{{ $faq->status ? 'active' : 'inactive' }}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="faq-question">
                                    <i class="fas fa-grip-vertical drag-handle"></i>
                                    <span class="order-badge">{{ $faq->sort_order }}</span>
                                    <div class="question-text">{{ $faq->question }}</div>
                                    <span class="badge-status {{ $faq->status ? 'badge-active' : 'badge-inactive' }}">
                                        {{ $faq->status ? 'نشط' : 'غير نشط' }}
                                    </span>
                                </div>
                                <div class="action-buttons">
                                    <button class="action-btn view" onclick="viewFaq({{ $faq->id }})" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="action-btn edit" onclick="editFaq({{ $faq->id }})" title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="action-btn toggle {{ !$faq->status ? 'inactive' : '' }}" 
                                            onclick="toggleStatus({{ $faq->id }})" 
                                            title="{{ $faq->status=== 'active' ? 'تعطيل' : 'تفعيل' }}">
                                        <i class="fas {{ $faq->status ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                    </button>
                                    <button class="action-btn delete" onclick="deleteFaq({{ $faq->id }})" title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="answer-preview">
                                {!! strip_tags($faq->answer) !!}
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <h5 class="empty-state-text">لا توجد أسئلة شائعة</h5>
                            <p class="text-muted">قم بإضافة أول سؤال شائع الآن</p>
                            <button class="btn btn-primary" onclick="openAddModal()">
                                <i class="fas fa-plus me-2"></i>
                                إضافة سؤال
                            </button>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Modal لإضافة/تعديل السؤال -->
    <div class="modal fade" id="faqModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">إضافة سؤال جديد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="faqForm" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="methodField" value="POST">
                    <input type="hidden" name="faq_id" id="faqId">
                    
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">السؤال <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="question" 
                                   id="question" 
                                   class="form-control" 
                                   placeholder="أدخل السؤال"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">الإجابة <span class="text-danger">*</span></label>
                            <textarea name="answer" 
                                      id="answer" 
                                      class="form-control summernote" 
                                      rows="8"
                                      placeholder="أدخل الإجابة"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">الترتيب</label>
                                <input type="number" 
                                       name="sort_order" 
                                       id="sort_order" 
                                       class="form-control" 
                                       min="0"
                                       placeholder="الترتيب (اختياري)">
                                <small class="text-muted">سيتم تعيين ترتيب تلقائي إذا تركته فارغاً</small>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="toggle-wrapper">
                                    <label class="switch">
                                        <input type="checkbox" name="status" id="status" value="1" checked>
                                        <span class="slider"></span>
                                    </label>
                                    <span class="toggle-label">السؤال نشط</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>
                            إلغاء
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            حفظ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal لعرض السؤال -->
    <div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">عرض السؤال</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-4">
                        <h6 class="text-white mb-2">السؤال:</h6>
                        <p id="viewQuestion" class="text-white-50 p-3" style="background: rgba(255,255,255,0.05); border-radius: 8px;"></p>
                    </div>
                    <div>
                        <h6 class="text-white mb-2">الإجابة:</h6>
                        <div id="viewAnswer" class="text-white-50 p-3" style="background: rgba(255,255,255,0.05); border-radius: 8px;"></div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="p-3" style="background: rgba(255,255,255,0.05); border-radius: 8px;">
                                <small class="text-white-50 d-block">الحالة</small>
                                <span id="viewStatus" class="badge-status mt-2"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3" style="background: rgba(255,255,255,0.05); border-radius: 8px;">
                                <small class="text-white-50 d-block">الترتيب</small>
                                <span id="viewOrder" class="badge bg-primary mt-2"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>
                        إغلاق
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/lang/summernote-ar-AR.min.js"></script>

    <script>
        let faqsData = @json($faqs);
        let currentFaqId = null;

        $(document).ready(function() {
            // تهيئة Summernote
            $('.summernote').summernote({
                height: 200,
                lang: 'ar-AR',
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'italic', 'clear']],
                    ['fontname', ['fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });

            // تهيية السحب والترتيب
            const faqsList = document.getElementById('faqsList');
            if (faqsList) {
                new Sortable(faqsList, {
                    animation: 150,
                    handle: '.drag-handle',
                    ghostClass: 'dragging',
                    onEnd: function() {
                        updateOrder();
                    }
                });
            }

            // البحث المباشر
            $('#searchInput').on('keyup', function() {
                const searchTerm = $(this).val().toLowerCase();
                
                $('.faq-item').each(function() {
                    const question = $(this).find('.question-text').text().toLowerCase();
                    const answer = $(this).find('.answer-preview').text().toLowerCase();
                    
                    if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                        $(this).show();
                    } else {
                        $(this).hide();
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

        // تصفية الأسئلة
        function filterFaqs(type) {
            $('.filter-btn').removeClass('active');
            $(event.target).addClass('active');

            if (type === 'all') {
                $('.faq-item').show();
            } else if (type === 'active') {
                $('.faq-item').hide();
                $('.faq-item[data-status="active"]').show();
            } else if (type === 'inactive') {
                $('.faq-item').hide();
                $('.faq-item[data-status="inactive"]').show();
            }
        }

        // فتح modal الإضافة
        function openAddModal() {
            $('#modalTitle').text('إضافة سؤال جديد');
            $('#faqForm').attr('action', '{{ route("admin.faqs.store") }}');
            $('#methodField').val('POST');
            $('#faqId').val('');
            $('#question').val('');
            $('#answer').summernote('code', '');
            $('#sort_order').val('');
            $('#status').prop('checked', true);
            
            $('#faqModal').modal('show');
        }

        // تعديل سؤال
        function editFaq(id) {
            const faq = faqsData.find(f => f.id === id);
            if (!faq) return;

            $('#modalTitle').text('تعديل السؤال');
            $('#faqForm').attr('action', `{{ route("admin.faqs.update", "") }}/${id}`);
            $('#methodField').val('PUT');
            $('#faqId').val(faq.id);
            $('#question').val(faq.question);
            $('#answer').summernote('code', faq.answer);
            $('#sort_order').val(faq.sort_order);
            $('#status').prop('checked', faq.status == 1);
            
            $('#faqModal').modal('show');
        }

        // عرض سؤال
        function viewFaq(id) {
            const faq = faqsData.find(f => f.id === id);
            if (!faq) return;

            $('#viewQuestion').text(faq.question);
            $('#viewAnswer').html(faq.answer);
            $('#viewStatus').text(faq.status ? 'نشط' : 'غير نشط')
                           .attr('class', 'badge-status ' + (faq.status ? 'badge-active' : 'badge-inactive'));
            $('#viewOrder').text(faq.sort_order);
            
            $('#viewModal').modal('show');
        }

        // حذف سؤال
        function deleteFaq(id) {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "سيتم حذف هذا السؤال نهائياً",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ route("admin.faqs.destroy", "") }}/${id}`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE'
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'تم الحذف',
                                text: 'تم حذف السؤال بنجاح',
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
        }

        // تغيير حالة السؤال
        function toggleStatus(id) {
            $.ajax({
                url: `{{ route("admin.faqs.toggle-status", "") }}/${id}`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم التحديث',
                        text: response.message,
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
                        text: 'حدث خطأ أثناء التحديث',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        }

        // تحديث الترتيب
        function updateOrder() {
            const ids = [];
            $('.faq-item').each(function() {
                ids.push($(this).data('id'));
            });

            $.ajax({
                url: '{{ route("admin.faqs.update-order") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    ids: ids
                },
                success: function(response) {
                    // تحديث أرقام الترتيب المعروضة
                    $('.faq-item').each(function(index) {
                        $(this).find('.order-badge').text(index + 1);
                    });
                    
                    // رسالة نجاح صامتة
                    console.log('تم تحديث الترتيب بنجاح');
                }
            });
        }

        // التحقق من صحة النموذج قبل الإرسال
        $('#faqForm').on('submit', function(e) {
            const question = $('#question').val().trim();
            const answer = $('#answer').summernote('code').trim();
            
            if (!question) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'تنبيه',
                    text: 'الرجاء إدخال السؤال',
                    timer: 2000,
                    showConfirmButton: false
                });
                return false;
            }
            
            if (!answer || answer === '<p><br></p>') {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'تنبيه',
                    text: 'الرجاء إدخال الإجابة',
                    timer: 2000,
                    showConfirmButton: false
                });
                return false;
            }
        });

        // إعادة تعيين النموذج عند إغلاق المودال
        $('#faqModal').on('hidden.bs.modal', function() {
            $('#faqForm')[0].reset();
            $('#answer').summernote('code', '');
            $('.is-invalid').removeClass('is-invalid');
        });
    </script>
@endsection