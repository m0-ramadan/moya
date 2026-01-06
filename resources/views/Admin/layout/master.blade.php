<!doctype html>
<html lang="ar" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="rtl"
    data-theme="theme-default" data-assets-path="{{ asset('dashboard/assets') }}/"
    data-template="vertical-menu-template-no-customizer">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>
        @yield('title')
    </title>

    <meta name="description" content="" />

    @include('Admin.layout.css')


</head>
@yield('css')

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            @include('Admin.layout.sidebar')
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->
                @include('Admin.layout.nav')
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    @yield('content')

                    <!-- / Content -->

                    <!-- Footer -->
                    @include('Admin.layout.footer')
                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        {{-- <div class="layout-overlay layout-menu-toggle"></div> --}}

        <!-- Drag Target Area To SlideIn Menu On Small Screens -->
        <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->
    <form id="form_action_delete" method="POST" class="d-none">
        @csrf
        <input type="hidden" name="_method" value="DELETE">
    </form>
    <form id="form_action_post" method="POST" class="d-none">
        @csrf
    </form>
    <!-- Core JS -->
    @include('Admin.layout.js')
    @yield('js')
</body>

</html>
