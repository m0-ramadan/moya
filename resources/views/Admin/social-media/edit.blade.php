@extends('Admin.layout.master')

@section('title', 'تعديل إعداد التواصل')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .social-preview-card {
            background: #26253d;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            margin-bottom: 30px;
            border: 2px dashed var(--bs-border-color);
        }

        .social-preview-icon {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            margin: 0 auto 20px;
            color: white;
            background: linear-gradient(135deg, #696cff, #5a5fcf);
        }

        .social-preview-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--bs-heading-color);
        }

        .social-preview-value {
            font-size: 16px;
            color: var(--bs-secondary-color);
            word-break: break-word;
        }

        .form-section {
            background:#26253d;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--bs-border-color);
        }

        .icon-options {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
            gap: 10px;
            margin-top: 15px;
        }

        .icon-option {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 15px 10px;
            border-radius: 8px;
            border: 2px solid transparent;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #26253d;
        }

        .icon-option:hover {
            border-color: #696cff;
            transform: translateY(-2px);
        }

        .icon-option.selected {
            border-color: #696cff;
            background: rgba(105, 108, 255, 0.1);
        }

        .icon-option i {
            font-size: 20px;
            margin-bottom: 8px;
            color: var(--bs-heading-color);
        }

        .icon-option span {
            font-size: 12px;
            color: var(--bs-secondary-color);
        }

        .help-text {
            background:#26253d;
            border-right: 4px solid #696cff;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }

        .help-text ul {
            margin-bottom: 0;
            padding-left: 20px;
        }

        .help-text li {
            margin-bottom: 5px;
            font-size: 14px;
        }

        .preview-link {
            display: inline-block;
            padding: 8px 15px;
            background: #26253d;
            border-radius: 5px;
            margin-top: 10px;
            text-decoration: none;
            color: var(--bs-body-color);
            border: 1px solid var(--bs-border-color);
        }

        .preview-link:hover {
            background: #26253d;
            color: #696cff;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                     <a href="{{ route('admin.home') }}">الرئيسية</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.social-media.index') }}">إعدادات التواصل</a>
                </li>
                <li class="breadcrumb-item active">تعديل {{ $social->key }}</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">تعديل {{ $social->key }}</h5>
                            <small class="text-muted">تحديث معلومات وسيلة التواصل</small>
                        </div>
                        <a href="{{ route('admin.social-media.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-right me-1"></i> رجوع للقائمة
                        </a>
                    </div>

                    <div class="card-body">
                        <!-- Preview Section -->
                        <div class="social-preview-card">
                            <div class="social-preview-icon">
                                <i id="previewIcon" class="{{ $social->icon }}"></i>
                            </div>
                            <div class="social-preview-title" id="previewKey">{{ $social->key }}</div>
                            <div class="social-preview-value" id="previewValue">
                                @if($social->value)
                                    @if(filter_var($social->value, FILTER_VALIDATE_URL))
                                        <a href="{{ $social->value }}" target="_blank" class="preview-link">
                                            <i class="fas fa-external-link-alt me-1"></i>
                                            {{ Str::limit($social->value, 40) }}
                                        </a>
                                    @elseif(filter_var($social->value, FILTER_VALIDATE_EMAIL))
                                        <a href="mailto:{{ $social->value }}" class="preview-link">
                                            <i class="fas fa-envelope me-1"></i>
                                            {{ $social->value }}
                                        </a>
                                    @elseif(preg_match('/^[0-9+\-\s]+$/', $social->value))
                                        <a href="tel:{{ $social->value }}" class="preview-link">
                                            <i class="fas fa-phone me-1"></i>
                                            {{ $social->value }}
                                        </a>
                                    @else
                                        {{ $social->value }}
                                    @endif
                                @else
                                    <span class="text-muted">غير محدد</span>
                                @endif
                            </div>
                        </div>

                        <form action="{{ route('admin.social-media.update', $social->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Value Field -->
                            <div class="form-section">
                                <label class="form-label fw-bold">القيمة</label>
                                <p class="text-muted mb-3">أدخل الرقم أو الرابط أو البريد الإلكتروني</p>
                                
                                <div class="mb-3">
                                    <input type="text" 
                                           class="form-control @error('value') is-invalid @enderror" 
                                           id="value" 
                                           name="value" 
                                           value="{{ old('value', $social->value) }}"
                                           placeholder="مثال: 01012345678 أو https://facebook.com/username أو email@example.com"
                                           required>
                                    @error('value')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Quick Examples -->
                                <div class="row g-2 mb-3">
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-text">📞</span>
                                            <input type="text" class="form-control" readonly value="+201012345678">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-text">🔗</span>
                                            <input type="text" class="form-control" readonly value="https://facebook.com/username">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-text">📧</span>
                                            <input type="text" class="form-control" readonly value="email@example.com">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Icon Field -->
                            <div class="form-section">
                                <label class="form-label fw-bold">الأيقونة</label>
                                <p class="text-muted mb-3">اختر أيقونة FontAwesome أو أدخل كود الأيقونة</p>
                                
                                <div class="mb-3">
                                    <input type="text" 
                                           class="form-control @error('icon') is-invalid @enderror" 
                                           id="icon" 
                                           name="icon" 
                                           value="{{ old('icon', $social->icon) }}"
                                           placeholder="مثال: fab fa-facebook أو fas fa-phone"
                                           required>
                                    @error('icon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Common Icons -->
                                <div class="mb-3">
                                    <label class="form-label">الأيقونات الشائعة:</label>
                                    <div class="icon-options">
                                        <div class="icon-option" data-icon="fas fa-phone" onclick="selectIcon(this)">
                                            <i class="fas fa-phone"></i>
                                            <span>Phone</span>
                                        </div>
                                        <div class="icon-option" data-icon="fab fa-whatsapp" onclick="selectIcon(this)">
                                            <i class="fab fa-whatsapp"></i>
                                            <span>WhatsApp</span>
                                        </div>
                                        <div class="icon-option" data-icon="fab fa-facebook" onclick="selectIcon(this)">
                                            <i class="fab fa-facebook"></i>
                                            <span>Facebook</span>
                                        </div>
                                        <div class="icon-option" data-icon="fab fa-twitter" onclick="selectIcon(this)">
                                            <i class="fab fa-twitter"></i>
                                            <span>Twitter</span>
                                        </div>
                                        <div class="icon-option" data-icon="fab fa-instagram" onclick="selectIcon(this)">
                                            <i class="fab fa-instagram"></i>
                                            <span>Instagram</span>
                                        </div>
                                        <div class="icon-option" data-icon="fab fa-telegram" onclick="selectIcon(this)">
                                            <i class="fab fa-telegram"></i>
                                            <span>Telegram</span>
                                        </div>
                                        <div class="icon-option" data-icon="fas fa-envelope" onclick="selectIcon(this)">
                                            <i class="fas fa-envelope"></i>
                                            <span>Email</span>
                                        </div>
                                        <div class="icon-option" data-icon="fas fa-map-marker-alt" onclick="selectIcon(this)">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <span>Address</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Help Section -->
                            <div class="help-text">
                                <h6><i class="fas fa-lightbulb me-2"></i>ملاحظات مهمة:</h6>
                                <ul>
                                    <li>استخدم <code>fas fa-</code> للأيقونات العادية و <code>fab fa-</code> للعلامات التجارية</li>
                                    <li>للاتصال الهاتفي: ابدأ بـ + أو 0 متبوعاً برمز الدولة</li>
                                    <li>للبريد الإلكتروني: تأكد من صيغة البريد الصحيحة</li>
                                    <li>لروابط وسائل التواصل: تأكد من أن الرابط كامل يبدأ بـ https://</li>
                                    <li>يمكنك زيارة <a href="https://fontawesome.com/icons" target="_blank">FontAwesome</a> لمشاهدة جميع الأيقونات</li>
                                </ul>
                            </div>

                            <!-- Form Actions -->
                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('admin.social-media.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i> إلغاء
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> حفظ التغييرات
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        // Select icon from options
        function selectIcon(element) {
            const icon = $(element).data('icon');
            $('#icon').val(icon);
            $('#previewIcon').attr('class', icon);
            
            // Remove selected class from all
            $('.icon-option').removeClass('selected');
            // Add selected class to clicked
            $(element).addClass('selected');
            
            updatePreview();
        }

        // Update preview in real-time
        function updatePreview() {
            const value = $('#value').val();
            const previewValue = $('#previewValue');
            
            if (!value) {
                previewValue.html('<span class="text-muted">غير محدد</span>');
                return;
            }
            
            if (value.match(/^https?:\/\//)) {
                previewValue.html(`
                    <a href="${value}" target="_blank" class="preview-link">
                        <i class="fas fa-external-link-alt me-1"></i>
                        ${value.length > 40 ? value.substring(0, 40) + '...' : value}
                    </a>
                `);
            } else if (value.includes('@') && value.includes('.')) {
                previewValue.html(`
                    <a href="mailto:${value}" class="preview-link">
                        <i class="fas fa-envelope me-1"></i>
                        ${value}
                    </a>
                `);
            } else if (value.match(/^[+0-9\s\-()]+$/)) {
                previewValue.html(`
                    <a href="tel:${value}" class="preview-link">
                        <i class="fas fa-phone me-1"></i>
                        ${value}
                    </a>
                `);
            } else {
                previewValue.text(value.length > 50 ? value.substring(0, 50) + '...' : value);
            }
        }

        // Initialize selected icon
        $(document).ready(function() {
            const currentIcon = '{{ $social->icon }}';
            $(`.icon-option[data-icon="${currentIcon}"]`).addClass('selected');
            
            // Update preview on value change
            $('#value').on('input', updatePreview);
            $('#icon').on('input', function() {
                $('#previewIcon').attr('class', $(this).val());
            });

            // Auto-detect value type
            $('#value').on('blur', function() {
                const value = $(this).val().trim();
                
                // Auto-suggest icon based on value
                if (value.includes('facebook.com')) {
                    $('#icon').val('fab fa-facebook');
                    $('#previewIcon').attr('class', 'fab fa-facebook');
                } else if (value.includes('twitter.com')) {
                    $('#icon').val('fab fa-twitter');
                    $('#previewIcon').attr('class', 'fab fa-twitter');
                } else if (value.includes('instagram.com')) {
                    $('#icon').val('fab fa-instagram');
                    $('#previewIcon').attr('class', 'fab fa-instagram');
                } else if (value.includes('whatsapp') || value.match(/^\+?\d+$/)) {
                    $('#icon').val('fab fa-whatsapp');
                    $('#previewIcon').attr('class', 'fab fa-whatsapp');
                } else if (value.includes('@')) {
                    $('#icon').val('fas fa-envelope');
                    $('#previewIcon').attr('class', 'fas fa-envelope');
                }
                
                updatePreview();
            });

            // Form validation
            $('form').on('submit', function(e) {
                const value = $('#value').val().trim();
                const icon = $('#icon').val().trim();
                
                if (!value) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'قيمة مطلوبة',
                        text: 'يرجى إدخال قيمة لوسيلة التواصل',
                        confirmButtonText: 'حسناً'
                    });
                    return;
                }
                
                if (!icon) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'أيقونة مطلوبة',
                        text: 'يرجى إدخال أيقونة لوسيلة التواصل',
                        confirmButtonText: 'حسناً'
                    });
                    return;
                }
                
                // Show loading
                Swal.fire({
                    title: 'جاري الحفظ...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            });
        });
    </script>
@endsection