<!doctype html>
<html lang="ar" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="rtl"
    data-theme="theme-default" data-assets-path="#"
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
    <!-- Page -->
    <link rel="stylesheet" href="{{ asset('dashboard/assets/vendor/css/pages/page-auth.css') }}" />
</head>
@yield('css')

<body>
    <!-- Content -->
    @yield('content')
    <!-- / Content -->

    <!-- Core JS -->
    @include('Admin.layout.js')
</body>

</html>
