@extends('Admin.layout.master')

@section('title', 'تعديل المستخدم: ' . $user->name)

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
            background: #dee2e6;
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
            background: #242f3b;
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

        .info-card {
            /* background: #f8f9fa; */
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            border-right: 4px solid #696cff;
        }

        .info-row {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #dee2e6;
        }

        .info-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .info-label {
            min-width: 120px;
            font-weight: 600;
            color: #495057;
        }

        .info-value {
            color: #2c3e50;
            font-weight: 500;
        }

        .badge-custom {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-social {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .badge-google {
            background: #ea4335;
        }

        .badge-facebook {
            background: #1877f2;
        }

        .badge-apple {
            background: #000000;
        }

        .badge-email {
            /* background: #e7f5ff; */
            color: #0c63e4;
        }

        .social-icons {
            display: flex;
            gap: 8px;
            margin-top: 5px;
        }

        .social-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: white;
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
                <li class="breadcrumb-item active">تعديل</li>
            </ol>
        </nav>

        <div class="row" bis_skin_checked="1">
            <div class="col-12" bis_skin_checked="1">
                <div class="form-card" bis_skin_checked="1">
                    <div class="form-header" bis_skin_checked="1">
                        <div class="d-flex justify-content-between align-items-center" bis_skin_checked="1">
                            <div bis_skin_checked="1">
                                <h5 class="mb-1">تعديل بيانات المستخدم</h5>
                                <p class="text-muted mb-0">ID: #{{ $user->id }}</p>
                            </div>
                            <div class="btn-group" bis_skin_checked="1">
                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-info">
                                    <i class="fas fa-eye me-2"></i>عرض
                                </a>
                                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-right me-2"></i>رجوع
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- معلومات المستخدم -->
                    <div class="info-card" bis_skin_checked="1">
                        <h6 class="mb-3">معلومات المستخدم</h6>

                        <div class="info-row" bis_skin_checked="1">
                            <div class="info-label" bis_skin_checked="1">طريقة التسجيل:</div>
                            <div class="info-value" bis_skin_checked="1">
                                @if ($user->google_id || $user->facebook_id || $user->apple_id)
                                    <span class="badge-custom badge-social">
                                        <i class="fas fa-share-alt me-1"></i>
                                        التواصل الاجتماعي
                                    </span>
                                    <div class="social-icons" bis_skin_checked="1">
                                        @if ($user->google_id)
                                            <span class="badge-custom badge-google" title="تسجيل الدخول بجوجل">
                                                <i class="fab fa-google me-1"></i>جوجل
                                            </span>
                                        @endif
                                        @if ($user->facebook_id)
                                            <span class="badge-custom badge-facebook" title="تسجيل الدخول بفيسبوك">
                                                <i class="fab fa-facebook-f me-1"></i>فيسبوك
                                            </span>
                                        @endif
                                        @if ($user->apple_id)
                                            <span class="badge-custom badge-apple" title="تسجيل الدخول بأبل">
                                                <i class="fab fa-apple me-1"></i>أبل
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="badge-custom badge-email">
                                        <i class="fas fa-envelope me-1"></i>
                                        البريد الإلكتروني
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="info-row" bis_skin_checked="1">
                            <div class="info-label" bis_skin_checked="1">تاريخ التسجيل:</div>
                            <div class="info-value" bis_skin_checked="1">
                                {{ $user->created_at->translatedFormat('d M Y - h:i A') }}
                            </div>
                        </div>

                        <div class="info-row" bis_skin_checked="1">
                            <div class="info-label" bis_skin_checked="1">آخر تحديث:</div>
                            <div class="info-value" bis_skin_checked="1">
                                {{ $user->updated_at->translatedFormat('d M Y - h:i A') }}
                            </div>
                        </div>

                        <div class="info-row" bis_skin_checked="1">
                            <div class="info-label" bis_skin_checked="1">عدد الطلبات:</div>
                            <div class="info-value" bis_skin_checked="1">
                                {{ $user->orders()->count() }}
                            </div>
                        </div>

                        <div class="info-row" bis_skin_checked="1">
                            <div class="info-label" bis_skin_checked="1">عدد التقييمات:</div>
                            <div class="info-value" bis_skin_checked="1">
                                {{ $user->reviews()->count() }}
                            </div>
                        </div>
                    </div>

                    <div class="alert-guide" bis_skin_checked="1">
                        <h6><i class="fas fa-lightbulb me-2"></i>نصائح للتعديل:</h6>
                        <ul>
                            <li>يمكنك تعديل أي معلومات عن المستخدم</li>
                            <li>البريد الإلكتروني يجب أن يكون فريداً ولا يتكرر</li>
                            <li>إذا لم ترد تغيير كلمة المرور، اترك الحقلين فارغين</li>
                            <li>احفظ التغييرات بعد الانتهاء</li>
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

                    <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data"
                        id="editForm">
                        @csrf
                        @method('PUT')

                        <div class="row" bis_skin_checked="1">
                            <div class="col-lg-8" bis_skin_checked="1">
                                <!-- صورة المستخدم -->
                                <div class="text-center mb-4" bis_skin_checked="1">
                                    <div class="avatar-upload" bis_skin_checked="1">
                                        <div class="avatar-preview" id="avatarPreview">
                                            @if ($user->image)
                                                <img src="{{ asset('storage/' . $user->image) }}"
                                                    alt="{{ $user->name }}">
                                            @else
                                                <i class="fas fa-user"></i>
                                            @endif
                                        </div>
                                        <label for="avatar">
                                            <i class="fas fa-camera"></i>
                                        </label>
                                        <input type="file" id="avatar" name="image" accept="image/*">
                                    </div>
                                    <div class="help-text" bis_skin_checked="1">
                                        انقر على الأيقونة لتغيير الصورة (اختياري)
                                        @if ($user->image)
                                            <br>
                                            <button type="button" class="btn btn-sm btn-danger mt-2"
                                                onclick="removeImage()">
                                                <i class="fas fa-trash me-1"></i>حذف الصورة
                                            </button>
                                        @endif
                                    </div>
                                    <input type="hidden" id="remove_image" name="remove_image" value="0">
                                </div>

                                <!-- المعلومات الأساسية -->
                                <div class="form-section" bis_skin_checked="1">
                                    <h6><i class="fas fa-user me-2"></i>المعلومات الشخصية</h6>

                                    <div class="mb-3" bis_skin_checked="1">
                                        <label for="name" class="form-label required">الاسم الكامل</label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            value="{{ old('name', $user->name) }}"
                                            placeholder="أدخل الاسم الكامل للمستخدم" required>
                                        <div class="help-text" bis_skin_checked="1">الاسم الذي سيظهر في النظام</div>
                                    </div>
                                </div>

                                <!-- معلومات الاتصال -->
                                <div class="form-section" bis_skin_checked="1">
                                    <h6><i class="fas fa-address-card me-2"></i>معلومات الاتصال</h6>

                                    <div class="mb-3" bis_skin_checked="1">
                                        <label for="email" class="form-label required">البريد الإلكتروني</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            value="{{ old('email', $user->email) }}" placeholder="example@domain.com"
                                            required>
                                        <div class="help-text" bis_skin_checked="1">يستخدم لتسجيل الدخول واستقبال
                                            الإشعارات</div>
                                    </div>

                                    <div class="mb-3" bis_skin_checked="1">
                                        <label for="phone" class="form-label">رقم الهاتف</label>
                                        <input type="tel" class="form-control" id="phone" name="phone"
                                            value="{{ old('phone', $user->phone) }}" placeholder="05xxxxxxxx">
                                        <div class="help-text" bis_skin_checked="1">رقم الهاتف للتواصل (اختياري)</div>
                                    </div>
                                </div>

                                <!-- كلمة المرور -->
                                <div class="form-section" bis_skin_checked="1">
                                    <h6><i class="fas fa-lock me-2"></i>كلمة المرور</h6>

                                    <div class="mb-3" bis_skin_checked="1">
                                        <label for="password" class="form-label">كلمة المرور الجديدة</label>
                                        <input type="password" class="form-control" id="password" name="password"
                                            placeholder="أدخل كلمة مرور جديدة (اختياري)"
                                            onkeyup="checkPasswordStrength(this.value)">
                                        <div class="password-strength" bis_skin_checked="1">
                                            <div class="strength-bar" bis_skin_checked="1">
                                                <div class="strength-fill" id="strengthFill"></div>
                                            </div>
                                            <div class="strength-text" id="strengthText">قوة كلمة المرور</div>
                                        </div>
                                        <div class="help-text" bis_skin_checked="1">
                                            اتركه فارغاً إذا لم ترد تغيير كلمة المرور
                                        </div>
                                    </div>

                                    <div class="mb-3" bis_skin_checked="1">
                                        <label for="password_confirmation" class="form-label">تأكيد كلمة المرور
                                            الجديدة</label>
                                        <input type="password" class="form-control" id="password_confirmation"
                                            name="password_confirmation" placeholder="أعد إدخال كلمة المرور الجديدة">
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4" bis_skin_checked="1">
                                <!-- معلومات سريعة -->
                                <div class="info-card" bis_skin_checked="1">
                                    <h6 class="mb-3">إحصائيات سريعة</h6>

                                    <div class="mb-3" bis_skin_checked="1">
                                        <a href="{{ route('admin.users.orders', $user) }}"
                                            class="btn btn-outline-primary w-100 mb-2">
                                            <i class="fas fa-shopping-cart me-2"></i>
                                            الطلبات ({{ $user->orders()->count() }})
                                        </a>
                                    </div>

                                    <div class="mb-3" bis_skin_checked="1">
                                        <a href="{{ route('admin.users.reviews', $user) }}"
                                            class="btn btn-outline-info w-100 mb-2">
                                            <i class="fas fa-star me-2"></i>
                                            التقييمات ({{ $user->reviews()->count() }})
                                        </a>
                                    </div>

                                    <div class="mb-3" bis_skin_checked="1">
                                        <a href="{{ route('admin.users.favourites', $user) }}"
                                            class="btn btn-outline-warning w-100 mb-2">
                                            <i class="fas fa-heart me-2"></i>
                                            المفضلة ({{ $user->favouriteProducts()->count() }})
                                        </a>
                                    </div>
                                </div>

                                <!-- الأزرار -->
                                <div class="mt-4" bis_skin_checked="1">
                                    <div class="d-grid gap-2" bis_skin_checked="1">
                                        <button type="submit" class="btn btn-success btn-lg">
                                            <i class="fas fa-save me-2"></i>حفظ التعديلات
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                                            <i class="fas fa-trash me-2"></i>حذف المستخدم
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

    <!-- Delete Form -->
    <form id="deleteForm" action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // رفع الصورة ومعاينتها
            $('#avatar').on('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        $('#avatarPreview').html(`<img src="${e.target.result}" alt="صورة المستخدم">`);
                    }

                    reader.readAsDataURL(file);
                }
            });

            // التحقق من النموذج قبل الإرسال
            $('#editForm').on('submit', function(e) {
                const password = $('#password').val();
                const confirmPassword = $('#password_confirmation').val();

                if (password && password !== confirmPassword) {
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

                if (password && password.length < 8) {
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

        function removeImage() {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: 'سيتم حذف الصورة الشخصية للمستخدم',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#avatarPreview').html('<i class="fas fa-user"></i>');
                    $('#remove_image').val('1');
                    Swal.fire({
                        icon: 'success',
                        title: 'تم الحذف',
                        text: 'سيتم حذف الصورة عند حفظ التعديلات',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        }

        function checkPasswordStrength(password) {
            if (!password) {
                $('#strengthFill').css('width', '0%');
                $('#strengthText').text('قوة كلمة المرور').css('color', '#6c757d');
                return;
            }

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

        function confirmDelete() {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: 'سيتم حذف المستخدم "{{ $user->name }}" نهائياً',
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
