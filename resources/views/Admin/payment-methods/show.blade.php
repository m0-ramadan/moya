@extends('Admin.layout.master')

@section('title', 'تفاصيل وسيلة الدفع: ' . $paymentMethod->name)

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: "Cairo", sans-serif !important;
        }

        .detail-card {
            /* background: white; */
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .detail-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px 30px;
            position: relative;
        }

        .detail-icon {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            margin-bottom: 20px;
        }

        .detail-body {
            padding: 30px;
        }

        .info-section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .info-section h6 {
            color: #696cff;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f8f9fa;
        }

        .info-row {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .info-label {
            min-width: 150px;
            font-weight: 600;
            color: #495057;
        }

        .info-value {
            color: #2c3e50;
            font-weight: 500;
            flex-grow: 1;
        }

        .badge-custom {
            padding: 6px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 13px;
        }

        .badge-status-active {
            background-color: #d4edda;
            color: #155724;
        }

        .badge-status-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }

        .badge-type-payment {
            background-color: #e7f5ff;
            color: #0c63e4;
        }

        .badge-type-method {
            background-color: #f8f9fa;
            color: #495057;
        }

        .code-block {
            background: #2a3036;
            border-radius: 10px;
            padding: 15px;
            font-family: monospace;
            margin: 5px 0;
        }

        .stats-card {
            /* background: #f8f9fa; */
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
            border-top: 4px solid #696cff;
        }

        .stats-icon {
            font-size: 32px;
            color: #696cff;
            margin-bottom: 15px;
        }

        .stats-number {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stats-label {
            color: #6c757d;
            font-size: 14px;
        }

        .timeline {
            position: relative;
            padding-right: 30px;
        }

        .timeline:before {
            content: '';
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: #696cff;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 25px;
        }

        .timeline-item:before {
            content: '';
            position: absolute;
            right: -33px;
            top: 5px;
            width: 13px;
            height: 13px;
            border-radius: 50%;
            background: white;
            border: 3px solid #696cff;
        }

        .timeline-content {
            /* background: #f8f9fa; */
            border-radius: 10px;
            padding: 15px;
        }

        .timeline-date {
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 5px;
        }

        .timeline-text {
            color: #2c3e50;
            font-weight: 500;
        }

        .action-buttons {
            position: absolute;
            left: 30px;
            top: 30px;
            display: flex;
            gap: 10px;
        }

        .btn-action {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-action:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .usage-example {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            border-right: 4px solid #696cff;
        }

        .usage-example h6 {
            color: #696cff;
            margin-bottom: 15px;
        }

        pre {
            background: #2c3e50;
            color: #ecf0f1;
            padding: 15px;
            border-radius: 8px;
            font-size: 14px;
            overflow-x: auto;
        }

        pre code {
            font-family: 'Courier New', monospace;
        }

        @media (max-width: 768px) {
            .action-buttons {
                position: relative;
                left: 0;
                top: 0;
                margin-bottom: 20px;
                justify-content: center;
            }

            .info-row {
                flex-direction: column;
                align-items: flex-start;
            }

            .info-label {
                min-width: auto;
                margin-bottom: 5px;
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
                <li class="breadcrumb-item active">تفاصيل</li>
            </ol>
        </nav>

        <div class="row" bis_skin_checked="1">
            <div class="col-12" bis_skin_checked="1">
                <div class="detail-card" bis_skin_checked="1">
                    <div class="detail-header" bis_skin_checked="1">
                        <div class="action-buttons" bis_skin_checked="1">
                            <a href="{{ route('admin.payment-methods.edit', $paymentMethod) }}" class="btn-action"
                                title="تعديل">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ route('admin.payment-methods.index') }}" class="btn-action" title="رجوع">
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>

                        <div class="text-center" bis_skin_checked="1">
                            <div class="detail-icon" bis_skin_checked="1">
                                @if ($paymentMethod->icon)
                                    <i class="{{ $paymentMethod->icon }}"></i>
                                @else
                                    <i class="fas fa-credit-card"></i>
                                @endif
                            </div>
                            <h4 class="mb-2">{{ $paymentMethod->name }}</h4>
                            <div class="d-flex justify-content-center align-items-center gap-3" bis_skin_checked="1">
                                <span
                                    class="badge-custom {{ $paymentMethod->is_active ? 'badge-status-active' : 'badge-status-inactive' }}">
                                    {{ $paymentMethod->is_active ? 'نشط' : 'غير نشط' }}
                                </span>
                                <span
                                    class="badge-custom {{ $paymentMethod->is_payment ? 'badge-type-payment' : 'badge-type-method' }}">
                                    {{ $paymentMethod->is_payment ? 'طريقة دفع' : 'طريقة أخرى' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="detail-body" bis_skin_checked="1">
                        <div class="row" bis_skin_checked="1">
                            <div class="col-lg-8" bis_skin_checked="1">
                                <!-- Basic Information -->
                                <div class="info-section" bis_skin_checked="1">
                                    <h6><i class="fas fa-info-circle me-2"></i>المعلومات الأساسية</h6>

                                    <div class="info-row" bis_skin_checked="1">
                                        <div class="info-label" bis_skin_checked="1">اسم وسيلة الدفع:</div>
                                        <div class="info-value" bis_skin_checked="1">{{ $paymentMethod->name }}</div>
                                    </div>

                                    <div class="info-row" bis_skin_checked="1">
                                        <div class="info-label" bis_skin_checked="1">المعرف (Key):</div>
                                        <div class="info-value" bis_skin_checked="1">
                                            <div class="code-block" bis_skin_checked="1">{{ $paymentMethod->key }}</div>
                                        </div>
                                    </div>

                                    <div class="info-row" bis_skin_checked="1">
                                        <div class="info-label" bis_skin_checked="1">الأيقونة:</div>
                                        <div class="info-value" bis_skin_checked="1">
                                            @if ($paymentMethod->icon)
                                                <div class="d-flex align-items-center gap-3" bis_skin_checked="1">
                                                    <div class="icon-preview"
                                                        style="width: 40px; height: 40px; font-size: 20px;"
                                                        bis_skin_checked="1">
                                                        <i class="{{ $paymentMethod->icon }}"></i>
                                                    </div>
                                                    <code>{{ $paymentMethod->icon }}</code>
                                                </div>
                                            @else
                                                <span class="text-muted">بدون أيقونة</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="info-row" bis_skin_checked="1">
                                        <div class="info-label" bis_skin_checked="1">معرف قاعدة البيانات:</div>
                                        <div class="info-value" bis_skin_checked="1">#{{ $paymentMethod->id }}</div>
                                    </div>
                                </div>

                                <!-- Settings -->
                                <div class="info-section" bis_skin_checked="1">
                                    <h6><i class="fas fa-cog me-2"></i>الإعدادات</h6>

                                    <div class="info-row" bis_skin_checked="1">
                                        <div class="info-label" bis_skin_checked="1">الحالة:</div>
                                        <div class="info-value" bis_skin_checked="1">
                                            @if ($paymentMethod->is_active)
                                                <span class="badge-custom badge-status-active">
                                                    <i class="fas fa-check-circle me-1"></i>نشط
                                                </span>
                                                <small class="text-muted ms-2">(ستظهر للعملاء)</small>
                                            @else
                                                <span class="badge-custom badge-status-inactive">
                                                    <i class="fas fa-times-circle me-1"></i>غير نشط
                                                </span>
                                                <small class="text-muted ms-2">(لن تظهر للعملاء)</small>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="info-row" bis_skin_checked="1">
                                        <div class="info-label" bis_skin_checked="1">النوع:</div>
                                        <div class="info-value" bis_skin_checked="1">
                                            @if ($paymentMethod->is_payment)
                                                <span class="badge-custom badge-type-payment">
                                                    <i class="fas fa-money-bill-wave me-1"></i>طريقة دفع
                                                </span>
                                                <small class="text-muted ms-2">(وسيلة دفع حقيقية)</small>
                                            @else
                                                <span class="badge-custom badge-type-method">
                                                    <i class="fas fa-cog me-1"></i>طريقة أخرى
                                                </span>
                                                <small class="text-muted ms-2">(مثل التحويل البنكي)</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Usage Example -->
                                <div class="usage-example" bis_skin_checked="1">
                                    <h6><i class="fas fa-code me-2"></i>مثال للاستخدام في الكود</h6>
                                    <pre><code>// التحقق من وسيلة الدفع
if ($paymentMethod->is_active) {
    echo "وسيلة الدفع '{{ $paymentMethod->name }}' متاحة";
}

// استخدام المعرف (Key)
$method = PaymentMethod::where('key', '{{ $paymentMethod->key }}')->first();

// عرض الأيقونة
&lt;i class="{{ $paymentMethod->icon ?: 'fas fa-credit-card' }}"&gt;&lt;/i&gt;</code></pre>
                                </div>
                            </div>

                            <div class="col-lg-4" bis_skin_checked="1">
                                <!-- Statistics -->
                                <div class="stats-card" bis_skin_checked="1">
                                    <div class="stats-icon" bis_skin_checked="1">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <div class="stats-number" bis_skin_checked="1">
                                        {{ $paymentMethod->created_at->diffForHumans() }}
                                    </div>
                                    <div class="stats-label" bis_skin_checked="1">تاريخ الإضافة</div>
                                </div>

                                <div class="stats-card" bis_skin_checked="1">
                                    <div class="stats-icon" bis_skin_checked="1">
                                        <i class="fas fa-history"></i>
                                    </div>
                                    <div class="stats-number" bis_skin_checked="1">
                                        {{ $paymentMethod->updated_at->diffForHumans() }}
                                    </div>
                                    <div class="stats-label" bis_skin_checked="1">آخر تحديث</div>
                                </div>

                                <!-- Timeline -->
                                <div class="info-section" bis_skin_checked="1">
                                    <h6><i class="fas fa-history me-2"></i>السجل الزمني</h6>

                                    <div class="timeline" bis_skin_checked="1">
                                        <div class="timeline-item" bis_skin_checked="1">
                                            <div class="timeline-content" bis_skin_checked="1">
                                                <div class="timeline-date" bis_skin_checked="1">
                                                    {{ $paymentMethod->updated_at->translatedFormat('d M Y - h:i A') }}
                                                </div>
                                                <div class="timeline-text" bis_skin_checked="1">
                                                    آخر تحديث للمعلومات
                                                </div>
                                            </div>
                                        </div>

                                        <div class="timeline-item" bis_skin_checked="1">
                                            <div class="timeline-content" bis_skin_checked="1">
                                                <div class="timeline-date" bis_skin_checked="1">
                                                    {{ $paymentMethod->created_at->translatedFormat('d M Y - h:i A') }}
                                                </div>
                                                <div class="timeline-text" bis_skin_checked="1">
                                                    إضافة وسيلة الدفع إلى النظام
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Quick Actions -->
                                <div class="info-section" bis_skin_checked="1">
                                    <h6><i class="fas fa-bolt me-2"></i>إجراءات سريعة</h6>

                                    <div class="d-grid gap-2" bis_skin_checked="1">
                                        <a href="{{ route('admin.payment-methods.edit', $paymentMethod) }}"
                                            class="btn btn-warning">
                                            <i class="fas fa-edit me-2"></i>تعديل وسيلة الدفع
                                        </a>

                                        <form action="{{ route('admin.payment-methods.toggle-status', $paymentMethod) }}"
                                            method="POST" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="btn btn-{{ $paymentMethod->is_active ? 'secondary' : 'success' }} w-100">
                                                <i class="fas fa-power-off me-2"></i>
                                                {{ $paymentMethod->is_active ? 'تعطيل' : 'تفعيل' }}
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.payment-methods.destroy', $paymentMethod) }}"
                                            method="POST" id="deleteForm" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-danger w-100"
                                                onclick="confirmDelete()">
                                                <i class="fas fa-trash me-2"></i>حذف وسيلة الدفع
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete() {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: 'سيتم حذف وسيلة الدفع "{{ $paymentMethod->name }}" نهائياً',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('deleteForm').submit();
                }
            });
        }

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
    </script>
@endsection
