<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير مدفوعات العقد</title>
    <style>
        @page {
            margin: 20px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #111827;
            font-size: 12px;
        }

        .header {
            margin-bottom: 16px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 10px;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            margin: 0 0 6px 0;
        }

        .meta {
            margin: 0;
            color: #4b5563;
            line-height: 1.7;
        }

        .summary {
            margin: 14px 0;
            padding: 10px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
        }

        .summary p {
            margin: 4px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #e5e7eb;
            padding: 8px;
            text-align: right;
            vertical-align: top;
        }

        th {
            background: #f3f4f6;
            font-weight: bold;
        }

        .empty {
            margin-top: 20px;
            text-align: center;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">تقرير مدفوعات العقد</h1>
        <p class="meta">رقم العقد: {{ $contract->contract_number ?? '—' }}</p>
        <p class="meta">اسم العميل: {{ $contract->user->name ?? ($contract->applicant_name ?? '—') }}</p>
        <p class="meta">تاريخ التصدير: {{ now()->format('Y-m-d H:i') }}</p>
    </div>

    @php
        $totalAmount = $payments->sum('amount');
        $completedAmount = $payments->where('status', 'completed')->sum('amount');
        $pendingAmount = $payments->where('status', 'pending')->sum('amount');
    @endphp

    <div class="summary">
        <p>إجمالي عدد الدفعات: {{ $payments->count() }}</p>
        <p>إجمالي المبالغ: {{ number_format($totalAmount, 2) }} ر.س</p>
        <p>مبالغ مكتملة: {{ number_format($completedAmount, 2) }} ر.س</p>
        <p>مبالغ معلقة: {{ number_format($pendingAmount, 2) }} ر.س</p>
    </div>

    @if($payments->isEmpty())
        <p class="empty">لا توجد دفعات مطابقة للفلترة الحالية.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>رقم العملية</th>
                    <th>التاريخ</th>
                    <th>المبلغ</th>
                    <th>طريقة الدفع</th>
                    <th>الحالة</th>
                    <th>رقم المرجع</th>
                    <th>ملاحظات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $index => $payment)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $payment->transaction_id ?? '—' }}</td>
                        <td>{{ optional($payment->payment_date)->format('Y-m-d') ?? '—' }}</td>
                        <td>{{ number_format($payment->amount, 2) }} ر.س</td>
                        <td>
                            @switch($payment->payment_method)
                                @case('cash') نقدي @break
                                @case('card') بطاقة @break
                                @case('bank_transfer') تحويل بنكي @break
                                @case('wallet') محفظة @break
                                @default {{ $payment->payment_method ?? '—' }}
                            @endswitch
                        </td>
                        <td>
                            @switch($payment->status)
                                @case('completed') مكتملة @break
                                @case('pending') معلقة @break
                                @case('failed') فاشلة @break
                                @case('refunded') مسترجعة @break
                                @default {{ $payment->status ?? '—' }}
                            @endswitch
                        </td>
                        <td>{{ $payment->reference_number ?? '—' }}</td>
                        <td>{{ $payment->notes ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
