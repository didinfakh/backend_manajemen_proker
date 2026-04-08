<?php

namespace App\Models;

class SysMenuPermission extends BaseModel
{
    protected $table = 'sys_menu_permissions';
    protected $primaryKey = 'id_sys_menu_permission';

    protected $fillable = [
        'id_sys_menu',
        'id_sys_permission',
        'id_organization',
    ];

    public $rules = [
        'id_sys_menu'       => 'required|integer',
        'id_sys_permission' => 'required|integer',
    ];

    protected $orderDefault = 'id_sys_menu_permission desc';

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
