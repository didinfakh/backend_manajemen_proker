<?php

namespace App\Models;

class SysMenu extends BaseModel
{
    protected $table = 'sys_menu';
    protected $primaryKey = 'id_sys_menu';

    protected $fillable = [
        'name',
        'description',
        'id_organization',
        'url',
        'visible',
        'id_menu_parent',
        'menu_order',
        'icon',
    ];

    protected $orderDefault = 'menu_order asc';

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
