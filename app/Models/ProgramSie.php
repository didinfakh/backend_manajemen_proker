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
        'id_koordinator',
    ];

    public $rules = [
        'id_program'     => 'required|integer',
        'sie_name'       => 'required|string|max:255',
        'id_koordinator' => 'nullable|integer',
    ];

    /**
     * Relation to User (Coordinator)
     */
    public function koordinator()
    {
        return $this->belongsTo(User::class, 'id_koordinator', 'id_user');
    }

    /**
     * Relation to Members
     */
    public function members()
    {
        return $this->hasMany(ProgramSieMember::class, 'id_sie', 'id_sie');
    }

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
