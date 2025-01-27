var Cal = function(divId) {

  //Store div id
  this.divId = divId;

  // Days of week, starting on Sunday
  this.DaysOfWeek = [
    'Sun',
    'Mon',
    'Tue',
    'Wed',
    'Thu',
    'Fri',
    'Sat'
  ];

  // Months, stating on January
  this.Months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' ];

  // Set the current month, year
  var d = new Date();


     if(this.divId =="divCal2"){
d.setMonth(d.getMonth() + 1,1);
        }
      
  
     if(this.divId =="divCal3"){
d.setMonth(d.getMonth() + 2,1);
        }
  
       if(this.divId =="divCal4"){
d.setMonth(d.getMonth() + 3,1);
        }
   if(this.divId =="divCal5"){
d.setMonth(d.getMonth() + 4,1);
        }   if(this.divId =="divCal6"){
d.setMonth(d.getMonth() + 5,1);
        }
  
  this.currMonth = d.getMonth();
  this.currYear = d.getFullYear();
  this.currDay = d.getDate();
  
  

};




// Show current month
Cal.prototype.showcurr = function(x) {
  this.showMonth(this.currYear, this.currMonth,x);
};

// Show month (year, month)
Cal.prototype.showMonth = function(y, m,x) {

    var id = x;
  var d = new Date()
  // First day of the week in the selected month
  , firstDayOfMonth = new Date(y, m, 1).getDay()
  // Last day of the selected month
  , lastDateOfMonth =  new Date(y, m+1, 0).getDate()
  // Last day of the previous month
  , lastDayOfLastMonth = m == 0 ? new Date(y-1, 11, 0).getDate() : new Date(y, m, 0).getDate();
     
           
           

  var html = '<table>';

  // Write selected month and year
  html += '<thead><tr>';
  html += '<td colspan="7">' + this.Months[m] + ' ' + y + '</td>';
  html += '</tr></thead>';


  // Write the header of the days of the week
  html += '<tr class="days">';
  for(var i=0; i < this.DaysOfWeek.length;i++) {
    html += '<td>' + this.DaysOfWeek[i] + '</td>';
  }
  html += '</tr>';

  // Write the days
  var i=1;
  do {

    var dow = new Date(y, m, i).getDay();

    // If Sunday, start new row
    if ( dow == 0 ) {
      html += '<tr>';
    }
    // If not Sunday but first day of the month
    // it will write the last days from the previous month
    else if ( i == 1 ) {
      html += '<tr>';
      var k = lastDayOfLastMonth - firstDayOfMonth+1;
      for(var j=0; j < firstDayOfMonth; j++) {
        html += '<td class="not-current">' + k + '</td>';
        k++;
      }
    }

// Write the current day in the loop @@@@

//load date array

var datesjtb = document.getElementById('dateslistjtb').value;
var datesjtb2 = datesjtb.split("@");
//var date_array_2 = [  [20190110,20190131,1] , [20190201,20190322,2]  ];
var date_array_2 = [];
for (var iii = 0;iii< datesjtb2.length ; iii++) {
  date_array_2.push(datesjtb2[iii].split("-"));
}

//var date_array_2 = datesjtb2.split("-");
var labels = ["x","one","two","three","four","five","six","seven"];
var chk = new Date();
var chkY = chk.getFullYear();
var chkM = chk.getMonth();

chkM = (chkM + 1);

     if(id =="divCal2"){

chkM = (chkM + 1);
        }
     if(id =="divCal3"){
chkM=(chkM + 2);
        }
       if(id =="divCal4"){
chkM=(chkM + 3);
        }
   if(id =="divCal5"){
chkM=(chkM + 4);
        }   if(id =="divCal6"){
chkM=(chkM+ 5);
        }
if(chkM>12){
  chkM = (chkM-12);
  chkY+=1;
}
if(String(chkM).length ==1){
  chkM = "0" + chkM;
}
var chkD = i;
if(String(chkD).length ==1){
  chkD = "0" + i;
}

//var chkD = chk.getDay();
  if(    (String(chkY)+String(chkM)+String(chkD)) < (String(chkY)+String(chkM)+String(chk.getDate())) && (id =="divCal") && 0  ){
  html += '<td class="normal past">' + i + '</td>';
}else{
var label3 = "";

for (var iii = 0;iii< date_array_2.length ; iii++) {
  //label3 = chkY+chkM+chkD+"-"+date_array_2[iii][1]+"-"+date_array_2[iii][0]
   //label3 = String(chkY)+String(chkM)+String(chkD)+"-"+date_array_2[iii][1]+"-"+date_array_2[iii][0]+"--"+chkY+chkM+String(chk.getDate());
  //normal 2056-undefined-
  if((parseInt(String(chkY)+String(chkM)+String(chkD))<=date_array_2[iii][1])&&(parseInt(String(chkY)+String(chkM)+String(chkD))>=date_array_2[iii][0])){
    label3 = String(labels[date_array_2[iii][2]]);
    //label3 += " "+chkY+chkM+chkD+"-"+date_array_2[iii][1]+"-"+date_array_2[iii][0]+"--"+chkY+chkM+String(chk.getDate()) + "===" +  (chkY+chkM+chkD) +"-"+(chkY+chkM+String(chk.getDate())) ;
    break;
  }
}

  html += '<td class="normal '+label3+'">' + i + '</td>';
}
// If Saturday, closes the row
if ( dow == 6 ) {
  html += '</tr>';
}
    // If not Saturday, but last day of the selected month
    // it will write the next few days from the next month
    else if ( i == lastDateOfMonth ) {
      var k=1;
      for(dow; dow < 6; dow++) {
        html += '<td class="not-current">' + k + '</td>';
        k++;
      }
    }

    i++;
  }while(i <= lastDateOfMonth);

  // Closes table
  html += '</table>';

  // Write HTML to the div
  document.getElementById(this.divId).innerHTML = html;
};

// On Load of the window
window.onload = function() {

  // Start calendar
  var c = new Cal("divCal");			
  c.showcurr("divCal");

    var c = new Cal("divCal2");			
  c.showcurr("divCal2");
  
    var c = new Cal("divCal3");			
  c.showcurr("divCal3");
  
    var c = new Cal("divCal4");     
  c.showcurr("divCal4");		


    var c = new Cal("divCal5");     
  c.showcurr("divCal5");

  var c = new Cal("divCal6");     
  c.showcurr("divCal6");

  

}

// Get element by id
function getId(id) {
  return document.getElementById(id);
}