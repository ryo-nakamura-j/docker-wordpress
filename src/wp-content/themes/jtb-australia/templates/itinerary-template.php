<?php $this->expectedController( "TpItinerary" ); ?>
<?php 
	$progressImg = array();
	$progressImgCur = array();
	for( $i = 1; $i <= 4; $i++ ){
		array_push( $progressImg, get_template_directory_uri() . '/templates/img/checkout/Checkout_process_' . $i . '.png' );
		array_push( $progressImgCur, get_template_directory_uri() . '/templates/img/checkout/Checkout_process_' . $i . 'b.png' );
	}
	$progressNext = get_template_directory_uri() . '/templates/img/checkout/Checkout_process_arrow.png';
	$navMenuItinerary = get_template_directory_uri() . '/templates/img/checkout/nav-menu-1.png';
	$navMenuCheckout = get_template_directory_uri() . '/templates/img/checkout/nav-menu-2.png';
?>

<div class="col-xs-12" id="tp_itinerary_section" hidden>
	<div class="tourplan_plugin_section" id="tp_itinerary_panel">
		<div class="plugin_control">
			<div class="row itinerary">
				<div class="progress_bar_master_container">
					<table v-if="!isViewMobile" class="progress_bar_container">
						<tbody>
							<tr class="progress_img">
								<td class="pic"><img src="<?php echo $progressImgCur[0]?>"></td>
								<td class="next"><img src="<?php echo $progressNext ?>"></td>
								<td class="pic">
									<!-- The checkout image here is not clickable, cause we need to get validation passed first-->
									<img src="<?php echo $progressImg[1]?>">
								</td>
								<td class="next"><img src="<?php echo $progressNext ?>"></td>
								<td class="pic"><img src="<?php echo $progressImg[2]?>"></td>
								<td class="next"><img src="<?php echo $progressNext ?>"></td>
								<td class="pic"><img src="<?php echo $progressImg[3]?>"></td>
							</tr>
							<tr class="progress_label">
								<td><label>{{dataListLabel("passenger_information", "Passenger Information")}}</label></td>
								<td></td>
								<td><label>{{dataListLabel("delivery_details_order_review", "Delivery Details & Order Review")}}</label></td>
								<td></td>
								<td><label>{{dataListLabel("payment", "Payment")}}</label></td>
								<td></td>
								<td><label>{{dataListLabel("order_confirmation", "Order Confirmation")}}</label></td>
							</tr>
						</tbody>
					</table>
					<table v-else="">
					</table>
					<div class="col-xs-12">
						<div class="col-xs-12">
							<div v-if="isViewMobile" id="tp-breadcrumbs">
								<img src="<?php echo $navMenuItinerary ?>" style="width:100%"/>
							</div>
							<div v-if="isViewMobile" class="col-xs-12">
								<div class="col-xs-6 col-sm-6" style="font-weight:bold">
									{{dataListLabel("shopping_cart_sub_total", "SHOPPING CART SUB TOTAL - ")}}{{cart_price}}
								</div>
								<div class="col-xs-6 col-sm-6" v-show="displayServiceLineList.length > 0">
									<button name="checkout" class="btn btn-danger form-control tp-big-button tp-main-button pull-right">{{dataListLabel("checkout", "Checkout")}}</button>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-12">
					<div class="servicelineSection col-md-8 col-xs-12" id="servicelineSectionHeight">
						<div v-if="!isViewMobile" class="col-xs-12 top_service_button_section">
							<div class="col-xs-4 col-sm-4 col-md-4 col-lg-2 pull-right">
								<button name="collapse-all" class="form-control btn btn-danger tp-small-button tp-secondary-button pull-right" v-on:click="collapseAll">{{dataListLabel("collapse_all", "Collapse All")}}</button>
							</div>
						</div>
						<span v-for="(s, sIdx) in originalServiceLineList">
							<serviceline-input-panel :ref="'vueServiceLine' + sIdx"
								:class-prefix="s.serviceline.servicetype + '_product'"
								:serviceline-wrapper="s" v-model="s.serviceline" :serviceline-index="sIdx"
								:has-passenger-section="helper.serviceButtonConfigContains(s.serviceline.servicetype, 'cartSections', 'passengerSection')" 
								:has-preference-section="helper.serviceButtonConfigContains(s.serviceline.servicetype, 'cartSections', 'preferenceSection')"
								:has-info-section="helper.serviceButtonConfigContains(s.serviceline.servicetype, 'cartSections', 'infoSection')"
								:has-notice-section="helper.serviceButtonConfigContains(s.serviceline.servicetype, 'cartSections', 'noticeSection')"
								 v-on:height-changed="onHeightChanged" :is-hidden="isDeletedServiceLine(s)" :date-picker-options="minMaxPref1DateObjMap[s.serviceline.unique_ui_id]">
								<template slot-scope="sp1" slot="header-line-1">
						              <div :class="sectionHeaderClassProductName(s.serviceline.servicetype)" v-if="helper.serviceButtonConfigContains(s.serviceline.servicetype, 'cartSections', 'productName')">
						                <span v-if="!helper.serviceButtonConfigContains(s.serviceline.servicetype, 'cartSections', 'supplierName')" class="product_name">{{helper.propWithLang( sp1.line, "productname")}}</span>
										<span v-else class="product_name">{{helper.propWithLang( sp1.line, "suppliername")}} - {{helper.propWithLang( sp1.line, "productname")}}</span>
						              </div>
									  <div :class="sectionHeaderClassPaxConfig(s.serviceline.servicetype)">
										<span v-if="helper.serviceButtonConfigContains(s.serviceline.servicetype, 'cartSections', 'productDate')" 
											class="product_date">
											{{sp1.line.date}}
										</span>
									  	<span v-if="helper.serviceButtonConfigContains(s.serviceline.servicetype, 'cartSections', 'paxCount')" 
									  		class="pax_config pull-right">
									  		{{ helper.paxString( sp1.line.qty, 
									  			helper.serviceButtonConfig( s.serviceline.servicetype, "adultCountLabel"), 
									  			helper.serviceButtonConfig( s.serviceline.servicetype, "childCountLabel") )}}
									  	</span>
										<span v-if="helper.serviceButtonConfigContains(s.serviceline.servicetype, 'cartSections', 'roomCount')" 
											class="pax_config pull-right">
											{{helper.roomTypeString( sp1.line.qty )}}
										</span>
						              </div>
									  <div v-if="helper.serviceButtonConfigContains(s.serviceline.servicetype, 'cartSections', 'productPrice')" 
									  	:class="sectionHeaderClassServicePrice(s.serviceline.servicetype)">
						                <span class="price service_price pull-right">
						                	{{sp1.wrapper.service_price}}
						                </span>
						              </div>
								</template>
								<template v-if="helper.serviceButtonConfigContains(s.serviceline.servicetype, 'cartSections', 'arrangementsSection')" slot-scope="sp1" slot="header-line-2">
								    <div class="row"> 
								        <div class="col-xs-12 col-sm-12"> 
								            <span class="">
								                <small class="ArrangementsLabel"> 
								                    {{sp1.line.arrangementDisplay}} 
								                </small> 
								            </span> 
								        </div> 
								    </div>
								</template>
							</serviceline-input-panel>
						</span>
						<div class="col-xs-12 service_button_section">
							<div v-if="!isViewMobile" class="col-xs-4 col-sm-4 col-md-4 col-lg-2 pull-right">
								<button name="collapse-all" v-on:click="collapseAll" class="form-control btn btn-danger tp-small-button tp-secondary-button pull-right">{{dataListLabel("collapse_all", "Collapse All")}}</button>
							</div>
							<div class="col-xs-4 col-sm-4 col-md-4 col-lg-2 pull-right">
								<button name="empty" class="form-control btn btn-danger tp-small-button tp-secondary-button pull-right">{{dataListLabel("remove_all", "Remove All")}}</button>
							</div>
						</div>
					</div>
    				<div class="col-md-4 col-xs-12">
						<tp-scroll-magnet ref="tpScrollMagnet" tp-offset-top-pad="75" tp-bounds-element-selector="#servicelineSectionHeight" :is-view-mobile="isViewMobile" min-bottom-height="500">
							<div class="row">
								<div class="col-xs-12 summery_section">
									<span class="col-xs-12 ">
										<div class="col-xs-12 summery_heading">
											<h4>{{dataListLabel("total", "Total")}}</h4>
										</div>
										<div class="col-xs-12 summery_content">
											<div class="col-sm-6 col-md-6"> 
												<span class="sub_label">{{dataListLabel("sub_total", "SUB TOTAL")}}</span>
											</div>
											<div class="col-sm-6 col-md-6"> 
												<span class="sub_price pull-right">{{cart_price}}</span>
											</div>
										</div>
										<div class="col-xs-12 col-sm-6" v-show="displayServiceLineList.length > 0">
											<a :href="dataList.text_data.continue_shopping_url[0].url" name="continue" class="btn btn-danger form-control tp-big-button tp-secondary-button">{{dataListLabel("continue_shopping", "Continue Shopping")}}</a>
										</div>
										<div class="col-xs-12 col-sm-6" v-show="displayServiceLineList.length > 0">
											<button name="checkout" class="btn btn-danger form-control tp-big-button tp-main-button">{{dataListLabel("checkout", "Checkout")}}</button>
										</div>
									</span>
								</div>
							</div>
							<div class="row" >
								<div class="itinerary-message form-control" v-html="dataList.text_data.remarks[0].text">
								</div>
							</div>
							<div class="row additional_buttons" >
								<div v-for="n in ['slot_1','slot_2','slot_3','slot_4']" v-if="!_.isEmpty(sectionConfig[n]) && !_.isEmpty(sectionConfig[n].url)"
									class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
									<a :class="sectionConfig[n].classes + ' ' + 'btn form-control' + ' ' + 'tp-small-long-button' + ' ' + 'tp-secondary-button'" 
										:style="sectionConfig[n].styles"
										:href="sectionConfig[n].url">
										{{sectionConfig[n].label}}
									</a>
								</div>
							</div>
						</tp-scroll-magnet>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<link href="<?php echo get_template_directory_uri() ?>/templates/tp-css/tp-button.css" rel="stylesheet">		
<link href="<?php echo get_template_directory_uri() ?>/templates/tp-css/itinerary-checkout.css" rel="stylesheet">	
<link href="<?php echo get_template_directory_uri() ?>/templates/tp-css/tourplan.css" rel="stylesheet">		
	
