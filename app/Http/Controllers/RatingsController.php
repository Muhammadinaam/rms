<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class RatingsController extends Controller
{
    //

    public function index()
    {
        $from_date = \Carbon\Carbon::parse( request()->from_date )->format('Y-m-d H:i:s');
        $to_date = \Carbon\Carbon::parse( request()->to_date )->format('Y-m-d H:i:s');

        return DB::table('ratings')
            ->select('users.name as order_taken_by', 
            'tos.id as order_id', 'tos.order_datetime',
            'ratings.food_rating', 'ratings.service_rating', 'ratings.ambiance_rating')
            ->join('tos', 'tos.id', '=', 'ratings.order_id')
            ->leftJoin('users', 'users.id', '=', 'tos.created_by')
            ->whereBetween('tos.order_datetime', [$from_date, $to_date])
            ->orderBy('tos.order_datetime', 'asc')
            ->get();
    }

    public function saveRating()
    {
        try
        {
            $order = DB::table('tos')
                ->where('id', request()->order_id)
                ->first();

            if( $order == null )
                return ['success' => false, 'message' => 'Order ID: ' . request()->order_id . ' not found' ];

            if( request()->food_rating > 5 || request()->food_rating < 0 )
                return ['success' => false, 'message' => 'Please enter rating between 0 and 5' ];

            if( request()->service_rating > 5 || request()->service_rating < 0 )
                return ['success' => false, 'message' => 'Please enter rating between 0 and 5' ];

            if( request()->ambiance_rating > 5 || request()->ambiance_rating < 0 )
                return ['success' => false, 'message' => 'Please enter rating between 0 and 5' ];

            DB::beginTransaction();

            DB::table('ratings')
                ->where('order_id', request()->order_id)
                ->delete();

            DB::table('ratings')
                ->insert([
                    'order_id' => request()->order_id,
                    'food_rating' => request()->food_rating,
                    'service_rating' => request()->service_rating,
                    'ambiance_rating' => request()->ambiance_rating,
                ]);

            DB::commit();

            return ['success' => true, 'message' => 'Rating saved successfully'];
        }
        catch(\Exception $ex)
        {
            return ['success' => false, 'message' => 'Rating not saved. Please try again'];
        }

        
    }
}
