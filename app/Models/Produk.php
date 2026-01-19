<?php

namespace App\Models;

class Produk extends BaseModel
{
    protected $table = 'sys_permissions';

    protected $fillable = [
        'id_sys_permission',
        'code',
        'description',
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