@extends('Admin.layout.master')

@section('title', 'تسديد دفعة جديدة - ' . $contract->contract_number)

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #696cff;
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --dark-bg: #1e1e2d;
            --dark-card: #2b3b4c;
        }

        body {
            font-family: "Cairo", sans-serif !important;
            background: var(--dark-bg);
            color: #fff;
        }

        .payment-card {
            background: var(--dark-card);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            overflow: hidden;
        }

        .card-header {
            background: var(--primary-gradient);
            color: white;
            padding: 20px 25px;
            font-weight: 600;
            font-size: 18px;
        }

        .card-header i {
            margin-left: 10px;
        }

        .card-body {
            padding: 30px;
        }

        .contract-info {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .info-row {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            min-width: 150px;
            color: rgba(255, 255, 255, 0.7);
        }

        .info-label i {
            width: 25px;
            color: var(--primary-color);
        }

        .info-value {
            color: #fff;
            font-weight: 500;
        }

        .amount-box {
            background: rgba(105, 108, 255, 0.1);
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin-bottom: 25px;
            border: 1px solid rgba(105, 108, 255, 0.3);
        }

        .amount-title {
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
            margin-bottom: 10px;
        }

        .amount-number {
            font-size: 36px;
            font-weight: 700;
            color: var(--primary-color);
        }

        .amount-remaining {
            font-size: 18px;
            color: #28a745;
            margin-top: 10px;
        }

        .form-section {
            background: rgba(255, 255, 255, 0.02);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .section-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .form-label {
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
            margin-bottom: 8px;
        }

        .form-label .required {
            color: #dc3545;
            margin-right: 3px;
        }

        .form-control, .form-select {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 12px 15px;
            color: #fff;
        }

        .form-control:focus, .form-select:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--primary-color);
            color: #fff;
            box-shadow: 0 0 0 0.25rem rgba(105, 108, 255, 0.25);
        }

        .form-select option {
            background: var(--dark-card);
            color: #fff;
        }

        .input-group-text {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.7);
            border-radius: 8px;
        }

        .btn-action {
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: var(--primary-gradient);
            border: none;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        .file-upload-area {
            border: 2px dashed rgba(105, 108, 255, 0.3);
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .file-upload-area:hover {
            border-color: var(--primary-color);
            background: rgba(105, 108, 255, 0.05);
        }

        .file-upload-area i {
            font-size: 40px;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .file-info {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            padding: 10px 15px;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .validation-error {
            color: #dc3545;
            font-size: 13px;
            margin-top: 5px;
        }

        .alert-warning {
            background: rgba(255, 193, 7, 0.1);
            border: 1px solid rgba(255, 193, 7, 0.3);
            color: #ffc107;
            border-radius: 8px;
            padding: 15px;
        }

        @media (max-width: 768px) {
            .card-body {
                padding: 20px;
            }
            
            .info-row {
                flex-direction: column;
            }
            
            .info-label {
                margin-bottom: 5px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                     <a href="{{ route('admin.home') }}">الرئيسية</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.contracts.index') }}">العقود</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.contracts.show', $contract->id) }}">{{ $contract->contract_number }}</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.contracts.payments', $contract->id) }}">المدفوعات</a>
                </li>
                <li class="breadcrumb-item active">تسديد دفعة جديدة</li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">
                    <i class="fas fa-plus-circle text-primary me-2"></i>
                    تسديد دفعة جديدة
                </h4>
                <p class="text-muted mb-0">العقد: {{ $contract->contract_number }}</p>
            </div>
            <a href="{{ route('admin.contracts.payments', $contract->id) }}" class="btn btn-secondary btn-action">
                <i class="fas fa-arrow-right me-2"></i>
                عودة للمدفوعات
            </a>
        </div>

        <!-- Payment Form -->
        <div class="payment-card">
            <div class="card-header">
                <i class="fas fa-money-bill-wave"></i>
                إدخال بيانات الدفعة
            </div>
            <div class="card-body">
                <!-- Contract Summary -->
                <div class="contract-info">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-row">
                                <span class="info-label">
                                    <i class="fas fa-user"></i>
                                    العميل:
                                </span>
                                <span class="info-value">{{ $contract->user->name ?? 'غير معروف' }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">
                                    <i class="fas fa-phone"></i>
                                    الجوال:
                                </span>
                                <span class="info-value">{{ $contract->user->phone ?? 'غير معروف' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-row">
                                <span class="info-label">
                                    <i class="fas fa-calendar"></i>
                                    مدة العقد:
                                </span>
                                <span class="info-value">
                                    {{ $contract->start_date?->format('Y-m-d') }} إلى {{ $contract->end_date?->format('Y-m-d') }}
                                </span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">
                                    <i class="fas fa-tag"></i>
                                    الحالة:
                                </span>
                                <span class="info-value">
                                    @switch($contract->status)
                                        @case('active')
                                            <span class="badge bg-success">نشط</span>
                                            @break
                                        @case('expired')
                                            <span class="badge bg-secondary">منتهي</span>
                                            @break
                                        @case('pending')
                                            <span class="badge bg-warning">معلق</span>
                                            @break
                                        @case('cancelled')
                                            <span class="badge bg-danger">ملغي</span>
                                            @break
                                    @endswitch
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Amount Box -->
                <div class="amount-box">
                    <div class="amount-title">إجمالي العقد</div>
                    <div class="amount-number">{{ number_format($contract->total_amount, 2) }} ر.س</div>
                    <div class="amount-remaining">
                        <i class="fas fa-clock me-2"></i>
                        المتبقي: {{ number_format($contract->remaining_amount, 2) }} ر.س
                    </div>
                </div>

                <!-- Form -->
                <form action="{{ route('admin.contracts.payments.store', $contract->id) }}" method="POST" enctype="multipart/form-data" id="paymentForm">
                    @csrf

                    <div class="form-section">
                        <div class="section-title">
                            <i class="fas fa-info-circle"></i>
                            معلومات الدفعة
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-money-bill-wave me-2"></i>
                                    المبلغ <span class="required">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" name="amount" id="amount" 
                                           class="form-control @error('amount') is-invalid @enderror" 
                                           value="{{ old('amount') }}" 
                                           step="0.01" min="0.01" 
                                           max="{{ $contract->remaining_amount }}" 
                                           placeholder="أدخل المبلغ" required>
                                    <span class="input-group-text">ر.س</span>
                                </div>
                                @error('amount')
                                    <div class="validation-error">{{ $message }}</div>
                                @enderror
                                <small class="text-muted" id="amountHelp">الحد الأقصى: {{ number_format($contract->remaining_amount, 2) }} ر.س</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-calendar me-2"></i>
                                    تاريخ الدفع <span class="required">*</span>
                                </label>
                                <input type="date" name="payment_date" 
                                       class="form-control @error('payment_date') is-invalid @enderror" 
                                       value="{{ old('payment_date', now()->format('Y-m-d')) }}" required>
                                @error('payment_date')
                                    <div class="validation-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-credit-card me-2"></i>
                                    طريقة الدفع <span class="required">*</span>
                                </label>
                                <select name="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                                    <option value="">اختر طريقة الدفع</option>
                                    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>نقدي</option>
                                    <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>بطاقة ائتمان</option>
                                    <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                                    <option value="wallet" {{ old('payment_method') == 'wallet' ? 'selected' : '' }}>محفظة إلكترونية</option>
                                </select>
                                @error('payment_method')
                                    <div class="validation-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-tag me-2"></i>
                                    الحالة <span class="required">*</span>
                                </label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>مكتملة</option>
                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>معلقة</option>
                                    <option value="failed" {{ old('status') == 'failed' ? 'selected' : '' }}>فاشلة</option>
                                </select>
                                @error('status')
                                    <div class="validation-error">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">عند اختيار "مكتملة" سيتم تحديث رصيد العقد تلقائياً</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="section-title">
                            <i class="fas fa-hashtag"></i>
                            معلومات إضافية
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-hashtag me-2"></i>
                                    رقم العملية
                                </label>
                                <input type="text" name="transaction_id" 
                                       class="form-control @error('transaction_id') is-invalid @enderror" 
                                       value="{{ old('transaction_id') }}" 
                                       placeholder="رقم العملية من البنك">
                                @error('transaction_id')
                                    <div class="validation-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-reference me-2"></i>
                                    رقم المرجع
                                </label>
                                <input type="text" name="reference_number" 
                                       class="form-control @error('reference_number') is-invalid @enderror" 
                                       value="{{ old('reference_number') }}" 
                                       placeholder="رقم مرجعي للدفعة">
                                @error('reference_number')
                                    <div class="validation-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-sticky-note me-2"></i>
                                ملاحظات
                            </label>
                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" 
                                      rows="3" placeholder="ملاحظات إضافية عن الدفعة...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="validation-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="section-title">
                            <i class="fas fa-paperclip"></i>
                            إرفاق إيصال
                        </div>

                        <div class="file-upload-area" onclick="document.getElementById('receipt').click()">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <h6>اضغط لرفع إيصال الدفع</h6>
                            <p class="text-muted small mb-0">PDF, JPG, PNG (الحد الأقصى 5MB)</p>
                        </div>

                        <input type="file" name="receipt" id="receipt" class="d-none" 
                               accept=".pdf,.jpg,.jpeg,.png" onchange="handleFileSelect(this)">

                        <div id="fileInfo" class="file-info" style="display: none;">
                            <div>
                                <i class="fas fa-file-pdf text-danger me-2"></i>
                                <span id="fileName"></span>
                            </div>
                            <button type="button" class="btn btn-sm btn-link text-danger" onclick="removeFile()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        @error('receipt')
                            <div class="validation-error mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>تنبيه:</strong> سيتم تحديث رصيد العقد تلقائياً عند إضافة دفعة بحالة "مكتملة"
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex gap-3 justify-content-center mt-4">
                        <button type="submit" class="btn btn-primary btn-action px-5">
                            <i class="fas fa-save me-2"></i>
                            تسجيل الدفعة
                        </button>
                        <a href="{{ route('admin.contracts.payments', $contract->id) }}" class="btn btn-secondary btn-action px-5">
                            <i class="fas fa-times me-2"></i>
                            إلغاء
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Amount validation
            $('#amount').on('input', function() {
                let amount = parseFloat($(this).val()) || 0;
                let maxAmount = {{ $contract->remaining_amount }};
                
                if (amount > maxAmount) {
                    $(this).addClass('is-invalid');
                    $('#amountHelp').html(`<span class="text-danger">المبلغ يتجاوز المتبقي (${maxAmount.toFixed(2)} ر.س)</span>`);
                } else {
                    $(this).removeClass('is-invalid');
                    $('#amountHelp').text(`الحد الأقصى: ${maxAmount.toFixed(2)} ر.س`);
                }
            });

            // Form validation
            $('#paymentForm').on('submit', function(e) {
                let amount = parseFloat($('#amount').val()) || 0;
                let maxAmount = {{ $contract->remaining_amount }};
                
                if (amount <= 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: 'الرجاء إدخال مبلغ صحيح'
                    });
                    return false;
                }
                
                if (amount > maxAmount) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: 'المبلغ المدفوع لا يمكن أن يكون أكبر من المتبقي'
                    });
                    return false;
                }
                
                return true;
            });

            // Session messages
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'نجاح',
                    text: "{{ session('success') }}",
                    timer: 2000,
                    showConfirmButton: false
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: "{{ session('error') }}",
                    timer: 2000,
                    showConfirmButton: false
                });
            @endif

            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ في البيانات',
                    text: 'يرجى التحقق من المدخلات',
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif
        });

        // File handling
        function handleFileSelect(input) {
            if (input.files && input.files[0]) {
                let file = input.files[0];
                let fileName = file.name;
                let fileSize = (file.size / 1024).toFixed(2);
                
                $('#fileName').text(`${fileName} (${fileSize} KB)`);
                $('#fileInfo').show();
            }
        }

        function removeFile() {
            $('#receipt').val('');
            $('#fileInfo').hide();
        }

        // Unsaved changes warning
        let formChanged = false;
        $('form input, form select, form textarea').on('change', function() {
            formChanged = true;
        });

        window.addEventListener('beforeunload', function(e) {
            if (formChanged) {
                e.preventDefault();
                e.returnValue = '';
            }
        });

        $('form').on('submit', function() {
            formChanged = false;
        });
    </script>
@endsection