<script class="masterTemplate" type="text/x-handlebars-template">
	<div class="productSearchSection"></div>
</script>

<script class="productSearchTemplate" type="text/x-handlebars-template">
	<div class="productSearchControl">
		<h3>Day Tours Search</h3>
		<div class="searchControls">
	
			<input type="hidden" name="srb" />
			<input type="hidden" name="cty" />

			<label>{{serviceButtonConfig (getValueByKey search_config "srb") "destinationsLabel"}}
				<select class="form-control" name="dst"></select>
			</label>
			<label>{{serviceButtonConfig (getValueByKey search_config "srb") "qtyLabel"}}
				<select name="qty" class="form-control" >
					{{#forLoop 1 10 1}}
						<option value="{{index}}A">{{index}}</option>
					{{/forLoop}}
				</select>
			</label>
			<div class="input-daterange">
				<label>{{serviceButtonConfig (getValueByKey search_config "srb") "dateInLabel"}}
					<input type="text" name="date" class="form-control date" />
				</label>
				<!-- <label>{{date_control.scu_label}}
					<select name="scu" class="form-control" >
						{{#forLoop 1 30 1}}
							<option value="{{index}}" {{#if first}}selected="selected"{{/if}}>{{index}}</option>
						{{/forLoop}}
					</select>
				</label> -->
			</div>
			{{#each amenity_filters}}
				<label>{{control_label}}
					<select class="amenity-filter form-control" name="{{amenity_category}}" multiple="multiple"></select>
				</label>
			{{/each}}
				
			<button name="search" class="btn btn-block btn-search">{{serviceButtonConfig (getValueByKey search_config "srb") "searchButtonLabel"}}</button>
		</div>
	</div>
</script>