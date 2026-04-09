<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    /**
     * Boot model (auto set organization on create)
     */
    protected static function booted(): void
    {
        static::addGlobalScope('organization', function ($builder) {
            if (app()->bound('id_organization')) {
                $builder->where('id_organization', (int) app('id_organization'));
            }
        });

        static::creating(function ($model) {
            if (empty($model->id_organization)) {
                $model->id_organization = app()->bound('id_organization')
                    ? (int) app('id_organization')
                    : null;
            }
        });
    }

    protected $primaryKey = 'id_user';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'telegram_number',
        'id_organization',
    ];

    public $rules = [
        'name'            => 'required|string|max:255',
        'email'           => 'required|email|max:255|unique:users,email',
        'password'        => 'required|string|min:8',
        'telegram_number' => 'nullable|string|max:20',
    ];

    public function getUpdateRules($id)
    {
        $rules = $this->rules;
        $rules['email'] = 'required|email|max:255|unique:users,email,' . $id . ',id_user';
        $rules['password'] = 'nullable|string|min:8';
        return $rules;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected $appends = ['id_sys_group'];

    public function groups()
    {
        return $this->hasMany(SysUserGroups::class, 'id_user', 'id_user');
    }

    public function getIdSysGroupAttribute()
    {
        if ($this->relationLoaded('groups')) {
            return $this->groups->first()?->id_sys_group;
        }
        return $this->groups()->first()?->id_sys_group;
    }

    public function scopeSearch($query, $search)
    {
        $query->with('groups');
        if (!$search) return $query;

        foreach ($search as $field => $value) {
            if (!empty($value)) {
                $query->where($field, 'like', $value);
            }
        }

        return $query;
    }
}
