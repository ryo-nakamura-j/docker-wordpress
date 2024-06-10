<?php $this->expectedController( "TpTourProductDetail" )?>

<div class="result__page" id="tp_detail_section" hidden>
	<div class="searchPage" id="tp_detail_panel">
		<div v-if="supplier == null && error == null" class="row" >
			<div class="col-xs-12">
				<image :src="helper.loadingImage()" class="img-responsive center-block" />
			</div>
		</div>
		<div v-else-if="supplier == null" class="container" >
			<tp-error :error-list="[error]"/>
		</div>
		<span v-else>
		  <div class="container resultBox1">
		     <div class="row">
		        <div class="col-sm-5 hidden-xs">
		           <h3 class="ttl">{{product.name}}</h3>
		           <div class="box1 clearfix">
		              <p class="txtPrice" v-if="availability.adult">
		                 <span class="txt1">{{helper.getServiceButtonConfig(sectionConfig.srb, "searchPricePrefix")}}</span>
		                 <span class="txt2">{{'$' + helper.displayPrice( availability.adult.TotalPrice, 2 )}}</span>
		                 <span class="txt3">{{helper.getServiceButtonConfig(sectionConfig.srb, "searchPriceSuffix")}}</span>
		              </p>
		              <p class="icon">
			              <span v-for="a in helper.amenities( product.amenities, 'TIC')">
			                <img :src="helper.getTourIconSrc(a.key)" :alt="a.value" :title="a.value" />
			              </span>
		              </p>
		           </div>
		           <div class="box2" v-html="helper.getNotes( product.notes, 'SDO', 'html')">
		           </div>
		        </div>
		        <div class="col-sm-7">
					<div class="result__slider">
	        			<tp-slides :tp-id="'carousel-' + product.code" :tp-src-list="helper.getProductImageSrcList(product, 3)"/>
					</div>
		        </div>
		        <div class="col-sm-5 visible-xs">
		           <h3 class="ttl">{{product.name}}</h3>
		           <div class="box1 clearfix">
		              <p class="txtPrice" v-if="availability.adult">
		                 <span class="txt1">{{helper.getServiceButtonConfig(sectionConfig.srb, "searchPricePrefix")}}</span>
		                 <span class="txt2">{{'$' + helper.displayPrice( availability.adult.TotalPrice, 2 )}}</span>
		                 <span class="txt3">{{helper.getServiceButtonConfig(sectionConfig.srb, "searchPriceSuffix")}}</span>
		              </p>
		              <p class="icon">
			              <span v-for="a in helper.amenities( product.amenities, 'TIC')">
			                <img :src="helper.getTourIconSrc(a.key)" :alt="a.value" :title="a.value" />
			              </span>
		              </p>
		           </div>
		           <div class="box2" v-html="helper.getNotes( product.notes, 'SDO', 'html')">
		           </div>
		        </div>
		     </div>
		  </div>
		  <div class="resultBox2">
		     <div class="innerBox">
		        <h4 class="ttl">{{product.name}}</h4>
		        <ul class="list express_slide owl-carousel owl-theme clearfix">
		           <li class="owl-item product-section" v-for="(dr,idx) in dateRangesList">
		              <div v-if="dr.searchData && dr.searchData.availability.adult" :class="'box search-date search-date-' + idx + ' ' + (currentDateSelectedIndex == idx || currentDateSelectedIndex == -1 ? ' ' : 'un_select' )">
		                 <p class="calBox productInfo">
		                    <input type="text" id="pick_date" value="" :placeholder="helper.formatDate( dr.searchData.availability.adult.Date, 'DD/MM/YYYY' )" readonly name="txtDate">
		                 </p>
		                 <div class="subBox">
		                    <table>
		                       <tr>
		                          <th>
		                             <p class="txt01">{{helper.getServiceButtonConfig(sectionConfig.srb, "adultCountLabel")}} {{dr.searchData.ageBrackets.adult}}</p>
		                             <p class="txt02">${{helper.displayPrice( dr.searchData.availability.adult.TotalPrice, 2)}}</p>
		                          </th>
		                          <td>
		                             <select class="txtNum" name="adult" v-model="dr.adultCount" v-on:change="selectedPanel(idx)">
		                                <option v-for="iidx in 11" :value="iidx-1">{{iidx-1}}</option>
		                             </select>
		                          </td>
		                       </tr>
		                       <tr v-if="dr.searchData.availability.child">
		                          <th>
		                             <p class="txt01">{{helper.getServiceButtonConfig(sectionConfig.srb, "childCountLabel")}} {{dr.searchData.ageBrackets.child}}</p>
		                             <p class="txt02">${{helper.displayPrice( dr.searchData.availability.child.TotalPrice, 2)}}</p>
		                          </th>
		                          <td>
		                             <select class="txtNum" name="child" v-model="dr.childCount" v-on:change="selectedPanel(idx)">
		                                <option v-for="iidx in 11" :value="iidx-1">{{iidx-1}}</option>
		                             </select>
		                          </td>
		                       </tr>
		                    </table>
		                 </div>
		                 <p class="btnSelect">
		                    <button name="book" value="select" :disabled="dr.adultCount + dr.childCount == 0">{{dr.searchData.buttonName}}</button>
		                 </p>
		              </div>
		              <div v-else class="box">
						<div class="row">
								<div class="col-xs-12">
							<div style="background:white; padding:1em 0; margin:1em 0;">
									<h4 class="text-center">{{dataListLabel("no_rates_available", "No Rates Available")}}</h4>
								</div>
							</div>
						</div>
		              </div>
		           </li>
		        </ul>
		     </div>
		  </div>
			 <div v-if="product != null" class="result__selection">
			     <div class="container resultBox4">
			        <h3 class="ttl"> {{dataListLabel("tour_details", "Tour Details")}}</h3>
			        <div class="box clearfix">
			           <table>
			              <tr>
			                 <th>{{dataListLabel("departure", "Departure")}}</th>
			                 <td>{{product.dst}}</td>
			              </tr>
			              <tr>
			                 <th>{{dataListLabel("duration", "Duration")}}</th>
			                 <td>
					             <span class="text01" v-if="helper.amenities( product.amenities, 'TDE').length > 0">
					                <span v-for="a in helper.amenities( product.amenities, 'TDE')">
					                  {{a.value}}
					                </span>
					             </span>
					         </td>
			              </tr>
			              <tr>
			                 <th>{{dataListLabel("destinations", "Destinations")}}</th>
			                 <td>{{helper.convertContentMaxLimit( helper.getNotes( product.notes, 'SDO', 'text' ), 9999 )}}</td>
			              </tr>
			           </table>
			           <table>
			              <tr>
			                 <th>{{dataListLabel("time", "Time")}}</th>
			                 <td>{{helper.getNotes( product.notes, 'TTI', 'text' )}}</td>
			              </tr>
			              <tr>
			                 <th>{{dataListLabel("guide", "Guide")}}</th>
			                 <td>{{helper.getNotes( product.notes, 'TGU', 'text' )}}</td>
			              </tr>
			              <tr>
			                 <th>{{dataListLabel("meals", "Meals")}}</th>
			                 <td>{{helper.getNotes( product.notes, 'TME', 'text' )}}</td>
			              </tr>
			              <tr>
			                 <th>{{dataListLabel("visits", "Visits")}}</th>
			                 <td>{{ helper.convertContentMaxLimit( helper.visitString( product.amenities, 'TVI' ), 9999 ) }}</td>
			              </tr>
			           </table>
			        </div>
			     </div>
			 </div>
		  <div class="result__selection" v-if="arrangementsData != null && arrangementsData.arrangements != null && currentDateSelectedIndex != -1 && isExternalProduct == true" id="tourArrangementsPanel">
		     <div class="container resultBox3">
		        <h3 class="ttl"> {{dataListLabel("tour_arrangements", "Tour Arrangements")}} </h3>
				<div class="row arrangements-container frmForm">
					<div class="col-xs-12 arrangement-row" v-for="arr in arrangementsData.arrangements" :name="arr.ArrangementName">
						<span v-for="dt in arr.Details">
							<span v-if="dt.InputType == 'LIST'">
								<span v-if="dt.SelectionType == 'MULTIPLE'">
									<tp-arrangement-input class-container="arrangements-checkbox-list" over-write-required-false :details="dt">
										<div class="col-md-3">
											<span v-for="(opt,iiidx) in dt.Options" v-if="iiidx==0">
												<div class="col-md-12" v-for="n in arrangementsData.paxCount">
													Pax {{n}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
													<input type="checkbox" :code="opt.SelectionCode">
													{{opt.SelectionName}}
												</div>
											</span>
										</div>
									</tp-arrangement-input>
								</span>
								<span v-if="dt.SelectionType == 'ONE'">
									<tp-arrangement-input class-container="arrangements-dropdown" :details="dt">
										<select class=" txtList">
											<option v-for="opt in dt.Options" :value="opt.SelectionCode">{{opt.HotelName}} {{opt.Time}}</option>
										</select>
									</tp-arrangement-input>
								</span>
							</span>
							<span v-if="dt.InputType == 'VALUE' || dt.InputType == 'VALUE_FORMAT'">
								<span v-if="dt.Format == 'INDATE'">
									<tp-arrangement-input class-container="arrangements-datepicker" :details="dt">
										<input type="text" name="date" class="datepicker"/>
									</tp-arrangement-input>
								</span>
								<span v-else>
									<tp-arrangement-input class-container="arrangements-textarea" :details="dt" :validation-string="dt.Format">
										<div v-if="dt.IsPaxInputUnitPax" class="row" style="display: inline-block;">
											<div class="col-md-8">
												<div class="row" v-for="n in arrangementsData.paxCount">
													<div class="col-md-12">
														<div>Pax {{n}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
														<div>
															<textarea :validation="dt.Format" style="height: 50px;" rows="5" >{{dt.DefaultValue}}</textarea>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div v-if="!dt.IsPaxInputUnitPax" class="row" style="display: inline-block;">
											<div class="col-md-8">
												<textarea class="" :validation="dt.Format" rows="5" >{{dt.DefaultValue}}</textarea>
											</div>
										</div>
									</tp-arrangement-input>
								</span>
							</span>
						</span>
					</div>
					<div class="col-xs-12 col-md-3 arrangements-col1">
					</div>
					<div class="col-xs-12 col-md-9 arrangements-col2">
						<button id="book_now" class="btnSubmit" name="btnSend" value="Book">{{dataListLabel("book", "Book")}}</button>
					</div>
				</div>
		     </div>
		  </div>
		     <div v-if="product != null" class="result__selection">
		        <div class="container resultBox5">
		            <h3 class="ttl"> {{dataListLabel("tour_itinerary", "Tour Itinerary")}}</h3>
					<div v-html="helper.getNotes( product.notes, 'LDO', 'html')">
					</div>
		        </div>
		     </div>
		</span>
	</div>
</div>


<link href="<?php echo get_template_directory_uri() ?>/templates/css/version.css" rel="stylesheet">
<link href="<?php echo get_template_directory_uri() ?>/templates/css/tour_result.css" rel="stylesheet">
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
<link href="<?php echo get_template_directory_uri() ?>/templates/tp-css/tourplan.css" rel="stylesheet">		