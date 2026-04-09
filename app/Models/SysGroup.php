<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Validation\Rule;

class SysGroup extends BaseModel
{
    use HasFactory;

    protected $table = 'sys_groups';
    protected $primaryKey = 'id_sys_group';

    protected $fillable = [
        'name',
        'description',
        'id_organization',
    ];

    public $rules = [
        'name'        => 'required|string|max:100',
        'description' => 'nullable|string',
    ];

    protected $orderDefault = 'id_sys_group desc';

    public function getStoreRules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique($this->table, 'name')->where(
                    fn($query) => $query->where('id_organization', static::getOrganizationId())
                ),
            ],
            'description' => ['nullable', 'string'],
        ];
    }

    public function getUpdateRules(int $id): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique($this->table, 'name')
                    ->ignore($id, $this->primaryKey)
                    ->where(
                        fn($query) => $query->where('id_organization', static::getOrganizationId())
                    ),
            ],
            'description' => ['nullable', 'string'],
        ];
    }

    public function scopeSearch($query, $search)
    {
        if (!$search) return $query;

        foreach ($search as $field => $value) {
            if (empty($value)) {
                continue;
            }

            if ($field === 'id_sys_group') {
                $query->where($field, $value);
                continue;
            }

            if (in_array($field, ['name', 'description'], true)) {
                $keyword = str_contains($value, '%') ? $value : '%' . $value . '%';
                $query->where($field, 'like', $keyword);
            }
        }

        return $query;
    }

    public function userGroups()
    {
        return $this->hasMany(SysUserGroups::class, 'id_sys_group', 'id_sys_group');
    }

    public function permissions()
    {
        return $this->hasMany(SysGroupPermission::class, 'id_sys_group', 'id_sys_group');
    }
}
