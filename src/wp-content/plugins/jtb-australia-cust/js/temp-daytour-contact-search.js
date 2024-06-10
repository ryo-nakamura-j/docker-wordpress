$(document).ready(function(){

$('#wrap').on('mouseover', '.dtsearch', function () {
    //alert('clicked');
    var newUrl = $(this).attr('href').replace(/day\-tour\/\/.*?\?/i, 'day-tour/?');
$(this).attr('href', newUrl);

});

/*
 
 1.11.1
 alert( $().jquery     );

*/

});

