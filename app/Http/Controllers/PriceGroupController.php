<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PriceGroupController extends Controller
{
    public function index()
    {
        return \App\PriceGroup::get();
    }
}
