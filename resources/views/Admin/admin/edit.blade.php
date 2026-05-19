@extends('Admin.layout.master')

@section('title', 'تعديل بيانات الموظف')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #696cff;
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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

        .form-control {
            background: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: #fff !important;
            border-radius: 10px !important;
            padding: 12px 15px !important;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.1) !important;
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 0.25rem rgba(105, 108, 255, 0.25) !important;
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
            margin-left: 10px;
        }

        .select-all-btn {
            background: rgba(105, 108, 255, 0.2);
            border: 1px solid rgba(105, 108, 255, 0.3);
            color: var(--primary-color);
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-left: auto;
        }

        .select-all-btn:hover {
            background: rgba(105, 108, 255, 0.3);
        }

        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
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
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.3);
        }

        .error-message {
            color: #dc3545;
            font-size: 13px;
            margin-top: 5px;
        }

        .avatar-preview {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary-color);
            margin-bottom: 10px;
        }

        .form-check-input:checked + .form-check-label {
            background: rgba(105, 108, 255, 0.15);
            border-left: 3px solid var(--primary-color);
        }

        .form-check-label {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 15px;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }

        .form-check-label i.fa-check-circle {
            display: none;
            color: #2ecc71;
        }

        .form-check-input:checked + .form-check-label i.fa-check-circle {
            display: inline-block;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y" dir="rtl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">الرئيسية</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.admins.index') }}">الموظفين</a></li>
                <li class="breadcrumb-item active">تعديل الموظف: {{ $admin->name }}</li>
            </ol>
        </nav>

        <div class="form-card">
            <div class="form-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">تعديل بيانات الموظف</h5>
                        <small class="opacity-75">تحديث معلومات الموظف والرتب المخصصة له</small>
                    </div>
                    <a href="{{ route('admin.admins.index') }}" class="btn btn-light">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <form action="{{ route('admin.admins.update', $admin) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-lg-8">
                        <!-- معلومات أساسية -->
                        <div class="module-section">
                            <h6 class="module-title"><i class="fas fa-user-circle"></i> معلومات الحساب</h6>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">الاسم الكامل *</label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name', $admin->name) }}" required>
                                    @error('name')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">البريد الإلكتروني *</label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                           value="{{ old('email', $admin->email) }}" required>
                                    @error('email')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">رقم الهاتف</label>
                                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                           value="{{ old('phone', $admin->phone) }}">
                                    @error('phone')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">كلمة المرور الجديدة (اتركه فارغاً إن لم ترغب بالتغيير)</label>
                                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                           placeholder="********">
                                    <div class="form-text">يجب أن تكون 8 أحرف على الأقل</div>
                                    @error('password')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- الفرع - إن كان موجوداً -->
                                @if(isset($branches))
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">الفرع</label>
                                    <select name="branch_id" class="form-control @error('branch_id') is-invalid @enderror">
                                        <option value="">بدون فرع</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" {{ old('branch_id', $admin->branch_id) == $branch->id ? 'selected' : '' }}>
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
                            <h6 class="module-title"><i class="fas fa-user-tag"></i> الرتب والصلاحيات</h6>
                            <div class="row">
                                @foreach ($roles as $role)
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check">
                                            <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                                   id="role_{{ $role->id }}" class="form-check-input d-none"
                                                   @if(in_array($role->id, old('roles', $admin->roles->pluck('id')->toArray()))) checked @endif>
                                            <label class="form-check-label" for="role_{{ $role->id }}">
                                                <div class="d-flex justify-content-between align-items-center w-100">
                                                    <div>
                                                        <span class="role-name">{{ $role->display_name ?? $role->name }}</span>
                                                        @if($role->description)
                                                            <br><small class="role-description">{{ $role->description }}</small>
                                                        @endif
                                                    </div>
                                                    <i class="fas fa-check-circle"></i>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <!-- الصورة الرمزية -->
                        <div class="module-section text-center">
                            <h6 class="module-title"><i class="fas fa-camera"></i> الصورة الشخصية</h6>
                            <div class="mb-3">
                                <img src="{{ $admin->avatar ? get_user_image($admin->avatar) : asset('assets/images/default-avatar.png') }}"
                                     alt="الصورة الحالية" class="avatar-preview" id="avatarPreview">
                            </div>
                            <input type="file" name="avatar" class="form-control" id="avatarInput" accept="image/*">
                            <div class="form-text">يُسمح بـ JPG, PNG. الحجم الأقصى 2MB</div>
                            @error('avatar')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- نصائح -->
                        <div class="module-section">
                            <h6 class="module-title"><i class="fas fa-lightbulb"></i> تذكير</h6>
                            <ul class="list-unstyled" style="color: rgba(255, 255, 255, 0.7);">
                                <li class="mb-2"><i class="fas fa-info-circle me-2 text-info"></i> تغيير الرتب يؤثر على صلاحيات الموظف فوراً.</li>
                                <li class="mb-2"><i class="fas fa-info-circle me-2 text-info"></i> ترك كلمة المرور فارغة يعني الإبقاء على القديمة.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- أزرار -->
                <div class="d-flex justify-content-end gap-3 mt-4 pt-4 border-top border-secondary">
                    <a href="{{ route('admin.admins.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>إلغاء
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>حفظ التغييرات
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
            // معاينة الصورة قبل الرفع
            $('#avatarInput').on('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#avatarPreview').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(file);
                }
            });

            // إظهار علامة الصح عند تحديد رتبة
            function toggleCheckIcon() {
                $('input[name="roles[]"]').each(function() {
                    const icon = $(this).siblings('label').find('i.fa-check-circle');
                    if ($(this).is(':checked')) {
                        icon.show();
                    } else {
                        icon.hide();
                    }
                });
            }

            // تشغيل عند تحميل الصفحة
            toggleCheckIcon();

            // عند تغيير أي مربع اختيار
            $('input[name="roles[]"]').on('change', toggleCheckIcon);
        });
    </script>
@endsection