<?php namespace App\Modules\Offline\Controllers;

use App, View, Session,Auth,URL,Input,Datatables,Redirect;
use App\Modules\Settings\Models\Setting;

class OfflineController extends \AdminController {
		
		public function getIndex() {
		// Show the page
		$offline = $offlinemessage ="";
		$settings = Setting::whereIn('varname',array('offlinemessage','offline'))->get();
		foreach ($settings as $v) {
				if ($v -> varname == 'offlinemessage') {
					$offlinemessage = $v -> value;
				}
				if ($v -> varname == 'offline') {
					$offline = $v -> value;
				}
		}
		$title = "A2Z CMS is temporary offline";
		if($offline=='No')
		{
			header('Location: '. URL::to('/'));
			exit ;
		}
		return View::make('offline::index',compact('offlinemessage','title'));
	}
}
	