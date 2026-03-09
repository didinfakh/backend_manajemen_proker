<?php

namespace App\Http\Controllers;

use App\Models\SysMenu;

class SysMenuController extends BaseController
{
    protected $model;

    public function __construct(SysMenu $model)
    {
        $this->model = $model;
    }
}
