<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRoles;

    protected $guard_name = 'admin'; // مهم جداً لـ Spatie

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function isSuperAdmin()
    {
        return $this->hasRole('super_admin');
    }

    /**
     * الحصول على الأدوار المخصصة
     */
    public function getCustomRolesAttribute()
    {
        return $this->roles->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'display_name' => $role->display_name,
                'description' => $role->description
            ];
        });
    }

    /**
     * الحصول على جميع الصلاحيات (بما في ذلك تلك من الأدوار)
     */
    public function getAllPermissionsAttribute()
    {
        return $this->getAllPermissions()->map(function ($permission) {
            return [
                'id' => $permission->id,
                'name' => $permission->name,
                'display_name' => $permission->display_name,
                'module' => $permission->module
            ];
        });
    }
}
