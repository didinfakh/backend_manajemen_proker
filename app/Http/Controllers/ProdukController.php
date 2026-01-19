<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;

class ProdukController extends BaseController
{
    protected $model;

    public function __construct(Produk $model)
    {
        $this->model = $model;
    }
}