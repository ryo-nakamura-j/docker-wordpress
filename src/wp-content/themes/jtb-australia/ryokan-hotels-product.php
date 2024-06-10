<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 * Template Name: Ryokan & Hotels - Product
 */

get_header(); ?>

    <script>
    function tpLoadGallery() {
            jQuery("#galleryImages").PikaChoose({autoPlay:false});
            jQuery(".pika-image a").css('cursor', 'none').on('click', function() {return false;});
    }
    </script>
                

	<div class="container">
		<div class="row">
			<div class="col-sm-12">
				<?php if ( function_exists('yoast_breadcrumb') ) {
				yoast_breadcrumb('<p id="breadcrumbs">','</p>');
				} ?>
			</div>
		</div>
	</div>

	<section id="content" class="container ryokan-hotel product">
		<div class="row">
			<div class="col-sm-12">
				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				<div class="post">
					<div class="entry">
						<div id="product_content" class="tpproduct-accommodation hotelProduct">	 	 			
						<h1 class="hotelName"><?php echo do_shortcode('[tp-supplier-name]'); ?></h1>	  
						<div class="ribon-red-desktop"></div>	 	 
						<div id="suppliersection">	 	 
							<p class="supAddress">
								<span class="address"><?php echo do_shortcode('[tp-supplier-address line="1"]'); ?></span>
								<span class="address"><?php echo do_shortcode('[tp-supplier-address line="2"]'); ?></span>
								<span class="address"><?php echo do_shortcode('[tp-supplier-address line="3"]'); ?></span>
								<span class="address"><?php echo do_shortcode('[tp-supplier-address line="4"] [tp-supplier-postcode]'); ?>
								<span class="viewMap"><a href="#mapsection">View Map</a></span>	 	 
							</p>	 	 
							<p id="infoNoteTST" class="infoNoteTST"><?php echo do_shortcode('[tp-supplier-note code="TST" deps="#infoNoteTST"]'); ?></p>	 	 
							<p class="shortDesc"><?php echo do_shortcode('[tp-supplier-note code="SDS"]'); ?></p>	 	 
							<p><a href="#longdesc">Read more</a></p>	 
							</div>	 	 

							<div id="gallerysection"><?php echo do_shortcode('[tp-supplier-imagelist callback="tpLoadGallery"]'); ?></div> 
							<div style="clear:both"></div>	 	 	 
							<div class="ribon-red-desktop"></div>	 	 
							<div id="products_section">	 	 
								<h3 id="selectrooms">Select Your Room</h3> 
								<div class="product_content_section_hightlight_gray">
									<div id="productavailabilitysection">
										<div class="detailsTitle">Fill in Details</div>	 	 
									</div>	 	 
									<div id="productssection" class="productssection-accommodation">	 	
										<div class="productsHeader">	 
							 			<div class="productsHeading roomdescription">Room Description</div> 
							 			<div class="productsHeading status">Room Status</div>	 
							 			<div class="productsHeading price">Price (total)</div>	 
										</div>	 	 
									</div>	 	 
								</div>	 	 
							</div>	 	 

							<div class="ribon-red-desktop"></div>


							<div class="product_content_section_highlight row">
								<div class="col-sm-8">
									<div id="hoteldescriptionsection">	 	 
										<h2 id="longdesc">Hotel Description</h2>
										<p class="longdesc"><?php echo do_shortcode('[tp-supplier-note code="LDS"]'); ?></p>	 	 
									</div>
								</div>	 	 

								<div class="col-sm-4">
									<div id="hotelamenitiessection">	 	 
										<h2 id="ntulabel">Amenities:</h2>	 
										<p><em><?php echo do_shortcode('[tp-supplier-amenities]'); ?></em></p>	 	 
									</div>
								</div>

								<div class="col-sm-12">
									<div class="hotelmap">
										<div id="mapsection"><?php echo do_shortcode('[tp-supplier-map code="GEO"]'); ?></div>		 
									</div>
								</div>
							</div>	 	 

							<div class="ribon-red-desktop"></div>	 	 
							<div id="termsconditionssection">	 	 
								<h2>Terms and Conditions</h2>	 	 
								<p>Please read the hotel <a title="booking conditions" href="http://www.nx.jtbtravel.com.au/terms-and-conditions-for-hotels" target="_blank">booking conditions</a> carefully as they incorporate the basis upon which bookings are accepted by JTB Australia Pty Ltd. ("JTB")</p>	 	 
							</div>	 	 
						</div>
					</div>
				</div>
				<?php endwhile; endif; ?>
			</div>

	 		<div class="page-top col-sm-12">
				<div class="ribon-red-desktop"></div>
			</div>
		</div>
	</section>

<?php get_footer(); ?>