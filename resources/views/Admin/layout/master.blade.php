<!doctype html>
<html lang="ar" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="rtl"
    data-theme="theme-default" data-assets-path="{{ asset('dashboard/assets') }}/"
    data-template="vertical-menu-template-no-customizer">

<head>
    <script>
        (function() {
            const templateName = 'vertical-menu-template-no-customizer';
            let style = localStorage.getItem('templateCustomizer-' + templateName + '--Style');
            if (!style || style === 'system') {
                style = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
            document.documentElement.className = style + '-style layout-navbar-fixed layout-menu-fixed layout-compact';
            document.documentElement.setAttribute('data-theme', 'theme-default');
            document.documentElement.setAttribute('data-style', style);
        })();
    </script>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>
        @yield('title')
    </title>

    <meta name="description" content="" />

    @include('Admin.layout.css')
    @include('Admin.layout.app-shell-style')
</head>
@yield('css')

<body class="app-shell">
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
    <script>
        (function() {
            function bindBootstrapPlugin($, pluginName, Constructor) {
                if (!$ || !$.fn || !Constructor || $.fn[pluginName]) {
                    return;
                }

                $.fn[pluginName] = function(config) {
                    return this.each(function() {
                        const instance = Constructor.getOrCreateInstance(
                            this,
                            typeof config === 'object' ? config : undefined
                        );

                        if (typeof config === 'string') {
                            if (typeof instance[config] !== 'function') {
                                throw new Error(`No method named "${config}"`);
                            }

                            instance[config]();
                        }
                    });
                };

                $.fn[pluginName].Constructor = Constructor;
            }

            function rebindDashboardBootstrapJQuery() {
                const $ = window.jQuery;
                const bootstrap = window.bootstrap;

                if (!$ || !bootstrap) {
                    return;
                }

                bindBootstrapPlugin($, 'modal', bootstrap.Modal);
                bindBootstrapPlugin($, 'offcanvas', bootstrap.Offcanvas);
                bindBootstrapPlugin($, 'collapse', bootstrap.Collapse);
                bindBootstrapPlugin($, 'dropdown', bootstrap.Dropdown);
                bindBootstrapPlugin($, 'tab', bootstrap.Tab);
                bindBootstrapPlugin($, 'tooltip', bootstrap.Tooltip);
                bindBootstrapPlugin($, 'popover', bootstrap.Popover);
                bindBootstrapPlugin($, 'toast', bootstrap.Toast);
            }

            rebindDashboardBootstrapJQuery();
            window.rebindDashboardBootstrapJQuery = rebindDashboardBootstrapJQuery;
        })();
    </script>
</body>

</html>
