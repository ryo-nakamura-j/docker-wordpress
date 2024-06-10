<?php

// array (
//   'enabled' => true,
//   'trip_type_settings' => 
//   array (
//     'label' => 'Trip Type*',
//     'one-way_label' => 'One-way',
//     'roundtrip_label' => 'Roundtrip',
//     'default_trip_type' => '',
//   ),
//   'testjson' => '[
//     {
//         "label":"Example Laabel",
//         "value":"Example Value"
//     }
// ]',
//   'adult_settings' => 
//   array (
//     'label' => 'Adult (above 12 years):',
//     'minimum' => 0,
//     'maximum' => 6,
//     'default' => 1,
//   ),
//   'child_settings' => 
//   array (
//     'label' => 'Child (2-11 years):',
//     'minimum' => 0,
//     'maximum' => 5,
//     'default' => 0,
//   ),
//   'infant_settings' => 
//   array (
//     'label' => 'Infant (under 2 years):',
//     'minimum' => 0,
//     'maximum' => 2,
//     'default' => 0,
//   ),
//   'service_class_settings' => 
//   array (
//     'label' => 'Classes of Service:',
//     'service_classes' => '[
// {"value":"F", "label":"First"},
// {"value":"C", "label":"Business"},
// {"value":"S", "label":"Premium Economy"},
// {"value":"Y", "label":"Economy"}
// ]',
//   ),
//   'flight_type_settings' => 
//   array (
//     'label' => 'Flight Type:',
//     'flight_types' => '[
// {"value":1", "label":"Direct"},
// {"value":2", "label":"Direct & Connecting"}
// ]',
//   ),
// )

class SabreSearchModule {

	public $configs;

	public function __construct($configs) {
		$this->configs = $configs;
		// $this->dumpConfigs();
	}

	function dumpConfigs() {
		echo '<pre>';
		var_export($this->configs);
		echo '</pre>';
	}

