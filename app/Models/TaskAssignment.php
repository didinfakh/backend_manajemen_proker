<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaskAssignment extends BaseModel
{
    use HasFactory;

    protected $table = 'task_assignment';
    protected $primaryKey = 'id_task_assignment';

    protected $fillable = [
        'id_task',
        'id_user',
        'role',
        'assigned_at',
        'id_organization',
    ];

    public $rules = [
        'id_task' => 'required|integer',
        'id_user' => 'required|integer',
        'role'    => 'nullable|string|max:100',
    ];
}
