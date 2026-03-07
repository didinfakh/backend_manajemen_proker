<?php

namespace App\Http\Controllers;

use App\Models\SysUserGroups;
use Illuminate\Http\Request;

class SysUserGroupsController extends BaseController
{
    protected $model;

    public function __construct(SysUserGroups $model)
    {
        $this->model = $model;
    }
}