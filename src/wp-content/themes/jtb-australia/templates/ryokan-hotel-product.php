<script class="masterTemplate" type="text/x-handlebars-template">
	<div class="supplierInfoSection">
		<div class="row">
			<div class="col-xs-12">
				<image src="{{loadingImage}}" class="img-responsive center-block" />
			</div>
		</div>
	</div>
</script>

<script class="supplierInfoTemplate" type="text/x-handlebars-template">
	<div class="row">
		<div class="col-xs-12">
			<h1>{{name}}</h1>
			<div class="ribbon-red-desktop"></div>
		</div>
		<div class="col-xs-12 col-sm-6 col-md-8">
			<p>{{contact.address1}} {{contact.address2}}</p>
			<p>{{getNotes notes "TST" "text"}}</p>
			<p>{{getNotes notes "SDS" "text"}}</p>
		</div>
		<div class="col-xs-12 col-sm-6 col-md-4">
			<div id="carousel-{{code}}" class="carousel slide" data-ride="carousel">
				<div style="position:relative">
					<div class="carousel-inner" role="listbox">
						{{#forLoop 0 10 1}}
							<div class="item{{#if first}} active{{/if}}">
								<img 
								src="{{imagesBaseURL}}/Supplier_{{../code}}/{{../code}}.{{indexPlusOne}}.jpg" 
								alt="slide-{{index}}" 
								class="fullwidth"
								onerror="this.onerror = null; {{#if first}}this.src = '{{defaultImageURL}}';{{else}}$(this).parent().remove();{{/if}}"
								>
							</div>

						{{/forLoop}}
					</div>


					<a class="left carousel-control" href="#carousel-{{code}}" role="button" data-slide="prev">
						<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
						<span class="sr-only">Previous</span>
					</a>
					<a class="right carousel-control" href="#carousel-{{code}}" role="button" data-slide="next">
						<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
						<span class="sr-only">Next</span>
					</a>
				</div>

				<ol class="carousel-indicators">

					{{#forLoop 0 10 1}}
						<li 
						data-target="#carousel-{{../code}}" 
						data-slide-to="{{index}}"{{#if first}}class="active"{{/if}}>
							<img 
								src="{{imagesBaseURL}}/Supplier_{{../code}}/{{../code}}.{{indexPlusOne}}.jpg" 
								alt="slide-{{index}}"
								onerror="this.onerror = null; {{#if first}}this.src = '{{defaultImageURL}}';{{else}}$(this).parent().remove();{{/if}}"
							>
						</li>
					{{/forLoop}}

				</ol>

			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="ribbon-red-desktop"></div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<h3><i class="fa fa-bed"></i> Room Selection</h3>
			<div class="refreshSearchSection clearfix"></div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="row hidden-xs hidden-sm">
				<div class="productInfoHeadings">
					<div class="col-md-6">
						<div class="heading"><label>Room Type</label></div>
					</div>
					<div class="col-md-2">
						<div class="heading"><label>Room Status</label></div>
					</div>
					<div class="col-md-2">
						<div class="heading"><label>Price (total)</label></div>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
			<div class="productInfoSection"></div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="ribbon-red-desktop"></div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 col-md-8">
			<h3>Hotel Description</h3>
			<p>{{getNotes notes "LDS" "text"}}
		</div>
		<div class="col-xs-12 col-md-4">
			<h3>Amenities</h3>
			<ul>
			{{#each amenities}}
				{{#tp_lookup 'AMN' this}}
					<li style="font-style:italic;">{{name}}</li>
				{{/tp_lookup}}
			{{/each}}
			</ul>
		</div>
	</div>
</script>

<script class="productInfoTemplate" type="text/x-handlebars-template">
	<div class="productInfo clearfix {{cleanCSSString availability.RateId}}">
		<div class="row">
			<div class="col-xs-12 col-md-4">
				<div class="productName">
					<span>{{product.name}}</span>
				</div>
			</div>
			<div class="col-xs-6 col-md-2 detail_section">
				<div class="heading hidden-md hidden-lg"><label>Room Type</label></div>
				<div class="detail">{{roomTypeString availability.Qty}}</div>
			</div>
			<div class="col-xs-6 col-md-2 detail_section">
				<div class="heading hidden-md hidden-lg"><label>Room Status</label></div>
				<div class="detail">{{availabilityString availability.Availability}}</div>
			</div>
			<div class="col-xs-12 col-md-2 detail_section">
				<div class="heading hidden-md hidden-lg"><label>Price (total)</label></div>
				<div class="detail">${{displayPrice availability.TotalPrice 2}}</div>
			</div>
			<div class="col-xs-12 col-md-2 detail_section">
				<button class="btn btn-block btn-success" name="book">Book</button>
			</div>
		</div>
	</div>
</script>

<script class="refreshSearchTemplate" type="text/x-handlebars-template">
	<h3>{{section_heading}}</h3>
	<div class="row">
		<div class="refreshSearchSectionInner clearfix">
			<div class="col-xs-12">
				<div class="row">
					<div class="input-daterange">
						<div class="col-xs-12 col-sm-6 col-md-3">
							<label>{{getValueByKey this.modify_search_config 'date_in_label'}}
								<input type="text" name="date" class="form-control date">
							</label>
						</div>
						<div class="col-xs-12 col-sm-6 col-md-3">
							<label>{{getValueByKey this.modify_search_config 'date_out_label'}}
								<input type="text" name="toDate" class="form-control date">
							</label>
						</div>
						<div class="col-xs-6 col-md-2">
							<label>{{getValueByKey this.modify_search_config 'scu_label'}}
								<select name="scu" class="form-control">
									{{#forLoop 1 30 1}}
										<option value="{{index}}">{{index}}</option>
									{{/forLoop}}
								</select>
							</label>
						</div>
					</div>
					<div class="col-xs-6 col-md-2">
						<label>{{getValueByKey this.modify_search_config 'qty_label'}}
							<select name="qty" class="form-control">
								{{#forLoop 1 30 1}}
									<option value="{{index}}">{{index}}</option>
								{{/forLoop}}
							</select>
						</label>
					</div>
					<div class="col-xs-12 col-md-2">
						<label></label>
						<button class="btn btn-primary btn-block refresh-search">Check</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</script>