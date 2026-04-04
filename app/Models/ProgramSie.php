<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProgramSie extends BaseModel
{
    use HasFactory;

    protected $table = 'program_sie';
    protected $primaryKey = 'id_sie';

    protected $fillable = [
        'id_program',
        'sie_name',
        'description',
    ];

    public $rules = [
        'id_program' => 'required|integer',
        'sie_name'   => 'required|string|max:255',
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
