
//working
var modala=null;
var modaldt=null;
var modalb=null;
var modalc=null;
var modal1=null;
var modal2=null;
var modal3=null;
var modal4=null;
var modal5=null;

//subscribe link in secondary menu & footer
var myElem = document.getElementById('myModal-a');
if (myElem != null){
	modala = document.getElementById("myModal-a");
	if(document.getElementById("menu-item-4613")!=null){
		var btna = document.getElementById("menu-item-4613").children[0];
		btna.onclick = function() {modala.style.display = "block";document.getElementById("entry_1306134107").focus();}
	}
	if(document.getElementById("menu-item-4618")!=null){
		var btna2 = document.getElementById("menu-item-4618").children[0];
		btna2.onclick = function() {modala.style.display = "block";document.getElementById("entry_1306134107").focus();}
	}
}

var myElem = document.getElementById('myModal-dt');
if (myElem != null){
	modaldt = document.getElementById("myModal-dt");
	if(document.getElementById("dt-search-contact-button")!=null){
		var btnadt = document.getElementById("dt-search-contact-button");
		btnadt.onclick = function() {modaldt.style.display = "block"; }
	}
}



//Flight search in banner, menu, hot-deals
var myElem = document.getElementById('myModal-b');
if (myElem != null){
	var modalb = document.getElementById("myModal-b"); //menu-item-23685
	if(document.getElementById("menu-item-5092")!=null){
		var btnb = document.getElementById("menu-item-5092").children[0];
		if (btnb != null){
			btnb.onclick = function() {modalb.style.display = "block";}
		}
	}

		if(document.getElementById("menu-item-23685")!=null){
		var btnb33 = document.getElementById("menu-item-23685").children[0];
		if (btnb33 != null){
			btnb33.onclick = function() {modalb.style.display = "block";}
		}
	}


	if(document.getElementById("sliderdiv99")!=null){
		var btnb2 = document.getElementById("sliderdiv99").children[0];
		if (btnb2 != null){
			btnb2.onclick = function() {modalb.style.display = "block";}
		}
	}
	if(document.getElementById("hotdealpopup0")!=null){
		var btnb3 = document.getElementById("hotdealpopup0");
		if (btnb3 != null){
			btnb3.onclick = function() {modalb.style.display = "block";}
		}
	}
	if(document.getElementById("hotdealpopuplink0")!=null){
		var btnb4 = document.getElementById("hotdealpopuplink0").children[0];
		if (btnb4 != null){
			btnb4.onclick = function() {modalb.style.display = "block";}
		}
	}//mobile menu VvVvV
	if(document.getElementsByClassName("flightsbuttonpopup")!=null){
		var btnb5 = document.getElementsByClassName("flightsbuttonpopup")[0];
		if (btnb5 != null){
			btnb5.onclick = function() {modalb.style.display = "block";}
		}
		var btnb6 = document.getElementsByClassName("flightsbuttonpopup")[1];
		if (btnb6 != null){
			btnb6.onclick = function() {modalb.style.display = "block";}
		}
	}
}

//website search social button
var myElem = document.getElementById('myModal-c');
if (myElem != null){
	modalc = document.getElementById("myModal-c");
	var btnc = document.getElementById("websearchpopup");
	btnc.onclick = function() {
		modalc.style.display = "block";
		document.getElementById("websearchbox").focus();
	}
	var btnc2 = document.getElementById("websearchpopup2");
	btnc2.onclick = function() {
		modalc.style.display = "block";
		document.getElementById("websearchbox").focus();
	}
}



//5 place holders for shortcode popup boxes.
myElem = document.getElementById('myModal-1');
if (myElem != null){
	modal1 = document.getElementById("myModal-1");
	var btn1 = document.getElementById("wppopup1");
	btn1.onclick = function() {modal1.style.display = "block";}
}

myElem = document.getElementById('myModal-2');
if (myElem != null){
	modal2 = document.getElementById("myModal-2");
	var btn2 = document.getElementById("wppopup2");
	btn2.onclick = function() {modal2.style.display = "block";}
}

myElem = document.getElementById('myModal-3');
if (myElem != null){
	modal3 = document.getElementById("myModal-3");
	var btn3 = document.getElementById("wppopup3");
	btn3.onclick = function() {modal3.style.display = "block";}
}

myElem = document.getElementById('myModal-4');
if (myElem != null){
	var modal4 = document.getElementById("myModal-4");
	var btn4 = document.getElementById("wppopup4");
	btn4.onclick = function() {modal4.style.display = "block";}
}

myElem = document.getElementById('myModal-5');
if (myElem != null){
	modal5 = document.getElementById("myModal-5");
	var btn5 = document.getElementById("wppopup5");
	btn5.onclick = function() {modal5.style.display = "block";}
}



window.onclick = function(event) {
	if (event.target == modal1) { modal1.style.display = "none"; }
	else if (event.target == modal2) { modal2.style.display = "none"; }
	else if (event.target == modal3) { modal3.style.display = "none"; }
	else if (event.target == modal4) { modal4.style.display = "none"; }
	else if (event.target == modal5) { modal5.style.display = "none"; }

	else if (event.target == modala) { modala.style.display = "none"; }
	else if (event.target == modaldt) { modaldt.style.display = "none"; }
	else if (event.target == modalb) { modalb.style.display = "none"; }
	else if (event.target == modalc) { modalc.style.display = "none"; }
}

//var numItems = $(".close").length;
//for (var i=0 ; i<numItems;i+=1){
$(".close").click(function() {
	if (window.modal1) { modal1.style.display = "none"; }
	if (window.modal2) { modal2.style.display = "none"; }
	if (window.modal3) { modal3.style.display = "none"; }
	if (window.modal4) { modal4.style.display = "none"; }
	if (window.modal5) { modal5.style.display = "none"; }

	if (window.modala) { modala.style.display = "none"; }
	if (window.modaldt) { modaldt.style.display = "none"; }
	if (window.modalb) { modalb.style.display = "none"; }
	if (window.modalc) { modalc.style.display = "none"; }
	if (window.document.getElementById("myModal-hyperdia") ) { 
		document.getElementById("myModal-hyperdia").style.display = "none"; }
	return true;
});
	//document.getElementsByClassName("close")[i].onclick = function(event) {}
//}



$(".modal").click(function(event) {
 if(event.target.id=="myModal-hyperdia"){
if (window.document.getElementById("myModal-hyperdia") ) {
		document.getElementById("myModal-hyperdia").style.display = "none"; }
	return true;
}
 
});
