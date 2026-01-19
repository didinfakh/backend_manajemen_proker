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
}
