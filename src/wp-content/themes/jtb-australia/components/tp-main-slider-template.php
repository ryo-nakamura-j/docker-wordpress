<!-- tp-main-slider -->
<div class="<?php echo $this->class ?> container">
	<section id="<?php echo $this->id ?>" class="carousel slide carousel-fade tp-wp-carousel" data-ride="carousel">
	  	<!-- Wrapper for slides -->
	  	<div class="carousel-inner">
	    	<?php $count = 0; ?>
	        <?php foreach( $this->slideList as $slides ) {?>
				<div class="item <?php if($count == 0) echo " active"; ?>">
					<?php if ( isset( $slides->link ) ){ ?>
					<a href='<?php echo isset($slides->url_banner) ? $slides->url_banner : $slides->link; ?>'>
					<?php } ?>
						<img class="hidden-xs" src="<?php echo $slides->slide;?>" alt="slide-<?php echo $count;?>">
						<img class="visible-xs-block" src="<?php echo $slides->slide_mobile; ?>" alt="slide-<?php echo $count; ?>">
					<?php if ( isset( $slides->link ) ){ ?>
					</a>
					<?php } ?>
				</div>
			<?php
				$count++;
			} ?>
		</div>

		<!-- Controls -->
	    <ol class="carousel-indicators">
	    	<?php $count = 0; ?>
	        <?php foreach( $this->slideList as $slides ) {?>
	        	 <li data-target="#<?php echo $this->id ?>" data-slide-to="<?php echo $count;?>" class="<?php if($count == 0) echo "active"; ?>">
	        	 	<img class="hidden-xs" src="<?php echo $slides->slide; ?>" alt="slide-<?php echo $count;?> thumbnail">
	        	 	<img class="visible-xs-block" src="<?php echo $slides->slide_mobile; ?>" alt="slide-<?php echo $count;?> thumbnail">
	        	 </li>
		    <?php 
		    	$count++;
		    } ?>
		</ol>
	</section>
</div>

<?php if( $this->usePremotion ) { ?>
	<script type="text/javascript">
		$(document).ready(function() { 
				dataLayer.push({
			  'ecommerce': {
			    'promoView': {
			      'promotions': <?php echo json_encode($this->promotions); ?>
			    }
			  }
			})});
			function onPromoClick(promoObj) {
			dataLayer.push({
			    'event': 'promotionClick',
			    'ecommerce': {
			      'promoClick': {
			        'promotions': [
			         {
			           'id': promoObj.id,
			           'name': promoObj.name,
			           'position': promoObj.position
			         }]
			      }
			    },
			    'eventCallback': function() {
			      document.location = promoObj.url_promo;
			    }
			});		
		}
	</script>
<?php } ?>