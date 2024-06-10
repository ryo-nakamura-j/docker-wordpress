<?php $this->expectedController( "TpAccomProductDetail" )?>

<div class="result__page" id="tp_detail_section" hidden>
	<div class="searchPage" id="tp_detail_panel">
		<div v-if="supplier == null" class="row" >
			<div class="col-xs-12">
				<image :src="helper.loadingImage()" class="img-responsive center-block" />
			</div>
		</div>
<!-- 		<div v-else-if="supplier == null" class="container" >
			<tp-error :error-list="[error]"/>
		</div> -->
		<span v-else>
			<div class="container">
				<div class="row">
					<div class="col-sm-7">
						<div class="result__slider">
                			<tp-slides :tp-id="'carousel-' + supplier.code" :tp-src-list="helper.getSupplierImageSrcList(supplier)"/>
						</div>
					</div>
					<div class="col-sm-5">
						<div class="result__title">{{supplier.name}}</div>
						<div class="result__address">{{supplier.contact.address1}} {{supplier.contact.address2}}</div>
						<div class="result__room"></div>
						<div class="result__content"> 
							<p>{{helper.getNotes( supplier.notes, "TST", "text" )}}</p>
							<p>{{helper.getNotes( supplier.notes, "SDS", "text" )}}</p>
						</div>
					</div>
				</div>
			</div>
			<div class="result__selection">
				<div class="container mb50">
					<h3> {{dataListLabel("room_selection", "Room Selection")}} </h3>
					<div id="refreshSearchSection" class="room__selection__outer">
						<div v-if="hasRefreshSearchSection" class="room__selection">
							<div class="date__row clearfix">
								<div class="date__row__inner clearfix">
									<form action="#">
										<ul>
											<li class='input_text'>
												<div class="check_in form_work"> <span>{{helper.getValueByKey( sectionConfig.modify_search_config, 'date_in_label' )}}</span>
													<input type="text" name='date' id="check_in" class="date">
												</div>
											</li>
											<li class='input_text'>
												<div class="check_out form_work"> <span>{{helper.getValueByKey( sectionConfig.modify_search_config, 'date_out_label' )}}</span>
													<input type="text" name='toDate' id="check_out" class="date">
												</div>
											</li>
											<li class='input_select' hidden>
												<div class="check_out form_work"> <span>{{helper.getValueByKey( sectionConfig.modify_search_config, 'scu_label' )}}</span>
													<select name="scu" id="room">
														<option v-for="n in 30" :value="n">{{n}}</option>
													</select>
												</div>
											</li>
											<li class='input_select'>
												<div class="check_out form_work"> <span>{{helper.getValueByKey( sectionConfig.modify_search_config, 'qty_label' )}}</span>
													<select name="qty" id="room">
														<option v-for="n in 30" :value="n">{{n}}</option>
													</select>
												</div>
											</li>
											<li>
												<div class="form_btn">
													<button class="refresh-search">{{dataListLabel( "check_availability", "Check Availability")}}</button>
												</div>
											</li>
										</ul>
									</form>
								</div>
							</div>
							<div class="room__detail__outer clearfix">
								<table v-if="productList != null">
									<thead>
										<td>{{dataListLabel("room_type", "Room Type")}}</td>
										<td>{{sectionConfig.show_per_scu_rate == "true" ? 
											dataListLabel( "price_per_night", "Price (Per Night)" ) : 
											dataListLabel( "price_total", "Price (Total)" ) }}</td>
										<td></td>
									</thead>
									<tbody>
										<tr v-if="p.availability != null" v-for="(p,idx) in productList" :id="helper.cleanCSSString( p.availability.RateId )">
											<td><div class="room__detail clearfix">
													<div class="thumb">
														<tp-image :src-image="helper.getProductImageSrcList(p.product, 1)[0]" :on-error-src="helper.defaultImageURL()">
														</tp-image>
													</div>
													<div class="room_desc">
														<h4>{{helper.partOfString( p.product.name, "/", 0 ) }}</h4>
														<span class="type">{{helper.partOfString( p.product.name, "/", 1 ) }}</span> 
															<span class="room_bed"> 
<!-- 															<v-lazy-img :src="getBedImage(p.availability.Qty)" alt=""> -->
														</span>
													</div>
												</div>
											</td>
											<td>
												<span class="price">
													{{'$' + helper.displayPrice( (sectionConfig.show_per_scu_rate == "true" ? p.availability.TotalPrice / p.availability.Scu : p.availability.TotalPrice ), 2 )}} 
													<span class='visible-xs-inline'>
														{{sectionConfig.show_per_scu_rate == "true" ? dataListLabel("per_night", "per night") : dataListLabel("total", "total")}}
													</span>
												</span>
											</td>
											<td>
												<a class='btn_booking' v-if="!p.isWaiting" name="book">
													<span >{{dataListLabel("book_now", "Book Now")}}</span>
												</a>
												<img v-else :src="helper.loadingImage()" class="img-responsive center-block"/>
											</td>
										</tr>
									</tbody>
								</table>
								<div v-else class="row" >
									<div class="col-xs-12">
										<image :src="helper.loadingImage()" class="img-responsive center-block" />
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="result__selection" v-if="helper.getNotes( supplier.notes, 'LDS', 'text' ) || (supplier.amenities != null && supplier.amenities.length > 0)">
				<div class="container">
					<h3> {{dataListLabel("hotel_description", "Hotel Description")}} </h3>
					<div class="hotel_desc__outer">
						<div class="hotel_desc">
							<table>
								<tr>
									<td><div class="blk01">
											<h4>{{dataListLabel("amenities", "Amenities")}}</h4>
											<ul>
												<li v-for="a in supplier.amenities" style="font-style:italic;">
													<span v-for="l in helper.tp_lookup( 'AMN', a )">
														{{l.name}}
													</span>
												</li>
											</ul>
										</div></td>
									<td><div class="blk02">
											<h4>{{dataListLabel("location", "Location")}}</h4>
											{{helper.getNotes( supplier.notes, "LDS", "text" )}}
										</div></td>
									<td><div class="map"> <v-lazy-img src="img/result/map.jpg" alt=""> </div></td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
		</span>
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
   function onVueSearchResultMounted(){
   }
</script>
