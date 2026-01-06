@extends('Admin.layout.master')

@section('title', $staticPage->title)

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

        .card {
            background: var(--dark-card);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .card-header {
            background: var(--primary-gradient);
            color: white;
            padding: 25px 30px;
            border-radius: 15px 15px 0 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .info-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }

        .info-label {
            color: rgba(255, 255, 255, 0.7);
            font-weight: 600;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .info-value {
            color: #fff;
            font-size: 16px;
            margin-bottom: 15px;
        }

        .badge-status {
            padding: 6px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 13px;
        }

        .status-active {
            background: linear-gradient(135deg, rgba(21, 87, 36, 0.2) 0%, rgba(32, 201, 151, 0.2) 100%);
            color: #20c997;
            border: 1px solid rgba(32, 201, 151, 0.3);
        }

        .status-inactive {
            background: rgba(133, 100, 4, 0.2);
            color: #ffc107;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }

        .content-preview {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 25px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 20px;
            line-height: 1.8;
        }

        .content-preview h1,
        .content-preview h2,
        .content-preview h3 {
            color: #fff;
            margin-top: 20px;
            margin-bottom: 15px;
        }

        .content-preview p {
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 15px;
        }

        .content-preview ul,
        .content-preview ol {
            color: rgba(255, 255, 255, 0.8);
            padding-right: 20px;
            margin-bottom: 15px;
        }

        .content-preview li {
            margin-bottom: 8px;
        }

        .btn-primary {
            background: var(--primary-gradient);
            border: none;
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
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.3);
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            color: white;
        }

        .timeline {
            position: relative;
            padding-right: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            width: 2px;
            background: var(--primary-color);
        }

        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            right: -34px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--primary-color);
            border: 2px solid var(--dark-card);
        }

        .timeline-date {
            color: rgba(255, 255, 255, 0.7);
            font-size: 12px;
            margin-bottom: 5px;
        }

        .timeline-content {
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
        }

        .url-preview {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 10px 15px;
            display: inline-block;
            margin: 5px 0;
        }

        .url-preview code {
            color: #20c997;
            font-weight: 600;
        }

        .stats-box {
            text-align: center;
            padding: 20px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }

        .stats-number {
            font-size: 24px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 5px;
        }

        .stats-label {
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
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
                    <a href="{{ route('admin.static-pages.index') }}">الصفحات الثابتة</a>
                </li>
                <li class="breadcrumb-item active">{{ $staticPage->title }}</li>
            </ol>
        </nav>

        <div class="row" bis_skin_checked="1">
            <div class="col-lg-8" bis_skin_checked="1">
                <!-- معلومات الصفحة -->
                <div class="card mb-4" bis_skin_checked="1">
                    <div class="card-header" bis_skin_checked="1">
                        <div class="d-flex justify-content-between align-items-center" bis_skin_checked="1">
                            <div bis_skin_checked="1">
                                <h5 class="mb-0">{{ $staticPage->title }}</h5>
                                <small class="opacity-75">عرض تفاصيل الصفحة</small>
                            </div>
                            <div class="d-flex gap-2" bis_skin_checked="1">
                                <a href="{{ route('admin.static-pages.edit', $staticPage) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-2"></i>تعديل
                                </a>
                                <a href="{{ route('admin.static-pages.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-right me-2"></i>رجوع
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body" bis_skin_checked="1">
                        <div class="row" bis_skin_checked="1">
                            <div class="col-md-6 mb-3" bis_skin_checked="1">
                                <div class="info-label" bis_skin_checked="1">
                                    <i class="fas fa-heading me-2"></i>عنوان الصفحة
                                </div>
                                <div class="info-value" bis_skin_checked="1">
                                    {{ $staticPage->title }}
                                </div>
                            </div>

                            <div class="col-md-6 mb-3" bis_skin_checked="1">
                                <div class="info-label" bis_skin_checked="1">
                                    <i class="fas fa-link me-2"></i>الرابط (Slug)
                                </div>
                                <div class="info-value" bis_skin_checked="1">
                                    <div class="url-preview" bis_skin_checked="1">
                                        <code>/page/{{ $staticPage->slug }}</code>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3" bis_skin_checked="1">
                                <div class="info-label" bis_skin_checked="1">
                                    <i class="fas fa-toggle-on me-2"></i>حالة الصفحة
                                </div>
                                <div class="info-value" bis_skin_checked="1">
                                    <span class="badge-status status-{{ $staticPage->status }}">
                                        @if ($staticPage->status == 'active')
                                            <i class="fas fa-check-circle me-1"></i> نشط
                                        @else
                                            <i class="fas fa-times-circle me-1"></i> غير نشط
                                        @endif
                                    </span>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3" bis_skin_checked="1">
                                <div class="info-label" bis_skin_checked="1">
                                    <i class="fas fa-tags me-2"></i>الكلمات المفتاحية
                                </div>
                                <div class="info-value" bis_skin_checked="1">
                                    @if ($staticPage->meta_keywords)
                                        <div class="d-flex flex-wrap gap-2" bis_skin_checked="1">
                                            @foreach (explode(',', $staticPage->meta_keywords) as $keyword)
                                                <span class="badge bg-secondary">{{ trim($keyword) }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted">لا توجد كلمات مفتاحية</span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-12 mb-3" bis_skin_checked="1">
                                <div class="info-label" bis_skin_checked="1">
                                    <i class="fas fa-search me-2"></i>Meta Title
                                </div>
                                <div class="info-value" bis_skin_checked="1">
                                    {{ $staticPage->meta_title ?: $staticPage->title }}
                                </div>
                            </div>

                            <div class="col-12 mb-3" bis_skin_checked="1">
                                <div class="info-label" bis_skin_checked="1">
                                    <i class="fas fa-align-left me-2"></i>Meta Description
                                </div>
                                <div class="info-value" bis_skin_checked="1">
                                    {{ $staticPage->meta_description ?: 'لا يوجد وصف' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- محتوى الصفحة -->
                <div class="card" bis_skin_checked="1">
                    <div class="card-header" bis_skin_checked="1">
                        <h5 class="mb-0">
                            <i class="fas fa-file-alt me-2"></i>محتوى الصفحة
                        </h5>
                    </div>
                    <div class="card-body" bis_skin_checked="1">
                        <div class="content-preview" bis_skin_checked="1">
                            {!! $staticPage->content !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4" bis_skin_checked="1">
                <!-- الإحصائيات -->
                <div class="card mb-4" bis_skin_checked="1">
                    <div class="card-header" bis_skin_checked="1">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-bar me-2"></i>إحصائيات
                        </h5>
                    </div>
                    <div class="card-body" bis_skin_checked="1">
                        <div class="row" bis_skin_checked="1">
                            <div class="col-6 mb-3" bis_skin_checked="1">
                                <div class="stats-box" bis_skin_checked="1">
                                    <div class="stats-number" bis_skin_checked="1">
                                        {{ strlen(strip_tags($staticPage->content)) }}
                                    </div>
                                    <div class="stats-label" bis_skin_checked="1">عدد الأحرف</div>
                                </div>
                            </div>
                            <div class="col-6 mb-3" bis_skin_checked="1">
                                <div class="stats-box" bis_skin_checked="1">
                                    <div class="stats-number" bis_skin_checked="1">
                                        {{ count(explode(' ', strip_tags($staticPage->content))) }}
                                    </div>
                                    <div class="stats-label" bis_skin_checked="1">عدد الكلمات</div>
                                </div>
                            </div>
                            <div class="col-6" bis_skin_checked="1">
                                <div class="stats-box" bis_skin_checked="1">
                                    <div class="stats-number" bis_skin_checked="1">
                                        {{ strlen($staticPage->meta_title ?: $staticPage->title) }}
                                    </div>
                                    <div class="stats-label" bis_skin_checked="1">طول العنوان</div>
                                </div>
                            </div>
                            <div class="col-6" bis_skin_checked="1">
                                <div class="stats-box" bis_skin_checked="1">
                                    <div class="stats-number" bis_skin_checked="1">
                                        {{ strlen($staticPage->meta_description ?: '') }}
                                    </div>
                                    <div class="stats-label" bis_skin_checked="1">طول الوصف</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- الإجراءات -->
                <div class="card mb-4" bis_skin_checked="1">
                    <div class="card-header" bis_skin_checked="1">
                        <h5 class="mb-0">
                            <i class="fas fa-cog me-2"></i>الإجراءات
                        </h5>
                    </div>
                    <div class="card-body" bis_skin_checked="1">
                        <div class="d-grid gap-2" bis_skin_checked="1">
                            <a href="{{ route('admin.static-pages.edit', $staticPage) }}" class="btn btn-primary">
                                <i class="fas fa-edit me-2"></i>تعديل الصفحة
                            </a>

                            @if (!in_array($staticPage->slug, ['syas-alkhsosy', 'syas-alastrgaaa', 'aldman', 'mn-nhn', 'alshrot-oalahkam']))
                                <button type="button" class="btn btn-danger"
                                    onclick="deletePage({{ $staticPage->id }}, '{{ $staticPage->title }}')">
                                    <i class="fas fa-trash me-2"></i>حذف الصفحة
                                </button>
                            @endif

                            <a href="/page/{{ $staticPage->slug }}" target="_blank" class="btn btn-outline-primary">
                                <i class="fas fa-external-link-alt me-2"></i>عرض في الموقع
                            </a>

                            <a href="{{ route('admin.static-pages.create') }}" class="btn btn-success">
                                <i class="fas fa-plus me-2"></i>إنشاء صفحة جديدة
                            </a>
                        </div>
                    </div>
                </div>

                <!-- السجل الزمني -->
                <div class="card" bis_skin_checked="1">
                    <div class="card-header" bis_skin_checked="1">
                        <h5 class="mb-0">
                            <i class="fas fa-history me-2"></i>السجل الزمني
                        </h5>
                    </div>
                    <div class="card-body" bis_skin_checked="1">
                        <div class="timeline" bis_skin_checked="1">
                            <div class="timeline-item" bis_skin_checked="1">
                                <div class="timeline-date" bis_skin_checked="1">
                                    {{ $staticPage->created_at->translatedFormat('d M Y - h:i A') }}
                                </div>
                                <div class="timeline-content" bis_skin_checked="1">
                                    <i class="fas fa-plus-circle me-2"></i>تم إنشاء الصفحة
                                </div>
                            </div>

                            <div class="timeline-item" bis_skin_checked="1">
                                <div class="timeline-date" bis_skin_checked="1">
                                    {{ $staticPage->updated_at->translatedFormat('d M Y - h:i A') }}
                                </div>
                                <div class="timeline-content" bis_skin_checked="1">
                                    <i class="fas fa-edit me-2"></i>آخر تحديث
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function deletePage(pageId, pageName) {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: `سيتم حذف الصفحة "${pageName}" نهائياً`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.static-pages.destroy', '') }}/" + pageId,
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            _method: 'DELETE'
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'تم الحذف',
                                text: response.success,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href =
                                    "{{ route('admin.static-pages.index') }}";
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ',
                                text: xhr.responseJSON?.error || 'حدث خطأ أثناء الحذف',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    });
                }
            });
        }

        // نسخ الرابط
        function copyUrl() {
            const url = `/page/{{ $staticPage->slug }}`;
            navigator.clipboard.writeText(url).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'تم النسخ',
                    text: 'تم نسخ الرابط إلى الحافظة',
                    timer: 1500,
                    showConfirmButton: false
                });
            });
        }

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
