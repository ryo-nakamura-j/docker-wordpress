{{#if products}}
	{{#everyNth products 2}}
	{{#if isModZeroNotFirst}}
	</div>
	{{/if}}
	{{#if isModZero}}
	<div class="row tourResult-row">
	{{/if}}
		<div class="col-xs-12 col-md-6">
			<div class="row">
				<div class="col-xs-12">
					<a href="{{productLink this 'Tours'}}">
						<h4 class="tourResult-tour-title">{{name}}</h4>
					</a>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<p class="tourSearchNote">{{getNotes notes 'SDO' 'text'}}</p>
				</div>
				<div class="col-xs-12 col-md-4 tour-result-image">
					<a href="{{productLink this 'Tours'}}">
						{{optionImage code 'img-responsive tourSearchImage'}}
					</a>
				</div>
				<div class="col-xs-12 col-md-8">
					<div class="row">
						<div class="col-xs-6 tourResult-icon-div">
							{{#amenities amenities 'TIC'}}
								<span>{{tourIcon key value "tourIconSmall"}}</span>
							{{/amenities}}
						</div>
						<div class="col-xs-6 tourResult-price-div">
							<span>From</span>
							<div class="tourResult-price">
							{{#getAvailability this}}
								{{Currency}} ${{displayPrice TotalPrice 2}}
							{{/getAvailability}}
							</div>
							<span>Per Person</span>
						</div>
					</div>

					<div class="row durationRow">
						<div class="col-xs-3">
							<label>Duration:</label>
						</div>
						<div class="col-xs-9">
							<span>
								{{#amenities amenities 'TDU'}}
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
							<span>{{dst}}</span>
						</div>
					</div>
					<div class="row visitRow">
						<div class="col-xs-3">
							<label>Visit:</label>
						</div>
						<div class="col-xs-9">
							<span class="tourResult-visit-content">
								{{#amenities amenities 'TVI'}}
								{{value}}{{#unless last}},{{/unless}}
								{{/amenities}}
							</span>
						</div>
					</div>
					
					<div class="row">
						<div class="col-xs-12">
							<a href="{{productLink this 'Tours'}}">
								<div class="tourLink">Details</div>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	{{#if isLast}}
	</div>
	{{/if}}
	{{/everyNth}}
{{else}}
	Sorry, no results were found
{{/if}}