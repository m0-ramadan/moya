@extends('Admin.layout.master')

@section('title', 'تعديل بيانات السائق')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        body {
            font-family: "Cairo", sans-serif !important;
        }

        /* Form Card */
        .form-card {
            background: var(--bs-card-bg);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        /* Section Header */
        .section-header {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--bs-border-color);
        }

        .section-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-left: 15px;
        }

        .section-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--bs-heading-color);
            margin-bottom: 5px;
        }

        .section-description {
            color: var(--bs-secondary-color);
            font-size: 14px;
        }

        /* Driver Info Card */
        .driver-info-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            padding: 25px;
            color: white;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .driver-avatar {
            width: 80px;
            height: 80px;
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            font-weight: 600;
            overflow: hidden;
        }

        .driver-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .driver-details h4 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .driver-details p {
            opacity: 0.9;
            margin-bottom: 0;
        }

        .driver-status {
            margin-right: auto;
        }

        .status-badge {
            padding: 8px 20px;
            border-radius: 25px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        /* Form Grid */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        /* Form Group */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: var(--bs-heading-color);
            margin-bottom: 8px;
        }

        .form-label i {
            margin-left: 5px;
            color: #696cff;
        }

        .form-label .required {
            color: #dc3545;
            margin-right: 3px;
        }

        .form-control, .form-select {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid var(--bs-border-color);
            border-radius: 8px;
            background: var(--bs-card-bg);
            color: var(--bs-heading-color);
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #696cff;
            outline: none;
            box-shadow: 0 0 0 3px rgba(105, 108, 255, 0.1);
        }

        .form-control.is-invalid, .form-select.is-invalid {
            border-color: #dc3545;
        }

        .invalid-feedback {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
        }

        /* Readonly Field */
        .form-control-static {
            padding: 10px 15px;
            background: rgb(18 24 36 / 90%);
            border-radius: 8px;
            color: var(--bs-heading-color);
            border: 1px solid var(--bs-border-color);
        }

        /* File Upload */
        .file-upload {
            position: relative;
            margin-bottom: 15px;
        }

        .file-upload-input {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
            z-index: 2;
        }

        .file-upload-area {
            border: 2px dashed var(--bs-border-color);
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s ease;
            background: rgb(18 24 36 / 90%);
        }

        .file-upload-area:hover {
            border-color: #696cff;
            background: rgba(105, 108, 255, 0.05);
        }

        .file-upload-icon {
            font-size: 40px;
            color: #696cff;
            margin-bottom: 10px;
        }

        .file-upload-text {
            font-size: 16px;
            font-weight: 600;
            color: var(--bs-heading-color);
            margin-bottom: 5px;
        }

        .file-upload-hint {
            font-size: 12px;
            color: var(--bs-secondary-color);
        }

        /* Image Preview */
        .image-preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 15px;
        }

        .image-preview-item {
            position: relative;
            width: 120px;
            height: 120px;
            border-radius: 10px;
            overflow: hidden;
            border: 2px solid var(--bs-border-color);
        }

        .image-preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .image-preview-remove {
            position: absolute;
            top: 5px;
            right: 5px;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            background: rgba(220, 53, 69, 0.9);
            color: white;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.3s ease;
        }

        .image-preview-remove:hover {
            background: #dc3545;
            transform: scale(1.1);
        }

        /* Current Image */
        .current-image {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
            padding: 10px;
            background: rgb(18 24 36 / 90%);
            border-radius: 8px;
        }

        .current-image img {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            object-fit: cover;
        }

        .current-image-info {
            flex: 1;
        }

        .current-image-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--bs-heading-color);
        }

        .current-image-size {
            font-size: 11px;
            color: var(--bs-secondary-color);
        }

        .current-image-actions {
            display: flex;
            gap: 5px;
        }

        .btn-view {
            width: 35px;
            height: 35px;
            border-radius: 8px;
            background: #696cff;
            color: white;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-view:hover {
            opacity: 0.8;
            transform: translateY(-2px);
        }

        .btn-delete-image {
            width: 35px;
            height: 35px;
            border-radius: 8px;
            background: #dc3545;
            color: white;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-delete-image:hover {
            opacity: 0.8;
            transform: translateY(-2px);
        }

        /* Citizenship Toggle */
        .citizenship-toggle {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .citizenship-option {
            flex: 1;
            position: relative;
        }

        .citizenship-option input {
            position: absolute;
            opacity: 0;
        }

        .citizenship-option label {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 15px;
            border: 2px solid var(--bs-border-color);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .citizenship-option input:checked + label {
            border-color: #696cff;
            background: rgba(105, 108, 255, 0.1);
            color: #696cff;
        }

        .citizenship-option.saudi input:checked + label {
            border-color: #198754;
            background: rgba(25, 135, 84, 0.1);
            color: #198754;
        }

        .citizenship-option.resident input:checked + label {
            border-color: #fd7e14;
            background: rgba(253, 126, 20, 0.1);
            color: #fd7e14;
        }

        /* Vehicle Ownership Toggle */
        .ownership-toggle {
            display: flex;
            gap: 15px;
            margin-top: 10px;
        }

        .ownership-option {
            flex: 1;
            position: relative;
        }

        .ownership-option input {
            position: absolute;
            opacity: 0;
        }

        .ownership-option label {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 12px;
            border: 2px solid var(--bs-border-color);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .ownership-option.owner input:checked + label {
            border-color: #198754;
            background: rgba(25, 135, 84, 0.1);
            color: #198754;
        }

        .ownership-option.not-owner input:checked + label {
            border-color: #dc3545;
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        /* Select2 Customization */
        .select2-container--default .select2-selection--single {
            height: 45px;
            border: 1px solid var(--bs-border-color);
            border-radius: 8px;
            background: var(--bs-card-bg);
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 45px;
            color: var(--bs-heading-color);
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 43px;
        }

        .select2-dropdown {
            background: var(--bs-card-bg);
            border-color: var(--bs-border-color);
        }

        /* Form Actions */
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid var(--bs-border-color);
        }

        .btn-submit {
            background: linear-gradient(135deg, #198754 0%, #20c997 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            opacity: 0.9;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(25, 135, 84, 0.3);
        }

        .btn-cancel {
            background: var(--bs-secondary-bg);
            color: var(--bs-heading-color);
            border: 1px solid var(--bs-border-color);
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-cancel:hover {
            background: var(--bs-border-color);
            transform: translateY(-2px);
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #d63384 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-danger:hover {
            opacity: 0.9;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
        }

        /* Verification Status */
        .verification-status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 15px;
            border-radius: 25px;
            font-weight: 600;
        }

        .verification-status.verified {
            background: var(--bs-success-bg-subtle);
            color: var(--bs-success-text);
        }

        .verification-status.pending {
            background: var(--bs-warning-bg-subtle);
            color: var(--bs-warning-text);
        }

        .verification-status.rejected {
            background: var(--bs-danger-bg-subtle);
            color: var(--bs-danger-text);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .driver-info-card {
                flex-direction: column;
                text-align: center;
            }

            .driver-status {
                margin-right: 0;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .citizenship-toggle {
                flex-direction: column;
            }

            .ownership-toggle {
                flex-direction: column;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn-submit, .btn-cancel, .btn-danger {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.index') }}">الرئيسية</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.drivers.index') }}">السائقين</a>
                </li>
                <li class="breadcrumb-item active">تعديل بيانات السائق: {{ $driver->user->name }}</li>
            </ol>
        </nav>

        <!-- Driver Info Card -->
        <div class="driver-info-card">
            <div class="driver-avatar">
                @if($driver->personal_photo)
                    <img src="{{ asset('storage/' . $driver->personal_photo) }}" alt="{{ $driver->user->name }}">
                @else
                    {{ substr($driver->user->name, 0, 1) }}
                @endif
            </div>
            <div class="driver-details">
                <h4>{{ $driver->user->name }}</h4>
                <p>
                    <i class="fas fa-phone"></i> {{ $driver->user->full_phone ?? $driver->user->phone }} |
                    <i class="fas fa-envelope"></i> {{ $driver->user->email }}
                </p>
            </div>
            <div class="driver-status">
                <span class="status-badge">
                    <i class="fas {{ $driver->is_verified ? 'fa-check-circle' : 'fa-clock' }}"></i>
                    {{ $driver->is_verified ? 'موثق' : 'في انتظار التحقق' }}
                </span>
                @if($driver->rejection_reason)
                    <span class="status-badge" style="background: rgba(220, 53, 69, 0.3);">
                        <i class="fas fa-times-circle"></i>
                        مرفوض
                    </span>
                @endif
            </div>
        </div>

        <form action="{{ route('admin.drivers.update', $driver->id) }}" method="POST" enctype="multipart/form-data" id="driverForm">
            @csrf
            @method('PUT')

            <!-- Personal Information Section -->
            <div class="form-card">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <h5 class="section-title">المعلومات الشخصية</h5>
                        <p class="section-description">البيانات الأساسية للسائق</p>
                    </div>
                </div>

                <div class="form-grid">
                    <!-- User Selection (Readonly) -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-user"></i>
                            حساب المستخدم
                            <span class="required">*</span>
                        </label>
                        <div class="form-control-static">
                            {{ $driver->user->name }} - {{ $driver->user->email }} - {{ $driver->user->phone }}
                        </div>
                        <input type="hidden" name="user_id" value="{{ $driver->user_id }}">
                    </div>

                    <!-- Citizenship Toggle -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-globe"></i>
                            الجنسية
                            <span class="required">*</span>
                        </label>
                        <div class="citizenship-toggle">
                            <div class="citizenship-option saudi">
                                <input type="radio" name="citizenship" id="citizenship_saudi" value="saudi" 
                                    {{ old('citizenship', $driver->citizenship) == 'saudi' ? 'checked' : '' }} required>
                                <label for="citizenship_saudi">
                                    <i class="fas fa-flag"></i>
                                    سعودي
                                </label>
                            </div>
                            <div class="citizenship-option resident">
                                <input type="radio" name="citizenship" id="citizenship_resident" value="resident" 
                                    {{ old('citizenship', $driver->citizenship) == 'resident' ? 'checked' : '' }} required>
                                <label for="citizenship_resident">
                                    <i class="fas fa-passport"></i>
                                    مقيم
                                </label>
                            </div>
                        </div>
                        @error('citizenship')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Date of Birth -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-calendar-alt"></i>
                            تاريخ الميلاد
                            <span class="required">*</span>
                        </label>
                        <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" 
                            value="{{ old('date_of_birth', $driver->date_of_birth ? $driver->date_of_birth->format('Y-m-d') : '') }}" required>
                        @error('date_of_birth')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Saudi Fields -->
                    <div class="saudi-fields" style="{{ old('citizenship', $driver->citizenship) == 'saudi' ? 'display: block;' : 'display: none;' }}">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-id-card"></i>
                                رقم الهوية
                                <span class="required">*</span>
                            </label>
                            <input type="text" name="national_id" class="form-control @error('national_id') is-invalid @enderror" 
                                value="{{ old('national_id', $driver->national_id) }}" placeholder="أدخل رقم الهوية">
                            @error('national_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Resident Fields -->
                    <div class="resident-fields" style="{{ old('citizenship', $driver->citizenship) == 'resident' ? 'display: block;' : 'display: none;' }}">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-passport"></i>
                                رقم الإقامة
                                <span class="required">*</span>
                            </label>
                            <input type="text" name="iqama_number" class="form-control @error('iqama_number') is-invalid @enderror" 
                                value="{{ old('iqama_number', $driver->iqama_number) }}" placeholder="أدخل رقم الإقامة">
                            @error('iqama_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-calendar-alt"></i>
                                تاريخ انتهاء الإقامة
                                <span class="required">*</span>
                            </label>
                            <input type="date" name="iqama_expiry_date" class="form-control @error('iqama_expiry_date') is-invalid @enderror" 
                                value="{{ old('iqama_expiry_date', $driver->iqama_expiry_date ? $driver->iqama_expiry_date->format('Y-m-d') : '') }}">
                            @error('iqama_expiry_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- License & Documents Section -->
            <div class="form-card">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <div>
                        <h5 class="section-title">الرخصة والوثائق</h5>
                        <p class="section-description">معلومات رخصة القيادة والمستندات الرسمية</p>
                    </div>
                </div>

                <div class="form-grid">
                    <!-- License Number -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-id-card"></i>
                            رقم رخصة القيادة
                            <span class="required">*</span>
                        </label>
                        <input type="text" name="license_number" class="form-control @error('license_number') is-invalid @enderror" 
                            value="{{ old('license_number', $driver->license_number) }}" placeholder="أدخل رقم الرخصة" required>
                        @error('license_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- License Expiry Date -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-calendar-alt"></i>
                            تاريخ انتهاء الرخصة
                            <span class="required">*</span>
                        </label>
                        <input type="date" name="license_expiry_date" class="form-control @error('license_expiry_date') is-invalid @enderror" 
                            value="{{ old('license_expiry_date', $driver->license_expiry_date ? $driver->license_expiry_date->format('Y-m-d') : '') }}" required>
                        @error('license_expiry_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Documents Upload -->
                <div class="form-grid">
                    <!-- Personal Photo -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-user"></i>
                            الصورة الشخصية
                        </label>
                        
                        @if($driver->personal_photo)
                            <div class="current-image">
                                <img src="{{ asset('storage/' . $driver->personal_photo) }}" alt="Personal Photo">
                                <div class="current-image-info">
                                    <div class="current-image-name">الصورة الشخصية الحالية</div>
                                    <div class="current-image-size">
                                        @php
                                            $size = Storage::disk('public')->exists($driver->personal_photo) ? Storage::disk('public')->size($driver->personal_photo) : 0;
                                            echo $size ? round($size / 1024, 2) . ' KB' : 'غير معروف';
                                        @endphp
                                    </div>
                                </div>
                                <div class="current-image-actions">
                                    <button type="button" class="btn-view" onclick="viewImage('{{ asset('storage/' . $driver->personal_photo) }}')" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn-delete-image" onclick="deleteImage('personal_photo')" title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endif

                        <div class="file-upload">
                            <input type="file" name="personal_photo" class="file-upload-input" accept="image/*" onchange="previewImage(this, 'personal-photo')">
                            <div class="file-upload-area">
                                <div class="file-upload-icon">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                                <div class="file-upload-text">تغيير الصورة الشخصية</div>
                                <div class="file-upload-hint">PNG, JPG, JPEG (حد أقصى 2MB)</div>
                            </div>
                        </div>
                        <div class="image-preview-container" id="personal-photo-preview"></div>
                        @error('personal_photo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- ID Front -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-id-card"></i>
                            صورة الهوية - وجه
                            <span class="required">*</span>
                        </label>
                        
                        @if($driver->id_image_front)
                            <div class="current-image">
                                <img src="{{ asset('storage/' . $driver->id_image_front) }}" alt="ID Front">
                                <div class="current-image-info">
                                    <div class="current-image-name">صورة الهوية - وجه (حالية)</div>
                                    <div class="current-image-size">
                                        @php
                                            $size = Storage::disk('public')->exists($driver->id_image_front) ? Storage::disk('public')->size($driver->id_image_front) : 0;
                                            echo $size ? round($size / 1024, 2) . ' KB' : 'غير معروف';
                                        @endphp
                                    </div>
                                </div>
                                <div class="current-image-actions">
                                    <button type="button" class="btn-view" onclick="viewImage('{{ asset('storage/' . $driver->id_image_front) }}')" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn-delete-image" onclick="deleteImage('id_image_front')" title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endif

                        <div class="file-upload">
                            <input type="file" name="id_image_front" class="file-upload-input" accept="image/*" onchange="previewImage(this, 'id-front')">
                            <div class="file-upload-area">
                                <div class="file-upload-icon">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                                <div class="file-upload-text">تغيير صورة الهوية (الوجه)</div>
                                <div class="file-upload-hint">PNG, JPG, JPEG (حد أقصى 2MB)</div>
                            </div>
                        </div>
                        <div class="image-preview-container" id="id-front-preview"></div>
                        @error('id_image_front')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- ID Back -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-id-card"></i>
                            صورة الهوية - ظهر
                            <span class="required">*</span>
                        </label>
                        
                        @if($driver->id_image_back)
                            <div class="current-image">
                                <img src="{{ asset('storage/' . $driver->id_image_back) }}" alt="ID Back">
                                <div class="current-image-info">
                                    <div class="current-image-name">صورة الهوية - ظهر (حالية)</div>
                                    <div class="current-image-size">
                                        @php
                                            $size = Storage::disk('public')->exists($driver->id_image_back) ? Storage::disk('public')->size($driver->id_image_back) : 0;
                                            echo $size ? round($size / 1024, 2) . ' KB' : 'غير معروف';
                                        @endphp
                                    </div>
                                </div>
                                <div class="current-image-actions">
                                    <button type="button" class="btn-view" onclick="viewImage('{{ asset('storage/' . $driver->id_image_back) }}')" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn-delete-image" onclick="deleteImage('id_image_back')" title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endif

                        <div class="file-upload">
                            <input type="file" name="id_image_back" class="file-upload-input" accept="image/*" onchange="previewImage(this, 'id-back')">
                            <div class="file-upload-area">
                                <div class="file-upload-icon">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                                <div class="file-upload-text">تغيير صورة الهوية (الظهر)</div>
                                <div class="file-upload-hint">PNG, JPG, JPEG (حد أقصى 2MB)</div>
                            </div>
                        </div>
                        <div class="image-preview-container" id="id-back-preview"></div>
                        @error('id_image_back')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- License Front -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-id-card"></i>
                            صورة الرخصة - وجه
                            <span class="required">*</span>
                        </label>
                        
                        @if($driver->license_image_front)
                            <div class="current-image">
                                <img src="{{ asset('storage/' . $driver->license_image_front) }}" alt="License Front">
                                <div class="current-image-info">
                                    <div class="current-image-name">صورة الرخصة - وجه (حالية)</div>
                                    <div class="current-image-size">
                                        @php
                                            $size = Storage::disk('public')->exists($driver->license_image_front) ? Storage::disk('public')->size($driver->license_image_front) : 0;
                                            echo $size ? round($size / 1024, 2) . ' KB' : 'غير معروف';
                                        @endphp
                                    </div>
                                </div>
                                <div class="current-image-actions">
                                    <button type="button" class="btn-view" onclick="viewImage('{{ asset('storage/' . $driver->license_image_front) }}')" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn-delete-image" onclick="deleteImage('license_image_front')" title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endif

                        <div class="file-upload">
                            <input type="file" name="license_image_front" class="file-upload-input" accept="image/*" onchange="previewImage(this, 'license-front')">
                            <div class="file-upload-area">
                                <div class="file-upload-icon">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                                <div class="file-upload-text">تغيير صورة الرخصة (الوجه)</div>
                                <div class="file-upload-hint">PNG, JPG, JPEG (حد أقصى 2MB)</div>
                            </div>
                        </div>
                        <div class="image-preview-container" id="license-front-preview"></div>
                        @error('license_image_front')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- License Back -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-id-card"></i>
                            صورة الرخصة - ظهر
                            <span class="required">*</span>
                        </label>
                        
                        @if($driver->license_image_back)
                            <div class="current-image">
                                <img src="{{ asset('storage/' . $driver->license_image_back) }}" alt="License Back">
                                <div class="current-image-info">
                                    <div class="current-image-name">صورة الرخصة - ظهر (حالية)</div>
                                    <div class="current-image-size">
                                        @php
                                            $size = Storage::disk('public')->exists($driver->license_image_back) ? Storage::disk('public')->size($driver->license_image_back) : 0;
                                            echo $size ? round($size / 1024, 2) . ' KB' : 'غير معروف';
                                        @endphp
                                    </div>
                                </div>
                                <div class="current-image-actions">
                                    <button type="button" class="btn-view" onclick="viewImage('{{ asset('storage/' . $driver->license_image_back) }}')" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn-delete-image" onclick="deleteImage('license_image_back')" title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endif

                        <div class="file-upload">
                            <input type="file" name="license_image_back" class="file-upload-input" accept="image/*" onchange="previewImage(this, 'license-back')">
                            <div class="file-upload-area">
                                <div class="file-upload-icon">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                                <div class="file-upload-text">تغيير صورة الرخصة (الظهر)</div>
                                <div class="file-upload-hint">PNG, JPG, JPEG (حد أقصى 2MB)</div>
                            </div>
                        </div>
                        <div class="image-preview-container" id="license-back-preview"></div>
                        @error('license_image_back')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Vehicle Information Section -->
            <div class="form-card">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <div>
                        <h5 class="section-title">معلومات المركبة</h5>
                        <p class="section-description">بيانات المركبة المسجلة باسم السائق</p>
                    </div>
                </div>

                <div class="form-grid">
                    <!-- Vehicle Size -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-truck"></i>
                            حجم المركبة
                            <span class="required">*</span>
                        </label>
                        @php
                            // get all vehicle sizes from the database
                            $vehicle_sizes = DB::table('vehicle_sizes')->get();
                        @endphp
                        <select name="vehicle_size" class="form-select @error('vehicle_size') is-invalid @enderror" required>
                            <option value="">اختر حجم المركبة</option>
                            @foreach ($vehicle_sizes as $vehicle_size)
                                <option value="{{ $vehicle_size->name }}" {{ old('vehicle_size', $driver->vehicle_size) == $vehicle_size->name ? 'selected' : '' }}>{{ $vehicle_size->name }}</option>
                            @endforeach 
                        </select>
                        @error('vehicle_size')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Vehicle Plate Number -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-car"></i>
                            رقم اللوحة
                            <span class="required">*</span>
                        </label>
                        <input type="text" name="vehicle_plate_number" class="form-control @error('vehicle_plate_number') is-invalid @enderror" 
                            value="{{ old('vehicle_plate_number', $driver->vehicle_plate_number) }}" placeholder="أدخل رقم اللوحة" required>
                        @error('vehicle_plate_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Vehicle Registration Number -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-file-alt"></i>
                            رقم التسجيل
                            <span class="required">*</span>
                        </label>
                        <input type="text" name="vehicle_registration_number" class="form-control @error('vehicle_registration_number') is-invalid @enderror" 
                            value="{{ old('vehicle_registration_number', $driver->vehicle_registration_number) }}" placeholder="أدخل رقم التسجيل" required>
                        @error('vehicle_registration_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Vehicle Residency Number -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-id-card"></i>
                            رقم الاستمارة
                            <span class="required">*</span>
                        </label>
                        <input type="text" name="vehicle_residency_number" class="form-control @error('vehicle_residency_number') is-invalid @enderror" 
                            value="{{ old('vehicle_residency_number', $driver->vehicle_residency_number) }}" placeholder="أدخل رقم الاستمارة" required>
                        @error('vehicle_residency_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Vehicle Ownership -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-user-tie"></i>
                            ملكية المركبة
                            <span class="required">*</span>
                        </label>
                        <div class="ownership-toggle">
                            <div class="ownership-option owner">
                                <input type="radio" name="is_vehicle_owner" id="owner_yes" value="1" 
                                    {{ old('is_vehicle_owner', $driver->is_vehicle_owner) == '1' ? 'checked' : '' }} required>
                                <label for="owner_yes">
                                    <i class="fas fa-check-circle"></i>
                                    مالك
                                </label>
                            </div>
                            <div class="ownership-option not-owner">
                                <input type="radio" name="is_vehicle_owner" id="owner_no" value="0" 
                                    {{ old('is_vehicle_owner', $driver->is_vehicle_owner) == '0' ? 'checked' : '' }} required>
                                <label for="owner_no">
                                    <i class="fas fa-times-circle"></i>
                                    غير مالك
                                </label>
                            </div>
                        </div>
                        @error('is_vehicle_owner')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Vehicle Registration Image -->
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-file-image"></i>
                        صورة رخصة السير (الاستمارة)
                        <span class="required">*</span>
                    </label>
                    
                    @if($driver->vehicle_registration_image)
                        <div class="current-image">
                            <img src="{{ asset('storage/' . $driver->vehicle_registration_image) }}" alt="Vehicle Registration">
                            <div class="current-image-info">
                                <div class="current-image-name">صورة الاستمارة الحالية</div>
                                <div class="current-image-size">
                                    @php
                                        $size = Storage::disk('public')->exists($driver->vehicle_registration_image) ? Storage::disk('public')->size($driver->vehicle_registration_image) : 0;
                                        echo $size ? round($size / 1024, 2) . ' KB' : 'غير معروف';
                                    @endphp
                                </div>
                            </div>
                            <div class="current-image-actions">
                                <button type="button" class="btn-view" onclick="viewImage('{{ asset('storage/' . $driver->vehicle_registration_image) }}')" title="عرض">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn-delete-image" onclick="deleteImage('vehicle_registration_image')" title="حذف">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    @endif

                    <div class="file-upload">
                        <input type="file" name="vehicle_registration_image" class="file-upload-input" accept="image/*" onchange="previewImage(this, 'vehicle-reg')">
                        <div class="file-upload-area">
                            <div class="file-upload-icon">
                                <i class="fas fa-cloud-upload-alt"></i>
                            </div>
                            <div class="file-upload-text">تغيير صورة الاستمارة</div>
                            <div class="file-upload-hint">PNG, JPG, JPEG (حد أقصى 2MB)</div>
                        </div>
                    </div>
                    <div class="image-preview-container" id="vehicle-reg-preview"></div>
                    @error('vehicle_registration_image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <a href="{{ route('admin.drivers.index') }}" class="btn-cancel">
                    <i class="fas fa-times"></i>
                    إلغاء
                </a>
                <button type="submit" class="btn-submit">
                    <i class="fas fa-save"></i>
                    حفظ التغييرات
                </button>
                @if(!$driver->is_verified && !$driver->rejection_reason)
                    <button type="button" class="btn-submit" onclick="approveDriver({{ $driver->id }})" style="background: linear-gradient(135deg, #198754 0%, #20c997 100%);">
                        <i class="fas fa-check"></i>
                        توثيق السائق
                    </button>
                @endif
                @if($driver->is_verified || $driver->rejection_reason)
                    <button type="button" class="btn-danger" onclick="resetVerification({{ $driver->id }})">
                        <i class="fas fa-undo"></i>
                        إعادة تعيين التحقق
                    </button>
                @endif
            </div>
        </form>
    </div>

    <!-- Image Viewer Modal -->
    <div class="modal fade" id="imageViewerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">عرض الصورة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="" alt="Image" class="img-fluid" id="viewerImage">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    <a href="#" class="btn btn-primary" id="downloadImage" download>
                        <i class="fas fa-download"></i> تحميل
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('select[name="user_id"]').select2({
                placeholder: 'اختر حساب المستخدم',
                allowClear: true,
                width: '100%'
            });

            // Citizenship toggle
            $('input[name="citizenship"]').change(function() {
                const value = $(this).val();
                if (value === 'saudi') {
                    $('.saudi-fields').show();
                    $('.resident-fields').hide();
                    $('input[name="national_id"]').prop('required', true);
                    $('input[name="iqama_number"]').prop('required', false);
                    $('input[name="iqama_expiry_date"]').prop('required', false);
                } else {
                    $('.saudi-fields').hide();
                    $('.resident-fields').show();
                    $('input[name="national_id"]').prop('required', false);
                    $('input[name="iqama_number"]').prop('required', true);
                    $('input[name="iqama_expiry_date"]').prop('required', true);
                }
            });
        });

        // Image preview
        function previewImage(input, previewId) {
            const previewContainer = $('#' + previewId + '-preview');
            previewContainer.empty();
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const previewItem = `
                        <div class="image-preview-item">
                            <img src="${e.target.result}" alt="Preview">
                            <button type="button" class="image-preview-remove" onclick="removeImage(this, '${previewId}')">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                    previewContainer.append(previewItem);
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }

        function removeImage(button, previewId) {
            $(button).closest('.image-preview-item').remove();
            $('input[name="' + previewId.replace('-preview', '') + '"]').val('');
        }

        // View image
        function viewImage(src) {
            $('#viewerImage').attr('src', src);
            $('#downloadImage').attr('href', src);
            $('#imageViewerModal').modal('show');
        }

        // Delete image (AJAX)
        function deleteImage(field) {
            Swal.fire({
                title: 'تأكيد الحذف',
                text: 'هل أنت متأكد من حذف هذه الصورة؟',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("admin.drivers.delete-image", $driver->id) }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            field: field
                        },
                        beforeSend: function() {
                            Swal.fire({
                                title: 'جاري الحذف...',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'تم الحذف!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ!',
                                text: xhr.responseJSON?.message || 'حدث خطأ أثناء الحذف'
                            });
                        }
                    });
                }
            });
        }

        // Approve driver
        function approveDriver(driverId) {
            Swal.fire({
                title: 'توثيق السائق',
                text: 'هل أنت متأكد من توثيق هذا السائق؟',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#d33',
                confirmButtonText: 'نعم، وثق',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/admin/drivers/${driverId}/approve`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        beforeSend: function() {
                            Swal.fire({
                                title: 'جاري التوثيق...',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'تم التوثيق!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ!',
                                text: xhr.responseJSON?.message || 'حدث خطأ أثناء التوثيق'
                            });
                        }
                    });
                }
            });
        }

        // Reset verification
        function resetVerification(driverId) {
            Swal.fire({
                title: 'إعادة تعيين التحقق',
                text: 'هل أنت متأكد من إعادة تعيين حالة التحقق؟ سيصبح السائق في قائمة الانتظار',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#fd7e14',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'نعم، إعادة تعيين',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/admin/drivers/${driverId}/reset-verification`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        beforeSend: function() {
                            Swal.fire({
                                title: 'جاري إعادة التعيين...',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'تم إعادة التعيين!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ!',
                                text: xhr.responseJSON?.message || 'حدث خطأ أثناء إعادة التعيين'
                            });
                        }
                    });
                }
            });
        }

        // Display error messages from server
        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'خطأ في البيانات',
                html: '<ul style="text-align: right;">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
                confirmButtonText: 'حسناً'
            });
        @endif

        // Display success message
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'تم بنجاح',
                text: '{{ session('success') }}',
                confirmButtonText: 'حسناً'
            });
        @endif

        // Display error message
        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: '{{ session('error') }}',
                confirmButtonText: 'حسناً'
            });
        @endif
    </script>
@endsection