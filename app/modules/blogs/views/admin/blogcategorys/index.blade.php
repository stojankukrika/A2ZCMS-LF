@extends('layouts.admin.default')

{{-- Web site Title --}}
@section('title')
{{{ $title }}} ::
@stop

{{-- Content --}}
@section('content')
<div class="page-header">
	<h1> {{{ $title }}}
	<div class="pull-right">
		<a href="{{{ URL::to('admin/blogs/blogcategorys/create') }}}" class="btn btn-small btn-info iframe">
			<span class="icon-plus-sign icon-white"></span> {{{ Lang::get('admin/general.create') }}}</a>
	</div></h1>
</div>
<table id="comments" class="table table-striped table-hover">
	<thead>
		<tr>
			<th class="col-md-3">{{{ Lang::get('admin/blogcategorys/table.title') }}}</th>
			<th class="col-md-3">{{{ Lang::get('admin/blogcategorys/table.no_of_blogs') }}}</th>
			<th class="col-md-2">{{{ Lang::get('admin/blogcategorys/table.created_at') }}}</th>
			<th class="col-md-2">{{{ Lang::get('table.actions') }}}</th>
		</tr>
	</thead>
</table>
@stop

{{-- Scripts --}}
@section('scripts')
<script type="text/javascript">
	var oTable;
	$(document).ready(function() {
		oTable = $('#comments').dataTable({
			"sDom" : "<'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
			"sPaginationType" : "bootstrap",
			"oLanguage" : {
				"sLengthMenu" : "_MENU_ {{{ Lang::get('admin/general.records_per_page') }}}"
			},
			"bProcessing" : true,
			"bServerSide" : true,
			"sAjaxSource" : "{{ URL::to('admin/blogs/blogcategorys/data') }}",
			"fnDrawCallback" : function(oSettings) {
				$(".iframe").colorbox({
					iframe : true,
					width : "80%",
					height : "80%"
				});
			}
		});
	}); 
</script>
@stop