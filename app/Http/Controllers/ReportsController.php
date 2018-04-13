<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class ReportsController extends Controller
{
    //

    public $orders_table = 'tos';
    public $orders_details_table = 'tos_details';
    public $order_details_fk = 'to_id';

    public function salesReportByItem()
    {
        $from_date = \Carbon\Carbon::parse( request()->from_date )->format('Y-m-d H:i:s');
        $to_date = \Carbon\Carbon::parse( request()->to_date )->format('Y-m-d H:i:s');

        return DB::table($this->orders_table)
                    ->whereBetween('order_datetime', [$from_date, $to_date])
                    ->where($this->orders_table.'.order_status_id', 3)
                    ->join($this->orders_details_table, 
                            $this->orders_details_table .'.'.$this->order_details_fk,
                            '=',
                            $this->orders_table.'.id'
                    )
                    ->leftJoin('items', 'items.id', '=', $this->orders_details_table .'.item_id')
                    ->select(
                        'items.name',
                        DB::raw('sum('.$this->orders_details_table .'.qty) as qty'),
                        DB::raw('sum('.$this->orders_details_table .'.amount) as amount')
                    )
                    ->groupBy(DB::raw('items.name WITH ROLLUP'))
                    ->get();
    }

    public function salesReportByOrder()
    {
        $from_date = \Carbon\Carbon::parse( request()->from_date )->format('Y-m-d H:i:s');
        $to_date = \Carbon\Carbon::parse( request()->to_date )->format('Y-m-d H:i:s');

        return DB::table($this->orders_table)
                    ->whereBetween('order_datetime', [$from_date, $to_date])
                    ->where($this->orders_table.'.order_status_id', 3)
                    
                    ->select(
                        $this->orders_table.'.id',
                        $this->orders_table.'.order_amount_inc_st as amount'
                    )
                    ->get();
    }

    public function collectionReport()
    {
        $from_date = \Carbon\Carbon::parse( request()->from_date )->format('Y-m-d H:i:s');
        $to_date = \Carbon\Carbon::parse( request()->to_date )->format('Y-m-d H:i:s');
    }

}
