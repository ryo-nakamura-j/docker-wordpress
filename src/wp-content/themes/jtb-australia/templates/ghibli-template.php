<script class="masterTemplate" type="text/x-handlebars-template">
<div class="refreshSearchSection">
	<div class="row">
		<div class="col-xs-12">
			<div class="ticketSearchPanel clearfix">
				<h3>{{section_heading}}</h3>
				<div class="row">
					<div class="clearfix ticketSearchPanelInner">
						<div class="col-xs-12 col-md-2">
							<label>
								<div class="row">
									<div class="col-xs-12">
										<div class="controlTitle">Ticket Date</div>
									</div>
									<div class="col-xs-12 hidden-xs hidden-sm">&nbsp;</div>
								</div>
								<input type="text" class="datepicker searchDate form-control" name="date" />
							</label>
						</div>
						<div class="col-xs-12 col-md-6">
							<div class="row">
								<div class="productSearchSection"></div>
							</div>
						</div>
						<div class="col-xs-12 col-md-2 col-md-offset-2">
							<div class="row">
								<div class="col-xs-12 hidden-xs hidden-sm">&nbsp;</div>
								<div class="col-xs-12">
									<label>&nbsp;
										<button class="btn btn-success btn-block" name="search">Search</button>
									</label>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="productInfoSection" style="display:none;">
</div>
</script>

<script class="productSearchTemplate" type="text/x-handlebars-template">
	<div class="col-xs-6 col-md-3">
		<label>
			<div class="row">
				<div class="col-xs-12">
					<div class="controlTitle">
						{{controlConfig.title}}
					</div>
				</div>
				<div class="col-xs-12">
					<div class="priceElement">&nbsp;</div>
				</div>
			</div>

			<select class="form-control" name="{{controlConfig.paxType}}">
			{{#forLoop 0 11 1}}
				<option value="{{index}}">{{index}}</option>
			{{/forLoop}}
			</select>
		</label>
	</div>
</script>

<script class="productResultTemplate" type="text/x-handlebars-template">
	<div class="col-xs-6 col-md-3 md-top-margin">
		<div class="adultsLabel">{{controlConfig.title}}</div>
		<div class="qtyAdults">
			{{getProperty qtyObj controlConfig.paxType}}
		</div>
	</div>
</script>

<script class="productInfoTemplate" type="text/x-handlebars-template">
	<div class="row">
		<div class="col-xs-12">
			<div class="ticketResultPanel clearfix">
				<div class="row">
					<div class="clearfix ticketResultPanelInner">
						<div class="col-xs-12 col-md-2">
							<div class="productName">{{title}}</div>
						</div>
						<div class="col-xs-12 col-md-6">
							<div class="row">
								<div class="productResultSection"></div>
							</div>
						</div>
						<div class="col-xs-12 col-md-2 md-top-margin">
							<div class="totalPriceLabel">Total Price</div>
							<div class="totalPrice"></div>
						</div>
						<div class="col-xs-12 col-md-2 md-top-margin">
							<button class="btn btn-success btn-block book" name="book">Book</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</script>

<script class="noResultTemplate" type="text/x-handlebars-template">
<div class="ticketResultPanel clearfix">
	<div class="row">
		<div class="ticketResultPanelInner clearfix">
			<div class="col-xs-12">
				{{serviceButtonConfig service_button "notFoundLabel"}}
			</div>
		</div>
	</div>
</div>
</script>