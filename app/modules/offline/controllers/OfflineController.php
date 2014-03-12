<?php namespace App\Modules\Offline\Controllers;

use App, View, Session,Auth,URL,Input,Datatables,Redirect;

class OfflineController extends \AdminController {
		
		public function getIndex() {
		
		$settings = new \App\Modules\Settings\Models\SlugService;
		$offline = $settings::where('groupname','=' ,'offlinemessage')->get();
		$title = "Website is temporary offline";
		// Show the page
		return View::make('offline::admin/index', compact('offline','title'));
	}
}
	