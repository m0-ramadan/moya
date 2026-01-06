<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>فاتورة الطلب #{{ $order->order_number }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: "Cairo", sans-serif !important;
            margin: 0;
            padding: 20px;
            color: #333;
            background: white;
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
        }

        .invoice-header {
            border-bottom: 3px solid #696cff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .company-info {
            text-align: center;
            margin-bottom: 20px;
        }

        .company-name {
            font-size: 28px;
            font-weight: 700;
            color: #696cff;
            margin-bottom: 10px;
        }

        .company-details {
            color: #666;
            font-size: 14px;
            line-height: 1.6;
        }

        .invoice-title {
            text-align: center;
            margin-bottom: 30px;
        }

        .invoice-title h1 {
            font-size: 24px;
            color: #333;
            margin-bottom: 10px;
        }

        .invoice-title .order-number {
            font-size: 18px;
            color: #696cff;
            font-weight: 600;
        }

        .info-section {
            margin-bottom: 30px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .info-box {
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #f9f9f9;
        }

        .info-box h4 {
            color: #696cff;
            margin-bottom: 10px;
            font-size: 16px;
            border-bottom: 2px solid #eee;
            padding-bottom: 5px;
        }

        .info-row {
            display: flex;
            margin-bottom: 8px;
        }

        .info-label {
            font-weight: 600;
            color: #555;
            min-width: 120px;
        }

        .info-value {
            color: #333;
            flex-grow: 1;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .items-table th {
            background: #696cff;
            color: white;
            padding: 12px;
            text-align: right;
            font-weight: 600;
            border: none;
        }

        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            vertical-align: top;
        }

        .items-table tr:last-child td {
            border-bottom: 2px solid #696cff;
        }

        .items-table tr:hover {
            background: #f9f9f9;
        }

        .product-name {
            font-weight: 600;
            color: #333;
        }

        .product-details {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }

        .summary-section {
            margin-bottom: 30px;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .summary-table tr:last-child td {
            border-bottom: none;
            font-weight: 700;
            font-size: 16px;
        }

        .summary-label {
            text-align: left;
            color: #555;
        }

        .summary-value {
            text-align: left;
            color: #333;
        }

        .total-row {
            background: #f0f7ff;
            color: #696cff;
        }

        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px dashed #ddd;
            text-align: center;
            color: #666;
            font-size: 12px;
        }

        .footer-logo {
            font-size: 24px;
            color: #696cff;
            margin-bottom: 10px;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
            margin-bottom: 10px;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-processing {
            background: #cce5ff;
            color: #004085;
        }

        .status-shipped {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-delivered {
            background: #d4edda;
            color: #155724;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        .notes-box {
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #f9f9f9;
            margin-bottom: 20px;
        }

        .notes-box h4 {
            color: #696cff;
            margin-bottom: 10px;
            font-size: 16px;
        }

        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none !important;
            }

            .invoice-container {
                max-width: 100%;
            }

            .items-table,
            .summary-table {
                page-break-inside: avoid;
            }
        }

        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr;
            }

            .items-table {
                font-size: 12px;
            }

            .items-table th,
            .items-table td {
                padding: 8px;
            }
        }
    </style>
</head>

