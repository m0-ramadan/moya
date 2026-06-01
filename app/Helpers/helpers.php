<?php

if (!function_exists('get_user_image')) {
    /**
     * إرجاع رابط الصورة الصحيح للمستخدم
     *
     * @param string|null $image
     * @return string
     */
    function get_user_image(?string $image): string
    {
        if (!$image) {
            return config('app.default_user_image', "https://static.vecteezy.com/system/resources/previews/011/209/565/non_2x/user-profile-avatar-free-vector.jpg"); // لا توجد صورة
        }

        // إذا الرابط موجود بالفعل كـ https أو http
        if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
            return $image;
        }
        // الرابط نسبي في قاعدة البيانات
        return asset('storage/' . $image);
    }
}



if (!function_exists('formatBytes')) {
    function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}


if (!function_exists('parsePHPLog')) {
    function parsePHPLog($content)
    {
        $lines = explode("\n", $content);
        $errors = [];
        $currentError = null;
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            // Check for new error entry (PHP format)
            if (preg_match('/^\[(.*?)\] (PHP )?(.*?): (.*)$/', $line, $matches)) {
                if ($currentError) {
                    $errors[] = $currentError;
                }
                
                $currentError = [
                    'timestamp' => $matches[1] ?? '',
                    'level' => $matches[3] ?? '',
                    'message' => $matches[4] ?? '',
                    'file' => '',
                    'line' => '',
                    'stack' => ''
                ];
            }
            // Check for Laravel format
            elseif (preg_match('/^(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}) (.*?): (.*)$/', $line, $matches)) {
                if ($currentError) {
                    $errors[] = $currentError;
                }
                
                $currentError = [
                    'timestamp' => $matches[1] ?? '',
                    'level' => $matches[2] ?? '',
                    'message' => $matches[3] ?? '',
                    'file' => '',
                    'line' => '',
                    'stack' => ''
                ];
            }
            // Stack trace or file info
            elseif ($currentError) {
                if (str_contains($line, 'Stack trace:')) {
                    $currentError['stack'] = $line;
                }
                elseif (preg_match('/ in (.*?) on line (\d+)/', $line, $matches)) {
                    $currentError['file'] = $matches[1] ?? '';
                    $currentError['line'] = $matches[2] ?? '';
                }
                elseif (str_starts_with($line, '#') && !empty($currentError['stack'])) {
                    $currentError['stack'] .= "\n" . $line;
                }
            }
        }
        
        if ($currentError) {
            $errors[] = $currentError;
        }
        
        return $errors;
    }
}

if (!function_exists('module_icon')) {
    function module_icon($module) {
        $icons = [
            'dashboard' => 'gauge-high',
            'users' => 'users',
            'admins' => 'user-shield',
            'roles' => 'shield-alt',
            'permissions' => 'key',
            'settings' => 'cog',
            'orders' => 'shopping-cart',
            'products' => 'box',
            'categories' => 'tags',
            'drivers' => 'car',
            'banners' => 'image',
            'contracts' => 'file-contract',
            'chats' => 'comments',
            'services' => 'concierge-bell',
            'reports' => 'chart-bar',
            'contact_us' => 'envelope',
            'articles' => 'newspaper',
            'faqs' => 'question-circle',
            'countries' => 'flag',
            'employees' => 'id-badge',
            'managers' => 'user-tie',
            'regions' => 'map',
            'visitors' => 'chart-line',
            'payments' => 'money-check-dollar',
            'payment_methods' => 'credit-card',
            'operations' => 'receipt',
            'subscriptions' => 'bell',
            'static_pages' => 'file-lines',
            'errors' => 'bug',
            'logistic_services' => 'truck-fast',
        ];
        return $icons[$module] ?? 'layer-group';
    }
}

