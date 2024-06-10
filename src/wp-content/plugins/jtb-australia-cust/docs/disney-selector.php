

<script type="text/javascript">
//<!--
function selectDays(x){ 
    document.getElementById("bookButton").innerHTML = "";
    var htmlContent = ' <span><select  id = "passselect"  name="pass" onmousedown="this.value" onchange="selectDays(this.value)"> <option value="9">Select option</option>   <option value="5">Disneyland</option>   <option value="6">DisneySea</option>  </select> </span><span> <select  id = "passselect2"  name="pass" onmousedown="this.value" onchange="selectDays(this.value)">  <option value="9">Select option</option>  <option value="7">Disneyland</option>   <option value="8">DisneySea</option>  </select>  </span> ';

    if (x==0){
        document.getElementById("chosePasses").innerHTML = "";
        document.getElementById("chosePassesTitle").innerHTML = "";
        document.getElementById("bookButton").innerHTML = "";
    }
    else if (x==1){
        document.getElementById("chosePasses").innerHTML = ' <span><select  id = "passselect"  name="pass" onmousedown="this.value" onchange="selectDays(this.value)"> <option value="9">Select option</option>  <option value="5">Disneyland</option>  <option value="6">DisneySea</option> </select></span> ';
        document.getElementById("chosePassesTitle").innerHTML = '<h4>Day 1</h4>';
    }
    else if (x==2){
        document.getElementById("chosePasses").innerHTML = htmlContent;
        document.getElementById("chosePassesTitle").innerHTML = '<h4>Day 1</h4><h4>Day 2</h4>';
    }
    else if (x==3){
        document.getElementById("chosePasses").innerHTML = htmlContent + ' <p>3rd Day - Park hop at your leisure</p>';
        document.getElementById("chosePassesTitle").innerHTML = '<h4>Day 1</h4><h4>Day 2</h4><h4>Day 3</h4>';
    }
    else if (x==4){
        document.getElementById("chosePasses").innerHTML = htmlContent + ' <p>3rd Day - Park hop at your leisure</p><p>4th Day - Park hop at your leisure</p>';
        document.getElementById("chosePassesTitle").innerHTML = '<h4>Day 1</h4><h4>Day 2</h4><h4>Day 3</h4><h4>Day 4</h4>';
    }
    else if (x==5 || x==6 || x==7 || x==8 || x==9){
        printButtons();
    }
    
}

function printButtons(){
    var lvl1 = document.getElementById("durationselect").value;
    var p1 = document.getElementById("passselect").value;
    if (p1=="9"){
        document.getElementById("bookButton").innerHTML = " ";
        return false;
    }
    var p2 = "-1";
    var output = print_multi_day_summary(p1,p2,lvl1);
    if (lvl1 != 1 && lvl1 != 0){
        p2 = document.getElementById("passselect2").value;
        if (p2=="9"){
            document.getElementById("bookButton").innerHTML = " ";
            return false;
        }
    }else if (lvl1 == 1){
        //print for one pass only 5,6
        if (p1=="5"){
            document.getElementById("bookButton").innerHTML = ' <p><a class="btn btn-primary centre" href="'+output+'">Select Ticket</a></p>';
        }
        else if (p1=="6"){
            document.getElementById("bookButton").innerHTML = ' <p><a class="btn btn-primary centre" href="'+output+'">Select Ticket</a></p>';
        }
    }

    var output = print_multi_day_summary(p1,p2,lvl1);
    
    if (lvl1=="2"){
        //print for 2 passes
        var outhtml = "";
        outhtml += ' <p><a class=" btn btn-primary centre" href="'+output+'">Select Ticket</a></p>';
        document.getElementById("bookButton").innerHTML = outhtml;
    }else if(lvl1=="3"){
        var outhtml = "";
        outhtml += ' <p><a class=" btn btn-primary centre" href="'+output+'">Select Ticket</a></p>';
        document.getElementById("bookButton").innerHTML = outhtml;
    }else if(lvl1=="4"){
        var outhtml = "";
        outhtml += ' <p><a class=" btn btn-primary centre" href="'+output+'">Select Ticket</a></p>';
        document.getElementById("bookButton").innerHTML = outhtml;
    }

}

