<script class="masterTemplate" type="text/x-handlebars-template">
	<div class="productSearchSection">
		<div class="row">
			<div class="col-xs-12">
				<image src="{{loadingImage}}" class="img-responsive center-block" />
			</div>
		</div>
	</div>
	<div class="resultsSection"></div>
</script>

<script class="productSearchTemplate" type="text/x-handlebars-template">
	<div class="productSearchControl">
		<h3>Hotel & Ryokan Search</h3>
		<div class="searchControls">

			<div class="row">
				<input type="hidden" name="srb" />
				<input type="hidden" name="cty" />
					
				<div class="col-xs-12 col-sm-4">
					<label>Destination
						<select class="form-control" name="dst"></select>
					</label>
				</div>
				
				<div class="col-xs-12 col-sm-4">
					<label>Region
						<select class="form-control" name="lcl"></select>
					</label>
				</div>

				<div class="col-xs-12 col-sm-4">
					<label>Rooms
						<select name="qty" class="form-control" >
							{{#forLoop 1 10 1}}
								<option value="{{index}}">{{index}}</option>
							{{/forLoop}}
						</select>
					</label>
				</div>
			</div>

			<div class="row">
				<div class="input-daterange">
					<div class="col-xs-12 col-sm-4">
						<label>Check In
							<input type="text" name="date" class="form-control date" />
						</label>
					</div>
					<div class="col-xs-12 col-sm-4">
						<label>Check Out
							<input type="text" name="toDate" class="form-control date" />
						</label>
					</div>
					<div class="col-xs-12 col-sm-4">
						<label>Nights
							<select name="scu" class="form-control" >
								{{#forLoop 1 30 1}}
									<option value="{{index}}">{{index}}</option>
								{{/forLoop}}
							</select>
						</label>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 col-sm-offset-2 col-sm-8">
					<button name="search" class="btn-search btn btn-block">Search</button>
				</div>
			</div>
	</div>
</script>

<script class="resultTemplate" type="text/x-handlebars-template">
	{{#each results}}
	<div class="accommodation-product-result">
		<div class="row">

			<div class="col-xs-12 col-sm-6 col-md-4">
				<a href="{{supplierURL destinationUrl supplier product}}">
					<div id="carousel-{{product.code}}" class="carousel slide" data-ride="carousel">
						<div class="carousel-inner" role="listbox">
							{{#forLoop 0 3 1}}
								<div class="item{{#if first}} active{{/if}}">
									<img 
									src="{{imagesBaseURL}}/Supplier_{{../supplier.code}}/{{../supplier.code}}.{{indexPlusOne}}.jpg" 
									alt="slide-{{index}}" 
									class="fullwidth"
									onerror="this.onerror = null; {{#if first}}this.src = '{{defaultImageURL}}';{{else}}$(this).parent().remove();{{/if}}"
									>
								</div>

							{{/forLoop}}
						</div>
					</div>
				</a>
				<a class="left carousel-control" href="#carousel-{{product.code}}" role="button" data-slide="prev">
					<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
					<span class="sr-only">Previous</span>
				</a>
				<a class="right carousel-control" href="#carousel-{{product.code}}" role="button" data-slide="next">
					<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
					<span class="sr-only">Next</span>
				</a>
			</div>

			<div class="col-xs-12 col-sm-6 col-md-8">
				<div class="productDetails">
					<div class="row">
						<div class="col-xs-12">
							<a href="{{supplierURL destinationUrl supplier product}}">
								<h3>{{supplier.name}} <small>{{product.name}}</small></h3>
							</a>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12 col-md-9">
							<p class="tstNote"><i class="fa fa-train"></i> {{getNotes supplier.notes "TST" "text"}}</p>
							<p class="sdsNote">{{getNotes supplier.notes "SDS" "text"}}</p>
							<a href="{{supplierURL destinationUrl supplier product}}">
								<p class="readMore">Read More...</p>
							</a>
						</div>
						<div class="col-xs-12 col-md-3">
							<div class="priceSection">
								<span class="price-label">from</span>
								<h3 class="price">${{displayPrice availability.pricePerSCU 2}}</h3>
								<span class="price-label">per night</span>
							</div>
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>
	{{/each}}
</script>

<script class="noResultTemplate" type="text/x-handlebars-template">
	<div class="col-xs-12">
		<h4 class="text-center">{{serviceButtonConfig "Accommodation" "notFoundLabel"}}</h4>
	</div>
</script>