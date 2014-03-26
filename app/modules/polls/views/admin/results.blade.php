@extends('layouts.admin.default')

{{-- Web site Title --}}
@section('title')
{{{ $title }}} ::
@stop

{{-- Content --}}
@section('content')
	<div class="row">
		<div class="page-header">
			<h1><h1> {{{ $title }}}: <b>{{$poll->title}}</b></h1>
		</div>
		<div class="pull-right">
			<a class="btn btn-small btn-info" href="{{URL::to('admin/polls/index')}}">
				<span class="icon-share-alt icon-white"></span> Back</a>
		</div>
		@if (!empty($pollOptions))			   
		<table class="table table-hover">
			<thead>
        <tr>
          <th>Answer</th>
          <th>Votes</th>
          <th>Percentage</th>
        </tr>
	      </thead>
	      <tbody>
			@foreach ($pollOptions as $item)
				<tr>
				<td>{{$item->title}}</td>
				<td>{{$item->votes}}</td>
				<td>{{$item->percentage}}</td>
				</tr>
			@endforeach
	    	</tbody>
		</table>
	@else
    <div class="item_list_empty">
        No items found. 
    </div>
@endif
</div>
@stop