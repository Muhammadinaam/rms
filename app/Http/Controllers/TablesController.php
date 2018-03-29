<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use App\Table;

class TablesController extends Controller
{
    //

	public function getPortions()
	{
		return DB::table('tables')
				->select('portion')
				->groupBy('portion')
				->get();
	}

    public function index()
    {
    	$tables = Table::orderBy('portion')->get();
    	$tables = $tables->groupBy('portion');
    	return $tables;
    }

    public function edit($id)
    {
    	$table = Table::find($id);
    	return $table;
    }

    public function store()
    {
        //return request()->all();
        $this->validate(request(), [
            'portion' => 'required',
            'name' => 'required',
        ]);

        $table = new Table;

        return $this->saveDataFromRequest($table);

        
    }

    public function update($id)
    {
        // return request()->all();
        $this->validate(request(), [
            'portion' => 'required',
            'name' => 'required',
        ]);

        $table = Table::find($id);

        return $this->saveDataFromRequest($table);
    }

    public function saveDataFromRequest($table)
    {
        try {

            DB::beginTransaction();
            

            $table->name = request()->name;
            $table->portion = request()->portion;

            $table->save();

            

            DB::commit();

            return ['success' => true, 'message' => 'Saved Successfully'];

        } catch (Exception $e) {
            
            DB::rollBack();
            return ['success' => false, 'message' => 'Some error occured. Error: ' . $ex->getMessage()];
        }
    }

    public function freeTables()
    {
        return Table::whereasdfNull('is_free', 1)
                    ->orderBy('portion')->get();
    }
}
