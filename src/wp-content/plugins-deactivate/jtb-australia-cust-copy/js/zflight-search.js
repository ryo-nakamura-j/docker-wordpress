/*
Date.locale = {
    month_names: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
    short_names: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
};

var Explore = Explore || {};

Explore.Options = {
  earliestDeparture: 7,
  defaultReturn: 7,
  minAutocompleteCharacters: 4,

  dayOptions: function(){ var dayopts = "";
                 for(var i = 1; i < 32; i++) {  dayopts += "<option value=\"" + i + "\">" + i + "</option>"; }
                 return dayopts; }(),
  monthOptions: function(){  var monthopts = "";
                             for(var i = 0; i < 12; i++)
                             {  monthopts += "<option value=\"" + (i+1) + "\">" + Date.locale.short_names[i] + "</option>"; }
                             return monthopts; }(),
  yearOptions: function(){  var today = new Date();
                            var yearopts = "";
                            for(var i = today.getFullYear(); i <= new Date(today.getFullYear(), today.getMonth(), today.getDay()+330).getFullYear(); i++)
                            {  yearopts += "<option value=\"" + i + "\">" + i + "</option>"; }
                            return yearopts; }()

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

  //disable/enable
  $("input[name=returnOrOneWay]").change(function(){
     var x = $("#searchAir\\.segments1\\.departDate")
     if($(this).val() == 'R'){
        x.removeClass('disabled');
        $("input, select", x).prop('disabled', false);
     } else {
        x.addClass('disabled');
        $("input, select", x).prop('disabled', true);
     }
  });

  $("#explore-amenities").buttonset();
  //room guests
  $("#searchHotelV2\\.roomCount").change(function(){
     var count = $(this).val();
     var base = $("#searchHotelV2\\.segments0\\.guestCount");
     for(var i = 1; i < 5; i++){
        var elem = $("#searchHotelV2\\.segments" + i + "\\.guestCount");
        var prev = $("#searchHotelV2\\.segments" + (i-1) + "\\.guestCount");
        if(i < count && elem.size() == 0){
           elem = base.clone(); $("label", elem).html("Guests in room " + (i+1) + ":").attr('for','searchHotelV2.segments[' + i + '].guestCount'); elem.attr('id', 'searchHotelV2.segments' + i + '.guestCount');
           $("select",elem).attr('name','searchHotelV2.segments[' + i + '].guestCount');
           prev.after(elem);
        }else if(i >= count){
           elem.detach();
        }
     }
  });

  //setup validation, copy
  $("#explore-flights form, #explore-hotels form, #explore-cars form").submit(function(event){
     $(".explore-error").detach();
     $(".explore-validate-airport",this).each(function(){
         var val = $(this).val();
         if(val == null || !(val.length == 3 || /.*\([A-Z]{3}\)/.test(val) ) ){
            $(this).after("<div class='explore-error'>Please select an airport or enter an airport code</div>");
         }
      });
     $(".explore-validate-date", this).each(function(){
         if($(this).hasClass("disabled")) { return; }
         var selDate = new Date($(".year",this).val(), $(".month", this).val()-1, $(".day",this).val());
         if($(this).attr('dateafter')){
            var that = $("#" + $(this).attr('dateafter').replace(/\./g, "\\."));
            var thatDate = new Date($(".year",that).val(), $(".month", that).val()-1, $(".day",that).val());
            if(selDate < thatDate){
               $(this).after("<div class='explore-error'>Please select a date after " + thatDate.getDate() + "-" + Date.locale.short_names[thatDate.getMonth()] + "-" + thatDate.getFullYear() + "</div>");
            }
         }
         if(selDate < earliestDeparture){
            $(this).after("<div class='explore-error'>Please select a date after " + earliestDeparture.getDate() + "-" + Date.locale.short_names[earliestDeparture.getMonth()] + "-" + earliestDeparture.getFullYear() + "</div>");
         }
     });
     if($("input[name=returnOrOneWay]:checked").val() == 'R'){
        $("#searchAir\\.segments1\\.departCity").val($("#searchAir\\.segments0\\.arrivalCity").val());
        $("#searchAir\\.segments1\\.arrivalCity").val($("#searchAir\\.segments0\\.departCity").val());
        $("#searchAir\\.segments0\\.cabinIndicator").val($("#cabClassSelect").val());
        $("#searchAir\\.segments1\\.cabinIndicator").val($("#cabClassSelect").val());
     } else {
        $("#searchAir\\.segments1\\.departCity").val("");
        $("#searchAir\\.segments1\\.arrivalCity").val("");
        $("#searchAir\\.segments0\\.cabinIndicator").val($("#cabClassSelect").val());
        $("#searchAir\\.segments1\\.cabinIndicator").val("");
     }
     $(".explore-data-copy").each(function(){
        $(this).val($("#" + $(this).attr('idref').replace(/\./g, "\\.")).val());
     });

     //return false;
     return $(".explore-error", this).size() == 0;

   });


  //setup calendars
  $(".explore-date-picker").each(function(){
     var wrapper = this;

     if($(this).hasClass("explore-date-return")){
        $(".day", this).html(Explore.Options.dayOptions).val(defaultReturn.getDate());
        $(".month", this).html(Explore.Options.monthOptions).val(defaultReturn.getMonth()+1);
        $(".year", this).html(Explore.Options.yearOptions).val(defaultReturn.getFullYear());
     }else{
        $(".day", this).html(Explore.Options.dayOptions).val(earliestDeparture.getDate());
        $(".month", this).html(Explore.Options.monthOptions).val(earliestDeparture.getMonth()+1);
        $(".year", this).html(Explore.Options.yearOptions).val(earliestDeparture.getFullYear());
    }

     $(".date-selector", this ).datepicker({
        showOn: "button",
        buttonImage: "https://www.nx.jtbtravel.com.au/wp-content/uploads/svg/ic_date_range_black_24px.svg",
        buttonImageOnly: true,
            onSelect: function(dateText, inst) {
                var date = $(this).datepicker( 'getDate' );
                $(".day", wrapper).val(date.getDate());
                $(".month", wrapper).val(date.getMonth());
                $(".year", wrapper).val(date.getFullYear());
            }
     });
  });
});
*/