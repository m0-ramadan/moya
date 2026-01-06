@extends('Admin.layout.master')

@section('title', 'وسائل الدفع')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: "Cairo", sans-serif !important;
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-active {
            /* background-color: #d4edda; */
            color: #155724;
        }

        .status-inactive {
            /* background-color: #f8d7da; */
            color: #721c24;
        }

        .type-badge {
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: 600;
        }

        .type-payment {
            /* background-color: #e7f5ff; */
            color: #0c63e4;
        }

        .type-method {
            /* background-color: #f8f9fa; */
            color: #495057;
        }

        .icon-preview {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: #f8f9fa;
            font-size: 20px;
            color: #696cff;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn-action {
            width: 32px;
            height: 32px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .toggle-switch {
            position: relative;
            width: 50px;
            height: 26px;
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
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 4px;
            bottom: 4px;
            /* background-color: white; */
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.toggle-slider {
            background-color: #696cff;
        }

        input:checked+.toggle-slider:before {
            transform: translateX(24px);
        }

        .table-card {
            /* background: white; */
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .table-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            /* color: white; */
            padding: 20px 25px;
        }

        .search-box {
            position: relative;
        }

        .search-box input {
            padding-right: 40px;
            border-radius: 25px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .search-box input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .search-box .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: white;
        }

        .stats-card {
            /* background: white; */
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border-top: 4px solid #696cff;
            transition: transform 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .stats-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 15px;
        }

        .icon-active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .icon-inactive {
            /* background: #f8f9fa; */
            color: #6c757d;
        }

        .icon-payment {
            background: #e7f5ff;
            color: #0c63e4;
        }

        .icon-method {
            /* background: #f8f9fa; */
            color: #495057;
        }

        .stats-number {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stats-label {
            color: #6c757d;
            font-size: 14px;
        }

        .empty-state {
            text-align: center;
            padding: 50px 20px;
        }

        .empty-state-icon {
            font-size: 60px;
            color: #dee2e6;
            margin-bottom: 20px;
        }

        .empty-state-text {
            color: #6c757d;
            margin-bottom: 20px;
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
                <li class="breadcrumb-item active">وسائل الدفع</li>
            </ol>
        </nav>

        <div class="row mb-4" bis_skin_checked="1">
            <div class="col-lg-3 col-md-6 mb-4" bis_skin_checked="1">
                <div class="stats-card" bis_skin_checked="1">
                    <div class="stats-icon icon-active" bis_skin_checked="1">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div class="stats-number" bis_skin_checked="1">
                        {{ $paymentMethods->where('is_active', true)->count() }}
                    </div>
                    <div class="stats-label" bis_skin_checked="1">وسائل دفع نشطة</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4" bis_skin_checked="1">
                <div class="stats-card" bis_skin_checked="1">
                    <div class="stats-icon icon-inactive" bis_skin_checked="1">
                        <i class="fas fa-ban"></i>
                    </div>
                    <div class="stats-number" bis_skin_checked="1">
                        {{ $paymentMethods->where('is_active', false)->count() }}
                    </div>
                    <div class="stats-label" bis_skin_checked="1">وسائل دفع غير نشطة</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4" bis_skin_checked="1">
                <div class="stats-card" bis_skin_checked="1">
                    <div class="stats-icon icon-payment" bis_skin_checked="1">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="stats-number" bis_skin_checked="1">
                        {{ $paymentMethods->where('is_payment', true)->count() }}
                    </div>
                    <div class="stats-label" bis_skin_checked="1">طرق دفع</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4" bis_skin_checked="1">
                <div class="stats-card" bis_skin_checked="1">
                    <div class="stats-icon icon-method" bis_skin_checked="1">
                        <i class="fas fa-cog"></i>
                    </div>
                    <div class="stats-number" bis_skin_checked="1">
                        {{ $paymentMethods->where('is_payment', false)->count() }}
                    </div>
                    <div class="stats-label" bis_skin_checked="1">طرق أخرى</div>
                </div>
            </div>
        </div>

        <div class="row" bis_skin_checked="1">
            <div class="col-12" bis_skin_checked="1">
                <div class="table-card" bis_skin_checked="1">
                    <div class="table-header" bis_skin_checked="1">
                        <div class="row align-items-center" bis_skin_checked="1">
                            <div class="col-md-6" bis_skin_checked="1">
                                <h5 class="mb-0">وسائل الدفع</h5>
                                <small class="opacity-75">إدارة جميع وسائل وطرق الدفع في النظام</small>
                            </div>
                            <div class="col-md-6" bis_skin_checked="1">
                                <div class="d-flex justify-content-end align-items-center gap-3" bis_skin_checked="1">
                                    <div class="search-box" bis_skin_checked="1">
                                        <i class="fas fa-search search-icon"></i>
                                        <input type="text" class="form-control" placeholder="بحث في وسائل الدفع..."
                                            id="searchInput" style="width: 250px;">
                                    </div>
                                    <a href="{{ route('admin.payment-methods.create') }}" class="btn btn-light">
                                        <i class="fas fa-plus me-2"></i>إضافة وسيلة دفع
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive" bis_skin_checked="1">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th width="60">#</th>
                                    <th>الاسم</th>
                                    <th>المعرف</th>
                                    <th>الأيقونة</th>
                                    <th>النوع</th>
                                    <th>الحالة</th>
                                    <th>تاريخ الإضافة</th>
                                    <th width="120">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="paymentMethodsTable">
                                @forelse($paymentMethods as $method)
                                    <tr data-id="{{ $method->id }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="d-flex align-items-center" bis_skin_checked="1">
                                                <div class="icon-preview me-3" bis_skin_checked="1">
                                                    @if ($method->icon)
                                                        <i class="{{ $method->icon }}"></i>
                                                    @else
                                                        <i class="fas fa-credit-card"></i>
                                                    @endif
                                                </div>
                                                <div bis_skin_checked="1">
                                                    <strong>{{ $method->name }}</strong>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <code>{{ $method->key }}</code>
                                        </td>
                                        <td>
                                            @if ($method->icon)
                                                <span class="badge bg-info">
                                                    <i class="{{ $method->icon }} me-1"></i>
                                                    {{ $method->icon }}
                                                </span>
                                            @else
                                                <span class="text-muted">بدون أيقونة</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($method->is_payment)
                                                <span class="type-badge type-payment">
                                                    <i class="fas fa-money-bill-wave me-1"></i>
                                                    طريقة دفع
                                                </span>
                                            @else
                                                <span class="type-badge type-method">
                                                    <i class="fas fa-cog me-1"></i>
                                                    طريقة أخرى
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2" bis_skin_checked="1">
                                                <label class="toggle-switch">
                                                    <input type="checkbox" class="status-toggle"
                                                        data-id="{{ $method->id }}"
                                                        {{ $method->is_active ? 'checked' : '' }}>
                                                    <span class="toggle-slider"></span>
                                                </label>
                                                <span
                                                    class="status-badge {{ $method->is_active ? 'status-active' : 'status-inactive' }}">
                                                    {{ $method->is_active ? 'نشط' : 'غير نشط' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $method->created_at->translatedFormat('d M Y') }}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="action-buttons" bis_skin_checked="1">
                                                <a href="{{ route('admin.payment-methods.show', $method) }}"
                                                    class="btn btn-action btn-info" title="عرض التفاصيل">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.payment-methods.edit', $method) }}"
                                                    class="btn btn-action btn-warning" title="تعديل">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-action btn-danger delete-btn"
                                                    title="حذف" data-id="{{ $method->id }}"
                                                    data-name="{{ $method->name }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8">
                                            <div class="empty-state" bis_skin_checked="1">
                                                <div class="empty-state-icon" bis_skin_checked="1">
                                                    <i class="fas fa-credit-card"></i>
                                                </div>
                                                <h5 class="empty-state-text">لا توجد وسائل دفع</h5>
                                                <a href="{{ route('admin.payment-methods.create') }}"
                                                    class="btn btn-primary">
                                                    <i class="fas fa-plus me-2"></i>إضافة وسيلة دفع جديدة
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($paymentMethods->hasPages())
                        <div class="card-footer" bis_skin_checked="1">
                            <div class="d-flex justify-content-center" bis_skin_checked="1">
                                {{ $paymentMethods->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // البحث في الجدول
            $('#searchInput').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                $('#paymentMethodsTable tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });

            // تبديل الحالة
            $('.status-toggle').on('change', function() {
                const methodId = $(this).data('id');
                const isChecked = $(this).is(':checked');

                $.ajax({
                    url: "{{ route('admin.payment-methods.toggle-status', '') }}/" + methodId,
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        _method: 'PATCH'
                    },
                    success: function(response) {
                        if (response.success) {
                            const statusBadge = $(`tr[data-id="${methodId}"] .status-badge`);
                            if (response.is_active) {
                                statusBadge.removeClass('status-inactive').addClass(
                                    'status-active').text('نشط');
                            } else {
                                statusBadge.removeClass('status-active').addClass(
                                    'status-inactive').text('غير نشط');
                            }

                            Swal.fire({
                                icon: 'success',
                                title: 'نجاح',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: 'حدث خطأ أثناء تغيير الحالة',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                });
            });

            // حذف وسيلة الدفع
            $('.delete-btn').on('click', function() {
                const methodId = $(this).data('id');
                const methodName = $(this).data('name');

                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: `سيتم حذف وسيلة الدفع "${methodName}" نهائياً`,
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
                            url: "{{ route('admin.payment-methods.destroy', '') }}/" +
                                methodId,
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
                                    location.reload();
                                });
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'خطأ',
                                    text: 'حدث خطأ أثناء الحذف',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            }
                        });
                    }
                });
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
    </script>
@endsection
