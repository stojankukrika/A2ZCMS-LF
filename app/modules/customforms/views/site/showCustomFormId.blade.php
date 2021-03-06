<br>
@if(!empty($showCustomFormId))
 <div class="row">
  	<div class="col-lg-12 col-md-12">
	@foreach($showCustomFormId as $item)
			@if(!empty($showCustomFormId))
				<form action="{{{ URL::to('customform/'.$item->id) }}}" method="post" enctype="multipart/form-data">
				<input type="hidden" name="_token" value="{{{ Session::getToken() }}}" />
				@foreach($showCustomFormFildId[$item->id] as $field)
						<div class="col-lg-6 form-group">
							{{ $field->name }}
						</div>
						<div class="col-lg-6 form-group">
							      @if($field->type == '1')
							            <input type="text" class="form-control" name="{{Str::slug($field->name)}}" value=""/>
							      @elseif($field->type == '2')
							             <textarea  class="form-control" rows="6" name="{{Str::slug($field->name)}}" /></textarea>
							      @elseif($field->type == '3')
							            <select class="form-control" name="{{Str::slug($field->name)}}">
							            	<?php
								            	$options = rtrim($field->options, ";");
												$options = explode(';', $options);
												foreach ($options as $value) {
													echo "<option value='".Str::slug($value)."'>".$value."</option>";
												}
							            	?>
							            </select>
							      @elseif($field->type == '4')
					      			<?php
						            	$options = rtrim($field->options,";");
										$options = explode(';', $options);
										foreach ($options as $value) {
											echo "<input  class='form-control' type='radio' name='".Str::slug($field->name)."' value='".Str::slug($value)."'>".$value."<br>";
										}
					            	?>
							      @elseif($field->type == '5')
							         	<input type="file"  class="form-control" name="{{Str::slug($field->name)}}" value="">
							      @elseif($field->type == '6')
							         <?php
						            	$options = rtrim($field->options,";");
										$options = explode(';', $options);
										foreach ($options as $value) {
											echo "<input  class='form-control' type='checkbox' name='".Str::slug($field->name)."' value='".Str::slug($value)."'>".$value."<br>";
										}
					            	?>
					              @endif						
						</div>
				@endforeach
				<input class="btn btn-primary" type="submit" value="Submit">
				</form>
			@endif
	@endforeach
	</div>	
</div> 
<hr>
@endif
