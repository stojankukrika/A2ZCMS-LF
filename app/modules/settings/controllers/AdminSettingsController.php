<?php namespace App\Modules\Settings\Controllers;

use App, View, Session,Auth,Validator,Input,Redirect;
use App\Modules\Settings\Models\Setting;

class AdminSettingsController extends \AdminController {

	/**
	 * Inject the models.
	 * @param Post $post
	 */
	protected $settings;
	
	public function __construct(Setting $settings) {
		parent::__construct();
		$this->settings = $settings;
	}

	/**
	 * Show a list of all the blog posts.
	 *
	 * @return View
	 */
	public function getIndex() {
		//load settings from database
		$settingsgroup = Setting::where('groupname','!=' ,'version')
								->groupBy('groupname')->get();
		foreach ($settingsgroup as $group) {
			$group->items = Setting::where('groupname', $group->groupname)->get();
		}
		$title = "Settings";
		// Show the page
		return View::make('settings::index', compact('title','settingsgroup'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function postIndex() {
		// Declare the rules for the form validation
		$rules = array();
		$settings_role = Setting::where('groupname','!=','version')->where('rule', '!=', '')->get();
		foreach ($settings_role as $item) {
			$rules[$item->varname] = $item->rule;
      	}
		// Validate the inputs
		$validator = Validator::make(Input::all(), $rules);
	
		// Check if the form validates with success
		if ($validator -> passes()) {
				
			$settings = Setting::all();

	        foreach($settings as $setting)
	        {
	            $setting->value = Input::get($setting->varname);
	            $setting->save();
	        }

			// Redirect to the settings page
			return Redirect::to('admin/settings/') -> with('success', 'Success');
		}

		// Form validation failed
		return Redirect::to('admin/settings/') -> withInput() -> withErrors($validator);
	}

}
