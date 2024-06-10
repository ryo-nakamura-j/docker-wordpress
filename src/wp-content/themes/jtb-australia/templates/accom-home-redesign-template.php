<?php $this->expectedController( "TpProductSearch" )?>

<div id="tp_search_section" class="accomPage" hidden>
	<div class="container">
		<h2 class="ver__h2">{{dataListLabel("ryokan_and_hotels", "Ryokan & Hotels")}}</h2>
	</div>
	<div class="ver__top">
		<div class="top__mainImg">
			<div class="container">
				<div class="mainImg__box">
					<form class="" action="#" method="post" class="frmTop clearfix">
						<!-- Tourplan Search Panel -->
						<span id="tp_search_panel">
							<product-search-panel v-bind:section-config="sectionConfig" show-to-date="true" show-amenities="false" qty-postfix="DOM_MODIFICATION" :is-dst-auto-hide="isDstAutoHide"/>
						</span>
					</form>
				</div>
			</div>
		</div>
		<div class="top__slider">
			<ul class="slider__owl owl-carousel owl-theme clearfix">
				<li class="owl__item" v-for="bn in dataList.banner">
					<a v-if="bn.url" :href="bn.url"><p class="item__img"><img :src="bn.image" alt=""></p></a>
					<p v-else class="item__img"><img :src="bn.image" alt=""></p>
					<p class="item__title">{{ bn.description }}</p>
				</li>
			</ul>
		</div>

		<div class="ryokan bgGrey">
			<div class="container">
				<h3>{{dataListLabel("ryokan_japanese_style_inns", "Ryokan -Japanese style inns")}}</h3>
				<h4 class="ryokan__h4">{{dataListLabel("special_ryokan", "Special Ryokan")}}</h4>
				<div class="ryokan__special owl-theme owl-carousel">
					<div class="special__item" v-for="pl in dataList.special_ryokan"> 
						<a :href="pl.url">
							<img :src="pl.image" class="img-responsive fullwidth" alt="">
						</a>
						<p class="ryokan__text">{{pl.title}}</p>
						<p class="ryokan__text2">{{pl.description}}</p>
					</div>
				</div>
				<h4 class="ryokan__h4">{{dataListLabel("popular_destinations", "Popular Destinations")}}</h4>
				<div class="ryokan__popular__outer">
					<div class="multi row ryokan__popular owl-theme owl-carousel ">
						<div class="col-sm-3 multi" v-for="pl in dataList.popular_ryokan"> 
							<a :href="pl.url">
								<img :src="pl.image" class="img-responsive fullwidth" :alt="pl.title">
							</a>
							<p class="popular__text">{{pl.title}}</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	
		<div class="ryokan mt0">
			<div class="container">
				<h3>{{dataListLabel("hotels", "Hotels")}}</h3>
				<h4 class="ryokan__h4">{{dataListLabel("special_hotels", "Special Hotels")}}</h4>
				<div class="ryokan__special owl-theme owl-carousel">
					<div class="special__item" v-for="pl in dataList.special_hotel"> 
						<a :href="pl.url">
							<img :src="pl.image" class="img-responsive fullwidth" alt="">
						</a>
						<p class="ryokan__text">{{pl.title}}</p>
						<p class="ryokan__text2">{{pl.description}}</p>
					</div>
				</div>
				<h4 class="ryokan__h4">{{dataListLabel("popular_destinations", "Popular Destinations")}}</h4>
				<div class="ryokan__popular__outer">
					<div class="multi row ryokan__popular owl-theme owl-carousel ">
						<div class="col-sm-3 multi" v-for="pl in dataList.popular_hotel"> 
							<a :href="pl.url">
								<img :src="pl.image" class="img-responsive fullwidth" :alt="pl.title">
							</a>
							<p class="popular__text">{{pl.title}}</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<link href="<?php echo get_template_directory_uri() ?>/templates/css/version.css" rel="stylesheet">		
<!-- Owl Stylesheets -->
<link rel="stylesheet" href="<?php echo get_template_directory_uri() ?>/templates/css/owl.carousel.min.css">
<link rel="stylesheet" href="<?php echo get_template_directory_uri() ?>/templates/css/owl.theme.default.min.css">
<link href='//fonts.googleapis.com/css?family=Droid+Sans:400,700|Arimo|Signika:400,600' rel='stylesheet' type='text/css'>
<link href="//fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

<link rel="stylesheet" href="<?php echo get_template_directory_uri() ?>/templates/css/jquery.multiselect.css">
<script src="<?php echo get_template_directory_uri() ?>/templates/js/jquery.multiselect.js"></script>
<script src="<?php echo get_template_directory_uri() ?>/templates/js/owl.carousel.js"></script>

<script>
	function onVueMounted(){
	      $('.top__slider .owl-carousel').owlCarousel({
	         	center: true,
	          loop:true,
	          margin:15,
	          nav:true,
	          responsive:{
	              0:{
	                  items:2,
	                  margin:7,
	              },
	              600:{
	                  items:3,
	                  margin:7,
	              },
	              1000:{
	                  items:4
	              }
	          }
	      })
		  
	      $('.ryokan__special.owl-carousel').owlCarousel({
			loop: false,
			margin: 15,
			nav: true,
			responsive:{
				0:{
					center: true,
					items: 1,
					margin: 7,
				},
				600:{
					items: 1,
					margin: 7,
				},
				1000:{
					items: 3,
					slideBy: 3
				}
			}
	      })


	      //show slider in sp
		  $(function() {
			var owl = $('.ryokan__popular.owl-carousel'),
			owlOptions = {
				loop: false,
				margin: 0,
				responsive: {
					0: {
						items: 2,
						margin: 0,
						slideBy: 2
					},
					600:{
						items: 2,
						margin: 0,
						slideBy: 2
					},
				}
			};

			if ( $(window).width() < 768 ) {
				var owlActive = owl.owlCarousel(owlOptions);
			} else {
				owl.addClass('off');
			}

			$(window).resize(function() {
				if ( $(window).width() < 768 ) {
					if ( $('.ryokan__popular.owl-carousel').hasClass('off') ) {
						var owlActive = owl.owlCarousel(owlOptions);
						owl.removeClass('off');
					}
				} else {
					if ( !$('.ryokan__popular.owl-carousel').hasClass('off') ) {
						owl.addClass('off').trigger('destroy.owl.carousel');
						owl.find('.owl-stage-outer').children(':eq(0)').unwrap();
					}
				}
			});
		});
	}
</script>