function print_multi_day_summary(p1,p2,lvl1){ //return array of list items + button URL  
    var outurl="";
     
    outurl="";
    if(lvl1=="1"){//1 day only 154
        if (p1=="5"){
            outurl =  'https://www.nx.jtbtravel.com.au/tickets/tokyo-disney-resort/disney-ticket/?supplierid=1231&productid=7183&scu=1&qty=1A&srb=Tickets&dst=Chiba&searchurl='; // 1 day d land 
        }
        else if (p1=="6"){
            outurl =  'https://www.nx.jtbtravel.com.au/tickets/tokyo-disney-resort/disney-ticket/?supplierid=1231&productid=7184&scu=1&qty=1A&srb=Tickets&dst=Chiba&searchurl=';// 1 day d sea 
        }
    }else if(lvl1=="2"){
        if ((p1=="5")&&(p2=="7")){
            outurl =  'https://www.nx.jtbtravel.com.au/tickets/tokyo-disney-resort/disney-ticket/?supplierid=1231&productid=7185&scu=1&qty=1A&srb=Tickets&dst=Chiba&searchurl='; // 2 day d land    d land 
        }
        else if ((p1=="6")&&(p2=="7")){
            outurl =  'https://www.nx.jtbtravel.com.au/tickets/tokyo-disney-resort/disney-ticket/?supplierid=1231&productid=7187&scu=1&qty=1A&srb=Tickets&dst=Chiba&searchurl='; // 2 day D-SEA    d land 
        }
        else if ((p1=="5")&&(p2=="8")){
            outurl =  'https://www.nx.jtbtravel.com.au/tickets/tokyo-disney-resort/disney-ticket/?supplierid=1231&productid=7186&scu=1&qty=1A&srb=Tickets&dst=Chiba&searchurl='; // 2 day d land  D-SEA 
        }
        else if ((p1=="6")&&(p2=="8")){
            outurl =  'https://www.nx.jtbtravel.com.au/tickets/tokyo-disney-resort/disney-ticket/?supplierid=1231&productid=7188&scu=1&qty=1A&srb=Tickets&dst=Chiba&searchurl='; // 2 day  D-SEA    D-SEA 
        }
    }else if(lvl1=="3"){
        if ((p1=="5")&&(p2=="7")){
            outurl =  'https://www.nx.jtbtravel.com.au/tickets/tokyo-disney-resort/disney-ticket/?supplierid=1231&productid=7191&scu=1&qty=1A&srb=Tickets&dst=Chiba&searchurl='; // 3 day d land    d land 
        }
        else if ((p1=="6")&&(p2=="7")){
            outurl =  'https://www.nx.jtbtravel.com.au/tickets/tokyo-disney-resort/disney-ticket/?supplierid=1231&productid=7192&scu=1&qty=1A&srb=Tickets&dst=Chiba&searchurl='; // 3 day D-SEA    d land 
        }
        else if ((p1=="5")&&(p2=="8")){
            outurl =  'https://www.nx.jtbtravel.com.au/tickets/tokyo-disney-resort/disney-ticket/?supplierid=1231&productid=7190&scu=1&qty=1A&srb=Tickets&dst=Chiba&searchurl='; // 3 day d land  D-SEA 
        }
        else if ((p1=="6")&&(p2=="8")){
            outurl =  'https://www.nx.jtbtravel.com.au/tickets/tokyo-disney-resort/disney-ticket/?supplierid=1231&productid=7193&scu=1&qty=1A&srb=Tickets&dst=Chiba&searchurl='; // 3 day  D-SEA    D-SEA 
        }
    }else if(lvl1=="4"){
        if ((p1=="5")&&(p2=="7")){
            outurl =  'https://www.nx.jtbtravel.com.au/tickets/tokyo-disney-resort/disney-ticket/?supplierid=1231&productid=7194&scu=1&qty=1A&srb=Tickets&dst=Chiba&searchurl='; // 4 day d land    d land 
        }
        else if ((p1=="6")&&(p2=="7")){
            outurl =  'https://www.nx.jtbtravel.com.au/tickets/tokyo-disney-resort/disney-ticket/?supplierid=1231&productid=7196&scu=1&qty=1A&srb=Tickets&dst=Chiba&searchurl='; // 4 day D-SEA    d land 
        }
        else if ((p1=="5")&&(p2=="8")){
            outurl =  'https://www.nx.jtbtravel.com.au/tickets/tokyo-disney-resort/disney-ticket/?supplierid=1231&productid=7195&scu=1&qty=1A&srb=Tickets&dst=Chiba&searchurl='; // 4 day d land  D-SEA 
        }
        else if ((p1=="6")&&(p2=="8")){
            outurl =  'https://www.nx.jtbtravel.com.au/tickets/tokyo-disney-resort/disney-ticket/?supplierid=1231&productid=7197&scu=1&qty=1A&srb=Tickets&dst=Chiba&searchurl='; // 4 day  D-SEA    D-SEA 
        }
    } 


    return outurl + '&date='+ document.getElementById("departDateyear").value + '-' + document.getElementById("departDatemonth").value +'-' +document.getElementById("departDateday").value; //2016-06-09
}


