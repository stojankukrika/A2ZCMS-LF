@extends('layouts.admin.default')

{{-- Web site Title --}}
@section('title')
{{{ $title }}} ::
@stop

{{-- Content --}}
@section('content')
<div class="page-header">

	{{ Form::open() }}
	<div class="col-md-12">
		<div class="box">
			<div class="box-header">
				<h2><i class="icon-th"></i><span class="break"></span>{{{ $title }}}</h2>
				<ul id="myTab" class="nav tab-menu nav-tabs">
					@foreach ($settingsgroup as $group)
						<li><a href="#{{ $group -> groupname}}"> {{ ucfirst($group -> groupname) }}</a></li>
					@endforeach
				</ul>
			</div>
			<div class="box-content">
				<div class="tab-content" id="myTabContent">
				@foreach ($settingsgroup as $group)
					<div class="tab-pane active" id="{{$group -> groupname}}">				
					@foreach ($group ->items as $item)
						<div class="form-group">
							<div class="col-md-12">
								@if ($item->type == 'text' || $item->type == 'date')
									{{Form::label(@$item->varname, @$item->vartitle, array('class' => 'control-label'))}}
									{{Form::text(@$item->varname, Input::old(@$item->varname, @$item->value) , array('class' => 'form-control'))}}
									{{ $errors->first(@$item->varname, '<span class="help-inline">:message</span>') }}
							    @elseif($item->type == 'textarea')
							        {{Form::label(@$item->varname, @$item->vartitle, array('class' => 'control-label'))}}
									{{Form::textarea(@$item->varname, Input::old(@$item->varname, @$item->value) , array('class' => 'form-control'))}}
									{{ $errors->first(@$item->varname, '<span class="help-inline">:message</span>') }}
							    @elseif($item->type == 'radio')							    
							    	{{Form::label(@$item->varname, @$item->vartitle, array('class' => 'control-label'))}}
							    	<?php
							    	$variables = explode(";", $item -> defaultvalue);
									foreach ($variables as $variable) {?>
							    	<label class="radio">
										{{ Form::radio(@$item->varname, 1, (Input::old(@$item->varname) == $variable  || @$item->value == $variable) ? true : false, array('id'=>@$item->varname, 'class'=>'radio')) }}
										{{$variable}}
									</label>
									<?php } ?>
								@elseif($item->type == 'option')
								<?php
								$options= array();
								if (strpos($item -> defaultvalue, ';') === false) {
									foreach (glob(base_path() . constant($item -> defaultvalue) . '/*', GLOB_ONLYDIR) as $dir) {
										$dir = str_replace(base_path() .constant($item -> defaultvalue).'/', '', $dir);
										$options[$dir] = ucfirst($dir);
									}
								} else {
									$variables = explode(";", $item -> defaultvalue);
									foreach ($variables as $variable) {
										$options[$variable] = $variable;
									}
								}
								?>
							       {{ Form::select(@$item->varname, $options, Input::old(@$item->varname, @$item->value), array('class' => 'form-control')) }}
							    @elseif($item->type == 'checkbox')
							     {{Form::label(@$item->varname, @$item->vartitle, array('class' => 'control-label'))}}
							    	<?php
							    	$variables = explode(";", $item -> defaultvalue);
									foreach ($variables as $variable) {?>
							    	<label class="checkbox">
										{{ Form::checkbox(@$item->varname, 1, (Input::old(@$item->varname) == $variable  || @$item->value == $variable) ? true : false, array('id'=>@$item->varname, 'class'=>'radio')) }}
										{{$variable}}
									</label>
									<?php } ?>
							    @elseif($item->type == 'password')
							        {{Form::label(@$item->varname, @$item->vartitle, array('class' => 'control-label'))}}
									{{Form::password(@$item->varname, Input::old(@$item->varname, @$item->value) , array('class' => 'form-control'))}}
									{{ $errors->first(@$item->varname, '<span class="help-inline">:message</span>') }}
							    @endif
							</div>
						</div>
					@endforeach
					</div>
				@endforeach
				</div>
				<div class="form-group">
					<div class="col-md-12">
						<button type="reset" class="btn btn-link close_popup">
							<span class="icon-remove"></span>  {{{ Lang::get('admin/general.cancel') }}}
						</button>
						<button type="reset" class="btn btn-default">
							<span class="icon-refresh"></span> {{{ Lang::get('admin/general.reset') }}}
						</button>
						<button type="submit" class="btn btn-success">
							<span class="icon-ok"></span> @if (isset($settings)){{{ Lang::get('admin/general.update') }}} @else {{{ Lang::get('admin/general.create') }}} @endif
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	{{ Form::close() }}
</div>
@stop
@section('scripts')
<script>
	$(function() {
		$("#tabs").tabs();
		$('#useravatwidth,#useravatheight,#shortmsg,#pageitem').keyup(function () { 
			    this.value = this.value.replace(/[^0-9\.]/g,'');
			});
	}); 
</script>
@stop
