<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReportsController extends Controller
{
    //

    public function salesReportByItem()
    {
        $from_date = \Carbon\Carbon::parse( request()->from_date )->format('Y-m-d H:i:s');
        $to_date = \Carbon\Carbon::parse( request()->to_date )->format('Y-m-d H:i:s');

        
    }

    public function collectionReport()
    {
        $from_date = \Carbon\Carbon::parse( request()->from_date )->format('Y-m-d H:i:s');
        $to_date = \Carbon\Carbon::parse( request()->to_date )->format('Y-m-d H:i:s');
    }

}
