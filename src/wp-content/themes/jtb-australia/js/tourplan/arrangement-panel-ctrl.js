
ArrangementPanelCtrl = function() { 
	var data = null;
	var ctrl = {
		Validate: Validate,
		GetArrangementRequestString: GetArrangementRequestString,
		GetArrangementDisplay: GetArrangementDisplay,
		GenerateFromAdditionalDetailXml: GenerateFromAdditionalDetailXml
	}; 
	return ctrl;
	function Validate( rootJqElement ) {
		var validationParamsList = [];
		var itemList = rootJqElement.find(".arrangements-datepicker.required");
		_.forEach( itemList, function(item) {
			validationParamsList.push({
				containerJq: $(item),
				isRequired: true,
				validator: ValidationHelper.ValidateDatePicker
			});
		})
		itemList = rootJqElement.find(".arrangements-textarea.required")
		_.forEach( itemList, function(item) {
			validationParamsList.push({
				containerJq: $(item),
				format: $(item).attr("validation"),
				isRequired: true,
				validator: ValidationHelper.ValidateTextArea
			});
		})
		itemList = rootJqElement.find(".arrangements-textarea:not(.required)")
		_.forEach( itemList, function(item) {
			validationParamsList.push({
				containerJq: $(item),
				format: $(item).attr("validation"),
				isRequired: false,
				validator: ValidationHelper.ValidateTextArea
			});
		})
		itemList = rootJqElement.find(".arrangements-dropdown.required")
		_.forEach( itemList, function(item) {
			validationParamsList.push({
				containerJq: $(item),
				isRequired: true,
				validator: ValidationHelper.ValidateDropdown
			});
		})
		return ValidationHelper.ValidateAll( validationParamsList );
	}
	// Retail engine is expecting value below encoded and supplied to serviceline remarks field:
	// 	<Arrangements>
	//    <Arrangement DateNo="1" ArrangementID="NP3J082">
	//       <OptionInfo SelectionCode="TGP" />
	//    </Arrangement>
	//    <Arrangement DateNo="1" ArrangementID="NV-VEGI">
	//       <OptionInfo SelectionCode="1" />
	//       <OptionInfo SelectionCode="1" />
	//       <OptionInfo SelectionCode="1" />
	//    </Arrangement>
	//    <Arrangement DateNo="1" ArrangementID="NZZZ01_BS">
	//       <OptionInfo InputValue="Some test comment for the service" />
	//    </Arrangement>
	// </Arrangements>
	function _getArrangementObject( rootJqElement ) {
		var rlt = [];
		_.forEach( data.data.arrangements, function(arr) {
			var item = {
				ArrangementId: arr.Details[0].ArrangementID,	// for request
				ArrangementName: arr.Details[0].SelectionCodeMessage,	// for display
				OptionInfo: [
				// {
				// 	SelectionCode: code,	// for request
				// 	Time: selectedOpt.Time,				// for display
				// 	HotelName: selectedOpt.HotelName,	// for display
				// 	InputValue: val 	// for request
				// }
				],
				Display: null	// for display
			}
			var ui = rootJqElement.find("div[name=" + arr.Details[0].ArrangementID + "]");
			if ( $(ui).hasClass("arrangements-checkbox-list") ) {
				var vList = $(ui).find("input");
				var c = 0;
				_.forEach( vList, function(v) {
					++c;
					if ( v.checked ) {
						var code = $(v).attr("code");
						if ( vList.length > 1 ) {
							item.OptionInfo.push( {
								SelectionCode: code,
								RPH: c,
							} );
						}
						else{
							item.OptionInfo.push( {
								SelectionCode: code,
							} );
						}
					}
				});
				if ( item.OptionInfo.length > 0 || arr.Details[0].IsRequiredFlag ) {
					item.Display = arr.Details[0].SelectionCodeMessage + ": " + item.OptionInfo.length
				}
			}
			else if ( $(ui).hasClass("arrangements-dropdown") ) {
				var val = $(ui).find("select").val();
				if ( !_.isEmpty(val) ) {
					var selectedOpt = null;
					_.forEach( arr.Details[0].Options, function(opt) {
						if ( opt.SelectionCode == val ) 
							selectedOpt = opt;
					})
					item.OptionInfo.push( {
						SelectionCode: val,
						Time: selectedOpt.Time,
						HotelName: selectedOpt.HotelName
					} );
				}
				if ( item.OptionInfo.length > 0 ) {
					var o = item.OptionInfo[0];
					item.Display = arr.Details[0].SelectionCodeMessage + ": " 
						+ ( o.HotelName ? o.HotelName : "" ) + " " 
						+ ( o.Time ? o.Time : "" );
				}
			}
			else if ( $(ui).hasClass("arrangements-datepicker") ) {
				var val = $(ui).find("input").val();
				item.OptionInfo.push( {
					InputValue: val
				} );
				if ( !_.isEmpty(val) ) {
					var o = item.OptionInfo[0];
					item.Display = arr.Details[0].SelectionCodeMessage + ": " + o.InputValue;
				}
			}
			else if ( $(ui).hasClass("arrangements-textarea") ) {
				var found = $(ui).find("textarea");
				var count = found.length;
				if ( count <= 1 ) {
					var val = found.val();
					if ( !_.isEmpty(val) ) {
						item.Display = arr.Details[0].SelectionCodeMessage + ": " + val;
					}
					// We will use a default value if the val is empty. This is a jtb requirement.
					if ( _.isEmpty(val)) {
						val = ValidationHelper.DEVAULT_TEXTAREA_EMPTY;
					}
					item.OptionInfo.push( {
						InputValue: val
					} );
				}
				else {
					var c = 0;
					found.each(function(){
						var val = $(this).val();
						if ( !_.isEmpty(val) ) {
							if ( c == 0 )
								item.Display = arr.Details[0].SelectionCodeMessage + ": " + val;
							else
								item.Display += " | " + val;
						}
						// We will use a default value if the val is empty. This is a jtb requirement.
						if ( _.isEmpty(val)) {
							val = ValidationHelper.DEVAULT_TEXTAREA_EMPTY;
						}
						item.OptionInfo.push( {
							InputValue: val,
							RPH: ++c,
						} );
					});
				}
			}
			// Only show and sent item if the item has a value
			if ( item.OptionInfo.length > 0 )
				rlt.push(item);
		});
		return rlt;
	}
	function GetArrangementRequestString( rootJqElement ) {
		var arr = _getArrangementObject( rootJqElement );
		var xmlRlt = "";
		// 	<Arrangements>
		//    <Arrangement DateNo="1" ArrangementID="NP3J082">
		//       <OptionInfo SelectionCode="TGP" />
		//    </Arrangement>
		//    <Arrangement DateNo="1" ArrangementID="NV-VEGI">
		//       <OptionInfo SelectionCode="1" />
		//       <OptionInfo SelectionCode="1" />
		//       <OptionInfo SelectionCode="1" />
		//    </Arrangement>
		//    <Arrangement DateNo="1" ArrangementID="NZZZ01_BS">
		//       <OptionInfo InputValue="Some test comment for the service" />
		//    </Arrangement>
		//    <Arrangement DateNo="1" ArrangementID="NR-J097">
		//       <OptionInfo InputValue="Some test comment for the service" RPH="1"/>
		//       <OptionInfo InputValue="Some test comment for the service" RPH="2"/>
		//    </Arrangement>
		// </Arrangements>
		xmlRlt += "<Arrangements>";
		_.forEach( arr, function( a ) {
			xmlRlt += "<Arrangement DateNo=\"1\" ArrangementID=\"" + a.ArrangementId + "\">"
			_.forEach( a.OptionInfo, function(o){
				xmlRlt += "<OptionInfo ";
				if ( !_.isEmpty(o.SelectionCode) )
					xmlRlt += "SelectionCode=\"" + o.SelectionCode + "\" ";
				if ( !_.isEmpty(o.InputValue) ) 
					// at iCom level special characters actually need to be escaped 3 times.
					// Retail is doing escape by it's own.
					// So if we need to test special characters for arrangements 
					// e.g. '&' for request in retail engine should be '&amp;amp;'
					// e.g. '&' for request in iCom should be '&amp;amp;amp;' 
					xmlRlt += "InputValue=\"" + _.escape(_.escape( o.InputValue )) + "\" "; 
				if ( o.RPH != null )
					xmlRlt += "RPH=\"" + _.escape(_.escape( o.RPH )) + "\" "; 
				xmlRlt += "/>";
			})
			xmlRlt += "</Arrangement>"
		})
		xmlRlt += "</Arrangements>";
		return xmlRlt; // _.escape(xmlRlt);
	}
	function GetArrangementDisplay( rootJqElement ) {
		var arr = _getArrangementObject( rootJqElement );
		var displayList = [];
		_.forEach( arr, function( a ) {
			if ( !_.isEmpty( a.Display ) )
				displayList.push( a.Display );
		});
		var rlt = displayList.join( ", " );
		return rlt;
	}
	function GenerateFromAdditionalDetailXml( sectionConfig, details, paxCount ) {
		var orderString = sectionConfig.arrangements_sequence;
		var arrangements = _.map( details, function(d) {
			var rlt = [];
			// Can't use lodash here, as lodash does not cooperate well with
			// xml elements
			var xml = $.parseXML( d.DetailDescription );
			if ( xml != null && xml.childNodes.length > 0 ) {
				var elm = xml.childNodes[0];	// The first element is expected to be the root node
				var elmJq = $(elm);
				// construct setting module for Arrangements
				var rltElm = { 
					ArrangementTypeName: elm.nodeName,
					IsRequiredFlag: elmJq.attr("IsRequiredFlag") == "true", // true, false
					InputType: elmJq.attr("InputType"), // VALUE, LIST
					SelectionType: elmJq.attr("SelectionType"), // ONE, MULTIPLE
					SelectionCodeMessage: elmJq.attr("SelectionCodeMessage"), // input field label
					PaxInputUnit: elmJq.attr("PaxInputUnit"), // PAX, TOURS
					IsPaxInputUnitPax: elmJq.attr("PaxInputUnit") == "PAX",
					ArrangementPatternName: elmJq.attr("ArrangementPatternName"), // Free Form Remarks, Pick-up Place, Request for vegetarian menu, ...
					ArrangementPatternID: elmJq.attr("ArrangementPatternID"), // X_BSREM, A_J085, V_VEGI, ...
					ArrangementID: elmJq.attr("ArrangementID"), // MV_VEGI, NZZZ01_BS, NP3J085, ...
					InputMessage: elmJq.attr("InputMessage"), // Message show up as a help tip
					IsInputMessageEmpty: _.isEmpty(elmJq.attr("InputMessage")) ? true : _.isEmpty(elmJq.attr("InputMessage").trim()),
					Format: elmJq.attr("Format"), // ^.{1,500}$, ^.{1,200}$, INDATE
					DateNo: elmJq.attr("DateNo"), // Always 1 according to iCom documentation
					Options: []
				};
				if ( sectionConfig.arrangements_field_data_overwrite != null ) {
					var toReplace = _.find( 
						sectionConfig.arrangements_field_data_overwrite, { 
							arrangementid: rltElm.ArrangementID
						});
					if ( toReplace ) {
						if ( toReplace.defaultvalue != "" 
							&& toReplace.defaultvalue != null )
							rltElm.DefaultValue = toReplace.defaultvalue;
						if ( toReplace.isrequiredflag == "true"
							|| toReplace.isrequiredflag == "false" )
							rltElm.IsRequiredFlag = toReplace.isrequiredflag == "true";
					}
				}
				// construct options
				for ( var j = 0; j < $(elmJq[0]).children().length; j++ ) {
					var opt = $(elmJq[0]).children()[j];
					var optJq = $(opt);
					var optJqTime = optJq.attr("Time"); // LIST/ONE, pickup/dropoff time in 0000 format
					var rltOpt = {
						OptionName: opt.nodeName,
						HotelCode: optJq.attr("HotelCode") == null ? optJq.attr("SelectionCode") : optJq.attr("HotelCode"),	// LIST/ONE, could be empty
						HotelName: optJq.attr("HotelName") == null ? optJq.attr("SelectionName") : optJq.attr("HotelName"),	// LIST/ONE, for display
						RouteCode: optJq.attr("RouteCode"), // LIST/ONE, CNP3J085, ...
						SelectionCode: optJq.attr("SelectionCode"), // LIST/ONE/MULTIPLE, code for selection
						SelectionName: optJq.attr("SelectionName"), // LIST/MULTIPLE(checkbox), for display
						Sequence: optJq.attr("Sequence"), // LIST/ONE
						Time: optJqTime != null ? optJqTime[0] + optJqTime[1] + ":" + optJqTime[2] + optJqTime[3] : null, 
						URL: optJq.attr("URL") // LIST/ONE, image for selection
					};
					rltElm.Options.push( rltOpt );
				}
				rlt.push( rltElm );
			}
			return { 
				ArrangementName: d.DetailName,
				Details: rlt
			}
		});
		// Sort by sequence code in the page setting
		var sequenceCode = orderString.split(',');
		_.forEach( arrangements, function( ar ) {
			var seq = 999999;
			for ( var k = 0; k < sequenceCode.length; k++ ) {
				if ( ar.Details.length > 0 && 
					ar.Details[0].ArrangementID.indexOf( sequenceCode[k] ) == 0 ) {
					ar.Sequence = k;
					return;
				}
			} 
		});
		arrangements = _.sortBy( arrangements, function( ar ) { 
			return ar.Sequence; 
		} );
		data = {
			data: {
				visible: arrangements != null && arrangements.length > 0,
				paxCount: paxCount,
				arrangements: arrangements
			}
		};
		return data;
	}
}
ArrangementPanel = ArrangementPanelCtrl();