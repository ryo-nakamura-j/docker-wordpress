{{#each engineResponse.products}}
	<div class="row">
		<div class="col-xs-12 col-md-8">
			<div class="row">
				<div class="col-xs-12 col-md-9">
					<h2>{{name}}</h2>
				</div>
				<div class="col-xs-12 col-md-3 tourProduct-priceDiv">
					<span>From</span>
					<div class="tourProduct-price">
						AUD $<span id="{{../selectors.tourPrice}}">
						{{#if ../../fromPrice}}
						{{displayPrice ../../fromPrice 2}}
						{{/if}}
						</span>
					</div>
					<span>Per Person</span>
				</div>
			</div>
			{{#amenities amenities "TIC"}}
				<span>{{tourIcon key value "tourIcon"}}</span>
			{{/amenities}}
			<p>{{{getNotes notes 'SDO' 'html'}}}</p>
		</div>
		<div class="col-xs-12 col-md-4">
			<div id="product-slider" class="carousel slide carousel-fade" data-ride="carousel">
				<div class="carousel-inner">
					{{#each ../images}}
					{{#if @first}}
					<div class="item {{#if @first}}active{{/if}}">
						<img src="{{this}}" onerror="this.onerror=null;$(this).attr('src', '{{defaultImage}}');" alt="slide-{{@index}}"/>
					</div>
					{{else}}
					<div class="item">
						<img src="{{this}}" onerror="$(this.parentNode).remove();" alt="slide-{{@index}}"/>
					</div>
					{{/if}}
					{{/each}}
				</div>
				<ul class="carousel-indicators">
					{{#each ../images}}
					<li data-target="#product-slider" data-slide-to="{{@index}}" class="{{#if @first}}active{{/if}}">
						<img src="{{this}}" onerror="$(this.parentNode).remove();" alt="slide-{{@index}} thumbnail" />
					</li>
					{{/each}}
				</ul>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<h4 id="tourPricePanelTitle">{{name}}</h4>
		</div>
	</div>
	<div id="tourPricePanel" class="row">
		<div class="col-xs-12">
			<div class="row">
				<div id="date-minus-1" class="col-xs-12 col-md-3">
					<img src="{{loadingImage}}">
					<span>Loading</span>
				</div>
				<div id="date-minus-0" class="col-xs-12 col-md-3">
					<img src="{{loadingImage}}">
					<span>Loading</span>
				</div>
				<div id="date-plus-1" class="col-xs-12 col-md-3">
					<img src="{{loadingImage}}">
					<span>Loading</span>
				</div>
				<div id="date-plus-2" class="col-xs-12 col-md-3">
					<img src="{{loadingImage}}">
					<span>Loading</span>
				</div>
			</div>
		</div>
		<div class="col-xs-12">
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
		</div>
	</div>


	<div class="row">
		<div class="col-xs-12">
			<h3>Tour Details</h3>
			<div class="ribon-red-desktop"></div>
		</div>
	</div>

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
			<label class="tourDetailLabel pickupsLabel">Pickups:</label> <div class="tourDetail pickupsDetail">{{../pickups}}</div>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<h3>Tour Itinerary</h3>
			<div class="ribon-red-desktop"></div>
			<p>{{{getNotes notes 'LDO' 'html'}}}</p>
		</div>
	</div>

	{{!--
	<div class="row">
		<div class="col-xs-12">
			<h3>Tour Remarks</h3>
			<div class="ribon-red-desktop"></div>
		</div>
	</div>
	--}}
{{/each}}
