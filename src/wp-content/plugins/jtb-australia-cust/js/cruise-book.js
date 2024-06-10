
function cruise_book(){
	$("#tabbutton3").click();
    scrollToAnchor('tabsanchor');
    var x = document.getElementsByClassName("wpcf7-textarea");
    x[0].value = x[0].value + "\n\n[Cruise special booking enquiry]\n";

    //var y = document.getElementsByClassName("mdc-textfield__label");
    //y[4].style.visibility = "hidden";
    
    //wpcf7-textarea
    //myTextArea.value
}