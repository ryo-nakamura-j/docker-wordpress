$(window).bind("load resize",function(e){
    $('a[href^="#"]').on('click', function (e) {		
		var widthwin = $( window ).width();
		if(widthwin > 1000) var headerh = 118;
		else if (widthwin <= 1000 && widthwin >= 768) headerh = 118;
		else  headerh = 50;
		e.preventDefault();
		var target = this.hash,
			$target = $(target);

		$('html, body').stop().animate({
			'scrollTop': $target.offset().top - headerh 
		}, 300, 'swing', function () {});		
    });
});

$(window).bind("load",function(e){
	
	var widthwin = $( window ).width();
		if(widthwin > 1000) var headerh = 118;
		else if (widthwin <= 1000 && widthwin >= 768) headerh = 118;
		else headerh = 50;
		  
	var str = location.hash; 
	if(str != '' && $(str).length != 0) {
		var n = str.replace("_temp","");
		$('html,body').animate({scrollTop:$(n).offset().top - headerh}, 300);
	}
});

