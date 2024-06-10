 

<div id="FlightSearchHomepagex" class="widgets_on_page hiddenx">
<ul class="widgetonpage">
<li id="text-12" class="widget widget_text">
<div class="textwidget">


<script type="text/javascript">

Date.locale = {
  month_names: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
  short_names: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
};

var Explore = Explore || {};

Explore.Options = {
  earliestDeparture: 4,
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
                            for(var i = today.getFullYear(); i <= new Date(today.getFullYear(), today.getMonth(), today.getDay()+362).getFullYear(); i++)
                            {  yearopts += "<option value=\"" + i + "\">" + i + "</option>"; }
                            return yearopts; }()

}
</script>

<script type="text/javascript">

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
    buttonImage: "calendar.gif",
    buttonImageOnly: true,
            onSelect: function(dateText, inst) {
                var date = $(this).datepicker( 'getDate' );
                $(".day", wrapper).val(date.getDate());
                $(".month", wrapper).val(date.getMonth()+1);
                $(".year", wrapper).val(date.getFullYear());
            }
     });
  });
});
</script>

<html>
  <body>
    <div class="explore-tabs">
      
      <div>
        
          <div id="explore-flights">
            <form action="https://jtb.sabreexplore.com.au:443/searchAir.do" method="post" name="flights">
              <div>
                <span>
                  <input name="returnOrOneWay" value="R" checked="checked" type="radio"> Return
                  <input name="returnOrOneWay" value="O" type="radio"> One Way
                </span>
              </div>
              <div>
                <span>
                  <a href="https://jtb.sabreexplore.com.au:443/searchAir.do">Advanced search</a><br />
                </span>
              </div>
              <div>
                <span>
                  From:
                </span>
              </div>
              <div>
                <span>
                  
                    <select id="searchAir.segments0.departCity" name="searchAir.segments[0].departCity" title="Choose the departure city" class="explore-validate-airport">
                      <option value="">Select a location</option>
                      
                        <option value="ADL">Adelaide (ADL), Australia - Adelaide</option>
                      
                        <option value="ALH">Albany (ALH), Australia - Albany</option>
                      
                        <option value="ABX">Albury (ABX), Australia - Albury</option>
                      
                        <option value="ASP">Alice Springs (ASP), Australia - Alice Springs</option>
                      
                        <option value="ARM">Armidale (ARM), Australia - Armidale</option>
                      
                        <option value="AVV">Avalon (AVV), Australia - Avalon/Geelong</option>
                      
                        <option value="AYQ">Ayers Rock (AYQ), Australia - Connellan</option>
                      
                        <option value="BNK">Ballina (BNK), Australia - Byron Gateway</option>
                      
                        <option value="BCI">Barcaldine (BCI), Australia - Barcaldine</option>
                      
                        <option value="BHS">Bathurst (BHS), Australia - Raglan</option>
                      
                        <option value="ZBL">Biloela (ZBL), Australia - Biloela</option>
                      
                        <option value="BVI">Birdsville (BVI), Australia - Birdsville</option>
                      
                        <option value="BKQ">Blackall (BKQ), Australia - Blackall</option>
                      
                        <option value="BNE">Brisbane (BNE), Australia - Brisbane</option>
                      
                        <option value="BHQ">Broken Hill (BHQ), Australia - Broken Hill</option>
                      
                        <option value="BME">Broome (BME), Australia - Broome</option>
                      
                        <option value="BDB">Bundaberg (BDB), Australia - Bundaberg</option>
                      
                        <option value="BWT">Burnie (BWT), Australia - Wynyard</option>
                      
                        <option value="CNS">Cairns (CNS), Australia - Cairns</option>
                      
                        <option value="CBR">Canberra (CBR), Australia - Canberra</option>
                      
                        <option value="CVQ">Carnarvon (CVQ), Australia - Carnarvon</option>
                      
                        <option value="CED">Ceduna (CED), Australia - Ceduna</option>
                      
                        <option value="CTL">Charleville (CTL), Australia - Charleville</option>
                      
                        <option value="CNJ">Cloncurry (CNJ), Australia - Cloncurry</option>
                      
                        <option value="CAZ">Cobar (CAZ), Australia - Cobar</option>
                      
                        <option value="CFS">Coffs Harbour (CFS), Australia - Coffs Harbour</option>
                      
                        <option value="CPD">Coober Pedy (CPD), Australia - Coober Pedy</option>
                      
                        <option value="DRW">Darwin (DRW), Australia - Darwin</option>
                      
                        <option value="DPO">Devonport (DPO), Australia - Devonport</option>
                      
                        <option value="DBO">Dubbo (DBO), Australia - Dubbo</option>
                      
                        <option value="ELC">Elcho Island (ELC), Australia - Elcho Island</option>
                      
                        <option value="EMD">Emerald (EMD), Australia - Emerald</option>
                      
                        <option value="EPR">Esperance (EPR), Australia - Esperance</option>
                      
                        <option value="GET">Geraldton (GET), Australia - Geraldton</option>
                      
                        <option value="GLT">Gladstone (GLT), Australia - Gladstone</option>
                      
                        <option value="OOL">Gold Coast (OOL), Australia - Coolangatta</option>
                      
                        <option value="GFN">Grafton (GFN), Australia - Clarence Valley</option>
                      
                        <option value="GFF">Griffith (GFF), Australia - Griffith</option>
                      
                        <option value="GTE">Groote Eylandt (GTE), Australia - Alyangula</option>
                      
                        <option value="HTI">Hamilton Island (HTI), Australia - Hamilton Island/Great Barrier Reef</option>
                      
                        <option value="HIS">Hayman Island (HIS), Australia - Hayman Island Heliport</option>
                      
                        <option value="HVB">Hervey Bay (HVB), Australia - Hervey Bay</option>
                      
                        <option value="HBA">Hobart (HBA), Australia - Hobart</option>
                      
                        <option value="HID">Horn Island (HID), Australia - Horn Island</option>
                      
                        <option value="HGD">Hughenden (HGD), Australia - Hughenden</option>
                      
                        <option value="JCK">Julia Creek (JCK), Australia - Julia Creek</option>
                      
                        <option value="KGI">Kalgoorlie-Boulder (KGI), Australia - Kalgoorlie</option>
                      
                        <option value="KTA">Karratha (KTA), Australia - Karratha</option>
                      
                        <option value="KNS">King Island (KNS), Australia - King Island</option>
                      
                        <option value="KGC">Kingscote (KGC), Australia - Kingscote</option>
                      
                        <option value="KNX">Kununurra (KNX), Australia - Kununurra</option>
                      
                        <option value="LST">Launceston (LST), Australia - Launceston</option>
                      
                        <option value="LEA">Learmonth (LEA), Australia - Learmonth</option>
                      
                        <option value="LSY">Lismore (LSY), Australia - Lismore</option>
                      
                        <option value="LRE">Longreach (LRE), Australia - Longreach</option>
                      
                        <option value="LDH">Lord Howe Island (LDH), Australia - Lord Howe Island</option>
                      
                        <option value="MKY">Mackay (MKY), Australia - Mackay</option>
                      
                        <option value="MNG">Maningrida (MNG), Australia - Maningrida</option>
                      
                        <option value="MCV">Mcarthur River Mine (MCV), Australia - Mcarthur River</option>
                      
                        <option value="MEL">Melbourne (MEL), Australia - Melbourne Airport</option>
                      
                        <option value="MIM">Merimbula (MIM), Australia - Merimbula</option>
                      
                        <option value="MQL">Mildura (MQL), Australia - Mildura</option>
                      
                        <option value="MGT">Milingimbi Island (MGT), Australia - Milingimbi</option>
                      
                        <option value="MRZ">Moree (MRZ), Australia - Moree</option>
                      
                        <option value="MYA">Moruya (MYA), Australia - Moruya</option>
                      
                        <option value="MGB">Mount Gambier (MGB), Australia - Mount Gambier</option>
                      
                        <option value="ISA">Mount Isa (ISA), Australia - Mount Isa</option>
                      
                        <option value="DGE">Mudgee (DGE), Australia - Mudgee</option>
                      
                        <option value="NAA">Narrabri (NAA), Australia - Narrabri</option>
                      
                        <option value="BEO">Newcastle (BEO), Australia - Port Hunter Harbour Heliport</option>
                      
                        <option value="NTL">Newcastle (NTL), Australia - Williamtown</option>
                      
                        <option value="ZNE">Newman (ZNE), Australia - Newman</option>
                      
                        <option value="GOV">Nhulunbuy (GOV), Australia - Gove</option>
                      
                        <option value="OLP">Olympic Dam (OLP), Australia - Olympic Dam</option>
                      
                        <option value="OAG">Orange (OAG), Australia - Springhill</option>
                      
                        <option value="PBO">Paraburdoo (PBO), Australia - Paraburdoo</option>
                      
                        <option value="PKE">Parkes (PKE), Australia - Parkes</option>
                      
                        <option value="PER">Perth (PER), Australia - Perth</option>
                      
                        <option value="PHE">Port Hedland (PHE), Australia - Port Hedland</option>
                      
                        <option value="PLO">Port Lincoln (PLO), Australia - Port Lincoln</option>
                      
                        <option value="PQQ">Port Macquarie (PQQ), Australia - Port Macquarie</option>
                      
                        <option value="PPP">Proserpine (PPP), Australia - Whitsunday Coast</option>
                      
                        <option value="RVT">Ravensthorpe (RVT), Australia - Ravensthorpe</option>
                      
                        <option value="RCM">Richmond (RCM), Australia - Richmond</option>
                      
                        <option value="ROK">Rockhampton (ROK), Australia - Rockhampton</option>
                      
                        <option value="RMA">Roma (RMA), Australia - Roma</option>
                      
                        <option value="MCY">Sunshine Coast (MCY), Australia - Maroochydore</option>
                      
                        <option value="SYD">Sydney (SYD), Australia - Kingsford Smith</option>
                      
                        <option value="TMW">Tamworth (TMW), Australia - Tamworth Regional Airport</option>
                      
                        <option value="TRO">Taree (TRO), Australia - Taree</option>
                      
                        <option value="WTB">Toowoomba (WTB), Australia - Brisbane West Wellcamp</option>
                      
                        <option value="TSV">Townsville (TSV), Australia - Townsville International</option>
                      
                        <option value="WGA">Wagga Wagga (WGA), Australia - Forrest Hill</option>
                      
                        <option value="WEI">Weipa (WEI), Australia - Weipa</option>
                      
                        <option value="WYA">Whyalla (WYA), Australia - Whyalla</option>
                      
                        <option value="WIN">Winton (WIN), Australia - Winton</option>
                      
                    </select>
                  
                  
                </span>
              </div>
              
              <div>
                <span>
                  Depart:
                </span>
              </div>
              <div>
                <div class="explore-date-picker explore-validate-date" id="searchAir.segments0.departDate">
                   <span>
                  <select class="day" id="searchAir.segments0.departDate.day" name="searchAir.segments[0].departDate.day"></select>
                  <select class="month" id="searchAir.segments0.departDate.month" name="searchAir.segments[0].departDate.month"></select>
                  <select class="year" id="searchAir.segments0.departDate.year" name="searchAir.segments[0].departDate.year"></select>
                  <input class="date-selector" type="hidden" />
                   </span>
                </div>
              </div>

              <div>
                <span>
                  <input name="searchAir.segments[0].departTime" value="00:00" type="hidden" />
                </span>
              </div>
              <div >
                <span>
                  To:
                </span>
              </div>
              <div>
                <div id="searchAir.segments0.arrivalCity.container"></div>
              </div>
              <div>
                <span>
                  <input id="searchAir.segments0.arrivalCity" name="searchAir.segments[0].arrivalCity" type="text" class="airport-autocomplete explore-validate-airport">
                </span>
              </div>
              <div>
                <span>
                  **Type the first 4 letters of the city

                </span>
              </div>
              <div>
                <span>
                  Return:
                </span>
              </div>
              <div>
                <div class="explore-date-picker explore-date-return explore-validate-date" dateafter="searchAir.segments0.departDate" id="searchAir.segments1.departDate">
                  <span>

                  <select class="day" id="searchAir.segments1.departDate.day" name="searchAir.segments[1].departDate.day"></select>
                  <select class="month" id="searchAir.segments1.departDate.month" name="searchAir.segments[1].departDate.month"></select>
                  <select class="year" id="searchAir.segments1.departDate.year" name="searchAir.segments[1].departDate.year"></select>
                          <input class="date-selector" type="hidden" />
                   </span>
                        </div>
              </div>
              <div>
                <span>
                  <input name="searchAir.segments[1].departTime" value="00:00" type="hidden">
                </span>
              </div>
              <table>
                <tr>
                  <td>
                    Adult
                  </td>
                  <td >
                    Child
                  </td>
                  <td>
                    Infant
                  </td>
                </tr>
                <tr>
                  <td>
                    <select name="adultCount">
                      <option value="1">1</option>
                      <option value="2">2</option>
                      <option value="3">3</option>
                      <option value="4">4</option>
                      <option value="5">5</option>
                    </select>
                  </td>
                  <td>
                    <select name="childCount">
                      <option value="0"></option>
                      <option value="1">1</option>
                      <option value="2">2</option>
                      <option value="3">3</option>
                      <option value="4">4</option>
                      <option value="5">5</option>
                    </select>
                  </td>
                  <td>
                    <select name="infantCount">
                      <option value="0"></option>
                      <option value="1">1</option>
                      <option value="2">2</option>
                      <option value="3">3</option>
                      <option value="4">4</option>
                      <option value="5">5</option>
                    </select>
                  </td>
                </tr>
                <tr>
                  <td>
                    Cabin Class
                  </td>
                </tr>
                <tr>
                  <td>
                    <select id="cabClassSelect" name="cabClassSelect" title="Choose flight class" align="center">
                      <option value="">Any</option>
                      <option value="E" selected="selected">Economy</option>
                      <option value="S">Premium Economy</option>
                      <option value="B">Business</option>
                      <option value="J">Premium Business</option>
                      <option value="F">First</option>
                      <option value="P">Premium First</option>
                    </select>
                  </td>
                </tr>
                
                
                <tr>
                  <td align="right" colspan="3">
                    <input name="fareType" value="YYYYN" type="hidden">
                    <input name="wait" value="true" type="hidden">
                    <input name="searchAir.segments[1].arrivalCity" id="searchAir.segments1.arrivalCity" type="hidden">
                    <input name="searchAir.segments[1].departCity" id="searchAir.segments1.departCity" type="hidden">
                    <input name="searchAir.segments[0].cabinIndicator" id="searchAir.segments0.cabinIndicator" type="hidden">
                    <input name="searchAir.segments[1].cabinIndicator" id="searchAir.segments1.cabinIndicator" type="hidden">
                    <input title="Search for availability using the entered criteria" value="Search Flights" type="submit">
                  </td>
                </tr>
              </table>
            </form>
          </div>
        

        

        
      </div>





</div></li></ul></div>

      

