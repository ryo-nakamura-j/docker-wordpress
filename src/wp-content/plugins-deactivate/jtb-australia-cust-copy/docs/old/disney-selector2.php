<?php


function print_disney_selector(){ 
    //echo plugins_url('/jtb-australia-cust/docs/disney-selector.php' ); 

$string = <<<HEREDOC
   
<script type="text/javascript">

function selectDays(x){ 
    document.getElementById("bookButton").innerHTML = "";
    var htmlContent = '  <p>Select the pass you would like for the first day</p> <select  id = "passselect"  name="pass" onmousedown="this.value" onchange="selectDays(this.value)"> <option value="9">Select a pass option</option>   <option value="5">Disneyland</option>   <option value="6">DisneySea</option>  </select>   <p>Select the pass you would like for the second day</p> <select  id = "passselect2"  name="pass" onmousedown="this.value" onchange="selectDays(this.value)">  <option value="9">Select a pass option</option>  <option value="7">Disneyland</option>   <option value="8">DisneySea</option>  </select>  ';

    if (x==0){
        document.getElementById("chosePasses").innerHTML = "";
    }
    else if (x==1){
        document.getElementById("chosePasses").innerHTML = '<p>Select the pass you would like to book</p><select  id = "passselect"  name="pass" onmousedown="this.value" onchange="selectDays(this.value)"> <option value="9">Select a pass option</option>  <option value="5">Disneyland</option>  <option value="6">DisneySea</option> </select>';
    }
    else if (x==2){
        document.getElementById("chosePasses").innerHTML = htmlContent;
    }
    else if (x==3){
        document.getElementById("chosePasses").innerHTML = htmlContent + '<p>You will be able to select either option for your third day.</p>';
    }
    else if (x==4){
        document.getElementById("chosePasses").innerHTML = htmlContent + '<p>You will be able to select either option for your third and fourth days.</p>';
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
            document.getElementById("bookButton").innerHTML = '<h3>Summary</h3><p>You have selected a one day Disneyland pass.</p><p><a class="button" href="'+output[1]+'">Book pass</a></p>';
        }
        else if (p1=="6"){
            document.getElementById("bookButton").innerHTML = '<h3>Summary</h3><p>You have selected a one day DisneySea pass.</p><p><a class="button" href="'+output[1]+'">Book pass</a></p>';
        }
    }

    var output = print_multi_day_summary(p1,p2,lvl1);
    
    if (lvl1=="2"){
        //print for 2 passes
        var outhtml = "";
        outhtml += '<h3>Summary</h3><p>You have selected a two day pass.</p><ul>';
        outhtml += output[0];
        outhtml +=  '</ul><p><a class="button" href="'+output[1]+'">Book pass</a></p>';
        document.getElementById("bookButton").innerHTML = outhtml;
    }else if(lvl1=="3"){
        var outhtml = "";
        outhtml += '<h3>Summary</h3><p>You have selected a three day pass.</p><ul>';
        outhtml += output[0];
        outhtml +=  '<li>Day three: either option</li></ul><p><a class="button" href="'+output[1]+'">Book pass</a></p>';
        document.getElementById("bookButton").innerHTML = outhtml;
    }else if(lvl1=="4"){
        var outhtml = "";
        outhtml += '<h3>Summary</h3><p>You have selected a four day pass.</p><ul>';
        outhtml += output[0];
        outhtml +=  '<li>Day three: either option</li><li>Day four: either option</li></ul><p><a class="button" href="'+output[1]+'">Book pass</a></p>';
        document.getElementById("bookButton").innerHTML = outhtml;
    }

}

