@if(!empty($poll))
	@if($uservoted)
		<form method="POST" action="{{ URL::to('polls/vote')}}" accept-charset="UTF-8">
		<input type="hidden" name="_token" value="{{{ csrf_token() }}}" />	
		<input type="hidden" name="pollid" value="{{$poll->id}}">
		<h4>{{$poll->title}}</h4>
		  <div class="row">
		    <div class="col-lg-12">
		      	@if(!empty($poll_options))
		      		@foreach($poll_options as $item)
			    	<label class="radio">
						{{ Form::radio('vote', @$item->id, false, array('id'=>@$item->id, 'class'=>'radio')) }}
						{{$item->title}}
					</label>
					@endforeach
				@endif
		     </div>
		     <div class="form-group">
				<div class="col-md-12">
					<button type="submit" class="btn btn-success">
						<span class="icon-ok"></span>Vote
					</button>
				</div>
			</div>
		  </div>
		  </form>	
	@else
		<h4>{{$poll->title}}</h4>
		<div class="row">
	    <div class="col-lg-12"><ul>
	      	@if(!empty($poll_options))
	      		@foreach($poll_options as $item)
					<li><b>{{$item->title}}<span class="label label-success">{{$item->votes}}</span><b></li>	
				@endforeach	
			@endif
			</ul></div></div>		
	@endif
@endif