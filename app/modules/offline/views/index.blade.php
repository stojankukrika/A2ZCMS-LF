@extends('layouts.site.offline')
{{-- Content --}}
@section('title')
{{$title}}
@stop
@section('content')
<div class="page-header">
	<div class="col-md-12">
		<div class="box">
			<h1>{{$title}}</h1>
			{{$offlinemessage}}
		</div>
	</div>
</div>
@stop