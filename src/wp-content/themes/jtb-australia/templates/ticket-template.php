<?php $this->expectedController( "TpAccomProductDetail" )?>

<div class="container">
	<div class="col-xs-12" id="tp_detail_section" hidden>
		<div id="tp_detail_panel" class="tourplan_plugin_section tourplan_product_page">
			<div class="ribbon-red-desktop attached"></div>
			<h3 class="red-heading"><i class="fa fa-ticket"></i> {{dataListLabel("ticket_information", "Ticket Information")}}</h3>
			<div id="refreshSearchSection" class="refreshSearchSection">
				<div class="row" v-if="hasRefreshSearchSection">
					<div class="col-xs-12">
						<div class="ticketSearchPanel clearfix">
							<h3>{{sectionConfig.section_heading}}</h3>
							<div class="row">
								<div class="clearfix ticketSearchPanelInner">
									<div class="col-xs-12 col-sm-6 col-md-4">
										<label>{{dataListLabel("ticket_date", "Ticket Date")}} <input type="text" class="searchDate form-control" name="date"/></label>
									</div>
									<div class="col-xs-6 col-sm-3 col-md-3">
										<label>{{helper.getServiceButtonConfig(sectionConfig.srb, "adultCountLabel")}}
											<select class="adultQty form-control" name="adultQty">
												<option v-for="n in 12" :value="(n-1)+'A'">{{n-1}}</option>
											</select> 
										</label>
									</div>
									<div class="col-xs-6 col-sm-3 col-md-3">
										<label>{{helper.getServiceButtonConfig(sectionConfig.srb, "childCountLabel")}}
											<select class="childQty form-control" name="childQty">
												<option v-for="n in 12" :value="(n-1)+'C'">{{n-1}}</option>
											</select>
										</label>
									</div>
									<div class="col-xs-12 col-sm-12 col-md-2">
										<label class="hidden-xs hidden-sm"></label>
										<button type="button" class="refresh-search btn btn-success btn-block" name="refresh">{{dataListLabel("check", "Check")}}</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="productInfoSection" class="productInfoSection">
				<div class="row">
					<div v-if="productWrapper != null" class="col-xs-12">
						<div class="ticketResultPanel clearfix">
							<div class="row">
								<div class="clearfix ticketResultPanelInner">
									<span v-if="productWrapper.product.availability.length > 0">
										<div class="col-xs-12 col-md-4">
											<div class="productName">{{productWrapper.product.name}}</div>
										</div>

										<div class="col-xs-6 col-md-2 md-top-margin">
											<div class="adultsLabel">{{helper.serviceButtonConfig( "Tickets", "adultCountLabel")}}</div>
											<div class="qtyAdults">{{helper.getProductPaxCount(productWrapper.product, 'A')}}</div>
										</div>

										<div class="col-xs-6 col-md-2 md-top-margin">
											<div class="childrenLabel">{{helper.serviceButtonConfig( "Tickets", "childCountLabel" )}}</div>
											<div class="qtyChildren">{{helper.getProductPaxCount(productWrapper.product, 'C')}}</div>
										</div>

										<div class="col-xs-12 col-md-2 md-top-margin">
											<div class="totalPriceLabel">{{dataListLabel("total_price", "Total Price")}}</div>
											<div class="totalPrice">{{helper.serviceButtonConfig( "Tickets", "productPricePrefix" )}}{{helper.displayPrice( productWrapper.product.availability[0].AgentPrice, 2 )}}</div>
										</div>

										<div class="col-xs-12 col-md-2 md-top-margin">
											<button v-if="!productWrapper.isWaiting" type="button" name="book" 
												class="book btn btn-success btn-block">
												{{dataListLabel("book", "Book")}}
											</button> 
											<img v-else :src="helper.loadingImage()" class="img-responsive center-block"/>
										</div>
									</span>
									<div v-else class="col-xs-12">
										<div class="noAvail">
											<span>{{helper.serviceButtonConfig( "Tickets", "notFoundLabel" )}}</span>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			
		</div>
	</div>
</div>