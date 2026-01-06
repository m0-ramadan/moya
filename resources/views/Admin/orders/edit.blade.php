@extends('Admin.layout.master')

@section('title', 'تعديل الطلب: ' . $order->order_number)

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
            --light-bg: #f8f9fa;
            --border-color: #e9ecef;
            --text-muted: #6c757d;
            --dark-bg: #1e1e2d;
            --dark-card: #2b3b4c;
        }

        body {
            font-family: "Cairo", sans-serif !important;
            background: var(--dark-bg);
            color: #fff;
        }

        .order-edit-card {
            background: var(--dark-card);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .order-edit-header {
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .badge-status {
            padding: 6px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 13px;
        }

        .status-pending {
            background: rgba(133, 100, 4, 0.2);
            color: #ffc107;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }

        .status-processing {
            background: rgba(0, 64, 133, 0.2);
            color: #0dcaf0;
            border: 1px solid rgba(13, 202, 240, 0.3);
        }

        .status-shipped {
            background: rgba(12, 84, 96, 0.2);
            color: #0dcaf0;
            border: 1px solid rgba(13, 202, 240, 0.3);
        }

        .status-delivered {
            background: linear-gradient(135deg, rgba(21, 87, 36, 0.2) 0%, rgba(32, 201, 151, 0.2) 100%);
            color: #20c997;
            border: 1px solid rgba(32, 201, 151, 0.3);
        }

        .status-cancelled {
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.2) 0%, rgba(253, 126, 20, 0.2) 100%);
            color: #fd7e14;
            border: 1px solid rgba(253, 126, 20, 0.3);
        }

        .form-section {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .form-section h6 {
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

        .alert-guide h6 {
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .alert-guide ul {
            margin-bottom: 0;
            padding-left: 20px;
        }

        .alert-guide li {
            margin-bottom: 8px;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.8);
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .items-table th {
            background: rgba(255, 255, 255, 0.05);
            padding: 12px;
            text-align: right;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.8);
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
        }

        .items-table td {
            padding: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            vertical-align: middle;
            color: rgba(255, 255, 255, 0.9);
        }

        .item-row:hover {
            background: rgba(105, 108, 255, 0.1);
        }

        .product-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .product-image {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            object-fit: cover;
            border: 2px solid rgba(255, 255, 255, 0.1);
        }

        .product-details h6 {
            margin-bottom: 5px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
        }

        .product-details p {
            margin: 0;
            color: rgba(255, 255, 255, 0.7);
            font-size: 12px;
        }

        .summary-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 20px;
            border: 2px solid rgba(255, 255, 255, 0.1);
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dashed rgba(255, 255, 255, 0.1);
        }

        .summary-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .summary-label {
            font-weight: 600;
            color: rgba(255, 255, 255, 0.8);
        }

        .summary-value {
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
        }

        .total-row {
            font-size: 18px;
            color: #20c997;
        }

        .help-text {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.6);
            margin-top: 5px;
        }

        .info-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            border-right: 4px solid var(--primary-color);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .info-row {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .info-row:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .info-label {
            min-width: 120px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.8);
        }

        .info-value {
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--primary-color);
            color: #fff;
            box-shadow: 0 0 0 0.25rem rgba(105, 108, 255, 0.25);
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .form-label {
            color: rgba(255, 255, 255, 0.9);
        }

        .input-group-text {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.1);
            color: #fff;
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

        @media (max-width: 768px) {
            .order-edit-card {
                padding: 20px;
            }

            .items-table {
                display: block;
                overflow-x: auto;
            }

            .product-info {
                flex-direction: column;
                text-align: center;
                gap: 10px;
            }

            .info-row {
                flex-direction: column;
                align-items: flex-start;
            }

            .info-label {
                min-width: auto;
                margin-bottom: 5px;
            }
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
                    <a href="{{ route('admin.orders.index') }}">الطلبات</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.orders.show', $order) }}">{{ $order->order_number }}</a>
                </li>
                <li class="breadcrumb-item active">تعديل</li>
            </ol>
        </nav>

        <div class="row" bis_skin_checked="1">
            <div class="col-12" bis_skin_checked="1">
                <div class="order-edit-card" bis_skin_checked="1">
                    <div class="order-edit-header" bis_skin_checked="1">
                        <div class="d-flex justify-content-between align-items-center" bis_skin_checked="1">
                            <div bis_skin_checked="1">
                                <h5 class="mb-1">تعديل الطلب</h5>
                                <div class="d-flex align-items-center gap-3" bis_skin_checked="1">
                                    <span class="badge-status status-{{ $order->status }}">
                                        {{ $order->status_label }}
                                    </span>
                                    <small class="text-muted">ID: #{{ $order->id }}</small>
                                </div>
                            </div>
                            <div class="btn-group" bis_skin_checked="1">
                                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-outline-info">
                                    <i class="fas fa-eye me-2"></i>عرض
                                </a>
                                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-right me-2"></i>رجوع
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- معلومات الطلب -->
                    <div class="info-card" bis_skin_checked="1">
                        <h6 class="mb-3">معلومات الطلب</h6>

                        <div class="info-row" bis_skin_checked="1">
                            <div class="info-label" bis_skin_checked="1">رقم الطلب:</div>
                            <div class="info-value" bis_skin_checked="1">{{ $order->order_number }}</div>
                        </div>

                        <div class="info-row" bis_skin_checked="1">
                            <div class="info-label" bis_skin_checked="1">تاريخ الإنشاء:</div>
                            <div class="info-value" bis_skin_checked="1">
                                {{ $order->created_at->translatedFormat('d M Y - h:i A') }}
                            </div>
                        </div>

                        <div class="info-row" bis_skin_checked="1">
                            <div class="info-label" bis_skin_checked="1">آخر تحديث:</div>
                            <div class="info-value" bis_skin_checked="1">
                                {{ $order->updated_at->translatedFormat('d M Y - h:i A') }}
                            </div>
                        </div>

                        <div class="info-row" bis_skin_checked="1">
                            <div class="info-label" bis_skin_checked="1">عدد المنتجات:</div>
                            <div class="info-value" bis_skin_checked="1">{{ $order->items->count() }}</div>
                        </div>

                        <div class="info-row" bis_skin_checked="1">
                            <div class="info-label" bis_skin_checked="1">المجموع الإجمالي:</div>
                            <div class="info-value" bis_skin_checked="1">
                                {{ number_format($order->total_amount, 2) }} ج.م
                            </div>
                        </div>
                    </div>

                    <div class="alert-guide" bis_skin_checked="1">
                        <h6><i class="fas fa-lightbulb me-2"></i>نصائح للتعديل:</h6>
                        <ul>
                            <li>يمكنك تعديل معلومات العميل والعنوان</li>
                            <li>يمكنك تحديث حالة الطلب والملاحظات</li>
                            <li>تغيير المبالغ سيؤثر على الإجمالي النهائي</li>
                            <li>احفظ التغييرات بعد الانتهاء</li>
                        </ul>
                    </div>

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
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

                    <form action="{{ route('admin.orders.update', $order) }}" method="POST" id="editOrderForm">
                        @csrf
                        @method('PUT')

                        <div class="row" bis_skin_checked="1">
                            <div class="col-lg-8" bis_skin_checked="1">
                                <!-- معلومات العميل -->
                                <div class="form-section" bis_skin_checked="1">
                                    <h6><i class="fas fa-user me-2"></i>معلومات العميل</h6>

                                    <div class="row" bis_skin_checked="1">
                                        <div class="col-md-6 mb-3" bis_skin_checked="1">
                                            <label for="customer_name" class="form-label required">اسم العميل</label>
                                            <input type="text" class="form-control" id="customer_name"
                                                name="customer_name"
                                                value="{{ old('customer_name', $order->customer_name) }}" required>
                                        </div>

                                        <div class="col-md-6 mb-3" bis_skin_checked="1">
                                            <label for="customer_email" class="form-label required">البريد
                                                الإلكتروني</label>
                                            <input type="email" class="form-control" id="customer_email"
                                                name="customer_email"
                                                value="{{ old('customer_email', $order->customer_email) }}" required>
                                        </div>

                                        <div class="col-md-6 mb-3" bis_skin_checked="1">
                                            <label for="customer_phone" class="form-label required">رقم الهاتف</label>
                                            <input type="tel" class="form-control" id="customer_phone"
                                                name="customer_phone"
                                                value="{{ old('customer_phone', $order->customer_phone) }}" required>
                                        </div>

                                        <div class="col-12 mb-3" bis_skin_checked="1">
                                            <label for="shipping_address" class="form-label required">عنوان الشحن</label>
                                            <textarea class="form-control" id="shipping_address" name="shipping_address" rows="3" required>{{ old('shipping_address', $order->shipping_address) }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- المنتجات (للعرض فقط) -->
                                <div class="form-section" bis_skin_checked="1">
                                    <h6><i class="fas fa-shopping-cart me-2"></i>المنتجات</h6>

                                    <div class="table-responsive" bis_skin_checked="1">
                                        <table class="items-table">
                                            <thead>
                                                <tr>
                                                    <th width="300">المنتج</th>
                                                    <th width="100">الكمية</th>
                                                    <th width="120">السعر للوحدة</th>
                                                    <th width="120">المجموع</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($order->items as $item)
                                                    <tr class="item-row">
                                                        <td>
                                                            <div class="product-info">
                                                                @if ($item->product && $item->product->image)
                                                                    <img src="{{ asset('storage/' . $item->product->image) }}"
                                                                        alt="{{ $item->product->name }}"
                                                                        class="product-image">
                                                                @else
                                                                    <div class="product-image"
                                                                        style="background: #dee2e6; display: flex; align-items: center; justify-content: center;">
                                                                        <i class="fas fa-box text-muted"></i>
                                                                    </div>
                                                                @endif
                                                                <div class="product-details">
                                                                    <h6>{{ $item->product->name ?? 'منتج محذوف' }}</h6>
                                                                    <p>ID: {{ $item->product_id }}</p>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>{{ $item->quantity }}</td>
                                                        <td>{{ number_format($item->price_per_unit, 2) }} ج.م</td>
                                                        <td>{{ number_format($item->total_price, 2) }} ج.م</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="help-text" bis_skin_checked="1">
                                        <i class="fas fa-info-circle me-1"></i>
                                        المنتجات غير قابلة للتعديل من هنا. للحذف أو الإضافة، قم بإلغاء الطلب وإنشاؤه من
                                        جديد.
                                    </div>
                                </div>

                                <!-- معلومات إضافية -->
                                <div class="form-section" bis_skin_checked="1">
                                    <h6><i class="fas fa-info-circle me-2"></i>معلومات إضافية</h6>

                                    <div class="row" bis_skin_checked="1">
                                        <div class="col-md-6 mb-3" bis_skin_checked="1">
                                            <label for="payment_method" class="form-label required">طريقة الدفع</label>
                                            <select class="form-select" id="payment_method" name="payment_method"
                                                required>
                                                <option value="cash"
                                                    {{ old('payment_method', $order->payment_method) == 'cash' ? 'selected' : '' }}>
                                                    نقداً</option>
                                                <option value="credit_card"
                                                    {{ old('payment_method', $order->payment_method) == 'credit_card' ? 'selected' : '' }}>
                                                    بطاقة ائتمان</option>
                                                <option value="bank_transfer"
                                                    {{ old('payment_method', $order->payment_method) == 'bank_transfer' ? 'selected' : '' }}>
                                                    تحويل بنكي</option>
                                                <option value="wallet"
                                                    {{ old('payment_method', $order->payment_method) == 'wallet' ? 'selected' : '' }}>
                                                    محفظة إلكترونية</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6 mb-3" bis_skin_checked="1">
                                            <label for="status" class="form-label required">حالة الطلب</label>
                                            <select class="form-select" id="status" name="status" required>
                                                <option value="pending"
                                                    {{ old('status', $order->status) == 'pending' ? 'selected' : '' }}>قيد
                                                    الانتظار</option>
                                                <option value="processing"
                                                    {{ old('status', $order->status) == 'processing' ? 'selected' : '' }}>
                                                    تحت المعالجة</option>
                                                <option value="shipped"
                                                    {{ old('status', $order->status) == 'shipped' ? 'selected' : '' }}>تم
                                                    الشحن</option>
                                                <option value="delivered"
                                                    {{ old('status', $order->status) == 'delivered' ? 'selected' : '' }}>تم
                                                    التسليم</option>
                                                <option value="cancelled"
                                                    {{ old('status', $order->status) == 'cancelled' ? 'selected' : '' }}>
                                                    ملغي</option>
                                            </select>
                                        </div>

                                        <div class="col-12 mb-3" bis_skin_checked="1">
                                            <label for="notes" class="form-label">ملاحظات</label>
                                            <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes', $order->notes) }}</textarea>
                                            <div class="help-text" bis_skin_checked="1">ملاحظات إضافية حول الطلب (اختياري)
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4" bis_skin_checked="1">
                                <!-- ملخص الطلب -->
                                <div class="summary-card" bis_skin_checked="1">
                                    <h6 class="mb-3">ملخص الطلب</h6>

                                    <div class="summary-row" bis_skin_checked="1">
                                        <span class="summary-label">المجموع الجزئي:</span>
                                        <div class="input-group input-group-sm" style="width: 150px;"
                                            bis_skin_checked="1">
                                            <input type="number" class="form-control" id="subtotal" name="subtotal"
                                                value="{{ old('subtotal', $order->subtotal) }}" step="0.01"
                                                min="0" required>
                                            <span class="input-group-text">ج.م</span>
                                        </div>
                                    </div>

                                    <div class="summary-row" bis_skin_checked="1">
                                        <span class="summary-label">الشحن:</span>
                                        <div class="input-group input-group-sm" style="width: 150px;"
                                            bis_skin_checked="1">
                                            <input type="number" class="form-control" id="shipping_amount"
                                                name="shipping_amount"
                                                value="{{ old('shipping_amount', $order->shipping_amount) }}"
                                                step="0.01" min="0">
                                            <span class="input-group-text">ج.م</span>
                                        </div>
                                    </div>

                                    <div class="summary-row" bis_skin_checked="1">
                                        <span class="summary-label">الخصم:</span>
                                        <div class="input-group input-group-sm" style="width: 150px;"
                                            bis_skin_checked="1">
                                            <input type="number" class="form-control" id="discount_amount"
                                                name="discount_amount"
                                                value="{{ old('discount_amount', $order->discount_amount) }}"
                                                step="0.01" min="0">
                                            <span class="input-group-text">ج.م</span>
                                        </div>
                                    </div>

                                    <div class="summary-row" bis_skin_checked="1">
                                        <span class="summary-label">الضريبة:</span>
                                        <div class="input-group input-group-sm" style="width: 150px;"
                                            bis_skin_checked="1">
                                            <input type="number" class="form-control" id="tax_amount" name="tax_amount"
                                                value="{{ old('tax_amount', $order->tax_amount) }}" step="0.01"
                                                min="0">
                                            <span class="input-group-text">ج.م</span>
                                        </div>
                                    </div>

                                    <div class="summary-row total-row" bis_skin_checked="1">
                                        <span class="summary-label">الإجمالي:</span>
                                        <div class="input-group input-group-sm" style="width: 150px;"
                                            bis_skin_checked="1">
                                            <input type="number" class="form-control" id="total_amount"
                                                name="total_amount"
                                                value="{{ old('total_amount', $order->total_amount) }}" step="0.01"
                                                min="0" required>
                                            <span class="input-group-text">ج.م</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- الأزرار -->
                                <div class="mt-4" bis_skin_checked="1">
                                    <div class="d-grid gap-2" bis_skin_checked="1">
                                        <button type="submit" class="btn btn-success btn-lg">
                                            <i class="fas fa-save me-2"></i>حفظ التعديلات
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                                            <i class="fas fa-trash me-2"></i>حذف الطلب
                                        </button>
                                    </div>
                                </div>

                                <!-- معلومات سريعة -->
                                <div class="mt-4" bis_skin_checked="1">
                                    <div class="alert alert-warning" bis_skin_checked="1">
                                        <h6><i class="fas fa-exclamation-triangle me-2"></i>تنبيهات مهمة</h6>
                                        <ul class="mt-2 mb-0 ps-3">
                                            <li>تغيير حالة الطلب قد يؤثر على المخزون</li>
                                            <li>تغيير المبالغ لا يؤثر على المنتجات</li>
                                            <li>لإضافة أو حذف منتجات، قم بإلغاء الطلب</li>
                                            <li>تأكد من صحة المعلومات قبل الحفظ</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Form -->
    <form id="deleteForm" action="{{ route('admin.orders.destroy', $order) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // حساب الإجمالي تلقائياً عند تغيير المبالغ
            $('#subtotal, #shipping_amount, #discount_amount, #tax_amount').on('input', function() {
                calculateTotal();
            });

            // التحقق من النموذج قبل الإرسال
            $('#editOrderForm').on('submit', function(e) {
                const total = parseFloat($('#total_amount').val());

                if (total < 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ في المبلغ',
                        text: 'المبلغ الإجمالي لا يمكن أن يكون سالباً',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        });

        function calculateTotal() {
            const subtotal = parseFloat($('#subtotal').val()) || 0;
            const shipping = parseFloat($('#shipping_amount').val()) || 0;
            const discount = parseFloat($('#discount_amount').val()) || 0;
            const tax = parseFloat($('#tax_amount').val()) || 0;

            const total = subtotal + shipping - discount + tax;

            $('#total_amount').val(total.toFixed(2));
        }

        function confirmDelete() {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: 'سيتم حذف الطلب "{{ $order->order_number }}" نهائياً',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('deleteForm').submit();
                }
            });
        }

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
    </script>
@endsection
