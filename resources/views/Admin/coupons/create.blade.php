@extends('Admin.layout.master')

@section('title', 'إضافة كوبون جديد')

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
    
    .form-switch .form-check-input {
        width: 3em;
        height: 1.5em;
    }
    
    .required:after {
        content: " *";
        color: #dc3545;
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
            <li class="breadcrumb-item active">إضافة كوبون جديد</li>
        </ol>
    </nav>

    <div class="row" bis_skin_checked="1">
        <div class="col-md-8">
            <div class="card mb-4" bis_skin_checked="1">
                <div class="card-header d-flex justify-content-between align-items-center" bis_skin_checked="1">
                    <h5 class="mb-0">بيانات الكوبون</h5>
                </div>
                <div class="card-body" bis_skin_checked="1">
                    <form id="couponForm" method="POST" action="{{ route('admin.coupons.store') }}">
                        @csrf

                        <div class="row" bis_skin_checked="1">
                            <!-- الكود -->
                            <div class="col-md-6 mb-3" bis_skin_checked="1">
                                <label class="form-label required" for="code">كود الكوبون</label>
                                <div class="input-group" bis_skin_checked="1">
                                    <input type="text" class="form-control" id="code" name="code" 
                                           value="{{ old('code') }}" required
                                           placeholder="مثال: SUMMER2026">
                                    <button class="btn btn-outline-secondary" type="button" id="generateCode">
                                        <i class="fas fa-random"></i>
                                    </button>
                                </div>
                                <div class="form-text">يجب أن يكون الكود فريداً وغير مستخدم من قبل</div>
                                <div id="codeFeedback" class="invalid-feedback"></div>
                            </div>

                            <!-- الاسم -->
                            <div class="col-md-6 mb-3" bis_skin_checked="1">
                                <label class="form-label required" for="name">اسم الكوبون</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="{{ old('name') }}" required
                                       placeholder="مثال: خصم الصيف 2024">
                            </div>
                        </div>

                        <!-- الوصف -->
                        <div class="mb-3" bis_skin_checked="1">
                            <label class="form-label" for="description">وصف الكوبون</label>
                            <textarea class="form-control" id="description" name="description" 
                                      rows="3" placeholder="وصف قصير للكوبون...">{{ old('description') }}</textarea>
                        </div>

                        <div class="row" bis_skin_checked="1">
                            <!-- النوع -->
                            <div class="col-md-6 mb-3" bis_skin_checked="1">
                                <label class="form-label required">نوع الكوبون</label>
                                <div class="btn-group w-100" role="group" aria-label="نوع الكوبون" bis_skin_checked="1">
                                    <input type="radio" class="btn-check" name="type" id="type_percentage" 
                                           value="percentage" {{ old('type', 'percentage') == 'percentage' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="type_percentage">
                                        <i class="fas fa-percentage"></i> نسبة مئوية
                                    </label>
                                    
                                    <input type="radio" class="btn-check" name="type" id="type_fixed" 
                                           value="fixed" {{ old('type') == 'fixed' ? 'checked' : '' }}>
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
                                           value="{{ old('value', 0) }}" step="0.01" min="0" required>
                                    <span class="input-group-text" id="valueSuffix">%</span>
                                </div>
                            </div>
                        </div>

                        <!-- الحد الأدنى للطلب -->
                        <div class="mb-3" bis_skin_checked="1">
                            <label class="form-label" for="min_order_amount">الحد الأدنى لقيمة الطلب</label>
                            <div class="input-group" bis_skin_checked="1">
                                <input type="number" class="form-control" id="min_order_amount" 
                                       name="min_order_amount" value="{{ old('min_order_amount') }}" 
                                       step="0.01" min="0" placeholder="0.00">
                                <span class="input-group-text">ج.م</span>
                            </div>
                            <div class="form-text">اتركه فارغاً إذا كنت لا تريد تحديد حد أدنى</div>
                        </div>

                        <div class="row" bis_skin_checked="1">
                            <!-- الحد الأقصى للاستخدام -->
                            <div class="col-md-6 mb-3" bis_skin_checked="1">
                                <label class="form-label" for="max_uses">الحد الأقصى للاستخدام</label>
                                <input type="number" class="form-control" id="max_uses" name="max_uses" 
                                       value="{{ old('max_uses') }}" min="1" placeholder="لا نهائي">
                                <div class="form-text">عدد مرات الاستخدام الكلي</div>
                            </div>

                            <!-- الحد الأقصى لكل مستخدم -->
                            <div class="col-md-6 mb-3" bis_skin_checked="1">
                                <label class="form-label" for="max_uses_per_user">الحد الأقصى لكل مستخدم</label>
                                <input type="number" class="form-control" id="max_uses_per_user" 
                                       name="max_uses_per_user" value="{{ old('max_uses_per_user') }}" 
                                       min="1" placeholder="لا نهائي">
                                <div class="form-text">عدد مرات الاستخدام لكل عميل</div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- التواريخ -->
                        <div class="row" bis_skin_checked="1">
                            <div class="col-md-6 mb-3" bis_skin_checked="1">
                                <label class="form-label" for="starts_at">تاريخ البدء</label>
                                <input type="datetime-local" class="form-control" id="starts_at" 
                                       name="starts_at" value="{{ old('starts_at') }}">
                                <div class="form-text">اتركه فارغاً ليبدأ فوراً</div>
                            </div>

                            <div class="col-md-6 mb-3" bis_skin_checked="1">
                                <label class="form-label" for="expires_at">تاريخ الانتهاء</label>
                                <input type="datetime-local" class="form-control" id="expires_at" 
                                       name="expires_at" value="{{ old('expires_at') }}">
                                <div class="form-text">اتركه فارغاً ليبقى فعالاً دائماً</div>
                            </div>
                        </div>

                        <!-- الحالة -->
                        <div class="mb-3" bis_skin_checked="1">
                            <div class="form-check form-switch" bis_skin_checked="1">
                                <input class="form-check-input" type="checkbox" id="is_active" 
                                       name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">تفعيل الكوبون</label>
                            </div>
                        </div>

                        <div class="mt-4" bis_skin_checked="1">
                            <button type="submit" class="btn btn-primary me-3">
                                <i class="fas fa-save me-1"></i> حفظ الكوبون
                            </button>
                            <button type="reset" class="btn btn-outline-secondary">
                                <i class="fas fa-redo me-1"></i> إعادة تعيين
                            </button>
                            <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-danger">
                                <i class="fas fa-times me-1"></i> إلغاء
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Preview Section -->
        <div class="col-md-4">
            <div class="card mb-4" bis_skin_checked="1">
                <div class="card-header" bis_skin_checked="1">
                    <h5 class="mb-0">معاينة الكوبون</h5>
                </div>
                <div class="card-body" bis_skin_checked="1">
                    <div class="coupon-preview" id="couponPreview">
                        <div class="coupon-preview-code" id="previewCode">SUMMER2024</div>
                        <div class="coupon-preview-value" id="previewValue">10%</div>
                        <div class="coupon-preview-dates" id="previewDates">
                            <div>يبدأ: فوراً</div>
                            <div>ينتهي: لا نهائي</div>
                        </div>
                    </div>

                    <div class="alert alert-info" bis_skin_checked="1">
                        <h6 class="alert-heading">نصائح للكوبونات:</h6>
                        <ul class="mb-0">
                            <li>استخدم أسماء واضحة تعبر عن الهدف</li>
                            <li>ضع تواريخ انتهاء منطقية</li>
                            <li>حدد القيم المناسبة لنوعك التجاري</li>
                            <li>راقب استخدامات الكوبونات بانتظام</li>
                        </ul>
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
        // Update preview on input change
        $('#code').on('input', function() {
            $('#previewCode').text($(this).val() || 'SUMMER2024');
        });

        $('#value').on('input', function() {
            const type = $('input[name="type"]:checked').val();
            updateValuePreview(type);
        });

        $('input[name="type"]').change(function() {
            const type = $(this).val();
            updateValueSuffix(type);
            updateValuePreview(type);
        });

        $('#starts_at, #expires_at').on('input', function() {
            updateDatesPreview();
        });
const textAds = [];
$('#textAdsContainer input[name^="text_ads"]').each(function() {
    if ($(this).val().trim()) {
        textAds.push($(this).val());
    }
});
$('#summary_text_ads').text(textAds.join(', ') || 'لا يوجد');
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
                        
                        // Validate the generated code
                        validateCode(response.code);
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ!',
                        text: 'حدث خطأ أثناء إنشاء الكود'
                    });
                }
            });
        });

        // Validate code on blur
        $('#code').on('blur', function() {
            const code = $(this).val().trim();
            if (code) {
                validateCode(code);
            }
        });

        function validateCode(code) {
            $.ajax({
                url: '{{ route("admin.coupons.validate-code") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    code: code
                },
                success: function(response) {
                    if (response.valid) {
                        $('#code').removeClass('is-invalid').addClass('is-valid');
                        $('#codeFeedback').text('').removeClass('invalid-feedback').addClass('valid-feedback');
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

        function updateValuePreview(type) {
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

        // Initialize
        const initialType = $('input[name="type"]:checked').val();
        updateValueSuffix(initialType);
        updateValuePreview(initialType);
        updateDatesPreview();

        // Form validation
        $('#couponForm').on('submit', function(e) {
            const code = $('#code').val().trim();
            const value = $('#value').val();
            
            if (!code) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ!',
                    text: 'الرجاء إدخال كود الكوبون'
                });
                return;
            }
            
            if (!value || value <= 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ!',
                    text: 'الرجاء إدخال قيمة صحيحة للخصم'
                });
                return;
            }
            
            const startsAt = $('#starts_at').val();
            const expiresAt = $('#expires_at').val();
            
            if (startsAt && expiresAt && new Date(startsAt) >= new Date(expiresAt)) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ!',
                    text: 'تاريخ الانتهاء يجب أن يكون بعد تاريخ البدء'
                });
            }
        });
    });
</script>
@endsection