<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class SysGroup extends BaseModel
{
    use HasFactory;

    protected $table = 'sys_groups';
    protected $primaryKey = 'id_sys_group';

    protected $fillable = [
        'name',
        'description',
    ];

    public $rules = [
        'name'        => 'required|string|max:100',
        'description' => 'nullable|string',
    ];

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
