@extends('layouts.admin.modal')

@section('scripts')
<script type="text/javascript">
	$(function() {
		$("#link_type").change(function() {
			var link_type = $(this).val();
			$(".link_type").hide();
			$("#" + link_type).show();

		}).change();
	}); 
</script>

@stop

{{-- Content --}}
@section('content')
<ul class="nav nav-tabs">
	<li class="active">
		<a href="#tab-general" data-toggle="tab">{{{ Lang::get('admin/general.general') }}}</a>
	</li>
</ul>
{{-- Edit Page Form --}}
<!-- <form class="form-horizontal" method="post" action="{{ URL::to('admin/navigation/create') }}" > -->
<form class="form-horizontal" method="post" action="@if (isset($navigationGroup)){{ URL::to('admin/pages/navigation/' . $navigationGroup->id . '/edit') }}@endif" >
	<!-- CSRF Token -->
	<input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
	<!-- ./ csrf token -->

	<!-- Tabs Content -->
	<div class="tab-content">
		<!-- General tab -->
		<div class="tab-pane active" id="tab-general">
			<!-- Title -->
			<div class="form-group {{{ $errors->has('title') ? 'error' : '' }}}">
				<div class="col-lg-12">
					<label class="control-label col-lg-2" for="title">{{{ Lang::get('admin/general.title') }}}</label>
					{{ Form::text('title', Input::old('title', isset($navigation) ? $navigation->title : null), array('class' => 'form-control input-sm')) }}
					<span class="help-inline">{{{ $errors->first('title', ':message') }}}</span>
				</div>
			</div>
			<!-- ./ Title -->

			<!-- navigation parent -->
			<div class="form-group {{{ $errors->has('parent') ? 'error' : '' }}}">
				<div class="col-lg-12">
				<label class="control-label col-lg-2" for="parent">{{{ Lang::get('admin/navigation/table.parent') }}}</label>
					{{ Form::select('parent', array('' => Lang::get("admin/navigation/table.select_page") ) + (array)$navigationList, !empty($navigation->parent) ? $navigation->parent  : '', array('class' => 'form-control input-sm')) }}
					<span class="help-inline">{{Lang::get("admin/navigation/table.select_page_info")}}</span>
					<span class="help-inline">{{{ $errors->first('parent', ':message') }}}</span>
				</div>
			</div>
			<!-- ./ navigation parent -->

			<!-- navigation parent -->
			<div class="form-group {{{ $errors->has('link_type') ? 'error' : '' }}}">
				<div class="col-lg-12">
					<label class="control-label col-lg-2" for="link_type">{{{ Lang::get('admin/navigation/table.link_type') }}}</label>
					{{ Form::select('link_type', array('page' => Lang::get("admin/navigation/table.page"), 'url' => Lang::get("admin/navigation/table.external_link"), 'uri' => Lang::get("admin/navigation/table.site_link")), Input::old('link_type', isset($navigation) ? $navigation->link_type : ''), array('id' => 'link_type', 'class' => 'form-control input-sm')) }}
					<span class="help-inline">{{{ $errors->first('link_type', ':message') }}}</span>
				</div>
			</div>
			<!-- ./ navigation link_type -->

			<!-- navigation page_id -->
			<div id="page" style="display: none;" class="form-group link_type {{{ $errors->has('page_id') ? 'error' : '' }}}">
				<div class="col-lg-12">
					<label class="control-label col-lg-2" for="page_id">{{{ Lang::get('admin/navigation/table.page') }}}</label>
					{{ Form::select('page_id', array('' => Lang::get("admin/navigation/table.select_page")) + $pageList, Input::old('page_id', isset($navigation) ? $navigation->page_id : ''), array('class' => 'form-control input-sm')) }}
					<span class="help-inline">{{{ $errors->first('page_id', ':message') }}}</span>
				</div>
			</div>
			<!-- ./ navigation page_id -->

			<!-- URL -->
			<div id="url" style="display: none;" class="form-group link_type {{{ $errors->has('url') ? 'error' : '' }}}">
				<div class="col-lg-12">
					<label class="control-label col-lg-2" for="url">{{{ Lang::get('admin/navigation/table.url') }}}</label>
					{{ Form::text('url', Input::old('url', isset($navigation) ? $navigation->url : null), array('class' => 'form-control input-sm')) }}
					<span class="help-inline">{{{ $errors->first('url', ':message') }}}</span>
				</div>
			</div>
			<!-- ./ URL -->

			<!-- URL -->
			<div id="uri" style="display: none;" class="form-group link_type {{{ $errors->has('uri') ? 'error' : '' }}}">
				<div class="col-lg-12">
					<label class="control-label col-lg-2" for="uri">{{{ Lang::get('admin/navigation/table.uri') }}}</label>
					{{ Form::text('uri', Input::old('uri', isset($navigation) ? $navigation->uri : null), array('class' => 'form-control input-sm')) }}
					<span class="help-inline">{{{ $errors->first('uri', ':message') }}}</span>
				</div>
			</div>
			<!-- ./ URL -->

			<!-- navigation navigation_group_id -->
			<div class="form-group {{{ $errors->has('navigation_group_id') ? 'error' : '' }}}">
				<div class="col-lg-12">
					<label class="control-label col-lg-2" for="navigation_group_id">{{{ Lang::get('admin/navigation/table.navigation_group') }}}</label>
					{{ Form::select('navigation_group_id', array('' => Lang::get("admin/navigation/table.select_group")) + (array)$navigationGroupList, Input::old('page_id', isset($navigation) ? $navigation->navigation_group_id : ''), array('class' => 'form-control input-sm')); }}
					<span class="help-inline">{{{ $errors->first('navigation_group_id', ':message') }}}</span>
				</div>
			</div>
			<!-- ./ navigation navigation_group_id -->

			<!-- navigation target -->
			<div class="form-group {{{ $errors->has('target') ? 'error' : '' }}}">
				<div class="col-lg-12">
					<label class="control-label col-lg-2" for="target">{{{ Lang::get('admin/navigation/table.target') }}}</label>
					{{ Form::select('target', array('_self'=>'Self', '_blank' => Lang::get("admin/navigation/table.blank_page")), Input::old('page_id', isset($navigation) ? $navigation->target : ''), array('class' => 'form-control input-sm')) }}
					<span class="help-inline">{{{ $errors->first('target', ':message') }}}</span>
				</div>
			</div>
			<!-- ./ navigation target -->

			<!-- Css Class -->
			<div class="form-group {{{ $errors->has('class') ? 'error' : '' }}}">
				<div class="col-lg-12">
					<label class="control-label col-lg-2" for="class">{{{ Lang::get('admin/navigation/table.class') }}}</label>
					{{ Form::text('class', Input::old('class', isset($navigation) ? $navigation->class : null), array('class' => 'form-control input-sm')) }}
					<span class="help-inline">{{Lang::get("admin/navigation/table.class_info")}}</span>
					<span class="help-inline">{{{ $errors->first('class', ':message') }}}</span>
				</div>
			</div>
			<!-- ./ Css Class -->

		</div>
		<!-- ./ general tab -->

	</div>
	<!-- ./ tabs content -->

	<!-- Form Actions -->
	<div class="form-group">
		<div class="col-md-12">
			<button type="reset" class="btn btn-warning close_popup">
				<span class="icon-remove"></span>  {{{ Lang::get('admin/general.cancel') }}}
			</button>
			<button type="reset" class="btn btn-default">
				<span class="icon-refresh"></span> {{{ Lang::get('admin/general.reset') }}}
			</button>
			<button type="submit" class="btn btn-success">
				<span class="icon-ok"></span> @if (isset($navigation)){{{ Lang::get('admin/general.update') }}} @else {{{ Lang::get('admin/general.create') }}} @endif
			</button>
		</div>
	</div>
	<!-- ./ form actions -->
</form>
@stop
