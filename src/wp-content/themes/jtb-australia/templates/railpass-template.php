<script class="controlTemplate" type="text/x-handlebars-template">
	<div class="resultControl"></div>
</script>

<script class="resultTemplate" type="text/x-handlebars-template">

	<div class="row">
		<div class="price adult col-xs-6 {{#if wpRates.adult}}multipleRates{{/if}}">
		<div class="rail_price_inner">
			<p class="age">Adult {{ageRange info.Option.OptGeneral.Adult_From info.Option.OptGeneral.Adult_To}}</p>
			<p class="amount"><span class="symbol">{{serviceButtonConfig "Rail" "productPricePrefix"}}</span><br />
			{{#if rates.adult}}
			{{displayPrice rates.adult 0}}
			{{else}}
			{{serviceButtonConfig "Rail" "rateNotAvailLabel" "N/A"}}
			{{/if}}
			</p>
			{{#if configs.JRAdultRate}}
			<p class="wp-amount">{{configs.JRCurrency}}<br />{{configs.JRAdultRate}}</p>
			{{/if}}

			<select class="passenger-counter" name="adults" {{#unless rates.adult}}disabled="disabled"{{/unless}}>
			{{#forLoop 0 11 1}}
				<option value='{{index}}A'>{{index}}</option>
			{{/forLoop}}
			</select>

			</div>
			<i class="fa fa-caret-down down-arrow left pull-right"></i>
		</div>

		<div class="price child col-xs-6 {{#if wpRates.child}}multipleRates{{/if}}">
			<div class="rail_price_inner">
			<p class="age">Child {{ageRange info.Option.OptGeneral.Child_From info.Option.OptGeneral.Child_To}}</p>
			<p class="amount"><span class="symbol">{{serviceButtonConfig "Rail" "productPricePrefix"}}</span><br />
			{{#if rates.child}}
			{{displayPrice rates.child 0}}
			{{else}}
			{{serviceButtonConfig "Rail" "rateNotAvailLabel" "N/A"}}
			{{/if}}
			</p>
			{{#if configs.JRChildRate}}
			<p class="wp-amount">{{configs.JRCurrency}}<br />{{configs.JRChildRate}}</p>
			{{/if}}

			<select class="passenger-counter" name="children" {{#unless rates.child}}disabled="disabled"{{/unless}}>
			{{#forLoop 0 11 1}}
				<option value='{{index}}C'>{{index}}</option>
			{{/forLoop}}
			</select>

			</div>
			<i class="fa fa-caret-down down-arrow right pull-left"></i>
		</div>

		<button type="button" class="book">Buy Now</button>
	</div>
</script>