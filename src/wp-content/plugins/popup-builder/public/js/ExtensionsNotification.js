function SGPBExtensionsNotification()
{this.init();}
SGPBExtensionsNotification.prototype.init=function()
{this.closeExtensionNotificationPanel();this.dontShowAgain();this.closeProblemAlert();this.dontShowAgainProblemAlert();this.addToPopupMenuLinkColor();};SGPBExtensionsNotification.prototype.addToPopupMenuLinkColor=function()
{jQuery('a[href="edit.php?post_type='+SGPB_JS_EXTENSIONS_PARAMS.popupPostType+'&page='+SGPB_JS_EXTENSIONS_PARAMS.extendPage+'"]').css({color:"#55efc4"});jQuery('a[href="edit.php?post_type='+SGPB_JS_EXTENSIONS_PARAMS.popupPostType+'&page='+SGPB_JS_EXTENSIONS_PARAMS.extendPage+'"]').attr('href',SGPB_JS_EXTENSIONS_PARAMS.allExtensionsUrl);jQuery('a[href="edit.php?post_type='+SGPB_JS_EXTENSIONS_PARAMS.popupPostType+'&page='+SGPB_JS_EXTENSIONS_PARAMS.supportPage+'"]').attr('href',SGPB_JS_EXTENSIONS_PARAMS.supportUrl);};SGPBExtensionsNotification.prototype.closeProblemAlert=function()
{var closeButton=jQuery('.sgpb-problem-notice-close');if(!closeButton.length){return false;}
closeButton.bind('click',function(){jQuery('.sgpb-alert-problem').remove();});};SGPBExtensionsNotification.prototype.dontShowAgainProblemAlert=function()
{var dontShow=jQuery('.sgpb-problem-notice-dont-show');if(!dontShow.length){return false;}
dontShow.bind('click',function(){var data={action:'sgpb_dont_show_problem_alert',nonce:SGPB_JS_EXTENSIONS_PARAMS.nonce};jQuery.post(ajaxurl,data,function(responce){jQuery('.sgpb-alert-problem').remove();});});};SGPBExtensionsNotification.prototype.closeExtensionNotificationPanel=function()
{var closeButton=jQuery('.sgpb-extension-notice-close');if(!closeButton.length){return false;}
closeButton.bind('click',function(){jQuery('.sgpb-extensions-notices').remove();});};SGPBExtensionsNotification.prototype.dontShowAgain=function()
{var dontShow=jQuery('.sgpb-extension-notice-dont-show');if(!dontShow.length){return false;}
dontShow.bind('click',function(){var data={action:'sgpb_dont_show_extension_panel',nonce:SGPB_JS_EXTENSIONS_PARAMS.nonce};jQuery.post(ajaxurl,data,function(responce){jQuery('.sgpb-extensions-notices').remove();});});};jQuery(document).ready(function(){new SGPBExtensionsNotification();});