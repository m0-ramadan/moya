   <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
       <div class="app-brand demo">
           <a href="{{ route('admin.index') }}" class="app-brand-link">
               <span style="width: 75px !important ; height:75px !important" class="app-brand-logo demo"></span>
               <span class="app-brand-text demo menu-text fw-bold"style="font-size: 0.90rem">{{ env('APP_NAME') }}</span>
           </a>

           <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
               <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
               <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
           </a>
       </div>

       <div class="menu-inner-shadow"></div>

       <ul class="menu-inner py-1">

           <li class="menu-item ">
               <a href="{{ route('admin.index') }}" class="menu-link">
                   <i class="menu-icon tf-icons ti ti-home"></i>
                   <div>الرئيسية</div>
               </a>
           </li>

           {{-- <li class="menu-item ">
               <a href="javascript:void(0);" class="menu-link menu-toggle">
                   <i class="menu-icon tf-icons ti ti-world"></i>
                   <div>المناطق</div>

               </a>
               <ul class="menu-sub">

                   <li class="menu-item ">
                       <a href="#/admin/zones/countries" class="menu-link">
                           <div>الدول</div>
                       </a>
                   </li>

                   <li class="menu-item ">
                       <a href="#/admin/zones/governorates" class="menu-link">
                           <div>المحافظات</div>
                       </a>
                   </li>

                   <li class="menu-item ">
                       <a href="#/admin/zones" class="menu-link">
                           <div>المناطق</div>
                       </a>
                   </li>

               </ul>
           </li> --}}
           <li class="menu-item ">
               <a href="javascript:void(0);" class="menu-link menu-toggle">
                   <i class="menu-icon tf-icons ti ti-user-bolt"></i>
                   <div>المستخدمين</div>

               </a>
               <ul class="menu-sub">

                   <li class="menu-item ">
                       <a href="#/admin/admins" class="menu-link">
                           <div>مديرين الموقع</div>
                       </a>
                   </li>

                   <li class="menu-item ">
                       <a href="{{ route('admin.users.index') }}" class="menu-link">
                           <div>العملاء</div>
                       </a>
                   </li>

                                      <li class="menu-item ">
                       <a href="{{ route('admin.drivers.index') }}" class="menu-link">
                           <div> السواقين </div>
                       </a>
                   </li>
               </ul>
           </li>
           <li class="menu-item ">
               <a href="javascript:void(0);" class="menu-link menu-toggle">
                   <i class="menu-icon tf-icons ti ti-shield-lock"></i>
                   <div>الصلاحيات</div>
               </a>
               <ul class="menu-sub">
                   <li class="menu-item ">
                       <a href="{{ route('admin.roles.index') }}" class="menu-link">
                           <div>الرتب</div>
                       </a>
                   </li>

                   <li class="menu-item ">
                       <a href="{{ route('admin.permissions.index') }}" class="menu-link">
                           <div>الصلاحيات</div>
                       </a>
                   </li>

                   <li class="menu-item ">
                       <a href="{{ route('admin.roles.assign.index') }}" class="menu-link">
                           <div>تخصيص الصلاحيات</div>
                       </a>
                   </li>
               </ul>
           </li>

           <li class="menu-item">
               <a href="{{ route('admin.chats.index') }}" class="menu-link">
                   <i class="menu-icon ti ti-messages"></i>
                   <div>الشات</div>
               </a>
           </li>
           <li class="menu-item">
               <a href="{{ route('admin.adminChats.index') }}" class="menu-link">
                   <i class="menu-icon ti ti-message-circle"></i>
                   <div>محادثاتي</div>
               </a>
           </li>

           <li class="menu-item ">
               <a href="{{ route('admin.payment-methods.index') }}" class="menu-link">
                   <i class="menua-icon ti ti-credit-card"></i>
                   <div>طرق الدفع</div>
               </a>
           </li>

           <li class="menu-item">
               <a href="{{ route('admin.products.index') }}" class="menu-link">
                   <i class="menua-icon ti ti-shopping-bag"></i>
                   <div>المنتجات</div>
               </a>
           </li>

           <li class="menu-item">
               <a href="{{ route('admin.categories.index') }}" class="menu-link">
                   <i class="menua-icon ti ti-category"></i>
                   <div>الأقسام</div>
               </a>
           </li>

           <li class="menu-item">
               <a href="{{ route('admin.banners.index') }}" class="menu-link">
                   <i class="menua-icon ti ti-photo"></i>
                   <div>البانرات</div>
               </a>
           </li>

           <li class="menu-item">
               <a href="{{ route('admin.orders.index') }}" class="menu-link">
                   <i class="menu-icon ti ti-package"></i>

                   <div>الطلبات</div>
               </a>
           </li>



           {{-- <li class="menu-item ">
               <a href="#/admin/problems" class="menu-link">
                   <i class="menu-icon ti ti-category-2"></i>
                   <div>المشكلات</div>
               </a>
           </li> --}}

           <li class="menu-item ">
               <a href="{{ route('admin.coupons.index') }}" class="menu-link">
                   <i class="menu-icon ti ti-discount"></i>
                   <div>الكوبونات</div>
               </a>
           </li>
           <li class="menu-item">
               <a href="{{ route('admin.articles.index') }}" class="menu-link">
                   <i class="menu-icon ti ti-article"></i>
                   <div>المقالات</div>
               </a>
           </li>


           <li class="menu-item ">
               <a href="#/admin/contact-us" class="menu-link">
                   <i class="menu-icon ti ti-messages"></i>
                   <div>تواصل معنا</div>
               </a>
           </li>

           {{-- <li class="menu-item ">
               <a href="#/admin/order-reports" class="menu-link">
                   <i class="menu-icon ti ti-message-report"></i>
                   <div>الشكاوي</div>
               </a>
           </li> --}}


           <li class="menu-item ">
               <a href="javascript:void(0);" class="menu-link menu-toggle">
                   <i class="menu-icon tf-icons ti ti-info-circle"></i>
                   <div>عنا</div>

               </a>
               <ul class="menu-sub">

                   <li class="menu-item ">
                       <a href="{{ route('admin.static-pages.index') }}" class="menu-link">
                           <i class="menu-icon ti ti-file-description"></i>
                           <div> الصفحات الثابته
                           </div>
                       </a>
                   </li>
               </ul>
           </li>

           <li class="menu-item ">
               <a href="javascript:void(0);" class="menu-link menu-toggle">
                   <i class="menu-icon tf-icons ti ti-file-pencil "></i>
                   <div>السجلات</div>

               </a>
               <ul class="menu-sub">

                   <li class="menu-item ">
                       <a href="{{ route('admin.errors.index') }}" class="menu-link">
                           <div>الاخطاء البرمجية</div>
                       </a>
                   </li>

               </ul>
           </li>


           <li class="menu-item">
               <a href="javascript:void(0);" class="menu-link menu-toggle">
                   <i class="menu-icon tf-icons ti ti-settings"></i>
                   <div>الإعدادات</div>
               </a>

               <ul class="menu-sub">
                   {{-- <li class="menu-item">
                       <a href="#/admin/settings/languages" class="menu-link">
                           <div>اللغات</div>
                       </a>
                   </li> --}}

                   {{-- <li class="menu-item">
                       <a href="#/admin/settings/api" class="menu-link">
                           <div>واجهة الـ API</div>
                       </a>
                   </li> --}}
                   {{-- 
                   <li class="menu-item">
                       <a href="#/admin/settings/pages/Orders" class="menu-link">
                           <div>الطلبات</div>
                       </a>
                   </li> --}}

                   <li class="menu-item">
                       <a href="#/admin/settings/pages/SMTP" class="menu-link">
                           <div>إعدادات البريد (SMTP)</div>
                       </a>
                   </li>

                   <li class="menu-item">
                       <a href="#/admin/settings/pages/General" class="menu-link">
                           <div>الإعدادات العامة</div>
                       </a>
                   </li>

                   {{-- <li class="menu-item">
                       <a href="#/admin/settings/pages/Payment" class="menu-link">
                           <div>وسائل الدفع</div>
                       </a>
                   </li> --}}

                   <li class="menu-item">
                       <a href="{{ route('admin.social-media.index') }}" class="menu-link">
                           <div>إعدادات التواصل</div>
                       </a>
                   </li>

                   {{-- <li class="menu-item">
                       <a href="#/admin/settings/pages/Realtime" class="menu-link">
                           <div>التحديث اللحظي (Realtime)</div>
                       </a>
                   </li> --}}

                   <li class="menu-item">
                       <a href="#/admin/settings/pages/Files" class="menu-link">
                           <div>إدارة الملفات</div>
                       </a>
                   </li>
               </ul>
           </li>


       </ul>
   </aside>
