@php
    use App\Models\Admin;
@endphp
<header class="main-nav">
    <div class="text-center">
        <img class="img-90" src="{{ asset(App\Models\Setting::first()->logo) }}" alt="">
        <h6 class="mt-3 f-14 f-w-600"></h6>
    </div>
    <nav>
        <div class="main-navbar">
            <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
            <div id="mainnav">
                <ul class="nav-menu custom-scrollbar" style="height:600px">
                    <li class="back-btn">
                        <div class="mobile-back text-end"><span>Back</span><i class="fa fa-angle-right ps-2"
                                aria-hidden="true"></i></div>
                    </li>
                    <!-- Search Inputs -->
                    @if (auth()->user()->hasAnyPermission([
                                'عرض الرحلات',
                                'عرض الشحنات',
                                'عرض شحنة برقم أو اسم المستلم',
                                'عرض شحنة برقم أو اسم المرسل',
                            ]))
                        <li>
                            <form action="{{ route('admin.getTripByCode') }}" method="GET" class="nav-link"
                                style="font-family: 'Cairo', sans-serif;">
                                @if (auth()->user()->hasPermissionTo('عرض الرحلات'))
                                    <input type="text" name="trip_search" class="form-control"
                                        placeholder="بحث عن الرحلات"
                                        style="width: 100%; margin-bottom: 10px; text-align: right;">
                                @endif
                            </form>
                        </li>
                        <li>
                            <form action="{{ route('admin.shipment.getShipByCode') }}" method="GET" class="nav-link"
                                style="font-family: 'Cairo', sans-serif;">
                                @if (auth()->user()->hasPermissionTo('عرض الشحنات'))
                                    <input type="text" name="shipment_search" class="form-control"
                                        placeholder="بحث عن الشحنات"
                                        style="width: 100%; margin-bottom: 10px; text-align: right;">
                                @endif
                            </form>
                        </li>
                        <li>
                            <form action="{{ route('admin.shipment.getShipByRecieve') }}" method="GET"
                                class="nav-link" style="font-family: 'Cairo', sans-serif;">
                                @if (auth()->user()->hasPermissionTo('عرض شحنة برقم أو اسم المستلم'))
                                    <input type="text" name="shipment_search" class="form-control"
                                        placeholder="شحنة برقم أو اسم المستلم"
                                        style="width: 100%; margin-bottom: 10px; text-align: right;">
                                @endif
                            </form>
                        </li>
                        <li>
                            <form action="{{ route('admin.shipment.getShipBySender') }}" method="GET" class="nav-link"
                                style="font-family: 'Cairo', sans-serif;">
                                @if (auth()->user()->hasPermissionTo('عرض شحنة برقم أو اسم المرسل'))
                                    <input type="text" name="shipment_search" class="form-control"
                                        placeholder="شحنة برقم أو اسم المرسل"
                                        style="width: 100%; margin-bottom: 10px; text-align: right;">
                                @endif
                            </form>
                        </li>
                    @endif
                    <!-- Branch Name Display -->
                    @if (auth()->user()->hasPermissionTo('عرض فرع'))
                        <li class="dropdown">
                            <a class="nav-link text-center" title="">
                                <span style="font-family: 'Cairo', sans-serif;">
                                    @if ( auth()->user()->branch)
                                     فرع
                                    :{{ auth()->user()->branch?->name }}</span>
                                    @endif
                            </a>
                        </li>
                    @endif
                    <!-- System Users -->
                    @if (auth()->user()->hasAnyPermission([
                                'عرض الإدمن',
                                'عرض المندوبين',
                                'عرض السائقين',
                                'عرض الزبائن',
                                'عرض التجار',
                                'عرض الصلاحيات',
                                'عرض الأدوار',
                            ]))
                        <li class="dropdown">
                            <a class="nav-link menu-title" href="javascript:void(0)" data-bs-original-title=""
                                title="">
                                <span style="font-family: 'Cairo', sans-serif;">مستخدمين النظام</span>
                                <div class="according-menu"><i class="fa fa-angle-down"></i></div>
                            </a>
                            <ul class="nav-submenu menu-content">
                                @if (auth()->user()->hasPermissionTo('عرض الإدمن'))
                                    <li><a href="{{ route('admin.admins.index') }}" class="nav-link">الإدمن</a></li>
                                @endif
                                @if (auth()->user()->hasPermissionTo('عرض المندوبين'))
                                    <li><a href="{{ route('admin.representatives.index') }}"
                                            class="nav-link">المندوبين</a></li>
                                @endif
                                @if (auth()->user()->hasPermissionTo('عرض السائقين'))
                                    <li><a href="{{ route('admin.representatives.index_driver') }}"
                                            class="nav-link">السائقين</a></li>
                                @endif
                                @if (auth()->user()->hasPermissionTo('عرض الزبائن'))
                                    <li><a class="nav-link" href="{{ route('admin.client.index') }}">الزبون</a></li>
                                @endif
                                @if (auth()->user()->hasPermissionTo('عرض التجار'))
                                    <li><a class="nav-link" href="{{ route('admin.client.providers') }}">التجار</a>
                                    </li>
                                @endif
                                @if (auth()->user()->hasPermissionTo('عرض الصلاحيات'))
                                    <li><a class="nav-link" href="{{ route('admin.permissions.index') }}">الصلاحيات</a>
                                    </li>
                                @endif
                                @if (auth()->user()->hasPermissionTo('عرض الأدوار'))
                                    <li><a class="nav-link" href="{{ route('admin.roles.index') }}">الأدوار</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif
                    <!-- Financial Transfers -->
                    @if (auth()->user()->hasAnyPermission([
                                'عرض الخزائن',
                                'عرض مديري التحويلات المالية',
                                'عرض موظفي التحويلات المالية',
                                'عرض مكاتب التحويلات',
                                'عرض التحويلات المالية',
                            ]))
                        <li class="dropdown">
                            <a class="nav-link menu-title" href="javascript:void(0)" data-bs-original-title=""
                                title="">
                                <span style="font-family: 'Cairo', sans-serif;">التحويلات المالية</span>
                                <div class="according-menu"><i class="fa fa-angle-down"></i></div>
                            </a>
                            <ul class="nav-submenu menu-content">
                                @if (auth()->user()->hasPermissionTo('عرض الخزائن'))
                                    <li><a class="nav-link" href="{{ route('admin.lockers.index') }}">الخزائن</a></li>
                                @endif
                                @if (auth()->user()->hasPermissionTo('عرض مديري التحويلات المالية'))
                                    <li><a href="{{ route('admin.managers.index') }}" class="nav-link">مديرين التحويلات
                                            المالية</a></li>
                                @endif
                                @if (auth()->user()->hasPermissionTo('عرض موظفي التحويلات المالية'))
                                    <li><a href="{{ route('admin.transfer.index') }}" class="nav-link">موظفي التحويلات
                                            المالية</a></li>
                                @endif
                                @if (auth()->user()->hasPermissionTo('عرض مكاتب التحويلات'))
                                    <li><a href="{{ route('admin.transfer.index') }}" class="nav-link">مكاتب
                                            التحويلات</a></li>
                                @endif
                                @if (auth()->user()->hasPermissionTo('عرض التحويلات المالية'))
                                    <li><a class="nav-link" href="{{ route('admin.transfermoney.index') }}">التحويلات
                                            المالية</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif
                    <!-- General Settings -->
                    @if (auth()->user()->hasAnyPermission([
                                'عرض الصفحات الأساسية',
                                'تعديل الإعدادات العامة',
                                'عرض بيانات التواصل',
                                'عرض الدول',
                                'عرض المدن',
                                'عرض الصور المتحركة',
                                'عرض الخدمات',
                                'عرض تواصل معانا',
                                'عرض الأسئلة الشائعة',
                                'عرض طرق الدفع',
                                'عرض أسعار تحويل العملات',
                            ]))
                        <li class="dropdown">
                            <a class="nav-link menu-title" href="javascript:void(0)" data-bs-original-title=""
                                title="">
                                <span style="font-family: 'Cairo', sans-serif;">إعدادات عامة</span>
                                <div class="according-menu"><i class="fa fa-angle-down"></i></div>
                            </a>
                            <ul class="nav-submenu menu-content">
                                @if (auth()->user()->hasPermissionTo('عرض الصفحات الأساسية'))
                                    <li><a class="nav-link" href="{{ route('admin.setting.pages') }}">الصفحات
                                            الأساسية</a></li>
                                @endif
                                @if (auth()->user()->hasPermissionTo('تعديل الإعدادات العامة'))
                                    <li><a class="nav-link" href="{{ route('admin.setting.edit') }}">إعدادات عامة</a>
                                    </li>
                                @endif
                                @if (auth()->user()->hasPermissionTo('عرض بيانات التواصل'))
                                    <li><a class="nav-link" href="{{ route('admin.contactus.index') }}">بيانات
                                            التواصل</a></li>
                                @endif
                                @if (auth()->user()->hasPermissionTo('عرض الدول'))
                                    <li><a class="nav-link" href="{{ route('admin.country.index') }}">الدول</a></li>
                                @endif
                                @if (auth()->user()->hasPermissionTo('عرض المدن'))
                                    <li><a class="nav-link" href="{{ route('admin.region.index') }}">المدن</a></li>
                                @endif
                                @if (auth()->user()->hasPermissionTo('عرض الصور المتحركة'))
                                    <li><a class="nav-link" href="{{ route('admin.slider.index') }}">الصور
                                            المتحركة</a></li>
                                @endif
                                @if (auth()->user()->hasPermissionTo('عرض الخدمات'))
                                    <li><a class="nav-link"
                                            href="{{ route('admin.logisticservice.index') }}">الخدمات</a></li>
                                @endif
                                @if (auth()->user()->hasPermissionTo('عرض تواصل معانا'))
                                    <li><a class="nav-link" href="{{ route('admin.contact.index') }}">تواصل معانا</a>
                                    </li>
                                @endif
                                @if (auth()->user()->hasPermissionTo('عرض الأسئلة الشائعة'))
                                    <li><a class="nav-link" href="{{ route('admin.faq.index') }}">الأسئلة الشائعة</a>
                                    </li>
                                @endif
                                @if (auth()->user()->hasPermissionTo('عرض طرق الدفع'))
                                    <li><a class="nav-link" href="{{ route('admin.payments.index') }}">طرق الدفع</a>
                                    </li>
                                @endif
                                @if (auth()->user()->hasPermissionTo('عرض أسعار تحويل العملات'))
                                    <li><a class="nav-link"
                                            href="{{ route('admin.shipments_currency_conversions.index') }}">أسعار
                                            تحويل العملات</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif
                    <!-- Customer Shipments -->
                    @if (auth()->user()->hasAnyPermission([
                                'عرض إنشاء شحنة',
                                'عرض شحنات زبائن منتظرة التسعير',
                                'عرض شحنات زبائن تم تسعيرها منتظرة التأكيد',
                                'عرض شحنات منتظرة مرجعة الإدارة',
                                'عرض شحنات الزبائن',
                                'عرض شحنات تم تسليمها',
                                'عرض مخازن الزبائن',
                                'عرض مخازن الزبائن الواردة',
                                'عرض مخازن الزبائن الصادرة',
                                'عرض مخزن الوارد المرحل',
                                'عرض محتويات معلقة',
                            ]))
                        <li class="dropdown">
                            <a class="nav-link menu-title" href="javascript:void(0)" data-bs-original-title=""
                                title="">
                                <span style="font-family: 'Cairo', sans-serif;">شحنات الزبائن</span>
                                <div class="according-menu"><i class="fa fa-angle-down"></i></div>
                            </a>
                            <ul class="nav-submenu menu-content">
                                @if (auth()->user()->hasPermissionTo('عرض إنشاء شحنة'))
                                    <li><a class="nav-link" href="{{ route('admin.shipment.create') }}">إنشاء
                                            شحنة</a></li>
                                @endif
                                @if (auth()->user()->hasPermissionTo('عرض شحنات زبائن منتظرة التسعير'))
                                    <li><a class="nav-link" href="{{ route('admin.shipment.n_priced') }}">شحنات زبائن
                                            منتظرة التسعير</a></li>
                                @endif
                                @if (auth()->user()->hasPermissionTo('عرض شحنات زبائن تم تسعيرها منتظرة التأكيد'))
                                    <li><a class="nav-link" href="{{ route('admin.shipment.priced') }}">شحنات زبائن
                                            تم تسعيرها منتظرة التأكيد</a></li>
                                @endif
                                @if (auth()->user()->hasPermissionTo('عرض شحنات منتظرة مرجعة الإدارة'))
                                    <li><a class="nav-link" href="{{ route('admin.shipment.n_active') }}">شحنات
                                            منتظرة مرجعة الإدارة</a></li>
                                @endif
                                @if (auth()->user()->hasPermissionTo('عرض شحنات الزبائن'))
                                    <li><a class="nav-link" href="{{ route('admin.shipment.client') }}">شحنات
                                            الزبائن</a></li>
                                @endif
                                @if (auth()->user()->hasPermissionTo('عرض شحنات تم تسليمها'))
                                    <li><a class="nav-link" href="{{ route('admin.client_recieved') }}">شحنات تم
                                            تسليمها</a></li>
                                @endif
                                @if (auth()->user()->hasPermissionTo('عرض مخازن الزبائن'))
                                    <li><a class="nav-link" href="{{ route('admin.stores.index') }}">مخازن
                                            الزبائن</a></li>
                                @endif
                                @if (auth()->user()->hasPermissionTo('عرض مخازن الزبائن الواردة'))
                                    <li><a class="nav-link" href="{{ route('admin.stores.incoming') }}">مخازن
                                            الزبائن الواردة</a></li>
                                @endif
                                @if (auth()->user()->hasPermissionTo('عرض مخازن الزبائن الصادرة'))
                                    <li><a class="nav-link" href="{{ route('admin.stores.outgoing') }}">مخازن
                                            الزبائن الصادرة</a></li>
                                @endif
                                @if (auth()->user()->hasPermissionTo('عرض مخزن الوارد المرحل'))
                                    <li><a href="{{ route('admin.stores.stageBranch') }}" class="nav-link">مخزن
                                            الوارد المرحل</a></li>
                                @endif
                                @if (auth()->user()->hasPermissionTo('عرض محتويات معلقة'))
                                    <li><a href="{{ route('admin.stores.pendding') }}" class="nav-link">محتويات
                                            معلقة</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif
                    <!-- Merchant Shipments -->
                    @if (auth()->user()->hasAnyPermission([
                                'عرض إنشاء شحنة تاجر',
                                'عرض شحنات التجار منتظرة مرجعة الإدارة',
                                'عرض شحنات التجار',
                                'عرض شحنات معلقة للتاجر',
                                'عرض شحنات تم تسليمها',
                                'عرض مخازن التجار',
                            ]))
                        <li class="dropdown">
                            <a class="nav-link menu-title" href="javascript:void(0)" data-bs-original-title=""
                                title="">
                                <span style="font-family: 'Cairo', sans-serif;">شحنات التجار</span>
                                <div class="according-menu"><i class="fa fa-angle-down"></i></div>
                            </a>
                            <ul class="nav-submenu menu-content">
                                @if (auth()->user()->hasPermissionTo('عرض إنشاء شحنة تاجر'))
                                    <li><a class="nav-link" href="{{ route('admin.merch_create') }}">إنشاء شحنة
                                            تاجر</a></li>
                                @endif
                                @if (auth()->user()->hasPermissionTo('عرض شحنات التجار منتظرة مرجعة الإدارة'))
                                    <li><a class="nav-link" href="{{ route('admin.shipment.n_activeMerch') }}">شحنات
                                            منتظرة مرجعة الإدارة</a></li>
                                @endif
                                @if (auth()->user()->hasPermissionTo('عرض شحنات التجار'))
                                    <li><a class="nav-link"
                                            href="{{ route('admin.shipment.index', ['status' => 1]) }}">شحنات
                                            التجار</a></li>
                                @endif
                                @if (auth()->user()->hasPermissionTo('عرض شحنات معلقة للتاجر'))
                                    <li><a class="nav-link" href="{{ route('admin.shipment.showSusMerch') }}">شحنات
                                            معلقة للتاجر</a></li>
                                @endif
                                @if (auth()->user()->hasPermissionTo('عرض شحنات تم تسليمها'))
                                    <li><a class="nav-link" href="{{ route('admin.merch_recieved') }}">شحنات تم
                                            تسليمها</a></li>
                                @endif
                                @if (auth()->user()->hasPermissionTo('عرض مخازن التجار'))
                                    <li><a class="nav-link" href="{{ route('admin.product.index') }}">مخازن
                                            التجار</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif
                    <!-- Trips and Shipments -->
                    @if (auth()->user()->hasAnyPermission([
                                'عرض رحلات المندوب',
                                'عرض رحلات السائق',
                                'عرض رحلات الوارد المرحل',
                                'عرض الرحلات الصادرة',
                                'عرض الرحلات الواردة',
                                'عرض رحلات الركاب',
                                'عرض أسعار الفروع',
                                'عرض الفروع',
                                'عرض شحنات ملغية',
                                'عرض ملاحظات حالات الشحنات',
                                'عرض شحنات تم تسليمها',
                                'عرض أنواع المركبات',
                                'عرض أرشيف الشحنات',
                            ]))
                        <li class="dropdown">
                            <a class="nav-link menu-title" href="javascript:void(0)" data-bs-original-title=""
                                title="">
                                <span style="font-family: 'Cairo', sans-serif;">الرحلات والشحنات</span>
                                <div class="according-menu"><i class="fa fa-angle-down"></i></div>
                            </a>
                            <ul class="nav-submenu menu-content">
                                @if (auth()->user()->hasPermissionTo('عرض رحلات المندوب'))
                                    <li><a href="{{ route('admin.trips.index') }}" class="nav-link">رحلات المندوب</a>
                                    </li>
                                @endif
                                @if (auth()->user()->hasPermissionTo('عرض رحلات السائق'))
                                    <li><a href="{{ route('admin.driver') }}" class="nav-link">رحلات السائق</a></li>
                                @endif
                                @if (auth()->user()->hasPermissionTo('عرض رحلات الوارد المرحل'))
                                    <li><a href="{{ route('admin.representativeTransfer') }}" class="nav-link">رحلات
                                            الوارد المرحل</a></li>
                                @endif
                                @if (auth()->user()->hasPermissionTo('عرض الرحلات الصادرة'))
                                    <li><a href="{{ route('admin.outgoing') }}" class="nav-link">الرحلات
                                            الصادرة</a></li>
                                @endif
                                @if (auth()->user()->hasPermissionTo('عرض الرحلات الواردة'))
                                    <li><a href="{{ route('admin.ingoing') }}" class="nav-link">الرحلات
                                            الواردة</a></li>
                                @endif
                                @if (auth()->user()->hasPermissionTo('عرض رحلات الركاب'))
                                    <li><a href="{{ route('admin.travel.trips.index') }}" class="nav-link">رحلات
                                            الركاب</a></li>
                                @endif
                                @if (auth()->user()->hasPermissionTo('عرض أرشيف الرحلات'))
                                    <li><a href="{{ route('admin.trips.archive') }}" class="nav-link"> ارشيف الرحلات
                                        </a></li>
                                @endif
                                @if (auth()->user()->hasPermissionTo('عرض أسعار الفروع'))
                                    <li><a class="nav-link" href="{{ route('admin.branch.prices') }}">أسعار
                                            الفروع</a></li>
                                @endif
                                @if (auth()->user()->hasPermissionTo('عرض الفروع'))
                                    <li><a class="nav-link" href="{{ route('admin.branch.index') }}">الفروع</a></li>
                                @endif
                                @if (auth()->user()->hasPermissionTo('عرض شحنات ملغية'))
                                    <li><a class="nav-link" href="{{ route('admin.shipment.clientWait') }}">شحنات
                                            ملغية</a></li>
                                @endif
                                @if (auth()->user()->hasPermissionTo('عرض ملاحظات حالات الشحنات'))
                                    <li><a class="nav-link" href="{{ route('admin.notes.index') }}">ملاحظات حالات
                                            الشحنات</a></li>
                                @endif
                                @if (auth()->user()->hasPermissionTo('عرض شحنات تم تسليمها'))
                                    <li><a class="nav-link" href="{{ route('admin.shipment.recieved') }}">شحنات تم
                                            تسليمها</a></li>
                                @endif
                                @if (auth()->user()->hasPermissionTo('عرض أنواع المركبات'))
                                    <li><a class="nav-link" href="{{ route('admin.vehicles.index') }}">أنواع
                                            المركبات</a></li>
                                @endif
                                @if (auth()->user()->hasPermissionTo('عرض أرشيف الشحنات'))
                                    <li><a class="nav-link" href="{{ route('admin.archived') }}">أرشيف الشحنات</a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif
                </ul>
            </div>
            <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
        </div>
    </nav>
</header>
