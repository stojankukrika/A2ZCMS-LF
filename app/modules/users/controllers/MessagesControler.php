<?php namespace App\Modules\Users\Controllers;

use App, User, Page,Input, Redirect, View, Confide,Auth, Session,Validator,DB;

use App\Modules\Users\Models\Message;

class MessagesController extends \BaseController {
	
	 /*
	 * User Model
	 * @var User
	 */
	protected $user;
	protected $messages;
	protected $page;
	/**
	 * Inject the models.
	 * @param User $user
	 */
	public function __construct(User $user,Message $messages) {
		parent::__construct();
		$this -> user = $user;
		$this -> messages = $messages;
	}
	/**
	 * Users messages page
	 *
	 * @return View
	 */
	public function getIndex() {
		
		$user = Auth::user();
		$allUsers = User::where('id','<>',$user->id)->get();
		
		$received = $this -> messages -> where('user_id_to','=',$user->id)->where(DB::raw('deleted_at_receiver IS NULL'))->orderBy('id', 'DESC')-> get();
		$send = $this -> messages -> where('user_id_from','=',$user->id)->where(DB::raw('deleted_at_sender IS NULL'))->orderBy('id', 'DESC')-> get();
		
		$page = App\Modules\Pages\Models\Page::first();
		$pagecontent = \BaseController::createSiderContent($page->id);
		
		$data['sidebar_right'] = $pagecontent['sidebar_right'];
		$data['sidebar_left'] = $pagecontent['sidebar_left'];
		$data['page'] = $page;
		$data['user'] =$user;
		$data['received'] = $received;
		$data['send'] =$send;
		$data['allUsers'] =$allUsers;
		
		return View::make('users::messages', $data);
	}
	
	public function postSendmessage() {
		// Declare the rules for the form validation
		$rules = array('content' => 'required|min:3','subject' => 'required|min:3','recipients' => 'required');

		// Validate the inputs
		$validator = Validator::make(Input::all(), $rules);

		// Check if the form validates with success
		if ($validator -> passes()) {
			
			$user = Auth::user();
			
			$this -> messages -> subject = Input::get('subject');
			$this -> messages -> content = Input::get('content');
			$this -> messages -> user_id_from = $user->id;
			foreach (Input::get('recipients') as $recipient) {
				$this -> messages -> user_id_to = $recipient;
				$this -> messages -> save();
			}
		}	

		// Show the page
		return Redirect::to('users/messages');
	}
	
	public function getRead($message_id)
	{
		$message = Message::where('id', '=', $message_id)->first();
		$message->read = 1;
		$message->save();
	}
}
