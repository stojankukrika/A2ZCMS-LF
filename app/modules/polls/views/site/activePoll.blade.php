<?php
if(!empty($poll)){
	if($uservoted){
		?>
		<form method="POST" action="{{ URL::to('polls/vote')}}" accept-charset="UTF-8">
		<input type="hidden" name="pollid" value="<?=$poll->id?>">
		<h4>{{$poll->title}}</h4>
		  <div class="row">
		    <div class="col-lg-12">
		      	<?php
		      	if(!empty($poll_options)){
		      		foreach($poll_options as $item){?>
			    	<label class="radio">
						{{ Form::radio(@$item->id, @$item-id, (Input::old(@$item->title) == $title  || @$item->title == $title) ? true : false, array('id'=>@$item->$title, 'class'=>'radio')) }}
						{{$variable}}
					</label>
					<?php } 
				}?>
		     </div>
		     <div class="form-group">
				<div class="col-md-12">
					<button type="submit" class="btn btn-success">
						<span class="icon-ok"></span>Vote
					</button>
				</div>
			</div>
		  </div>
		  </form>	
		  <?php
		}
		else {
			echo '<h4>'.$poll->title.'</h4>
			<div class="row">
		    <div class="col-lg-12"><ul>';
		      	if(!empty($poll_options)){
		      		foreach($poll_options as $item){
		      			
						echo '<li><b>'.$item->title.' <span class="label label-success">'.$item->votes.'</span><b></li>';	
					}	
				}	
		    echo'</ul></div></div>';
			
		}
}
?>