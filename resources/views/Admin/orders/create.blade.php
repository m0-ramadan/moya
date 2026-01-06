@extends('Admin.layout.master')

@section('title', 'إنشاء طلب جديد')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: "Cairo", sans-serif !important;
        }

        .order-form-card {
            /* background: white; */
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        .order-form-header {
            border-bottom: 2px solid #f8f9fa;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .form-section {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .form-section h6 {
            color: #696cff;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #dee2e6;
        }

        .required::after {
            content: " *";
            color: #dc3545;
        }

        .alert-guide {
            background: #e7f7ff;
            border-right: 4px solid #696cff;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
        }

        .alert-guide h6 {
            color: #696cff;
            margin-bottom: 15px;
        }

        .alert-guide ul {
            margin-bottom: 0;
            padding-left: 20px;
        }

        .alert-guide li {
            margin-bottom: 8px;
            font-size: 14px;
        }

        .product-search {
            position: relative;
        }

        .search-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            max-height: 300px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .search-result-item {
            padding: 12px 15px;
            cursor: pointer;
            border-bottom: 1px solid #f8f9fa;
            transition: background 0.3s;
        }

        .search-result-item:hover {
            background: #f8f9fa;
        }

        .search-result-item:last-child {
            border-bottom: none;
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
        }

        .product-details h6 {
            margin-bottom: 5px;
            font-weight: 600;
            color: #2c3e50;
            font-size: 14px;
        }

        .product-details p {
            margin: 0;
            color: #6c757d;
            font-size: 12px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .items-table th {
            background: #f8f9fa;
            padding: 12px;
            text-align: right;
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
        }

        .items-table td {
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
            vertical-align: middle;
        }

        .item-row:hover {
            background: #f8f9fa;
        }

        .quantity-input {
            width: 80px;
            text-align: center;
        }

        .price-input {
            width: 120px;
            text-align: left;
        }

        .remove-item {
            color: #dc3545;
            cursor: pointer;
            transition: color 0.3s;
        }

        .remove-item:hover {
            color: #bd2130;
        }

        .summary-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            border: 2px solid #dee2e6;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #dee2e6;
        }

        .summary-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .summary-label {
            font-weight: 600;
            color: #495057;
        }

        .summary-value {
            font-weight: 600;
            color: #2c3e50;
        }

        .total-row {
            font-size: 18px;
            color: #198754;
        }

        .empty-items {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }

        .empty-items i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #dee2e6;
        }

        .help-text {
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
        }

        @media (max-width: 768px) {
            .order-form-card {
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
                <li class="breadcrumb-item active">إنشاء جديد</li>
            </ol>
        </nav>

        <div class="row" bis_skin_checked="1">
            <div class="col-12" bis_skin_checked="1">
                <div class="order-form-card" bis_skin_checked="1">
                    <div class="order-form-header" bis_skin_checked="1">
                        <div class="d-flex justify-content-between align-items-center" bis_skin_checked="1">
                            <div bis_skin_checked="1">
                                <h5 class="mb-1">إنشاء طلب جديد</h5>
                                <p class="text-muted mb-0">إضافة طلب جديد يدوياً</p>
                            </div>
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                            </a>
                        </div>
                    </div>

                    <div class="alert-guide" bis_skin_checked="1">
                        <h6><i class="fas fa-lightbulb me-2"></i>معلومات مهمة:</h6>
                        <ul>
                            <li>يمكنك إنشاء طلب يدوياً لأي عميل</li>
                            <li>تأكد من صحة المعلومات قبل الحفظ</li>
                            <li>الكميات المتاحة للمنتجات معروضة أثناء البحث</li>
                            <li>سيتم تحديث المخزون تلقائياً بعد إنشاء الطلب</li>
                        </ul>
                    </div>

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

                    <form action="{{ route('admin.orders.store') }}" method="POST" id="createOrderForm">
                        @csrf

                        <div class="row" bis_skin_checked="1">
                            <div class="col-lg-8" bis_skin_checked="1">
                                <!-- معلومات العميل -->
                                <div class="form-section" bis_skin_checked="1">
                                    <h6><i class="fas fa-user me-2"></i>معلومات العميل</h6>

                                    <div class="row" bis_skin_checked="1">
                                        <div class="col-md-6 mb-3" bis_skin_checked="1">
                                            <label for="user_id" class="form-label">اختر عميل (اختياري)</label>
                                            <select class="form-select" id="user_id" name="user_id">
                                                <option value="">إنشاء طلب بدون حساب</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}"
                                                        {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                                        {{ $user->name }} - {{ $user->email }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="help-text" bis_skin_checked="1">إذا اخترت عميلاً، سيتم ملء المعلومات
                                                تلقائياً</div>
                                        </div>

                                        <div class="col-md-6 mb-3" bis_skin_checked="1">
                                            <label for="customer_name" class="form-label required">اسم العميل</label>
                                            <input type="text" class="form-control" id="customer_name"
                                                name="customer_name" value="{{ old('customer_name') }}" required>
                                        </div>

                                        <div class="col-md-6 mb-3" bis_skin_checked="1">
                                            <label for="customer_email" class="form-label required">البريد
                                                الإلكتروني</label>
                                            <input type="email" class="form-control" id="customer_email"
                                                name="customer_email" value="{{ old('customer_email') }}" required>
                                        </div>

                                        <div class="col-md-6 mb-3" bis_skin_checked="1">
                                            <label for="customer_phone" class="form-label required">رقم الهاتف</label>
                                            <input type="tel" class="form-control" id="customer_phone"
                                                name="customer_phone" value="{{ old('customer_phone') }}" required>
                                        </div>

                                        <div class="col-12 mb-3" bis_skin_checked="1">
                                            <label for="shipping_address" class="form-label required">عنوان الشحن</label>
                                            <textarea class="form-control" id="shipping_address" name="shipping_address" rows="3" required>{{ old('shipping_address') }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- إضافة المنتجات -->
                                <div class="form-section" bis_skin_checked="1">
                                    <h6><i class="fas fa-shopping-cart me-2"></i>المنتجات</h6>

                                    <div class="mb-4" bis_skin_checked="1">
                                        <label class="form-label">بحث عن منتج</label>
                                        <div class="product-search" bis_skin_checked="1">
                                            <input type="text" class="form-control" id="productSearch"
                                                placeholder="ابحث عن منتج بالاسم أو الرقم...">
                                            <div class="search-results" id="searchResults"></div>
                                        </div>
                                    </div>

                                    <!-- جدول المنتجات -->
                                    <div class="table-responsive" bis_skin_checked="1">
                                        <table class="items-table" id="itemsTable">
                                            <thead>
                                                <tr>
                                                    <th width="300">المنتج</th>
                                                    <th width="100">الكمية</th>
                                                    <th width="120">السعر للوحدة</th>
                                                    <th width="120">المجموع</th>
                                                    <th width="50"></th>
                                                </tr>
                                            </thead>
                                            <tbody id="itemsTableBody">
                                                <!-- سيتم إضافة العناصر هنا ديناميكياً -->
                                            </tbody>
                                        </table>

                                        <div class="empty-items" id="emptyItemsMessage">
                                            <i class="fas fa-shopping-cart"></i>
                                            <p>لا توجد منتجات في الطلب</p>
                                            <p class="text-muted">ابحث عن منتج وأضفه إلى الطلب</p>
                                        </div>
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
                                                <option value="">اختر طريقة الدفع</option>
                                                <option value="cash"
                                                    {{ old('payment_method') == 'cash' ? 'selected' : '' }}>نقداً</option>
                                                <option value="credit_card"
                                                    {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>بطاقة
                                                    ائتمان</option>
                                                <option value="bank_transfer"
                                                    {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>تحويل
                                                    بنكي</option>
                                                <option value="wallet"
                                                    {{ old('payment_method') == 'wallet' ? 'selected' : '' }}>محفظة
                                                    إلكترونية</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6 mb-3" bis_skin_checked="1">
                                            <label for="status" class="form-label required">حالة الطلب</label>
                                            <select class="form-select" id="status" name="status" required>
                                                <option value="pending"
                                                    {{ old('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار
                                                </option>
                                                <option value="processing"
                                                    {{ old('status') == 'processing' ? 'selected' : '' }}>تحت المعالجة
                                                </option>
                                                <option value="shipped"
                                                    {{ old('status') == 'shipped' ? 'selected' : '' }}>تم الشحن</option>
                                                <option value="delivered"
                                                    {{ old('status') == 'delivered' ? 'selected' : '' }}>تم التسليم
                                                </option>
                                                <option value="cancelled"
                                                    {{ old('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                                            </select>
                                        </div>

                                        <div class="col-12 mb-3" bis_skin_checked="1">
                                            <label for="notes" class="form-label">ملاحظات</label>
                                            <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
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
                                        <span class="summary-value" id="subtotalDisplay">0.00 ج.م</span>
                                        <input type="hidden" id="subtotal" name="subtotal" value="0">
                                    </div>

                                    <div class="summary-row" bis_skin_checked="1">
                                        <span class="summary-label">الشحن:</span>
                                        <div class="input-group input-group-sm" style="width: 150px;"
                                            bis_skin_checked="1">
                                            <input type="number" class="form-control" id="shipping_amount"
                                                name="shipping_amount" value="{{ old('shipping_amount', 0) }}"
                                                step="0.01" min="0">
                                            <span class="input-group-text">ج.م</span>
                                        </div>
                                    </div>

                                    <div class="summary-row" bis_skin_checked="1">
                                        <span class="summary-label">الخصم:</span>
                                        <div class="input-group input-group-sm" style="width: 150px;"
                                            bis_skin_checked="1">
                                            <input type="number" class="form-control" id="discount_amount"
                                                name="discount_amount" value="{{ old('discount_amount', 0) }}"
                                                step="0.01" min="0">
                                            <span class="input-group-text">ج.م</span>
                                        </div>
                                    </div>

                                    <div class="summary-row" bis_skin_checked="1">
                                        <span class="summary-label">الضريبة:</span>
                                        <div class="input-group input-group-sm" style="width: 150px;"
                                            bis_skin_checked="1">
                                            <input type="number" class="form-control" id="tax_amount" name="tax_amount"
                                                value="{{ old('tax_amount', 0) }}" step="0.01" min="0">
                                            <span class="input-group-text">ج.م</span>
                                        </div>
                                    </div>

                                    <div class="summary-row total-row" bis_skin_checked="1">
                                        <span class="summary-label">الإجمالي:</span>
                                        <span class="summary-value" id="totalDisplay">0.00 ج.م</span>
                                        <input type="hidden" id="total_amount" name="total_amount" value="0">
                                    </div>
                                </div>

                                <!-- الأزرار -->
                                <div class="mt-4" bis_skin_checked="1">
                                    <div class="d-grid gap-2" bis_skin_checked="1">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fas fa-save me-2"></i>إنشاء الطلب
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                            <i class="fas fa-redo me-2"></i>إعادة تعيين
                                        </button>
                                    </div>
                                </div>

                                <!-- معلومات سريعة -->
                                <div class="mt-4" bis_skin_checked="1">
                                    <div class="alert alert-info" bis_skin_checked="1">
                                        <h6><i class="fas fa-info-circle me-2"></i>معلومات سريعة</h6>
                                        <ul class="mt-2 mb-0 ps-3">
                                            <li>الحد الأدنى للطلب: 1 منتج</li>
                                            <li>سيتم خصم الكميات من المخزون</li>
                                            <li>يمكنك تعديل الطلب لاحقاً</li>
                                            <li>سيتم إرسال إشعار للعميل</li>
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

    <!-- Template لعنصر المنتج -->
    <template id="productItemTemplate">
        <tr class="item-row" data-product-id="{product_id}">
            <td>
                <div class="product-info">
                    <img src="{image}" alt="{name}" class="product-image">
                    <div class="product-details">
                        <h6>{name}</h6>
                        <p>المخزون: {stock}</p>
                        <input type="hidden" name="items[{index}][product_id]" value="{product_id}">
                    </div>
                </div>
            </td>
            <td>
                <input type="number" class="form-control quantity-input" name="items[{index}][quantity]" value="1"
                    min="1" max="{stock}" onchange="updateItem(this)">
            </td>
            <td>
                <div class="input-group" bis_skin_checked="1">
                    <input type="number" class="form-control price-input" name="items[{index}][price_per_unit]"
                        value="{price}" step="0.01" min="0" onchange="updateItem(this)">
                    <span class="input-group-text">ج.م</span>
                </div>
            </td>
            <td>
                <span class="item-total">{total} ج.م</span>
            </td>
            <td>
                <i class="fas fa-times remove-item" onclick="removeItem(this)"></i>
            </td>
        </tr>
    </template>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let itemIndex = 0;
        let products = @json(
            $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'final_price' => $product->final_price,
                    'stock' => $product->stock,
                    'image' => $product->image ? asset('storage/' . $product->image) : asset('images/default-product.png'),
                    'category' => $product->category->name ?? 'غير مصنف',
                ];
            }));


        $(document).ready(function() {
            // البحث عن المنتجات
            $('#productSearch').on('keyup', function() {
                const searchTerm = $(this).val().toLowerCase();
                const resultsContainer = $('#searchResults');

                if (searchTerm.length < 2) {
                    resultsContainer.hide();
                    return;
                }

                const filteredProducts = products.filter(product =>
                    product.name.toLowerCase().includes(searchTerm) ||
                    product.id.toString().includes(searchTerm)
                );

                if (filteredProducts.length > 0) {
                    let html = '';
                    filteredProducts.forEach(product => {
                        html += `
                        <div class="search-result-item" onclick="addProduct(${product.id})">
                            <div class="product-info">
                                <img src="${product.image}" alt="${product.name}" class="product-image">
                                <div class="product-details">
                                    <h6>${product.name}</h6>
                                    <p>السعر: ${product.final_price} ج.م | المخزون: ${product.stock}</p>
                                </div>
                            </div>
                        </div>
                    `;
                    });

                    resultsContainer.html(html).show();
                } else {
                    resultsContainer.html('<div class="search-result-item">لا توجد نتائج</div>').show();
                }
            });

            // إغلاق نتائج البحث عند النقر خارجها
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.product-search').length) {
                    $('#searchResults').hide();
                }
            });

            // تحديث الإجماليات عند تغيير الشحن أو الخصم أو الضريبة
            $('#shipping_amount, #discount_amount, #tax_amount').on('input', updateSummary);

            // ملء معلومات العميل عند اختياره
            $('#user_id').on('change', function() {
                const userId = $(this).val();
                if (userId) {
                    // يمكنك إضافة AJAX لجلب معلومات العميل هنا
                    // $.get(`/admin/users/${userId}/data`, function(user) {
                    //     $('#customer_name').val(user.name);
                    //     $('#customer_email').val(user.email);
                    //     $('#customer_phone').val(user.phone);
                    // });
                }
            });

            // التحقق من النموذج قبل الإرسال
            $('#createOrderForm').on('submit', function(e) {
                if (itemIndex === 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'لا توجد منتجات',
                        text: 'يجب إضافة منتج واحد على الأقل إلى الطلب',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    return;
                }
            });
        });

        function addProduct(productId) {
            const product = products.find(p => p.id == productId);
            if (!product) return;

            // التحقق من عدم إضافة المنتج مسبقاً
            if ($(`tr[data-product-id="${productId}"]`).length > 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'المنتج مضاف مسبقاً',
                    text: 'هذا المنتج مضاف بالفعل إلى الطلب',
                    timer: 1500,
                    showConfirmButton: false
                });
                return;
            }

            // إضافة المنتج إلى الجدول
            const template = document.getElementById('productItemTemplate').innerHTML;
            const html = template
                .replace(/{product_id}/g, product.id)
                .replace(/{name}/g, product.name)
                .replace(/{price}/g, product.final_price)
                .replace(/{stock}/g, product.stock)
                .replace(/{image}/g, product.image)
                .replace(/{index}/g, itemIndex)
                .replace(/{total}/g, product.final_price);

            $('#itemsTableBody').append(html);
            $('#emptyItemsMessage').hide();
            $('#searchResults').hide();
            $('#productSearch').val('');

            itemIndex++;
            updateSummary();
        }

        function updateItem(input) {
            const row = $(input).closest('tr');
            const quantity = row.find('.quantity-input').val();
            const price = row.find('.price-input').val();
            const total = (quantity * price).toFixed(2);

            row.find('.item-total').text(total + ' ج.م');
            updateSummary();
        }

        function removeItem(icon) {
            const row = $(icon).closest('tr');
            row.remove();

            if ($('#itemsTableBody tr').length === 0) {
                $('#emptyItemsMessage').show();
            }

            updateSummary();
        }

        function updateSummary() {
            let subtotal = 0;

            // حساب مجموع العناصر
            $('.item-row').each(function() {
                const quantity = $(this).find('.quantity-input').val();
                const price = $(this).find('.price-input').val();
                subtotal += quantity * price;
            });

            // الحصول على القيم الإضافية
            const shipping = parseFloat($('#shipping_amount').val()) || 0;
            const discount = parseFloat($('#discount_amount').val()) || 0;
            const tax = parseFloat($('#tax_amount').val()) || 0;

            // حساب الإجمالي
            const total = subtotal + shipping - discount + tax;

            // تحديث العرض
            $('#subtotalDisplay').text(subtotal.toFixed(2) + ' ج.م');
            $('#subtotal').val(subtotal.toFixed(2));
            $('#totalDisplay').text(total.toFixed(2) + ' ج.م');
            $('#total_amount').val(total.toFixed(2));
        }

        function resetForm() {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: 'سيتم مسح جميع البيانات المدخلة',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'نعم، امسح',
                cancelButtonText: 'إلغاء',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // إعادة تعيين النموذج
                    document.getElementById('createOrderForm').reset();

                    // مسح العناصر
                    $('#itemsTableBody').empty();
                    $('#emptyItemsMessage').show();
                    itemIndex = 0;

                    // إعادة تعيين الإجماليات
                    $('#subtotalDisplay').text('0.00 ج.م');
                    $('#subtotal').val('0');
                    $('#totalDisplay').text('0.00 ج.م');
                    $('#total_amount').val('0');

                    Swal.fire({
                        icon: 'success',
                        title: 'تم الإعادة',
                        text: 'تم إعادة تعيين النموذج بنجاح',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        }
    </script>
@endsection
