jQuery(document).ready(function(){
	$=jQuery;
	var divs = document.getElementsByTagName("div"); 
	for(var i = 0; i < divs.length; i++){
  if(!$(divs[i]).hasClass('MOWrn'))
  {
    if($(divs[i]).hasClass('notice') || $(divs[i]).hasClass('updated') 
    || $(divs[i]).hasClass('notice-info') || $(divs[i]).hasClass('is-dismissible')
    || $(divs[i]).hasClass('notice-success'))
    {
       	$(divs[i]).hide()
    }
  }
  }
});