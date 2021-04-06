<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use \DB;

class PriceGroupController extends Controller
{
    public function index()
    {
        return \App\PriceGroup::with('priceGroupItems')->get();
    }

    public function edit($id)
    {
        return \App\PriceGroup::with('priceGroupItems')->find($id);
    }

    public function store()
    {
        $this->validate(request(), [
            'name' => 'required|unique:price_groups',
        ]);

        $price_group = new \App\PriceGroup;

        return $this->saveDataFromRequest($price_group);
    }

    public function update($id)
    {
        $this->validate(request(), [
            'name' => 'required|unique:price_groups,name,' . $id ,
        ]);

        $price_group = \App\PriceGroup::find($id);

        return $this->saveDataFromRequest($price_group);
    }

    public function destroy($id)
    {
        \App\PriceGroup::where('id', $id)->delete();
        return ['success' => true, 'message' => 'Deleted successfully'];
    }

    public function saveDataFromRequest($price_group)
    {
        try {

            DB::beginTransaction();

            $price_group->name = request()->name;
            $price_group->multiplying_factor = request()->multiplying_factor;

            $price_group->save();

            $price_group->priceGroupItems()->delete();
            $items = json_decode(request()->items, true) ;
            foreach ($items as $item) {
                \App\PriceGroupItem::create([
                    'price_group_id' => $price_group->id,
                    'item_id' => $item['item_id'],
                    'price' => $item['price']
                ]);
            }

            DB::commit();

            return ['success' => true, 'message' => 'Saved Successfully'];

        } catch (\Throwable $ex) {
            DB::rollBack();
            return ['success' => false, 'message' => 'Some error occured. Error: ' . $ex->getMessage()];
        }
    }
}
