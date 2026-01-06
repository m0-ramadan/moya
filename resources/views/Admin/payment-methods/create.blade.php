@extends('Admin.layout.master')

@section('title', 'إضافة وسيلة دفع جديدة')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: "Cairo", sans-serif !important;
        }

        .form-card {
            /* background: white; */
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        .form-header {
            border-bottom: 2px solid #f8f9fa;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .step-card {
            /* background: #f8f9fa; */
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 25px;
            border-left: 4px solid #696cff;
        }

        .step-number {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: #696cff;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-left: 15px;
            float: right;
        }

        .step-title {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
        }

        .step-description {
            color: #6c757d;
            font-size: 14px;
            margin-top: 5px;
        }

        .icon-preview {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            /* background: #f8f9fa; */
            font-size: 28px;
            color: #696cff;
            margin: 10px auto;
            border: 2px dashed #dee2e6;
        }

        .icon-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(70px, 1fr));
            gap: 15px;
            max-height: 200px;
            overflow-y: auto;
            padding: 15px;
            /* background: #f8f9fa; */
            border-radius: 10px;
            margin-top: 10px;
        }

        .icon-item {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            /* background: white; */
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .icon-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-color: #696cff;
        }

        .icon-item.selected {
            background: #696cff;
            color: white;
            border-color: #696cff;
        }

        .icon-item i {
            font-size: 20px;
        }

        .toggle-container {
            display: flex;
            align-items: center;
            gap: 15px;
            background: #426788;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .toggle-switch {
            position: relative;
            width: 60px;
            height: 30px;
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
            height: 22px;
            width: 22px;
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
            transform: translateX(30px);
        }

        .toggle-label {
            font-weight: 500;
            color: #2c3e50;
            flex-grow: 1;
        }

        .toggle-description {
            color: #6c757d;
            font-size: 13px;
            margin-top: 5px;
        }

        .key-generator {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .key-generator input {
            flex-grow: 1;
        }

        .preview-card {
            /* background: #f8f9fa; */
            border-radius: 12px;
            padding: 25px;
            border: 2px dashed #dee2e6;
            text-align: center;
            margin-top: 30px;
        }

        .preview-icon {
            font-size: 48px;
            color: #696cff;
            margin-bottom: 15px;
        }

        .preview-name {
            font-size: 20px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .preview-key {
            /* background: white; */
            padding: 8px 15px;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: 15px;
            font-family: monospace;
        }

        .preview-status {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 15px;
        }

        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }

        .status-active {
            background-color: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }

        .type-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }

        .type-payment {
            background-color: #e7f5ff;
            color: #0c63e4;
        }

        .type-method {
            background-color: #f8f9fa;
            color: #495057;
        }

        .required::after {
            content: " *";
            color: #dc3545;
        }

        .alert-guide {
            background: #e7f7ff;
            border-right: 4px solid #696cff;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
        }

        .alert-guide h6 {
            color: #696cff;
            margin-bottom: 15px;
        }

        .alert-guide ul {
            margin-bottom: 0;
            padding-left: 20px;
        }

        .alert-guide li {
            margin-bottom: 8px;
            font-size: 14px;
        }

        .help-text {
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
        }

        @media (max-width: 768px) {
            .form-card {
                padding: 20px;
            }

            .key-generator {
                flex-direction: column;
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
                    <a href="{{ route('admin.payment-methods.index') }}">وسائل الدفع</a>
                </li>
                <li class="breadcrumb-item active">إضافة جديدة</li>
            </ol>
        </nav>

        <div class="row" bis_skin_checked="1">
            <div class="col-12" bis_skin_checked="1">
                <div class="form-card" bis_skin_checked="1">
                    <div class="form-header" bis_skin_checked="1">
                        <div class="d-flex justify-content-between align-items-center" bis_skin_checked="1">
                            <div bis_skin_checked="1">
                                <h5 class="mb-1">إضافة وسيلة دفع جديدة</h5>
                                <p class="text-muted mb-0">أضف وسيلة دفع جديدة للنظام</p>
                            </div>
                            <a href="{{ route('admin.payment-methods.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                            </a>
                        </div>
                    </div>

                    <div class="alert-guide" bis_skin_checked="1">
                        <h6><i class="fas fa-lightbulb me-2"></i>معلومات مهمة:</h6>
                        <ul>
                            <li>المعرف (Key) يجب أن يكون فريداً ولا يتكرر</li>
                            <li>المعرف سيتم استخدامه في الكود البرمجي</li>
                            <li>يمكنك اختيار أيقونة من القائمة أو كتابة رمز FontAwesome</li>
                            <li>وسائل الدفع النشطة فقط ستظهر للعملاء</li>
                        </ul>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.payment-methods.store') }}" method="POST" id="createForm">
                        @csrf

                        <div class="row" bis_skin_checked="1">
                            <div class="col-lg-8" bis_skin_checked="1">
                                <!-- Step 1: Basic Information -->
                                <div class="step-card" bis_skin_checked="1">
                                    <div class="step-number" bis_skin_checked="1">1</div>
                                    <div bis_skin_checked="1">
                                        <h5 class="step-title">المعلومات الأساسية</h5>
                                        <p class="step-description">أدخل المعلومات الأساسية لوسيلة الدفع</p>
                                    </div>
                                </div>

                                <div class="mb-4" bis_skin_checked="1">
                                    <label for="name" class="form-label required">اسم وسيلة الدفع</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ old('name') }}"
                                        placeholder="مثال: بطاقة ائتمان، PayPal، الدفع عند الاستلام" required>
                                    <div class="help-text" bis_skin_checked="1">الاسم الذي سيظهر للعملاء</div>
                                </div>

                                <div class="mb-4" bis_skin_checked="1">
                                    <label for="key" class="form-label required">المعرف (Key)</label>
                                    <div class="key-generator" bis_skin_checked="1">
                                        <input type="text" class="form-control" id="key" name="key"
                                            value="{{ old('key') }}"
                                            placeholder="مثال: credit-card, paypal, cash-on-delivery" required>
                                        <button type="button" class="btn btn-outline-secondary" onclick="generateKey()">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </div>
                                    <div class="help-text" bis_skin_checked="1">
                                        معرف فريد يستخدم في النظام، سيتم توليده تلقائياً من الاسم
                                    </div>
                                </div>

                                <!-- Step 2: Icon Selection -->
                                <div class="step-card" bis_skin_checked="1">
                                    <div class="step-number" bis_skin_checked="1">2</div>
                                    <div bis_skin_checked="1">
                                        <h5 class="step-title">الأيقونة</h5>
                                        <p class="step-description">اختر أيقونة مناسبة لوسيلة الدفع</p>
                                    </div>
                                </div>

                                <div class="mb-4" bis_skin_checked="1">
                                    <label class="form-label">اختر أيقونة</label>

                                    <div class="mb-3" bis_skin_checked="1">
                                        <div class="icon-preview" id="iconPreview">
                                            <i class="fas fa-credit-card"></i>
                                        </div>
                                        <input type="hidden" id="selected_icon" name="icon" value="fas fa-credit-card">
                                    </div>

                                    <input type="text" class="form-control mb-3" id="icon_input"
                                        placeholder="أو اكتب رمز FontAwesome مثل: fas fa-paypal"
                                        value="fas fa-credit-card" oninput="updateIconPreview(this.value)">

                                    <label class="form-label mt-4">أيقونات مقترحة:</label>
                                    <div class="icon-grid" bis_skin_checked="1">
                                        <div class="icon-item selected" onclick="selectIcon('fas fa-credit-card')">
                                            <i class="fas fa-credit-card"></i>
                                        </div>
                                        <div class="icon-item" onclick="selectIcon('fas fa-money-bill-wave')">
                                            <i class="fas fa-money-bill-wave"></i>
                                        </div>
                                        <div class="icon-item" onclick="selectIcon('fab fa-paypal')">
                                            <i class="fab fa-paypal"></i>
                                        </div>
                                        <div class="icon-item" onclick="selectIcon('fas fa-university')">
                                            <i class="fas fa-university"></i>
                                        </div>
                                        <div class="icon-item" onclick="selectIcon('fas fa-mobile-alt')">
                                            <i class="fas fa-mobile-alt"></i>
                                        </div>
                                        <div class="icon-item" onclick="selectIcon('fas fa-wallet')">
                                            <i class="fas fa-wallet"></i>
                                        </div>
                                        <div class="icon-item" onclick="selectIcon('fas fa-hand-holding-usd')">
                                            <i class="fas fa-hand-holding-usd"></i>
                                        </div>
                                        <div class="icon-item" onclick="selectIcon('fas fa-truck')">
                                            <i class="fas fa-truck"></i>
                                        </div>
                                        <div class="icon-item" onclick="selectIcon('fas fa-qrcode')">
                                            <i class="fas fa-qrcode"></i>
                                        </div>
                                        <div class="icon-item" onclick="selectIcon('fas fa-shield-alt')">
                                            <i class="fas fa-shield-alt"></i>
                                        </div>
                                        <div class="icon-item" onclick="selectIcon('fas fa-globe')">
                                            <i class="fas fa-globe"></i>
                                        </div>
                                        <div class="icon-item" onclick="selectIcon('fas fa-coins')">
                                            <i class="fas fa-coins"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 3: Settings -->
                                <div class="step-card" bis_skin_checked="1">
                                    <div class="step-number" bis_skin_checked="1">3</div>
                                    <div bis_skin_checked="1">
                                        <h5 class="step-title">الإعدادات</h5>
                                        <p class="step-description">تحديد إعدادات وسيلة الدفع</p>
                                    </div>
                                </div>

                                <div class="mb-4" bis_skin_checked="1">
                                    <div class="toggle-container" bis_skin_checked="1">
                                        <label class="toggle-switch">
                                            <input type="checkbox" id="is_active" name="is_active" value="1"
                                                checked>
                                            <span class="toggle-slider"></span>
                                        </label>
                                        <div bis_skin_checked="1">
                                            <div class="toggle-label" bis_skin_checked="1">نشط</div>
                                            <div class="toggle-description" bis_skin_checked="1">
                                                وسائل الدفع النشطة فقط ستظهر للعملاء
                                            </div>
                                        </div>
                                    </div>

                                    <div class="toggle-container" bis_skin_checked="1">
                                        <label class="toggle-switch">
                                            <input type="checkbox" id="is_payment" name="is_payment" value="1"
                                                checked>
                                            <span class="toggle-slider"></span>
                                        </label>
                                        <div bis_skin_checked="1">
                                            <div class="toggle-label" bis_skin_checked="1">طريقة دفع</div>
                                            <div class="toggle-description" bis_skin_checked="1">
                                                إذا كانت طريقة دفع فعلاً أم طريقة أخرى مثل التحويل البنكي
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4" bis_skin_checked="1">
                                <!-- Preview Section -->
                                <div class="preview-card" bis_skin_checked="1">
                                    <h6 class="mb-3">معاينة وسيلة الدفع</h6>

                                    <div class="preview-icon" id="previewIcon">
                                        <i class="fas fa-credit-card"></i>
                                    </div>

                                    <div class="preview-name" id="previewName">اسم وسيلة الدفع</div>

                                    <div class="preview-key" id="previewKey">credit-card</div>

                                    <div class="preview-status" bis_skin_checked="1">
                                        <span class="status-badge status-active" id="previewStatus">نشط</span>
                                        <span class="type-badge type-payment" id="previewType">طريقة دفع</span>
                                    </div>

                                    <p class="text-muted mb-0">
                                        هذه هي معاينة وسيلة الدفع كما ستظهر في النظام
                                    </p>
                                </div>

                                <!-- Quick Actions -->
                                <div class="mt-4" bis_skin_checked="1">
                                    <div class="d-grid gap-2" bis_skin_checked="1">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fas fa-save me-2"></i>حفظ وسيلة الدفع
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                            <i class="fas fa-redo me-2"></i>إعادة تعيين
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
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
            // تحديث المعاينة عند تغيير الاسم
            $('#name').on('input', function() {
                $('#previewName').text($(this).val() || 'اسم وسيلة الدفع');
                if (!$('#key').val()) {
                    generateKey();
                }
            });

            // تحديث المعاينة عند تغيير الـ Key
            $('#key').on('input', function() {
                $('#previewKey').text($(this).val() || 'key');
            });

            // تحديث حالة النشاط
            $('#is_active').on('change', function() {
                const isActive = $(this).is(':checked');
                const badge = $('#previewStatus');
                badge.removeClass('status-active status-inactive');

                if (isActive) {
                    badge.addClass('status-active').text('نشط');
                } else {
                    badge.addClass('status-inactive').text('غير نشط');
                }
            });

            // تحديث نوع وسيلة الدفع
            $('#is_payment').on('change', function() {
                const isPayment = $(this).is(':checked');
                const badge = $('#previewType');
                badge.removeClass('type-payment type-method');

                if (isPayment) {
                    badge.addClass('type-payment').text('طريقة دفع');
                } else {
                    badge.addClass('type-method').text('طريقة أخرى');
                }
            });

            // التحقق من النموذج قبل الإرسال
            $('#createForm').on('submit', function(e) {
                if (!$('#name').val() || !$('#key').val()) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'بيانات ناقصة',
                        text: 'يرجى ملء جميع الحقول المطلوبة',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        });

        function generateKey() {
            const name = $('#name').val();
            if (name) {
                const key = name
                    .toLowerCase()
                    .replace(/[^a-z0-9\u0621-\u064A\u0660-\u0669\u0671-\u06D3]/g, '-')
                    .replace(/-+/g, '-')
                    .replace(/^-|-$/g, '');

                $('#key').val(key);
                $('#previewKey').text(key);
            }
        }

        function selectIcon(iconClass) {
            // إزالة التحديد من جميع الأيقونات
            $('.icon-item').removeClass('selected');

            // إضافة التحديد للأيقونة المختارة
            $(`.icon-item[onclick*="${iconClass}"]`).addClass('selected');

            // تحديث حقل الإدخال والمعاينة
            $('#icon_input').val(iconClass);
            $('#selected_icon').val(iconClass);

            // تحديث المعاينة
            updateIconPreview(iconClass);
        }

        function updateIconPreview(iconClass) {
            if (iconClass) {
                $('#iconPreview').html(`<i class="${iconClass}"></i>`);
                $('#previewIcon').html(`<i class="${iconClass}"></i>`);
                $('#selected_icon').val(iconClass);

                // تحديث التحديد في الأيقونات المقترحة
                $('.icon-item').removeClass('selected');
                $(`.icon-item[onclick*="${iconClass}"]`).addClass('selected');
            }
        }

        function resetForm() {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: 'سيتم مسح جميع البيانات المدخلة',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'نعم، امسح',
                cancelButtonText: 'إلغاء',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // إعادة تعيين النموذج
                    document.getElementById('createForm').reset();

                    // إعادة تعيين الأيقونة
                    selectIcon('fas fa-credit-card');

                    // إعادة تعيين التبديلات
                    $('#is_active').prop('checked', true).trigger('change');
                    $('#is_payment').prop('checked', true).trigger('change');

                    // إعادة تعيين المعاينة
                    $('#previewName').text('اسم وسيلة الدفع');
                    $('#previewKey').text('credit-card');

                    Swal.fire({
                        icon: 'success',
                        title: 'تم الإعادة',
                        text: 'تم إعادة تعيين النموذج بنجاح',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        }
    </script>
@endsection
