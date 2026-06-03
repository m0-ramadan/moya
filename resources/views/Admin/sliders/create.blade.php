@extends('Admin.layout.master')

@section('title', 'إضافة سلايدر جديد')

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

        .form-card {
            background: var(--dark-card);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .form-header {
            background: var(--primary-gradient);
            color: white;
            padding: 25px 30px;
            border-radius: 15px 15px 0 0;
            margin: -30px -30px 30px -30px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            font-weight: 600;
            margin-bottom: 8px;
            color: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-label i {
            width: 20px;
            text-align: center;
            color: var(--primary-color);
        }

        .required-star {
            color: var(--danger-color);
            margin-right: 3px;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            border-radius: 10px;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--primary-color);
            color: #fff;
            box-shadow: 0 0 0 0.25rem rgba(105, 108, 255, 0.25);
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .form-select {
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            border-radius: 10px;
            padding: 12px 15px;
            cursor: pointer;
        }

        .form-select:focus {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: var(--primary-color);
            color: #fff;
            box-shadow: 0 0 0 0.25rem rgba(105, 108, 255, 0.25);
        }

        .form-select option {
            background: var(--dark-card);
            color: #fff;
        }

        .image-upload-container {
            position: relative;
            border: 2px dashed rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            padding: 40px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.02);
        }

        .image-upload-container:hover {
            border-color: var(--primary-color);
            background: rgba(105, 108, 255, 0.05);
        }

        .image-upload-container.has-image {
            border-style: solid;
            border-color: var(--success-color);
            padding: 20px;
        }

        .upload-icon {
            font-size: 48px;
            color: rgba(255, 255, 255, 0.3);
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }

        .image-upload-container:hover .upload-icon {
            color: var(--primary-color);
        }

        .upload-text {
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 10px;
        }

        .upload-hint {
            color: rgba(255, 255, 255, 0.4);
            font-size: 12px;
        }

        .preview-image {
            max-width: 100%;
            max-height: 300px;
            border-radius: 10px;
            object-fit: contain;
        }

        .remove-image-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: var(--danger-color);
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .remove-image-btn:hover {
            background: #c82333;
            transform: scale(1.1);
        }

        .toggle-wrapper {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 55px;
            height: 28px;
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
            background-color: rgba(255, 255, 255, 0.2);
            transition: 0.4s;
            border-radius: 34px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: 0.4s;
            border-radius: 50%;
        }

        input:checked + .toggle-slider {
            background: var(--primary-gradient);
        }

        input:checked + .toggle-slider:before {
            transform: translateX(27px);
        }

        .toggle-label {
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
        }

        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4a9a 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(105, 108, 255, 0.4);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.3);
            color: #fff;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .error-feedback {
            color: var(--danger-color);
            font-size: 13px;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .is-invalid {
            border-color: var(--danger-color) !important;
        }

        .type-selector {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .type-option {
            position: relative;
        }

        .type-option input[type="radio"] {
            display: none;
        }

        .type-option label {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }

        .type-option label i {
            font-size: 30px;
            color: rgba(255, 255, 255, 0.5);
            transition: all 0.3s ease;
        }

        .type-option label span {
            font-weight: 600;
            color: rgba(255, 255, 255, 0.7);
        }

        .type-option input[type="radio"]:checked + label {
            border-color: var(--primary-color);
            background: rgba(105, 108, 255, 0.1);
        }

        .type-option input[type="radio"]:checked + label i {
            color: var(--primary-color);
        }

        .type-option input[type="radio"]:checked + label span {
            color: #fff;
        }

        .type-option label:hover {
            border-color: rgba(105, 108, 255, 0.5);
            background: rgba(105, 108, 255, 0.05);
        }

        @media (max-width: 768px) {
            .type-selector {
                grid-template-columns: 1fr;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn-primary,
            .btn-secondary {
                width: 100%;
            }
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
                    <a href="{{ route('admin.banners.index') }}">السلايدرات</a>
                </li>
                <li class="breadcrumb-item active">إضافة سلايدر جديد</li>
            </ol>
        </nav>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="form-card">
                    <div class="form-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0">
                                    <i class="fas fa-plus-circle me-2"></i>
                                    إضافة سلايدر جديد
                                </h5>
                                <small class="opacity-75">أدخل بيانات السلايدر الجديد</small>
                            </div>
                            <a href="{{ route('admin.banners.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-right me-1"></i>
                                العودة للقائمة
                            </a>
                        </div>
                    </div>

                    <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" id="sliderForm">
                        @csrf

                        <!-- العنوان -->
                        <div class="form-group">
                            <label for="title" class="form-label">
                                <i class="fas fa-heading"></i>
                                عنوان السلايدر
                                <span class="required-star">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('title') is-invalid @enderror" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title') }}"
                                   placeholder="أدخل عنوان السلايدر"
                                   required>
                            @error('title')
                                <div class="error-feedback">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- نوع السلايدر -->
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-tag"></i>
                                نوع السلايدر
                                <span class="required-star">*</span>
                            </label>
                            <div class="type-selector">
                                <div class="type-option">
                                    <input type="radio" 
                                           id="type_user" 
                                           name="type" 
                                           value="user" 
                                           {{ old('type') == 'user' ? 'checked' : '' }}
                                           required>
                                    <label for="type_user">
                                        <i class="fas fa-user"></i>
                                        <span>مستخدم</span>
                                    </label>
                                </div>
                                <div class="type-option">
                                    <input type="radio" 
                                           id="type_driver" 
                                           name="type" 
                                           value="driver" 
                                           {{ old('type') == 'driver' ? 'checked' : '' }}
                                           required>
                                    <label for="type_driver">
                                        <i class="fas fa-truck"></i>
                                        <span>سائق</span>
                                    </label>
                                </div>
                            </div>
                            @error('type')
                                <div class="error-feedback">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- رفع الصورة -->
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-image"></i>
                                صورة السلايدر
                                <span class="required-star">*</span>
                            </label>
                            <div class="image-upload-container" id="imageUploadContainer" onclick="document.getElementById('image').click()">
                                <input type="file" 
                                       class="d-none" 
                                       id="image" 
                                       name="image" 
                                       accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                                       required
                                       onchange="previewImage(this)">
                                <div id="uploadPlaceholder">
                                    <div class="upload-icon">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                    </div>
                                    <div class="upload-text">انقر لرفع الصورة أو اسحب وأفلت هنا</div>
                                    <div class="upload-hint">
                                        الصيغ المدعومة: JPEG, PNG, JPG, GIF, WebP
                                        <br>
                                        الحد الأقصى للحجم: 2 ميجابايت
                                    </div>
                                </div>
                                <div id="imagePreview" style="display: none; position: relative;">
                                    <img id="previewImg" class="preview-image" src="" alt="معاينة الصورة">
                                    <button type="button" class="remove-image-btn" onclick="removeImage(event)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            @error('image')
                                <div class="error-feedback">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- الرابط -->
                        <div class="form-group">
                            <label for="link" class="form-label">
                                <i class="fas fa-link"></i>
                                الرابط
                            </label>
                            <input type="url" 
                                   class="form-control @error('link') is-invalid @enderror" 
                                   id="link" 
                                   name="link" 
                                   value="{{ old('link') }}"
                                   placeholder="https://example.com">
                            <small class="text-muted">اختياري - الرابط الذي سيتم التوجيه إليه عند الضغط على السلايدر</small>
                            @error('link')
                                <div class="error-feedback">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- الترتيب -->
                        <div class="form-group">
                            <label for="order" class="form-label">
                                <i class="fas fa-sort-numeric-down"></i>
                                الترتيب
                            </label>
                            <input type="number" 
                                   class="form-control @error('order') is-invalid @enderror" 
                                   id="order" 
                                   name="order" 
                                   value="{{ old('order') }}"
                                   placeholder="اتركه فارغاً للترتيب التلقائي"
                                   min="1">
                            <small class="text-muted">اختياري - الرقم الأصغر يظهر أولاً</small>
                            @error('order')
                                <div class="error-feedback">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- حالة التفعيل -->
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-toggle-on"></i>
                                حالة السلايدر
                            </label>
                            <div class="toggle-wrapper">
                                <label class="toggle-switch">
                                    <input type="checkbox" 
                                           name="is_active" 
                                           value="1"
                                           {{ old('is_active', true) ? 'checked' : '' }}>
                                    <span class="toggle-slider"></span>
                                </label>
                                <span class="toggle-label" id="statusLabel">
                                    {{ old('is_active', true) ? 'نشط' : 'غير نشط' }}
                                </span>
                            </div>
                            @error('is_active')
                                <div class="error-feedback">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- أزرار الإجراءات -->
                        <div class="form-actions">
                            <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>
                                إلغاء
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                حفظ السلايدر
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // تغيير نص حالة التفعيل
            $('input[name="is_active"]').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#statusLabel').text('نشط');
                } else {
                    $('#statusLabel').text('غير نشط');
                }
            });

            // السحب والإفلات للصورة
            const imageContainer = $('#imageUploadContainer');
            
            imageContainer.on('dragover', function(e) {
                e.preventDefault();
                $(this).addClass('border-primary');
            });

            imageContainer.on('dragleave', function(e) {
                e.preventDefault();
                $(this).removeClass('border-primary');
            });

            imageContainer.on('drop', function(e) {
                e.preventDefault();
                $(this).removeClass('border-primary');
                
                const files = e.originalEvent.dataTransfer.files;
                if (files.length > 0) {
                    $('#image')[0].files = files;
                    previewImage($('#image')[0]);
                }
            });

            // رسائل الخطأ من الجلسة
            @if ($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ في البيانات',
                    html: `
                        <ul class="text-end">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    `,
                });
            @endif
        });

        // معاينة الصورة
        function previewImage(input) {
            const container = $('#imageUploadContainer');
            const placeholder = $('#uploadPlaceholder');
            const preview = $('#imagePreview');
            const previewImg = $('#previewImg');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImg.attr('src', e.target.result);
                    placeholder.hide();
                    preview.show();
                    container.addClass('has-image');
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }

        // حذف الصورة
        function removeImage(event) {
            event.stopPropagation();
            
            const container = $('#imageUploadContainer');
            const placeholder = $('#uploadPlaceholder');
            const preview = $('#imagePreview');
            const imageInput = $('#image');

            imageInput.val('');
            preview.hide();
            placeholder.show();
            container.removeClass('has-image');
        }

        // تأكيد قبل مغادرة الصفحة إذا كان هناك بيانات
        let formChanged = false;
        
        $('#sliderForm input, #sliderForm textarea').on('change', function() {
            formChanged = true;
        });

        $(window).on('beforeunload', function() {
            if (formChanged) {
                return 'لديك تغييرات غير محفوظة. هل تريد المغادرة؟';
            }
        });

        // إلغاء التحذير عند تقديم النموذج
        $('#sliderForm').on('submit', function() {
            formChanged = false;
        });
    </script>
@endsection