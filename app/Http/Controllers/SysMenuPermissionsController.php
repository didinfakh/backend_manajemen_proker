<?php

namespace App\Http\Controllers;

use App\Models\SysMenuPermissions;
use Illuminate\Http\Request;

class SysMenuPermissionsController extends BaseController
{
    protected $model;

    public function __construct(SysMenuPermissions $model)
    {
        $this->model = $model;
    }
}