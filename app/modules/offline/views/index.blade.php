@extends('layouts.offline')

{{-- Web site Title --}}
@section('title')
{{{ $title }}} ::
@stop

{{-- Content --}}
@section('content')
<div class="page-header">
	<div class="col-md-12">
		<div class="box">
			{{$offline}}
		</div>
	</div>
</div>
@stop