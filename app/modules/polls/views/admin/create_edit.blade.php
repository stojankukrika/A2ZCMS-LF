@extends('layouts.admin.modal')
{{-- Content --}}
@section('content')
<!-- Tabs -->
<ul class="nav nav-tabs">
	<li class="active">
		<a href="#tab-general" data-toggle="tab">{{{ Lang::get('admin/general.general') }}}</a>
	</li>
	<li class="">
		<a href="#tab-dates" data-toggle="tab">{{{ Lang::get('admin/polls/table.fields') }}}</a>
	</li>
</ul>
<!-- ./ tabs -->

{{-- Edit Custom Form --}}
<form class="form-horizontal" enctype="multipart/form-data"  method="post" action="@if (isset($customform)){{ URL::to('admin/polls/' . $poll->id . '/edit') }}@endif" autocomplete="off">
	<!-- CSRF Token -->
	<input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
	<!-- ./ csrf token -->
	<!-- Tabs Content -->
	<div class="tab-content">
		<!-- General tab -->
		<div class="tab-pane active" id="tab-general">
			<!-- Title -->
			<div class="form-group {{{ $errors->has('title') ? 'error' : '' }}}">
				<div class="col-md-12">
					<label class="control-label" for="title">{{{ Lang::get('admin/general.title') }}}</label>
					<input class="form-control" type="text" name="title" id="title" value="{{{ Input::old('title', isset($poll) ? $poll->title : null) }}}" />
					{{{ $errors->first('title', '<span class="help-inline">:message</span>') }}}
				</div>
			</div>
			<!-- ./ title -->

			<!-- Recievers -->
			<div class="form-group {{{ $errors->has('recievers') ? 'error' : '' }}}">
				<div class="col-md-12">
					<label class="control-label" for="active">{{{ Lang::get('admin/polls/table.active') }}} </label>
					<select class="form-control" name="active" id="active">
						<option value="1"{{{ (Input::old('active', isset($poll) ? $poll->active : null) === 1 ? ' selected="selected"' : '') }}}>{{{ Lang::get('general.yes') }}}</option>
						<option value="0"{{{ (Input::old('active', isset($poll) ? $poll->active : null) === 0 ? ' selected="selected"' : '') }}}>{{{ Lang::get('general.no') }}}</option>
					</select>
				</div>
			</div>
			<!-- ./ resource recievers -->
			
		</div>
		<!-- ./ general tab -->
		
		<!-- Custom form filds tab -->
		<div class="tab-pane" id="tab-dates">
			<a class="btn btn-link" id="add" href="#"><i class="icon-plus-sign"></i> {{Lang::get('admin/polls/table.add_filed')}}</a>
			<div id="fields">
				<div class="row responsive-utilities-test">
					<div class="col-md-10 col-xs-10" id="form_fields">
						<ul id="sortable1">
							<input type="hidden" value="{{ (isset($poll_options_count))?$poll_options_count:0}}" name="count" id="count">
							<input type="hidden" value="" name="pagecontentorder" id="pagecontentorder">
							<div class="clearfix"></div>
							<?php $id=1;?>
							@if(!empty($polloption))
								@foreach($polloption as $item)							
									<li class="ui-state-default" name="formf" value="<?=$item->id?>" id="formf{{@$item->id}}">
										<label class="control-label" for="name">Answer title</label>
										<input id="id<?=$item->id?>" type="hidden" value="{{@$item->id}}" name="id{{@$item->id}}">
										<input id="title{{@$item->id}}" type="text" value="{{@$item->title}}" name="title{{@$item->id}}">	
										<a class="btn btn-default btn-sm btn-small remove">
											<span class="icon-trash">
												<input type="hidden" value="{{@$item->id}}" class="remove" name="remove">
											</span>
										</a>										
									</li>
									<?php $id++; ?>
								@endforeach
							@endif
						</ul>
					</div>
				</div>
			</div>
		</div>
		<!-- ./ custom form filds tab -->
	</div>
	<!-- ./ tabs content -->
	<!-- Form Actions -->	
	<div class="form-group">
		<div class="col-md-12">
			<button type="reset" class="btn btn-link close_popup">
				<span class="icon-remove"></span>  {{{ Lang::get('admin/general.cancel') }}}
			</button>
			<button type="reset" class="btn btn-default">
				<span class="icon-refresh"></span> {{{ Lang::get('admin/general.reset') }}}
			</button>
			<button type="submit" class="btn btn-success">
				<span class="icon-ok"></span> @if (isset($poll)){{{ Lang::get('admin/general.update') }}} @else {{{ Lang::get('admin/general.create') }}} @endif
			</button>
		</div>
	</div>
	<!-- ./ form actions -->
</form>
	<div class="hidden" id ="addfield">
	<li id="formf" class="ui-state-default" name="formf" value="formf">
		<label class="control-label" for="name">Answer title</label>
		<input id="title" type="text" value="" name="title">		
		<a class="btn btn-default btn-sm btn-small remove">
			<span class="icon-trash">
			</span>
		</a>									
	</li>
</div>
@stop
	@section('scripts')
	<script type="text/javascript">
	var clicked = 0;
	@if(isset($poll))
		$('.remove').click(function(){
			clicked = $(this).children().children().val();
			$.ajax({
		            url : "{{ URL::to('admin/polls/' . $poll->id . '/deleteitem') }}"
		            , type : "post"
		            , data : { id : clicked, _token: "{{{ csrf_token() }}}" }
		            , success : function(resp){
		            	 if( resp == 0){
		            	 	$("#formf"+clicked).remove();
		            	 	//window.location.replace("");
		                }
		            }
		        	});
			});	 
	@endif	 
		$(function() {
		var formfild =$('#addfield').html();
		$("#add").click(function(){
			var count = 0;
			
			count = parseInt( $('#count').val(), 10) + 1;;
			
			formfild = formfild.split('<li id="formf" class="ui-state-default" name="formf" value="formf">').join('<li id="formf'+count+'" class="ui-state-default" name="formf'+count+'" value="'+count+'" >');
			formfild = formfild.split('<input id="title" value="" name="title" type="text">').join('<input id="title'+count+'" value="" name="title'+count+'" type="text">');
			formfild = formfild.split('<a class="btn btn-default btn-sm btn-small remove">').join('<a class="btn btn-default btn-sm btn-small remove id-'+count+'">');


			$("#sortable1").append(formfild);
			$('#count').val(count);
			
			$('.id-'+count).children().append('<input class="remove" type="hidden" name="remove" value="'+count+'">');
			
		})
			$( "#sortable1" ).sortable();
			
			$('.btn-success').click(function(){
		 	var neworder = new Array();
	        $('#sortable1 li').each(function() { 
	        	var title  = $(this).children('[name^="title"]').attr("value");
	        	neworder.push(title);
	            var id  = $(this).children('[name^="id"]').attr("value");
	            neworder.push(id);
	        });
	        $('#pagecontentorder').val(neworder);
        });
		});
	</script>
	@stop