function print_multi_day_summary(p1,p2,lvl1){ //return array of list items + button URL 
    var outhtml = "";
    var outarray = new Array();
    var outurl="";
    if (p1=="5"){
        outhtml +=  '<li>Day one: Disneyland</li>';
    }
    else if (p1=="6"){
        outhtml +=  '<li>Day one: DisneySea</li>';
    }
    if (p2=="7"){
        outhtml +=  '<li>Day two: Disneyland</li>';
    }
    else if (p2=="8"){
        outhtml +=  '<li>Day two: DisneySea</li>';
    }

    if(lvl1=="1"){//1 day only 154
        if (p1=="5"){
            outurl =  'https://www.nx.jtbtravel.com.au/tour//?supplierid=100&productid=154&scu=1&qty=1A&srb=Tickets&dst=Chiba&searchurl='; // 1 day d land 
        }
        else if (p1=="6"){
            outurl =  'https://www.nx.jtbtravel.com.au/tour//?supplierid=100&productid=154&scu=1&qty=1A&srb=Tickets&dst=Chiba&searchurl=';// 1 day d sea 
        }
    }else if(lvl1=="2"){
        if ((p1=="5")&&(p2=="7")){
            outurl =  'https://www.nx.jtbtravel.com.au/tour//?supplierid=100&productid=155&scu=1&qty=1A&srb=Tickets&dst=Chiba&searchurl='; // 2 day d land    d land 
        }
        else if ((p1=="6")&&(p2=="7")){
            outurl =  'https://www.nx.jtbtravel.com.au/tour//?supplierid=100&productid=155&scu=1&qty=1A&srb=Tickets&dst=Chiba&searchurl='; // 2 day D-SEA    d land 
        }
        else if ((p1=="5")&&(p2=="8")){
            outurl =  'https://www.nx.jtbtravel.com.au/tour//?supplierid=100&productid=155&scu=1&qty=1A&srb=Tickets&dst=Chiba&searchurl='; // 2 day d land  D-SEA 
        }
        else if ((p1=="6")&&(p2=="8")){
            outurl =  'https://www.nx.jtbtravel.com.au/tour//?supplierid=100&productid=155&scu=1&qty=1A&srb=Tickets&dst=Chiba&searchurl='; // 2 day  D-SEA    D-SEA 
        }
    }else if(lvl1=="3"){
        if ((p1=="5")&&(p2=="7")){
            outurl =  'https://www.nx.jtbtravel.com.au/tour//?supplierid=100&productid=156&scu=1&qty=1A&srb=Tickets&dst=Chiba&searchurl='; // 3 day d land    d land 
        }
        else if ((p1=="6")&&(p2=="7")){
            outurl =  'https://www.nx.jtbtravel.com.au/tour//?supplierid=100&productid=156&scu=1&qty=1A&srb=Tickets&dst=Chiba&searchurl='; // 3 day D-SEA    d land 
        }
        else if ((p1=="5")&&(p2=="8")){
            outurl =  'https://www.nx.jtbtravel.com.au/tour//?supplierid=100&productid=156&scu=1&qty=1A&srb=Tickets&dst=Chiba&searchurl='; // 3 day d land  D-SEA 
        }
        else if ((p1=="6")&&(p2=="8")){
            outurl =  'https://www.nx.jtbtravel.com.au/tour//?supplierid=100&productid=156&scu=1&qty=1A&srb=Tickets&dst=Chiba&searchurl='; // 3 day  D-SEA    D-SEA 
        }
    }else if(lvl1=="4"){
        if ((p1=="5")&&(p2=="7")){
            outurl =  'https://www.nx.jtbtravel.com.au/tour//?supplierid=100&productid=157&scu=1&qty=1A&srb=Tickets&dst=Chiba&searchurl='; // 4 day d land    d land 
        }
        else if ((p1=="6")&&(p2=="7")){
            outurl =  'https://www.nx.jtbtravel.com.au/tour//?supplierid=100&productid=157&scu=1&qty=1A&srb=Tickets&dst=Chiba&searchurl='; // 4 day D-SEA    d land 
        }
        else if ((p1=="5")&&(p2=="8")){
            outurl =  'https://www.nx.jtbtravel.com.au/tour//?supplierid=100&productid=157&scu=1&qty=1A&srb=Tickets&dst=Chiba&searchurl='; // 4 day d land  D-SEA 
        }
        else if ((p1=="6")&&(p2=="8")){
            outurl =  'https://www.nx.jtbtravel.com.au/tour//?supplierid=100&productid=157&scu=1&qty=1A&srb=Tickets&dst=Chiba&searchurl='; // 4 day  D-SEA    D-SEA 
        }
    }
    outarray[outarray.length]=outhtml;
    outarray[outarray.length]=outurl;
    return outarray;
}



</script>

<p>Select the number of days you want for your Disneyland/ DisneySea combination ticket</p>
<select id = "durationselect" name="duration" onmousedown="this.value" onchange="selectDays(this.value)">
  <option value="0">Select number of days</option>
  <option value="1">1 day</option>
  <option value="2">2 days</option>
  <option value="3">3 days</option>
  <option value="4">4 days</option>
</select>

<div id="chosePasses"></div>

<div id="bookButton"></div>


HEREDOC;
return $string;
}




?>
