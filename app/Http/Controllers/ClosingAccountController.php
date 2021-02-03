<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class ClosingAccountController extends Controller
{
    public function index()
    {
        return \App\ClosingAccount::orderBy('order')->get();
    }

    public function edit($id)
    {
        return \App\ClosingAccount::find($id);
    }

    public function validateRequest()
    {
        $this->validate(request(), [
            'name' => 'required',
        ]);
    }

    public function store()
    {
        $this->validateRequest();

        $model = new \App\ClosingAccount;

        return $this->saveDataFromRequest($model);

        
    }

    public function update($id)
    {
        $this->validateRequest();

        $model = \App\ClosingAccount::find($id);

        return $this->saveDataFromRequest($model);
    }

    public function saveDataFromRequest($model)
    {
        try {

            DB::beginTransaction();

            $model->order = request()->order;
            $model->name = request()->name;
            $model->sales_tax_rate = request()->sales_tax_rate;
            $model->show_amount_received_input = request()->show_amount_received_input;
            $model->additional_information_fields = request()->additional_information_fields;

            $model->save();

            DB::commit();

            return ['success' => true, 'message' => 'Saved Successfully'];

        } catch (\Throwable $ex) {
            DB::rollBack();
            return ['success' => false, 'message' => 'Some error occured. Error: ' . $ex->getMessage()];
        }
    }

    public function destroy($id)
    {
        try {
            \App\ClosingAccount::find($id)->delete($id);
            return ['success' => true, 'message' => 'Deleted successfully'];
        } catch (\Throwable $th) {
            return ['success' => false, 'message' => 'Some error occured. Error: ' . $th->getMessage()];
        }
    }
}
