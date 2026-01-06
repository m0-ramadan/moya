@extends('Admin.layout.master')

@section('title', 'تعديل كوبون')

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    body {
        font-family: "Cairo", sans-serif !important;
    }
    
    .coupon-preview {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        padding: 30px;
        text-align: center;
        margin-bottom: 20px;
    }
    
    .coupon-preview-code {
        font-size: 32px;
        font-weight: bold;
        letter-spacing: 3px;
        font-family: 'Courier New', monospace;
        margin-bottom: 10px;
    }
    
    .coupon-preview-value {
        font-size: 24px;
        margin-bottom: 5px;
    }
    
    .coupon-preview-dates {
        font-size: 14px;
        opacity: 0.9;
    }
    
    .stats-card {
        background: white;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 15px;
        border: 1px solid #e9ecef;
        text-align: center;
    }
    
    .stats-number {
        font-size: 24px;
        font-weight: bold;
        color: #696cff;
        margin-bottom: 5px;
    }
    
    .stats-label {
        color: #7f8c8d;
        font-size: 14px;
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
                <a href="{{ route('admin.coupons.index') }}">الكوبونات</a>
            </li>
            <li class="breadcrumb-item active">تعديل كوبون</li>
        </ol>
    </nav>

    <div class="row" bis_skin_checked="1">
        <div class="col-md-8">
            <div class="card mb-4" bis_skin_checked="1">
                <div class="card-header d-flex justify-content-between align-items-center" bis_skin_checked="1">
                    <h5 class="mb-0">تعديل بيانات الكوبون</h5>
                    <div class="d-flex gap-2" bis_skin_checked="1">
                        <a href="{{ route('admin.coupons.show', $coupon) }}" class="btn btn-sm btn-outline-info">
                            <i class="fas fa-eye me-1"></i> عرض
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="duplicateCoupon()">
                            <i class="fas fa-copy me-1"></i> نسخ
                        </button>
                    </div>
                </div>
                <div class="card-body" bis_skin_checked="1">
                    <form id="couponForm" method="POST" action="{{ route('admin.coupons.update', $coupon) }}">
                        @csrf
                        @method('PUT')

                        <div class="row" bis_skin_checked="1">
                            <!-- الكود -->
                            <div class="col-md-6 mb-3" bis_skin_checked="1">
                                <label class="form-label required" for="code">كود الكوبون</label>
                                <div class="input-group" bis_skin_checked="1">
                                    <input type="text" class="form-control" id="code" name="code" 
                                           value="{{ old('code', $coupon->code) }}" required>
                                    <button class="btn btn-outline-secondary" type="button" id="generateCode">
                                        <i class="fas fa-random"></i>
                                    </button>
                                </div>
                                <div id="codeFeedback" class="invalid-feedback"></div>
                            </div>

                            <!-- الاسم -->
                            <div class="col-md-6 mb-3" bis_skin_checked="1">
                                <label class="form-label required" for="name">اسم الكوبون</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="{{ old('name', $coupon->name) }}" required>
                            </div>
                        </div>

                        <!-- الوصف -->
                        <div class="mb-3" bis_skin_checked="1">
                            <label class="form-label" for="description">وصف الكوبون</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $coupon->description) }}</textarea>
                        </div>

                        <div class="row" bis_skin_checked="1">
                            <!-- النوع -->
                            <div class="col-md-6 mb-3" bis_skin_checked="1">
                                <label class="form-label required">نوع الكوبون</label>
                                <div class="btn-group w-100" role="group" aria-label="نوع الكوبون" bis_skin_checked="1">
                                    <input type="radio" class="btn-check" name="type" id="type_percentage" 
                                           value="percentage" {{ old('type', $coupon->type) == 'percentage' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="type_percentage">
                                        <i class="fas fa-percentage"></i> نسبة مئوية
                                    </label>
                                    
                                    <input type="radio" class="btn-check" name="type" id="type_fixed" 
                                           value="fixed" {{ old('type', $coupon->type) == 'fixed' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="type_fixed">
                                        <i class="fas fa-money-bill-wave"></i> مبلغ ثابت
                                    </label>
                                </div>
                            </div>

                            <!-- القيمة -->
                            <div class="col-md-6 mb-3" bis_skin_checked="1">
                                <label class="form-label required" for="value">قيمة الخصم</label>
                                <div class="input-group" bis_skin_checked="1">
                                    <input type="number" class="form-control" id="value" name="value" 
                                           value="{{ old('value', $coupon->value) }}" step="0.01" min="0" required>
                                    <span class="input-group-text" id="valueSuffix">
                                        {{ $coupon->type == 'percentage' ? '%' : 'ج.م' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- الحد الأدنى للطلب -->
                        <div class="mb-3" bis_skin_checked="1">
                            <label class="form-label" for="min_order_amount">الحد الأدنى لقيمة الطلب</label>
                            <div class="input-group" bis_skin_checked="1">
                                <input type="number" class="form-control" id="min_order_amount" 
                                       name="min_order_amount" value="{{ old('min_order_amount', $coupon->min_order_amount) }}" 
                                       step="0.01" min="0">
                                <span class="input-group-text">ج.م</span>
                            </div>
                        </div>

                        <div class="row" bis_skin_checked="1">
                            <!-- الحد الأقصى للاستخدام -->
                            <div class="col-md-6 mb-3" bis_skin_checked="1">
                                <label class="form-label" for="max_uses">الحد الأقصى للاستخدام</label>
                                <input type="number" class="form-control" id="max_uses" name="max_uses" 
                                       value="{{ old('max_uses', $coupon->max_uses) }}" min="1">
                                <div class="form-text">تم استخدام {{ $coupon->usages()->count() }} من {{ $coupon->max_uses ?? '∞' }}</div>
                            </div>

                            <!-- الحد الأقصى لكل مستخدم -->
                            <div class="col-md-6 mb-3" bis_skin_checked="1">
                                <label class="form-label" for="max_uses_per_user">الحد الأقصى لكل مستخدم</label>
                                <input type="number" class="form-control" id="max_uses_per_user" 
                                       name="max_uses_per_user" value="{{ old('max_uses_per_user', $coupon->max_uses_per_user) }}" 
                                       min="1">
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- التواريخ -->
                        <div class="row" bis_skin_checked="1">
                            <div class="col-md-6 mb-3" bis_skin_checked="1">
                                <label class="form-label" for="starts_at">تاريخ البدء</label>
                                <input type="datetime-local" class="form-control" id="starts_at" 
                                       name="starts_at" value="{{ old('starts_at', $coupon->starts_at ? $coupon->starts_at->format('Y-m-d\TH:i') : '') }}">
                            </div>

                            <div class="col-md-6 mb-3" bis_skin_checked="1">
                                <label class="form-label" for="expires_at">تاريخ الانتهاء</label>
                                <input type="datetime-local" class="form-control" id="expires_at" 
                                       name="expires_at" value="{{ old('expires_at', $coupon->expires_at ? $coupon->expires_at->format('Y-m-d\TH:i') : '') }}">
                            </div>
                        </div>

                        <!-- الحالة -->
                        <div class="mb-3" bis_skin_checked="1">
                            <div class="form-check form-switch" bis_skin_checked="1">
                                <input class="form-check-input" type="checkbox" id="is_active" 
                                       name="is_active" value="1" {{ old('is_active', $coupon->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">تفعيل الكوبون</label>
                            </div>
                        </div>

                        <div class="mt-4" bis_skin_checked="1">
                            <button type="submit" class="btn btn-primary me-3">
                                <i class="fas fa-save me-1"></i> حفظ التغييرات
                            </button>
                            <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-right me-1"></i> العودة
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Preview and Stats Section -->
        <div class="col-md-4">
            <!-- Preview -->
            <div class="card mb-4" bis_skin_checked="1">
                <div class="card-header" bis_skin_checked="1">
                    <h5 class="mb-0">معاينة الكوبون</h5>
                </div>
                <div class="card-body" bis_skin_checked="1">
                    <div class="coupon-preview" id="couponPreview">
                        <div class="coupon-preview-code" id="previewCode">{{ $coupon->code }}</div>
                        <div class="coupon-preview-value" id="previewValue">
                            {{ number_format($coupon->value, $coupon->type == 'percentage' ? 0 : 2) }}
                            {{ $coupon->type == 'percentage' ? '%' : 'ج.م' }}
                        </div>
                        <div class="coupon-preview-dates" id="previewDates">
                            <div>يبدأ: {{ $coupon->starts_at ? $coupon->starts_at->format('Y/m/d') : 'فوراً' }}</div>
                            <div>ينتهي: {{ $coupon->expires_at ? $coupon->expires_at->format('Y/m/d') : 'لا نهائي' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="card" bis_skin_checked="1">
                <div class="card-header" bis_skin_checked="1">
                    <h5 class="mb-0">إحصائيات الاستخدام</h5>
                </div>
                <div class="card-body" bis_skin_checked="1">
                    <div class="row" bis_skin_checked="1">
                        <div class="col-6 mb-3" bis_skin_checked="1">
                            <div class="stats-card" bis_skin_checked="1">
                                <div class="stats-number" bis_skin_checked="1">{{ $coupon->usages()->count() }}</div>
                                <div class="stats-label" bis_skin_checked="1">إجمالي الاستخدامات</div>
                            </div>
                        </div>
                        <div class="col-6 mb-3" bis_skin_checked="1">
                            <div class="stats-card" bis_skin_checked="1">
                                <div class="stats-number" bis_skin_checked="1">
                                    @if($coupon->max_uses)
                                        {{ round(($coupon->usages()->count() / $coupon->max_uses) * 100) }}%
                                    @else
                                        ∞
                                    @endif
                                </div>
                                <div class="stats-label" bis_skin_checked="1">نسبة الاستخدام</div>
                            </div>
                        </div>
                    </div>

                    @if($coupon->max_uses && $coupon->usages()->count() >= $coupon->max_uses)
                        <div class="alert alert-warning" bis_skin_checked="1">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            تم الوصول للحد الأقصى للاستخدام
                        </div>
                    @endif

                    @if($coupon->expires_at && $coupon->expires_at->lt(now()))
                        <div class="alert alert-danger" bis_skin_checked="1">
                            <i class="fas fa-calendar-times me-2"></i>
                            الكوبون منتهي الصلاحية
                        </div>
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
        // Update preview on input change
        $('#code').on('input', function() {
            $('#previewCode').text($(this).val());
        });

        $('#value').on('input', function() {
            updateValuePreview();
        });

        $('input[name="type"]').change(function() {
            const type = $(this).val();
            updateValueSuffix(type);
            updateValuePreview();
        });

        $('#starts_at, #expires_at').on('input', function() {
            updateDatesPreview();
        });

        // Generate random code
        $('#generateCode').click(function() {
            $.ajax({
                url: '{{ route("admin.coupons.generate-code") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        $('#code').val(response.code);
                        $('#previewCode').text(response.code);
                        validateCode(response.code);
                    }
                }
            });
        });

        // Validate code
        $('#code').on('blur', function() {
            const code = $(this).val().trim();
            if (code) {
                validateCode(code, {{ $coupon->id }});
            }
        });

        function validateCode(code, except = null) {
            $.ajax({
                url: '{{ route("admin.coupons.validate-code") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    code: code,
                    except: except
                },
                success: function(response) {
                    if (response.valid) {
                        $('#code').removeClass('is-invalid').addClass('is-valid');
                        $('#codeFeedback').text('').addClass('valid-feedback');
                        $('#codeFeedback').text('الكود متاح');
                    } else {
                        $('#code').removeClass('is-valid').addClass('is-invalid');
                        $('#codeFeedback').text(response.message);
                    }
                }
            });
        }

        function updateValueSuffix(type) {
            const suffix = type === 'percentage' ? '%' : 'ج.م';
            $('#valueSuffix').text(suffix);
        }

        function updateValuePreview() {
            const type = $('input[name="type"]:checked').val();
            const value = $('#value').val() || 0;
            let preview = value;
            
            if (type === 'percentage') {
                preview = value + '%';
            } else {
                preview = numberFormat(value) + ' ج.م';
            }
            
            $('#previewValue').text(preview);
        }

        function updateDatesPreview() {
            const startsAt = $('#starts_at').val();
            const expiresAt = $('#expires_at').val();
            
            let startsText = 'يبدأ: فوراً';
            let expiresText = 'ينتهي: لا نهائي';
            
            if (startsAt) {
                const startsDate = new Date(startsAt);
                startsText = 'يبدأ: ' + formatDate(startsDate);
            }
            
            if (expiresAt) {
                const expiresDate = new Date(expiresAt);
                expiresText = 'ينتهي: ' + formatDate(expiresDate);
            }
            
            $('#previewDates').html(`
                <div>${startsText}</div>
                <div>${expiresText}</div>
            `);
        }

        function formatDate(date) {
            return date.toLocaleDateString('ar-EG', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }

        function numberFormat(number) {
            return new Intl.NumberFormat('ar-EG', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(number);
        }

        // Duplicate coupon
        window.duplicateCoupon = function() {
            Swal.fire({
                title: 'نسخ الكوبون',
                input: 'text',
                inputLabel: 'أدخل اسم للكوبون المنسوخ:',
                inputValue: '{{ $coupon->name }} - نسخة',
                showCancelButton: true,
                confirmButtonText: 'نسخ',
                cancelButtonText: 'إلغاء',
                preConfirm: (name) => {
                    if (!name) {
                        Swal.showValidationMessage('يجب إدخال اسم للكوبون');
                        return false;
                    }
                    return name;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("admin.coupons.duplicate", $coupon) }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            name: result.value
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    title: 'تم النسخ!',
                                    text: 'تم نسخ الكوبون بنجاح',
                                    icon: 'success',
                                    confirmButtonText: 'حسناً'
                                }).then(() => {
                                    window.location.href = '/admin/coupons/' + response.data.id + '/edit';
                                });
                            }
                        }
                    });
                }
            });
        }

        // Validate form
        $('#couponForm').on('submit', function(e) {
            const code = $('#code').val().trim();
            
            if (!code) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ!',
                    text: 'الرجاء إدخال كود الكوبون'
                });
            }
        });
    });
</script>
@endsection