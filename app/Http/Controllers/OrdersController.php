<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class OrdersController extends Controller
{
    //

    public function orderTypes()
    {
        return DB::table('order_types')->get();
    }
}
