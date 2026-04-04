<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProgramKerja extends BaseModel
{
    use HasFactory;

    protected $table = 'program_kerja';
    protected $primaryKey = 'id_program';

    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'status',
        'progress_percent',
        'id_user_leader',
    ];

    public $rules = [
        'name' => 'required|string|max:255',
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
