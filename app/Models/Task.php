<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Task extends BaseModel
{
    use HasFactory;

    protected $table = 'task';
    protected $primaryKey = 'id_task';

    protected $fillable = [
        'id_program',
        'id_sie',
        'id_organization',
        'id_task_status',
        'title',
        'description',
        'due_date',
        'has_expense',
    ];

    public $rules = [
        'id_program'     => 'required|integer',
        'id_sie'         => 'nullable|integer',
        'id_task_status' => 'required|integer',
        'title'          => 'required|string|max:255',
        'description'    => 'nullable|string',
        'due_date'       => 'nullable|date',
    ];

    /**
     * Relation to TaskStatus
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(TaskStatus::class, 'id_task_status', 'id_task_status');
    }

    /**
     * Relation to Users (Assignees)
     */
    public function assignees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_assignment', 'id_task', 'id_user')
                    ->withPivot('id_task_assignment', 'role', 'assigned_at')
                    ->withTimestamps();
    }

    /**
     * Scope for searching/filtering
     */
    public function scopeSearch(Builder $query, $search)
    {
        if (!$search) return $query;

        foreach ($search as $field => $value) {
            if (!empty($value)) {
                if ($field === 'title' || $field === 'description') {
                    $query->where($field, 'like', '%' . $value . '%');
                } else {
                    $query->where($field, $value);
                }
            }
        }

        return $query;
    }
}
