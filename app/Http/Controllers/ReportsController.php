<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class ReportsController extends Controller
{
    //

    public $invoices_table = 'invoices';
    public $invoices_details_table = 'invoices_details';
    public $invoices_details_fk = 'invoice_id';

    public $ent_table = 'ent_bills';
    public $ent_details_table = 'ent_bills_details';
    public $ent_details_fk = 'ent_bill_id';

    public function createTempTables($from_date, $to_date)
    {
        {
            

            DB::statement('CREATE TEMPORARY TABLE IF NOT EXISTS invoices_temp AS (SELECT * FROM ' 
                . $this->invoices_table . ' limit 0 )');

            DB::statement('CREATE TEMPORARY TABLE IF NOT EXISTS invoices_details_temp AS (SELECT * FROM ' 
                . $this->invoices_details_table . 
                ' limit 0 )');

            DB::statement('CREATE TEMPORARY TABLE IF NOT EXISTS ent_bills_temp AS (SELECT * FROM ' 
                . $this->ent_table . ' limit 0 )');

            DB::statement('CREATE TEMPORARY TABLE IF NOT EXISTS ent_bills_details_temp AS (SELECT * FROM ' 
                . $this->ent_details_table . 
                ' limit 0 )');

            DB::connection('db2')
                ->table($this->invoices_table)
                ->whereBetween($this->invoices_table.'.order_datetime', [$from_date, $to_date])
                ->orderBy($this->invoices_table.'.id')
                ->chunk(100, function($rows){
                    $rows = json_decode(json_encode($rows), true);
                    DB::table('invoices_temp')
                    ->insert(
                        $rows
                    );
                });

            

            DB::connection('db2')
                ->table($this->invoices_details_table)
                ->select($this->invoices_details_table.'.*')
                ->join($this->invoices_table, 
                        $this->invoices_details_table .'.'.$this->invoices_details_fk,
                        '=',
                        $this->invoices_table.'.id'
                )
                ->whereBetween($this->invoices_table.'.order_datetime', [$from_date, $to_date])
                ->orderBy($this->invoices_details_table.'.id')
                ->chunk(100, function($rows){
                    $rows = json_decode(json_encode($rows), true);
                    DB::table('invoices_details_temp')
                    ->insert(
                        $rows
                    );
                });


            DB::connection('db2')
                ->table($this->ent_table)
                ->whereBetween($this->ent_table.'.order_datetime', [$from_date, $to_date])
                ->orderBy($this->ent_table.'.id')
                ->chunk(100, function($rows){
                    $rows = json_decode(json_encode($rows), true);
                    DB::table('ent_bills_temp')
                    ->insert(
                        $rows
                    );
                });

            

            DB::connection('db2')
                ->table($this->ent_details_table)
                ->select($this->ent_details_table.'.*')
                ->join($this->ent_table, 
                        $this->ent_details_table .'.'.$this->ent_details_fk,
                        '=',
                        $this->ent_table.'.id'
                )
                ->whereBetween($this->ent_table.'.order_datetime', [$from_date, $to_date])
                ->orderBy($this->ent_details_table.'.id')
                ->chunk(100, function($rows){
                    $rows = json_decode(json_encode($rows), true);
                    DB::table('ent_bills_details_temp')
                    ->insert(
                        $rows
                    );
                });


            $this->invoices_table = 'invoices_temp';
            $this->invoices_details_table = 'invoices_details_temp';

            $this->ent_table = 'ent_bills_temp';
            $this->ent_details_table = 'ent_bills_details_temp';
        }
    }

    public function TopLeastSellingItemsReport()
    {
        $from_date = \Carbon\Carbon::parse( request()->from_date )->format('Y-m-d H:i:s');
        $to_date = \Carbon\Carbon::parse( request()->to_date )->format('Y-m-d H:i:s');
        $show_actual = request()->s_a;

        $show_actual = filter_var($show_actual, FILTER_VALIDATE_BOOLEAN);

        if($show_actual == true)
        {
            $this->createTempTables($from_date, $to_date);
        }

        $query = DB::table($this->invoices_details_table)
        ->leftJoin('items', 'items.id', '=', $this->invoices_details_table . '.item_id')
        ->select('items.name as name')
        ->groupBy('items.name')
        ->limit(5);

        $top_selling_items_by_qty = (clone $query)->addSelect(DB::raw('sum('.$this->invoices_details_table.'.qty) as value'))
                                ->orderBy('value', 'desc')
                                ->get();

        $top_selling_items_by_amount = (clone $query)->addSelect(DB::raw('sum('.$this->invoices_details_table.'.amount) as value'))
                                ->orderBy('value', 'desc')
                                ->get();

        $least_selling_items_by_qty = (clone $query)->addSelect(DB::raw('sum('.$this->invoices_details_table.'.qty) as value'))
                                ->orderBy('value', 'asc')
                                ->get();

        $least_selling_items_by_amount = (clone $query)->addSelect(DB::raw('sum('.$this->invoices_details_table.'.amount) as value'))
                                ->orderBy('value', 'asc')
                                ->get();

        return compact('top_selling_items_by_qty', 
        'top_selling_items_by_amount', 
        'least_selling_items_by_qty',
        'least_selling_items_by_amount');
    }

    public function salesReportByItem()
    {
        $from_date = \Carbon\Carbon::parse( request()->from_date )->format('Y-m-d H:i:s');
        $to_date = \Carbon\Carbon::parse( request()->to_date )->format('Y-m-d H:i:s');
        $show_actual = request()->s_a;

        $show_actual = filter_var($show_actual, FILTER_VALIDATE_BOOLEAN);

        if($show_actual == true)
        {
            $this->createTempTables($from_date, $to_date);
        }

        $data = array();

        $data['report_summary'] = $this->getReportSummary($from_date, $to_date);

        $data['report_detail'] = array();

        $data['report_detail']['orders'] = $this->getReportByItems(
            $this->invoices_table, 
            $this->invoices_details_table, 
            $this->invoices_details_fk, 
            $from_date, 
            $to_date);

        $data['report_detail']['ent_orders'] = $this->getReportByItems(
            $this->ent_table, 
            $this->ent_details_table, 
            $this->ent_details_fk, 
            $from_date, 
            $to_date);

        return $data;
    }

    public function getReportByItems($master_table, $details_table, $fk, $from_date, $to_date)
    {
        return DB::table($master_table)
            ->whereBetween('order_datetime', [$from_date, $to_date])
            ->where($master_table.'.order_status_id', 3)
            ->join($details_table, 
                    $details_table .'.'.$fk,
                    '=',
                    $master_table.'.id'
            )
            ->leftJoin('items', 'items.id', '=', $details_table .'.item_id')
            ->select(
                'items.category',
                'items.item_group',
                'items.name',
                DB::raw('sum('.$details_table .'.qty) as qty'),
                DB::raw('sum('.$details_table .'.amount) as amount')
            )
            ->groupBy(DB::raw('items.category, items.item_group, items.name'))
            ->get();
    }

    public function getReportSummary($from_date, $to_date)
    {
        $summary = array();

        $summary['receipt_detail'] = DB::table($this->invoices_table)
                    ->whereBetween('order_datetime', [$from_date, $to_date])
                    ->select(
                        $this->invoices_table . '.received_through',
                        DB::raw('sum('.$this->invoices_table.'.order_amount_inc_st) as total_received'),
                        DB::raw('sum('.$this->invoices_table.'.cover) as total_cover'),
                    )
                    ->groupBy($this->invoices_table.'.received_through')
                    ->get();

        $summary['st_and_discount'] = DB::table($this->invoices_table)
            ->whereBetween('order_datetime', [$from_date, $to_date])
            ->select(
                DB::raw('sum('.$this->invoices_table.'.sales_tax) as total_sales_tax'),
                DB::raw('sum('.$this->invoices_table.'.discount) as total_discount')
            )
            ->first();

        return $summary;
    }

    public function getReportByOrders($orders_table, $from_date, $to_date)
    {
        $query = DB::table($orders_table)
        ->leftJoin('tos', 'tos.id', '=', $orders_table.'.order_id')
        ->leftJoin('users', 'users.id', '=', 'tos.created_by')
        ->whereBetween($orders_table.'.order_datetime', [$from_date, $to_date])
        ->where($orders_table.'.order_status_id', 3)
        
        ->join('order_types', 'order_types.id', '=', $orders_table.'.order_type_id');
        
        // $totals_row = $query->select(
        //     DB::raw('null as id'),
        //     DB::raw('null as order_datetime'),
        //     DB::raw('null as order_id'),
        //     DB::raw('null as ent_remarks'),
        //     DB::raw('null as received_through'),
        //     DB::raw('null as order_type'),
        //     DB::raw('null as closing_time'),
        //     DB::raw('null as created_by'),
        //     DB::raw('sum('.$orders_table.'.cover) as cover'),
        //     DB::raw('sum('.$orders_table.'.discount) as discount'),
        //     DB::raw('sum('.$orders_table.'.sales_tax) as sales_tax'),
        //     DB::raw('sum('.$orders_table.'.order_amount_inc_st) as amount')
        // )->get()->toArray();

        $rows = $query->select(
            $orders_table.'.id',
            $orders_table.'.order_datetime',
            $orders_table.'.order_id',
            $orders_table.'.ent_remarks',
            $orders_table.'.received_through',
            'order_types.name as order_type',
            $orders_table.'.closing_time',
            'users.name as created_by',
            $orders_table.'.cover',
            $orders_table.'.discount',
            $orders_table.'.sales_tax',
            $orders_table.'.order_amount_inc_st as amount'
        )->get()->toArray();

        // $rows = array_merge($rows, $totals_row);

        return $rows;

        
    }

    public function salesReportByOrder()
    {
        $from_date = \Carbon\Carbon::parse( request()->from_date )->format('Y-m-d H:i:s');
        $to_date = \Carbon\Carbon::parse( request()->to_date )->format('Y-m-d H:i:s');
        $show_actual = request()->s_a;

        $show_actual = filter_var($show_actual, FILTER_VALIDATE_BOOLEAN);

        if($show_actual == true)
        {
            $this->createTempTables($from_date, $to_date);
        }

        $data = array();

        $data['report_summary'] = $this->getReportSummary($from_date, $to_date);

        $data['report_detail'] = array();

        $data['report_detail']['orders'] = $this->getReportByOrders(
            $this->invoices_table, 
            $from_date, 
            $to_date);

        $data['report_detail']['ent_orders'] = $this->getReportByOrders(
            $this->ent_table, 
            $from_date, 
            $to_date);

        return $data;

    }

    public function editsAfterPrintReport()
    {
        $from_date = \Carbon\Carbon::parse( request()->from_date )->format('Y-m-d H:i:s');
        $to_date = \Carbon\Carbon::parse( request()->to_date )->format('Y-m-d H:i:s');
        $show_actual = request()->s_a;

        $show_actual = filter_var($show_actual, FILTER_VALIDATE_BOOLEAN);

        return DB::table('edits_after_print_details')
                    ->select('edits_after_print_details.*', 'users1.name as edited_by_name', 'users2.name as approved_by_name')
                    ->whereBetween('edits_after_print_details.created_at', [$from_date, $to_date])
                    ->join('users as users1', 'users1.id', '=', 'edits_after_print_details.edited_by')
                    ->join('users as users2', 'users2.id', '=', 'edits_after_print_details.approved_by')
                    ->get();
    }

    public function collectionReport()
    {
        $from_date = \Carbon\Carbon::parse( request()->from_date )->format('Y-m-d H:i:s');
        $to_date = \Carbon\Carbon::parse( request()->to_date )->format('Y-m-d H:i:s');
    }

    public function cancelledOrdersReport()
    {
        $from_date = \Carbon\Carbon::parse( request()->from_date )->format('Y-m-d H:i:s');
        $to_date = \Carbon\Carbon::parse( request()->to_date )->format('Y-m-d H:i:s');
        //$show_actual = request()->s_a;

        return DB::table('tos')
                ->select('tos.id', 'tos.order_amount_inc_st as amount', 'users.name as cancelled_by', 'tos.cancellation_remarks')
                ->leftJoin('users', 'users.id', '=', 'tos.cancelled_by')
                ->whereBetween('tos.order_datetime', [$from_date, $to_date])
                ->where('tos.order_status_id', 4)
                ->orderBy('tos.id')
                ->get();
    }

    public function getInvoiceData()
    {
        $show_actual = request()->s_a;
        $invoice_datetime = request()->invoice_datetime;

        $show_actual = filter_var($show_actual, FILTER_VALIDATE_BOOLEAN);

        if($show_actual == true)
        {
            $this->createTempTables($invoice_datetime, $invoice_datetime);
        }

        $invoice_id = request()->invoice_id;

        $invoice_data = DB::table($this->invoices_details_table)
                            ->select('items.name as item_name', 'qty', 'rate', 'amount')
                            ->where('invoice_id', $invoice_id)
                            ->join('items', $this->invoices_details_table . '.item_id', '=', 'items.id')
                            ->get();

        return $invoice_data;
    }

}
