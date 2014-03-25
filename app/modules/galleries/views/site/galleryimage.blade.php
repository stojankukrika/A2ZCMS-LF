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
      <img src="{{ URL::to('')}}/gallery/{{{$gallery->folderid}}}/{{{ $gallery_image->content }}}" class="img-responsive">        
     <hr>
     <p id="vote">{{ Lang::get("site.num_of_votes") }} <span id="countvote">{{$gallery->voteup-$gallery->votedown}}</span> 
		@if (!isset($post_image_vote))
		<br><b><i>{{ Lang::get('site.add_votes_permission') }}</i></b>
		@else	
		<span style="display: inline-block;" onclick="contentvote('gallery','1','galleryimage',{{$gallery_image->id}},'countvote')" class="up"></span>
		<span style="display: inline-block;" onclick="contentvote('gallery','0','galleryimage',{{$gallery_image->id}},'countvote')" class="down"></span>
		@endif
	</p>
<!-- the comment box -->
  <div class="well">            
	<h4>{{{ $gallery_comments->count() }}} {{ Lang::get('site/blog.comments') }}</h4>

	@if ($gallery_comments->count())
	@foreach ($gallery_comments as $comment)
		<h4>
			<b>{{{ $comment->author->username }}}</b>
			<small>	{{{ $comment->date() }}} || Numer of votes <span id="commentcountvote{{$comment->id}}">
		 	{{ $comment->voteup-$comment->votedown}}</span>
		 	@if (!isset($post_image_vote))
			<br><b><i>{{ Lang::get('site.add_votes_permission') }}</i></b>
			@else				
			<span style="display: inline-block;" onclick="contentvote('gallery','1','gallerycomment','{{$comment->id}}','commentcountvote{{$comment->id}}')" class="up"></span>
			<span style="display: inline-block;" onclick="contentvote('gallery','0','gallerycomment','{{$comment->id}}','commentcountvote{{$comment->id}}')" class="down"></span>
			@endif
		</small>
		</h4>
          <p>{{ $comment->content() }}</p>

	@endforeach
	@else
	<hr />
	@endif
</div>
@if ( ! Auth::check())
{{ Lang::get('site/gallery.need_to_login') }}
<br />
<br />
{{ Lang::get('site/blog.click') }} <a href="{{{ URL::to('user/login') }}}">{{ Lang::get('site/blog.here') }}</a> {{ Lang::get('site/blog.to_login') }}
@elseif (!isset($post_gallery_comment))
<br><b></i>{{ Lang::get('site/blog.add_comment_permission') }}</i></b>
@else
@if($errors->has())
<div class="alert alert-danger alert-block">
	<ul>
		@foreach ($errors->all() as $error)
		<li>
			{{ $error }}
		</li>
		@endforeach
	</ul>
</div>
@endif
<div class="new_comment">
	<h4>{{ Lang::get('site/blog.add_comment') }}</h4>
	<form method="post" action="{{{ URL::to('gallery/galleryimage/'.$gallery->id.'/'.$gallery_image->id) }}}">
		<input type="hidden" name="_token" value="{{{ Session::getToken() }}}" />
			<div class="form-group">
				<textarea class="form-control" name="gallcomment" id="gallcomment" placeholder="gallcomment" rows="7">{{{ Request::old('gallcomment') }}}</textarea>
				<label id="characterLeft"></label>
			</div>
			<div class="form-group">
				<button type="submit" class="btn btn-success">{{ Lang::get('site.submit') }}</button>
			</div>
	</form>
</div>
@endif
</div>
</div>
@stop
{{-- Sidebar right --}}
@section('sidebar_right')
<@if(!empty($sidebar_right))
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

{{-- Scripts --}}
@section('scripts')
@stop