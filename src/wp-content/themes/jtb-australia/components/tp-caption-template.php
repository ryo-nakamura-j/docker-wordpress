<!-- tp-caption -->
<div id="<?php echo $this->id ?>" class="<?php echo $this->class ?> container">
	<div class="row"> 
		<div class="slider-caption col-sm-12">
			<?php echo isset($this->content) ? $this->content : "Content not found" ?>
		</div>
	</div>
</div>

<script>
	$( document ).ready( function(){
		var vue = new Vue({
			el: '#<?php echo $this->id ?>',
			data: {
			}
		})
	})
</script>
