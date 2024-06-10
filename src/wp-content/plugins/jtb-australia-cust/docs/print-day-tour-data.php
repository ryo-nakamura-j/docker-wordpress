<script class="masterTemplate" type="text/x-handlebars-template">
	<div class="productInfoSection">
		<div class="row">
			<div class="col-xs-12">
				<image src="{{loadingImage}}" class="img-responsive center-block" />
			</div>
		</div>
	</div>
</script>

<script class="productInfoTemplate" type="text/x-handlebars-template">
	<div class="row">
		<div class="col-xs-12 col-md-4">
			<div class="row">
				<div class="col-xs-12 col-md-12">
					<h2>{{product.name}}</h2>
				</div>
				<div class="col-xs-12 col-md-7">
				{{#amenities product.amenities "TIC"}}
				<span>{{tourIcon key value "tourIcon"}}</span>
				{{/amenities}}
				</div>
				<div class="col-xs-12 col-md-5 tourProduct-priceDiv">
					<span>From</span>
					<div class="tourProduct-price fromPrice">
						{{serviceButtonConfig product.srb "productPricePrefix"}}
					</div>
					<span>Per Person</span>
				</div>
			</div>
			
			<p>{{{getNotes product.notes 'SDO' 'html'}}}</p>
		</div>
		<div class="col-xs-12 col-md-8 customjtbaulayout">

			<div id="product-slider" class="carousel slide carousel-fade" data-ride="carousel">
			<div class="col-xs-12 col-md-6">
				<ul class="carousel-indicators">
					{{#forLoop 0 10 1}}
					<li data-target="#product-slider" data-slide-to="{{index}}"{{#if first}}class="active"{{/if}}>
							<img 
								src="{{imagesBaseURL}}/Supplier_{{../supplier.code}}/Option_{{../product.code}}/{{../product.code}}.{{indexPlusOne}}.jpg" 
								alt="slide-{{index}}"
								onerror="this.onerror = null; {{#if first}}this.src = '{{defaultImageURL}}';{{else}}$(this).parent().remove();{{/if}}"
							>
						</li>
					{{/forLoop}}
				</ul>
</div><div class="col-xs-12 col-md-6">
				<div style="position:relative">
					<div class="carousel-inner">
						{{#forLoop 0 10 1}}
							<div class="item{{#if first}} active{{/if}}">
								<img 
								src="{{imagesBaseURL}}/Supplier_{{../supplier.code}}/Option_{{../product.code}}/{{../product.code}}.{{indexPlusOne}}.jpg" 
								alt="slide-{{index}}" 
								class="fullwidth"
								onerror="this.onerror = null; {{#if first}}this.src = '{{defaultImageURL}}';{{else}}$(this).parent().remove();{{/if}}"
								>
							</div>

						{{/forLoop}}
					</div>

					<a class="left carousel-control" href="#product-slider" role="button" data-slide="prev">
						<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
						<span class="sr-only">Previous</span>
					</a>
					<a class="right carousel-control" href="#product-slider" role="button" data-slide="next">
						<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
						<span class="sr-only">Next</span>
					</a>

				</div>
				</div>


			</div>

		</div>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<h4 id="tourPricePanelTitle">{{product.name}}</h4>
		</div>
	</div>
	<div id="tourPricePanel" class="row">
		<div class="col-xs-12">
			<div class="row">
				<div class="col-xs-12 col-sm-6 col-md-3">
					<div class="product-section search-date-minus-1">
						<img src="{{loadingImage}}">
						<span>Loading</span>
					</div>
				</div>
				<div class="col-xs-12 col-sm-6 col-md-3">
					<div class="product-section search-date" >
						<img src="{{loadingImage}}">
						<span>Loading</span>
					</div>
				</div>
				<div class="col-xs-12 col-sm-6 col-md-3">
					<div class="product-section search-date-plus-1">
						<img src="{{loadingImage}}">
						<span>Loading</span>
					</div>
				</div>
				<div class="col-xs-12 col-sm-6 col-md-3">
					<div class="product-section search-date-plus-2">
						<img src="{{loadingImage}}">
						<span>Loading</span>
					</div>
				</div>
			</div>
		</div>
		<!-- <div class="col-xs-12">
			<div class="row" style="margin-top:10px;">
				<div class="col-xs-12 col-md-6">
					<form class="form-inline">
						<div class="form-group tourProduct-refreshDateGroup">
							<label for="dateField">Search Again:</label>
							<input id="{{../selectors.refreshDateField}}" type="date" class="form-control"/>
							<input id="{{../selectors.refreshDateButton}}" type="button" class="form-control" value="Refresh" />
						</div>
					</form>
				</div>
				<div class="col-xs-12 col-md-offset-2 col-md-4 newTourSearchDiv">
					<a id="{{../selectors.newTourSearchButton}}" class="btn btn-default" href="{{../newSearchURL}}">New Tour Search</a>
				</div>
			</div>
		</div> -->
	</div>


	<div class="row">
		<div class="col-xs-12">
			<h3>Tour Details</h3>
			<div class="ribon-red-desktop"></div>
		</div>
	</div>

	{{#with product}}
	<div class="row">
		<div class="col-xs-12 col-md-6">
			<label class="tourDetailLabel">Departure:</label> <span class="tourDetail">{{dst}}</span>
		</div>
		<div class="col-xs-12 col-md-6">
			<label class="tourDetailLabel">Time:</label> <span class="tourDetail">{{getNotes notes 'TTI' 'text'}}</span>
		</div>
		<div class="col-xs-12 col-md-6">
			<label class="tourDetailLabel">Duration:</label> <span class="tourDetail">{{#amenities amenities "TDU"}} {{value}} {{/amenities}}</span>
		</div>
		<div class="col-xs-12 col-md-6">
			<label class="tourDetailLabel">Guide:</label> <span class="tourDetail">{{getNotes notes 'TGU' 'text'}}</span>
		</div>
		<div class="col-xs-12 col-md-6">
			<label class="tourDetailLabel">Destinations:</label> <span class="tourDetail">{{#amenities amenities "TDE"}} {{value}} {{/amenities}}</span>
		</div>
		<div class="col-xs-12 col-md-6">
			<label class="tourDetailLabel">Meals:</label> <span class="tourDetail">{{getNotes notes 'TME' 'text'}}</span>
		</div>
		<div class="col-xs-12">
			<label class="tourDetailLabel">Visits:</label> <span class="tourDetail">{{#amenities amenities "TVI"}} {{value}}{{#unless last}},{{/unless}} {{/amenities}}</span>
		</div>
		<div class="col-xs-12">
			<label class="tourDetailLabel pickupsLabel">Pickups:</label> <div class="tourDetail pickupPointsDetail">{{../pickups}}</div>
		</div>
	</div>
	{{/with}}

	<div class="row">
		<div class="col-xs-12">
			<h3>Tour Itinerary</h3>
			<div class="ribon-red-desktop"></div>
			<p>{{{getNotes product.notes 'LDO' 'html'}}}</p>
		</div>
	</div>
</script>

<script class="resultTemplate" type="text/x-handlebars-template">
	{{#if availability.adult}}
	<div class="row">
		<div class="col-xs-12">
			<table class="productInfo">
				<tr>
					<th colspan="3" class="dateHeading">{{formatDate availability.adult.Date 'ddd Do MMM'}}</th>
				</tr>
				<tr>
					<th scope="row">Adult</th>
					<td>${{displayPrice availability.adult.TotalPrice 2}}</td>
					<td><select class="form-control" name="adult">{{#forLoop 0 10 1}}<option value="{{index}}">{{index}}</option>{{/forLoop}}</select></td>
				</tr>
				<tr>
					<th scope="row">Child</th>
					<td>${{displayPrice availability.child.TotalPrice 2}}</td>
					<td><select class="form-control" name="child">{{#forLoop 0 10 1}}<option value="{{index}}">{{index}}</option>{{/forLoop}}</select></td>
				</tr>
				<tr>
					<td colspan="3"><select name="pickup" class="form-control"></select></td>
				</tr>
			</table>
		</div>
		<div class="col-xs-12">
			<div class="button-div">
				<button name="book" class="btn btn-book btn-success book">Book</button>
			</div>
		</div>
	</div>
	{{else}}
	<div class="row">
			<div class="col-xs-12">
		<div style="background:white; padding:1em 0; margin:1em 0;">
				<h4 class="text-center">No Rates Available</h4>
			</div>
		</div>
	</div>
	{{/if}}
</script>