<?php

namespace App\Http\Controllers;

use App\Models\SysPermissions;
use Illuminate\Http\Request;

class SysPermissionsController extends BaseController
{
    protected $model;

    public function __construct(SysPermissions $model)
    {
        $this->model = $model;
    }
}