if (!function_exists('module_display_name')) {
    function module_display_name($module) {
        $names = [
            'dashboard' => 'لوحة التحكم',
            'users' => 'المستخدمين',
            'roles' => 'الرتب',
            'permissions' => 'الصلاحيات',
            'settings' => 'الإعدادات',
            'orders' => 'الطلبات',
            'products' => 'المنتجات',
            'categories' => 'الأقسام',
            'drivers' => 'السائقين',
            'banners' => 'البنرات',
            'contracts' => 'العقود',
            'chats' => 'المحادثات',
            'services' => 'الخدمات',
            'reports' => 'التقارير',
            'faqs' => 'الأسئلة الشائعة',
            'contact_us' => 'تواصل معنا',
            'admins' => 'المدراء',
            'employees' => 'الموظفين',
            'articles' => 'المقالات',
            'countries' => 'الدول',
            'managers' => 'المديرين',
            'regions' => 'المناطق',
            'visitors' => 'الزوار',
            'payments' => 'المدفوعات',
            'payment_methods' => 'طرق الدفع',
            'operations' => 'العمليات',
            'subscriptions' => 'الاشتراكات',
            'static_pages' => 'الصفحات الثابتة',
            'errors' => 'الأخطاء',
            'logistic_services' => 'الخدمات اللوجستية',
        ];
        return $names[$module] ?? ucfirst($module);
    }
}

if (!function_exists('permission_type')) {
    function permission_type($permissionName) {
        $parts = explode('-', $permissionName);
        if (count($parts) === 1) {
            $parts = explode('_', $permissionName);
        }
        return $parts[0] ?? $permissionName;
    }
}

if (!function_exists('permission_type_label')) {
    function permission_type_label($permissionName) {
        $type = permission_type($permissionName);
        $labels = [
            'create' => 'إضافة',
            'read' => 'عرض',
            'view' => 'عرض',
            'update' => 'تعديل',
            'edit' => 'تعديل',
            'delete' => 'حذف',
            'manage' => 'إدارة',
            'destroy' => 'حذف',
        ];
        return $labels[$type] ?? ucfirst($type);
    }
}

if (!function_exists('permission_badge_class')) {
    function permission_badge_class($permissionName) {
        $type = permission_type($permissionName);
        $classes = [
            'create' => 'badge-create',
            'read' => 'badge-read',
            'view' => 'badge-read',
            'update' => 'badge-update',
            'edit' => 'badge-update',
            'delete' => 'badge-delete',
            'destroy' => 'badge-delete',
            'manage' => 'badge-manage',
        ];
        return $classes[$type] ?? 'badge-secondary';
    }
}

if (!function_exists('dashboard_permission_modules')) {
    function dashboard_permission_modules(): array
    {
        return [
            'dashboard' => 'لوحة التحكم',
            'users' => 'المستخدمين',
            'admins' => 'المشرفين',
            'roles' => 'الرتب',
            'permissions' => 'الصلاحيات',
            'products' => 'المنتجات',
            'categories' => 'الأقسام',
            'orders' => 'الطلبات',
            'banners' => 'البانرات',
            'coupons' => 'الكوبونات',
            'settings' => 'الإعدادات',
            'reports' => 'التقارير',
            'payment_methods' => 'طرق الدفع',
            'contact_us' => 'تواصل معنا',
            'about' => 'عن الموقع',
            'articles' => 'المقالات',
            'faqs' => 'الأسئلة الشائعة',
            'static_pages' => 'الصفحات الثابتة',
            'visitors' => 'الزوار',
            'chats' => 'المحادثات',
            'contracts' => 'العقود',
            'payments' => 'المدفوعات',
            'operations' => 'العمليات',
            'services' => 'الخدمات',
            'drivers' => 'السائقين',
            'countries' => 'الدول',
            'employees' => 'الموظفين',
            'managers' => 'المديرين',
            'regions' => 'المناطق',
            'logistic_services' => 'الخدمات اللوجستية',
            'subscriptions' => 'الاشتراكات',
            'errors' => 'الأخطاء',
        ];
    }
}

