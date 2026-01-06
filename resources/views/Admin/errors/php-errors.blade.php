@extends('Admin.layout.master')

@section('title', 'أخطاء PHP - سجلات النظام')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/atom-one-dark.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
   
   <style>
        .php-error-card {
            border-radius: 12px;
            border: 1px solid var(--bs-border-color);
            transition: all 0.3s ease;
        }

        .php-error-card:hover {
            border-color: #ff9f43;
            box-shadow: 0 5px 20px rgba(255, 159, 67, 0.1);
        }

        .php-error-header {
            background: linear-gradient(135deg, rgba(255, 159, 67, 0.1), rgba(255, 159, 67, 0.05));
            border-bottom: 2px solid #ff9f43;
            padding: 15px;
            border-radius: 12px 12px 0 0;
        }

        .php-error-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ff9f43, #ff8c2e);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            margin: 0 auto 15px;
        }

        .error-level-badge {
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .level-fatal {
            background: rgba(234, 84, 85, 0.2);
            color: #ea5455;
            border: 1px solid rgba(234, 84, 85, 0.3);
        }

        .level-error {
            background: rgba(255, 107, 107, 0.2);
            color: #ff6b6b;
            border: 1px solid rgba(255, 107, 107, 0.3);
        }

        .level-warning {
            background: rgba(255, 217, 61, 0.2);
            color: #ffd93d;
            border: 1px solid rgba(255, 217, 61, 0.3);
        }

        .level-notice {
            background: rgba(77, 171, 247, 0.2);
            color: #4dabf7;
            border: 1px solid rgba(77, 171, 247, 0.3);
        }

        .level-deprecated {
            background: rgba(161, 127, 255, 0.2);
            color: #a17fff;
            border: 1px solid rgba(161, 127, 255, 0.3);
        }

        .level-strict {
            background: rgba(32, 201, 151, 0.2);
            color: #20c997;
            border: 1px solid rgba(32, 201, 151, 0.3);
        }

        .error-content-container {
            background: #1e1e1e;
            border-radius: 10px;
            padding: 20px;
            margin: 15px 0;
            max-height: 500px;
            overflow-y: auto;
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 13px;
            line-height: 1.6;
        }

        .error-line {
            padding: 8px 10px;
            margin-bottom: 5px;
            border-radius: 5px;
            border-left: 3px solid transparent;
            transition: all 0.2s ease;
        }

        .error-line:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .error-line.fatal {
            border-left-color: #ea5455;
            background: rgba(234, 84, 85, 0.1);
        }

        .error-line.error {
            border-left-color: #ff6b6b;
            background: rgba(255, 107, 107, 0.1);
        }

        .error-line.warning {
            border-left-color: #ffd93d;
            background: rgba(255, 217, 61, 0.1);
        }

        .error-line.notice {
            border-left-color: #4dabf7;
            background: rgba(77, 171, 247, 0.1);
        }

        .error-timestamp {
            color: #20c997;
            font-weight: 500;
            font-size: 12px;
            margin-right: 10px;
        }

        .error-message {
            color: #f8f9fa;
            margin: 5px 0;
        }

        .error-file {
            color: #6c757d;
            font-size: 12px;
            margin-top: 5px;
        }

        .error-file .path {
            color: #4dabf7;
            font-weight: 500;
        }

        .error-file .line {
            color: #ff9f43;
            font-weight: 500;
        }

        .error-stack {
            background: rgba(255, 255, 255, 0.03);
            padding: 10px 15px;
            margin: 8px 0;
            border-radius: 5px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 12px;
            color: #adb5bd;
        }

        .stack-line {
            padding: 2px 0;
            margin-left: 20px;
        }

        .stack-file {
            color: #4dabf7;
        }

        .stack-line-number {
            color: #ff9f43;
        }

        .error-summary {
            background: #26253d;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .summary-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .summary-item:last-child {
            border-bottom: none;
        }

        .summary-label {
            color: var(--bs-secondary-color);
        }

        .summary-value {
            font-weight: 600;
            color: var(--bs-heading-color);
        }

        .error-filters {
            background:#26253d;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .filter-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 6px 15px;
            border-radius: 20px;
            border: 2px solid transparent;
            background: #26253d;
            color: var(--bs-body-color);
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .filter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        .filter-btn.active {
            border-color: #696cff;
            background: rgba(105, 108, 255, 0.1);
            color: #696cff;
        }

        .php-info-card {
            background: #26253d;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            border: 1px solid var(--bs-border-color);
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .info-item {
            background:#26253d;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid var(--bs-border-color);
        }

        .info-label {
            font-size: 12px;
            color: var(--bs-secondary-color);
            margin-bottom: 5px;
        }

        .info-value {
            font-weight: 600;
            color: var(--bs-heading-color);
            font-family: monospace;
        }

        .php-config-status {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-left: 8px;
        }

        .status-good {
            background: #28c76f;
        }

        .status-warning {
            background: #ff9f43;
        }

        .status-bad {
            background: #ea5455;
        }

        .error-pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
        }

        .page-btn {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background:#26253d;
            border: 1px solid var(--bs-border-color);
            color: var(--bs-body-color);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .page-btn:hover {
            background: #696cff;
            color: white;
            border-color: #696cff;
        }

        .page-btn.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .page-btn.active {
            background: #696cff;
            color: white;
            border-color: #696cff;
        }

        .error-count {
            font-size: 12px;
            color: var(--bs-secondary-color);
            text-align: center;
            margin-top: 10px;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: var(--bs-secondary-color);
        }

        .empty-state-icon {
            font-size: 48px;
            color: var(--bs-border-color);
            margin-bottom: 15px;
        }

        .auto-refresh-toggle {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            padding: 8px 15px;
            background: #26253d;
            border-radius: 20px;
            user-select: none;
        }

        .toggle-switch {
            position: relative;
            width: 40px;
            height: 20px;
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
            background-color:#26253d;
            transition: .4s;
            border-radius: 34px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 14px;
            width: 14px;
            left: 3px;
            bottom: 3px;
            background-color: #26253d;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .toggle-slider {
            background-color: #28c76f;
        }

        input:checked + .toggle-slider:before {
            transform: translateX(20px);
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
                    <a href="{{ route('admin.errors.index') }}">الأخطاء البرمجية</a>
                </li>
                <li class="breadcrumb-item active">أخطاء PHP</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-12">
                <div class="card mb-4 php-error-card">
                    <div class="php-error-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1">أخطاء PHP وسجلات النظام</h5>
                                <small class="text-muted">مراقبة ومراجعة أخطاء PHP وإعدادات الخادم</small>
                            </div>
                            <div class="php-error-icon">
                                <i class="fab fa-php"></i>
                            </div>
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

                        <!-- File Selection -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="mb-3">اختر ملف سجل PHP</h6>
                                        <div class="row g-2">
                                            @foreach($logFiles as $file)
                                                <div class="col-md-4">
                                                    <div class="file-item d-flex align-items-center p-3 rounded {{ $selectedFile == $file['name'] ? 'active' : '' }}" 
                                                         onclick="window.location.href='?file={{ $file['name'] }}'"
                                                         style="cursor: pointer; border: 1px solid var(--bs-border-color); transition: all 0.3s ease;">
                                                        <div class="me-3">
                                                            @if(str_contains(strtolower($file['name']), 'php'))
                                                                <i class="fas fa-file-code text-warning fa-lg"></i>
                                                            @else
                                                                <i class="fas fa-file-alt text-secondary fa-lg"></i>
                                                            @endif
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <div class="fw-bold">{{ $file['name'] }}</div>
                                                            <small class="text-muted">{{ $file['size'] }} • {{ $file['modified'] }}</small>
                                                        </div>
                                                        @if($selectedFile == $file['name'])
                                                            <i class="fas fa-check-circle text-success"></i>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="mb-3">إجراءات سريعة</h6>
                                        <div class="d-grid gap-2">
                                            <a href="{{ route('admin.errors.download', $selectedFile) }}" 
                                               class="btn btn-outline-warning">
                                                <i class="fas fa-download me-1"></i> تحميل الملف
                                            </a>
                                            
                                            <button type="button" 
                                                    class="btn btn-outline-danger"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#clearFileModal"
                                                    data-file="{{ $selectedFile }}">
                                                <i class="fas fa-trash me-1"></i> تفريغ الملف
                                            </button>

                                            <label class="auto-refresh-toggle mt-3">
                                                <span>التحديث التلقائي</span>
                                                <div class="toggle-switch">
                                                    <input type="checkbox" id="autoRefresh" checked>
                                                    <span class="toggle-slider"></span>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Error Summary -->
                        <div class="error-summary">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="summary-item">
                                        <span class="summary-label">الملف المحدد:</span>
                                        <span class="summary-value">{{ $selectedFile }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="summary-item">
                                        <span class="summary-label">الحجم:</span>
                                        <span class="summary-value">
                                            @php
                                                $fileSize = 0;
                                                foreach($logFiles as $file) {
                                                    if($file['name'] == $selectedFile) {
                                                        $fileSize = $file['size'];
                                                        break;
                                                    }
                                                }
                                                echo $fileSize;
                                            @endphp
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="summary-item">
                                        <span class="summary-label">عدد الأخطاء:</span>
                                        <span class="summary-value" id="errorCount">0</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="summary-item">
                                        <span class="summary-label">آخر تحديث:</span>
                                        <span class="summary-value">{{ now()->format('H:i:s') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Error Filters -->
                        <div class="error-filters">
                            <h6 class="mb-3">تصفية حسب النوع:</h6>
                            <div class="filter-group">
                                <button class="filter-btn active" data-level="all" onclick="filterErrors('all')">
                                    الكل
                                </button>
                                <button class="filter-btn" data-level="fatal" onclick="filterErrors('fatal')">
                                    <span class="error-level-badge level-fatal">Fatal</span>
                                </button>
                                <button class="filter-btn" data-level="error" onclick="filterErrors('error')">
                                    <span class="error-level-badge level-error">Error</span>
                                </button>
                                <button class="filter-btn" data-level="warning" onclick="filterErrors('warning')">
                                    <span class="error-level-badge level-warning">Warning</span>
                                </button>
                                <button class="filter-btn" data-level="notice" onclick="filterErrors('notice')">
                                    <span class="error-level-badge level-notice">Notice</span>
                                </button>
                                <button class="filter-btn" data-level="deprecated" onclick="filterErrors('deprecated')">
                                    <span class="error-level-badge level-deprecated">Deprecated</span>
                                </button>
                                <button class="filter-btn" data-level="strict" onclick="filterErrors('strict')">
                                    <span class="error-level-badge level-strict">Strict</span>
                                </button>
                            </div>
                        </div>

                        <!-- PHP Errors Content -->
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">أخطاء PHP</h6>
                                <small class="text-muted" id="displayedErrors">يتم تحميل الأخطاء...</small>
                            </div>
                            <div class="card-body p-0">
                                <div class="error-content-container" id="phpErrorsContainer">
                                    @if(empty($logContent) || $logContent == "ملف السجل غير موجود")
                                        <div class="empty-state">
                                            <div class="empty-state-icon">
                                                <i class="fas fa-search"></i>
                                            </div>
                                            <h6 class="mb-2">لا توجد أخطاء PHP مسجلة</h6>
                                            <p class="text-muted mb-0">
                                                @if($logContent == "ملف السجل غير موجود")
                                                    ملف السجل غير موجود أو لا يمكن قراءته
                                                @else
                                                    لم يتم تسجيل أي أخطاء PHP حتى الآن
                                                @endif
                                            </p>
                                            <div class="mt-3">
                                                <button class="btn btn-sm btn-outline-primary" onclick="checkPHPLog()">
                                                    <i class="fas fa-sync-alt me-1"></i> التحقق من السجلات
                                                </button>
                                            </div>
                                        </div>
                                    @else
                                        @php
                                            $errors = parsePHPLog($logContent);
                                            $errorCount = count($errors);
                                        @endphp
                                        
                                        @if($errorCount === 0)
                                            <div class="empty-state">
                                                <div class="empty-state-icon">
                                                    <i class="fas fa-check-circle"></i>
                                                </div>
                                                <h6 class="mb-2">لا توجد أخطاء PHP</h6>
                                                <p class="text-muted mb-0">النظام يعمل بشكل صحيح</p>
                                            </div>
                                        @else
                                            @foreach($errors as $index => $error)
                                                @php
                                                    $levelClass = 'error';
                                                    if (stripos($error['level'] ?? '', 'fatal') !== false) {
                                                        $levelClass = 'fatal';
                                                    } elseif (stripos($error['level'] ?? '', 'warning') !== false) {
                                                        $levelClass = 'warning';
                                                    } elseif (stripos($error['level'] ?? '', 'notice') !== false) {
                                                        $levelClass = 'notice';
                                                    } elseif (stripos($error['level'] ?? '', 'deprecated') !== false) {
                                                        $levelClass = 'deprecated';
                                                    } elseif (stripos($error['level'] ?? '', 'strict') !== false) {
                                                        $levelClass = 'strict';
                                                    }
                                                @endphp
                                                
                                                <div class="error-line {{ $levelClass }}" data-level="{{ $levelClass }}">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <span class="error-timestamp">{{ $error['timestamp'] ?? 'غير معروف' }}</span>
                                                            <span class="error-level-badge level-{{ $levelClass }}">
                                                                {{ strtoupper($error['level'] ?? 'UNKNOWN') }}
                                                            </span>
                                                        </div>
                                                        <button class="btn btn-sm btn-outline-secondary" onclick="toggleErrorDetails({{ $index }})">
                                                            <i class="fas fa-chevron-down"></i>
                                                        </button>
                                                    </div>
                                                    
                                                    <div class="error-message mt-2">
                                                        {{ $error['message'] ?? 'لا توجد رسالة' }}
                                                    </div>
                                                    
                                                    @if(!empty($error['file']))
                                                        <div class="error-file">
                                                            في الملف: <span class="path">{{ $error['file'] }}</span>
                                                            @if(!empty($error['line']))
                                                                ، السطر: <span class="line">{{ $error['line'] }}</span>
                                                            @endif
                                                        </div>
                                                    @endif
                                                    
                                                    @if(!empty($error['stack']))
                                                        <div class="error-stack" id="errorStack{{ $index }}" style="display: none;">
                                                            <strong>Stack Trace:</strong>
                                                            @foreach(explode("\n", $error['stack']) as $stackLine)
                                                                @if(trim($stackLine))
                                                                    <div class="stack-line">
                                                                        {{ trim($stackLine) }}
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        @endif
                                    @endif
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="error-count" id="totalErrorCount">
                                        @if(!empty($errors))
                                            عرض {{ count($errors) }} خطأ
                                        @endif
                                    </div>
                                    <div class="error-pagination" id="errorPagination">
                                        <button class="page-btn" onclick="prevPage()">
                                            <i class="fas fa-chevron-right"></i>
                                        </button>
                                        <span id="currentPage">1</span>
                                        <button class="page-btn" onclick="nextPage()">
                                            <i class="fas fa-chevron-left"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PHP Configuration Info -->
                        <div class="php-info-card">
                            <h6 class="mb-3"><i class="fas fa-cog me-2"></i>معلومات إعدادات PHP</h6>
                            <div class="info-grid">
                                <div class="info-item">
                                    <div class="info-label">إصدار PHP</div>
                                    <div class="info-value">
                                        {{ phpversion() }}
                                        @if(version_compare(phpversion(), '8.0', '>='))
                                            <span class="php-config-status status-good"></span>
                                        @else
                                            <span class="php-config-status status-warning"></span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="info-label">memory_limit</div>
                                    <div class="info-value">
                                        {{ ini_get('memory_limit') }}
                                        @if((int)ini_get('memory_limit') >= 128)
                                            <span class="php-config-status status-good"></span>
                                        @else
                                            <span class="php-config-status status-warning"></span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="info-label">max_execution_time</div>
                                    <div class="info-value">
                                        {{ ini_get('max_execution_time') }} ثانية
                                    </div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="info-label">display_errors</div>
                                    <div class="info-value">
                                        {{ ini_get('display_errors') ? 'On' : 'Off' }}
                                        @if(!ini_get('display_errors'))
                                            <span class="php-config-status status-good"></span>
                                        @else
                                            <span class="php-config-status status-warning"></span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="info-label">error_reporting</div>
                                    <div class="info-value">
                                        E_ALL & ~E_DEPRECATED & ~E_STRICT
                                    </div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="info-label">log_errors</div>
                                    <div class="info-value">
                                        {{ ini_get('log_errors') ? 'On' : 'Off' }}
                                        @if(ini_get('log_errors'))
                                            <span class="php-config-status status-good"></span>
                                        @else
                                            <span class="php-config-status status-bad"></span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="info-label">error_log</div>
                                    <div class="info-value">
                                        {{ ini_get('error_log') ?: 'غير محدد' }}
                                    </div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="info-label">upload_max_filesize</div>
                                    <div class="info-value">
                                        {{ ini_get('upload_max_filesize') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Clear File Modal -->
    <div class="modal fade" id="clearFileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">تفريغ ملف السجل</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>هل أنت متأكد من تفريغ محتويات ملف السجل؟</p>
                    <p class="text-warning">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        سيتم إزالة جميع الأخطاء المسجلة في الملف
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <form method="POST" action="{{ route('admin.errors.destroy') }}">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="file" id="clearFileName">
                        <button type="submit" class="btn btn-warning">تفريغ الملف</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/highlight.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize highlight.js for code snippets
            hljs.highlightAll();

            // Clear file modal
            $('#clearFileModal').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget);
                const fileName = button.data('file');
                $('#clearFileName').val(fileName);
                $(this).find('.modal-body p:first').text('هل أنت متأكد من تفريغ ملف السجل: ' + fileName + '؟');
            });

            // Count errors
            function countErrors() {
                const errorLines = $('.error-line');
                const errorCount = errorLines.length;
                $('#errorCount').text(errorCount);
                $('#displayedErrors').text('عرض ' + errorCount + ' خطأ');
                return errorCount;
            }

            // Initialize error count
            setTimeout(() => {
                const count = countErrors();
                updatePagination(count);
            }, 100);

            // Auto-refresh functionality
            let autoRefreshInterval = null;
            const autoRefreshCheckbox = $('#autoRefresh');
            
            function startAutoRefresh() {
                if (autoRefreshInterval) clearInterval(autoRefreshInterval);
                autoRefreshInterval = setInterval(() => {
                    refreshPHPLogs();
                }, 30000); // 30 seconds
            }
            
            function stopAutoRefresh() {
                if (autoRefreshInterval) {
                    clearInterval(autoRefreshInterval);
                    autoRefreshInterval = null;
                }
            }
            
            autoRefreshCheckbox.on('change', function() {
                if ($(this).is(':checked')) {
                    startAutoRefresh();
                } else {
                    stopAutoRefresh();
                }
            });
            
            // Start auto-refresh on page load
            if (autoRefreshCheckbox.is(':checked')) {
                startAutoRefresh();
            }

            // Toggle error details
            window.toggleErrorDetails = function(index) {
                const stackElement = $('#errorStack' + index);
                const button = $(`button[onclick="toggleErrorDetails(${index})"]`);
                
                if (stackElement.length) {
                    if (stackElement.is(':visible')) {
                        stackElement.slideUp();
                        button.html('<i class="fas fa-chevron-down"></i>');
                    } else {
                        stackElement.slideDown();
                        button.html('<i class="fas fa-chevron-up"></i>');
                    }
                }
            };

            // Filter errors by level
            window.filterErrors = function(level) {
                // Update active filter button
                $('.filter-btn').removeClass('active');
                $(`.filter-btn[data-level="${level}"]`).addClass('active');
                
                const errorLines = $('.error-line');
                
                if (level === 'all') {
                    errorLines.show();
                } else {
                    errorLines.each(function() {
                        const errorLevel = $(this).data('level');
                        $(this).toggle(errorLevel === level);
                    });
                }
                
                // Update count and pagination
                const visibleCount = $('.error-line:visible').length;
                countErrors();
                updatePagination(visibleCount);
                resetPagination();
            };

            // Pagination variables
            let currentPage = 1;
            const errorsPerPage = 10;

            // Update pagination
            function updatePagination(totalErrors) {
                const totalPages = Math.ceil(totalErrors / errorsPerPage);
                const pagination = $('#errorPagination');
                
                if (totalPages <= 1) {
                    pagination.hide();
                } else {
                    pagination.show();
                    $('#currentPage').text(currentPage + ' / ' + totalPages);
                }
                
                displayPage(currentPage);
            }

            // Display specific page
            function displayPage(page) {
                const startIndex = (page - 1) * errorsPerPage;
                const endIndex = startIndex + errorsPerPage;
                
                $('.error-line:visible').each(function(index) {
                    if (index >= startIndex && index < endIndex) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }

            // Navigation functions
            window.prevPage = function() {
                const totalErrors = $('.error-line:visible').length;
                const totalPages = Math.ceil(totalErrors / errorsPerPage);
                
                if (currentPage > 1) {
                    currentPage--;
                    displayPage(currentPage);
                    $('#currentPage').text(currentPage + ' / ' + totalPages);
                    scrollToTop();
                }
            };

            window.nextPage = function() {
                const totalErrors = $('.error-line:visible').length;
                const totalPages = Math.ceil(totalErrors / errorsPerPage);
                
                if (currentPage < totalPages) {
                    currentPage++;
                    displayPage(currentPage);
                    $('#currentPage').text(currentPage + ' / ' + totalPages);
                    scrollToTop();
                }
            };

            function resetPagination() {
                currentPage = 1;
                const totalErrors = $('.error-line:visible').length;
                updatePagination(totalErrors);
            }

            function scrollToTop() {
                $('html, body').animate({
                    scrollTop: $('#phpErrorsContainer').offset().top - 100
                }, 300);
            }

            // Check PHP logs
            window.checkPHPLog = function() {
                Swal.fire({
                    title: 'جاري التحقق...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            };

            // Refresh PHP logs
            window.refreshPHPLogs = function() {
                $.ajax({
                    url: window.location.href,
                    type: 'GET',
                    data: { refresh: true },
                    success: function(response) {
                        // Extract the errors container from response
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = response;
                        const newContent = $(tempDiv).find('#phpErrorsContainer').html();
                        
                        if (newContent) {
                            $('#phpErrorsContainer').html(newContent);
                            countErrors();
                            updatePagination($('.error-line').length);
                            
                            // Show notification
                            Swal.fire({
                                icon: 'success',
                                title: 'تم التحديث',
                                text: 'تم تحديث سجلات PHP',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: 'فشل تحديث السجلات',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                });
            };

            // Copy error to clipboard
            $(document).on('click', '.copy-error', function() {
                const errorText = $(this).closest('.error-line').text();
                navigator.clipboard.writeText(errorText).then(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم النسخ',
                        text: 'تم نسخ الخطأ إلى الحافظة',
                        timer: 1500,
                        showConfirmButton: false
                    });
                });
            });

            // Keyboard shortcuts
            $(document).on('keydown', function(e) {
                // Ctrl + R to refresh
                if (e.ctrlKey && e.key === 'r') {
                    e.preventDefault();
                    refreshPHPLogs();
                }
                
                // Ctrl + F to filter
                if (e.ctrlKey && e.key === 'f') {
                    e.preventDefault();
                    $('input[name="search"]').focus();
                }
                
                // Arrow keys for pagination
                if (e.key === 'ArrowRight') {
                    prevPage();
                } else if (e.key === 'ArrowLeft') {
                    nextPage();
                }
            });

            // Cleanup on page unload
            $(window).on('beforeunload', function() {
                stopAutoRefresh();
            });
        });

        // Helper function to parse PHP log content (simplified version)
        function parsePHPLogContent(content) {
            const lines = content.split('\n');
            const errors = [];
            let currentError = null;
            
            for (const line of lines) {
                if (line.trim() === '') continue;
                
                // Check for new error entry
                if (line.match(/\[.*?\] PHP .*?:/)) {
                    if (currentError) {
                        errors.push(currentError);
                    }
                    
                    currentError = {
                        timestamp: line.match(/\[(.*?)\]/)?.[1] || '',
                        level: line.match(/PHP (.*?):/)?.[1] || '',
                        message: line.split(':').slice(2).join(':').trim(),
                        file: '',
                        line: '',
                        stack: ''
                    };
                } else if (currentError) {
                    // Stack trace or additional info
                    if (line.includes('Stack trace:')) {
                        currentError.stack = line;
                    } else if (line.includes(' in ') && line.includes(' on line ')) {
                        const fileMatch = line.match(/ in (.*?) on line (\d+)/);
                        if (fileMatch) {
                            currentError.file = fileMatch[1];
                            currentError.line = fileMatch[2];
                        }
                    } else if (currentError.stack) {
                        currentError.stack += '\n' + line;
                    }
                }
            }
            
            if (currentError) {
                errors.push(currentError);
            }
            
            return errors;
        }
    </script>
@endsection