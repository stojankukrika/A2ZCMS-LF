<?php namespace App\Modules\Polls\Controllers;

use App, View, Session,Auth,Validator,Input,Redirect;

use App\Modules\Polls\Models\Poll;
use App\Modules\Polls\Models\Polloption;
use App\Modules\Polls\Models\Pollvote;

use Request;
class PollsController extends \BaseController {

 /*function for plugins*/
	public function activePoll(){
		$poll = Poll::where('active','1')->select(array('id','title'))->first();		
		$poll_options = Polloption::where('poll_id',$poll->id)->orderBy('order','ASC')->select(array('id','title','votes'))->get();
		
		$uservoted = (Pollvote::join('poll_options', 'poll_options.id','=', 'poll_votes.option_id')
				->where('poll_id',$poll->id)
				->where('ip_address',Request::getClientIp())->count()>0)?false:true;	
				
		return View::make('polls::site.activePoll', compact('poll','poll_options','uservoted'));
	}
	
	public function getPoll()
	{
		$page = \App\Modules\Pages\Models\Page::first();
		$pagecontent = \BaseController::createSiderContent($page->id);
		
		// Show the page
		$data['sidebar_right'] = $pagecontent['sidebar_right'];
		$data['sidebar_left'] = $pagecontent['sidebar_left'];
		$data['page'] = $page;
		
		$poll = Poll::where('active','1')->select(array('id','title'))->first();		
		$data['poll_options'] = Polloption::where('poll_id',$poll->id)->orderBy('order','ASC')->select(array('id','title','votes'))->get();
		
		$data['uservoted'] = (Pollvote::join('poll_options', 'poll_options.id','=', 'poll_votes.option_id')
				->where('poll_id',$poll->id)
				->where('ip_address',Request::getClientIp())->count()>0)?false:true;	
		$data['poll'] = $poll;	
		return View::make('polls::site.index', $data);
	}
	
	public function postPoll()
	{
		$rules = array('vote' => 'required');

		// Validate the inputs
		$validator = Validator::make(Input::all(), $rules);

		// Check if the form validates with success
		if ($validator -> passes()) {
			
			$pollvote = new Pollvote;
			$pollvote->option_id = Input::get('vote');
			$pollvote->ip_address = Request::getClientIp();
			$pollvote->save();
			
			$polloption = Polloption::find(Input::get('vote'));
			$polloption->votes = $polloption->votes+1;
			$polloption->save();
		}
		return Redirect::to('/');
	}	
	
	
}