<?php $this->expectedController( "TpProductSearch" )?>

<div id="tp_search_section" class="tourPage" hidden>
	<div class="container">
		<h2 class="ver__h2">{{dataListLabel("day_tours_search", "Day Tours Search")}}</h2>
	</div>
	<div class="ver__top">
		<div class="top__mainImg">
			<div class="container">
				<div class="mainImg__box">
					<form class="" action="#" method="post">
						<!-- Tourplan Search Panel -->
						<span id="tp_search_panel">
							<product-search-panel :section-config="sectionConfig" :is-dst-auto-hide="isDstAutoHide"/>
						</span>
					</form>
				</div>
			</div>
		</div>
		<div v-if="dataList.banner" class="top__slider">
			<ul class="slider__owl owl-carousel owl-theme clearfix">
				<li class="owl__item" v-for="bn in dataList.banner">
					<a v-if="bn.url" :href="bn.url"><p class="item__img"><img :src="bn.image" alt=""></p></a>
					<p v-else class="item__img"><img :src="bn.image" alt=""></p>
					<p class="item__title">{{ bn.description }}</p>
				</li>
			</ul>
		</div>
		<div class="ryokan mt0">
			<div class="container">
				<h4 class="ryokan__h4">{{dataListLabel("special_day_tours", "Special Day Tours")}}</h4>
				<div class="ryokan__special owl-theme owl-carousel">
					<!-- Multi grid -->
					<tp-product-preview v-for="p in dataList.product_preview" :product-id="p.product_id" :detail-page-url="sectionConfig.product_info_page" :service-date="sectionConfig.preview_service_date" :search-configs="sectionConfig.search_config">
						<template slot-scope="slotProps">
			                <tp-image :src-image="helper.getOptionImageRoot() + '/' + slotProps.tpProduct.code + 'tn.jpg'" class-image="img-responsive fullwidth tp-preview-image" :href-url="slotProps.tpProductUrl" :on-error-src="helper.defaultImageURL()">
			                </tp-image>
			                <p class="ryokan__txtp01">{{slotProps.tpProduct.name}}</p>
			                <p class="ryokan__txtp02">
			                	<span class="text01" v-if="helper.amenities( slotProps.tpProduct.amenities, 'TDU').length > 0">
					                <span v-if="iii == 0" v-for="(a,iii) in helper.amenities( slotProps.tpProduct.amenities, 'TDU')">
					                	{{a.value}}
					                </span>
					            </span> 
			                	<span class="text02">{{slotProps.tpProduct.dst}}</span>
			                </p>
			                <p class="ryokan__txtp03">{{ helper.convertContentMaxLimit( helper.visitString( slotProps.tpProduct.amenities, 'TVI' ), 75 )}}</p>
			                <p class="ryokan__txtp04">{{helper.getSearchLabel(slotProps.tpSearchConfigs, "srb", "searchPricePrefix")}}</p>
			                <p class="ryokan__txtp05"><span class="text01">{{'$' + helper.displayPrice( slotProps.tpTotalPrice, 2 )}}</span><span class="text02">{{helper.getSearchLabel(slotProps.tpSearchConfigs, "srb", "searchPriceSuffix")}}</span></p>
						</template>
					</tp-product-preview>
				</div>
				<h4 class="ryokan__h4">{{dataListLabel("popular_locations", "Popular Locations")}}</h4>
				<div class="ryokan__popular__outer">
					<div class="multi row ryokan__popular owl-theme owl-carousel">
						<div class="col-sm-3 multi" v-for="pl in dataList.popular_locations"> <a :href="pl.url"><img :src="pl.image" class="img-responsive fullwidth" :alt="pl.title"></a>
							<p class="popular__text">{{pl.title}}</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<link href="<?php echo get_template_directory_uri() ?>/templates/css/version.css" rel="stylesheet">
<link href="<?php echo get_template_directory_uri() ?>/templates/css/tour.css" rel="stylesheet">
			
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
		});

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