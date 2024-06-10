<script class="masterTemplate" type="text/x-handlebars-template">
	<div class="row">
		<div class="col-xs-12">
			<div class="productSearchSection"></div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<h3>Tour Search Results</h3>
			<div class="ribbon-red-desktop"></div>
		</div>
		<div class="resultsSection">{{loadingImage}}</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<h3>Nearby Dates</h3>
			<div class="ribbon-red-desktop"></div>
		</div>
		<div class="additionalResultsSection">{{loadingImage}}</div>
	</div>
</script>

<script class="productSearchTemplate" type="text/x-handlebars-template">
	<div class="productSearchControl">
		<h3>Day Tours Search</h3>
		<div class="searchControls">
			<div class="row">
	
				<input type="hidden" name="srb" />
				<input type="hidden" name="cty" />

				<div class="col-xs-12 col-md-4">
					<label>{{serviceButtonConfig (getValueByKey search_config "srb") "destinationsLabel"}}
						<select class="form-control" name="dst"></select>
					</label>
				</div>
				<div class="col-xs-12 col-md-4">
					<label>{{serviceButtonConfig (getValueByKey search_config "srb") "qtyLabel"}}
						<select name="qty" class="form-control" >
							{{#forLoop 1 10 1}}
								<option value="{{index}}A">{{index}}</option>
							{{/forLoop}}
						</select>
					</label>
				</div>
				<div class="col-xs-12 col-md-4">
					<div class="input-daterange">
						<label>{{serviceButtonConfig (getValueByKey search_config "srb") "dateInLabel"}}
							<input type="text" name="date" class="form-control date" />
						</label>
					</div>
				</div>
				{{#each amenity_filters}}
					<div class="col-xs-12 col-md-3">
						<label>{{control_label}}
							<select class="amenity-filter form-control" name="{{amenity_category}}" multiple="multiple"></select>
						</label>
					</div>
				{{/each}}
			</div>
				
			<button name="search" class="btn btn-block btn-search">{{serviceButtonConfig (getValueByKey search_config "srb") "searchButtonLabel"}}</button>
		</div>
	</div>
</script>

<script class="resultTemplate" type="text/x-handlebars-template">
	{{#everyNth results 2}}
		{{#if isModZeroNotFirst}}
				</div>
			</div>
		{{/if}}
		{{#if isModZero}}
			<div class="col-xs-12">
				<div class="row">
		{{/if}}
		<div class="col-xs-12 col-md-6 day-tours-result">
			<div class="row">
				<div class="col-xs-12">
					<a href="{{productURL destinationUrl product}}">
						<h4 class="tourResult-tour-title">{{product.name}}</h4>
					</a>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<p class="tourSearchNote">{{getNotes product.notes 'SDO' 'text'}}</p>
				</div>
				<div class="col-xs-12 col-sm-4 tour-result-image">
					<a href="{{productURL destinationUrl product}}">
						{{optionImage product.code 'img-responsive tourSearchImage'}}
					</a>
				</div>
				<div class="col-xs-12 col-sm-8">
					<div class="row">
						<div class="col-xs-8 tourResult-icon-div">
							{{#amenities product.amenities 'TIC'}}
								<span>{{tourIcon key value "tourIconSmall"}}</span>
							{{/amenities}}
						</div>
						<div class="col-xs-4 tourResult-price-div">
							<span>{{serviceButtonConfig (getValueByKey ../searchConfigs.search_config "srb") "searchPricePrefix"}}</span>
							<div class="tourResult-price">
								${{displayPrice availability.TotalPrice 2}}
							</div>
							<span>{{serviceButtonConfig (getValueByKey ../searchConfigs.search_config "srb") "searchPriceSuffix"}}</span>
						</div>
					</div>



					<div class="row durationRow">
						<div class="col-xs-3">
							<label>Duration:</label>
						</div>
						<div class="col-xs-9">
							<span>
								{{#amenities product.amenities 'TDU'}}
								{{value}}
								{{/amenities}}
							</span>
						</div>
					</div>
					<div class="row departRow">
						<div class="col-xs-3">
							<label>Depart:</label>
						</div>
						<div class="col-xs-9">
							<span>{{product.dst}}</span>
						</div>
					</div>
					<div class="row visitRow">
						<div class="col-xs-3">
							<label>Visit:</label>
						</div>
						<div class="col-xs-9">
							<span class="tourResult-visit-content">
								{{#amenities product.amenities 'TVI'}}
								{{value}}{{#unless last}},{{/unless}}
								{{/amenities}}
							</span>
						</div>
					</div>
					
					
					<div class="row">
						<div class="col-xs-12">
							<a href="{{productURL destinationUrl product}}">
								<div class="tourLink">Details</div>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		{{#if isLast}}
				</div>
			</div>
		{{/if}}
	{{/everyNth}}
</script>

<script class="noResultTemplate" type="text/x-handlebars-template">
	<div class="col-xs-12">
		<h4 class="text-center">{{serviceButtonConfig (getValueByKey search_config "srb") "notFoundLabel"}}</h4>
	</div>
</script>