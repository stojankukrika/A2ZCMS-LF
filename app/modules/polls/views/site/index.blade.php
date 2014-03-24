@extends('layouts.site.default')

{{-- Page title --}}
@section('page_header')
	@if($page->showtitle==1)
	<h1 class="page-header">
		{{{ $page->name }}}
	</h1>
	@endif
@stop

{{-- Add page scripts --}}
@section('page_scripts')
	<style>
	{{{ $page->page_css }}}
	</style>
	<script>
	{{ $page->page_javascript}}
	</script>
@stop

{{-- Sidebar left --}}
@section('sidebar_left')
@if(!empty($sidebar_left))
<br>
	<div class="col-xs-6 col-lg-4">
	@foreach ($sidebar_left as $item)
	
		  <div class="well">			
			{{ $item['content'] }}
		</div>
	@endforeach 
	</div>
@endif
@stop

{{-- Content --}}
@section('content')
<div class="col-xs-12 col-sm-6 col-lg-8">
<div class="page-header">
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
	</div>
	</div>
@stop


{{-- Sidebar right --}}
@section('sidebar_right')
@if(!empty($sidebar_right))
		<br>
		<div class="col-xs-6 col-lg-4">		 
	@foreach ($sidebar_right as $item)
		  <div class="well">			
			{{ $item['content'] }}
		</div>
	@endforeach 
</div>
	@endif
@stop
