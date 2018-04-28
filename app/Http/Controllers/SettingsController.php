<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class SettingsController extends Controller
{
    //

	public function show()
	{
		return DB::table('settings')
					->orderBy('group')
					->get();
	}

	public function save()
	{
		//return request()->all();
		$settings = json_decode( request()->settings, true );

		try {
			
			DB::beginTransaction();
			foreach ($settings as $setting) {
				DB::table('settings')
					->where('id', $setting['id'])
					->update([
						'value' => $setting['value'],
					]);
			}

			DB::commit();
			return ['success' => true, 'message' => 'Saved Successfully'];

		} catch (\Exception $e) {
			DB::rollBack();
			return ['success' => false, 'message' => 'Error Occurred: '. $e->getMessage()];
		}

	}

	public function getSettingBySlug()
	{
		$slug = request()->slug;

		return json_encode(DB::table('settings')->where('slug', $slug)->first());
	}

	public function getConfig()
	{
		$config = request()->config;

		return config($config);
	}

}
