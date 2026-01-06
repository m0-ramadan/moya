@extends('Admin.layout.master')

@section('title', 'الأخطاء البرمجية - سجلات النظام')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/atom-one-dark.min.css">
    <style>
        .error-log-card {
            border-radius: 12px;
            border: 1px solid var(--bs-border-color);
            height: 100%;
            transition: all 0.3s ease;
        }

        .error-log-card:hover {
            border-color: #696cff;
            box-shadow: 0 5px 20px rgba(105, 108, 255, 0.1);
        }

        .log-file-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 15px;
            color: #696cff;
            background: rgba(105, 108, 255, 0.1);
        }

        .log-file-icon.warning {
            color: #ff9f43;
            background: rgba(255, 159, 67, 0.1);
        }

        .log-file-icon.danger {
            color: #ea5455;
            background: rgba(234, 84, 85, 0.1);
        }

        .log-file-size {
            font-size: 12px;
            color: var(--bs-secondary-color);
            background: var(--bs-light-bg-subtle);
            padding: 2px 8px;
            border-radius: 10px;
        }

        .log-content-container {
            background: #1e1e1e;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            max-height: 600px;
            overflow-y: auto;
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 13px;
            line-height: 1.6;
        }

        .log-line {
            padding: 2px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .log-line:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .line-number {
            color: #6c757d;
            padding-right: 10px;
            user-select: none;
            min-width: 50px;
            text-align: left;
        }

        .log-error {
            color: #ff6b6b;
        }

        .log-warning {
            color: #ffd93d;
        }

        .log-info {
            color: #4dabf7;
        }

        .log-debug {
            color: #51cf66;
        }

        .log-critical {
            color: #ff8787;
            background: rgba(255, 135, 135, 0.1);
            padding: 2px 5px;
            border-radius: 3px;
        }

        .log-stack {
            background: rgba(255, 255, 255, 0.05);
            padding: 10px;
            margin: 5px 0;
            border-left: 3px solid #ff6b6b;
            border-radius: 0 5px 5px 0;
        }

        .timestamp {
            color: #20c997;
            font-weight: 500;
        }

        .error-type {
            color: #ff922b;
            font-weight: 600;
        }

        .search-highlight {
            background: #ffd43b;
            color: #000;
            padding: 0 2px;
            border-radius: 2px;
        }

        .log-stats {
            background: var(--bs-light-bg-subtle);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .stat-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .stat-item:last-child {
            border-bottom: none;
        }

        .stat-value {
            font-weight: 600;
            color: var(--bs-heading-color);
        }

        .badge-error {
            background: linear-gradient(135deg, #ea5455, #e73d3e);
            color: white;
        }

        .badge-warning {
            background: linear-gradient(135deg, #ff9f43, #ff8c2e);
            color: white;
        }

        .badge-info {
            background: linear-gradient(135deg, #696cff, #5a5fcf);
            color: white;
        }

        .file-list-container {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid var(--bs-border-color);
            border-radius: 8px;
            padding: 10px;
        }

        .file-item {
            display: flex;
            align-items: center;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 5px;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .file-item:hover {
            background: var(--bs-light-bg-subtle);
        }

        .file-item.active {
            background: rgba(105, 108, 255, 0.1);
            border-left: 3px solid #696cff;
        }

        .file-icon {
            margin-left: 10px;
            color: #696cff;
        }

        .file-name {
            flex: 1;
            font-weight: 500;
        }

        .file-size {
            font-size: 12px;
            color: var(--bs-secondary-color);
            background: var(--bs-light-bg-subtle);
            padding: 2px 8px;
            border-radius: 10px;
        }

        .file-date {
            font-size: 11px;
            color: var(--bs-secondary-color);
            margin-top: 2px;
        }

        .btn-log-action {
            padding: 5px 15px;
            font-size: 13px;
        }

        .log-level-filter {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
            margin-bottom: 15px;
        }

        .log-level-badge {
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }

        .log-level-badge:hover {
            opacity: 0.8;
        }

        .log-level-badge.active {
            border-color: white;
            box-shadow: 0 0 0 2px currentColor;
        }

        .scroll-to-top {
            position: fixed;
            bottom: 20px;
            left: 20px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #696cff;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s ease;
            box-shadow: 0 3px 10px rgba(105, 108, 255, 0.3);
        }

        .scroll-to-top.show {
            opacity: 1;
        }

        .log-line pre {
            margin: 0;
            white-space: pre-wrap;
            word-wrap: break-word;
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
                <li class="breadcrumb-item active">الأخطاء البرمجية</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">سجلات الأخطاء البرمجية</h5>
                            <small class="text-muted">مراقبة وتتبع أخطاء النظام والاستثناءات</small>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.errors.php-errors') }}" class="btn btn-outline-warning btn-sm">
                                <i class="fas fa-bug me-1"></i> أخطاء PHP
                            </a>
                            <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#clearAllModal">
                                <i class="fas fa-trash-alt me-1"></i> تفريغ الكل
                            </button>
                        </div>
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

                        <!-- Statistics -->
                        <div class="log-stats">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="stat-item">
                                        <span>عدد ملفات السجل:</span>
                                        <span class="stat-value">{{ count($logFiles) }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="stat-item">
                                        <span>الحجم الإجمالي:</span>
                                        <span class="stat-value">
                                            @php
                                                $totalSize = 0;
                                                foreach ($logFiles as $file) {
                                                    $size = str_replace(['KB', 'MB', 'GB', 'TB', 'B', ' '], '', $file['size']);
                                                    $unit = substr($file['size'], -2);
                                                    $multiplier = match($unit) {
                                                        'KB' => 1024,
                                                        'MB' => 1024 * 1024,
                                                        'GB' => 1024 * 1024 * 1024,
                                                        'TB' => 1024 * 1024 * 1024 * 1024,
                                                        default => 1
                                                    };
                                                    $totalSize += $size * $multiplier;
                                                }
                                                echo formatBytes($totalSize);
                                            @endphp
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="stat-item">
                                        <span>آخر تحديث:</span>
                                        <span class="stat-value">
                                            {{ $logFiles[0]['modified'] ?? 'غير متوفر' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="stat-item">
                                        <span>الحالة:</span>
                                        <span class="badge bg-success">نشط</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Left Column: File List -->
                            <div class="col-lg-4 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">ملفات السجل</h6>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="file-list-container">
                                            @foreach($logFiles as $file)
                                                <div class="file-item {{ $selectedFile == $file['name'] ? 'active' : '' }}"
                                                     onclick="window.location.href='?file={{ $file['name'] }}'">
                                                    <div class="file-icon">
                                                        @if(str_contains($file['name'], 'laravel'))
                                                            <i class="fas fa-file-code text-primary"></i>
                                                        @elseif(str_contains($file['name'], 'error'))
                                                            <i class="fas fa-exclamation-triangle text-danger"></i>
                                                        @else
                                                            <i class="fas fa-file-alt text-secondary"></i>
                                                        @endif
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <div class="file-name">{{ $file['name'] }}</div>
                                                        <div class="file-date">
                                                            <i class="fas fa-clock me-1"></i>
                                                            {{ $file['modified'] }}
                                                        </div>
                                                    </div>
                                                    <div class="file-size">{{ $file['size'] }}</div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            إضغط على الملف لعرض محتوياته
                                        </small>
                                    </div>
                                </div>

                                <!-- File Actions -->
                                <div class="card mt-3">
                                    <div class="card-body">
                                        <h6 class="mb-3">إجراءات الملف</h6>
                                        <div class="d-grid gap-2">
                                            <a href="{{ route('admin.errors.download', $selectedFile) }}" 
                                               class="btn btn-outline-primary btn-log-action">
                                                <i class="fas fa-download me-1"></i> تحميل الملف
                                            </a>
                                            
                                            <button type="button" 
                                                    class="btn btn-outline-danger btn-log-action"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deleteModal"
                                                    data-file="{{ $selectedFile }}">
                                                <i class="fas fa-trash me-1"></i> حذف الملف
                                            </button>

                                            <!-- Search Form -->
                                            <form action="{{ route('admin.errors.search') }}" method="GET" class="mt-3">
                                                <div class="input-group">
                                                    <input type="text" 
                                                           name="search" 
                                                           class="form-control" 
                                                           placeholder="بحث في السجلات..."
                                                           required>
                                                    <input type="hidden" name="file" value="{{ $selectedFile }}">
                                                    <button class="btn btn-primary" type="submit">
                                                        <i class="fas fa-search"></i>
                                                    </button>
                                                </div>
                                                <small class="text-muted mt-1 d-block">ابحث عن كلمة أو جملة محددة</small>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column: Log Content -->
                            <div class="col-lg-8">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0">{{ $selectedFile }}</h6>
                                            <small class="text-muted">آخر تحديث: {{ now()->format('Y-m-d H:i:s') }}</small>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-sm btn-outline-secondary" onclick="copyLogContent()">
                                                <i class="fas fa-copy me-1"></i> نسخ
                                            </button>
                                            <button class="btn btn-sm btn-outline-secondary" onclick="refreshLog()">
                                                <i class="fas fa-sync-alt me-1"></i> تحديث
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="log-content-container" id="logContent">
                                            @if(empty($logContent))
                                                <div class="text-center py-5 text-muted">
                                                    <i class="fas fa-file-alt fa-3x mb-3"></i>
                                                    <p>لا توجد محتويات في ملف السجل المحدد</p>
                                                </div>
                                            @else
                                                @php
                                                    $lines = explode("\n", $logContent);
                                                    $lineCount = count($lines);
                                                @endphp
                                                
                                                @for($i = 0; $i < $lineCount; $i++)
                                                    @php
                                                        $line = $lines[$i];
                                                        $lineClass = 'text-white';
                                                        
                                                        if (str_contains($line, 'ERROR')) {
                                                            $lineClass = 'log-error';
                                                        } elseif (str_contains($line, 'WARNING')) {
                                                            $lineClass = 'log-warning';
                                                        } elseif (str_contains($line, 'INFO')) {
                                                            $lineClass = 'log-info';
                                                        } elseif (str_contains($line, 'DEBUG')) {
                                                            $lineClass = 'log-debug';
                                                        } elseif (str_contains($line, 'CRITICAL') || str_contains($line, 'EMERGENCY')) {
                                                            $lineClass = 'log-critical';
                                                        }
                                                    @endphp
                                                    
                                                    <div class="log-line d-flex">
                                                        <span class="line-number">{{ $i + 1 }}</span>
                                                        <pre class="flex-grow-1 {{ $lineClass }} mb-0">{{ $line }}</pre>
                                                    </div>
                                                @endfor
                                            @endif
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                @if(!empty($logContent))
                                                    عدد الأسطر: {{ $lineCount ?? 0 }}
                                                @endif
                                            </small>
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i>
                                                الأسطر ملونة حسب مستوى الخطأ
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Legend -->
                                <div class="card mt-3">
                                    <div class="card-body">
                                        <h6 class="mb-3">مفتاح الألوان</h6>
                                        <div class="row g-2">
                                            <div class="col-md-3">
                                                <span class="badge badge-error">ERROR</span>
                                                <small class="text-muted d-block mt-1">أخطاء تشغيلية</small>
                                            </div>
                                            <div class="col-md-3">
                                                <span class="badge badge-warning">WARNING</span>
                                                <small class="text-muted d-block mt-1">تحذيرات</small>
                                            </div>
                                            <div class="col-md-3">
                                                <span class="badge badge-info">INFO</span>
                                                <small class="text-muted d-block mt-1">معلومات</small>
                                            </div>
                                            <div class="col-md-3">
                                                <span class="badge bg-secondary">DEBUG</span>
                                                <small class="text-muted d-block mt-1">تصحيح أخطاء</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">حذف ملف السجل</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>هل أنت متأكد من حذف ملف السجل؟</p>
                    <p class="text-danger">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        هذا الإجراء لا يمكن التراجع عنه
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <form id="deleteForm" method="POST" action="{{ route('admin.errors.destroy') }}">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="file" id="deleteFileName">
                        <button type="submit" class="btn btn-danger">حذف</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Clear All Modal -->
    <div class="modal fade" id="clearAllModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">تفريغ جميع ملفات السجل</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>هل أنت متأكد من تفريغ جميع ملفات السجل؟</p>
                    <p class="text-warning">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        سيتم إزالة جميع محتويات ملفات السجل
                    </p>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-1"></i>
                        سيتم الاحتفاظ بالملفات الفارغة فقط
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <form method="POST" action="{{ route('admin.errors.clear-all') }}">
                        @csrf
                        <button type="submit" class="btn btn-warning">تفريغ الكل</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scroll to Top Button -->
    <div class="scroll-to-top" id="scrollToTop">
        <i class="fas fa-arrow-up"></i>
    </div>
@endsection

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/highlight.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize highlight.js
            hljs.highlightAll();

            // Delete modal
            $('#deleteModal').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget);
                const fileName = button.data('file');
                $('#deleteFileName').val(fileName);
                $(this).find('.modal-body p:first').text('هل أنت متأكد من حذف ملف السجل: ' + fileName + '؟');
            });

            // Copy log content
            window.copyLogContent = function() {
                const content = document.getElementById('logContent').innerText;
                navigator.clipboard.writeText(content).then(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم النسخ!',
                        text: 'تم نسخ محتوى السجل إلى الحافظة',
                        timer: 1500,
                        showConfirmButton: false
                    });
                });
            };

            // Refresh log
            window.refreshLog = function() {
                window.location.reload();
            };

            // Scroll to top
            const scrollButton = document.getElementById('scrollToTop');
            
            window.addEventListener('scroll', () => {
                if (window.pageYOffset > 300) {
                    scrollButton.classList.add('show');
                } else {
                    scrollButton.classList.remove('show');
                }
            });

            scrollButton.addEventListener('click', () => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });

            // Filter logs by level
            $('.log-level-badge').on('click', function() {
                const level = $(this).data('level');
                $(this).toggleClass('active');
                filterLogsByLevel(level);
            });

            function filterLogsByLevel(level) {
                $('.log-line').each(function() {
                    const lineText = $(this).text();
                    const isActive = $('.log-level-badge[data-level="' + level + '"]').hasClass('active');
                    
                    if (level === 'all') {
                        $(this).show();
                    } else if (lineText.includes(level.toUpperCase())) {
                        $(this).toggle(isActive);
                    }
                });
            }

            // Auto-refresh every 30 seconds
            setInterval(() => {
                refreshLog();
            }, 30000);

            // Search functionality
            $('#searchInput').on('keyup', function() {
                const searchTerm = $(this).val().toLowerCase();
                
                $('.log-line').each(function() {
                    const lineText = $(this).text().toLowerCase();
                    $(this).toggle(lineText.includes(searchTerm));
                });
            });

            // Format bytes helper
            function formatBytes(bytes, decimals = 2) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const dm = decimals < 0 ? 0 : decimals;
                const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
            }
        });
    </script>
@endsection