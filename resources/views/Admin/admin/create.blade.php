@extends('Admin.layout.master')

@section('title', 'إضافة موظف جديد')

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

        .form-label {
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 8px;
            display: block;
        }

        .required::after {
            content: " *";
            color: var(--danger-color);
        }

        .form-control,
        .form-select {
            background: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: #fff !important;
            border-radius: 10px !important;
            padding: 12px 15px !important;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            background: rgba(255, 255, 255, 0.1) !important;
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 0.25rem rgba(105, 108, 255, 0.25) !important;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .form-select option {
            background: var(--dark-card);
            color: #fff;
        }

        .form-text {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.6);
            margin-top: 5px;
        }

        .module-section {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .module-title {
            font-size: 16px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .module-title i {
            color: var(--primary-color);
        }

        .role-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            padding: 8px 12px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.02);
            transition: all 0.3s ease;
        }

        .role-item:hover {
            background: rgba(105, 108, 255, 0.1);
        }

        .role-name {
            font-weight: 500;
            color: rgba(255, 255, 255, 0.9);
        }

        .role-description {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.6);
            margin-right: 10px;
            display: block;
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
        }

        .btn-outline-primary {
            background: transparent;
            border: 1px solid var(--primary-color);
            color: var(--primary-color);
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            background: rgba(105, 108, 255, 0.15);
            color: #fff;
        }

        .error-message {
            color: var(--danger-color);
            font-size: 13px;
            margin-top: 5px;
        }

        .avatar-preview {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary-color);
            margin-bottom: 15px;
            background: rgba(255, 255, 255, 0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 50px;
            color: rgba(255, 255, 255, 0.3);
        }

        .avatar-upload-wrapper {
            position: relative;
            display: inline-block;
            cursor: pointer;
        }

        .avatar-upload-overlay {
            position: absolute;
            bottom: 15px;
            right: 0;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 14px;
            border: 3px solid var(--dark-card);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .avatar-upload-overlay:hover {
            transform: scale(1.1);
        }

        /* Form Check Styles */
        .form-check-input {
            display: none;
        }

        .form-check-label {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 15px;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .form-check-label:hover {
            background: rgba(105, 108, 255, 0.08);
            border-color: rgba(105, 108, 255, 0.2);
        }

        .form-check-input:checked+.form-check-label {
            background: rgba(105, 108, 255, 0.12);
            border-color: var(--primary-color);
            border-right: 3px solid var(--primary-color);
        }

        .check-icon {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }

        .form-check-input:checked+.form-check-label .check-icon {
            background: var(--success-color);
            border-color: var(--success-color);
            color: #fff;
        }

        /* Password Strength */
        .password-strength {
            height: 4px;
            border-radius: 2px;
            margin-top: 8px;
            background: rgba(255, 255, 255, 0.1);
            overflow: hidden;
        }

        .password-strength-bar {
            height: 100%;
            width: 0;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .strength-weak {
            background: var(--danger-color);
            width: 25%;
        }
        .strength-fair {
            background: var(--warning-color);
            width: 50%;
        }
        .strength-good {
            background: #17a2b8;
            width: 75%;
        }
        .strength-strong {
            background: var(--success-color);
            width: 100%;
        }

        /* Generate Password Button */
        .generate-password-btn {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(105, 108, 255, 0.2);
            border: none;
            color: var(--primary-color);
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .generate-password-btn:hover {
            background: rgba(105, 108, 255, 0.4);
            color: #fff;
        }

        .password-wrapper {
            position: relative;
        }

        .password-wrapper .form-control {
            padding-left: 50px;
        }

        /* Toggle Password Visibility */
        .toggle-password {
            position: absolute;
            left: 50px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.5);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .toggle-password:hover {
            color: #fff;
        }

        /* is-invalid */
        .form-control.is-invalid,
        .form-select.is-invalid {
            border-color: var(--danger-color) !important;
        }

        @media (max-width: 768px) {
            .form-header {
                padding: 20px;
                margin: -20px -20px 20px -20px;
            }

            .form-card {
                padding: 20px;
            }

            .avatar-preview {
                width: 100px;
                height: 100px;
                font-size: 40px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y" dir="rtl">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 mb-4">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.index') }}" class="text-primary">الرئيسية</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.admins.index') }}" class="text-primary">الموظفين</a>
                </li>
                <li class="breadcrumb-item active text-white">إضافة موظف جديد</li>
            </ol>
        </nav>

        <!-- Alert Messages -->
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert"
                style="background: rgba(220,53,69,0.1); border-color: rgba(220,53,69,0.3); color: #dc3545;">
                <i class="fas fa-exclamation-circle me-2"></i>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Main Form Card -->
        <div class="form-card">
            <div class="form-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h5 class="mb-1">
                            <i class="fas fa-user-plus me-2"></i>
                            إضافة موظف جديد
                        </h5>
                        <small class="opacity-75">إنشاء حساب جديد للموظف مع تعيين الرتب والصلاحيات</small>
                    </div>
                    <a href="{{ route('admin.admins.index') }}" class="btn btn-light">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <form action="{{ route('admin.admins.store') }}" method="POST" enctype="multipart/form-data" id="adminForm">
                @csrf

                <div class="row">
                    <!-- العمود الأيسر - المعلومات الأساسية -->
                    <div class="col-lg-8">
                        <!-- معلومات الحساب -->
                        <div class="module-section">
                            <h6 class="module-title">
                                <i class="fas fa-user-circle"></i>
                                معلومات الحساب
                            </h6>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label required">الاسم الكامل</label>
                                    <input type="text" name="name" id="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name') }}" placeholder="أدخل الاسم الكامل" required>
                                    @error('name')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label required">البريد الإلكتروني</label>
                                    <input type="email" name="email" id="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email') }}" placeholder="example@domain.com" required>
                                    @error('email')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">رقم الهاتف</label>
                                    <input type="text" name="phone" id="phone"
                                        class="form-control @error('phone') is-invalid @enderror"
                                        value="{{ old('phone') }}" placeholder="05xxxxxxxx">
                                    @error('phone')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label required">كلمة المرور</label>
                                    <div class="password-wrapper">
                                        <input type="password" name="password" id="password"
                                            class="form-control @error('password') is-invalid @enderror"
                                            placeholder="********" required oninput="checkPasswordStrength()">
                                        <button type="button" class="toggle-password" onclick="togglePasswordVisibility()" title="إظهار/إخفاء كلمة المرور">
                                            <i class="fas fa-eye" id="togglePasswordIcon"></i>
                                        </button>
                                        <button type="button" class="generate-password-btn" onclick="generatePassword()" title="توليد كلمة مرور">
                                            <i class="fas fa-magic"></i>
                                        </button>
                                    </div>
                                    <div class="password-strength mt-2">
                                        <div class="password-strength-bar" id="passwordStrengthBar"></div>
                                    </div>
                                    <div class="form-text" id="passwordStrengthText">يجب أن تكون 6 أحرف على الأقل</div>
                                    @error('password')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- الفرع - إن كان موجوداً -->
                                @if(isset($branches) && $branches->count() > 0)
                                <div class="col-md-6 mb-3">
                                    <label for="branch_id" class="form-label">الفرع</label>
                                    <select name="branch_id" id="branch_id"
                                        class="form-select @error('branch_id') is-invalid @enderror">
                                        <option value="">بدون فرع (رئيسي)</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('branch_id')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- تعيين الرتب -->
                        <div class="module-section">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="module-title mb-0">
                                    <i class="fas fa-user-tag"></i>
                                    الرتب والصلاحيات
                                </h6>
                                <button type="button" class="btn-outline-primary" onclick="selectAllRoles()">
                                    <i class="fas fa-check-double me-1"></i> تحديد الكل
                                </button>
                            </div>

                            @if($roles->count() > 0)
                                <div class="row">
                                    @foreach ($roles as $role)
                                        <div class="col-md-6 mb-3">
                                            <div class="form-check">
                                                <input type="checkbox" name="role" value="{{ $role->name }}"
                                                    id="role_{{ $role->id }}" class="form-check-input"
                                                    {{ old('role') == $role->name ? 'checked' : '' }}>
                                                <label class="form-check-label" for="role_{{ $role->id }}">
                                                    <span class="check-icon">
                                                        <i class="fas fa-check"></i>
                                                    </span>
                                                    <div>
                                                        <span class="role-name">{{ $role->display_name ?? $role->name }}</span>
                                                        @if($role->description)
                                                            <br><small class="role-description">{{ $role->description }}</small>
                                                        @endif
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4 text-muted">
                                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                                    <p>لا توجد رتب متاحة. يرجى إنشاء رتبة أولاً.</p>
                                </div>
                            @endif

                            @error('role')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- العمود الأيمن - الصورة والمعلومات الإضافية -->
                    <div class="col-lg-4">
                        <!-- الصورة الرمزية -->
                        <div class="module-section text-center">
                            <h6 class="module-title justify-content-center">
                                <i class="fas fa-camera"></i>
                                الصورة الشخصية
                            </h6>

                            <div class="avatar-upload-wrapper" onclick="document.getElementById('avatarInput').click()">
                                <div class="avatar-preview" id="avatarPreviewContainer">
                                    <i class="fas fa-user" id="avatarPlaceholder"></i>
                                    <img src="" alt="معاينة الصورة" id="avatarPreviewImg"
                                        style="display: none; width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                </div>
                                <div class="avatar-upload-overlay">
                                    <i class="fas fa-camera"></i>
                                </div>
                            </div>

                            <input type="file" name="avatar" id="avatarInput" class="d-none"
                                accept="image/*" onchange="previewAvatar(this)">

                            <div class="form-text">صيغ مسموحة: JPG, PNG, WEBP</div>
                            <div class="form-text">الحجم الأقصى: 2 ميجابايت</div>
                            @error('avatar')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- نصائح وإرشادات -->
                        <div class="module-section">
                            <h6 class="module-title">
                                <i class="fas fa-lightbulb"></i>
                                إرشادات هامة
                            </h6>
                            <ul class="list-unstyled" style="color: rgba(255, 255, 255, 0.7); font-size: 14px; line-height: 1.8;">
                                <li class="mb-2">
                                    <i class="fas fa-shield-alt me-2" style="color: var(--warning-color);"></i>
                                    استخدم كلمة مرور قوية تحتوي على أحرف وأرقام ورموز.
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-user-tag me-2" style="color: var(--primary-color);"></i>
                                    يمكنك اختيار رتبة واحدة أو أكثر للموظف.
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-envelope me-2" style="color: var(--info-color);"></i>
                                    تأكد من صحة البريد الإلكتروني، سيتم استخدامه لتسجيل الدخول.
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-info-circle me-2" style="color: var(--success-color);"></i>
                                    الصورة الشخصية اختيارية ويمكن إضافتها لاحقاً.
                                </li>
                            </ul>
                        </div>

                        <!-- معلومات سريعة عن الرتب -->
                        @if($roles->count() > 0)
                        <div class="module-section">
                            <h6 class="module-title">
                                <i class="fas fa-list"></i>
                                ملخص الرتب المتاحة
                            </h6>
                            <div style="max-height: 200px; overflow-y: auto;">
                                @foreach ($roles as $role)
                                    <div class="d-flex justify-content-between align-items-center mb-2 p-2"
                                        style="background: rgba(255,255,255,0.02); border-radius: 6px;">
                                        <span style="font-size: 13px;">{{ $role->display_name ?? $role->name }}</span>
                                        <span class="badge bg-primary rounded-pill" style="font-size: 11px;">
                                            {{ $role->permissions_count ?? 0 }} صلاحية
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- أزرار الإجراءات -->
                <div class="d-flex justify-content-end gap-3 mt-4 pt-4 border-top border-secondary">
                    <a href="{{ route('admin.admins.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>إلغاء
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>حفظ الموظف
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // تفعيل أيقونة التحديد المبدئية
            updateCheckIcons();
        });

        // ============================================
        // ⭐ AVATAR PREVIEW
        // ============================================
        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#avatarPreviewImg').attr('src', e.target.result).show();
                    $('#avatarPlaceholder').hide();
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                $('#avatarPreviewImg').hide();
                $('#avatarPlaceholder').show();
            }
        }

        // ============================================
        // ⭐ PASSWORD GENERATOR
        // ============================================
        function generatePassword() {
            const length = 12;
            const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+-=[]{}|;:,.<>?";
            let password = "";
            
            // Ensure at least one of each type
            password += "abcdefghijklmnopqrstuvwxyz"[Math.floor(Math.random() * 26)];
            password += "ABCDEFGHIJKLMNOPQRSTUVWXYZ"[Math.floor(Math.random() * 26)];
            password += "0123456789"[Math.floor(Math.random() * 10)];
            password += "!@#$%^&*"[Math.floor(Math.random() * 8)];
            
            // Fill the rest
            for (let i = password.length; i < length; i++) {
                password += charset[Math.floor(Math.random() * charset.length)];
            }
            
            // Shuffle
            password = password.split('').sort(() => Math.random() - 0.5).join('');
            
            $('#password').val(password);
            $('#password').attr('type', 'text');
            $('#togglePasswordIcon').removeClass('fa-eye').addClass('fa-eye-slash');
            
            // Trigger strength check
            checkPasswordStrength();
            
            // Auto hide after 3 seconds
            setTimeout(() => {
                $('#password').attr('type', 'password');
                $('#togglePasswordIcon').removeClass('fa-eye-slash').addClass('fa-eye');
            }, 3000);
        }

        // ============================================
        // ⭐ TOGGLE PASSWORD VISIBILITY
        // ============================================
        function togglePasswordVisibility() {
            const passwordInput = $('#password');
            const icon = $('#togglePasswordIcon');
            
            if (passwordInput.attr('type') === 'password') {
                passwordInput.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                passwordInput.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        }

        // ============================================
        // ⭐ PASSWORD STRENGTH CHECKER
        // ============================================
        function checkPasswordStrength() {
            const password = $('#password').val();
            const bar = $('#passwordStrengthBar');
            const text = $('#passwordStrengthText');
            
            // Remove all classes
            bar.removeClass('strength-weak strength-fair strength-good strength-strong');
            
            if (password.length === 0) {
                bar.css('width', '0');
                text.text('يجب أن تكون 6 أحرف على الأقل');
                text.css('color', 'rgba(255, 255, 255, 0.6)');
                return;
            }
            
            let strength = 0;
            
            // Length check
            if (password.length >= 6) strength++;
            if (password.length >= 8) strength++;
            if (password.length >= 12) strength++;
            
            // Complexity checks
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;
            
            let strengthClass, strengthText;
            
            if (strength <= 2) {
                strengthClass = 'strength-weak';
                strengthText = 'ضعيفة جداً';
                text.css('color', '#dc3545');
            } else if (strength <= 3) {
                strengthClass = 'strength-fair';
                strengthText = 'متوسطة';
                text.css('color', '#ffc107');
            } else if (strength <= 5) {
                strengthClass = 'strength-good';
                strengthText = 'جيدة';
                text.css('color', '#17a2b8');
            } else {
                strengthClass = 'strength-strong';
                strengthText = 'قوية جداً';
                text.css('color', '#28a745');
            }
            
            bar.addClass(strengthClass);
            text.text('قوة كلمة المرور: ' + strengthText);
        }

        // ============================================
        // ⭐ ROLE SELECTION
        // ============================================
        function selectAllRoles() {
            const checkboxes = $('input[name="role"]');
            const allChecked = checkboxes.length === checkboxes.filter(':checked').length;
            
            // Toggle all
            checkboxes.prop('checked', !allChecked);
            updateCheckIcons();
        }

        function updateCheckIcons() {
            $('input[name="role"]').each(function() {
                const icon = $(this).siblings('label').find('.check-icon i');
                if ($(this).is(':checked')) {
                    icon.show();
                } else {
                    icon.hide();
                }
            });
        }

        // Listen for checkbox changes
        $(document).on('change', 'input[name="role"]', function() {
            updateCheckIcons();
        });

        // ============================================
        // ⭐ FORM VALIDATION FEEDBACK
        // ============================================
        $('#adminForm').on('submit', function(e) {
            const roleChecked = $('input[name="role"]:checked').length > 0;
            
            if (!roleChecked) {
                e.preventDefault();
                
                // Highlight the roles section
                $('.module-section:has(input[name="role"])').css({
                    'border-color': '#dc3545',
                    'animation': 'shake 0.5s ease'
                });
                
                // Show error message if not exists
                if (!$('#role-error').length) {
                    $('input[name="role"]').first().closest('.row').after(
                        '<div id="role-error" class="error-message mt-2">يرجى اختيار رتبة واحدة على الأقل</div>'
                    );
                }
                
                // Scroll to roles section
                $('html, body').animate({
                    scrollTop: $('input[name="role"]').first().offset().top - 150
                }, 500);
                
                // Remove highlight after animation
                setTimeout(() => {
                    $('.module-section:has(input[name="role"])').css('border-color', 'rgba(255, 255, 255, 0.05)');
                }, 2000);
            }
        });

        // Remove error on role selection
        $(document).on('change', 'input[name="role"]', function() {
            $('#role-error').remove();
            $('.module-section:has(input[name="role"])').css('border-color', 'rgba(255, 255, 255, 0.05)');
        });

        // ============================================
        // ⭐ KEYBOARD SHORTCUTS
        // ============================================
        $(document).keydown(function(e) {
            // Ctrl+S to submit form
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                $('#adminForm').submit();
            }
            
            // Escape to cancel
            if (e.key === 'Escape') {
                window.location.href = '{{ route('admin.admins.index') }}';
            }
        });

        // ============================================
        // ⭐ ANIMATION
        // ============================================
        // Add shake animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes shake {
                0%, 100% { transform: translateX(0); }
                25% { transform: translateX(-5px); }
                50% { transform: translateX(5px); }
                75% { transform: translateX(-5px); }
            }
        `;
        document.head.appendChild(style);
    </script>
@endsection