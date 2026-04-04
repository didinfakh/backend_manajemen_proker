<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class TaskStatus extends BaseModel
{
    use HasFactory;

    protected $table = 'task_status';
    protected $primaryKey = 'id_task_status';

    protected $fillable = [
        'id_program',
        'id_sie',
        'id_organization',
        'kode',
        'nama',
    ];

    public $rules = [
        'id_program' => 'required|integer',
        'id_sie'     => 'nullable|integer',
        'nama'       => 'required|string|max:100',
        'kode'       => 'nullable|string|max:50',
    ];

    /**
     * Scope for searching/filtering
     */
    public function scopeSearch(Builder $query, $search)
    {
        if (!$search) return $query;

        foreach ($search as $field => $value) {
            if (!empty($value)) {
                if ($field === 'nama' || $field === 'kode') {
                    $query->where($field, 'like', '%' . $value . '%');
                } else {
                    $query->where($field, $value);
                }
            }
        }

        return $query;
    }
}
