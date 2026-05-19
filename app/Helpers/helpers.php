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
            'users' => 'users',
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
            'contactus' => 'envelope',
            'faqs' => 'question-circle',
        ];
        return $icons[$module] ?? 'layer-group';
    }
}

if (!function_exists('module_display_name')) {
    function module_display_name($module) {
        $names = [
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
            'contactus' => 'تواصل معنا',
            'admins' => 'المدراء',
            'employees' => 'الموظفين',
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
