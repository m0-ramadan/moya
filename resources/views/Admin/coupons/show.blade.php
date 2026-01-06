@extends('Admin.layout.master')

@section('title', 'عرض الكوبون')

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
<style>
    body {
        font-family: "Cairo", sans-serif !important;
    }
    
    .coupon-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        padding: 30px;
        text-align: center;
        margin-bottom: 30px;
    }
    
    .coupon-code {
        font-size: 42px;
        font-weight: bold;
        letter-spacing: 4px;
        font-family: 'Courier New', monospace;
        margin-bottom: 10px;
    }
    
    .coupon-name {
        font-size: 24px;
        margin-bottom: 20px;
        opacity: 0.9;
    }
    
    .info-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        border: 1px solid #e9ecef;
    }
    
    .info-card-title {
        font-size: 16px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid #e9ecef;
    }
    
    .info-item {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid #f8f9fa;
    }
    
    .info-item:last-child {
        border-bottom: none;
    }
    
    .info-label {
        color: #7f8c8d;
        font-weight: 500;
    }
    
    .info-value {
        color: #2c3e50;
        font-weight: 600;
    }
    
    .badge-status {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .badge-active {
        background-color: #d1fae5;
        color: #065f46;
    }
    
    .badge-inactive {
        background-color: #fee2e2;
        color: #991b1b;
    }
    
    .badge-expired {
        background-color: #f3f4f6;
        color: #374151;
    }
    
    .stats-card {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 15px;
        text-align: center;
    }
    
    .stats-number {
        font-size: 24px;
        font-weight: bold;
        color: #696cff;
        margin-bottom: 5px;
    }
    
    .stats-label {
        color: #7f8c8d;
        font-size: 14px;
    }
    
    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }
    
    .table-actions {
        white-space: nowrap;
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
                <a href="{{ route('admin.coupons.index') }}">الكوبونات</a>
            </li>
            <li class="breadcrumb-item active">عرض الكوبون</li>
        </ol>
    </nav>

    <!-- Header Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4" bis_skin_checked="1">
        <h4 class="fw-bold">تفاصيل الكوبون</h4>
        <div class="d-flex gap-2" bis_skin_checked="1">
            <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-primary">
                <i class="fas fa-edit me-1"></i> تعديل
            </a>
            <button type="button" class="btn btn-outline-success" onclick="copyCouponCode('{{ $coupon->code }}')">
                <i class="fas fa-copy me-1"></i> نسخ الكود
            </button>
            <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-right me-1"></i> العودة
            </a>
        </div>
    </div>

    <!-- Coupon Header -->
    <div class="coupon-header" bis_skin_checked="1">
        <div class="coupon-code" bis_skin_checked="1">{{ $coupon->code }}</div>
        <div class="coupon-name" bis_skin_checked="1">{{ $coupon->name }}</div>
        <div class="d-flex justify-content-center gap-3 align-items-center" bis_skin_checked="1">
            @if(!$coupon->is_active)
                <span class="badge-status badge-inactive">غير نشط</span>
            @elseif($coupon->expires_at && $coupon->expires_at->lt(now()))
                <span class="badge-status badge-expired">منتهي</span>
            @else
                <span class="badge-status badge-active">نشط</span>
            @endif
            
            <span class="badge bg-light text-dark">
                {{ $coupon->type === 'percentage' ? 'نسبة مئوية' : 'مبلغ ثابت' }}
            </span>
        </div>
    </div>

    <div class="row" bis_skin_checked="1">
        <!-- Left Column: Coupon Details -->
        <div class="col-md-8" bis_skin_checked="1">
            <!-- Basic Information -->
            <div class="card mb-4" bis_skin_checked="1">
                <div class="card-header" bis_skin_checked="1">
                    <h5 class="mb-0">المعلومات الأساسية</h5>
                </div>
                <div class="card-body" bis_skin_checked="1">
                    <div class="row" bis_skin_checked="1">
                        <div class="col-md-6" bis_skin_checked="1">
                            <div class="info-card" bis_skin_checked="1">
                                <div class="info-card-title" bis_skin_checked="1">القيمة والحدود</div>
                                <div class="info-item" bis_skin_checked="1">
                                    <span class="info-label">قيمة الخصم:</span>
                                    <span class="info-value">
                                        {{ number_format($coupon->value, $coupon->type === 'percentage' ? 0 : 2) }}
                                        {{ $coupon->type === 'percentage' ? '%' : 'ج.م' }}
                                    </span>
                                </div>
                                <div class="info-item" bis_skin_checked="1">
                                    <span class="info-label">الحد الأدنى للطلب:</span>
                                    <span class="info-value">
                                        {{ $coupon->min_order_amount ? number_format($coupon->min_order_amount, 2) . ' ج.م' : 'لا يوجد' }}
                                    </span>
                                </div>
                                <div class="info-item" bis_skin_checked="1">
                                    <span class="info-label">الحد الأقصى للاستخدام:</span>
                                    <span class="info-value">
                                        {{ $coupon->max_uses ?? 'لا نهائي' }}
                                    </span>
                                </div>
                                <div class="info-item" bis_skin_checked="1">
                                    <span class="info-label">لكل مستخدم:</span>
                                    <span class="info-value">
                                        {{ $coupon->max_uses_per_user ?? 'لا نهائي' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6" bis_skin_checked="1">
                            <div class="info-card" bis_skin_checked="1">
                                <div class="info-card-title" bis_skin_checked="1">التواريخ</div>
                                <div class="info-item" bis_skin_checked="1">
                                    <span class="info-label">تاريخ الإنشاء:</span>
                                    <span class="info-value">{{ $coupon->created_at->format('Y/m/d H:i') }}</span>
                                </div>
                                <div class="info-item" bis_skin_checked="1">
                                    <span class="info-label">تاريخ التعديل:</span>
                                    <span class="info-value">{{ $coupon->updated_at->format('Y/m/d H:i') }}</span>
                                </div>
                                <div class="info-item" bis_skin_checked="1">
                                    <span class="info-label">تاريخ البدء:</span>
                                    <span class="info-value">
                                        {{ $coupon->starts_at ? $coupon->starts_at->format('Y/m/d H:i') : 'فوراً' }}
                                    </span>
                                </div>
                                <div class="info-item" bis_skin_checked="1">
                                    <span class="info-label">تاريخ الانتهاء:</span>
                                    <span class="info-value">
                                        {{ $coupon->expires_at ? $coupon->expires_at->format('Y/m/d H:i') : 'لا نهائي' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    @if($coupon->description)
                        <div class="info-card mt-4" bis_skin_checked="1">
                            <div class="info-card-title" bis_skin_checked="1">الوصف</div>
                            <p>{{ $coupon->description }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Usage Statistics -->
            <div class="card mb-4" bis_skin_checked="1">
                <div class="card-header" bis_skin_checked="1">
                    <h5 class="mb-0">إحصائيات الاستخدام</h5>
                </div>
                <div class="card-body" bis_skin_checked="1">
                    <div class="row" bis_skin_checked="1">
                        <div class="col-md-3 col-sm-6 mb-3" bis_skin_checked="1">
                            <div class="stats-card" bis_skin_checked="1">
                                <div class="stats-number" bis_skin_checked="1">{{ $usageStatistics['total'] }}</div>
                                <div class="stats-label" bis_skin_checked="1">إجمالي الاستخدامات</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3" bis_skin_checked="1">
                            <div class="stats-card" bis_skin_checked="1">
                                <div class="stats-number" bis_skin_checked="1">{{ $usageStatistics['today'] }}</div>
                                <div class="stats-label" bis_skin_checked="1">اليوم</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3" bis_skin_checked="1">
                            <div class="stats-card" bis_skin_checked="1">
                                <div class="stats-number" bis_skin_checked="1">{{ $usageStatistics['this_week'] }}</div>
                                <div class="stats-label" bis_skin_checked="1">هذا الأسبوع</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3" bis_skin_checked="1">
                            <div class="stats-card" bis_skin_checked="1">
                                <div class="stats-number" bis_skin_checked="1">{{ $usageStatistics['this_month'] }}</div>
                                <div class="stats-label" bis_skin_checked="1">هذا الشهر</div>
                            </div>
                        </div>
                    </div>

                    <!-- Usage Progress -->
                    @if($coupon->max_uses)
                        <div class="mb-4" bis_skin_checked="1">
                            <div class="d-flex justify-content-between mb-2" bis_skin_checked="1">
                                <span class="text-muted">نسبة الاستخدام</span>
                                <span class="fw-bold">{{ round(($coupon->usages()->count() / $coupon->max_uses) * 100) }}%</span>
                            </div>
                            <div class="progress" style="height: 10px;" bis_skin_checked="1">
                                <div class="progress-bar {{ $coupon->usages()->count() >= $coupon->max_uses ? 'bg-danger' : 'bg-success' }}" 
                                     role="progressbar" 
                                     style="width: {{ min(($coupon->usages()->count() / $coupon->max_uses) * 100, 100) }}%">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-2" bis_skin_checked="1">
                                <small class="text-muted">تم استخدام {{ $coupon->usages()->count() }} من {{ $coupon->max_uses }}</small>
                            </div>
                        </div>
                    @endif

                    @if($coupon->expires_at)
                        <div class="mb-3" bis_skin_checked="1">
                            <div class="d-flex justify-content-between mb-2" bis_skin_checked="1">
                                <span class="text-muted">الوقت المتبقي</span>
                                <span class="fw-bold">
                                    @if($coupon->expires_at->gt(now()))
                                        {{ $coupon->expires_at->diffForHumans(['parts' => 2]) }}
                                    @else
                                        منتهي
                                    @endif
                                </span>
                            </div>
                            @if($coupon->expires_at->gt(now()))
                                <div class="progress" style="height: 10px;" bis_skin_checked="1">
                                    @php
                                        $totalDays = $coupon->created_at->diffInDays($coupon->expires_at);
                                        $passedDays = $coupon->created_at->diffInDays(now());
                                        $percentage = min(($passedDays / $totalDays) * 100, 100);
                                    @endphp
                                    <div class="progress-bar {{ $percentage >= 90 ? 'bg-danger' : ($percentage >= 70 ? 'bg-warning' : 'bg-info') }}" 
                                         role="progressbar" 
                                         style="width: {{ $percentage }}%">
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: Usage History -->
        <div class="col-md-4" bis_skin_checked="1">
            <!-- Quick Actions -->
            <div class="card mb-4" bis_skin_checked="1">
                <div class="card-header" bis_skin_checked="1">
                    <h5 class="mb-0">إجراءات سريعة</h5>
                </div>
                <div class="card-body" bis_skin_checked="1">
                    <div class="d-grid gap-2" bis_skin_checked="1">
                        @if($coupon->is_active)
                            <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="is_active" value="0">
                                <button type="submit" class="btn btn-outline-warning w-100">
                                    <i class="fas fa-ban me-2"></i> تعطيل الكوبون
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="is_active" value="1">
                                <button type="submit" class="btn btn-outline-success w-100">
                                    <i class="fas fa-check me-2"></i> تفعيل الكوبون
                                </button>
                            </form>
                        @endif

                        <button type="button" class="btn btn-outline-info w-100" onclick="shareCoupon('{{ $coupon->code }}')">
                            <i class="fas fa-share-alt me-2"></i> مشاركة الكوبون
                        </button>

                        <button type="button" class="btn btn-outline-primary w-100" onclick="sendToEmail('{{ $coupon->code }}')">
                            <i class="fas fa-envelope me-2"></i> إرسال بالبريد
                        </button>

                        <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" 
                              class="d-inline" onsubmit="return confirmDelete()">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="fas fa-trash me-2"></i> حذف الكوبون
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Recent Usage -->
            <div class="card" bis_skin_checked="1">
                <div class="card-header" bis_skin_checked="1">
                    <h5 class="mb-0">أحدث الاستخدامات</h5>
                </div>
                <div class="card-body" bis_skin_checked="1">
                    @if($usages->count() > 0)
                        <div class="list-group list-group-flush" bis_skin_checked="1">
                            @foreach($usages->take(5) as $usage)
                                <div class="list-group-item px-0" bis_skin_checked="1">
                                    <div class="d-flex justify-content-between align-items-center" bis_skin_checked="1">
                                        <div class="d-flex align-items-center gap-3" bis_skin_checked="1">
                                            @if($usage->user)
                                                <img src="{{ $usage->user->avatar ? get_user_image($usage->user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($usage->user->name) }}" 
                                                     class="user-avatar" alt="{{ $usage->user->name }}">
                                                <div bis_skin_checked="1">
                                                    <div class="fw-semibold" bis_skin_checked="1">{{ $usage->user->name }}</div>
                                                    <small class="text-muted">{{ $usage->created_at->diffForHumans() }}</small>
                                                </div>
                                            @else
                                                <div class="user-avatar bg-light d-flex align-items-center justify-content-center" bis_skin_checked="1">
                                                    <i class="fas fa-user text-muted"></i>
                                                </div>
                                                <div bis_skin_checked="1">
                                                    <div class="fw-semibold" bis_skin_checked="1">زائر</div>
                                                    <small class="text-muted">{{ $usage->created_at->diffForHumans() }}</small>
                                                </div>
                                            @endif
                                        </div>
                                        @if($usage->order)
                                            <a href="{{ route('admin.orders.show', $usage->order) }}" 
                                               class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        @if($usages->count() > 5)
                            <div class="text-center mt-3" bis_skin_checked="1">
                                <a href="#usageHistory" class="btn btn-sm btn-outline-primary">
                                    عرض الكل ({{ $usages->count() }})
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4" bis_skin_checked="1">
                            <i class="fas fa-history fa-2x text-muted mb-3"></i>
                            <p class="text-muted">لا توجد استخدامات بعد</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Usage History Table -->
    <div class="card mt-4" bis_skin_checked="1" id="usageHistory">
        <div class="card-header d-flex justify-content-between align-items-center" bis_skin_checked="1">
            <h5 class="mb-0">سجل الاستخدامات</h5>
            <div class="d-flex gap-2" bis_skin_checked="1">
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="exportUsageHistory()">
                    <i class="fas fa-file-export me-1"></i> تصدير
                </button>
            </div>
        </div>
        <div class="card-body" bis_skin_checked="1">
            <div class="table-responsive" bis_skin_checked="1">
                <table class="table table-hover" id="usageTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>المستخدم</th>
                            <th>الطلب</th>
                            <th>قيمة الخصم</th>
                            <th>الـ IP</th>
                            <th>الجلسة</th>
                            <th>التاريخ</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($usages as $usage)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    @if($usage->user)
                                        <div class="d-flex align-items-center gap-2" bis_skin_checked="1">
                                            <img src="{{ $usage->user->avatar ? get_user_image($usage->user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($usage->user->name) }}" 
                                                 class="user-avatar" alt="{{ $usage->user->name }}">
                                            <div bis_skin_checked="1">
                                                <div class="fw-semibold" bis_skin_checked="1">{{ $usage->user->name }}</div>
                                                <small class="text-muted">{{ $usage->user->email }}</small>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">زائر</span>
                                    @endif
                                </td>
                                <td>
                                    @if($usage->order)
                                        <a href="{{ route('admin.orders.show', $usage->order) }}" class="fw-semibold">
                                            #{{ $usage->order->id }}
                                        </a>
                                        <br>
                                        <small class="text-muted">{{ number_format($usage->order->total, 2) }} ج.م</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-bold text-success">
                                        {{ number_format($usage->discount_amount ?? 0, 2) }} ج.م
                                    </span>
                                </td>
                                <td>
                                    <code>{{ $usage->ip_address }}</code>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $usage->session_id ? substr($usage->session_id, 0, 8) . '...' : '-' }}</small>
                                </td>
                                <td>
                                    <div bis_skin_checked="1">
                                        <div class="fw-semibold" bis_skin_checked="1">{{ $usage->created_at->format('Y/m/d') }}</div>
                                        <small class="text-muted">{{ $usage->created_at->format('H:i') }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="table-actions" bis_skin_checked="1">
                                        @if($usage->order)
                                            <a href="{{ route('admin.orders.show', $usage->order) }}" 
                                               class="btn btn-sm btn-outline-info" title="عرض الطلب">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($usages->hasPages())
                <div class="mt-3">
                    {{ $usages->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#usageTable').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json'
            },
            responsive: true,
            pageLength: 10,
            order: [[0, 'desc']]
        });
    });

    // Copy coupon code to clipboard
    window.copyCouponCode = function(code) {
        navigator.clipboard.writeText(code).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'تم النسخ!',
                text: `تم نسخ الكود: ${code}`,
                timer: 1500,
                showConfirmButton: false
            });
        });
    }

    // Share coupon
    window.shareCoupon = function(code) {
        if (navigator.share) {
            navigator.share({
                title: 'كوبون خصم',
                text: `استخدم كود الخصم: ${code}`,
                url: window.location.href
            });
        } else {
            navigator.clipboard.writeText(code).then(() => {
                Swal.fire({
                    icon: 'info',
                    title: 'تم النسخ!',
                    text: 'تم نسخ كود الكوبون، يمكنك مشاركته الآن'
                });
            });
        }
    }

    // Send to email
    window.sendToEmail = function(code) {
        Swal.fire({
            title: 'إرسال الكوبون بالبريد',
            input: 'email',
            inputLabel: 'أدخل البريد الإلكتروني',
            inputPlaceholder: 'example@email.com',
            showCancelButton: true,
            confirmButtonText: 'إرسال',
            cancelButtonText: 'إلغاء',
            preConfirm: (email) => {
                if (!email) {
                    Swal.showValidationMessage('الرجاء إدخال البريد الإلكتروني');
                    return false;
                }
                return email;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Implement email sending logic here
                Swal.fire({
                    icon: 'info',
                    title: 'قيد التطوير',
                    text: 'هذه الميزة قيد التطوير حالياً',
                    confirmButtonText: 'حسناً'
                });
            }
        });
    }

    // Export usage history
    window.exportUsageHistory = function() {
        Swal.fire({
            title: 'تصدير سجل الاستخدامات',
            text: 'سيتم تصدير جميع الاستخدامات إلى ملف Excel',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'تصدير',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                // Implement export logic here
                window.open('{{ route("admin.coupons.export") }}?type=excel&coupon_id={{ $coupon->id }}', '_blank');
            }
        });
    }

    // Confirm delete
    function confirmDelete() {
        return confirm('هل أنت متأكد من حذف هذا الكوبون؟ هذا الإجراء لا يمكن التراجع عنه.');
    }
</script>
@endsection