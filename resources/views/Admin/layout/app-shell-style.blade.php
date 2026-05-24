<style>
    .light-style {
        --app-bg: #faf6f0;
        --app-surface: rgba(255, 255, 255, 0.88);
        --app-surface-strong: #ffffff;
        --app-line: rgba(44, 48, 56, 0.08);
        --app-shadow: 0 15px 35px rgba(44, 48, 56, 0.06);
        --app-nav-height: 86px;
        --app-primary: #f19022;
        --app-primary-2: #fbc875;
        --app-accent: #f19022;
        --app-text: #2c3038;
        --app-muted: #7a828a;
    }

    .dark-style {
        --app-bg: #152241;
        --app-surface: rgba(31, 53, 82, 0.88);
        --app-surface-strong: #1f3552;
        --app-line: rgba(255, 255, 255, 0.08);
        --app-shadow: 0 15px 35px rgba(0, 0, 0, 0.25);
        --app-nav-height: 86px;
        --app-primary: #ee9b00;
        --app-primary-2: #bb3e03;
        --app-accent: #ee9b00;
        --app-text: #ffffff;
        --app-muted: rgba(255, 255, 255, 0.7);
    }

    html,
    body {
        min-height: 100%;
    }

    html.light-style,
    .light-style body {
        background:
            radial-gradient(circle at top right, rgba(241, 144, 34, 0.15), transparent 28%),
            radial-gradient(circle at bottom left, rgba(36, 123, 160, 0.1), transparent 26%),
            linear-gradient(135deg, #fdfbf7 0%, #faf6f0 100%) !important;
        color: var(--app-text) !important;
    }

    html.dark-style,
    .dark-style body {
        background:
            radial-gradient(circle at top right, rgba(187, 62, 3, 0.15), transparent 28%),
            radial-gradient(circle at bottom left, rgba(36, 123, 160, 0.12), transparent 26%),
            linear-gradient(135deg, #152241 0%, #0c1426 100%) !important;
        color: var(--app-text) !important;
    }

    body {
        overscroll-behavior-y: none;
    }

    .layout-wrapper {
        min-height: 100vh;
        background: transparent;
    }

    .layout-page {
        min-height: 100vh;
    }

    .content-wrapper {
        min-height: calc(100vh - 64px);
        padding-bottom: calc(env(safe-area-inset-bottom, 0px) + 18px);
    }

    .app-shell {
        position: relative;
    }

    .light-style .app-shell::before {
        content: "";
        position: fixed;
        inset: 0;
        pointer-events: none;
        background:
            radial-gradient(circle at 85% 10%, rgba(241, 144, 34, 0.06), transparent 25%),
            radial-gradient(circle at 12% 88%, rgba(251, 200, 117, 0.05), transparent 18%);
        z-index: 0;
    }

    .dark-style .app-shell::before {
        content: "";
        position: fixed;
        inset: 0;
        pointer-events: none;
        background:
            radial-gradient(circle at 85% 10%, rgba(238, 155, 0, 0.08), transparent 25%),
            radial-gradient(circle at 12% 88%, rgba(187, 62, 3, 0.08), transparent 18%);
        z-index: 0;
    }

    .layout-container,
    .layout-page,
    .content-wrapper {
        position: relative;
        z-index: 1;
    }

    .layout-navbar {
        margin-top: 14px;
        backdrop-filter: blur(18px);
        box-shadow: var(--app-shadow) !important;
    }

    .light-style .layout-navbar {
        border: 1px solid rgba(22, 35, 52, 0.08) !important;
        background: rgba(255, 255, 255, 0.8) !important;
    }

    .dark-style .layout-navbar {
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        background: rgba(31, 53, 82, 0.8) !important;
    }

    .content-footer {
        background: transparent !important;
        border-top: 0 !important;
        color: var(--app-muted);
        padding-bottom: calc(env(safe-area-inset-bottom, 0px) + 6px);
    }

    .app-splash {
        position: fixed;
        inset: 0;
        z-index: 1085;
        display: grid;
        place-items: center;
        background:
            radial-gradient(circle at top right, rgba(14, 165, 233, 0.2), transparent 28%),
            linear-gradient(145deg, #152241 0%, #253b6c 55%, #0f766e 100%);
        color: #fff;
        transition: opacity .35s ease, visibility .35s ease;
    }

    .app-splash.is-hidden {
        opacity: 0;
        visibility: hidden;
    }

    .app-splash-card {
        text-align: center;
        padding: 28px 30px;
        border-radius: 28px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(12px);
        box-shadow: 0 22px 60px rgba(0, 0, 0, 0.18);
    }

    .app-splash-logo {
        width: 86px;
        height: 86px;
        margin: 0 auto 18px;
        border-radius: 26px;
        display: grid;
        place-items: center;
        font-size: 34px;
        font-weight: 900;
        background: linear-gradient(135deg, #5f63f2, #0ea5e9);
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.12);
    }

    .app-splash h2 {
        margin: 0 0 8px;
        font-size: 28px;
        font-weight: 800;
    }

    .app-splash p {
        margin: 0;
        color: rgba(255, 255, 255, 0.78);
    }

    .app-loader {
        width: 58px;
        height: 58px;
        margin: 22px auto 0;
        border-radius: 50%;
        border: 4px solid rgba(255, 255, 255, 0.18);
        border-top-color: #fff;
        animation: app-spin .85s linear infinite;
    }

    .app-page-loader {
        position: fixed;
        inset: auto 20px calc(env(safe-area-inset-bottom, 0px) + 18px) 20px;
        z-index: 1070;
        display: none;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        border-radius: 18px;
        color: #fff;
        background: rgba(21, 34, 65, 0.92);
        box-shadow: var(--app-shadow);
        backdrop-filter: blur(10px);
    }

    .app-page-loader.is-visible {
        display: flex;
    }

    .app-page-loader .dot {
        width: 12px;
        height: 12px;
        border-radius: 999px;
        background: #5f63f2;
        box-shadow: 18px 0 0 #0ea5e9, 36px 0 0 #f97316;
        animation: app-pulse 1s infinite ease-in-out;
    }

    .app-install-banner {
        position: fixed;
        inset-inline: 14px;
        inset-block-end: calc(env(safe-area-inset-bottom, 0px) + var(--app-nav-height) + 8px);
        z-index: 1069;
        display: none;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 14px 16px;
        border-radius: 18px;
        background: rgba(52, 37, 80, 0.95);
        border: 1px solid rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(14px);
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.35);
        color: #fff;
    }

    @media (min-width: 1200px) {
        .app-install-banner {
            inset-inline: unset;
            inset-inline-start: calc(260px + 1.5rem);
            inset-inline-end: 1.5rem;
            max-width: 1440px;
            margin-inline: auto;
        }
    }

    .app-install-banner.is-visible {
        display: flex;
    }

    .app-install-banner .meta {
        min-width: 0;
    }

    .app-install-banner strong {
        display: block;
        font-size: 14px;
        color: #ffffff;
    }

    .app-install-banner span {
        display: block;
        font-size: 12px;
        color: rgba(255, 255, 255, 0.78);
    }

    .app-install-banner .actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .app-install-banner #appInstallDismiss {
        background: rgba(255, 255, 255, 0.15) !important;
        color: #fff !important;
        border: 0 !important;
    }

    .app-install-banner #appInstallDismiss:hover {
        background: rgba(255, 255, 255, 0.25) !important;
    }

    .mobile-bottom-nav {
        position: fixed;
        inset-inline: 10px;
        inset-block-end: calc(env(safe-area-inset-bottom, 0px) + 10px);
        z-index: 1068;
        display: none;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 10px;
        border-radius: 24px;
        border: 1px solid rgba(255, 255, 255, 0.65);
        background: rgba(255, 255, 255, 0.86);
        backdrop-filter: blur(18px);
        box-shadow: 0 22px 42px rgba(15, 23, 42, 0.16);
    }

    .mobile-bottom-nav__item {
        flex: 1;
        min-width: 0;
        text-decoration: none;
        color: var(--app-muted);
        display: grid;
        place-items: center;
        gap: 4px;
        border-radius: 18px;
        padding: 10px 6px 8px;
        transition: transform .2s ease, background-color .2s ease, color .2s ease;
    }

    .mobile-bottom-nav__item i {
        font-size: 22px;
        line-height: 1;
    }

    .mobile-bottom-nav__item span {
        font-size: 11px;
        font-weight: 800;
        white-space: nowrap;
    }

    .mobile-bottom-nav__item.is-active {
        color: #fff;
        background: linear-gradient(135deg, var(--app-primary), var(--app-primary-2));
        box-shadow: 0 12px 24px rgba(95, 99, 242, 0.22);
    }

    .mobile-bottom-nav__item:active {
        transform: translateY(1px) scale(0.98);
    }

    .mobile-safe-bottom {
        display: none;
    }

    .app-file-shell {
        display: grid;
        gap: 10px;
    }

    .app-file-trigger {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        min-height: 54px;
        padding: 12px 14px;
        border-radius: 16px;
        border: 1px dashed rgba(95, 99, 242, 0.28);
        background: rgba(95, 99, 242, 0.05);
        font-weight: 700;
        color: var(--app-text);
        cursor: pointer;
    }

    .app-file-trigger small {
        color: var(--app-muted);
        font-weight: 600;
    }

    .app-file-inline-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .app-file-inline-actions .btn {
        border-radius: 14px;
    }

    .app-file-name {
        font-size: 12px;
        color: var(--app-muted);
        min-height: 18px;
    }

    @media (max-width: 1199.98px) {
        .layout-navbar {
            margin-top: 10px;
        }
    }

    @media (max-width: 991.98px) {
        .layout-page {
            padding-bottom: calc(var(--app-nav-height) + env(safe-area-inset-bottom, 0px));
        }

        .content-wrapper {
            padding-bottom: calc(var(--app-nav-height) + env(safe-area-inset-bottom, 0px) + 14px);
        }

        .mobile-bottom-nav,
        .mobile-safe-bottom {
            display: flex;
        }

        .content-footer {
            padding-bottom: calc(var(--app-nav-height) + env(safe-area-inset-bottom, 0px));
        }
    }

    @media (max-width: 767.98px) {
        .layout-navbar {
            margin-top: 0;
            border-radius: 0 0 20px 20px;
            padding-top: max(10px, env(safe-area-inset-top, 0px));
        }

        .layout-page {
            padding-bottom: calc(var(--app-nav-height) + env(safe-area-inset-bottom, 0px));
        }

        .app-splash-card {
            width: min(88vw, 360px);
        }

        .app-install-banner {
            inset-inline: 10px;
        }
    }

    @keyframes app-spin {
        to {
            transform: rotate(360deg);
        }
    }

    @keyframes app-pulse {

        0%,
        100% {
            transform: translateX(0);
            opacity: 1;
        }

        50% {
            transform: translateX(6px);
            opacity: .72;
        }
    }

    /* ERP Style Theme Overrides to Match Auth Pages */
    
    /* Primary buttons */
    .btn-primary {
        background: linear-gradient(135deg, #f19022, #fbc875) !important;
        border: 0 !important;
        box-shadow: 0 4px 14px rgba(241, 144, 34, 0.25) !important;
        color: #fff !important;
        transition: transform 0.2s ease, opacity 0.2s ease, box-shadow 0.2s ease !important;
    }
    .btn-primary:hover, .btn-primary:active, .btn-primary:focus {
        opacity: 0.95 !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 6px 20px rgba(241, 144, 34, 0.35) !important;
        background: linear-gradient(135deg, #d8760e, #f19022) !important;
        color: #fff !important;
    }
    .btn-primary:active {
        transform: translateY(1px) !important;
    }
    
    .dark-style .btn-primary {
        background: linear-gradient(135deg, #ee9b00, #bb3e03) !important;
        box-shadow: 0 4px 14px rgba(238, 155, 0, 0.25) !important;
    }
    .dark-style .btn-primary:hover, .dark-style .btn-primary:active, .dark-style .btn-primary:focus {
        background: linear-gradient(135deg, #ee9b00, #bb3e03) !important;
        box-shadow: 0 6px 20px rgba(238, 155, 0, 0.35) !important;
    }

    /* Secondary and outline buttons */
    .btn-outline-primary {
        color: #f19022 !important;
        border-color: #f19022 !important;
        background: transparent !important;
    }
    .btn-outline-primary:hover {
        background: rgba(241, 144, 34, 0.08) !important;
        color: #f19022 !important;
        border-color: #f19022 !important;
    }
    .dark-style .btn-outline-primary {
        color: #ee9b00 !important;
        border-color: #ee9b00 !important;
    }
    .dark-style .btn-outline-primary:hover {
        background: rgba(238, 155, 0, 0.08) !important;
        color: #ee9b00 !important;
        border-color: #ee9b00 !important;
    }
    
    /* Text primary */
    .text-primary {
        color: #f19022 !important;
    }
    .dark-style .text-primary {
        color: #ee9b00 !important;
    }
    
    /* Labels */
    .bg-label-primary {
        background-color: rgba(241, 144, 34, 0.12) !important;
        color: #f19022 !important;
    }
    .dark-style .bg-label-primary {
        background-color: rgba(238, 155, 0, 0.15) !important;
        color: #ee9b00 !important;
    }

    /* Cards and panels */
    .card {
        background-color: var(--app-surface) !important;
        backdrop-filter: blur(12px);
        border: 1px solid var(--app-line) !important;
        box-shadow: var(--app-shadow) !important;
    }
    
    /* Sidebar / Menu background & active items */
    .light-style .bg-menu-theme {
        background-color: rgba(255, 255, 255, 0.85) !important;
        backdrop-filter: blur(15px);
        border-inline-end: 1px solid rgba(44, 48, 56, 0.08) !important;
    }
    .dark-style .bg-menu-theme {
        background-color: rgba(25, 38, 66, 0.85) !important;
        backdrop-filter: blur(15px);
        border-inline-end: 1px solid rgba(255, 255, 255, 0.08) !important;
    }
    .bg-menu-theme .menu-item.active > .menu-link:not(.menu-toggle) {
        background: linear-gradient(135deg, #f19022, #fbc875) !important;
        color: #fff !important;
        box-shadow: 0 8px 20px rgba(241, 144, 34, 0.2) !important;
    }
    .dark-style .bg-menu-theme .menu-item.active > .menu-link:not(.menu-toggle) {
        background: linear-gradient(135deg, #ee9b00, #bb3e03) !important;
        box-shadow: 0 8px 20px rgba(238, 155, 0, 0.25) !important;
    }
    
    /* Active menu item icon */
    .menu-vertical .menu-item.active > .menu-link:not(.menu-toggle) i {
        color: #fff !important;
    }
    
    /* Forms and inputs */
    .form-control, .form-select {
        border-color: var(--app-line) !important;
        color: var(--app-text) !important;
    }
    .light-style .form-control, .light-style .form-select {
        background-color: rgba(255, 255, 255, 0.8) !important;
    }
    .dark-style .form-control, .dark-style .form-select {
        background-color: rgba(255, 255, 255, 0.08) !important;
    }
    .form-control:focus, .form-select:focus {
        border-color: rgba(241, 144, 34, 0.5) !important;
        box-shadow: 0 0 0 3px rgba(241, 144, 34, 0.15) !important;
    }
    .dark-style .form-control:focus, .dark-style .form-select:focus {
        border-color: rgba(238, 155, 0, 0.5) !important;
        box-shadow: 0 0 0 3px rgba(238, 155, 0, 0.15) !important;
    }
    
    /* Splash screen loader matching auth screen */
    .app-splash {
        background:
            radial-gradient(circle at top right, rgba(241, 144, 34, 0.15), transparent 28%),
            radial-gradient(circle at bottom left, rgba(36, 123, 160, 0.1), transparent 26%),
            linear-gradient(135deg, #fdfbf7 0%, #faf6f0 100%) !important;
    }
    .dark-style .app-splash {
        background:
            radial-gradient(circle at top right, rgba(187, 62, 3, 0.15), transparent 28%),
            radial-gradient(circle at bottom left, rgba(36, 123, 160, 0.12), transparent 26%),
            linear-gradient(135deg, #152241 0%, #0c1426 100%) !important;
    }
    
    .app-splash-logo {
        background: linear-gradient(135deg, #f19022, #fbc875) !important;
        box-shadow: 0 14px 30px rgba(241, 144, 34, 0.25) !important;
    }
    .dark-style .app-splash-logo {
        background: linear-gradient(135deg, #ee9b00, #bb3e03) !important;
        box-shadow: 0 14px 30px rgba(238, 155, 0, 0.25) !important;
    }

    /* Page list active pagination */
    .page-item.active .page-link {
        background: linear-gradient(135deg, #f19022, #fbc875) !important;
        border-color: transparent !important;
        box-shadow: 0 4px 10px rgba(241, 144, 34, 0.2) !important;
        color: #fff !important;
    }
    .dark-style .page-item.active .page-link {
        background: linear-gradient(135deg, #ee9b00, #bb3e03) !important;
        box-shadow: 0 4px 10px rgba(238, 155, 0, 0.25) !important;
    }

    /* Nav pills active list items */
    .nav-pills .nav-link.active {
        background: linear-gradient(135deg, #f19022, #fbc875) !important;
        color: #fff !important;
        box-shadow: 0 4px 10px rgba(241, 144, 34, 0.2) !important;
    }
    .dark-style .nav-pills .nav-link.active {
        background: linear-gradient(135deg, #ee9b00, #bb3e03) !important;
        box-shadow: 0 4px 10px rgba(238, 155, 0, 0.25) !important;
    }

    /* Theme toggle header element */
    .theme-toggle-btn {
        transition: transform 0.2s ease, opacity 0.2s ease !important;
    }
    .theme-toggle-btn:hover {
        transform: scale(1.1) rotate(12deg);
        color: #f19022 !important;
    }
    .dark-style .theme-toggle-btn:hover {
        color: #ee9b00 !important;
    }

    /* QW UI Component Styles Overrides for System-wide Themes */
    .qw-card,
    .qw-stat,
    .qw-quick-action,
    .qw-detail {
        background: var(--app-surface) !important;
        background-color: var(--app-surface) !important;
        border: 1px solid var(--app-line) !important;
        box-shadow: var(--app-shadow) !important;
        color: var(--app-text) !important;
    }

    .light-style .qw-hero {
        background: linear-gradient(135deg, #f19022 0%, #fbc875 100%) !important;
        box-shadow: 0 22px 50px rgba(241, 144, 34, .2) !important;
    }
    .dark-style .qw-hero {
        background: linear-gradient(135deg, #ee9b00 0%, #bb3e03 100%) !important;
        box-shadow: 0 22px 50px rgba(238, 155, 0, .25) !important;
    }

    .qw-section-title i,
    .qw-quick-action i {
        background: var(--app-primary) !important;
    }
    .light-style .qw-section-title i,
    .light-style .qw-quick-action i {
        background: linear-gradient(135deg, #f19022, #fbc875) !important;
    }
    .dark-style .qw-section-title i,
    .dark-style .qw-quick-action i {
        background: linear-gradient(135deg, #ee9b00, #bb3e03) !important;
    }

    .qw-table thead th {
        background: rgba(241, 144, 34, 0.08) !important;
        color: var(--app-text) !important;
    }
    .dark-style .qw-table thead th {
        background: rgba(238, 155, 0, 0.1) !important;
    }

    .qw-table tbody td {
        color: var(--app-text) !important;
    }

    .qw-section-title {
        color: var(--app-text) !important;
    }

    .qw-stat .number {
        color: var(--app-text) !important;
    }
    .qw-stat .label {
        color: var(--app-muted) !important;
    }
    .qw-stat::before {
        background: var(--app-primary) !important;
    }
    .light-style .qw-stat::before {
        background: linear-gradient(180deg, #f19022, #fbc875) !important;
    }
    .dark-style .qw-stat::before {
        background: linear-gradient(180deg, #ee9b00, #bb3e03) !important;
    }

    .qw-empty {
        color: var(--app-muted) !important;
    }

    .qw-detail .label {
        color: var(--app-muted) !important;
    }
    .qw-detail .value {
        color: var(--app-text) !important;
    }
    .qw-detail {
        border-color: var(--app-line) !important;
    }

    .qw-label {
        color: var(--app-text) !important;
    }

    .qw-pagination .page-link {
        background: rgba(241, 144, 34, 0.08) !important;
        color: #f19022 !important;
    }
    .dark-style .qw-pagination .page-link {
        background: rgba(238, 155, 0, 0.1) !important;
        color: #ee9b00 !important;
    }
    .qw-pagination .page-item.active .page-link {
        background: linear-gradient(135deg, #f19022, #fbc875) !important;
        color: #fff !important;
    }
    .dark-style .qw-pagination .page-item.active .page-link {
        background: linear-gradient(135deg, #ee9b00, #bb3e03) !important;
    }

    /* Robust Sidebar / Menu text color overrides */
    .light-style .bg-menu-theme .menu-link {
        color: #2c3038 !important;
    }
    .light-style .bg-menu-theme .menu-item:not(.active) > .menu-link:hover,
    .light-style .bg-menu-theme .menu-item.open > .menu-link {
        background-color: rgba(241, 144, 34, 0.08) !important;
        color: #f19022 !important;
    }
    .light-style .bg-menu-theme .menu-item.open > .menu-link i,
    .light-style .bg-menu-theme .menu-item:not(.active) > .menu-link:hover i {
        color: #f19022 !important;
    }
    .light-style .bg-menu-theme .menu-link i,
    .light-style .bg-menu-theme .menu-inner-shadow {
        color: #7a828a !important;
    }
    .light-style .bg-menu-theme .menu-header {
        color: #7a828a !important;
    }

    .dark-style .bg-menu-theme .menu-link {
        color: rgba(255, 255, 255, 0.8) !important;
    }
    .dark-style .bg-menu-theme .menu-item:not(.active) > .menu-link:hover,
    .dark-style .bg-menu-theme .menu-item.open > .menu-link {
        background-color: rgba(255, 255, 255, 0.08) !important;
        color: #ee9b00 !important;
    }
    .dark-style .bg-menu-theme .menu-item.open > .menu-link i,
    .dark-style .bg-menu-theme .menu-item:not(.active) > .menu-link:hover i {
        color: #ee9b00 !important;
    }
    .dark-style .bg-menu-theme .menu-link i {
        color: rgba(255, 255, 255, 0.6) !important;
    }
    .dark-style .bg-menu-theme .menu-header {
        color: rgba(255, 255, 255, 0.5) !important;
    }
</style>
