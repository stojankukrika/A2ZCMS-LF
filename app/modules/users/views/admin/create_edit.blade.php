@extends('layouts.admin.modal')

{{-- Content --}}
@section('content')
<!-- Tabs -->
<ul class="nav nav-tabs">
	<li class="active">
		<a href="#tab-general" data-toggle="tab">{{{ Lang::get('admin/general.general') }}}</a>
	</li>
</ul>
<!-- ./ tabs -->

{{-- Create User Form --}}
<form class="form-horizontal" method="post" action="@if (isset($user)){{ URL::to('admin/users/' . $user->id . '/edit') }}@endif" autocomplete="off">
	<!-- CSRF Token -->
	<input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
	<!-- ./ csrf token -->

	<!-- Tabs Content -->
	<div class="tab-content">
		<!-- General tab -->
		<div class="tab-pane active" id="tab-general">
			<!-- name -->
			<div class="form-group">
				<label class="col-md-2 control-label" for="name">{{ Lang::get('confide.name') }}</label>
				<div class="col-md-10">
					<input class="form-control" tabindex="1" placeholder="{{ Lang::get('confide.name') }}" type="text" name="name" id="name" value="{{{ Input::old('name', isset($user) ? $user->name : null) }}}">
				</div>
			</div>
			<!-- name -->
			<!-- surname -->
			<div class="form-group">
				<label class="col-md-2 control-label" for="surname">{{ Lang::get('confide.surname') }}</label>
				<div class="col-md-10">
					<input class="form-control" tabindex="2" placeholder="{{ Lang::get('confide.surname') }}" type="text" name="surname" id="surname" value="{{{ Input::old('surname', isset($user) ? $user->surname : null) }}}">
				</div>
			</div>
			<!-- surname -->
			<!-- username -->
			<div class="form-group {{{ $errors->has('username') ? 'error' : '' }}}">
				<label class="col-md-2 control-label" for="username">{{ Lang::get('confide.username') }}</label>
				<div class="col-md-10">
					<input class="form-control" type="text" tabindex="3" placeholder="{{ Lang::get('confide.username') }}" name="username" id="username" value="{{{ Input::old('username', isset($user) ? $user->username : null) }}}" />
					{{{ $errors->first('username', '<span class="help-inline">:message</span>') }}}
				</div>
			</div>
			<!-- ./ username -->

			<!-- Email -->
			<div class="form-group {{{ $errors->has('email') ? 'error' : '' }}}">
				<label class="col-md-2 control-label" for="email">{{ Lang::get('confide.e_mail') }}</label>
				<div class="col-md-10">
					<input class="form-control" type="text" tabindex="4" placeholder="{{ Lang::get('confide.e_mail') }}" name="email" id="email" value="{{{ Input::old('email', isset($user) ? $user->email : null) }}}" />
					{{{ $errors->first('email', '<span class="help-inline">:message</span>') }}}
				</div>
			</div>
			<!-- ./ email -->

			<!-- Password -->
			<div class="form-group {{{ $errors->has('password') ? 'error' : '' }}}">
				<label class="col-md-2 control-label" for="password">{{ Lang::get('confide.password') }}</label>
				<div class="col-md-10">
					<input class="form-control"  tabindex="5" placeholder="{{ Lang::get('confide.password') }}" type="password" name="password" id="password" value="" />
					{{{ $errors->first('password', '<span class="help-inline">:message</span>') }}}
				</div>
			</div>
			<!-- ./ password -->

			<!-- Password Confirm -->
			<div class="form-group {{{ $errors->has('password_confirmation') ? 'error' : '' }}}">
				<label class="col-md-2 control-label" for="password_confirmation">{{ Lang::get('confide.password_confirmation') }}</label>
				<div class="col-md-10">
					<input class="form-control" type="password" tabindex="6" placeholder="{{ Lang::get('confide.password_confirmation') }}"  name="password_confirmation" id="password_confirmation" value="" />
					{{{ $errors->first('password_confirmation', '<span class="help-inline">:message</span>') }}}
				</div>
			</div>
			<!-- ./ password confirm -->

			<!-- Activation Status -->
			<div class="form-group {{{ $errors->has('activated') || $errors->has('confirm') ? 'error' : '' }}}">
				<label class="col-md-2 control-label" for="confirm">{{ Lang::get('confide.activate_user') }}</label>
				<div class="col-md-6">
					@if ($mode == 'create')
					<select class="form-control" name="confirm" id="confirm">
						<option value="1"{{{ (Input::old('confirm', 0) === 1 ? ' selected="selected"' : '') }}}>{{{ Lang::get('general.yes') }}}</option>
						<option value="0"{{{ (Input::old('confirm', 0) === 0 ? ' selected="selected"' : '') }}}>{{{ Lang::get('general.no') }}}</option>
					</select>
					@else
					<select class="form-control" {{{ ($user->
						id === Confide::user()->id ? ' disabled="disabled"' : '') }}} name="confirm" id="confirm"> <option value="1"{{{ ($user->confirmed ? ' selected="selected"' : '') }}}>{{{ Lang::get('general.yes') }}}</option>
						<option value="0"{{{ ( ! $user->confirmed ? ' selected="selected"' : '') }}}>{{{ Lang::get('general.no') }}}</option>
					</select>
					@endif
					{{{ $errors->first('confirm', '<span class="help-inline">:message</span>') }}}
				</div>
			</div>
			<!-- ./ activation status -->

			<!-- Roles -->
			<div class="form-group {{{ $errors->has('roles') ? 'error' : '' }}}">
				<label class="col-md-2 control-label" for="roles">{{ Lang::get('confide.roles') }}</label>
				<div class="col-md-6">
					<select name="roles[]" id="roles" multiple style="width:350px;" >
						@foreach ($roles as $role)
						@if ($mode == 'create')
						<option value="{{{ $role->id }}}"{{{ ( in_array($role->id, $selectedRoles) ? ' selected="selected"' : '') }}}>{{{ $role->name }}}</option>
						@else
						<option value="{{{ $role->id }}}"{{{ ( array_search($role->id, $selectedRoles) !== false && array_search($role->id, $selectedRoles) >= 0 ? ' selected="selected"' : '') }}}>{{{ $role->name }}}</option>
						@endif
						@endforeach
					</select>

					<span class="help-block"> {{ Lang::get('confide.roles_info') }} </span>
				</div>
			</div>
			<!-- ./ roles -->
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
				<span class="icon-ok"></span> @if (isset($user)){{{ Lang::get('admin/general.update') }}} @else {{{ Lang::get('admin/general.create') }}} @endif
			</button>
		</div>
	</div>
	<!-- ./ form actions -->
</form>
@stop
{{-- Scripts --}}
@section('scripts')
<script type="text/javascript">
	$(function() {
		$("#roles").select2() // 0-based index;  
	});
</script>
@stop
