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
		<div class="col-xs-12 col-md-8">
			<div class="row">
				<div class="col-xs-12 col-md-9">
					<h2>{{product.name}}</h2>
				</div>
				<div class="col-xs-12 col-md-3 tourProduct-priceDiv">
					<span>From</span>
					<div class="tourProduct-price fromPrice">
					</div>
					<span>Per Person</span>
				</div>
			</div>
			{{#amenities product.amenities "TIC"}}
				<span>{{tourIcon key value "tourIcon"}}</span>
			{{/amenities}}
			<p>{{{getNotes product.notes 'SDO' 'html'}}}</p>
		</div>
		<div class="col-xs-12 col-md-4">

			<div id="product-slider" class="carousel slide carousel-fade" data-ride="carousel">
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

	<div id="tourArrangementsPanel" >
		<!-- Holding input fields for arrangements -->
	</div>

	<div class="row">
		<div class="col-xs-12">
			<h3>Tour Itinerary</h3>
			<div class="ribon-red-desktop"></div>
			<p>{{{getNotes product.notes 'LDO' 'html'}}}</p>
		</div>
	</div>
</script>

<script class="arrangementsTemplate" type="text/x-handlebars-template">
	{{#if data.visible }}
	<div class="row">
		<h3>Tour Arrangements</h3>
		<div class="ribon-red-desktop"></div>
	</div>
	<div class="row arrangements-container">
		{{#each data.arrangements}}
		<div class="col-xs-12 arrangement-row" name="{{this.ArrangementName}}">
			{{#each this.Details }}
			{{#switch this.InputType }}
			{{#case "LIST" break=true}}
			{{#switch this.SelectionType}}
			{{#case "MULTIPLE" break=true}}
			<div class="arrangement-details arrangements-checkbox-list {{#if this.IsRequiredFlag}}{{!-- treated as not required, even is required--}}{{/if}}" name="{{this.ArrangementID}}">
				<div class="col-xs-12 col-md-3 arrangements-col1">
					<label class="{{#if this.IsRequiredFlag}}required_asterisk{{/if}}"> 	
						{{this.SelectionCodeMessage}} 
					</label>
					<span></span>
					{{#unless this.IsInputMessageEmpty }}
					<div class="tp-mobile-tooltip small">
						{{../this.InputMessage}}
					</div>
					{{/unless}}
				</div>
				<div class="col-xs-12 col-md-4 arrangements-col2" name="{{this.ArrangementID}}">
					{{#each this.Options }}
					{{#if @first}}
					{{#forLoop 1 ../../../../../../../../data.paxCount 1}}
					<div class="row">
						<div class="col-xs-12 col-md-12">
							Pax {{index}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<input type="checkbox" code="{{../this.SelectionCode}}">
							{{../this.SelectionName}}
						</div>
					</div>
					{{/forLoop}}
					{{/if}}
					{{/each}}
					<br>
				</div>
				{{#unless this.IsInputMessageEmpty }}
				<div class="col-xs-12 col-md-1 arrangements-col3">
					<div class="round-social-grey tp-tooltip">
						<a class="sprite fa fa-question" data-toggle="tooltip" title="{{this.InputMessage}}"></a>
					</div>
				</div>
				{{/unless}}
			</div>
			{{/case}}
			{{#case "ONE" break=true}}
			<div class="arrangement-details arrangements-dropdown {{#if this.IsRequiredFlag}}required{{/if}}" name="{{this.ArrangementID}}">
				<!-- Dropdown is always a required field for GAccess, it always has to be supplied in a request-->
				<div class="col-xs-12 col-md-3 arrangements-col1">
					<label class="required_asterisk"> 	
						{{this.SelectionCodeMessage}} 
					</label>
					<span></span>
					{{#unless this.IsInputMessageEmpty }}
					<div class="tp-mobile-tooltip small">
						{{../this.InputMessage}}
					</div>
					{{/unless}}
				</div>
				<div class="col-xs-12 col-md-4 arrangements-col2 " name="{{this.ArrangementID}}">
					<select class="form-control">
						{{#each this.Options }}
						<option value="{{this.SelectionCode}}">{{this.HotelName}} {{this.Time}}</option>
						{{/each}}
					</select>
					<br>
				</div>
				{{#unless this.IsInputMessageEmpty }}
				<div class="col-xs-12 col-md-1 arrangements-col3">
					<div class="round-social-grey tp-tooltip">
						<a class="sprite fa fa-question" data-toggle="tooltip" title="{{this.InputMessage}}"></a>
					</div>
				</div>
				{{/unless}}
			</div>
			{{/case}}
			{{/switch}}
			{{/case}}
			{{#case "VALUE" "VALUE_FORMAT" break=true}}
			{{#switch this.Format}}
			{{#case "INDATE" break=true}}
			<div class="arrangement-details arrangements-datepicker {{#if this.IsRequiredFlag}}required{{/if}}" name="{{this.ArrangementID}}">
				<div class="col-xs-12 col-md-3 arrangements-col1">
					<label class="{{#if this.IsRequiredFlag}}required_asterisk{{/if}}"> 	
						{{this.SelectionCodeMessage}} 
					</label>
					<span></span>
					{{#unless this.IsInputMessageEmpty }}
					<div class="tp-mobile-tooltip small">
						{{../this.InputMessage}}
					</div>
					{{/unless}}
				</div>
				<div class="col-xs-12 col-md-5 arrangements-col2" name="{{this.ArrangementID}}">
					<input type="text" name="date" class="form-control datepicker"/>
					<br>
				</div>
				{{#unless this.IsInputMessageEmpty }}
				<div class="col-xs-12 col-md-1 arrangements-col3">
					<div class="round-social-grey tp-tooltip">
						<a class="sprite fa fa-question" data-toggle="tooltip" title="{{this.InputMessage}}"></a>
					</div>
				</div>
				{{/unless}}
			</div>
			{{/case}}
			{{#default}}
			<div class="arrangement-details arrangements-textarea {{#if this.IsRequiredFlag}}required{{/if}}" validation="{{this.Format}}" name="{{this.ArrangementID}}">
				<div class="col-xs-12 col-md-3 arrangements-col1">
					<label class="{{#if this.IsRequiredFlag}}required_asterisk{{/if}}"> 	
						{{this.SelectionCodeMessage}} 
					</label>
					<span></span>
					{{#unless this.IsInputMessageEmpty }}
					<div class="tp-mobile-tooltip small">
						{{../this.InputMessage}}
					</div>
					{{/unless}}
				</div>
				<div class="col-xs-12 col-md-5 arrangements-col2" name="{{this.ArrangementID}}">
					{{#if this.IsPaxInputUnitPax }}
					{{#forLoop 1 ../../../../../../../data.paxCount 1}}
					<div class="row">
						<div class="col-xs-12 col-md-12">
							<span >Pax {{index}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
							<textarea class="form-control" style="height: 50px;" validation="{{../../this.Format}}" rows="5" >{{../../this.DefaultValue}}</textarea>
						</div>
					</div>
					<br>
					{{/forLoop}}
					{{/if}}
					{{#unless this.IsPaxInputUnitPax}}
					<textarea class="form-control" validation="{{this.Format}}" rows="5" >{{this.DefaultValue}}</textarea>
					<br>
					{{/unless}}
				</div>
				{{#unless this.IsInputMessageEmpty }}
				<div class="col-xs-12 col-md-1 arrangements-col3">
					<div class="round-social-grey tp-tooltip">
						<a class="sprite fa fa-question" data-toggle="tooltip" title="{{this.InputMessage}}"></a>
					</div>
				</div>
				{{/unless}}
			</div>
			{{/default}}
			{{/switch}}
			{{/case}}
			{{/switch}}
			{{/each}}
		</div>
		{{#if @last}}
		<div class="col-xs-12">
			<button type="button" id="book_now" class="tp-booking-button btn btn-warning btn-lg pull-right">
				Book Now
			</button>
		</div>
		{{/if}}
		{{else}}
		<div class="col-xs-12">
			<div>No Content</div>
		</div>
		{{/each}}
	</div>
	{{/if}}
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
					<th scope="row">Adult<br/>{{ageBrackets.adult}}</th>
					<td>${{displayPrice availability.adult.TotalPrice 2}}</td>
					<td><select class="form-control" name="adult">{{#forLoop 0 10 1}}<option value="{{index}}">{{index}}</option>{{/forLoop}}</select></td>
				</tr>
				{{#if availability.child}}
				<tr>
					<th scope="row">Child<br/>{{ageBrackets.child}}</th>
					<td>${{displayPrice availability.child.TotalPrice 2}}</td>
					<td><select class="form-control" name="child">{{#forLoop 0 10 1}}<option value="{{index}}">{{index}}</option>{{/forLoop}}</select></td>
				</tr>
				{{/if}}
				<tr>
					<td colspan="3"><select name="pickup" class="form-control"></select></td>
				</tr>
			</table>
		</div>
		<div class="col-xs-12">
			<div class="button-div">
				<button name="book" class="btn btn-book btn-success book">{{this.buttonName}}</button>
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