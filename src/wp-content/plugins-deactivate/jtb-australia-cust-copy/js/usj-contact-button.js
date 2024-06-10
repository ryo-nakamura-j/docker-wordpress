function switch_book_button(){
	if(document.getElementById("tabbutton6")){
		document.getElementById("tabbutton6").click();
		document.getElementById('tabs_container').scrollIntoView();
	}else if(document.getElementById("tabbutton5")){
		document.getElementById("tabbutton5").click();
		document.getElementById('tabs_container').scrollIntoView();
	}else if(document.getElementById("tabbutton4")){
		document.getElementById("tabbutton4").click();
		document.getElementById('tabs_container').scrollIntoView();
	}else if(document.getElementById("tabbutton3")){
		document.getElementById("tabbutton3").click();
		document.getElementById('tabs_container').scrollIntoView();
	}else if(document.getElementById("tabbutton2")){
		document.getElementById("tabbutton2").click();
		document.getElementById('tabs_container').scrollIntoView();
	}
}