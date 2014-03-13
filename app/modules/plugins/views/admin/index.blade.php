@extends('layouts.admin.default')

{{-- Web site Title --}}
@section('title')
{{{ $title }}} ::
@stop

{{-- Content --}}
@section('content')
		<div class="page-header">
			<h1>Plugins</h1>
			<br><b>
					* Reorder plugins causes a change in the admin navigation<br>
					** After install some plugin, you need to add role to some user group that 
							they can access to that plugin. User get that roles after first login to website.
				</b>
		</div>
    <table class="table table-hover">
		<thead>
	        <tr>
	          <th>Title</th>
	          <th>Name</th>
	          <th>Installed at</th>
	          <th>Verison</th>	          
	        </tr>
      	</thead>
      	<tbody>
        @foreach ($plugin as $item)
           <tr>
		            <td>{{$item->title}}</td>		            
					<td>{{$item->name}}</td>
					<td>{{$item->pluginversion}}</td>
					<td>{{$item->created_at}}</td>
					<input id="row" type="hidden" value="{{$item->id}}" name="row">					
               </tr>
  		@endforeach
    	</tbody>
	</table>
@stop

{{-- Scripts --}}
@section('scripts')
<script type="text/javascript">
	$(".iframe").colorbox({
					iframe : true,
					width : "50%",
					height : "50%"
				});
	var startPosition;
	var endPosition;
	$(".table tbody").sortable({
		cursor : "move",
		start : function(event, ui) {
			startPosition = ui.item.prevAll().length + 1;
		},
		update : function(event, ui) {
			endPosition = ui.item.prevAll().length + 1;
			var navigationList = "";
			$('.table #row').each(function(i) {
				navigationList = navigationList + ',' + $(this).val();
			});
			$.getJSON("{{URL::to('admin/plugins/reorder')}}", {
				list : navigationList
			});			
		}
	});
</script>
@stop