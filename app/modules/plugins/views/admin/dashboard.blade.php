@extends('layouts.admin.default')
{{-- Content --}}
@section('content')
		<div class="page-header">
			<h1>Admin panel</h1>
			<b>Welcome to admin panel in A2Z CMS!</b>
		</div>
			@foreach($plugin as $item)
			<div class="col-lg-3 col-sm-6 col-xs-6 col-xxs-12">
				<div class="smallstat box">
					<i class="{{$item->icon}} {{$item->background_color}}">&nbsp;</i>
					<span class="title">{{$item->title}}<br><br></span>
					<a href="{{ URL::to('admin/'.$item->name)}}" class="more">
						<span>View More</span>
						<i class="icon-chevron-right"></i>
					</a>
				</div>
			</div>
			@endforeach
@stop