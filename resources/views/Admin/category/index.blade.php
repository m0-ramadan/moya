@extends('Admin.layout.master')

@section('title', 'إدارة الأقسام')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: "Cairo", sans-serif !important;
        }

        .layout-navbar-fixed body:not(.modal-open) .layout-content-navbar .layout-navbar,
        .layout-menu-fixed body:not(.modal-open) .layout-content-navbar .layout-navbar,
        .layout-menu-fixed-offcanvas body:not(.modal-open) .layout-content-navbar .layout-navbar {
            z-index: 1043;
        }

        .layout-navbar-fixed body:not(.modal-open) .layout-content-navbar .layout-menu,
        .layout-menu-fixed body:not(.modal-open) .layout-content-navbar .layout-menu,
        .layout-menu-fixed-offcanvas body:not(.modal-open) .layout-content-navbar .layout-menu {
            z-index: 1043;
        }

        i {
            margin: 0px 5px 0px 5px
        }

        textarea {
            height: 100px;
        }

        .category-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-active {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-inactive {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .parent-category {
            font-weight: 700;
            /* background-color: #f8f9fa; */
        }

        .child-category {
            padding-left: 30px !important;
            /* background-color: #fff; */
        }

        .badge-count {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            /* background-color: #e9ecef;
                                                                        color: #495057;  */
            font-size: 12px;
            margin-left: 5px;
        }

        .filter-card {
            /* background: #f8f9fa; */
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .action-dropdown {
            min-width: 150px;
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
                <li class="breadcrumb-item active">الأقسام</li>
            </ol>
        </nav>

        <!-- Filters Section -->
        <div class="card mb-4 filter-card" bis_skin_checked="1">
            <div class="card-body" bis_skin_checked="1">
                <form action="{{ route('admin.categories.index') }}" method="GET" id="filter-form">
                    <div class="row" bis_skin_checked="1">
                        <div class="col-md-3 mb-3" bis_skin_checked="1">
                            <label for="search" class="form-label">بحث</label>
                            <input type="text" name="search" id="search" class="form-control"
                                placeholder="ابحث بالاسم أو الوصف" value="{{ request('search') }}">
                        </div>

                        <div class="col-md-3 mb-3" bis_skin_checked="1">
                            <label for="parent_id" class="form-label">القسم الرئيسي</label>
                            <select name="parent_id" id="parent_id" class="form-select">
                                <option value="">جميع الأقسام</option>
                                <option value="null" {{ request('parent_id') === 'null' ? 'selected' : '' }}>الأقسام
                                    الرئيسية فقط</option>
                                @foreach ($parentCategories as $parent)
                                    <option value="{{ $parent->id }}"
                                        {{ request('parent_id') == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2 mb-3" bis_skin_checked="1">
                            <label for="status_id" class="form-label">الحالة</label>
                            <select name="status_id" id="status_id" class="form-select">
                                <option value="">جميع الحالات</option>
                                <option value="1" {{ request('status_id') == '1' ? 'selected' : '' }}>نشط</option>
                                <option value="2" {{ request('status_id') == '2' ? 'selected' : '' }}>غير نشط</option>
                            </select>
                        </div>

                        <div class="col-md-2 mb-3" bis_skin_checked="1">
                            <label for="order_by" class="form-label">ترتيب حسب</label>
                            <select name="order_by" id="order_by" class="form-select">
                                <option value="order" {{ request('order_by') == 'order' ? 'selected' : '' }}>الترتيب
                                </option>
                                <option value="name" {{ request('order_by') == 'name' ? 'selected' : '' }}>الاسم</option>
                                <option value="created_at" {{ request('order_by') == 'created_at' ? 'selected' : '' }}>
                                    تاريخ الإضافة</option>
                            </select>
                        </div>

                        <div class="col-md-2 mb-3" bis_skin_checked="1">
                            <label class="form-label d-block">&nbsp;</label>
                            <div class="d-grid gap-2 d-md-block" bis_skin_checked="1">
                                <button type="submit" class="btn btn-primary waves-effect">
                                    <i class="fas fa-filter"></i> تصفية
                                </button>
                                <a href="{{ route('admin.categories.index') }}"
                                    class="btn btn-outline-secondary waves-effect">
                                    <i class="fas fa-redo"></i> إعادة تعيين
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Basic Bootstrap Table -->
        <div class="card" bis_skin_checked="1">
            <div class="p-3 d-flex justify-content-between align-items-center" bis_skin_checked="1">
                <div bis_skin_checked="1">
                    <h5 class="card-title mb-0">الأقسام ({{ $categories->total() }})</h5>
                </div>
                <div bis_skin_checked="1">
                    <div class="btn-group" role="group" aria-label="Category Actions" bis_skin_checked="1">
                        <button type="button" class="btn btn-primary waves-effect" data-bs-toggle="modal"
                            data-bs-target="#addCategoryModal">
                            <i class="fas fa-plus-circle"></i> إضافة قسم
                        </button>

                        <!-- Add Subcategory Button -->
                        <button type="button" class="btn btn-success waves-effect" data-bs-toggle="modal"
                            data-bs-target="#addSubcategoryModal">
                            <i class="fas fa-layer-group"></i> إضافة قسم فرعي
                        </button>

                        <button type="button" class="btn btn-info waves-effect" id="reorderBtn">
                            <i class="fas fa-sort-amount-down"></i> إعادة الترتيب
                        </button>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible m-3" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible m-3" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive text-nowrap" bis_skin_checked="1">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th>القسم</th>
                            <th>الصورة</th>
                            <th>الوصف</th>
                            <th>الحالة</th>
                            <th>الترتيب</th>
                            <th>المحتوى</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($categories as $category)
                            <tr class="{{ is_null($category->parent_id) ? 'parent-category' : 'child-category' }}">
                                <td>{{ $loop->iteration + ($categories->currentPage() - 1) * $categories->perPage() }}
                                </td>

                                <td>
                                    <strong>{{ $category->name }}</strong>
                                    @if (!is_null($category->parent_id))
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-level-up-alt fa-rotate-90"></i> تابع لـ:
                                            {{ $category->parent->name ?? 'غير محدد' }}
                                        </small>
                                    @endif
                                    @if ($category->slug)
                                        <br>
                                        <small class="text-info">
                                            <i class="fas fa-link"></i> {{ $category->slug }}
                                        </small>
                                    @endif
                                </td>

                                <td>
                                    @if ($category->image)
                                        <img src="{{ get_user_image($category->image) }} " alt="{{ $category->name }}"
                                            class="category-image" data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="صورة القسم">
                                    @else
                                        <span class="badge bg-secondary">بدون صورة</span>
                                    @endif

                                    @if ($category->sub_image)
                                        <div class="mt-1" bis_skin_checked="1">
                                            <small class="text-muted">صورة فرعية</small>
                                        </div>
                                    @endif
                                </td>

                                <td>
                                    @if ($category->description)
                                        {{ Str::limit($category->description, 50) }}
                                    @else
                                        <span class="text-muted">---</span>
                                    @endif
                                </td>

                                <td>
                                    @if ($category->status_id == 1)
                                        <span class="status-badge status-active">
                                            <i class="fas fa-check-circle"></i> نشط
                                        </span>
                                    @else
                                        <span class="status-badge status-inactive">
                                            <i class="fas fa-times-circle"></i> غير نشط
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <span class="badge bg-primary">{{ $category->order }}</span>
                                </td>

                                <td>
                                    <div class="d-flex flex-column" bis_skin_checked="1">
                                        <small>
                                            <i class="fas fa-box"></i> منتجات
                                            <span class="badge-count">{{ $category->products->count() ?? 0 }}</span>
                                        </small>
                                        <small class="mt-1">
                                            <i class="fas fa-sitemap"></i> أقسام فرعية
                                            <span class="badge-count">{{ $category->children->count() ?? 0 }}</span>
                                        </small>
                                    </div>
                                </td>

                                <td>
                                    <div class="dropdown" bis_skin_checked="1">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu action-dropdown" bis_skin_checked="1">
                                            <a class="dropdown-item"
                                                href="{{ route('admin.categories.show', $category->id) }}">
                                                <i class="fas fa-eye me-2"></i>عرض
                                            </a>
                                            <a class="dropdown-item"
                                                href="{{ route('admin.categories.edit', $category) }}">
                                                <i class="fas fa-edit me-2"></i>تعديل
                                            </a>

                                            @if (is_null($category->parent_id))
                                                <a class="dropdown-item"
                                                    href="{{ route('admin.categories.index', ['parent_id' => $category->id]) }}">
                                                    <i class="fas fa-layer-group me-2"></i>عرض الأقسام الفرعية
                                                </a>
                                            @endif

                                            <div class="dropdown-divider" bis_skin_checked="1"></div>

                                            <a class="dropdown-item text-danger btn-delete" href="javascript:void(0);"
                                                data-url="{{ route('admin.categories.destroy', $category) }}"
                                                data-name="{{ $category->name }}">
                                                <i class="fas fa-trash me-2"></i>حذف
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="text-muted" bis_skin_checked="1">
                                        <i class="fas fa-folder-open fa-2x mb-3"></i>
                                        <h5>لا توجد أقسام</h5>
                                        <p>ابدأ بإضافة قسم جديد</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Pagination -->
                @if ($categories->hasPages())
                    <div class="m-3">
                        <nav>
                            <ul class="pagination">

                                {{-- Previous Page Link --}}
                                @if ($categories->onFirstPage())
                                    <li class="page-item disabled" aria-disabled="true">
                                        <span class="page-link waves-effect" aria-hidden="true">‹</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link waves-effect" href="{{ $categories->previousPageUrl() }}"
                                            rel="prev">‹</a>
                                    </li>
                                @endif

                                {{-- Pagination Elements --}}
                                @foreach ($categories->links()->elements[0] as $page => $url)
                                    @if ($page == $categories->currentPage())
                                        <li class="page-item active" aria-current="page">
                                            <span class="page-link waves-effect">{{ $page }}</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link waves-effect"
                                                href="{{ $url }}">{{ $page }}</a>
                                        </li>
                                    @endif
                                @endforeach

                                {{-- Next Page Link --}}
                                @if ($categories->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link waves-effect" href="{{ $categories->nextPageUrl() }}"
                                            rel="next">›</a>
                                    </li>
                                @else
                                    <li class="page-item disabled" aria-disabled="true">
                                        <span class="page-link waves-effect" aria-hidden="true">›</span>
                                    </li>
                                @endif

                            </ul>
                        </nav>
                    </div>
                @endif

            </div>
        </div>
        <!--/ Basic Bootstrap Table -->
    </div>

    <!-- Add Category Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">إضافة قسم جديد</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">اسم القسم <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="slug" class="form-label">الرابط (Slug)</label>
                                <input type="text" class="form-control" id="slug" name="slug">
                                <small class="text-muted">سيتم إنشاؤه تلقائياً إذا تركت فارغاً</small>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="description" class="form-label">الوصف</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="parent_id" class="form-label">القسم الرئيسي</label>
                                <select class="form-select" id="parent_id" name="parent_id">
                                    <option value="">قسم رئيسي (بدون أب)</option>
                                    @foreach ($parentCategories as $parent)
                                        <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="order" class="form-label">ترتيب العرض</label>
                                <input type="number" class="form-control" id="order" name="order" value="0"
                                    min="0">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="status_id" class="form-label">الحالة <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="status_id" name="status_id" required>
                                    <option value="1" selected>نشط</option>
                                    <option value="2">غير نشط</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="image" class="form-label">صورة القسم</label>
                                <input type="file" class="form-control" id="image" name="image"
                                    accept="image/*">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="sub_image" class="form-label">صورة فرعية</label>
                                <input type="file" class="form-control" id="sub_image" name="sub_image"
                                    accept="image/*">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary">حفظ</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Subcategory Modal -->
    <div class="modal fade" id="addSubcategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">إضافة قسم فرعي جديد</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="sub_name" class="form-label">اسم القسم الفرعي <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="sub_name" name="name" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="sub_parent_id" class="form-label">القسم الرئيسي <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="sub_parent_id" name="parent_id" required>
                                    <option value="">اختر القسم الرئيسي</option>
                                    @foreach ($parentCategories as $parent)
                                        <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="sub_description" class="form-label">الوصف</label>
                                <textarea class="form-control" id="sub_description" name="description" rows="3"></textarea>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="sub_slug" class="form-label">الرابط (Slug)</label>
                                <input type="text" class="form-control" id="sub_slug" name="slug">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="sub_order" class="form-label">ترتيب العرض</label>
                                <input type="number" class="form-control" id="sub_order" name="order" value="0"
                                    min="0">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="sub_status_id" class="form-label">الحالة <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="sub_status_id" name="status_id" required>
                                    <option value="1" selected>نشط</option>
                                    <option value="2">غير نشط</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="sub_image" class="form-label">صورة القسم</label>
                                <input type="file" class="form-control" id="sub_image" name="image"
                                    accept="image/*">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-success">إضافة قسم فرعي</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Reorder Modal -->
    <div class="modal fade" id="reorderModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">إعادة ترتيب الأقسام</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        اسحب وأفلت الأقسام لتغيير ترتيبها. يمكنك أيضاً تحويل الأقسام الفرعية إلى أقسام رئيسية والعكس.
                    </div>

                    <div id="category-tree" class="sortable-list">
                        <!-- Categories will be loaded here via AJAX -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-primary" id="saveOrderBtn">حفظ الترتيب</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();

            // Auto-generate slug from name
            $('#name').on('keyup', function() {
                const name = $(this).val();
                if (name && !$('#slug').val()) {
                    const slug = name.toLowerCase()
                        .replace(/\s+/g, '-')
                        .replace(/[^a-z0-9\-]/g, '');
                    $('#slug').val(slug);
                }
            });

            // Delete confirmation
            // In your blade template or separate JS file
            $(document).ready(function() {
                $('.btn-delete').on('click', function(e) {
                    e.preventDefault();
                    const url = $(this).data('url');
                    const name = $(this).data('name');

                    Swal.fire({
                        title: 'هل أنت متأكد؟',
                        text: `سيتم حذف القسم "${name}" بشكل دائم`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'نعم، احذف',
                        cancelButtonText: 'إلغاء'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: url,
                                type: 'DELETE',
                                data: {
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(response) {
                                    if (response.success) {
                                        Swal.fire(
                                            'تم الحذف!',
                                            'تم حذف القسم بنجاح.',
                                            'success'
                                        ).then(() => {
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire(
                                            'خطأ!',
                                            response.message ||
                                            'حدث خطأ أثناء الحذف.',
                                            'error'
                                        );
                                    }
                                },
                                error: function(xhr) {
                                    Swal.fire(
                                        'خطأ!',
                                        xhr.responseJSON?.message ||
                                        'حدث خطأ أثناء الحذف.',
                                        'error'
                                    );
                                }
                            });
                        }
                    });
                });
            });
            // Reorder functionality
            $('#reorderBtn').on('click', function() {
                loadCategoryTree();
                $('#reorderModal').modal('show');
            });

            function loadCategoryTree() {
                $.ajax({
                    url: '{{ route('admin.categories.tree.data') }}',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        //console.log('Response:', response);
                        if (response.success) {
                            renderCategoryTree(response.data);
                            initializeSortable();
                        } else {
                            $('#category-tree').html(
                                '<div class="alert alert-danger">' + (response.message ||
                                    'فشل في تحميل بيانات الأقسام') + '</div>'
                            );
                        }
                    },
                    error: function(xhr, status, error) {
                        // console.log('AJAX Error:', xhr.responseText);
                        let errorMsg = 'فشل في تحميل بيانات الأقسام';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        } else if (xhr.status === 401) {
                            errorMsg = 'يجب تسجيل الدخول أولاً. جارٍ إعادة التوجيه...';
                            setTimeout(() => {
                                window.location.href = '{{ route('admin.login') }}';
                            }, 2000);
                        }

                        $('#category-tree').html(
                            '<div class="alert alert-danger">' + errorMsg + '</div>'
                        );
                    }
                });
            }

            function renderCategoryTree(categories) {
                let html = '<ul class="list-group sortable-categories">';

                categories.forEach(function(category) {
                    html += `
                    <li class="list-group-item d-flex justify-content-between align-items-center" 
                         data-id="${category.id}" 
                         data-parent="${category.parent_id || ''}">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-arrows-alt me-3 text-muted handle"></i>
                            ${category.name}
                            ${category.children.length > 0 ? 
                                `<span class="badge bg-info ms-2">${category.children.length} فرعي</span>` : 
                                ''}
                        </div>
                        <div>
                            <span class="badge bg-secondary me-2">الترتيب: ${category.order}</span>
                            <select class="form-select form-select-sm d-inline-block w-auto parent-selector">
                                <option value="">قسم رئيسي</option>
                `;

                    // Add other parent options
                    categories.forEach(function(parent) {
                        if (parent.id !== category.id) {
                            html += `<option value="${parent.id}" 
                                  ${parent.id == category.parent_id ? 'selected' : ''}>
                                  ${parent.name}
                                 </option>`;
                        }
                    });

                    html += `
                            </select>
                        </div>
                    </li>
                `;

                    // Render children
                    if (category.children && category.children.length > 0) {
                        html += '<ul class="list-group ms-4 sortable-children">';
                        category.children.forEach(function(child) {
                            html += `
                            <li class="list-group-item d-flex justify-content-between align-items-center child-item"
                                 data-id="${child.id}" 
                                 data-parent="${child.parent_id}">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-arrows-alt me-3 text-muted handle"></i>
                                    <i class="fas fa-level-up-alt fa-rotate-90 me-2 text-muted"></i>
                                    ${child.name}
                                </div>
                                <span class="badge bg-secondary">الترتيب: ${child.order}</span>
                            </li>
                        `;
                        });
                        html += '</ul>';
                    }
                });

                html += '</ul>';
                $('#category-tree').html(html);
            }


            function initializeSortable() {
                // Initialize Sortable for parent categories
                new Sortable(document.querySelector('.sortable-categories'), {
                    group: 'categories',
                    animation: 150,
                    handle: '.handle',
                    onEnd: function(evt) {
                        updateOrderNumbers();
                    }
                });

                // Initialize Sortable for child categories
                document.querySelectorAll('.sortable-children').forEach(function(el) {
                    new Sortable(el, {
                        group: 'categories',
                        animation: 150,
                        handle: '.handle',
                        onEnd: function(evt) {
                            updateOrderNumbers();
                        }
                    });
                });
            }

            function updateOrderNumbers() {
                $('.sortable-categories > li').each(function(index) {
                    $(this).find('.badge.bg-secondary:contains("الترتيب")').text('الترتيب: ' + (index + 1));
                });

                $('.sortable-children').each(function() {
                    $(this).find('li').each(function(index) {
                        $(this).find('.badge.bg-secondary:contains("الترتيب")').text('الترتيب: ' + (
                            index + 1));
                    });
                });
            }

            // Save reorder
            $('#saveOrderBtn').on('click', function() {
                const categories = [];

                $('.sortable-categories > li').each(function(index) {
                    const categoryId = $(this).data('id');
                    const parentId = $(this).find('.parent-selector').val() || null;

                    categories.push({
                        id: categoryId,
                        order: index + 1,
                        parent_id: parentId
                    });

                    // Get children
                    $(this).next('ul.sortable-children').find('li').each(function(childIndex) {
                        const childId = $(this).data('id');
                        const childParentId = $(this).closest('ul').prev('li').data('id');

                        categories.push({
                            id: childId,
                            order: childIndex + 1,
                            parent_id: childParentId
                        });
                    });
                });

                $.ajax({
                    url: '{{ route('admin.categories.updateOrder') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        categories: categories
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire(
                                'نجاح!',
                                'تم حفظ الترتيب بنجاح.',
                                'success'
                            ).then(() => {
                                $('#reorderModal').modal('hide');
                                location.reload();
                            });
                        }
                    },
                    error: function() {
                        Swal.fire(
                            'خطأ!',
                            'فشل في حفظ الترتيب.',
                            'error'
                        );
                    }
                });
            });

            // Export functionality
            $('#exportBtn').on('click', function() {
                const form = $('#filter-form');
                form.attr('action', '{{ route('admin.categories.export') }}');
                form.attr('target', '_blank');
                form.submit();
                form.removeAttr('target');
            });

            // Toggle filter card
            $('#toggleFilters').on('click', function() {
                $('.filter-card').slideToggle();
            });
        });
    </script>
@endsection
