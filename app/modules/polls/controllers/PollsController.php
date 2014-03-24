<?php namespace App\Modules\Polls\Controllers;

use App, View, Session,Auth,Validator,Input,Redirect;
use App\Modules\Polls\Models\Poll;
use App\Modules\Polls\Models\Polloption;

class PollsController extends \BaseController {

 /*function for plugins*/
	public function activePoll(){
		$poll = Poll::where('active','=', '1')->select(array('id','title'))->get();
		foreach ($poll as $id){
			$poll_options = Polloption::where('poll_id',$id->id)->orderBy('order','ASC')->select(array('id','title','votes'))->get();
		}
		$uservoted=false;
				
		return View::make('polls::site.activePoll', compact('poll','poll_options','uservoted'));
	}	
	
	
}