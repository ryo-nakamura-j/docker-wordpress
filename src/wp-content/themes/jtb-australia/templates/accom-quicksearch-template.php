<script class="masterTemplate" type="text/x-handlebars-template">
	<div class="productSearchSection"></div>
</script>

<script class="productSearchTemplate" type="text/x-handlebars-template">
	<h3>Hotel & Ryokan Search</h3>

	<div class="searchControls">

		<input type="hidden" name="srb" />
		<input type="hidden" name="cty" />

		<label>Destination
			<select class="form-control" name="dst"></select>
		</label>
		
		<label>Region
			<select class="form-control" name="lcl"></select>
		</label>
		
		<label>Rooms
			<select name="qty" class="form-control" >
				{{#forLoop 1 10 1}}
					<option value="{{index}}">{{index}}</option>
				{{/forLoop}}
			</select>
		</label>
		
		<div class="input-daterange">
			<label>Check In
				<input type="text" name="date" class="form-control date" />
			</label>
			<label>Check Out
				<input type="text" name="toDate" class="form-control date" />
			</label>
			<label>Nights
				<select name="scu" class="form-control" >
					{{#forLoop 1 date_config.max_scu 1}}
						<option value="{{index}}">{{index}}</option>
					{{/forLoop}}
				</select>
			</label>
		</div>
		
		<button name="search" class="btn btn-block btn-search">Search</button>
	</div>
</script>