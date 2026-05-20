@extends('Admin.layout.master')

@section('title', 'إدارة العمليات')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --operations-primary: #696cff;
            --operations-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --operations-success: #198754;
            --operations-danger: #dc3545;
            --operations-warning: #ffc107;
            --operations-info: #0dcaf0;
        }

        body {
            font-family: "Cairo", sans-serif !important;
        }

        .operations-page {
            padding-top: 24px;
            padding-bottom: 24px;
        }

        .hero-card {
            background: var(--operations-gradient);
            color: #fff;
            border-radius: 18px;
            padding: 28px;
            box-shadow: 0 16px 38px rgba(102, 126, 234, 0.22);
            margin-bottom: 24px;
        }

        .hero-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 18px;
            flex-wrap: wrap;
        }

        .hero-title {
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .hero-subtitle {
            margin-bottom: 0;
            opacity: 0.92;
            font-size: 15px;
        }

        .hero-icon {
            width: 74px;
            height: 74px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            background: rgba(255, 255, 255, 0.18);
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.16);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 18px;
            margin-bottom: 24px;
        }

        .stat-card,
        .filter-card,
        .table-card,
        .detail-panel {
            background: var(--bs-card-bg);
            border-radius: 16px;
            box-shadow: 0 12px 28px rgba(18, 38, 63, 0.08);
        }

        .stat-card {
            padding: 22px;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(105, 108, 255, 0.08);
        }

        .stat-card::before {
            content: "";
            position: absolute;
            inset-inline-start: 0;
            inset-block: 0;
            width: 5px;
            background: var(--operations-gradient);
        }

        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-bottom: 16px;
            color: #fff;
            background: var(--operations-gradient);
        }

        .stat-label {
            color: var(--bs-secondary-color);
            font-size: 14px;
            margin-bottom: 8px;
        }

        .stat-number {
            font-size: 28px;
            font-weight: 700;
            color: var(--bs-heading-color);
            margin-bottom: 0;
        }

        .filter-card,
        .table-card {
            padding: 22px;
            margin-bottom: 24px;
        }

        .section-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 18px;
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 0;
            color: var(--bs-heading-color);
        }

        .section-title i {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            background: var(--operations-gradient);
        }

        .section-note {
            color: var(--bs-secondary-color);
            font-size: 13px;
            margin-bottom: 0;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
            gap: 16px;
        }

        .filter-group label {
            font-size: 13px;
            font-weight: 700;
            color: var(--bs-heading-color);
            margin-bottom: 8px;
            display: block;
        }

        .filter-group .form-control,
        .filter-group .form-select {
            border-radius: 12px;
            min-height: 46px;
        }

        .filter-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 18px;
            flex-wrap: wrap;
        }

        .btn-primary-gradient {
            border: none;
            color: #fff;
            background: var(--operations-gradient);
            border-radius: 12px;
            padding: 10px 18px;
            font-weight: 700;
        }

        .btn-light-bordered {
            border-radius: 12px;
            padding: 10px 18px;
            font-weight: 700;
            border: 1px solid var(--bs-border-color);
            background: transparent;
            color: var(--bs-heading-color);
        }

        .operations-table {
            margin-bottom: 0;
            min-width: 1120px;
        }

        .operations-table th {
            color: var(--bs-secondary-color);
            font-size: 13px;
            font-weight: 700;
            white-space: nowrap;
            border-bottom-width: 1px;
        }

        .operations-table td {
            vertical-align: middle;
            border-color: var(--bs-border-color-translucent);
        }

        .ref-block strong,
        .owner-block strong {
            display: block;
            color: var(--bs-heading-color);
            font-size: 14px;
        }

        .mini-text {
            color: var(--bs-secondary-color);
            font-size: 12px;
        }

        .type-chip,
        .status-chip,
        .direction-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }

        .type-chip {
            background: rgba(105, 108, 255, 0.12);
            color: var(--operations-primary);
        }

        .direction-chip.credit {
            background: rgba(25, 135, 84, 0.12);
            color: var(--operations-success);
        }

        .direction-chip.debit {
            background: rgba(220, 53, 69, 0.12);
            color: var(--operations-danger);
        }

        .status-chip.completed {
            background: rgba(25, 135, 84, 0.12);
            color: var(--operations-success);
        }

        .status-chip.pending {
            background: rgba(255, 193, 7, 0.16);
            color: #9a6b00;
        }

        .status-chip.processing,
        .status-chip.approved {
            background: rgba(13, 202, 240, 0.14);
            color: #0b7c92;
        }

        .status-chip.failed,
        .status-chip.cancelled {
            background: rgba(220, 53, 69, 0.12);
            color: var(--operations-danger);
        }

        .amount-block {
            font-weight: 700;
            color: var(--bs-heading-color);
        }

        .amount-block.credit {
            color: var(--operations-success);
        }

        .amount-block.debit {
            color: var(--operations-danger);
        }

        .table-actions {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .icon-btn {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            background: var(--operations-gradient);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.18);
        }

        .icon-btn.secondary {
            background: rgba(108, 117, 125, 0.12);
            color: var(--bs-heading-color);
            box-shadow: none;
        }

        .owner-link {
            color: inherit;
            text-decoration: none;
        }

        .owner-link:hover strong {
            color: var(--operations-primary);
        }

        .pagination-shell {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .empty-state {
            padding: 40px 18px;
            text-align: center;
            color: var(--bs-secondary-color);
        }

        .detail-panel {
            padding: 18px;
            height: 100%;
            border: 1px solid rgba(105, 108, 255, 0.08);
        }

        .detail-panel h6 {
            color: var(--operations-primary);
            font-weight: 700;
            margin-bottom: 14px;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            padding: 10px 0;
            border-bottom: 1px solid var(--bs-border-color-translucent);
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-item-label {
            color: var(--bs-secondary-color);
            font-size: 13px;
            min-width: 118px;
        }

        .detail-item-value {
            text-align: start;
            color: var(--bs-heading-color);
            font-weight: 600;
            word-break: break-word;
        }

        .detail-badges {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .metadata-box {
            background: #0f172a;
            color: #e2e8f0;
            border-radius: 14px;
            padding: 16px;
            font-size: 12px;
            line-height: 1.75;
            max-height: 320px;
            overflow: auto;
            margin-bottom: 0;
        }

        .modal-content {
            border-radius: 18px;
            overflow: hidden;
        }

        .modal-header {
            border-bottom: 1px solid var(--bs-border-color-translucent);
        }

        .modal-title {
            font-weight: 700;
        }

        .detail-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .modal-loading {
            padding: 50px 20px;
            text-align: center;
            color: var(--bs-secondary-color);
        }

        @media (max-width: 767.98px) {
            .hero-card,
            .filter-card,
            .table-card {
                padding: 18px;
            }

            .hero-title {
                font-size: 22px;
            }

            .pagination-shell,
            .detail-item {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y operations-page">
        <div class="hero-card">
            <div class="hero-content">
                <div>
                    <h1 class="hero-title">صفحة العمليات المالية</h1>
                    <p class="hero-subtitle">
                        متابعة كل عمليات `LedgerEntry` من مكان واحد مع الفلاتر، بيانات المالك، والحالة، وكل التفاصيل
                        المرتبطة بكل عملية.
                    </p>
                </div>
                <div class="hero-icon">
                    <i class="fas fa-receipt"></i>
                </div>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-layer-group"></i></div>
                <div class="stat-label">إجمالي العمليات</div>
                <h3 class="stat-number">{{ number_format($totalOperations) }}</h3>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-label">عمليات مكتملة</div>
                <h3 class="stat-number">{{ number_format($completedOperations) }}</h3>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
                <div class="stat-label">تحتاج متابعة</div>
                <h3 class="stat-number">{{ number_format($pendingOperations) }}</h3>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-arrow-trend-up"></i></div>
                <div class="stat-label">عمليات دائنة</div>
                <h3 class="stat-number">{{ number_format($creditOperations) }}</h3>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-money-bill-wave"></i></div>
                <div class="stat-label">إجمالي القيمة</div>
                <h3 class="stat-number">{{ number_format((float) $totalVolume, 2) }} {{ $defaultCurrency }}</h3>
            </div>
        </div>

        <div class="filter-card">
            <div class="section-heading">
                <div>
                    <h5 class="section-title">
                        <i class="fas fa-filter"></i>
                        <span>فلترة العمليات</span>
                    </h5>
                    <p class="section-note">ابحث بالمرجع أو الوصف أو رقم العملية أو اسم العميل/السائق.</p>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.operations.index') }}">
                <div class="filter-grid">
                    <div class="filter-group">
                        <label for="search">بحث</label>
                        <input type="text" class="form-control" id="search" name="search"
                            value="{{ $filters['search'] ?? '' }}" placeholder="مرجع، وصف، اسم، هاتف">
                    </div>
                    <div class="filter-group">
                        <label for="status">الحالة</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">الكل</option>
                            @foreach ($statusLabels as $value => $label)
                                <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="type">نوع العملية</label>
                        <select class="form-select" id="type" name="type">
                            <option value="">الكل</option>
                            @foreach ($typeLabels as $value => $label)
                                <option value="{{ $value }}" @selected(($filters['type'] ?? '') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="owner_type">نوع المالك</label>
                        <select class="form-select" id="owner_type" name="owner_type">
                            <option value="">الكل</option>
                            @foreach ($ownerTypeLabels as $value => $label)
                                <option value="{{ $value }}" @selected(($filters['owner_type'] ?? '') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="wallet_type">نوع المحفظة</label>
                        <select class="form-select" id="wallet_type" name="wallet_type">
                            <option value="">الكل</option>
                            @foreach ($walletTypeLabels as $value => $label)
                                <option value="{{ $value }}" @selected(($filters['wallet_type'] ?? '') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="payment_method">طريقة الدفع</label>
                        <select class="form-select" id="payment_method" name="payment_method">
                            <option value="">الكل</option>
                            @foreach ($paymentMethods as $method)
                                <option value="{{ $method }}" @selected(($filters['payment_method'] ?? '') === $method)>{{ $method }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="date_from">من تاريخ</label>
                        <input type="date" class="form-control" id="date_from" name="date_from"
                            value="{{ $filters['date_from'] ?? '' }}">
                    </div>
                    <div class="filter-group">
                        <label for="date_to">إلى تاريخ</label>
                        <input type="date" class="form-control" id="date_to" name="date_to"
                            value="{{ $filters['date_to'] ?? '' }}">
                    </div>
                    <div class="filter-group">
                        <label for="min_amount">أقل مبلغ</label>
                        <input type="number" step="0.01" class="form-control" id="min_amount" name="min_amount"
                            value="{{ $filters['min_amount'] ?? '' }}" placeholder="0.00">
                    </div>
                    <div class="filter-group">
                        <label for="max_amount">أعلى مبلغ</label>
                        <input type="number" step="0.01" class="form-control" id="max_amount" name="max_amount"
                            value="{{ $filters['max_amount'] ?? '' }}" placeholder="0.00">
                    </div>
                </div>

                <div class="filter-actions">
                    <a href="{{ route('admin.operations.index') }}" class="btn-light-bordered">
                        <i class="fas fa-rotate-left me-1"></i>
                        إعادة ضبط
                    </a>
                    <button type="submit" class="btn-primary-gradient">
                        <i class="fas fa-magnifying-glass me-1"></i>
                        تطبيق الفلاتر
                    </button>
                </div>
            </form>
        </div>

        <div class="table-card">
            <div class="section-heading">
                <div>
                    <h5 class="section-title">
                        <i class="fas fa-table-list"></i>
                        <span>سجل العمليات</span>
                    </h5>
                    <p class="section-note">
                        @if ($operations->total() > 0)
                            عرض {{ $operations->firstItem() }} - {{ $operations->lastItem() }} من أصل
                            {{ $operations->total() }} عملية
                        @else
                            لا توجد نتائج مطابقة للفلاتر الحالية.
                        @endif
                    </p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table operations-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>المرجع</th>
                            <th>المالك</th>
                            <th>التصنيف</th>
                            <th>المبلغ</th>
                            <th>الحالة</th>
                            <th>الدفع</th>
                            <th>التاريخ</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($operations as $operation)
                            <tr>
                                <td>{{ $operation->id }}</td>
                                <td>
                                    <div class="ref-block">
                                        <strong>{{ $operation->reference }}</strong>
                                        <span class="mini-text">Wallet #{{ $operation->wallet_id }} |
                                            {{ $operation->wallet_type_label }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="owner-block">
                                        @if ($operation->owner_url)
                                            <a href="{{ $operation->owner_url }}" class="owner-link">
                                                <strong>{{ $operation->owner_name }}</strong>
                                            </a>
                                        @else
                                            <strong>{{ $operation->owner_name }}</strong>
                                        @endif
                                        <span class="mini-text">{{ $operation->owner_type_label }}
                                            @if ($operation->owner_subtitle)
                                                | {{ $operation->owner_subtitle }}
                                            @endif
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-2">
                                        <span class="type-chip">{{ $operation->type_label }}</span>
                                        <span class="direction-chip {{ $operation->direction }}">
                                            <i
                                                class="fas {{ $operation->direction === 'credit' ? 'fa-arrow-down' : 'fa-arrow-up' }}"></i>
                                            {{ $operation->direction_label }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="amount-block {{ $operation->direction }}">
                                        {{ number_format((float) $operation->amount, 2) }} {{ $defaultCurrency }}
                                    </div>
                                    <span class="mini-text">
                                        قبل: {{ number_format((float) $operation->balance_before, 2) }} |
                                        بعد: {{ number_format((float) $operation->balance_after, 2) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="status-chip {{ $operation->status }}">
                                        {{ $operation->status_label }}
                                    </span>
                                    @if ($operation->can_review)
                                        <div class="mini-text mt-2">تحتاج مراجعة من الإدارة</div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $operation->payment_method ?: '-' }}</strong>
                                    <div class="mini-text">
                                        {{ $operation->payment_identifier ?: ($operation->payment_transaction_id ?: 'لا يوجد رقم دفع') }}
                                    </div>
                                </td>
                                <td>
                                    <strong>{{ $operation->created_at?->format('Y-m-d') }}</strong>
                                    <div class="mini-text">{{ $operation->created_at?->format('H:i') }}</div>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <button type="button" class="icon-btn"
                                            onclick="showOperationDetails({{ $operation->id }})" title="التفاصيل">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if ($operation->owner_url)
                                            <a href="{{ $operation->owner_url }}" class="icon-btn secondary"
                                                title="عرض المالك">
                                                <i class="fas fa-arrow-up-right-from-square"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">
                                    <div class="empty-state">
                                        <i class="fas fa-inbox fa-2x mb-3 d-block"></i>
                                        لا توجد عمليات حالية لعرضها.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($operations->hasPages())
                <div class="pagination-shell">
                    <div class="mini-text">
                        الصفحة {{ $operations->currentPage() }} من {{ $operations->lastPage() }}
                    </div>
                    <div>
                        {{ $operations->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="modal fade" id="operationDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">تفاصيل العملية</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="operationDetailsContent">
                    <div class="modal-loading">
                        <div class="spinner-border text-primary mb-3" role="status"></div>
                        <div>جاري تحميل التفاصيل...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        let currentOperationId = null;
        const operationDetailsModal = new bootstrap.Modal(document.getElementById('operationDetailsModal'));

        function showOperationDetails(operationId) {
            currentOperationId = operationId;
            $('#operationDetailsContent').html(`
                <div class="modal-loading">
                    <div class="spinner-border text-primary mb-3" role="status"></div>
                    <div>جاري تحميل التفاصيل...</div>
                </div>
            `);

            operationDetailsModal.show();

            $.ajax({
                url: `/admin/operations/${operationId}`,
                type: 'GET',
                success: function(response) {
                    if (!response.success) {
                        renderDetailsError(response.message || 'تعذر تحميل بيانات العملية.');
                        return;
                    }

                    $('#operationDetailsContent').html(renderOperationDetails(response.entry));
                },
                error: function(xhr) {
                    renderDetailsError(xhr.responseJSON?.message || 'حدث خطأ أثناء تحميل بيانات العملية.');
                }
            });
        }

        function renderDetailsError(message) {
            $('#operationDetailsContent').html(`
                <div class="alert alert-danger mb-0">
                    <i class="fas fa-circle-exclamation me-2"></i>
                    ${escapeHtml(message)}
                </div>
            `);
        }

        function renderOperationDetails(entry) {
            const metadata = entry.metadata_pretty
                ? `<pre class="metadata-box">${escapeHtml(entry.metadata_pretty)}</pre>`
                : `<div class="alert alert-light mb-0">لا توجد بيانات إضافية مسجلة لهذه العملية.</div>`;

            const relatedEntryHtml = entry.related_entry ? `
                <div class="detail-item">
                    <div class="detail-item-label">رقم العملية</div>
                    <div class="detail-item-value">#${escapeHtml(String(entry.related_entry.id))}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-item-label">المرجع</div>
                    <div class="detail-item-value">${escapeHtml(entry.related_entry.reference || '-')}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-item-label">النوع</div>
                    <div class="detail-item-value">${escapeHtml(entry.related_entry.type_label || '-')}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-item-label">الحالة</div>
                    <div class="detail-item-value">${escapeHtml(entry.related_entry.status_label || '-')}</div>
                </div>
            ` : `<div class="alert alert-light mb-0">لا توجد عملية مرتبطة.</div>`;

            const relatedOwnerHtml = entry.related_owner && entry.related_owner.name !== 'غير محدد'
                ? ownerSummaryMarkup(entry.related_owner, 'الطرف المرتبط')
                : `<div class="alert alert-light mb-0">لا يوجد مالك مرتبط.</div>`;

            const reviewActions = entry.can_review ? `
                <div class="detail-actions">
                    <button type="button" class="btn btn-success" onclick="approveOperation('${entry.approve_url}')">
                        <i class="fas fa-check me-1"></i>
                        موافقة على العملية
                    </button>
                    <button type="button" class="btn btn-danger" onclick="rejectOperation('${entry.reject_url}')">
                        <i class="fas fa-xmark me-1"></i>
                        رفض العملية
                    </button>
                </div>
            ` : '';

            return `
                <div class="detail-badges">
                    <span class="type-chip">${escapeHtml(entry.type_label || '-')}</span>
                    <span class="direction-chip ${escapeHtml(entry.direction || '')}">
                        <i class="fas ${entry.direction === 'credit' ? 'fa-arrow-down' : 'fa-arrow-up'}"></i>
                        ${escapeHtml(entry.direction_label || '-')}
                    </span>
                    <span class="status-chip ${escapeHtml(entry.status || '')}">
                        ${escapeHtml(entry.status_label || '-')}
                    </span>
                </div>

                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="detail-panel">
                            <h6>بيانات أساسية</h6>
                            ${detailItem('رقم العملية', '#' + escapeHtml(String(entry.id)))}
                            ${detailItem('المرجع', escapeHtml(entry.reference || '-'))}
                            ${detailItem('نوع المحفظة', escapeHtml(entry.wallet_type_label || '-'))}
                            ${detailItem('رقم المحفظة', '#' + escapeHtml(String(entry.wallet_id || '-')))}
                            ${detailItem('نوع المالك', escapeHtml(entry.owner_type_label || '-'))}
                            ${detailItem('الوصف', escapeHtml(entry.description || '-'))}
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="detail-panel">
                            <h6>المبالغ والأرصدة</h6>
                            ${detailItem('المبلغ', escapeHtml(entry.amount || '0.00') + ' {{ $defaultCurrency }}')}
                            ${detailItem('الرصيد قبل', escapeHtml(entry.balance_before || '0.00') + ' {{ $defaultCurrency }}')}
                            ${detailItem('الرصيد بعد', escapeHtml(entry.balance_after || '0.00') + ' {{ $defaultCurrency }}')}
                            ${detailItem('المتاح قبل', escapeHtml(entry.available_balance_before || '0.00') + ' {{ $defaultCurrency }}')}
                            ${detailItem('المتاح بعد', escapeHtml(entry.available_balance_after || '0.00') + ' {{ $defaultCurrency }}')}
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="detail-panel">
                            <h6>الدفع والمعالجة</h6>
                            ${detailItem('طريقة الدفع', escapeHtml(entry.payment_method || '-'))}
                            ${detailItem('Transaction ID', escapeHtml(entry.payment_transaction_id || '-'))}
                            ${detailItem('Payment Identifier', escapeHtml(entry.payment_identifier || '-'))}
                            ${detailItem('تاريخ الإنشاء', escapeHtml(entry.created_at || '-'))}
                            ${detailItem('تاريخ المعالجة', escapeHtml(entry.processed_at || '-'))}
                            ${detailItem('تاريخ الاعتماد', escapeHtml(entry.approved_at || '-'))}
                            ${detailItem('اعتمد بواسطة', escapeHtml(entry.approved_by || '-'))}
                            ${detailItem('تاريخ الانتهاء', escapeHtml(entry.expires_at || '-'))}
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="detail-panel">
                            <h6>المالك الأساسي</h6>
                            ${ownerSummaryMarkup(entry.owner, 'المالك')}
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="detail-panel">
                            <h6>المالك أو الطرف المرتبط</h6>
                            ${relatedOwnerHtml}
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="detail-panel">
                            <h6>العملية المرتبطة</h6>
                            ${relatedEntryHtml}
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="detail-panel">
                            <h6>معلومات الشبكة</h6>
                            ${detailItem('IP Address', escapeHtml(entry.ip_address || '-'))}
                            ${detailItem('User Agent', escapeHtml(entry.user_agent || '-'))}
                            ${detailItem('آخر تحديث', escapeHtml(entry.updated_at || '-'))}
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="detail-panel">
                            <h6>Metadata</h6>
                            ${metadata}
                            ${reviewActions}
                        </div>
                    </div>
                </div>
            `;
        }

        function ownerSummaryMarkup(owner, title) {
            if (!owner) {
                return '<div class="alert alert-light mb-0">لا توجد بيانات.</div>';
            }

            const name = escapeHtml(owner.name || '-');
            const typeLabel = escapeHtml(owner.type_label || '-');
            const subtitle = owner.subtitle ? escapeHtml(owner.subtitle) : '-';
            const url = owner.url ? `
                <a href="${escapeHtml(owner.url)}" class="btn btn-outline-primary btn-sm mt-3">
                    <i class="fas fa-arrow-up-right-from-square me-1"></i>
                    فتح صفحة ${escapeHtml(title)}
                </a>
            ` : '';

            return `
                ${detailItem('الاسم', name)}
                ${detailItem('النوع', typeLabel)}
                ${detailItem('المعرف', owner.id ? '#' + escapeHtml(String(owner.id)) : '-')}
                ${detailItem('بيانات إضافية', subtitle)}
                ${url}
            `;
        }

        function detailItem(label, value) {
            return `
                <div class="detail-item">
                    <div class="detail-item-label">${label}</div>
                    <div class="detail-item-value">${value}</div>
                </div>
            `;
        }

        function approveOperation(url) {
            Swal.fire({
                title: 'موافقة على العملية',
                text: 'هل تريد اعتماد هذه العملية الآن؟',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'نعم',
                cancelButtonText: 'إلغاء',
                confirmButtonColor: '#198754'
            }).then((result) => {
                if (result.isConfirmed) {
                    sendReviewRequest(url, {});
                }
            });
        }

        function rejectOperation(url) {
            Swal.fire({
                title: 'رفض العملية',
                input: 'text',
                inputLabel: 'سبب الرفض (اختياري)',
                inputPlaceholder: 'اكتب سبب الرفض',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'رفض العملية',
                cancelButtonText: 'إلغاء',
                confirmButtonColor: '#dc3545'
            }).then((result) => {
                if (result.isConfirmed) {
                    sendReviewRequest(url, {
                        reason: result.value || ''
                    });
                }
            });
        }

        function sendReviewRequest(url, data = {}) {
            $.ajax({
                url: url,
                type: 'POST',
                data: Object.assign({
                    _token: '{{ csrf_token() }}'
                }, data),
                beforeSend: function() {
                    Swal.fire({
                        title: 'جاري المعالجة...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم التنفيذ',
                        text: response.message || 'تم تحديث العملية بنجاح'
                    }).then(() => {
                        window.location.reload();
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'تعذر تنفيذ الطلب',
                        text: xhr.responseJSON?.message || 'حدث خطأ غير متوقع'
                    });
                }
            });
        }

        function escapeHtml(value) {
            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }
    </script>
@endsection
