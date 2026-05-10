@extends('Admin.layout.master')

@section('title', 'تعديل الطلب')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

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

        .order-create-card {
            background: var(--dark-card);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .order-create-header {
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .form-section,
        .summary-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .form-section h6,
        .summary-card h6 {
            color: var(--primary-color);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
        }

        .required::after {
            content: " *";
            color: var(--danger-color);
        }

        .alert-guide {
            background: rgba(12, 99, 228, 0.1);
            border-right: 4px solid var(--primary-color);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            border: 1px solid rgba(12, 99, 228, 0.2);
        }

        .alert-lock {
            background: rgba(220, 53, 69, 0.1);
            border-right: 4px solid var(--danger-color);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            border: 1px solid rgba(220, 53, 69, 0.25);
            color: #fff;
        }

        .form-control,
        .form-select {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1);
            color: #fff;
            border-radius: 8px;
            padding: 10px 15px;
        }

        .form-control:focus,
        .form-select:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--primary-color);
            color: #fff;
            box-shadow: 0 0 0 0.25rem rgba(105, 108, 255, 0.25);
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .form-control:disabled,
        .form-select:disabled,
        textarea:disabled {
            background: rgba(255, 255, 255, 0.04) !important;
            color: rgba(255, 255, 255, 0.55) !important;
            cursor: not-allowed;
            opacity: 0.75;
        }

        .form-label {
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
        }

        .help-text {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.6);
            margin-top: 5px;
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

        .btn-success {
            background: #20c997;
            border: none;
        }

        .btn-success:hover {
            background: #1ba87e;
        }

        .btn-danger {
            background: #dc3545;
            border: none;
        }

        .btn-danger:hover {
            background: #bb2d3b;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .choice-box {
            display: block;
            position: relative;
            cursor: pointer;
            height: 100%;
        }

        .choice-box.locked {
            cursor: not-allowed;
            opacity: 0.75;
        }

        .choice-box input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .choice-content {
            height: 100%;
            border-radius: 12px;
            padding: 18px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.12);
            transition: all 0.2s ease-in-out;
        }

        .choice-content i {
            font-size: 26px;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .choice-title {
            font-weight: 700;
            margin-bottom: 5px;
            color: #fff;
        }

        .choice-desc {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.65);
            line-height: 1.7;
        }

        .choice-box input:checked+.choice-content {
            border-color: var(--primary-color);
            background: rgba(105, 108, 255, 0.18);
            box-shadow: 0 0 0 0.2rem rgba(105, 108, 255, 0.18);
        }

        .contract-details-card {
            display: none;
            background: rgba(32, 201, 151, 0.08);
            border: 1px solid rgba(32, 201, 151, 0.25);
            border-radius: 12px;
            padding: 15px;
            margin-top: 15px;
        }

        .contract-details-card .info-line {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            padding: 8px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            font-size: 13px;
        }

        .contract-details-card .info-line:last-child {
            border-bottom: 0;
        }

        .contract-details-card .info-label {
            color: rgba(255, 255, 255, 0.65);
        }

        .contract-details-card .info-value {
            color: #fff;
            font-weight: 600;
            text-align: left;
        }

        .soft-warning {
            display: none;
            background: rgba(255, 193, 7, 0.1);
            border: 1px solid rgba(255, 193, 7, 0.25);
            color: #ffc107;
            border-radius: 10px;
            padding: 12px 15px;
            margin-top: 10px;
            font-size: 13px;
        }

        .soft-info {
            background: rgba(23, 162, 184, 0.08);
            border: 1px solid rgba(23, 162, 184, 0.22);
            color: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 13px;
            margin-top: 10px;
        }

        .select2-container--default .select2-selection--single {
            background: rgba(255, 255, 255, 0.05) !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
            border-radius: 8px !important;
            height: 43px !important;
            color: #fff !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #fff !important;
            line-height: 43px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 43px !important;
        }

        .select2-container--default.select2-container--disabled .select2-selection--single {
            background: rgba(255, 255, 255, 0.04) !important;
            opacity: 0.75;
            cursor: not-allowed;
        }

        .select2-dropdown {
            background: #2b3b4c !important;
            border-color: rgba(255, 255, 255, 0.15) !important;
            color: #fff !important;
        }

        .select2-results__option {
            color: #fff !important;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background: var(--primary-color) !important;
        }

        @media (max-width: 768px) {
            .order-create-card {
                padding: 20px;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $originalStatus = collect($orderStatuses ?? [])->firstWhere('id', $order->order_status_id);
        $originalStatusName =
            $originalStatus->name ?? (optional($order->orderStatus)->name ?? (optional($order->status)->name ?? ''));
        $isInRoad = $originalStatusName === 'in-road';

        $cancelledStatus =
            collect($orderStatuses ?? [])->firstWhere('name', 'cancelled') ??
            (collect($orderStatuses ?? [])->firstWhere('name', 'canceled') ??
                collect($orderStatuses ?? [])->firstWhere('name', 'cancel'));

        $cancelledStatusId = $cancelledStatus->id ?? null;

        try {
            $orderDateValue = old(
                'order_date',
                $order->order_date
                    ? \Carbon\Carbon::parse($order->order_date)->format('Y-m-d\TH:i')
                    : now()->format('Y-m-d\TH:i'),
            );
        } catch (\Throwable $e) {
            $orderDateValue = old('order_date', now()->format('Y-m-d\TH:i'));
        }

        $oldIsContract = old('is_contract', $order->contract_id ? '1' : '0');
        $oldOrderType = old('order_type', $originalStatusName === 'scheduled' ? 'scheduled' : 'current');

        $oldUserId = old('user_id', $order->user_id);
        $oldContractId = old('contract_id', $order->contract_id);
        $oldSavedLocationId = old('saved_location_id', $order->saved_location_id);
        $oldDriverId = old('driver_id', $order->driver_id);

        $contractsSource = collect($contracts ?? []);

        if ($contractsSource->isEmpty() && isset($users)) {
            try {
                $contractsSource = \App\Models\Contract::query()
                    ->whereIn('user_id', collect($users)->pluck('id')->filter()->values())
                    ->orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")
                    ->orderByDesc('created_at')
                    ->get();
            } catch (\Throwable $e) {
                $contractsSource = collect();
            }
        }

        $contractsForJs = $contractsSource
            ->map(function ($contract) {
                return [
                    'id' => $contract->id,
                    'user_id' => $contract->user_id,
                    'contract_number' => $contract->contract_number,
                    'contract_type' => $contract->contract_type,
                    'company_name' => $contract->company_name,
                    'applicant_name' => $contract->applicant_name,
                    'duration_type' => $contract->duration_type,
                    'start_date' => optional($contract->start_date)->format('Y-m-d'),
                    'end_date' => optional($contract->end_date)->format('Y-m-d'),
                    'renewal_date' => optional($contract->renewal_date)->format('Y-m-d'),
                    'total_orders_limit' => $contract->total_orders_limit,
                    'remaining_orders' => $contract->remaining_orders,
                    'total_amount' => $contract->total_amount,
                    'paid_amount' => $contract->paid_amount,
                    'remaining_amount' => $contract->remaining_amount,
                    'status' => $contract->status,
                    'phone' => $contract->phone,
                    'notes' => $contract->notes,
                ];
            })
            ->values();

        $disabled = $isInRoad ? 'disabled' : '';
        $lockedClass = $isInRoad ? 'locked' : '';
    @endphp

    <div class="container-xxl flex-grow-1 container-p-y">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.index') }}">الرئيسية</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.orders.index') }}">الطلبات</a>
                </li>
                <li class="breadcrumb-item active">تعديل الطلب</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-12">
                <div class="order-create-card">
                    <div class="order-create-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1">تعديل الطلب #{{ $order->id }}</h5>
                                <small class="text-muted">
                                    @if ($isInRoad)
                                        الطلب في الطريق، لذلك التعديل غير متاح.
                                    @else
                                        تعديل بيانات طلب التوصيل.
                                    @endif
                                </small>
                            </div>

                            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-right me-2"></i>رجوع للقائمة
                            </a>
                        </div>
                    </div>

                    @if ($isInRoad)
                        <div class="alert-lock">
                            <h6 class="mb-2">
                                <i class="fas fa-lock me-2"></i>
                                الطلب في الطريق
                            </h6>
                            <div>
                                لا يمكن تعديل بيانات الطلب بعد دخوله حالة في الطريق.
                                الإجراء الوحيد المتاح هو إلغاء الطلب.
                            </div>
                        </div>
                    @else
                        <div class="alert-guide">
                            <h6><i class="fas fa-lightbulb me-2"></i>إرشادات سريعة</h6>
                            <ul class="mb-0">
                                <li>يمكنك تعديل بيانات الطلب طالما لم يدخل حالة في الطريق.</li>
                                <li>لو تم اختيار حالة في الطريق يجب تحديد السائق.</li>
                                <li>لو الطلب تعاقد، اختر العميل أولاً ثم اختر العقد الخاص به.</li>
                                <li>لو الطلب مجدول، يجب تحديد تاريخ ووقت الطلب.</li>
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
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

                    <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" id="editOrderForm">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-lg-8">

                                {{-- نوع الطلب: تعاقد أم لا --}}
                                <div class="form-section">
                                    <h6><i class="fas fa-file-contract me-2"></i>نوع الطلب</h6>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="choice-box {{ $lockedClass }}">
                                                <input type="radio" name="is_contract" value="0" id="is_contract_no"
                                                    {{ $oldIsContract == '0' ? 'checked' : '' }} {{ $disabled }}>
                                                <div class="choice-content">
                                                    <i class="fas fa-receipt"></i>
                                                    <div class="choice-title">طلب عادي</div>
                                                    <div class="choice-desc">
                                                        طلب بدون ربطه بأي تعاقد، ويتم اختيار العميل والعنوان والخدمة بشكل
                                                        طبيعي.
                                                    </div>
                                                </div>
                                            </label>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="choice-box {{ $lockedClass }}">
                                                <input type="radio" name="is_contract" value="1" id="is_contract_yes"
                                                    {{ $oldIsContract == '1' ? 'checked' : '' }} {{ $disabled }}>
                                                <div class="choice-content">
                                                    <i class="fas fa-file-signature"></i>
                                                    <div class="choice-title">طلب تعاقد</div>
                                                    <div class="choice-desc">
                                                        اختر العميل أولاً، وبعدها سيتم عرض التعاقدات الخاصة به لاختيار
                                                        العقد.
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                {{-- توقيت الطلب --}}
                                <div class="form-section">
                                    <h6><i class="fas fa-clock me-2"></i>توقيت الطلب</h6>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="choice-box {{ $lockedClass }}">
                                                <input type="radio" name="order_type" value="current"
                                                    id="order_type_current"
                                                    {{ $oldOrderType == 'current' ? 'checked' : '' }} {{ $disabled }}>
                                                <div class="choice-content">
                                                    <i class="fas fa-bolt"></i>
                                                    <div class="choice-title">طلب حالي</div>
                                                    <div class="choice-desc">
                                                        سيتم تسجيل وقت الطلب بتاريخ ووقت الآن تلقائياً.
                                                    </div>
                                                </div>
                                            </label>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="choice-box {{ $lockedClass }}">
                                                <input type="radio" name="order_type" value="scheduled"
                                                    id="order_type_scheduled"
                                                    {{ $oldOrderType == 'scheduled' ? 'checked' : '' }}
                                                    {{ $disabled }}>
                                                <div class="choice-content">
                                                    <i class="fas fa-calendar-alt"></i>
                                                    <div class="choice-title">طلب مجدول</div>
                                                    <div class="choice-desc">
                                                        حدد التاريخ والوقت المطلوب لتنفيذ الطلب.
                                                    </div>
                                                </div>
                                            </label>
                                        </div>

                                        <div class="col-md-6 mb-3" id="scheduleDateWrapper">
                                            <label for="order_date" class="form-label required">تاريخ ووقت الطلب</label>
                                            <input type="datetime-local" class="form-control" id="order_date"
                                                name="order_date" value="{{ $orderDateValue }}" required
                                                {{ $disabled }}>
                                            <div class="help-text">يظهر هذا الحقل عند اختيار طلب مجدول.</div>
                                        </div>
                                    </div>
                                </div>

                                {{-- معلومات العميل --}}
                                <div class="form-section">
                                    <h6><i class="fas fa-user me-2"></i>معلومات العميل</h6>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="user_id" class="form-label required">العميل</label>
                                            <select class="form-select select2" id="user_id" name="user_id" required
                                                {{ $disabled }}>
                                                <option value="">-- اختر عميل --</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}"
                                                        {{ (string) $oldUserId === (string) $user->id ? 'selected' : '' }}>
                                                        {{ $user->name }} -
                                                        {{ $user->phone ?? ($user->phone_number ?? 'بدون هاتف') }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="saved_location_id" class="form-label">العنوان المحفوظ</label>
                                            <select class="form-select select2" id="saved_location_id"
                                                name="saved_location_id" {{ $disabled }}>
                                                <option value="">-- اختر عنوان --</option>
                                            </select>
                                            <div class="help-text">اختر العميل أولاً لتحميل عناوينه المحفوظة.</div>
                                        </div>
                                    </div>
                                </div>

                                {{-- بيانات التعاقد --}}
                                <div class="form-section" id="contractSection">
                                    <h6><i class="fas fa-file-contract me-2"></i>بيانات التعاقد</h6>

                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label for="contract_id" class="form-label required">العقد</label>
                                            <select class="form-select select2" id="contract_id" name="contract_id"
                                                {{ $disabled }}>
                                                <option value="">-- اختر العقد --</option>
                                            </select>

                                            <div class="help-text">
                                                يتم عرض العقود الخاصة بالعميل المختار فقط.
                                            </div>

                                            <div class="soft-warning" id="noContractsWarning">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                لا توجد تعاقدات لهذا العميل.
                                            </div>

                                            <div class="contract-details-card" id="contractDetailsCard">
                                                <div class="info-line">
                                                    <span class="info-label">رقم العقد</span>
                                                    <span class="info-value" id="contractNumberText">-</span>
                                                </div>

                                                <div class="info-line">
                                                    <span class="info-label">نوع العقد</span>
                                                    <span class="info-value" id="contractTypeText">-</span>
                                                </div>

                                                <div class="info-line">
                                                    <span class="info-label">اسم مقدم الطلب</span>
                                                    <span class="info-value" id="contractApplicantText">-</span>
                                                </div>

                                                <div class="info-line">
                                                    <span class="info-label">الشركة</span>
                                                    <span class="info-value" id="contractCompanyText">-</span>
                                                </div>

                                                <div class="info-line">
                                                    <span class="info-label">الفترة</span>
                                                    <span class="info-value" id="contractDatesText">-</span>
                                                </div>

                                                <div class="info-line">
                                                    <span class="info-label">الطلبات المتبقية</span>
                                                    <span class="info-value" id="contractRemainingText">-</span>
                                                </div>

                                                <div class="info-line">
                                                    <span class="info-label">حالة العقد</span>
                                                    <span class="info-value" id="contractStatusText">-</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- تفاصيل الخدمة --}}
                                <div class="form-section">
                                    <h6><i class="fas fa-cogs me-2"></i>تفاصيل الخدمة</h6>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="service_id" class="form-label required">الخدمة</label>
                                            <select class="form-select select2" id="service_id" name="service_id"
                                                required {{ $disabled }}>
                                                <option value="">-- اختر خدمة --</option>
                                                @foreach ($services as $service)
                                                    <option value="{{ $service->id }}"
                                                        {{ (string) old('service_id', $order->service_id) === (string) $service->id ? 'selected' : '' }}>
                                                        {{ $service->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="water_type_id" class="form-label required">نوع المياه</label>
                                            <select class="form-select select2" id="water_type_id" name="water_type_id"
                                                required {{ $disabled }}>
                                                <option value="">-- اختر نوع المياه --</option>
                                                @foreach ($waterTypes as $type)
                                                    <option value="{{ $type->id }}"
                                                        {{ (string) old('water_type_id', $order->water_type_id) === (string) $type->id ? 'selected' : '' }}>
                                                        {{ $type->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="order_status_id" class="form-label required">حالة الطلب</label>
                                            <select class="form-select select2" id="order_status_id"
                                                name="order_status_id" required {{ $disabled }}>
                                                <option value="">-- اختر حالة --</option>
                                                @foreach ($orderStatuses as $status)
                                                    <option value="{{ $status->id }}"
                                                        data-status-name="{{ $status->name }}"
                                                        {{ (string) old('order_status_id', $order->order_status_id) === (string) $status->id ? 'selected' : '' }}>
                                                        {{ $status->label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="help-text">
                                                عند اختيار حالة في الطريق يجب تحديد السائق.
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3" id="driverWrapper" style="display: none;">
                                            <label for="driver_id" class="form-label required">السائق</label>
                                            <select class="form-select select2" id="driver_id" name="driver_id"
                                                {{ $disabled }}>
                                                <option value="">-- اختر السائق --</option>
                                                @foreach ($drivers ?? collect() as $driver)
                                                    <option value="{{ $driver->id }}"
                                                        {{ (string) $oldDriverId === (string) $driver->id ? 'selected' : '' }}>
                                                        {{ $driver->user->name ?? 'سائق بدون اسم' }}
                                                        -
                                                        {{ $driver->user->phone ?? ($driver->user->phone_number ?? 'بدون هاتف') }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="help-text">يجب اختيار السائق إذا كانت حالة الطلب في الطريق.</div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="col-lg-4">
                                {{-- معلومات الدفع --}}
                                <div class="summary-card mb-4">
                                    <h6><i class="fas fa-credit-card me-2"></i>معلومات الدفع</h6>

                                    <div class="mb-3">
                                        <label for="payment_method" class="form-label required">طريقة الدفع</label>
                                        <select class="form-select" id="payment_method" name="payment_method" required
                                            {{ $disabled }}>
                                            <option value="wallet"
                                                {{ old('payment_method', $order->payment_method) == 'wallet' ? 'selected' : '' }}>
                                                محفظة</option>
                                            <option value="paymob"
                                                {{ old('payment_method', $order->payment_method) == 'paymob' ? 'selected' : '' }}>
                                                Paymob</option>
                                            <option value="tamara"
                                                {{ old('payment_method', $order->payment_method) == 'tamara' ? 'selected' : '' }}>
                                                تمارا</option>
                                            <option value="tabby"
                                                {{ old('payment_method', $order->payment_method) == 'tabby' ? 'selected' : '' }}>
                                                تابي</option>
                                            <option value="cash_on_delivery"
                                                {{ old('payment_method', $order->payment_method) == 'cash_on_delivery' ? 'selected' : '' }}>
                                                الدفع عند الاستلام</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="payment_gateway" class="form-label">بوابة الدفع</label>
                                        <select class="form-select" id="payment_gateway" name="payment_gateway"
                                            {{ $disabled }}>
                                            <option value="">-- اختر --</option>
                                            <option value="wallet"
                                                {{ old('payment_gateway', $order->payment_gateway) == 'wallet' ? 'selected' : '' }}>
                                                محفظة</option>
                                            <option value="paymob"
                                                {{ old('payment_gateway', $order->payment_gateway) == 'paymob' ? 'selected' : '' }}>
                                                Paymob</option>
                                            <option value="tamara"
                                                {{ old('payment_gateway', $order->payment_gateway) == 'tamara' ? 'selected' : '' }}>
                                                Tamara</option>
                                            <option value="tabby"
                                                {{ old('payment_gateway', $order->payment_gateway) == 'tabby' ? 'selected' : '' }}>
                                                Tabby</option>
                                        </select>
                                    </div>

                                    <div class="soft-info" id="paymentContractInfo">
                                        <i class="fas fa-info-circle me-2"></i>
                                        في حالة طلب التعاقد سيتم ربط الطلب بالعقد المختار عن طريق حقل contract_id.
                                    </div>
                                </div>

                                {{-- ملاحظات --}}
                                <div class="summary-card">
                                    <h6><i class="fas fa-sticky-note me-2"></i>ملاحظات إضافية</h6>

                                    <div class="mb-3">
                                        <textarea class="form-control" id="notes" name="notes" rows="4" placeholder="أي ملاحظات تود إضافتها..."
                                            {{ $disabled }}>{{ old('notes', $order->notes) }}</textarea>
                                    </div>
                                </div>

                                {{-- أزرار --}}
                                <div class="mt-4 d-grid gap-2">
                                    @if (!$isInRoad)
                                        <button type="submit" class="btn btn-success btn-lg">
                                            <i class="fas fa-save me-2"></i>حفظ التعديلات
                                        </button>
                                    @endif

                                    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-lg">
                                        <i class="fas fa-times me-2"></i>رجوع
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    @if ($isInRoad)
                        <form action="{{ route('admin.orders.update', $order->id) }}" method="POST"
                            id="cancelInRoadForm" class="mt-3">
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="cancel_only" value="1">
                            <input type="hidden" name="is_contract" value="{{ $order->contract_id ? '1' : '0' }}">
                            <input type="hidden" name="order_type" value="{{ $oldOrderType }}">
                            <input type="hidden" name="user_id" value="{{ $order->user_id }}">
                            <input type="hidden" name="saved_location_id" value="{{ $order->saved_location_id }}">
                            <input type="hidden" name="contract_id" value="{{ $order->contract_id }}">
                            <input type="hidden" name="service_id" value="{{ $order->service_id }}">
                            <input type="hidden" name="water_type_id" value="{{ $order->water_type_id }}">
                            <input type="hidden" name="driver_id" value="{{ $order->driver_id }}">
                            <input type="hidden" name="order_date" value="{{ $orderDateValue }}">
                            <input type="hidden" name="payment_method" value="{{ $order->payment_method }}">
                            <input type="hidden" name="payment_gateway" value="{{ $order->payment_gateway }}">
                            <input type="hidden" name="order_status_id" value="{{ $cancelledStatusId }}">
                            <textarea name="notes" style="display:none;">{{ $order->notes }}</textarea>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-danger btn-lg"
                                    {{ !$cancelledStatusId ? 'disabled' : '' }}>
                                    <i class="fas fa-ban me-2"></i>إلغاء الطلب
                                </button>
                            </div>

                            @if (!$cancelledStatusId)
                                <div class="soft-warning d-block mt-3">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    لا توجد حالة إلغاء داخل حالات الطلبات. أضف حالة باسم cancelled أو canceled.
                                </div>
                            @endif
                        </form>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        const CONTRACTS = @json($contractsForJs);
        const OLD_USER_ID = @json((string) $oldUserId);
        const OLD_CONTRACT_ID = @json((string) $oldContractId);
        const OLD_SAVED_LOCATION_ID = @json((string) $oldSavedLocationId);
        const IS_IN_ROAD = @json($isInRoad);

        $(document).ready(function() {
            $('.select2').select2({
                theme: 'default',
                width: '100%',
                dropdownParent: $('body')
            });

            setMinDateTime();
            handleContractType();
            handleOrderType();
            handleDriverField();

            if (OLD_USER_ID) {
                loadUserLocations(OLD_USER_ID);
                loadUserContracts(OLD_USER_ID);
            }

            if (!IS_IN_ROAD) {
                $('#user_id').on('change', function() {
                    loadUserLocations($(this).val());
                    loadUserContracts($(this).val());
                });

                $('input[name="is_contract"]').on('change', function() {
                    handleContractType();
                });

                $('input[name="order_type"]').on('change', function() {
                    handleOrderType();
                });

                $('#contract_id').on('change', function() {
                    showContractDetails($(this).val());
                });

                $('#order_status_id').on('change', function() {
                    handleDriverField();
                });

                $('#editOrderForm').on('submit', function(e) {
                    const isContract = $('input[name="is_contract"]:checked').val();
                    const orderType = $('input[name="order_type"]:checked').val();
                    const selectedStatus = $('#order_status_id option:selected').data('status-name');

                    if (!$('#user_id').val()) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'warning',
                            title: 'تنبيه',
                            text: 'من فضلك اختر العميل أولاً'
                        });
                        return false;
                    }

                    if (isContract === '1' && !$('#contract_id').val()) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'warning',
                            title: 'تنبيه',
                            text: 'من فضلك اختر العقد الخاص بالعميل'
                        });
                        return false;
                    }

                    if (orderType === 'scheduled' && !$('#order_date').val()) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'warning',
                            title: 'تنبيه',
                            text: 'من فضلك حدد تاريخ ووقت الطلب المجدول'
                        });
                        return false;
                    }

                    if (selectedStatus === 'in-road' && !$('#driver_id').val()) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'warning',
                            title: 'تنبيه',
                            text: 'من فضلك اختر السائق لأن حالة الطلب في الطريق'
                        });
                        return false;
                    }

                    if (orderType === 'current') {
                        $('#order_date').val(formatDateTimeLocal(new Date()));
                    }
                });
            }

            $('#cancelInRoadForm').on('submit', function(e) {
                e.preventDefault();

                Swal.fire({
                    icon: 'warning',
                    title: 'تأكيد إلغاء الطلب',
                    text: 'هل أنت متأكد من إلغاء هذا الطلب؟',
                    showCancelButton: true,
                    confirmButtonText: 'نعم، إلغاء الطلب',
                    cancelButtonText: 'رجوع',
                    confirmButtonColor: '#dc3545'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });

        function handleDriverField() {
            const selectedStatus = $('#order_status_id option:selected').data('status-name');

            if (selectedStatus === 'in-road') {
                $('#driverWrapper').slideDown(200);
                $('#driver_id').prop('required', true).prop('disabled', IS_IN_ROAD ? true : false).trigger(
                'change.select2');
            } else {
                $('#driverWrapper').slideUp(200);

                if (!IS_IN_ROAD) {
                    $('#driver_id')
                        .val('')
                        .prop('required', false)
                        .prop('disabled', true)
                        .trigger('change.select2');
                }
            }
        }

        function handleContractType() {
            const isContract = $('input[name="is_contract"]:checked').val();

            if (isContract === '1') {
                $('#contractSection').slideDown(200);
                $('#contract_id')
                    .prop('disabled', IS_IN_ROAD ? true : false)
                    .prop('required', IS_IN_ROAD ? false : true)
                    .trigger('change.select2');

                $('#paymentContractInfo').show();

                if ($('#user_id').val()) {
                    loadUserContracts($('#user_id').val());
                }
            } else {
                $('#contractSection').slideUp(200);

                if (!IS_IN_ROAD) {
                    $('#contract_id')
                        .val('')
                        .prop('disabled', true)
                        .prop('required', false)
                        .trigger('change');
                }

                $('#noContractsWarning').hide();
                $('#contractDetailsCard').hide();
                $('#paymentContractInfo').hide();
            }
        }

        function handleOrderType() {
            const orderType = $('input[name="order_type"]:checked').val();

            if (orderType === 'scheduled') {
                $('#scheduleDateWrapper').slideDown(200);
                $('#order_date').prop('required', true);

                if (!$('#order_date').val()) {
                    $('#order_date').val(formatDateTimeLocal(new Date()));
                }

                if (!IS_IN_ROAD) {
                    selectStatusByName('scheduled');
                }
            } else {
                $('#scheduleDateWrapper').slideUp(200);

                if (!IS_IN_ROAD) {
                    $('#order_date').val(formatDateTimeLocal(new Date()));
                    $('#order_date').prop('required', true);
                    selectStatusByName('pending');
                }
            }
        }

        function loadUserLocations(userId) {
            const $locationSelect = $('#saved_location_id');

            $locationSelect.empty().append('<option value="">-- اختر عنوان --</option>').trigger('change.select2');

            if (!userId) {
                return;
            }

            $.ajax({
                url: "{{ route('admin.users.locations', '') }}/" + userId,
                type: 'GET',
                success: function(response) {
                    if (response.locations && response.locations.length > 0) {
                        response.locations.forEach(function(location) {
                            const label = location.label || location.name || location.type || 'عنوان';
                            const address = location.address_details || location.address || location
                                .area || location.city || '';
                            const selected = String(location.id) === String(OLD_SAVED_LOCATION_ID) ?
                                'selected' : '';

                            $locationSelect.append(
                                `<option value="${location.id}" ${selected}>${escapeHtml(label)} - ${escapeHtml(address)}</option>`
                            );
                        });
                    }

                    $locationSelect.trigger('change.select2');
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: 'فشل تحميل العناوين المحفوظة'
                    });
                }
            });
        }

        function loadUserContracts(userId) {
            const isContract = $('input[name="is_contract"]:checked').val();
            const $contractSelect = $('#contract_id');

            $contractSelect.empty().append('<option value="">-- اختر العقد --</option>');
            $('#contractDetailsCard').hide();
            $('#noContractsWarning').hide();

            if (isContract !== '1') {
                $contractSelect.val('').trigger('change.select2');
                return;
            }

            if (!userId) {
                $contractSelect.val('').trigger('change.select2');
                $('#noContractsWarning')
                    .html('<i class="fas fa-exclamation-triangle me-2"></i>اختر العميل أولاً لعرض العقود الخاصة به.')
                    .show();
                return;
            }

            const userContracts = CONTRACTS.filter(function(contract) {
                return String(contract.user_id) === String(userId);
            });

            if (userContracts.length === 0) {
                $('#noContractsWarning')
                    .html('<i class="fas fa-exclamation-triangle me-2"></i>لا توجد تعاقدات لهذا العميل.')
                    .show();
            }

            userContracts.forEach(function(contract) {
                const statusText = translateContractStatus(contract.status);
                const contractNumber = contract.contract_number || ('عقد #' + contract.id);
                const applicant = contract.applicant_name ? ' - ' + contract.applicant_name : '';
                const remaining = contract.remaining_orders !== null && contract.remaining_orders !== undefined ?
                    ' - المتبقي: ' + contract.remaining_orders :
                    '';

                const selected = String(contract.id) === String(OLD_CONTRACT_ID) ? 'selected' : '';

                $contractSelect.append(
                    `<option value="${contract.id}" ${selected}>
                        ${escapeHtml(contractNumber + applicant + ' - ' + statusText + remaining)}
                    </option>`
                );
            });

            $contractSelect.trigger('change.select2');

            if (OLD_CONTRACT_ID) {
                showContractDetails(OLD_CONTRACT_ID);
            }
        }

        function showContractDetails(contractId) {
            if (!contractId) {
                $('#contractDetailsCard').hide();
                return;
            }

            const contract = CONTRACTS.find(function(item) {
                return String(item.id) === String(contractId);
            });

            if (!contract) {
                $('#contractDetailsCard').hide();
                return;
            }

            $('#contractNumberText').text(contract.contract_number || '-');
            $('#contractTypeText').text(translateContractType(contract.contract_type));
            $('#contractApplicantText').text(contract.applicant_name || '-');
            $('#contractCompanyText').text(contract.company_name || '-');

            const startDate = contract.start_date || '-';
            const endDate = contract.end_date || '-';
            $('#contractDatesText').text(startDate + ' إلى ' + endDate);

            const remaining = contract.remaining_orders !== null && contract.remaining_orders !== undefined ?
                contract.remaining_orders :
                '-';

            const total = contract.total_orders_limit !== null && contract.total_orders_limit !== undefined ?
                contract.total_orders_limit :
                '-';

            $('#contractRemainingText').text(remaining + ' / ' + total);
            $('#contractStatusText').text(translateContractStatus(contract.status));

            $('#contractDetailsCard').slideDown(200);
        }

        function selectStatusByName(statusName) {
            const $statusSelect = $('#order_status_id');
            let foundValue = '';

            $statusSelect.find('option').each(function() {
                if ($(this).data('status-name') === statusName) {
                    foundValue = $(this).val();
                    return false;
                }
            });

            if (foundValue && !$statusSelect.val()) {
                $statusSelect.val(foundValue).trigger('change.select2');
            }
        }

        function setMinDateTime() {
            const now = new Date();

            if (!IS_IN_ROAD) {
                $('#order_date').attr('min', formatDateTimeLocal(now));
            }
        }

        function formatDateTimeLocal(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');

            return `${year}-${month}-${day}T${hours}:${minutes}`;
        }

        function translateContractStatus(status) {
            const statuses = {
                active: 'نشط',
                expired: 'منتهي',
                pending: 'قيد الانتظار',
                cancelled: 'ملغي',
                canceled: 'ملغي'
            };

            return statuses[status] || status || '-';
        }

        function translateContractType(type) {
            const types = {
                individual: 'فردي',
                company: 'شركة'
            };

            return types[type] || type || '-';
        }

        function escapeHtml(text) {
            if (text === null || text === undefined) {
                return '';
            }

            return String(text)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
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

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: "{{ session('error') }}",
                timer: 2000,
                showConfirmButton: false
            });
        @endif
    </script>
@endsection
