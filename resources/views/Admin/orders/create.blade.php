@extends('Admin.layout.master')

@section('title', 'إضافة طلب جديد')

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

        .order-create-card {
            background: var(--dark-card);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .order-create-header {
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .form-section {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .form-section h6 {
            color: var(--primary-color);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
        }

        .required::after {
            content: " *";
            color: var(--danger-color);
        }

        .alert-guide {
            background: rgba(12, 99, 228, 0.1);
            border-right: 4px solid var(--primary-color);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            border: 1px solid rgba(12, 99, 228, 0.2);
        }

        .form-control, .form-select {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1);
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

        .form-label {
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
        }

        .input-group-text {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.1);
            color: #fff;
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

        .btn-success {
            background: #20c997;
            border: none;
        }

        .btn-success:hover {
            background: #1ba87e;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .help-text {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.6);
            margin-top: 5px;
        }

        @media (max-width: 768px) {
            .order-create-card {
                padding: 20px;
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
                    <a href="{{ route('admin.index') }}">الرئيسية</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.orders.index') }}">الطلبات</a>
                </li>
                <li class="breadcrumb-item active">إضافة طلب جديد</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-12">
                <div class="order-create-card">
                    <div class="order-create-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1">إضافة طلب جديد</h5>
                                <small class="text-muted">ملء البيانات الأساسية لإنشاء طلب توصيل</small>
                            </div>
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-right me-2"></i>رجوع للقائمة
                            </a>
                        </div>
                    </div>

                    {{-- إرشادات --}}
                    <div class="alert-guide">
                        <h6><i class="fas fa-lightbulb me-2"></i>إرشادات سريعة</h6>
                        <ul>
                            <li>اختيار العميل إلزامي وسيتم تحميل عناوينه المحفوظة تلقائياً</li>
                            <li>يمكنك إنشاء الطلب بدون سائق وسيتم تعيينه لاحقاً</li>
                            <li>حالة الدفع الأولية ستكون "قيد الانتظار"</li>
                            <li>بعد الإنشاء يمكنك تعديل التفاصيل وإدارة العروض</li>
                        </ul>
                    </div>

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

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

                    <form action="{{ route('admin.orders.store') }}" method="POST" id="createOrderForm">
                        @csrf

                        <div class="row">
                            <div class="col-lg-8">
                                {{-- معلومات العميل --}}
                                <div class="form-section">
                                    <h6><i class="fas fa-user me-2"></i>معلومات العميل</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="user_id" class="form-label required">العميل</label>
                                            <select class="form-select select2" id="user_id" name="user_id" required>
                                                <option value="">-- اختر عميل --</option>
                                                @foreach($users as $user)
                                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                                        {{ $user->name }} - {{ $user->phone ?? 'بدون هاتف' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="saved_location_id" class="form-label">العنوان المحفوظ</label>
                                            <select class="form-select select2" id="saved_location_id" name="saved_location_id">
                                                <option value="">-- اختر عنوان --</option>
                                                {{-- سيتم تحميل العناوين عبر AJAX عند اختيار العميل --}}
                                            </select>
                                            <div class="help-text">اختر العميل أولاً لتحميل عناوينه المحفوظة</div>
                                        </div>
                                    </div>
                                </div>

                                {{-- تفاصيل الخدمة --}}
                                <div class="form-section">
                                    <h6><i class="fas fa-cogs me-2"></i>تفاصيل الخدمة</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="service_id" class="form-label required">الخدمة</label>
                                            <select class="form-select select2" id="service_id" name="service_id" required>
                                                <option value="">-- اختر خدمة --</option>
                                                @foreach($services as $service)
                                                    <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                                        {{ $service->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="water_type_id" class="form-label required">نوع المياه</label>
                                            <select class="form-select select2" id="water_type_id" name="water_type_id" required>
                                                <option value="">-- اختر نوع المياه --</option>
                                                @foreach($waterTypes as $type)
                                                    <option value="{{ $type->id }}" {{ old('water_type_id') == $type->id ? 'selected' : '' }}>
                                                        {{ $type->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="order_status_id" class="form-label required">حالة الطلب</label>
                                            <select class="form-select select2" id="order_status_id" name="order_status_id" required>
                                                <option value="">-- اختر حالة --</option>
                                                @foreach($orderStatuses as $status)
                                                    <option value="{{ $status->id }}" {{ old('order_status_id') == $status->id ? 'selected' : '' }}>
                                                        {{ $status->label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="order_date" class="form-label required">تاريخ الطلب</label>
                                            <input type="datetime-local" class="form-control" id="order_date"
                                                name="order_date"
                                                value="{{ old('order_date', now()->format('Y-m-d\TH:i')) }}" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                {{-- معلومات الدفع --}}
                                <div class="summary-card mb-4">
                                    <h6 class="mb-3">معلومات الدفع</h6>
                                    <div class="mb-3">
                                        <label for="payment_method" class="form-label required">طريقة الدفع</label>
                                        <select class="form-select" id="payment_method" name="payment_method" required>
                                            <option value="wallet" {{ old('payment_method') == 'wallet' ? 'selected' : '' }}>محفظة</option>
                                            <option value="credit_card" {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>بطاقة ائتمان</option>
                                            <option value="mada" {{ old('payment_method') == 'mada' ? 'selected' : '' }}>مدى</option>
                                            <option value="apple_pay" {{ old('payment_method') == 'apple_pay' ? 'selected' : '' }}>Apple Pay</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="payment_gateway" class="form-label">بوابة الدفع</label>
                                        <select class="form-select" id="payment_gateway" name="payment_gateway">
                                            <option value="">-- اختر --</option>
                                            <option value="wallet" {{ old('payment_gateway') == 'wallet' ? 'selected' : '' }}>محفظة</option>
                                            <option value="paymob" {{ old('payment_gateway') == 'paymob' ? 'selected' : '' }}>Paymob</option>
                                            <option value="tamara" {{ old('payment_gateway') == 'tamara' ? 'selected' : '' }}>Tamara</option>
                                            <option value="tabby" {{ old('payment_gateway') == 'tabby' ? 'selected' : '' }}>Tabby</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- ملاحظات --}}
                                <div class="summary-card">
                                    <h6 class="mb-3">ملاحظات إضافية</h6>
                                    <div class="mb-3">
                                        <textarea class="form-control" id="notes" name="notes" rows="4" placeholder="أي ملاحظات تود إضافتها...">{{ old('notes') }}</textarea>
                                    </div>
                                </div>

                                {{-- أزرار --}}
                                <div class="mt-4 d-grid gap-2">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="fas fa-plus-circle me-2"></i>إنشاء الطلب
                                    </button>
                                    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-lg">
                                        <i class="fas fa-times me-2"></i>إلغاء
                                    </a>
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                theme: 'default',
                width: '100%',
                dropdownParent: $('body')
            });

            // تحميل عناوين العميل عند تغيير المستخدم
            $('#user_id').on('change', function() {
                const userId = $(this).val();
                const $locationSelect = $('#saved_location_id');
                $locationSelect.empty().append('<option value="">-- اختر عنوان --</option>');

                if (userId) {
                    $.ajax({
                        url: "{{ route('admin.users.locations', '') }}/" + userId,
                        type: 'GET',
                        success: function(response) {
                            if (response.locations && response.locations.length > 0) {
                                response.locations.forEach(function(location) {
                                    $locationSelect.append(
                                        `<option value="${location.id}">${location.label} - ${location.address_details}</option>`
                                    );
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ',
                                text: 'فشل تحميل العناوين المحفوظة'
                            });
                        }
                    });
                }
            });

            // إذا كان هناك عميل مختار مسبقاً (في حالة old input)
            @if(old('user_id'))
                $('#user_id').trigger('change');
            @endif
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
    </script>
@endsection