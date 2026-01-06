@extends('Admin.layout.master')

@section('title', 'إنشاء رتبة جديدة')

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
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 8px;
            display: block;
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

        .permission-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            padding: 8px 12px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.02);
            transition: all 0.3s ease;
        }

        .permission-item:hover {
            background: rgba(105, 108, 255, 0.1);
        }

        .permission-name {
            font-weight: 500;
            color: rgba(255, 255, 255, 0.9);
        }

        .permission-description {
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
                    <a href="{{ route('admin.roles.index') }}">الرتب</a>
                </li>
                <li class="breadcrumb-item active">إنشاء رتبة جديدة</li>
            </ol>
        </nav>

        <div class="row" bis_skin_checked="1">
            <div class="col-12" bis_skin_checked="1">
                <div class="form-card" bis_skin_checked="1">
                    <div class="form-header" bis_skin_checked="1">
                        <div class="d-flex justify-content-between align-items-center" bis_skin_checked="1">
                            <div bis_skin_checked="1">
                                <h5 class="mb-1">إنشاء رتبة جديدة</h5>
                                <small class="opacity-75">أضف رتبة جديدة مع الصلاحيات المناسبة</small>
                            </div>
                            <a href="{{ route('admin.roles.index') }}" class="btn btn-light">
                                <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                            </a>
                        </div>
                    </div>

                    <form action="{{ route('admin.roles.store') }}" method="POST">
                        @csrf

                        <div class="row" bis_skin_checked="1">
                            <div class="col-lg-8" bis_skin_checked="1">
                                <!-- معلومات الرتبة -->
                                <div class="mb-4" bis_skin_checked="1">
                                    <h6 class="mb-3"><i class="fas fa-info-circle me-2"></i>معلومات الرتبة</h6>

                                    <div class="form-group" bis_skin_checked="1">
                                        <label class="form-label">اسم الرتبة (إنجليزي) *</label>
                                        <input type="text" name="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            value="{{ old('name') }}" placeholder="مثال: content_manager" required>
                                        <div class="form-text" bis_skin_checked="1">
                                            استخدم أحرف صغيرة وشرطات سفلية فقط، مثال: content_manager
                                        </div>
                                        @error('name')
                                            <div class="error-message">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group" bis_skin_checked="1">
                                        <label class="form-label">الاسم المعروض (عربي) *</label>
                                        <input type="text" name="display_name"
                                            class="form-control @error('display_name') is-invalid @enderror"
                                            value="{{ old('display_name') }}" placeholder="مثال: مدير المحتوى" required>
                                        @error('display_name')
                                            <div class="error-message">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group" bis_skin_checked="1">
                                        <label class="form-label">الوصف</label>
                                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3"
                                            placeholder="وصف مختصر للرتبة">{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="error-message">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- الصلاحيات -->
                                <div bis_skin_checked="1">
                                    <h6 class="mb-3"><i class="fas fa-key me-2"></i>الصلاحيات</h6>

                                    @if ($modules->isEmpty())
                                        <div class="alert alert-warning" bis_skin_checked="1">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            لم يتم إنشاء أي صلاحيات بعد.
                                            <a href="{{ route('admin.permissions.create') }}" class="text-dark">
                                                أنشئ صلاحيات أولاً
                                            </a>
                                        </div>
                                    @else
                                        @foreach ($modules as $module)
                                            @php
                                                $modulePermissions = $permissions->where('module', $module);
                                                $moduleName = module_display_name($module);
                                            @endphp

                                            @if ($modulePermissions->count() > 0)
                                                <div class="module-section" bis_skin_checked="1">
                                                    <div class="d-flex align-items-center justify-content-between mb-3"
                                                        bis_skin_checked="1">
                                                        <h6 class="module-title mb-0">
                                                            <i class="fas fa-{{ module_icon($module) }}"></i>
                                                            {{ $moduleName }}
                                                        </h6>
                                                        <button type="button" class="select-all-btn"
                                                            data-module="{{ $module }}">
                                                            تحديد الكل
                                                        </button>
                                                    </div>

                                                    <div class="row" bis_skin_checked="1">
                                                        @foreach ($modulePermissions as $permission)
                                                            <div class="col-md-6 mb-2" bis_skin_checked="1">
                                                                <div class="permission-item" bis_skin_checked="1">
                                                                    <input type="checkbox" name="permissions[]"
                                                                        value="{{ $permission->id }}"
                                                                        id="permission_{{ $permission->id }}"
                                                                        class="form-check-input permission-checkbox"
                                                                        data-module="{{ $module }}"
                                                                        @if (in_array($permission->id, old('permissions', []))) checked @endif>
                                                                    <label for="permission_{{ $permission->id }}"
                                                                        class="form-check-label w-100">
                                                                        <div class="permission-name" bis_skin_checked="1">
                                                                            {{ $permission->display_name }}
                                                                        </div>
                                                                        @if ($permission->description)
                                                                            <div class="permission-description"
                                                                                bis_skin_checked="1">
                                                                                {{ $permission->description }}
                                                                            </div>
                                                                        @endif
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            <div class="col-lg-4" bis_skin_checked="1">
                                <!-- معلومات إضافية -->
                                <div class="mb-4" bis_skin_checked="1">
                                    <div class="module-section" bis_skin_checked="1">
                                        <h6 class="module-title">
                                            <i class="fas fa-cog"></i>
                                            الإعدادات
                                        </h6>

                                        <div class="form-group" bis_skin_checked="1">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="is_default"
                                                    id="is_default" value="1"
                                                    @if (old('is_default')) checked @endif>
                                                <label class="form-check-label" for="is_default">
                                                    رتبة افتراضية للمستخدمين الجدد
                                                </label>
                                                <div class="form-text" bis_skin_checked="1">
                                                    سيتم تعيين هذه الرتبة تلقائياً للمستخدمين الجدد
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- نصائح -->
                                <div class="module-section" bis_skin_checked="1">
                                    <h6 class="module-title">
                                        <i class="fas fa-lightbulb"></i>
                                        نصائح
                                    </h6>

                                    <ul class="list-unstyled" style="color: rgba(255, 255, 255, 0.7);">
                                        <li class="mb-2">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            اختر اسمًا وصفيًا للرتبة
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            امنح فقط الصلاحيات الضرورية
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            راجع الصلاحيات قبل الحفظ
                                        </li>
                                        <li>
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            يمكنك تعديل الصلاحيات لاحقاً
                                        </li>
                                    </ul>
                                </div>

                                <!-- إحصائيات -->
                                <div class="module-section" bis_skin_checked="1">
                                    <h6 class="module-title">
                                        <i class="fas fa-chart-bar"></i>
                                        إحصائيات
                                    </h6>

                                    <div class="row text-center" bis_skin_checked="1">
                                        <div class="col-6 mb-3" bis_skin_checked="1">
                                            <div class="stats-number"
                                                style="font-size: 24px; font-weight: 700; color: #fff;">
                                                {{ $modules->count() }}
                                            </div>
                                            <div class="stats-label"
                                                style="font-size: 13px; color: rgba(255, 255, 255, 0.7);">
                                                وحدة
                                            </div>
                                        </div>
                                        <div class="col-6 mb-3" bis_skin_checked="1">
                                            <div class="stats-number"
                                                style="font-size: 24px; font-weight: 700; color: #fff;">
                                                {{ $permissions->count() }}
                                            </div>
                                            <div class="stats-label"
                                                style="font-size: 13px; color: rgba(255, 255, 255, 0.7);">
                                                صلاحية
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- أزرار -->
                        <div class="d-flex justify-content-end gap-3 mt-4 pt-4 border-top border-secondary"
                            bis_skin_checked="1">
                            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>إلغاء
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>حفظ الرتبة
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // تحديد/إلغاء تحديد جميع صلاحيات وحدة معينة
            $('.select-all-btn').on('click', function() {
                const module = $(this).data('module');
                const checkboxes = $(`.permission-checkbox[data-module="${module}"]`);
                const allChecked = checkboxes.length === checkboxes.filter(':checked').length;

                checkboxes.prop('checked', !allChecked);
                updateSelectAllButtonText($(this), !allChecked);
            });

            // تحديث نص زر تحديد الكل
            function updateSelectAllButtonText(button, allSelected) {
                button.text(allSelected ? 'إلغاء تحديد الكل' : 'تحديد الكل');
            }

            // تهيئة أزرار تحديد الكل
            $('.select-all-btn').each(function() {
                const module = $(this).data('module');
                const checkboxes = $(`.permission-checkbox[data-module="${module}"]`);
                const allChecked = checkboxes.length === checkboxes.filter(':checked').length;
                updateSelectAllButtonText($(this), allChecked);
            });

            // عند تغيير أي checkbox
            $('.permission-checkbox').on('change', function() {
                const module = $(this).data('module');
                const checkboxes = $(`.permission-checkbox[data-module="${module}"]`);
                const allChecked = checkboxes.length === checkboxes.filter(':checked').length;
                const button = $(`.select-all-btn[data-module="${module}"]`);
                updateSelectAllButtonText(button, allChecked);
            });

            // رسائل التأكيد
            $('form').on('submit', function(e) {
                const selectedPermissions = $('input[name="permissions[]"]:checked').length;

                if (selectedPermissions === 0) {
                    if (!confirm('لم يتم اختيار أي صلاحيات. هل تريد المتابعة؟')) {
                        e.preventDefault();
                    }
                }
            });

            // التحقق من صحة اسم الرتبة
            $('input[name="name"]').on('blur', function() {
                const value = $(this).val();
                const regex = /^[a-z_]+$/;

                if (value && !regex.test(value)) {
                    alert('اسم الرتبة يجب أن يحتوي على أحرف صغيرة وشرطات سفلية فقط');
                    $(this).focus();
                }
            });

            // استرجاع البيانات عند إعادة تحميل الصفحة
            const savedPermissions = {!! json_encode(old('permissions', [])) !!};
            if (savedPermissions.length > 0) {
                savedPermissions.forEach(function(permissionId) {
                    $(`#permission_${permissionId}`).prop('checked', true);
                });

                // تحديث أزرار تحديد الكل
                $('.select-all-btn').each(function() {
                    const module = $(this).data('module');
                    const checkboxes = $(`.permission-checkbox[data-module="${module}"]`);
                    const allChecked = checkboxes.length === checkboxes.filter(':checked').length;
                    updateSelectAllButtonText($(this), allChecked);
                });
            }
        });
    </script>
@endsection
