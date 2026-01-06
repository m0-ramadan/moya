@extends('Admin.layout.master')

@section('title', 'إعدادات التواصل')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .social-media-card {
            border-radius: 12px;
            border: 1px solid var(--bs-border-color);
            transition: all 0.3s ease;
            height: 100%;
        }

        .social-media-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border-color: #696cff;
        }

        .social-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 15px;
            color: white;
        }

        .phone-icon { background: linear-gradient(135deg, #25D366, #128C7E); }
        .whatsapp-icon { background: linear-gradient(135deg, #25D366, #128C7E); }
        .facebook-icon { background: linear-gradient(135deg, #1877F2, #0D5F9A); }
        .twitter-icon { background: linear-gradient(135deg, #1DA1F2, #0A84C4); }
        .instagram-icon { background: linear-gradient(135deg, #E4405F, #C13584); }
        .linkedin-icon { background: linear-gradient(135deg, #0077B5, #005582); }
        .telegram-icon { background: linear-gradient(135deg, #0088CC, #006699); }
        .email-icon { background: linear-gradient(135deg, #EA4335, #D14836); }
        .youtube-icon { background: linear-gradient(135deg, #FF0000, #CC0000); }
        .tiktok-icon { background: linear-gradient(135deg, #000000, #69C9D0); }
        .default-icon { background: linear-gradient(135deg, #696cff, #5a5fcf); }

        .social-title {
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 10px;
            color: var(--bs-heading-color);
        }

        .social-value {
            color: var(--bs-secondary-color);
            font-size: 14px;
            word-break: break-word;
        }

        .social-actions {
            position: absolute;
            top: 15px;
            left: 15px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .social-media-card:hover .social-actions {
            opacity: 1;
        }

        .action-btn {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 5px;
        }

        .edit-form {
            background: var(--bs-light-bg-subtle);
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            border: 1px solid var(--bs-border-color);
        }

        .icon-preview {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            background: var(--bs-light-bg-subtle);
            border: 1px solid var(--bs-border-color);
        }

        .status-badge {
            font-size: 12px;
            padding: 3px 10px;
            border-radius: 20px;
        }

        .status-active {
            background: rgba(40, 199, 111, 0.1);
            color: #28c76f;
            border: 1px solid rgba(40, 199, 111, 0.3);
        }

        .status-inactive {
            background: rgba(234, 84, 85, 0.1);
            color: #ea5455;
            border: 1px solid rgba(234, 84, 85, 0.3);
        }

        .search-box {
            max-width: 300px;
        }

        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid var(--bs-border-color);
        }

        .table th {
            background: var(--bs-light-bg-subtle);
            border-bottom: 2px solid var(--bs-border-color);
            font-weight: 600;
            padding: 15px;
        }

        .table td {
            padding: 15px;
            vertical-align: middle;
        }

        .form-check-input:checked {
            background-color: #696cff;
            border-color: #696cff;
        }

        .bulk-actions {
            background: var(--bs-light-bg-subtle);
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid var(--bs-border-color);
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
                <li class="breadcrumb-item active">إعدادات التواصل</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">إعدادات التواصل</h5>
                            <small class="text-muted">إدارة جميع وسائل التواصل الخاصة بالموقع</small>
                        </div>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bulkEditModal">
                            <i class="fas fa-edit me-1"></i> التعديل السريع
                        </button>
                    </div>

                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <!-- Bulk Actions -->
                        <div class="bulk-actions mb-4">
                            <form action="{{ route('admin.social-media.bulk-update') }}" method="POST" id="bulkUpdateForm">
                                @csrf
                                <div class="row">
                                    <div class="col-md-10">
                                        <p class="mb-0"><i class="fas fa-info-circle text-primary me-2"></i>استخدم التعديل السريع لتحديث جميع الحقول مرة واحدة</p>
                                    </div>
                                    <div class="col-md-2 text-end">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-save me-1"></i> حفظ الكل
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Social Media Grid -->
                        <div class="row g-4">
                            @foreach($socialMedia as $social)
                                <div class="col-xl-3 col-lg-4 col-md-6">
                                    <div class="social-media-card position-relative p-4">
                                        <div class="social-actions">
                                            <a href="{{ route('admin.social-media.edit', $social->id) }}" 
                                               class="btn btn-primary btn-sm action-btn" 
                                               title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>

                                        <div class="text-center">
                                            @php
                                                $iconClass = str_replace(['fab fa-', 'fas fa-', ' '], '', $social->icon);
                                                $iconClass = $iconClass . '-icon';
                                            @endphp
                                            <div class="social-icon mx-auto {{ $iconClass }} default-icon">
                                                <i class="{{ $social->icon }}"></i>
                                            </div>

                                            <h6 class="social-title">{{ $social->key }}</h6>
                                            
                                            <div class="social-value mb-3">
                                                @if($social->value)
                                                    @if(filter_var($social->value, FILTER_VALIDATE_URL))
                                                        <a href="{{ $social->value }}" target="_blank" class="text-primary">
                                                            {{ Str::limit($social->value, 30) }}
                                                        </a>
                                                    @elseif(filter_var($social->value, FILTER_VALIDATE_EMAIL))
                                                        <a href="mailto:{{ $social->value }}" class="text-primary">
                                                            {{ $social->value }}
                                                        </a>
                                                    @elseif(preg_match('/^[0-9+\-\s]+$/', $social->value))
                                                        <a href="tel:{{ $social->value }}" class="text-primary">
                                                            {{ $social->value }}
                                                        </a>
                                                    @else
                                                        {{ Str::limit($social->value, 50) }}
                                                    @endif
                                                @else
                                                    <span class="text-muted">غير محدد</span>
                                                @endif
                                            </div>

                                            <span class="status-badge {{ $social->value ? 'status-active' : 'status-inactive' }}">
                                                {{ $social->value ? 'مفعل' : 'غير مفعل' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Bulk Edit Modal -->
                        <div class="modal fade" id="bulkEditModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">التعديل السريع لجميع وسائل التواصل</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('admin.social-media.bulk-update') }}" method="POST" id="quickEditForm">
                                            @csrf
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th width="30%">الوسيلة</th>
                                                            <th width="50%">القيمة</th>
                                                            <th width="20%">الأيقونة</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($socialMedia as $social)
                                                            <tr>
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="icon-preview me-3">
                                                                            <i class="{{ $social->icon }}"></i>
                                                                        </div>
                                                                        <div>
                                                                            <strong>{{ $social->key }}</strong>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <input type="text" 
                                                                           class="form-control" 
                                                                           name="value_{{ $social->id }}" 
                                                                           value="{{ old('value_' . $social->id, $social->value) }}"
                                                                           placeholder="أدخل القيمة...">
                                                                </td>
                                                                <td>
                                                                    <input type="text" 
                                                                           class="form-control" 
                                                                           name="icon_{{ $social->id }}" 
                                                                           value="{{ old('icon_' . $social->id, $social->icon) }}"
                                                                           placeholder="أيقونة FontAwesome">
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                                        <button type="submit" form="quickEditForm" class="btn btn-primary">حفظ جميع التغييرات</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // تحسين عرض القيم
            $('.social-value a').hover(
                function() {
                    $(this).css('text-decoration', 'underline');
                },
                function() {
                    $(this).css('text-decoration', 'none');
                }
            );

            // تأكيد قبل الحفظ
            $('#quickEditForm').on('submit', function(e) {
                const hasChanges = $(this).serialize() !== $(this).data('original');
                if (!hasChanges) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'info',
                        title: 'لا توجد تغييرات',
                        text: 'لم تقم بإجراء أي تغييرات لحفظها',
                        confirmButtonText: 'حسناً'
                    });
                }
            });

            // حفظ حالة النموذج الأصلي
            $('#quickEditForm').data('original', $('#quickEditForm').serialize());

            // البحث في الجدول
            $('input[name="search"]').on('keyup', function() {
                const value = $(this).val().toLowerCase();
                $('table tbody tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });

            // رسالة تحذير عند مغادرة الصفحة مع تغييرات غير محفوظة
            let formChanged = false;
            
            $('#quickEditForm input, #quickEditForm textarea').on('change input', function() {
                formChanged = true;
            });

            $(window).on('beforeunload', function() {
                if (formChanged) {
                    return 'لديك تغييرات غير محفوظة. هل أنت متأكد أنك تريد مغادرة الصفحة؟';
                }
            });

            $('#quickEditForm').on('submit', function() {
                formChanged = false;
            });
        });
    </script>
@endsection