function dateChange(){
  if(document.getElementById("bookButton").innerHTML != ""){
    printButtons();
  }
}


</script>

 

<script type="text/javascript">

Date.locale = {
    month_names: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
    short_names: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
};

var Explore = Explore || {};

Explore.Options = {
  earliestDeparture: 5,
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
</script>

<script type="text/javascript">

$(function(){

  var today = new Date();
  var earliestDeparture = new Date(today.getFullYear(), today.getMonth(), today.getDate()+Explore.Options.earliestDeparture);
  var defaultReturn = new Date(earliestDeparture.getFullYear(), earliestDeparture.getMonth(), earliestDeparture.getDate()+Explore.Options.defaultReturn);


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
                $(".month", wrapper).val(date.getMonth()+1);
                $(".year", wrapper).val(date.getFullYear());
            }
     });
  });
});


 //&date=2016-06-09
//@@@@@@@
//-->
</script>



<div class="tourplan_plugin_section Tours">
<div class="plugin_control">

            <div class="productSearchSection">
    <div class="productSearchControl">
        <h3>Disney Ticket Selector</h3>
        <div class="searchControls">
            <div class="row">
                <input type="hidden" name="srb" value="Tours">
                <input type="hidden" name="cty" value="Japan">

 
                    <div class="col-xs-12 col-md-3">
<label><h4>Number of days for the ticket</h4>
<select id = "durationselect" name="duration" onmousedown="this.value" onchange="selectDays(this.value)">
<option value="0">Select number of days</option>
<option value="1">1 day ticket</option>
<option value="2">2 consecutive days</option>
<option value="3">3 consecutive days</option>
<option value="4">4 consecutive days</option>
</select>
                        </label>
                    </div>
                    <div class="col-xs-12 col-md-3">
<label><h4>Date of usage (the first day)</h4>
<div class="explore-date-picker explore-validate-date" id="searchAir.segments0.departDate">
<span>
<select class="day" id="departDateday" name="departDateday" onchange="dateChange()" ></select>
<select class="month" id="departDatemonth" name="departDatemonth" onchange="dateChange()" ></select>
<select class="year" id="departDateyear" name="departDateyear" onchange="dateChange()" ></select>
<input class="date-selector" type="hidden" />
</span>
</div>
</label>
                    </div>
                    <div class="col-xs-12 col-md-3">
<label><div class="left-smol-30-left" id="chosePassesTitle"></div> 
<div class="left-smol-30-right" id="chosePasses"></div> 
</label>
                    </div>
                    <div class="col-xs-12 col-md-3">
<label><div id="bookButton"></div>
</label>
                    </div>
            </div>
                

</div>

        </div>
    </div>
</div>

</div>