<body>
    <div class="invoice-container">
        <!-- أزرار الطباعة (تظهر فقط على الشاشة) -->
        <div class="no-print" style="text-align: center; margin-bottom: 20px;">
            <button onclick="window.print()" class="btn-print">
                <i class="fas fa-print"></i> طباعة الفاتورة
            </button>
            <button onclick="window.close()" class="btn-close">
                <i class="fas fa-times"></i> إغلاق النافذة
            </button>

            <style>
                .btn-print,
                .btn-close {
                    padding: 10px 20px;
                    margin: 0 10px;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                    font-size: 14px;
                    font-weight: 600;
                    transition: all 0.3s ease;
                }

                .btn-print {
                    background: #696cff;
                    color: white;
                }

                .btn-close {
                    background: #dc3545;
                    color: white;
                }

                .btn-print:hover {
                    background: #5a5fcc;
                }

                .btn-close:hover {
                    background: #c82333;
                }
            </style>
        </div>

        <!-- رأس الفاتورة -->
        <div class="invoice-header">
            <div class="company-info">
                <div class="company-name">متجرك الإلكتروني</div>
                <div class="company-details">
                    <p>العنوان: شارع التجارة، الرياض، المملكة العربية السعودية</p>
                    <p>الهاتف: 0112345678 | البريد: info@yourstore.com</p>
                    <p>الموقع: www.yourstore.com</p>
                </div>
            </div>

            <div class="invoice-title">
                <h1>فاتورة بيع</h1>
                <div class="order-number">رقم الطلب: {{ $order->order_number }}</div>
                <div class="status-badge status-{{ $order->status }}">
                    {{ $order->status_label }}
                </div>
            </div>
        </div>

        <!-- معلومات العميل والتاجر -->
        <div class="info-section">
            <div class="info-grid">
                <!-- معلومات العميل -->
                <div class="info-box">
                    <h4><i class="fas fa-user me-2"></i>معلومات العميل</h4>
                    <div class="info-row">
                        <span class="info-label">الاسم:</span>
                        <span class="info-value">{{ $order->customer_name }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">البريد:</span>
                        <span class="info-value">{{ $order->customer_email }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">الهاتف:</span>
                        <span class="info-value">{{ $order->customer_phone }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">العنوان:</span>
                        <span class="info-value">{{ $order->shipping_address }}</span>
                    </div>
                </div>

                <!-- معلومات التاجر -->
                <div class="info-box">
                    <h4><i class="fas fa-store me-2"></i>معلومات التاجر</h4>
                    <div class="info-row">
                        <span class="info-label">الاسم:</span>
                        <span class="info-value">متجرك الإلكتروني</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">البريد:</span>
                        <span class="info-value">info@yourstore.com</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">الهاتف:</span>
                        <span class="info-value">0112345678</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">الموقع:</span>
                        <span class="info-value">www.yourstore.com</span>
                    </div>
                </div>
            </div>

            <!-- معلومات الطلب -->
            <div class="info-grid">
                <div class="info-box">
                    <h4><i class="fas fa-info-circle me-2"></i>معلومات الطلب</h4>
                    <div class="info-row">
                        <span class="info-label">رقم الطلب:</span>
                        <span class="info-value">{{ $order->order_number }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">تاريخ الطلب:</span>
                        <span class="info-value">{{ $order->created_at->translatedFormat('d M Y - h:i A') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">طريقة الدفع:</span>
                        <span class="info-value">{{ $order->payment_method }}</span>
                    </div>
                    @if ($order->transaction_id)
                        <div class="info-row">
                            <span class="info-label">رقم المعاملة:</span>
                            <span class="info-value">{{ $order->transaction_id }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- قائمة المنتجات -->
        <div class="items-section">
            <h3 style="color: #696cff; margin-bottom: 15px; border-bottom: 2px solid #eee; padding-bottom: 10px;">
                <i class="fas fa-shopping-cart me-2"></i>المنتجات المطلوبة
            </h3>

            <table class="items-table">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="40%">المنتج</th>
                        <th width="15%">السعر</th>
                        <th width="10%">الكمية</th>
                        <th width="15%">المجموع</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="product-name">{{ $item->product->name ?? 'منتج محذوف' }}</div>
                                @if ($item->color || $item->size)
                                    <div class="product-details">
                                        @if ($item->color)
                                            لون: {{ $item->color->name }}
                                        @endif
                                        @if ($item->size)
                                            | مقاس: {{ $item->size->name }}
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td>{{ number_format($item->price_per_unit, 2) }} ج.م</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->total_price, 2) }} ج.م</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- الملاحظات -->
        @if ($order->notes)
            <div class="notes-box">
                <h4><i class="fas fa-sticky-note me-2"></i>ملاحظات الطلب</h4>
                <p>{{ $order->notes }}</p>
            </div>
        @endif

        <!-- ملخص المبالغ -->
        <div class="summary-section">
            <table class="summary-table">
                <tr>
                    <td class="summary-label">المجموع الجزئي:</td>
                    <td class="summary-value">{{ number_format($order->subtotal, 2) }} ج.م</td>
                </tr>
                @if ($order->shipping_amount > 0)
                    <tr>
                        <td class="summary-label">تكلفة الشحن:</td>
                        <td class="summary-value">{{ number_format($order->shipping_amount, 2) }} ج.م</td>
                    </tr>
                @endif
                @if ($order->discount_amount > 0)
                    <tr>
                        <td class="summary-label">الخصم:</td>
                        <td class="summary-value">-{{ number_format($order->discount_amount, 2) }} ج.م</td>
                    </tr>
                @endif
                @if ($order->tax_amount > 0)
                    <tr>
                        <td class="summary-label">الضريبة:</td>
                        <td class="summary-value">{{ number_format($order->tax_amount, 2) }} ج.م</td>
                    </tr>
                @endif
                <tr class="total-row">
                    <td class="summary-label">المجموع الإجمالي:</td>
                    <td class="summary-value">{{ number_format($order->total_amount, 2) }} ج.م</td>
                </tr>
            </table>
        </div>

        <!-- تذييل الفاتورة -->
        <div class="footer">
            <div class="footer-logo">
                <i class="fas fa-store"></i>
            </div>
            <p>شكراً لتسوقك معنا</p>
            <p>للاستفسارات: info@yourstore.com | 0112345678</p>
            <p>تم إنشاء هذه الفاتورة تلقائياً بتاريخ: {{ now()->translatedFormat('d M Y - h:i A') }}</p>
            <p>رقم الفاتورة: INV-{{ $order->id }}-{{ date('Ymd') }}</p>
        </div>
    </div>

    <script>
        // الطباعة التلقائية
        window.onload = function() {
            // يمكنك تفعيل الطباعة التلقائية بإزالة التعليق من السطر التالي
            // window.print();
        };

        // تنسيق أزرار الطباعة
        const style = document.createElement('style');
        style.textContent = `
            @media print {
                .no-print { display: none !important; }
            }
            @media screen {
                .btn-print, .btn-close {
                    padding: 12px 24px;
                    margin: 0 10px;
                    border: none;
                    border-radius: 6px;
                    cursor: pointer;
                    font-size: 14px;
                    font-weight: 600;
                    transition: all 0.3s ease;
                    font-family: "Cairo", sans-serif;
                }
                
                .btn-print {
                    background: #696cff;
                    color: white;
                }
                
                .btn-close {
                    background: #dc3545;
                    color: white;
                }
                
                .btn-print:hover {
                    background: #5a5fcc;
                    transform: translateY(-2px);
                    box-shadow: 0 5px 15px rgba(105, 108, 255, 0.3);
                }
                
                .btn-close:hover {
                    background: #c82333;
                    transform: translateY(-2px);
                    box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>

</html>
