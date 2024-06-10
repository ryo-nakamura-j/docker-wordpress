<div class="row tourDate">
	<div class="col-xs-12">
		<span>{{displayDate date 'ddd D'}}</span>
	</div>
</div>

<div class="tourPrices">

	{{#if hasRates}}

		{{#if adult}}
		<form class="form-horizontal">
			<div class="form-group">
				<div class="col-xs-4">
					<div><label class="control-label">Adult:</label></div>
					<div><label class="control-label age-label">({{ageBrackets.adult}})</label></div>
				</div>
				<label class="col-xs-3 control-label">${{displayPrice adult.TotalPrice 2}}</label>
				<div class=" col-xs-5">
					<select id="{{selectors.adultSelect}}" class="form-control">
						{{#forLoop 0 11 1}}
							<option value='{{index}}'>{{index}}</option>
						{{/forLoop}}
					</select>
				</div>
			</div>
		</form>
		{{/if}}


		{{#if child}}
		<form class="form-horizontal">
			<div class="form-group">
				<div class="col-xs-4">
					<div><label class="control-label">Child:</label></div>
					<div><label class="control-label age-label">({{ageBrackets.child}})</label></div>
				</div>
				<label class="col-xs-3 control-label">${{displayPrice child.TotalPrice 2}}</label>
				<div class="col-xs-5">
					<select id="{{selectors.childSelect}}" class="form-control">
						{{#forLoop 0 11 1}}
							<option value='{{index}}'>{{index}}</option>
						{{/forLoop}}
					</select>
				</div>
			</div>
		</form>
		{{/if}}

		{{#if infant}}
		<form class="form-horizontal">
			<div class="form-group">
				<div class="col-xs-4">
					<div><label class="control-label">Infant:</label></div>
					<div><label class="control-label age-label">({{ageBrackets.infant}})</label></div>
				</div>
				<label class="col-xs-3 control-label">${{displayPrice infant.TotalPrice 2}}</label>
				<div class="col-xs-5">
					<select id="{{selectors.infantSelect}}" class="form-control">
						{{#forLoop 0 11 1}}
							<option value='{{index}}'>{{index}}</option>
						{{/forLoop}}
					</select>
				</div>
			</div>
		</form>
		{{/if}}

		{{#if pickupPoints}}
		<form class="form-horizontal">
			<div class="form-group">
				<div class="col-xs-12">
					<select id="{{selectors.pickupSelect}}" class="form-control">
						<option value="">-- Select pickup point --</option>
						{{#each pickupPoints}}
						<option value="{{rateId}}">{{pickupPoint}}</option>
						{{/each}}
					</select>
				</div>
			</div>
		</div>
		{{/if}}

	{{else}}
		<div class="row">
			<div class="col-xs-offset-2 col-xs-8">
				<h3 class="tourProduct-dateUnavailable">Not Available</h3>
			</div>
		</div>
	{{/if}}
</div>

{{#if hasRates}}
<div class="row tourBuyRow">
	<div class="col-xs-12">
		<input class="tourBuyButton" type="button" id="{{selectors.buyButton}}" value="Buy Now" disabled/>
	</div>
</div>
{{/if}}