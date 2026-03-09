<?php

namespace App\Models;

class SysGroupPermission extends BaseModel
{
    protected $table = 'sys_group_permissions';
    protected $primaryKey = 'id_sys_group_permission';

    protected $fillable = [
        'id_sys_group',
        'id_sys_menu_permission',
        'id_organization',
    ];

    protected $orderDefault = 'id_sys_group_permission desc';

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
