<?php $this->expectedController( "TpCheckout" )?>
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

<div class="col-xs-12 checkout" id="tp_checkout_section" hidden>
	<div class="tourplan_plugin_section" id="tp_checkout_panel">
	    <div v-show="isLoading" style="width:100%;text-align:center">
	      <img :src="helper.loadingImage()" />
	    </div>
		<span v-show="!isLoading">
			<div class="progress_bar_master_container">
				<table v-if="!helper.isMobile()" class="progress_bar_container">
					<tbody>
						<tr class="progress_img">
							<td class="pic">
								<a :href="sectionConfig.itinerary_url">
									<img src="<?php echo $progressImg[0]?>">
								</a>
							</td>
							<td class="next"><img src="<?php echo $progressNext ?>"></td>
							<td class="pic"><img src="<?php echo $progressImgCur[1]?>"></td>
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
						<div v-if="helper.isMobile()" id="tp-breadcrumbs">
							<img src="<?php echo $navMenuCheckout ?>" style="width:100%"/>
						</div>
					</div>
					<div v-show="helper.isMobile()" class="col-xs-12 summery_section">
						<div v-if="helper.isMobile()" class="col-xs-12">
							<booking-summery-panel :service-line-list="serviceLineList" :section-config="sectionConfig" :delivery-fee-amount="deliveryFeeAmount" :paymentfee-price="paymentfeePrice" :sub-total="subTotal"></booking-summery-panel>
						</div>
					</div>
				</div>
			</div>
			<div class="plugin_control_before col-md-8 col-xs-12" id="magnetSectionHeight">
				<div class="row checkout">
					<div class="col-xs-12">
						<tp-group>
							<div slot="tp-header">
								<h4>{{dataListLabel("1_billing", "1. BILLING")}}</h4>
							</div>
							<div slot="tp-body" class="customerSection form-horizontal row">
								<div v-for="cf in sectionConfig.customer_fields" :class="'col-xs-12 col-sm-6' + ' ' + (cf.required ? 'required' : '')">
									<label :for="'checkout_' + cf.customer_field" class="control-label">{{cf.label}}</label>
									<div class="">
										<select v-if="cf.customer_field == 'title'" 
											:id="'checkout_' + cf.customer_field" 
											:name="cf.customer_field" class="form-control"
											v-on:change="saveField(cf.customer_field, $event)" 
											:value="loadField(cf.customer_field) || ''"></select>
										<span v-else-if="cf.customer_field == 'branch'">
											<select :id="'checkout_' + cf.customer_field" 
											:name="cf.customer_field" class="form-control"  
											v-on:change="saveField(cf.customer_field, $event)" 
											:value="loadField(cf.customer_field) || ''"></select>
										<input type="hidden" :name="cf.customer_field + '_label'" />
									</span>
									<span v-else-if="cf.customer_field == 'country' && selections.country.length > 0">
										<select :id="'checkout_' + cf.customer_field" 
											:name="cf.customer_field" class="form-control"   
											v-model="input_country">
											<option value="" disabled></option>
											<option v-for="v in selections.country" :value="v">
												{{v}}
											</option>
										</select>
									</span>
									<span v-else-if="cf.customer_field == 'text'">{{cf.default_value}}</span>
									<tp-safe-text-input v-else 
										:tp-id="'checkout_' + cf.customer_field"
										:tp-name="cf.customer_field" 
										tp-class="form-control" 
										:tp-disabled="cf.read_only == 1"
										v-model="cf.default_value"
										is-field-retain="1" :is-always-upper-case="cf.customer_field != 'email' && cf.customer_field != 'email_confirm'" :tp-character-exception-rule="/[^A-Za-z0-9\[\]#@. \-_'\\\=\(\)\/]/g"/>
								</div>
							</div>
							</div>
						</tp-group>
						<tp-group v-if="!_.isEmpty(selections.deliveryFeeOption)">
							<div slot="tp-header">
								<div class="row">
									<div class="col-xs-12 col-sm-12 col-md-6">
										<h4>{{dataListLabel("2_collection_options", "2. COLLECTION OPTIONS")}}</h4>
									</div>
								</div>
							</div>
							<div slot="tp-body" id="deliveryFeesSection" class="deliveryFeesSection">
								<div class="row">
									<div class="col-xs-12 col-sm-12 col-md-12" v-for="(dv,iidx) in allDeliveryOptions" >
										<div style="margin-top:5px;margin-bottom:5px;">
											<label class="radio-inline">
												<input type="radio" name="deliveryfee" :value="dv.label" v-on:change="deliveryFeeChanged" v-model="input_deliveryFeeOption"/>{{helper.propWithLang( dv, "label" )}}
											</label>
										</div>
									</div>
								</div>
								<div class="row deliverySection">
									<div class="col-xs-12 col-sm-12 col-md-12 tp-subheader">
										<div class="row">
											<div class="col-xs-12 col-sm-12 col-md-6">
												<h4>{{dataListLabel("delivery_address", "Delivery Address")}}</h4>
											</div>
											<div class="col-xs-12 col-sm-12 col-md-6">
												<label class="checkbox-inline pull-right" :style="helper.isMobile()?'':'padding-top:10px;'">
													<input type="checkbox" name="deliverySameAsCustomer" v-on:change="saveCheckBox('deliverySameAsCustomer', $event)" 
														:checked="(loadField('deliverySameAsCustomer') != null && loadField('deliverySameAsCustomer') == 1) || 0"> {{dataListLabel("same_as_customer_address", "Same as Customer Address")}}
												</label>
											</div>
										</div>
									</div>
									<div class="col-xs-12 form-horizontal deliveryAddressSection form-group row">
										<span v-for="dv in sectionConfig.delivery_fields">
											<div v-if="dv.delivery_field == 'text'">
												<div class="col-sm-12 col-md-12">
													<span v-html="dv.default_value"></span>
												</div>
											</div>
											<!-- Only JTBAustralia use country as dropdown and they have deliveryAddress4 use as a country-->
											<div v-else-if="dv.delivery_field == 'deliveryAddress4' && selections.country.length > 0" :class="'col-sm-12 col-md-6 ' + ' ' + (dv.required ? 'required' : '')">
												<label :for="'delivery_' + dv.delivery_field" class="control-label">{{dv.label}}</label>
												<div class="">
													<select :id="'delivery_' + dv.delivery_field" 
														:name="dv.delivery_field" class="form-control"  
														v-model="input_deliveryCountry">
														<option value="" disabled></option>
														<option v-for="v in selections.country" :value="v">
															{{v}}
														</option>
													</select>
												</div>
											</div>
											<div v-else :class="'col-sm-12 col-md-6 ' + ' ' + (dv.required ? 'required' : '')">
												<label :for="'delivery_' + dv.delivery_field" class="control-label">{{dv.label}}</label>
												<div class="">
													<tp-safe-text-input
														:tp-id="'delivery_' + dv.delivery_field" 
														:tp-name="dv.delivery_field" 
														tp-class="form-control" 
													:tp-read-only="dv.read_only==1"
													v-model="dv.default_value" is-field-retain="1" :tp-character-exception-rule="/[^A-Za-z0-9\[\]#@. \-_'\\\=\(\)\/]/g"/>
												</div>
											</div>
										</span>
									</div>
								</div>
							</div>
						</tp-group>
						<tp-group v-if="!_.isEmpty(cardTypes)">
							<div slot="tp-header">
								<h4>{{dataListLabel("3_payment_options", "3. PAYMENT OPTIONS")}}</h4>
							</div>
							<div slot="tp-body" id="paymentTypesSection" class="paymentTypesSection">
								<div class="row">
									<div class="col-xs-12 col-sm-12 col-md-12" v-for="(pt,iidx) in cardTypes" >
										<div style="margin-top:5px; margin-bottom:5px;">
											<label class="radio-inline">
												<input type="radio" name="paymenttype" 
													:value="pt.value"/>{{pt.label}}
											</label>
										</div>
									</div>
								</div>
								<div class="tp-credit-card-form-template">
									<?php echo do_shortcode('[tp-creditcard-form-template]'); ?>
								</div>
							</div>
						</tp-group>
					</div>
				</div>
			</div>
			<div class="plugin_control_after col-md-4 col-xs-12">
				<tp-scroll-magnet ref="tpScrollMagnet" tp-offset-top-pad="75" tp-bounds-element-selector="#magnetSectionHeight" :is-view-mobile="helper.isMobile()" min-bottom-height="700">
					<div class="col-xs-12 summery_section" id="summery_section">
						<div class="col-xs-12">
							<booking-summery-panel v-if="!helper.isMobile()" :service-line-list="serviceLineList" :section-config="sectionConfig" :delivery-fee-amount="deliveryFeeAmount" :paymentfee-price="paymentfeePrice" :sub-total="subTotal"></booking-summery-panel>
							<div class="row checkout" v-if="dataList.confirm_policies != null &&
								dataList.confirm_policies.policy_filename_url != null &&
								dataList.confirm_policies.policy_text != null &&
								dataList.confirm_policies.policy_filename_url.length == dataList.confirm_policies.policy_text.length">
								<div class="col-xs-12">
									<ul class="col-xs-12 confirmationSection">
										<div v-for="n in dataList.confirm_policies.policy_filename_url.length" :class="{'bg-danger':errors.has('confirm' + n)}">
											<input type="checkbox" v-validate="'required'" :name="'confirm' + n" :id="'confirm' + n"/>
											<label v-if="dataList.confirm_policies.policy_filename_url[n-1].url" :for="'confirm' + n">
												{{dataList.confirm_policies.policy_text[n-1].text}}
												<a :href="dataList.confirm_policies.policy_filename_url[n-1].url" target="_blank">
													{{dataList.confirm_policies.policy_filename_url[n-1].text}}
												</a>
											</label>
											<label v-else :for="'confirm' + n">
												{{dataList.confirm_policies.policy_filename_url[n-1].text}}
											</label>   
										</div>
									</ul>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12 col-sm-offset-3 col-sm-6 col-md-offset-2 col-md-8">
									<button name="confirm" :disabled="isWaiting" class="form-control btn btn-success tp-big-button tp-main-button">
										{{onRequest ? dataListLabel("place_order", "Place Order") : dataListLabel("proceed_to_payment", "Proceed to Payment") }}
									</button>
								</div>
								<div class="col-xs-12" style="margin-top:10px">
									<p v-if="onRequest" style="text-align:center;"><em>{{dataListLabel("tp_order_on_request", "Your order contains services that are on request, we'll confirm via email within 24 hours of your payment.")}}</em></p>
									<p v-else style="text-align:center;"><em>{{dataListLabel("tp_order_confirm", "All your services are currently available.")}}</em></p>
								</div>
							</div>
						</div>
					</div>
				</tp-scroll-magnet>
			</div>
		</span>
	</div>
</div>
<?php echo do_shortcode('[tp-creditcard-form-script]'); ?>
<link href="<?php echo get_template_directory_uri() ?>/templates/tp-css/tp-button.css" rel="stylesheet">		
<link href="<?php echo get_template_directory_uri() ?>/templates/tp-css/itinerary-checkout.css" rel="stylesheet">	
<link href="<?php echo get_template_directory_uri() ?>/templates/tp-css/tourplan.css" rel="stylesheet">		