if (!function_exists('dashboard_route_permission_map')) {
    function dashboard_route_permission_map(): array
    {
        return [
            'admin.home' => 'dashboard',
            'admin.index' => 'admins',
            'admin.admins.' => 'admins',
            'admin.users.' => 'users',
            'admin.roles.' => 'roles',
            'admin.permissions.' => 'permissions',
            'admin.countries.' => 'countries',
            'admin.contactus.' => 'contact_us',
            'admin.contact.' => 'contact_us',
            'admin.faqs.' => 'faqs',
            'admin.logistic-services.' => 'logistic_services',
            'admin.employees.' => 'employees',
            'admin.managers.' => 'managers',
            'admin.regions.' => 'regions',
            'admin.setting.' => 'settings',
            'admin.social-media.' => 'settings',
            'admin.subscribe.' => 'subscriptions',
            'admin.payment-methods.' => 'payment_methods',
            'admin.banners.' => 'banners',
            'admin.orders.' => 'orders',
            'admin.operations.' => 'operations',
            'admin.articles.' => 'articles',
            'admin.coupons.' => 'coupons',
            'admin.contracts.' => 'contracts',
            'admin.payments.' => 'payments',
            'admin.static-pages.' => 'static_pages',
            'admin.chats.' => 'chats',
            'admin.adminChats.' => 'chats',
            'admin.drivers.map.' => 'drivers',
            'admin.drivers.' => 'drivers',
            'admin.services.' => 'services',
            'admin.visitors.' => 'visitors',
            'admin.errors.' => 'errors',
        ];
    }
}

if (!function_exists('dashboard_route_module')) {
    function dashboard_route_module(?string $routeName): ?string
    {
        if (!$routeName) {
            return null;
        }

        foreach (dashboard_route_permission_map() as $prefix => $module) {
            if ($routeName === $prefix || str_starts_with($routeName, $prefix)) {
                return $module;
            }
        }

        return null;
    }
}

if (!function_exists('dashboard_route_action')) {
    function dashboard_route_action(?string $routeName): ?string
    {
        if (!$routeName) {
            return null;
        }

        if (preg_match('/^admin\.roles\.(assign|permissions)(\.|$)/', $routeName)) {
            return 'manage';
        }

        if (preg_match('/\.(destroy|delete)(\.|$)/', $routeName)) {
            return 'delete';
        }

        if (preg_match('/\.(create|store)(\.|$)/', $routeName)) {
            return 'create';
        }

        if (preg_match('/\.(edit|update|toggle-status|toggle-featured|toggle-maintenance|approve|reject|assign|bulk|mark|read|send|sync|extend|refund|clear|upload|remove|process|change|reset|duplicate|validate|generate)(\.|$)/', $routeName)) {
            return 'edit';
        }

        if (preg_match('/\.(permissions|tracking|wallet|statistics|stats|chart|show|index|export|print|receipt|details|locations|orders|payments|devices|search|filter|check-number|generate-number)(\.|$)/', $routeName)) {
            return 'view';
        }

        return 'manage';
    }
}

if (!function_exists('dashboard_permission_candidates')) {
    function dashboard_permission_candidates(string $module, string $action = 'view'): array
    {
        return match ($action) {
            'create' => ["{$module}.create", "{$module}.manage"],
            'edit' => ["{$module}.edit", "{$module}.manage"],
            'delete' => ["{$module}.delete", "{$module}.manage"],
            'manage' => ["{$module}.manage"],
            default => ["{$module}.view", "{$module}.manage"],
        };
    }
}

if (!function_exists('admin_can_access_module')) {
    function admin_can_access_module(string $module, string $action = 'view', $admin = null): bool
    {
        $admin = $admin ?: auth('admin')->user();

        if (!$admin) {
            return false;
        }

        if (method_exists($admin, 'hasRole') && $admin->hasRole('super_admin')) {
            return true;
        }

        return $admin->hasAnyPermission(dashboard_permission_candidates($module, $action));
    }
}

if (!function_exists('admin_can_access_any_module')) {
    function admin_can_access_any_module(array $modules, string $action = 'view', $admin = null): bool
    {
        foreach ($modules as $module) {
            if (admin_can_access_module($module, $action, $admin)) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('admin_can_access_route')) {
    function admin_can_access_route(?string $routeName, $admin = null): bool
    {
        $admin = $admin ?: auth('admin')->user();

        if (!$admin || !$routeName) {
            return false;
        }

        if ($admin->hasRole('super_admin')) {
            return true;
        }

        if ($routeName === 'admin.logout') {
            return true;
        }

        $module = dashboard_route_module($routeName);

        if (!$module) {
            return true;
        }

        return admin_can_access_module($module, dashboard_route_action($routeName), $admin);
    }
}
