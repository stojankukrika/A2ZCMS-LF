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
		<h3><a href="{{{ URL::to('gallery/'.$gallery->id) }}}">{{ String::title($gallery->title) }}</a></h3>
	</div>
<div class="row">
@foreach ($gallery_images as $item)
	<div class="col-md-3 portfolio-item">
      	<a href="{{{ URL::to('gallery/galleryimage/'.$gallery->id.'/'.$item->id) }}}">
      		<img src="{{ URL::to('')}}/gallery/{{{$gallery->folderid}}}/thumbs/{{{ $item->content }}}" height="85px" width="150px" class="img-responsive">
      	</a>
      </div>
     @endforeach	
	</div>
	<p id="vote">{{ Lang::get("site.num_of_votes") }} <span id="countvote">{{$gallery->voteup-$gallery->votedown}}</span> 
		@if (!isset($post_image_vote))
		<br><b><i>{{ Lang::get('site.add_votes_permission') }}</i></b>
		@else	
		<span style="display: inline-block;" onclick="contentvote('gallery','1','gallery',{{$gallery->id}},'countvote')" class="up"></span>
		<span style="display: inline-block;" onclick="contentvote('gallery','0','gallery',{{$gallery->id}},'countvote')" class="down"></span>
		@endif
	</p>
	<ul class="pager">
		{{ $gallery_images->links() }}
	</ul>
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
