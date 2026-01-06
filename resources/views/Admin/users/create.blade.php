@extends('Admin.layout.master')

@section('title', 'إضافة مستخدم جديد')

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

        .avatar-upload {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 0 auto 20px;
        }

        .avatar-preview {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 4px solid #f8f9fa;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 60px;
            color: #6c757d;
        }

        .avatar-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .avatar-upload label {
            position: absolute;
            bottom: 10px;
            right: 10px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #696cff;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .avatar-upload label:hover {
            transform: scale(1.1);
            box-shadow: 0 5px 15px rgba(105, 108, 255, 0.4);
        }

        .avatar-upload input[type="file"] {
            display: none;
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

        .password-strength {
            margin-top: 5px;
        }

        .strength-bar {
            height: 5px;
            /* background: #dee2e6; */
            border-radius: 3px;
            overflow: hidden;
            margin-bottom: 5px;
        }

        .strength-fill {
            height: 100%;
            width: 0%;
            background: #dc3545;
            transition: all 0.3s ease;
        }

        .strength-text {
            font-size: 12px;
            color: #6c757d;
        }

        .form-section {
            /* background: #f8f9fa; */
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .form-section h6 {
            color: #495057;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #dee2e6;
        }

        .preview-card {
            /* background: #f8f9fa; */
            border-radius: 12px;
            padding: 25px;
            border: 2px dashed #dee2e6;
            text-align: center;
            margin-top: 20px;
        }

        .preview-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin: 0 auto 15px;
            /* background: #fff; */
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            color: #696cff;
            border: 3px solid #dee2e6;
        }

        .preview-avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        .preview-name {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .preview-email {
            color: #6c757d;
            margin-bottom: 15px;
        }

        @media (max-width: 768px) {
            .form-card {
                padding: 20px;
            }

            .avatar-upload {
                width: 120px;
                height: 120px;
            }

            .avatar-preview {
                width: 120px;
                height: 120px;
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
                    <a href="{{ route('admin.users.index') }}">المستخدمين</a>
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
                                <h5 class="mb-1">إضافة مستخدم جديد</h5>
                                <p class="text-muted mb-0">إضافة مستخدم جديد للنظام</p>
                            </div>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                            </a>
                        </div>
                    </div>

                    <div class="alert-guide" bis_skin_checked="1">
                        <h6><i class="fas fa-lightbulb me-2"></i>معلومات مهمة:</h6>
                        <ul>
                            <li>البريد الإلكتروني يجب أن يكون فريداً ولا يتكرر</li>
                            <li>رقم الهاتف اختياري، ولكن إذا أدخلته يجب أن يكون فريداً</li>
                            <li>كلمة المرور يجب أن تكون قوية (8 أحرف على الأقل)</li>
                            <li>يمكن للمستخدم تعديل بياناته بعد التسجيل</li>
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

                    <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data"
                        id="createForm">
                        @csrf

                        <div class="row" bis_skin_checked="1">
                            <div class="col-lg-8" bis_skin_checked="1">
                                <!-- صورة المستخدم -->
                                <div class="text-center mb-4" bis_skin_checked="1">
                                    <div class="avatar-upload" bis_skin_checked="1">
                                        <div class="avatar-preview" id="avatarPreview">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <label for="avatar">
                                            <i class="fas fa-camera"></i>
                                        </label>
                                        <input type="file" id="avatar" name="image" accept="image/*">
                                    </div>
                                    <div class="help-text" bis_skin_checked="1">انقر على الأيقونة لرفع صورة للمستخدم
                                        (اختياري)</div>
                                </div>

                                <!-- المعلومات الأساسية -->
                                <div class="form-section" bis_skin_checked="1">
                                    <h6><i class="fas fa-user me-2"></i>المعلومات الشخصية</h6>

                                    <div class="mb-3" bis_skin_checked="1">
                                        <label for="name" class="form-label required">الاسم الكامل</label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            value="{{ old('name') }}" placeholder="أدخل الاسم الكامل للمستخدم" required>
                                        <div class="help-text" bis_skin_checked="1">الاسم الذي سيظهر في النظام</div>
                                    </div>
                                </div>

                                <!-- معلومات الاتصال -->
                                <div class="form-section" bis_skin_checked="1">
                                    <h6><i class="fas fa-address-card me-2"></i>معلومات الاتصال</h6>

                                    <div class="mb-3" bis_skin_checked="1">
                                        <label for="email" class="form-label required">البريد الإلكتروني</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            value="{{ old('email') }}" placeholder="example@domain.com" required>
                                        <div class="help-text" bis_skin_checked="1">يستخدم لتسجيل الدخول واستقبال الإشعارات
                                        </div>
                                    </div>

                                    <div class="mb-3" bis_skin_checked="1">
                                        <label for="phone" class="form-label">رقم الهاتف</label>
                                        <input type="tel" class="form-control" id="phone" name="phone"
                                            value="{{ old('phone') }}" placeholder="05xxxxxxxx">
                                        <div class="help-text" bis_skin_checked="1">رقم الهاتف للتواصل (اختياري)</div>
                                    </div>
                                </div>

                                <!-- كلمة المرور -->
                                <div class="form-section" bis_skin_checked="1">
                                    <h6><i class="fas fa-lock me-2"></i>كلمة المرور</h6>

                                    <div class="mb-3" bis_skin_checked="1">
                                        <label for="password" class="form-label required">كلمة المرور</label>
                                        <input type="password" class="form-control" id="password" name="password"
                                            placeholder="أدخل كلمة مرور قوية" required
                                            onkeyup="checkPasswordStrength(this.value)">
                                        <div class="password-strength" bis_skin_checked="1">
                                            <div class="strength-bar" bis_skin_checked="1">
                                                <div class="strength-fill" id="strengthFill"></div>
                                            </div>
                                            <div class="strength-text" id="strengthText">قوة كلمة المرور</div>
                                        </div>
                                    </div>

                                    <div class="mb-3" bis_skin_checked="1">
                                        <label for="password_confirmation" class="form-label required">تأكيد كلمة
                                            المرور</label>
                                        <input type="password" class="form-control" id="password_confirmation"
                                            name="password_confirmation" placeholder="أعد إدخال كلمة المرور" required>
                                    </div>

                                    <div class="help-text" bis_skin_checked="1">
                                        <i class="fas fa-info-circle me-1"></i>
                                        كلمة المرور يجب أن تحتوي على 8 أحرف على الأقل
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4" bis_skin_checked="1">
                                <!-- المعاينة -->
                                <div class="preview-card" bis_skin_checked="1">
                                    <h6 class="mb-3">معاينة المستخدم</h6>

                                    <div class="preview-avatar" id="previewAvatar">
                                        <i class="fas fa-user"></i>
                                    </div>

                                    <div class="preview-name" id="previewName">اسم المستخدم</div>
                                    <div class="preview-email" id="previewEmail">example@domain.com</div>

                                    <div class="d-flex justify-content-center gap-2 mb-3" bis_skin_checked="1">
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>
                                            نشط
                                        </span>
                                    </div>

                                    <p class="text-muted mb-0">
                                        هذه هي معاينة المستخدم كما ستظهر في النظام
                                    </p>
                                </div>

                                <!-- المعلومات الإضافية -->
                                <div class="mt-4" bis_skin_checked="1">
                                    <div class="alert alert-info" bis_skin_checked="1">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>ملاحظة:</strong> يمكن للمستخدم فيما بعد:
                                        <ul class="mt-2 mb-0 ps-3">
                                            <li>تعديل بياناته الشخصية</li>
                                            <li>تغيير كلمة المرور</li>
                                            <li>إضافة صورة شخصية</li>
                                            <li>إدارة طلباته وتفضيلاته</li>
                                        </ul>
                                    </div>
                                </div>

                                <!-- الأزرار -->
                                <div class="mt-4" bis_skin_checked="1">
                                    <div class="d-grid gap-2" bis_skin_checked="1">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fas fa-save me-2"></i>حفظ المستخدم
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
                $('#previewName').text($(this).val() || 'اسم المستخدم');
            });

            // تحديث المعاينة عند تغيير البريد
            $('#email').on('input', function() {
                $('#previewEmail').text($(this).val() || 'example@domain.com');
            });

            // رفع الصورة ومعاينتها
            $('#avatar').on('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        $('#avatarPreview').html(`<img src="${e.target.result}" alt="صورة المستخدم">`);
                        $('#previewAvatar').html(`<img src="${e.target.result}" alt="صورة المستخدم">`);
                    }

                    reader.readAsDataURL(file);
                }
            });

            // التحقق من النموذج قبل الإرسال
            $('#createForm').on('submit', function(e) {
                const password = $('#password').val();
                const confirmPassword = $('#password_confirmation').val();

                if (password !== confirmPassword) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: 'كلمتا المرور غير متطابقتين',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    return;
                }

                if (password.length < 8) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'كلمة مرور ضعيفة',
                        text: 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        });

        function checkPasswordStrength(password) {
            let strength = 0;
            const strengthFill = $('#strengthFill');
            const strengthText = $('#strengthText');

            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
            if (password.match(/\d/)) strength++;
            if (password.match(/[^a-zA-Z\d]/)) strength++;

            let color, text;
            switch (strength) {
                case 0:
                    color = '#dc3545';
                    text = 'ضعيفة جداً';
                    break;
                case 1:
                    color = '#dc3545';
                    text = 'ضعيفة';
                    break;
                case 2:
                    color = '#ffc107';
                    text = 'متوسطة';
                    break;
                case 3:
                    color = '#28a745';
                    text = 'قوية';
                    break;
                case 4:
                    color = '#20c997';
                    text = 'قوية جداً';
                    break;
            }

            const width = (strength / 4) * 100;
            strengthFill.css({
                'width': width + '%',
                'background-color': color
            });
            strengthText.text('قوة كلمة المرور: ' + text).css('color', color);
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

                    // إعادة تعيين المعاينة
                    $('#avatarPreview').html('<i class="fas fa-user"></i>');
                    $('#previewAvatar').html('<i class="fas fa-user"></i>');
                    $('#previewName').text('اسم المستخدم');
                    $('#previewEmail').text('example@domain.com');
                    $('#strengthFill').css('width', '0%');
                    $('#strengthText').text('قوة كلمة المرور').css('color', '#6c757d');

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