	function renderControl() {
		?>

		<div class="home-content three grid container">
			<script type="text/javascript">
				$(document).ready(function() {
					$(".trigger-roundtrip").click(function () {
						if($(this).prop("checked")) {
							$('.return').show();
							$('input[name=departureDate2]').removeAttr('disabled');
						}
					});
					$(".trigger-oneway").click(function () {
						if($(this).prop("checked")) {
							$('.return').hide();
							$('input[name=departureDate2]').attr('disabled','disabled');
						}
					});
					$('#datetimepicker1').datetimepicker({
						format: 'yyyy-mm-dd hh:00:00',
						autoclose:true,
						minView:1
					}).on("changeDate", function(ev) {
						let selectedDate = moment(ev.date);
						let toPicker = $("#datetimepicker2");

						let currentToDate = moment(toPicker.datetimepicker("getDate"));

						toPicker.datetimepicker('setStartDate', selectedDate.format("YYYY-MM-DD"));

						if (currentToDate.isBefore(selectedDate)) {
							toPicker.datetimepicker('update', selectedDate.toDate());
						}
					});
					$('#datetimepicker2').datetimepicker({
						format: 'yyyy-mm-dd hh:00:00',
						autoclose:true,
						minView:1
					});
			        $('#from1').change(function() {
			            $('#to2').val($(this).val());
			        });
			        $('#datetimepicker1').mouseover(function() {
			            $('#from2').val($('#to1').val());
			        });
					var Explore = Explore || {};
					Explore.Options = {
						earliestDeparture: <?php echo $this->configs['date_settings']['minimum_departure_offset'] ?>,
						defaultReturn: <?php echo $this->configs['date_settings']['default_return_offset'] ?>,
						minAutocompleteCharacters: 3,
					}
					$(function(){

						var today = new Date();
						var earliestDeparture = new Date(today.getFullYear(), today.getMonth(), today.getDate()+Explore.Options.earliestDeparture);
						var defaultReturn = new Date(earliestDeparture.getFullYear(), earliestDeparture.getMonth(), earliestDeparture.getDate()+Explore.Options.defaultReturn);

					  //setup autocomplete
					  $(".airport-autocomplete").each(function(index){
					  	var ida = $(this).hasClass('init-dep-apt');
					  	$(this).autocomplete({
					  		source: function( request, response ) {
					  			$.ajax({
					  				url: "https://jtb.sabreexplore.com.au:443/citySearchJson.aj?sn=jtb&ida=" + ida,
					  				dataType: "jsonp",
					  				data: {
					  					term: request.term
					  				},
					  				success: function( data ) {
					  					response( $.map( data.query.results.result, function( item ) {
					  						return {
					  							label: item.display,
					  							value: item.select
					  						}
					  					}));
					  				}
					  			});
					  		},
					  		minLength: Explore.Options.minAutocompleteCharacters,
					  		open: function() {
					  			$( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
					  		},
					  		close: function() {
					  			$( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
					  		}
					  	});
					  });

					});

		        });
		</script>
		<h3 class="red-heading"><?php echo $this->configs['title']; ?></h3>
		<div style="position: relative;display: block;margin-top: 10px;margin-bottom: 14px;background: none repeat scroll 0% 0% #E00011;height: 4px;"></div>
			<div class="search-flights">
				<form action="https://webstart.sabretnapac.com/japan-travel-bureau/flight-search-process.aspx" method="post" class="form-searchflights" target="_blank">
					<div class="col-sm-6 col-xs-12 text-right pull-right">
						<strong class="red">*</strong> <i>Indicate required fields</i>
					</div>
					<div class="col-sm-6 col-xs-12 pull-left">
						<?php echo $this->configs['trip_type_settings']['label']; ?>:
						<div class="div-radio">
							<label class="radio-inline"><input class="trigger-oneway" type="radio" name="tripType" value="1" required><?php echo $this->configs['trip_type_settings']['one-way_label']; ?></label>
							<label class="radio-inline"><input class="trigger-roundtrip" type="radio" name="tripType" value="2" data-rel="return" checked><?php echo $this->configs['trip_type_settings']['roundtrip_label']; ?></label>
						</div>
					</div>
					<div class="clearfix"></div>

					<?php $fromSettings = $this->configs['from_settings']; ?>
					<div class="col-sm-6 col-xs-12">
						<?php echo $fromSettings['label']; 
						if ($fromSettings['autocomplete']) { ?>
							<input id="from1" name="from1" type="text" class="airport-autocomplete explore-validate_airport" required" style="margin: 3px 0;"/>
						<?php 
						} else { ?>
						<select name="from1" id="from1" required>
							<?php echo $this->generateOptions(json_decode($fromSettings['airports'], true)); ?>
						</select>
						<?php 
						}
						?>
					</div>

					<?php $toSettings = $this->configs['to_settings']; ?>
					<div class="col-sm-6 col-xs-12">
						<?php echo $toSettings['label']; 
						if ($toSettings['autocomplete']) { ?>
							<input id="to1" name="to1" type="text" class="airport-autocomplete explore-validate_airport" required" style="margin: 3px 0;"/>
						<?php 
						} else { ?>
						<select name="to1" id="to1" required>
							<?php echo $this->generateOptions(json_decode($toSettings['airports'], true)); ?>
						</select>
						<?php 
						}
						?>
					</div>
					<div class="clearfix"></div>

					<input type="hidden" name="from2" id="from2"/>
					<input type="hidden" name="to2" value="" id="to2"/>

					<div class="col-sm-6 col-xs-12">
						Departure Date<span class="red">*</span>:<br/>
						<div class='input-group date explore-validate-date' id='datetimepicker1' style="margin:3px 0">
							<input type='text' name="departureDate1" id="departuredate" required/>
							<span class="input-group-addon">
								<span class="glyphicon glyphicon-calendar"></span>
							</span>
						</div>
					</div>
					<div class="col-sm-6 col-xs-12 return">
						Return Date<span class="red">*</span>: &nbsp;&nbsp; <i>(Return date must be after Departure date)</i><br/>
						<div class="input-group date explore-validate-date" id="datetimepicker2" style="margin:3px 0">
							<input type='text' name="departureDate2" id="returndate" required/>
							<span class="input-group-addon">
								<span class="glyphicon glyphicon-calendar"></span>
							</span>
						</div>
					</div>
					<div class="clearfix"></div>

					<?php
						$adultSettings = $this->configs['adult_settings'];
						$opts = array_map(function($value) {
							return array(
								"label" => $value,
								"value" => $value
							);
						}, range($adultSettings['minimum'], $adultSettings['maximum']));
					?>
					<div class="col-sm-4 col-xs-12">
						<?php echo $adultSettings['label']; ?>
						<select name="adult">
							<?php echo $this->generateOptions($opts, $adultSettings['default']); ?>
						</select>
					</div>


					<?php
						$childSettings = $this->configs['child_settings'];
						$opts = array_map(function($value) {
							return array(
								"label" => $value,
								"value" => $value
							);
						}, range($childSettings['minimum'], $childSettings['maximum']));
					?>
					<div class="col-sm-4 col-xs-12">
						<?php echo $childSettings['label']; ?>
						<select name="child">
							<?php echo $this->generateOptions($opts, $childSettings['default']); ?>
						</select>
					</div>

					<?php
						$infantSettings = $this->configs['infant_settings'];
						$opts = array_map(function($value) {
							return array(
								"label" => $value,
								"value" => $value
							);
						}, range($infantSettings['minimum'], $infantSettings['maximum']));
					?>
					<div class="col-sm-4 col-xs-12">
						<?php echo $infantSettings['label']; ?>
						<select name="infant">
							<?php echo $this->generateOptions($opts, $infantSettings['default']); ?>
						</select>
					</div>

					<?php $serviceClassSettings = $this->configs['service_class_settings']; ?>
					<div class="col-sm-6 col-xs-12">
						<?php echo $serviceClassSettings['label']; ?>
						<select name="class">
							<?php echo $this->generateOptions(json_decode($serviceClassSettings['service_classes'], true)); ?>
						</select>
					</div>

					<?php $flightTypeSettings = $this->configs['flight_type_settings']; ?>
					<div class="col-sm-6 col-xs-12">
						<?php echo $flightTypeSettings['label']; ?>
						<select name="flightType">
							<?php echo $this->generateOptions(json_decode($flightTypeSettings['flight_types'], true)); ?>
						</select>
					</div>
					<div class="clearfix"></div>
					<br/>
					<div class="col-sm-2 col-xs-7">
						<button type="submit" onclick="return checkdate()" class="btn btn-default" target="_blank"/>Search Flights</button>
					</div>
					<div class="col-sm-10 col-xs-5">
						<div style="padding:6px 12px 6px 0px">
							<a href="https://webstart.sabretnapac.com/japan-travel-bureau/flight-search.aspx" class="advanced-search-link" target="_blank">Advanced Search</a>
						</div>
					</div>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>

	<?php
	}

	function generateOptions($options, $default = null) {
		$optionsString = "";
		foreach ($options as $option) {
			$isDefault = $default != null && $option['value'] == $default;
			$optionsString .= "<option value=\"" . $option['value'] . "\"" . ($isDefault ? "selected" : "") . ">" . $option['label'] . "</option>";
		}
		return $optionsString;
	}

}