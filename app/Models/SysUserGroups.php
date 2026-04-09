<?php

namespace App\Models;

class SysUserGroups extends BaseModel
{
    protected $table = 'sys_user_groups';
    protected $primaryKey = 'id_sys_user_group';

    protected $fillable = [
        'id_sys_user_group',
        'id_user',
        'id_sys_group',
        'id_organization'
    ];

    public $rules = [
        'id_user'      => 'required|integer',
        'id_sys_group' => 'required|integer',
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

    public function group()
    {
        return $this->belongsTo(SysGroup::class, 'id_sys_group');
    }
}
