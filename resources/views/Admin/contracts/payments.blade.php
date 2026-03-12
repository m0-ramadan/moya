@extends('Admin.layout.master')

@section('title', 'مدفوعات العقد - ' . $contract->contract_number)

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #696cff;
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --dark-bg: #1e1e2d;
            --dark-card: #2b3b4c;
        }

        body {
            font-family: "Cairo", sans-serif !important;
            background: var(--dark-bg);
            color: #fff;
        }

        .payments-card {
            background: var(--dark-card);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 25px;
            overflow: hidden;
        }

        .card-header {
            background: var(--primary-gradient);
            color: white;
            padding: 20px 25px;
            border-bottom: none;
            font-weight: 600;
            font-size: 18px;
        }

        .card-header i {
            margin-left: 10px;
        }

        .card-body {
            padding: 25px;
        }

        /* Summary Cards */
        .summary-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            height: 100%;
        }

        .summary-card:hover {
            transform: translateY(-5px);
            border-color: var(--primary-color);
            background: rgba(105, 108, 255, 0.1);
        }

        .summary-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 15px;
        }

        .icon-total {
            background: rgba(105, 108, 255, 0.2);
            color: var(--primary-color);
            border: 1px solid rgba(105, 108, 255, 0.3);
        }

        .icon-paid {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }

        .icon-remaining {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
            border: 1px solid rgba(220, 53, 69, 0.3);
        }

        .icon-payments {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }

        .summary-number {
            font-size: 28px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 5px;
        }

        .summary-label {
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
        }

        /* Progress Bar */
        .progress-container {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .progress {
            height: 12px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 6px;
            overflow: hidden;
            margin: 10px 0;
        }

        .progress-bar {
            background: var(--primary-gradient);
            position: relative;
            transition: width 0.3s ease;
        }

        .progress-bar.success {
            background: linear-gradient(135deg, #28a745, #20c997);
        }

        .progress-stats {
            display: flex;
            justify-content: space-between;
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
            margin-top: 10px;
        }

        /* Contract Info */
        .contract-info {
            background: rgba(255, 255, 255, 0.02);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .contract-number-badge {
            background: var(--primary-gradient);
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            display: inline-block;
            font-size: 16px;
            font-weight: 600;
            direction: ltr;
        }

        .info-row {
            display: flex;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            min-width: 120px;
            color: rgba(255, 255, 255, 0.7);
        }

        .info-label i {
            width: 25px;
            color: var(--primary-color);
        }

        .info-value {
            color: #fff;
            font-weight: 500;
        }

        /* Filter Section */
        .filter-card {
            background: rgba(255, 255, 255, 0.02);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .filter-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .form-control,
        .form-select {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 10px 15px;
            color: #fff;
        }

        .form-control:focus,
        .form-select:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--primary-color);
            color: #fff;
            box-shadow: 0 0 0 0.25rem rgba(105, 108, 255, 0.25);
        }

        .form-select option {
            background: var(--dark-card);
            color: #fff;
        }

        /* Payments Table */
        .table-responsive {
            margin-bottom: 25px;
        }

        .table {
            color: #fff;
            margin-bottom: 0;
        }

        .table thead th {
            color: rgba(255, 255, 255, 0.7);
            font-weight: 600;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 15px;
        }

        .table td {
            padding: 15px;
            vertical-align: middle;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .table tbody tr:hover {
            background: rgba(105, 108, 255, 0.1);
        }

        .payment-status {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .status-completed {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }

        .status-pending {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }

        .status-failed {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
            border: 1px solid rgba(220, 53, 69, 0.3);
        }

        .status-refunded {
            background: rgba(108, 117, 125, 0.2);
            color: #6c757d;
            border: 1px solid rgba(108, 117, 125, 0.3);
        }

        .payment-method-badge {
            background: rgba(255, 255, 255, 0.1);
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
            color: #fff;
        }

        .amount-col {
            font-weight: 600;
            color: var(--primary-color);
        }

        .btn-action {
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 13px;
            transition: all 0.3s ease;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .btn-primary {
            background: var(--primary-gradient);
            border: none;
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
        }

        .btn-info {
            background: linear-gradient(135deg, #17a2b8, #0dcaf0);
            border: none;
        }

        .btn-warning {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
            border: none;
            color: #000;
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545, #c82333);
            border: none;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state-icon {
            font-size: 80px;
            color: rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }

        .empty-state-text {
            color: rgba(255, 255, 255, 0.7);
            font-size: 18px;
            margin-bottom: 20px;
        }

        /* Pagination */
        .pagination {
            gap: 5px;
            justify-content: center;
            margin-top: 25px;
        }

        .page-link {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1);
            color: #fff;
            border-radius: 8px;
            padding: 8px 15px;
        }

        .page-link:hover {
            background: rgba(105, 108, 255, 0.1);
            border-color: var(--primary-color);
            color: #fff;
        }

        .page-item.active .page-link {
            background: var(--primary-gradient);
            border-color: var(--primary-color);
        }

        .page-item.disabled .page-link {
            background: rgba(255, 255, 255, 0.02);
            border-color: rgba(255, 255, 255, 0.05);
            color: rgba(255, 255, 255, 0.3);
        }

        /* Receipt Modal */
        .modal-content {
            background: var(--dark-card);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .modal-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .modal-footer {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn-close-white {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        .receipt-details {
            background: rgba(255, 255, 255, 0.02);
            border-radius: 10px;
            padding: 20px;
        }

        .receipt-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .receipt-row:last-child {
            border-bottom: none;
        }

        /* Toolbar */
        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .toolbar-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        /* Export Dropdown */
        .export-dropdown {
            position: relative;
            display: inline-block;
        }

        .export-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .export-dropdown-content {
            display: none;
            position: absolute;
            background: var(--dark-card);
            min-width: 160px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            border-radius: 8px;
            z-index: 1;
            padding: 5px 0;
            margin-top: 5px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .export-dropdown:hover .export-dropdown-content {
            display: block;
        }

        .export-item {
            padding: 8px 15px;
            cursor: pointer;
            transition: background 0.3s;
            color: rgba(255, 255, 255, 0.8);
        }

        .export-item:hover {
            background: rgba(105, 108, 255, 0.1);
            color: #fff;
        }

        @media (max-width: 768px) {
            .toolbar {
                flex-direction: column;
                align-items: stretch;
            }

            .toolbar-actions {
                justify-content: center;
            }

            .filter-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- مسار التنقل -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.index') }}">الرئيسية</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.contracts.index') }}">العقود</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.contracts.show', $contract->id) }}">{{ $contract->contract_number }}</a>
                </li>
                <li class="breadcrumb-item active">المدفوعات</li>
            </ol>
        </nav>

        <!-- رأس الصفحة -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">
                    <i class="fas fa-money-bill-wave text-primary me-2"></i>
                    مدفوعات العقد
                </h4>
                <p class="text-muted mb-0">
                    <span class="contract-number-badge ms-2">{{ $contract->contract_number }}</span>
                </p>
            </div>
            <div class="toolbar-actions">
                <a href="{{ route('admin.contracts.show', $contract->id) }}" class="btn btn-info btn-action">
                    <i class="fas fa-eye me-2"></i>
                    تفاصيل العقد
                </a>
                <button type="button" class="btn btn-success btn-action" data-bs-toggle="modal"
                    data-bs-target="#addPaymentModal">
                    <i class="fas fa-plus me-2"></i>
                    تسديد دفعة جديدة
                </button>
            </div>
        </div>

        <!-- معلومات العقد -->
        <div class="contract-info">
            <div class="row">
                <div class="col-md-6">
                    <div class="info-row">
                        <span class="info-label">
                            <i class="fas fa-user"></i>
                            العميل:
                        </span>
                        <span class="info-value">{{ $contract->user->name ?? 'غير معروف' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">
                            <i class="fas fa-calendar"></i>
                            تاريخ العقد:
                        </span>
                        <span class="info-value">{{ $contract->start_date?->format('Y-m-d') }} إلى
                            {{ $contract->end_date?->format('Y-m-d') }}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-row">
                        <span class="info-label">
                            <i class="fas fa-tag"></i>
                            حالة العقد:
                        </span>
                        <span class="info-value">
                            <span class="badge-status status-{{ $contract->status }}">
                                @switch($contract->status)
                                    @case('active')
                                        <i class="fas fa-check-circle"></i> نشط
                                    @break

                                    @case('expired')
                                        <i class="fas fa-clock"></i> منتهي
                                    @break

                                    @case('pending')
                                        <i class="fas fa-hourglass-half"></i> معلق
                                    @break

                                    @case('cancelled')
                                        <i class="fas fa-ban"></i> ملغي
                                    @break
                                @endswitch
                            </span>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">
                            <i class="fas fa-box"></i>
                            الطلبات:
                        </span>
                        <span
                            class="info-value">{{ $contract->remaining_orders }}/{{ $contract->total_orders_limit ?? '∞' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- بطاقات الملخص -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="summary-card">
                    <div class="summary-icon icon-total">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <div class="summary-number">{{ number_format($contract->total_amount, 2) }} ر.س</div>
                    <div class="summary-label">إجمالي العقد</div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="summary-card">
                    <div class="summary-icon icon-paid">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="summary-number">{{ number_format($contract->paid_amount, 2) }} ر.س</div>
                    <div class="summary-label">إجمالي المدفوع</div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="summary-card">
                    <div class="summary-icon icon-remaining">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="summary-number">{{ number_format($contract->remaining_amount, 2) }} ر.س</div>
                    <div class="summary-label">المتبقي</div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="summary-card">
                    <div class="summary-icon icon-payments">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div class="summary-number">{{ $payments->total() }}</div>
                    <div class="summary-label">عدد المدفوعات</div>
                </div>
            </div>
        </div>

        <!-- شريط التقدم -->
        <div class="progress-container">
            @php
                $paidPercentage =
                    $contract->total_amount > 0 ? ($contract->paid_amount / $contract->total_amount) * 100 : 0;
            @endphp
            <div class="d-flex justify-content-between mb-2">
                <span>نسبة الدفع</span>
                <span>{{ number_format($paidPercentage, 1) }}%</span>
            </div>
            <div class="progress">
                <div class="progress-bar success" style="width: {{ $paidPercentage }}%"></div>
            </div>
            <div class="progress-stats">
                <span><i class="fas fa-check-circle text-success"></i> مدفوع:
                    {{ number_format($contract->paid_amount, 2) }} ر.س</span>
                <span><i class="fas fa-clock text-warning"></i> متبقي: {{ number_format($contract->remaining_amount, 2) }}
                    ر.س</span>
            </div>
        </div>

        <!-- شريط الأدوات والفلترة -->
        <div class="toolbar">
            <div class="toolbar-actions">
                <div class="export-dropdown">
                    <button class="export-btn">
                        <i class="fas fa-download"></i>
                        تصدير
                        <i class="fas fa-chevron-down ms-1"></i>
                    </button>
                    <div class="export-dropdown-content">
                        <div class="export-item" onclick="exportPayments('pdf')">
                            <i class="fas fa-file-pdf me-2 text-danger"></i>
                            PDF
                        </div>
                        <div class="export-item" onclick="exportPayments('excel')">
                            <i class="fas fa-file-excel me-2 text-success"></i>
                            Excel
                        </div>
                        <div class="export-item" onclick="exportPayments('csv')">
                            <i class="fas fa-file-csv me-2 text-info"></i>
                            CSV
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-warning btn-action" onclick="printPayments()">
                    <i class="fas fa-print me-2"></i>
                    طباعة
                </button>
            </div>

            <!-- فلترة -->
            <form method="GET" action="{{ route('admin.contracts.payments', $contract->id) }}" id="filterForm">
                <div class="filter-row">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">جميع الحالات</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتملة</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>معلقة</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>فاشلة</option>
                        <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>مسترجعة</option>
                    </select>

                    <select name="payment_method" class="form-select" onchange="this.form.submit()">
                        <option value="">جميع طرق الدفع</option>
                        <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>نقدي</option>
                        <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>بطاقة</option>
                        <option value="bank_transfer"
                            {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                        <option value="wallet" {{ request('payment_method') == 'wallet' ? 'selected' : '' }}>محفظة
                        </option>
                    </select>

                    <input type="date" name="date_from" class="form-control" placeholder="من تاريخ"
                        value="{{ request('date_from') }}" onchange="this.form.submit()">

                    <input type="date" name="date_to" class="form-control" placeholder="إلى تاريخ"
                        value="{{ request('date_to') }}" onchange="this.form.submit()">
                </div>
            </form>
        </div>

        <!-- قائمة المدفوعات -->
        <div class="payments-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-list"></i>
                    سجل المدفوعات
                </div>
                <span class="badge bg-light text-dark">{{ $payments->total() }} مدفوعات</span>
            </div>
            <div class="card-body">
                @if ($payments->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <h5 class="empty-state-text">لا توجد مدفوعات مسجلة</h5>
                        <p class="text-muted mb-4">لم يتم تسجيل أي مدفوعات لهذا العقد بعد</p>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal"
                            data-bs-target="#addPaymentModal">
                            <i class="fas fa-plus me-2"></i>
                            تسجيل أول دفعة
                        </button>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>رقم العملية</th>
                                    <th>التاريخ</th>
                                    <th>المبلغ</th>
                                    <th>طريقة الدفع</th>
                                    <th>الحالة</th>
                                    <th>المرجع</th>
                                    <th>ملاحظات</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payments as $index => $payment)
                                    <tr>
                                        <td>{{ $payments->firstItem() + $index }}</td>
                                        <td>
                                            <strong>{{ $payment->transaction_id ?? '—' }}</strong>
                                        </td>
                                        <td>
                                            <div>{{ $payment->payment_date->format('Y-m-d') }}</div>
                                            <small
                                                class="text-muted">{{ $payment->payment_date->format('h:i A') }}</small>
                                        </td>
                                        <td>
                                            <span class="amount-col">{{ number_format($payment->amount, 2) }} ر.س</span>
                                        </td>
                                        <td>
                                            <span class="payment-method-badge">
                                                @switch($payment->payment_method)
                                                    @case('cash')
                                                        <i class="fas fa-money-bill-wave me-1"></i> نقدي
                                                    @break

                                                    @case('card')
                                                        <i class="fas fa-credit-card me-1"></i> بطاقة
                                                    @break

                                                    @case('bank_transfer')
                                                        <i class="fas fa-university me-1"></i> تحويل بنكي
                                                    @break

                                                    @case('wallet')
                                                        <i class="fas fa-wallet me-1"></i> محفظة
                                                    @break

                                                    @default
                                                        {{ $payment->payment_method }}
                                                @endswitch
                                            </span>
                                        </td>
                                        <td>
                                            <span class="payment-status status-{{ $payment->status }}">
                                                @switch($payment->status)
                                                    @case('completed')
                                                        <i class="fas fa-check-circle"></i> مكتملة
                                                    @break

                                                    @case('pending')
                                                        <i class="fas fa-clock"></i> معلقة
                                                    @break

                                                    @case('failed')
                                                        <i class="fas fa-times-circle"></i> فاشلة
                                                    @break

                                                    @case('refunded')
                                                        <i class="fas fa-undo-alt"></i> مسترجعة
                                                    @break
                                                @endswitch
                                            </span>
                                        </td>
                                        <td>
                                            @if ($payment->reference_number)
                                                <small class="text-muted">{{ $payment->reference_number }}</small>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($payment->notes)
                                                <i class="fas fa-sticky-note text-info" data-bs-toggle="tooltip"
                                                    title="{{ $payment->notes }}"></i>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-info"
                                                    onclick="viewReceipt({{ $payment->id }})" title="عرض الإيصال">
                                                    <i class="fas fa-receipt"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-warning"
                                                    onclick="editPayment({{ $payment->id }})" title="تعديل">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-success"
                                                    onclick="printReceipt({{ $payment->id }})" title="طباعة الإيصال">
                                                    <i class="fas fa-print"></i>
                                                </button>
                                                @if ($payment->status === 'completed')
                                                    <button type="button" class="btn btn-sm btn-secondary"
                                                        onclick="refundPayment({{ $payment->id }})" title="استرجاع">
                                                        <i class="fas fa-undo-alt"></i>
                                                    </button>
                                                @endif
                                                <button type="button" class="btn btn-sm btn-danger"
                                                    onclick="deletePayment({{ $payment->id }})" title="حذف">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- ملخص المدفوعات -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="info-box">
                                <h6 class="mb-3"><i class="fas fa-chart-pie me-2"></i>توزيع المدفوعات</h6>
                                @php
                                    $completedTotal = $payments->where('status', 'completed')->sum('amount');
                                    $pendingTotal = $payments->where('status', 'pending')->sum('amount');
                                    $failedTotal = $payments->where('status', 'failed')->sum('amount');
                                @endphp
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <span><i class="fas fa-check-circle text-success"></i> مكتملة</span>
                                        <span>{{ number_format($completedTotal, 2) }} ر.س</span>
                                    </div>
                                    <div class="progress mt-1" style="height: 5px;">
                                        <div class="progress-bar bg-success"
                                            style="width: {{ $contract->total_amount > 0 ? ($completedTotal / $contract->total_amount) * 100 : 0 }}%">
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <span><i class="fas fa-clock text-warning"></i> معلقة</span>
                                        <span>{{ number_format($pendingTotal, 2) }} ر.س</span>
                                    </div>
                                    <div class="progress mt-1" style="height: 5px;">
                                        <div class="progress-bar bg-warning"
                                            style="width: {{ $contract->total_amount > 0 ? ($pendingTotal / $contract->total_amount) * 100 : 0 }}%">
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <span><i class="fas fa-times-circle text-danger"></i> فاشلة</span>
                                        <span>{{ number_format($failedTotal, 2) }} ر.س</span>
                                    </div>
                                    <div class="progress mt-1" style="height: 5px;">
                                        <div class="progress-bar bg-danger"
                                            style="width: {{ $contract->total_amount > 0 ? ($failedTotal / $contract->total_amount) * 100 : 0 }}%">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <h6 class="mb-3"><i class="fas fa-credit-card me-2"></i>طرق الدفع</h6>
                                @php
                                    $paymentMethods = $payments->groupBy('payment_method');
                                @endphp
                                @foreach ($paymentMethods as $method => $methodPayments)
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>
                                            @switch($method)
                                                @case('cash')
                                                    <i class="fas fa-money-bill-wave text-success"></i> نقدي
                                                @break

                                                @case('card')
                                                    <i class="fas fa-credit-card text-info"></i> بطاقة
                                                @break

                                                @case('bank_transfer')
                                                    <i class="fas fa-university text-primary"></i> تحويل بنكي
                                                @break

                                                @case('wallet')
                                                    <i class="fas fa-wallet text-warning"></i> محفظة
                                                @break

                                                @default
                                                    {{ $method }}
                                            @endswitch
                                        </span>
                                        <span>{{ $methodPayments->count() }} عملية -
                                            {{ number_format($methodPayments->sum('amount'), 2) }} ر.س</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    @if ($payments->hasPages())
                        <nav>
                            <ul class="pagination">
                                {{-- Previous Page Link --}}
                                @if ($payments->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link"><i class="fas fa-chevron-right"></i></span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $payments->previousPageUrl() }}" rel="prev">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                @endif

                                {{-- Pagination Elements --}}
                                @foreach ($payments->getUrlRange(1, $payments->lastPage()) as $page => $url)
                                    @if ($page == $payments->currentPage())
                                        <li class="page-item active">
                                            <span class="page-link">{{ $page }}</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                        </li>
                                    @endif
                                @endforeach

                                {{-- Next Page Link --}}
                                @if ($payments->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $payments->nextPageUrl() }}" rel="next">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link"><i class="fas fa-chevron-left"></i></span>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <!-- مودال إضافة دفعة جديدة -->
    <div class="modal fade" id="addPaymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">تسديد دفعة جديدة</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.contracts.payments.store', $contract->id) }}" method="POST"
                    enctype="multipart/form-data" id="addPaymentForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-money-bill-wave me-2"></i>
                                    المبلغ <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" name="amount" class="form-control" step="0.01"
                                        min="0.01" max="{{ $contract->remaining_amount }}" required>
                                    <span class="input-group-text">ر.س</span>
                                </div>
                                <small class="text-muted">المتبقي: {{ number_format($contract->remaining_amount, 2) }}
                                    ر.س</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-calendar me-2"></i>
                                    تاريخ الدفع <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="payment_date" class="form-control"
                                    value="{{ now()->format('Y-m-d') }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-credit-card me-2"></i>
                                    طريقة الدفع <span class="text-danger">*</span>
                                </label>
                                <select name="payment_method" class="form-select" required>
                                    <option value="">اختر طريقة الدفع</option>
                                    <option value="cash">نقدي</option>
                                    <option value="card">بطاقة ائتمان</option>
                                    <option value="bank_transfer">تحويل بنكي</option>
                                    <option value="wallet">محفظة إلكترونية</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-tag me-2"></i>
                                    الحالة <span class="text-danger">*</span>
                                </label>
                                <select name="status" class="form-select" required>
                                    <option value="completed">مكتملة</option>
                                    <option value="pending">معلقة</option>
                                    <option value="failed">فاشلة</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-hashtag me-2"></i>
                                    رقم العملية
                                </label>
                                <input type="text" name="transaction_id" class="form-control"
                                    placeholder="رقم العملية من البنك">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-reference me-2"></i>
                                    رقم المرجع
                                </label>
                                <input type="text" name="reference_number" class="form-control"
                                    placeholder="رقم مرجعي للدفعة">
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-sticky-note me-2"></i>
                                    ملاحظات
                                </label>
                                <textarea name="notes" class="form-control" rows="3" placeholder="ملاحظات إضافية عن الدفعة..."></textarea>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-paperclip me-2"></i>
                                    إرفاق إيصال
                                </label>
                                <input type="file" name="receipt" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">PDF, JPG, PNG (الحد الأقصى 5MB)</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-success">تسجيل الدفعة</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- مودال عرض الإيصال -->
    <div class="modal fade" id="receiptModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">إيصال الدفع</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body" id="receiptContent">
                    <!-- محتوى الإيصال سيتم تحميله ديناميكياً -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="printReceiptModal()">
                        <i class="fas fa-print me-2"></i>
                        طباعة
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // تفعيل tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // التحقق من صحة نموذج إضافة دفعة
            $('#addPaymentForm').on('submit', function(e) {
                let amount = parseFloat($('input[name="amount"]').val()) || 0;
                let remaining = {{ $contract->remaining_amount }};

                if (amount > remaining) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: 'المبلغ المدفوع لا يمكن أن يكون أكبر من المتبقي'
                    });
                    return false;
                }

                if (amount <= 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: 'الرجاء إدخال مبلغ صحيح'
                    });
                    return false;
                }
            });

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

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: "{{ session('error') }}",
                    timer: 2000,
                    showConfirmButton: false
                });
            @endif
        });

        // عرض الإيصال
        function viewReceipt(paymentId) {
            $.ajax({
                url: `{{ url('admin/payments') }}/${paymentId}/receipt`,
                type: 'GET',
                beforeSend: function() {
                    $('#receiptContent').html(
                        '<div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-3x"></i></div>');
                },
                success: function(response) {
                    $('#receiptContent').html(response);
                    $('#receiptModal').modal('show');
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: 'حدث خطأ أثناء تحميل الإيصال'
                    });
                }
            });
        }

        // طباعة الإيصال
        function printReceipt(paymentId) {
            window.open(`{{ url('admin/payments') }}/${paymentId}/print`, '_blank');
        }

        function printReceiptModal() {
            window.print();
        }

        // تعديل دفعة
        function editPayment(paymentId) {
            // يمكن توجيه المستخدم إلى صفحة تعديل الدفعة
            window.location.href = `{{ url('admin/payments') }}/${paymentId}/edit`;
        }

        // استرجاع دفعة
        function refundPayment(paymentId) {
            Swal.fire({
                title: 'تأكيد الاسترجاع',
                text: 'هل أنت متأكد من استرجاع هذه الدفعة؟',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'نعم، استرجاع',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ url('admin/payments') }}/${paymentId}/refund`,
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'تم الاسترجاع',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ',
                                text: xhr.responseJSON?.message || 'حدث خطأ أثناء الاسترجاع'
                            });
                        }
                    });
                }
            });
        }

        // حذف دفعة
        function deletePayment(paymentId) {
            Swal.fire({
                title: 'تأكيد الحذف',
                text: 'هل أنت متأكد من حذف هذه الدفعة؟',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ url('admin/payments') }}/${paymentId}`,
                        type: 'DELETE',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'تم الحذف',
                                text: response.message || 'تم حذف الدفعة بنجاح',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ',
                                text: xhr.responseJSON?.message || 'حدث خطأ أثناء الحذف'
                            });
                        }
                    });
                }
            });
        }

        // تصدير المدفوعات
        function exportPayments(format) {
            let url = `{{ route('admin.contracts.payments.export', $contract->id) }}?format=${format}`;

            // إضافة معاملات الفلترة
            let status = document.querySelector('select[name="status"]').value;
            let paymentMethod = document.querySelector('select[name="payment_method"]').value;
            let dateFrom = document.querySelector('input[name="date_from"]').value;
            let dateTo = document.querySelector('input[name="date_to"]').value;

            if (status) url += `&status=${status}`;
            if (paymentMethod) url += `&payment_method=${paymentMethod}`;
            if (dateFrom) url += `&date_from=${dateFrom}`;
            if (dateTo) url += `&date_to=${dateTo}`;

            window.location.href = url;
        }

        // طباعة المدفوعات
        function printPayments() {
            window.print();
        }

        // تحديث الملخص
        function updateSummary() {
            // يمكن تحديث الملخص ديناميكياً إذا لزم الأمر
        }
    </script>
@endsection
