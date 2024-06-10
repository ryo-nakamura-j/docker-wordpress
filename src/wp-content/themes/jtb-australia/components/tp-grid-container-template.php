<!-- tp-grid-container -->
<div class="<?php echo $this->class ?> container">
	<?php if( $this->showHeader ) { ?>
		<?php if ( isset( $box->title ) ){ ?>
			<h3><?php echo $this->title ?></h3>
		<?php } ?>
		<div class="ribon-red-desktop"></div>
	<?php } ?>
	<div class="row multi"> 
		<?php foreach( $this->boxList as $box ) { ?>
	  		<?php if ( $this->style == $this->STYLE_STANDARD ){ ?>
			  	<div class=" <?php echo $this->classRow ?> col-xs-12">
					<div class="thumbnail">
				  		<?php if ( isset( $box->box_title ) ){ ?>
						<div class="caption">
							<a href="<?php echo $box->box_link; ?>">
								<h4><?php echo $box->box_title; ?></h4>
							</a>
						</div>
						<?php } ?>
				  		<?php if ( isset( $box->box_image ) ){ ?>
				  		<a href="<?php echo $box->box_link; ?>">
				  			<img src="<?php echo $box->box_image; ?>" alt="...">
				  		</a>
						<?php } ?>
				  		<?php if ( isset( $box->box_copy ) ){ ?>
						<div class="caption">
							<div class="row">
								<div class="col-sm-12 col-md-12">
									<?php echo $box->box_copy; ?>
								</div>
							</div>
					  	</div>
						<?php } ?>
					</div>
				</div>
			<?php } ?>
	  		<?php if ( $this->style == $this->STYLE_FLYING_TEXT ){ ?>
			  	<div class="<?php echo $this->classRow ?> multi">
					<a href="<?php echo $box->box_link ?>"><img src="<?php echo $box->box_image ?>" class="img-responsive fullwidth" alt=""></a>
					<p class="flying-text"><?php echo $box->box_title ?></p>
				</div>
			<?php } ?>
	  		<?php if ( $this->style == $this->STYLE_IMAGE_RESPONSIVE ){ ?>
			  	<div class=" <?php echo $this->classRow ?> ">
					<a href="<?php echo isset($box->link) ? $box->link : ''; ?>"><img src="<?php echo $box->box_image ?>" class="img-responsive" alt=""></a>
				</div>
			<?php } ?>
		<?php } ?>
	</div>
	<?php if( $this->showFooter ) { ?>
	<div class="row">
 		<div class="page-top col-sm-12">
			<p><img src="<?php bloginfo('template_directory'); ?>/images/top-btn.png" alt="gotop">&nbsp;<a href="#top" style="text-decoration: underline;">Page Top</a></p>
		</div>
	</div>
	<?php } ?>
</div>