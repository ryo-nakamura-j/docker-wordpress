<script>
	$(window).load(function() {

		var sectionConfig = <?php echo json_encode($this->sectionConfig); ?>;
		var jsSectionId = "#tp-railpass-section-<?php echo $this->sectionId; ?>";

		vueData = new Vue( {
			el: jsSectionId,
			data: {
				sectionConfig: sectionConfig,
			},
			computed: {
				isSupplierGroup: function() {
					return this.sectionConfig.supplier_group.length > 1;
				},
			},
			mounted: function() {
				$( jsSectionId ).removeAttr('hidden');
				if ( typeof onVueMounted === "function" ) {
					onVueMounted();
				}
			},
			methods: {
				productClass: function( supplier ) {
					return this.isSupplierGroup ? 'col-xs-12 col-sm-4' : 'col-xs-12 ' + ( supplier.numProducts == 1 ? 'col-md-offset-3 col-md-6' : 'col-md-' + ( 12/supplier.numProducts ) );
				},
				supplierClass: function( supplier ) {
					return this.isSupplierGroup ? 'col-sm-12 col-md-6 md-top-margin rail-group' : ( 'col-xs-12 ' + ( supplier.numProducts <= 2 ? 'col-md-8' : 'col-md-12') );
				},
				supplierWrapperClass: function( supplier ) {
					return this.isSupplierGroup ? '' : 'col-xs-12 col-sm-7 col-md-8';
					
				},
			},
		});
	});

</script>

<div class="row section-<?php echo $this->sectionId; ?> rail-product" id="tp-railpass-section-<?php echo $this->sectionId; ?>" hidden>

	<div class="col-xs-12"><br /></div>
	<div v-for="supplier in sectionConfig.supplier_group" :class="supplierWrapperClass(supplier)">
		<div :class="supplierClass(supplier)" >
			<h2 class="rail_pass_heading" :style="'background:' + supplier.group_colour"><i :class="supplier.group_title_icon"></i> {{supplier.group_title}}</h2>
			<div class="row">
				<div class="rail_pass_inner clearfix" :style="'background:' + supplier.group_colour">
					<div class="tourplan_plugin_section">
						<div  v-for="p in supplier.productConfs" :class="productClass(supplier)">
							<div class="row">
								<div :class="'col-xs-12 rail-product rail-product-' + p.product_id">
									<h3 class="rail_pass_title">{{p.product_title}}</h3>
									<div class="plugin_control"></div>
									<non-accom-product-panel :original-product="p" :search-configs="supplier.searchConf"/>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div v-if="supplier.numProducts <= 2" class="col-xs-12 col-md-4">
			<img v-if="sectionConfig.side_image" :src="sectionConfig.side_image" class="img-responsive fullwidth" />
		</div>
		<div v-if="!isSupplierGroup" class="col-xs-12">
			<div class="row">
				<div class="col-xs-12">
					<div class="description" v-html="sectionConfig.description">
					</div>
				</div>
			</div>
		</div>
	</div>
	<div v-if="!isSupplierGroup" class="col-xs-12 col-sm-5 col-md-4">
		<div class="row">
			<div class="col-xs-12">
				<!-- Upper Map -->
				<div class="upper-map">
					<img v-if="sectionConfig.upper_map" :src="sectionConfig.upper_map" class="img-responsive fullwidth" />
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<!-- Lower Map -->
				<div class="lower-map">
					<img v-if="sectionConfig.lower_map" :src="sectionConfig.lower_map" class="img-responsive fullwidth" />
				</div>
			</div>
		</div>
	</div>
	<div v-if="isSupplierGroup" class="col-xs-12">
		<div class="col-xs-12"><br /></div>
		<div class="row">
			<div class="col-xs-12 col-md-6" v-if="sectionConfig.description">
				<div class="description" v-html="sectionConfig.description">
				</div>
			</div>
			<div class="col-xs-12 col-md-6">
				<div class="map_image">
					<img v-if="sectionConfig.map_image" :src="sectionConfig.map_image" class="map-image img-responsive center-block fullwidth" />
				</div>
			</div>
		</div>
	</div>
	<div class="col-xs-12" v-if="sectionConfig.terms_and_conditions.length > 0">
		<!-- Terms and Conditions -->
		<div class="terms_and_conditions_section">
			<a class="toggle_section_toggle collapsed" data-toggle="collapse" href="#terms_and_conditions_<?php echo $this->sectionId; ?>">
			<h4>
				{{ dataListLabel( "terms_and_conditions", "Terms & Conditions" ) }}
			</h4>
			</a>
			<div id="terms_and_conditions_<?php echo $this->sectionId; ?>" class=" terms_and_conditions collapse">
				<div v-for="tc in sectionConfig.terms_and_conditions" >
					<h4 :style="'background:' + sectionConfig.supplier_group[0].group_colour" v-html="tc.header"></h4>
					<span v-html="tc.content"></span>
				</div>
			</div>
		</div>
	</div>
</div>