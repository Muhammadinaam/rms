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


    public function store()
    {

        $order = json_decode( request()->order, true );
        $order_details = $order['order_details'];
        $deleted_details = json_decode( request()->deleted_details, true );

        return $this->saveOrder($order, $order_details, $deleted_details);
    }

    public function saveOrder($order, $order_details, $deleted_details)
    {
        try
        {
            DB::beginTransaction();
            
            $id = $order['id'];
            $is_new_order = $id == null ? true : false;

            $order_being_updated = $is_new_order == false ? @DB::table('tos')->where('id', $id)->first() : null;

            $order_data = array();
            $order_data['order_type_id'] = $order['order_type'];

            if($is_new_order)
            {
                $order_data['order_datetime'] = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
                $order_data['order_status_id'] = 1;
            }


            // remove link of this order from table
            DB::table('tables')
                ->where('current_order_id', $id)
                ->update([
                    'current_order_id' => null,
                ]);

            if($order_data['order_type_id'] == 1)
            {
                $order_data['table_id'] = $order['table'];
                
                // add link of this order to table
                DB::table('tables')
                    ->where('id', $order_data['table_id'])
                    ->update([
                        'current_order_id' => $id,
                    ]);


                $order_data['deliver_to_name'] = null;
                $order_data['deliver_to_phone'] = null;
                $order_data['deliver_to_address'] = null;
            }
            else 
            {
                $order_data['deliver_to_name'] = $order['deliver_to_name'];
                $order_data['deliver_to_phone'] = $order['deliver_to_phone'];
                $order_data['deliver_to_address'] = $order['deliver_to_address'];
            }

            if( $is_new_order )
            {
                $id = DB::table('tos')
                        ->insertGetId($order_data);
            }
            else 
            {
                $tos_edit_id = DB::table('tos_edits')
                        ->insertGetId([
                            'to_id' => $id,
                            'is_table_changed' => $order_being_updated->table_id != $order_data['table_id'] ? 1 : 0,
                            'new_table_id' => $order_data['table_id'],
                            'created_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s'),
                        ]);

                DB::table('tos')
                        ->where('id', $id)
                        ->update($order_data);
            }



            //order details
            if( count($deleted_details) > 0 )
            {
                // delete deleted details from order
                DB::table('tos_details')
                    ->whereIn('id', collect($deleted_details)->pluck('detail_id')->toArray() )
                    ->delete();

                // add deleted details in tos_edits_details
                if($is_new_order == false)
                {
                    foreach($deleted_details as $deleted_detail)
                    {
                        DB::table('tos_edits_details')
                            ->insert([
                                'to_edit_id' => $tos_edit_id,
                                'edit_type' => 'item_deleted',
                                'item_id' => $deleted_detail['item_id'],
                                'qty' => $deleted_detail['qty'],
                                'rate' => $deleted_detail['rate'],
                                'amount' => $deleted_detail['qty'] * $deleted_detail['rate'],
                            ]);
                    }
                }
            }

            // add new details to order
            foreach($order_details as $order_detail)
            {
                if($order_detail['detail_id'] == null)
                {
                    DB::table('tos_details')
                        ->insert([
                            'to_id' => $id,
                            'item_id' => $order_detail['item_id'],
                            'qty' => $order_detail['qty'],
                            'rate' => $order_detail['rate'],
                            'amount' => $order_detail['qty']*$order_detail['rate'],
                        ]);
                }
            }

            // add new details to in tos_edits_details
            if($is_new_order == false)
            {
                if($order_detail['detail_id'] == null)
                {
                    foreach($order_details as $order_detail)
                    {
                        DB::table('tos_edits_details')
                            ->insert([
                                'to_edit_id' => $tos_edit_id,
                                'edit_type' => 'item_added',
                                'item_id' => $order_detail['item_id'],
                                'qty' => $order_detail['qty'],
                                'rate' => $order_detail['rate'],
                                'amount' => $order_detail['qty']*$order_detail['rate'],
                            ]);
                    }
                }
            }


            


            DB::commit();
            return ['success' => true, 'message' => 'Saved Successfully'];
        }
        catch(\Exception $ex)
        {
            DB::rollBack();
            return ['success' => false, 'message' => 'Order was not saved. Error: ' . $ex->getMessage()];
        }
    }
}
