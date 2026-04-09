<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProgramSieMember extends BaseModel
{
    use HasFactory;

    protected $table = 'program_sie_member';
    protected $primaryKey = 'id_sie_member';

    protected $fillable = [
        'id_sie',
        'id_user',
        'role',
    ];

    public $rules = [
        'id_sie'  => 'required|integer',
        'id_user' => 'required|integer',
    ];

    public function sie()
    {
        return $this->belongsTo(ProgramSie::class, 'id_sie', 'id_sie');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
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
