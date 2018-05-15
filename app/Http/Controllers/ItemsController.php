<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Item;
use DB;
use Illuminate\Support\Str;

class ItemsController extends Controller
{
    //

    public function index()
    {
    	$items = Item::orderBy('code')->orderBy('category')->get();
    	return $items;
    }

    public function edit($id)
    {
    	$item = Item::find($id);
    	return $item;
    }

    public function store()
    {
        //return request()->all();
        $this->validate(request(), [
            'name' => 'required',
            'code' => 'required',
            'unit' => 'required',
            'price' => 'required',
        ]);

        $item = new Item;

        $item->uid = (string) Str::uuid();

        return $this->saveDataFromRequest($item);

        
    }

    public function update($id)
    {
        // return request()->all();
        $this->validate(request(), [
            'name' => 'required',
            'code' => 'required',
            'unit' => 'required',
            'price' => 'required',
        ]);

        $item = Item::find($id);

        return $this->saveDataFromRequest($item);
    }

    public function saveDataFromRequest($item)
    {
        try {

            DB::beginTransaction();
            

            $item->category = request()->category;
            $item->item_group = request()->group;
            $item->name = request()->name;
            $item->code = request()->code;
            $item->unit = request()->unit;
            $item->price = request()->price;

            $item->save();

            

            DB::commit();

            return ['success' => true, 'message' => 'Saved Successfully'];

        } catch (Exception $e) {
            
            DB::rollBack();
            return ['success' => false, 'message' => 'Some error occured. Error: ' . $ex->getMessage()];
        }
    }
}
