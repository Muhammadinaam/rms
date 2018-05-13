<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Auth;
use Illuminate\Support\Facades\Hash;

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

    public function update($id)
    {

        $order = json_decode( request()->order, true );
        $order_details = $order['order_details'];
        $deleted_details = json_decode( request()->deleted_details, true );

        

        $other_info = json_decode( request()->other_info, true );

        return $this->saveOrder($order, $order_details, $deleted_details, $other_info);
    }

    public function saveOrderDiscount()
    {
        $order = json_decode( request()->order, true );
        $other_info = json_decode( request()->other_info, true );

        try
        {
            
            $order_being_updated = DB::table('tos')->where('id', $order['id'])->first();

            if($order['is_printed_for_customer'] == 1)
            {
                
                if($other_info['user_id'] == '' || $other_info['password'] == '')
                {
                    return ['success' => 'false', 'message' => 'Please enter User ID and Password'];
                }
                
                $auth_for_edit_after_print = $this->AuthForEditAfterPrint( $other_info['user_id'], $other_info['password'] );

                if( $auth_for_edit_after_print == 0 )
                {
                    return ['success' => 'false', 'message' => 'User ID / Password is not correct or does not have permission to edit after print'];
                }

                DB::table('edits_after_print_details')
                    ->insert([
                        'order_id' => $order['id'],
                        'edit_type' => 'Discount Changed',
                        'remarks' => $other_info['remarks'],
                        'before_amount' => $order_being_updated->order_amount_inc_st,
                        'after_amount' => $order['order_amount_inc_st'],
                        'edited_by' => Auth::user()->id,
                        'approved_by' => $auth_for_edit_after_print,
                        'created_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s'),
                    ]);
            }

            DB::table('tos')
                ->where('id', $order['id'])
                ->update([
                    'discount_allowed_by' => Auth::user()->id,
                    'order_amount_before_discount' => $order['order_amount_before_discount'],
                    'discount' => $order['discount'],
                    'order_amount_ex_st' => $order['order_amount_before_discount'] - $order['discount'],
                    'sales_tax' => $order['sales_tax'],
                    'order_amount_inc_st' => $order['order_amount_ex_st'] + $order['sales_tax'],
                ]);

                

            return ['success' => true, 'message' => 'Saved Successfully'];
        }
        catch(\Exception $ex)
        {
            throw $ex;
            return ['success' => false, 'message' => 'Not saved. Error: ' . $ex->getMessage()];
        }
    }

    public function edit($id)
    {
        $order = DB::table('tos')
                    ->where('tos.id', $id)
                    ->select('tos.id', 
                        'tos.order_type_id as order_type',
                        'tos.table_id as table',
                        'tos.cover',
                        'tables.name as table_name',
                        'tables.portion as table_portion',
                        'tos.deliver_to_name',
                        'tos.deliver_to_phone',
                        'tos.deliver_to_address',
                        'tos.is_printed_for_customer'
                    )
                    ->leftJoin('tables', 'tables.id', '=', 'tos.table_id')
                    ->first();

        $order->order_details = DB::table('tos_details')
                                ->where('to_id', $id)
                                ->select('tos_details.id as detail_id',
                                    'tos_details.item_id',
                                    'items.name as item_name',
                                    'tos_details.qty',
                                    'tos_details.rate'
                                )
                                ->join('items', 'items.id', '=', 'tos_details.item_id')
                                ->get();

        return json_encode($order);
    }

    public function AuthForEditAfterPrint($email, $password)
    {
        $ret = 0;

        $user = DB::table('users')
                        ->where('email', $email)
                        ->where('is_activated', 1)
                        ->select('id', 'password', 'is_admin')
                        ->first();

        if($user != null && Hash::check($password, $user->password))
        {
            if($user->is_admin == 1)
            {
                $ret = $user->id;
            }

            $permission = DB::table('user_permissions')
                ->join('permissions', 'permissions.id', '=', 'user_permissions.permission_id')
                ->where('user_permissions.user_id', $user->id)
                ->where('permissions.slug', 'edit-discount-after-print')
                ->first();

            if($permission != null)
            {
                $ret = $user->id;
            }

        }

        return $ret;

    }

    public function saveOrder($order, $order_details, $deleted_details, $other_info = null)
    {
        
        
        try
        {
            DB::beginTransaction();
            
            $id = $order['id'];
            $is_new_order = $id == null ? true : false;

            $order_being_updated = $is_new_order == false ? @DB::table('tos')->where('id', $id)->first() : null;

            

            $order_data = array();
            $order_data['order_type_id'] = $order['order_type'];
            $order_data['cover'] = isset($order['cover']) ? $order['cover'] : null;

            


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
                

                $order_data['deliver_to_name'] = null;
                $order_data['deliver_to_phone'] = null;
                $order_data['deliver_to_address'] = null;
            }
            else 
            {
                $order_data['deliver_to_name'] = $order['deliver_to_name'];
                $order_data['deliver_to_phone'] = isset($order['deliver_to_phone']) ? $order['deliver_to_phone'] : '' ;
                $order_data['deliver_to_address'] = isset($order['deliver_to_address']) ? $order['deliver_to_address'] : '' ;
            }

            
            $amount_before_discount = 0;
            foreach($order_details as $order_detail)
            {
                    
                $amount_before_discount += $order_detail['qty']*$order_detail['rate'];
                
            }



            $order_data['discount'] = 0;
            $order_data['order_amount_before_discount'] = $amount_before_discount;
            
            $order_data['order_amount_ex_st'] = $amount_before_discount;
            $order_data['sales_tax'] = $order['sales_tax'];
            $order_data['order_amount_inc_st'] = $amount_before_discount + $order['sales_tax'];

            if( $is_new_order )
            {
                $id = DB::table('tos')
                        ->insertGetId($order_data);
            }
            else 
            {
                $table_id = null;

                if( isset($order_data['table_id']) )
                {
                    $table_id = $order_data['table_id'];
                }

                $tos_edit_id = DB::table('tos_edits')
                        ->insertGetId([
                            'to_id' => $id,
                            'is_table_changed' => $order_being_updated->table_id != $table_id ? 1 : 0,
                            'new_table_id' => $table_id,
                            'created_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s'),
                        ]);

                DB::table('tos')
                        ->where('id', $id)
                        ->update($order_data);
            }


            
            // add link of this order to table
            if($order_data['order_type_id'] == 1)
            {
                DB::table('tables')
                    ->where('id', $order_data['table_id'])
                    ->update([
                        'current_order_id' => $id,
                    ]);
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
                                'edit_type' => 'Items Deleted',
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
                foreach($order_details as $order_detail)
                {
                    if($order_detail['detail_id'] == null)
                    {
                        DB::table('tos_edits_details')
                            ->insert([
                                'to_edit_id' => $tos_edit_id,
                                'edit_type' => 'Items Added',
                                'item_id' => $order_detail['item_id'],
                                'qty' => $order_detail['qty'],
                                'rate' => $order_detail['rate'],
                                'amount' => $order_detail['qty']*$order_detail['rate'],
                            ]);
                    }
                }
            }


            
            if($is_new_order)
            {
                $this->insertPrintJob('New Order', $id, false);
            }
            else
            {
                $this->insertPrintJob('Edit Order', $tos_edit_id, false);
            }






            if( $is_new_order == false )
            {
                if($order['is_printed_for_customer'] == 1)
                {
                    
                    if($other_info['user_id'] == '' || $other_info['password'] == '')
                    {
                        return ['success' => 'false', 'message' => 'Please enter User ID and Password'];
                    }
                    
                    $auth_for_edit_after_print = $this->AuthForEditAfterPrint( $other_info['user_id'], $other_info['password'] );

                    if( $auth_for_edit_after_print == 0 )
                    {
                        return ['success' => 'false', 'message' => 'User ID / Password is not correct or does not have permission to edit after print'];
                    }

                    DB::table('edits_after_print_details')
                        ->insert([
                            'order_id' => $order['id'],
                            'edit_type' => 'Order Edited',
                            'remarks' => $other_info['remarks'],
                            'before_amount' => $order_being_updated->order_amount_inc_st,
                            'after_amount' => $order_data['order_amount_inc_st'],
                            'edited_by' => Auth::user()->id,
                            'approved_by' => $auth_for_edit_after_print,
                            'created_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s'),
                        ]);
                }
            }




            DB::commit();
            return ['success' => true, 'message' => 'Saved Successfully'];
        }
        catch(\Exception $ex)
        {
            //throw $ex;
            DB::rollBack();
            return ['success' => false, 'message' => 'Order was not saved. Error: ' . $ex->getMessage()];
        }
    }

    public function openOrders()
    {
        return DB::table('tos')
                    ->join('order_statuses', 'order_statuses.id', '=', 'tos.order_status_id')
                    ->join('order_types', 'order_types.id', '=', 'tos.order_type_id')
                    ->leftJoin('tables', 'tables.id', '=', 'tos.table_id')
                    ->select('tos.*', 
                        'order_types.name as order_type', 
                        'order_statuses.name as order_status', 'order_statuses.slug as order_status_slug',
                        'tables.portion as portion', 'tables.name as table_name',
                        DB::raw('TIMESTAMPDIFF(MINUTE,tos.order_datetime,NOW()) as elapsed_minutes')
                    )
                    ->whereNotIn('order_statuses.slug', ['closed','cancelled'])
                    ->get();
    }

    public function changeOrderStatusApi()
    {
        $order_id = request()->order_id;
        $status = request()->status;

        return $this->changeOrderStatus($order_id, $status);
    }

    public function changeOrderStatus($order_id, $status)
    {
        try {

            DB::beginTransaction();

            $order_data = [
                'order_status_id' => $status
            ];

            if($status == 2)
            {
                $order_data['served_datetime'] = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
            }
            else if($status == 1)
            {
                $order_data['served_datetime'] = null;
            }
            else if($status == 3)
            {
                $order_data['closing_time'] = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
            }

            DB::table('tos')
                ->where('id', $order_id)
                ->update($order_data);



            if($status == 3 || $status == 4)
            {

                DB::table('tables')
                    ->where('current_order_id', $order_id)
                    ->update(['current_order_id' => null]);
            }

            if($status == 4)
            {
                $this->insertPrintJob('Order Cancelled', $order_id, 0);
            }



            DB::commit();

            return ['success' => true, 'message' => 'Status Changed Successfully'];
            
        } catch (\Exception $e) {
            
            DB::rollBack();
            return ['success' => false, 'message' => 'Error Occurred: ' . $e->getMessage()];
        }

        
    }

    public function closeOrder()
    {
        $order_id = request()->order_id;
        $received_through = request()->received_through;
        



        try {
            DB::beginTransaction();

            $tos_update_data = array();

            $tos_update_data['received_through'] = $received_through == 'Cash2' ? 'Cash' : $received_through;
            $tos_update_data['received_by'] = Auth::user()->id;
            $tos_update_data['received_at'] = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
            $tos_update_data['ent_remarks'] = request()->ent_remarks == '' ? null : request()->ent_remarks;

            DB::table('tos')
                ->where('id', $order_id)
                ->update($tos_update_data);


            $close_order_result = $this->changeOrderStatus($order_id, 3);

            if($close_order_result['success'] == false)
                throw new \Exception( $close_order_result['message'], 1);


            if($received_through == 'Cash2' && config('app.is_client_bad') == true)
            {
                // tax chori... put original in db2 and fake in db1
                $this->orderToFinalTable($order_id, 'invoices', 'db2');

                //put fake (reduced) in db1
                $this->reduceOrder($order_id);
                $this->orderToFinalTable($order_id, 'invoices');

            }
            else if($received_through == 'Ent')
            {
                if( config('app.is_client_bad') == false )
                {
                    $this->orderToFinalTable($order_id, 'ent_bills');
                }
                $this->orderToFinalTable($order_id, 'ent_bills', 'db2');
                
            }
            else
            {
                // NO tax chori
                // put in db1 and db2

                $this->orderToFinalTable($order_id, 'invoices');
                $this->orderToFinalTable($order_id, 'invoices', 'db2');
            }
                


            DB::commit();
            return ['success' => true, 'message' => 'Order Closed Successfully'];
            
        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => 'Error Occurred: ' . $e->getMessage()];
        }
    }

    public function reduceOrder($order_id)
    {
        $order = DB::table('tos')->where('id', $order_id)->first();
        //$order_details = DB::table('tos_details')->where('to_id', $order_id)->get();

        $order_amount_ex_st = $order->order_amount_ex_st;

        $st_rate = (($order->order_amount_inc_st / $order->order_amount_ex_st)*100) - 100;

        $new_item = DB::table('items')
            ->where('price', '<', $order_amount_ex_st)
            //->orderBy('price', 'asc')
            ->orderBy(DB::raw('RAND()'))
            ->first();

        if($new_item == null)
        {
            return;
        }



        DB::table('tos')
            ->where('id', $order_id)
            ->update([
                'cover' => 1,
                'order_amount_ex_st' => $new_item->price,
                'sales_tax' => $new_item->price * $st_rate / 100,
                'order_amount_inc_st' => $new_item->price + ($new_item->price * $st_rate / 100),
                'order_amount_before_discount' => $new_item->price,
                'discount' => 0,
                'discount_allowed_by' => null,
                
            ]);

            DB::table('tos_details')->where('to_id', $order_id)->delete();

            DB::table('tos_details')
                ->insert([
                    'to_id' => $order_id,
                    'item_id' => $new_item->id,
                    'qty' => 1,
                    'rate' => $new_item->price,
                    'amount' => $new_item->price,
                ]);
    }

    public function orderToFinalTable($order_id, $table, $connection_name = null)
    {
        $master_table = $table;
        $detail_table = $table . '_details';

        $foreign_key = '';

        if($master_table == 'invoices')
        {
            $foreign_key = 'invoice_id';
        }
        else if($master_table == 'ent_bills')
        {
            $foreign_key = 'ent_bill_id';
        }

        $to = DB::table('tos')
                    ->where('id', $order_id)
                    ->first();

        $to_detail = DB::table('tos_details')
                        ->where('to_id', $order_id)
                        ->get();

        


        $connection = $connection_name != null ? DB::connection($connection_name) : DB::connection();

        $connection->beginTransaction();
        
        $order_id = $to->id;

        $to = json_decode( json_encode( $to ), true);
        unset($to['id']);
        unset($to['is_printed_for_customer']);

        $to['order_id'] = $order_id;

        $duplicate_order = $connection->table($master_table)
            ->where('order_id', $order_id)
            ->first();

        if($duplicate_order != null)
        {
            //throw new \Exception('This Order has already been closed');

            

            $connection->table($master_table)
                ->where('order_id', $order_id)
                ->delete();

            $connection->table($detail_table)
                ->where($foreign_key, $order_id)
                ->delete();
        }

        $master_id = $connection->table($master_table)
                        ->insertGetId($to);

        

        foreach ($to_detail as $to_detail_row) {
            $to_detail_row = json_decode( json_encode( $to_detail_row ), true);
            
            unset( $to_detail_row['id'] );
            unset( $to_detail_row['to_id'] );

            $to_detail_row[$foreign_key] = $master_id;

            $connection->table($detail_table)
                ->insert($to_detail_row);
        }

        $connection->commit();

        

    }

    public function insertPrintJob($print_type, $entity_id, $is_reprint)
    {
        DB::table('print_jobs')
            ->insert([
                'print_type' => $print_type,
                'entity_id' => $entity_id,
                'is_reprint' => $is_reprint,
            ]);
    }

    public function printForCustomer($order_id)
    {
        DB::table('tos')
            ->where('id', $order_id)
            ->update(['is_printed_for_customer'=>1]);

        $this->insertPrintJob('Customer Print', $order_id, 0);
    }
    
    public function reprintForKitchens($order_id)
    {
        $this->insertPrintJob('Reprint for Kitchens', $order_id, true);        
    }

    public function transferOrdersToInvoicesTable()
    {
        $order_ids = DB::table('tos')
            ->select('id as order_id')
            ->whereNotIn('id', DB::table('invoices')->select('order_id')->get()->pluck('order_id') )
            ->where('tos.received_through', '<>', 'Ent')
            ->where('tos.order_status_id', 3)
            ->get()->pluck('order_id');

        foreach($order_ids as $order_id)
        {
            $this->orderToFinalTable($order_id, 'invoices');
        }

        return 'done';
        
    }
}
