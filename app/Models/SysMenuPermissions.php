<?php

namespace App\Models;

class SysMenuPermissions extends BaseModel
{
    protected $table = 'sys_menu_permissions';

    protected $fillable = [
        'id_sys_menu_permission',
        'id_sys_menu',
        'id_sys_permission',
        'id_organization'
    ];

    protected $orderDefault = 'id desc';

    public function scopeSearch($query, $search)
    {
        if (!$search) return $query;

        foreach ($search as $field => $value) {
            if (!empty($value)) {
                $query->where($field, 'like', $value);
            }
        }

        return $query;
    }
}