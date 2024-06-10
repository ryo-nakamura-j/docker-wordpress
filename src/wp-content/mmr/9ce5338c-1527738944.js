/*! jQuery Timepicker Addon - v1.4.1 - 2013-10-23
* http://trentrichardson.com/examples/timepicker
* Copyright (c) 2013 Trent Richardson; Licensed MIT */
(function($){if($.ui.timepicker=$.ui.timepicker||{},!$.ui.timepicker.version){$.extend($.ui,{timepicker:{version:"1.4.1"}});var Timepicker=function(){this.regional=[],this.regional[""]={currentText:"Now",closeText:"Done",amNames:["AM","A"],pmNames:["PM","P"],timeFormat:"HH:mm",timeSuffix:"",timeOnlyTitle:"Choose Time",timeText:"Time",hourText:"Hour",minuteText:"Minute",secondText:"Second",millisecText:"Millisecond",microsecText:"Microsecond",timezoneText:"Time Zone",isRTL:!1},this._defaults={showButtonPanel:!0,timeOnly:!1,showHour:null,showMinute:null,showSecond:null,showMillisec:null,showMicrosec:null,showTimezone:null,showTime:!0,stepHour:1,stepMinute:1,stepSecond:1,stepMillisec:1,stepMicrosec:1,hour:0,minute:0,second:0,millisec:0,microsec:0,timezone:null,hourMin:0,minuteMin:0,secondMin:0,millisecMin:0,microsecMin:0,hourMax:23,minuteMax:59,secondMax:59,millisecMax:999,microsecMax:999,minDateTime:null,maxDateTime:null,onSelect:null,hourGrid:0,minuteGrid:0,secondGrid:0,millisecGrid:0,microsecGrid:0,alwaysSetTime:!0,separator:" ",altFieldTimeOnly:!0,altTimeFormat:null,altSeparator:null,altTimeSuffix:null,pickerTimeFormat:null,pickerTimeSuffix:null,showTimepicker:!0,timezoneList:null,addSliderAccess:!1,sliderAccessArgs:null,controlType:"slider",defaultValue:null,parse:"strict"},$.extend(this._defaults,this.regional[""])};$.extend(Timepicker.prototype,{$input:null,$altInput:null,$timeObj:null,inst:null,hour_slider:null,minute_slider:null,second_slider:null,millisec_slider:null,microsec_slider:null,timezone_select:null,hour:0,minute:0,second:0,millisec:0,microsec:0,timezone:null,hourMinOriginal:null,minuteMinOriginal:null,secondMinOriginal:null,millisecMinOriginal:null,microsecMinOriginal:null,hourMaxOriginal:null,minuteMaxOriginal:null,secondMaxOriginal:null,millisecMaxOriginal:null,microsecMaxOriginal:null,ampm:"",formattedDate:"",formattedTime:"",formattedDateTime:"",timezoneList:null,units:["hour","minute","second","millisec","microsec"],support:{},control:null,setDefaults:function(e){return extendRemove(this._defaults,e||{}),this},_newInst:function($input,opts){var tp_inst=new Timepicker,inlineSettings={},fns={},overrides,i;for(var attrName in this._defaults)if(this._defaults.hasOwnProperty(attrName)){var attrValue=$input.attr("time:"+attrName);if(attrValue)try{inlineSettings[attrName]=eval(attrValue)}catch(err){inlineSettings[attrName]=attrValue}}overrides={beforeShow:function(e,t){return $.isFunction(tp_inst._defaults.evnts.beforeShow)?tp_inst._defaults.evnts.beforeShow.call($input[0],e,t,tp_inst):void 0},onChangeMonthYear:function(e,t,i){tp_inst._updateDateTime(i),$.isFunction(tp_inst._defaults.evnts.onChangeMonthYear)&&tp_inst._defaults.evnts.onChangeMonthYear.call($input[0],e,t,i,tp_inst)},onClose:function(e,t){tp_inst.timeDefined===!0&&""!==$input.val()&&tp_inst._updateDateTime(t),$.isFunction(tp_inst._defaults.evnts.onClose)&&tp_inst._defaults.evnts.onClose.call($input[0],e,t,tp_inst)}};for(i in overrides)overrides.hasOwnProperty(i)&&(fns[i]=opts[i]||null);tp_inst._defaults=$.extend({},this._defaults,inlineSettings,opts,overrides,{evnts:fns,timepicker:tp_inst}),tp_inst.amNames=$.map(tp_inst._defaults.amNames,function(e){return e.toUpperCase()}),tp_inst.pmNames=$.map(tp_inst._defaults.pmNames,function(e){return e.toUpperCase()}),tp_inst.support=detectSupport(tp_inst._defaults.timeFormat+(tp_inst._defaults.pickerTimeFormat?tp_inst._defaults.pickerTimeFormat:"")+(tp_inst._defaults.altTimeFormat?tp_inst._defaults.altTimeFormat:"")),"string"==typeof tp_inst._defaults.controlType?("slider"===tp_inst._defaults.controlType&&$.ui.slider===void 0&&(tp_inst._defaults.controlType="select"),tp_inst.control=tp_inst._controls[tp_inst._defaults.controlType]):tp_inst.control=tp_inst._defaults.controlType;var timezoneList=[-720,-660,-600,-570,-540,-480,-420,-360,-300,-270,-240,-210,-180,-120,-60,0,60,120,180,210,240,270,300,330,345,360,390,420,480,525,540,570,600,630,660,690,720,765,780,840];null!==tp_inst._defaults.timezoneList&&(timezoneList=tp_inst._defaults.timezoneList);var tzl=timezoneList.length,tzi=0,tzv=null;if(tzl>0&&"object"!=typeof timezoneList[0])for(;tzl>tzi;tzi++)tzv=timezoneList[tzi],timezoneList[tzi]={value:tzv,label:$.timepicker.timezoneOffsetString(tzv,tp_inst.support.iso8601)};return tp_inst._defaults.timezoneList=timezoneList,tp_inst.timezone=null!==tp_inst._defaults.timezone?$.timepicker.timezoneOffsetNumber(tp_inst._defaults.timezone):-1*(new Date).getTimezoneOffset(),tp_inst.hour=tp_inst._defaults.hour<tp_inst._defaults.hourMin?tp_inst._defaults.hourMin:tp_inst._defaults.hour>tp_inst._defaults.hourMax?tp_inst._defaults.hourMax:tp_inst._defaults.hour,tp_inst.minute=tp_inst._defaults.minute<tp_inst._defaults.minuteMin?tp_inst._defaults.minuteMin:tp_inst._defaults.minute>tp_inst._defaults.minuteMax?tp_inst._defaults.minuteMax:tp_inst._defaults.minute,tp_inst.second=tp_inst._defaults.second<tp_inst._defaults.secondMin?tp_inst._defaults.secondMin:tp_inst._defaults.second>tp_inst._defaults.secondMax?tp_inst._defaults.secondMax:tp_inst._defaults.second,tp_inst.millisec=tp_inst._defaults.millisec<tp_inst._defaults.millisecMin?tp_inst._defaults.millisecMin:tp_inst._defaults.millisec>tp_inst._defaults.millisecMax?tp_inst._defaults.millisecMax:tp_inst._defaults.millisec,tp_inst.microsec=tp_inst._defaults.microsec<tp_inst._defaults.microsecMin?tp_inst._defaults.microsecMin:tp_inst._defaults.microsec>tp_inst._defaults.microsecMax?tp_inst._defaults.microsecMax:tp_inst._defaults.microsec,tp_inst.ampm="",tp_inst.$input=$input,tp_inst._defaults.altField&&(tp_inst.$altInput=$(tp_inst._defaults.altField).css({cursor:"pointer"}).focus(function(){$input.trigger("focus")})),(0===tp_inst._defaults.minDate||0===tp_inst._defaults.minDateTime)&&(tp_inst._defaults.minDate=new Date),(0===tp_inst._defaults.maxDate||0===tp_inst._defaults.maxDateTime)&&(tp_inst._defaults.maxDate=new Date),void 0!==tp_inst._defaults.minDate&&tp_inst._defaults.minDate instanceof Date&&(tp_inst._defaults.minDateTime=new Date(tp_inst._defaults.minDate.getTime())),void 0!==tp_inst._defaults.minDateTime&&tp_inst._defaults.minDateTime instanceof Date&&(tp_inst._defaults.minDate=new Date(tp_inst._defaults.minDateTime.getTime())),void 0!==tp_inst._defaults.maxDate&&tp_inst._defaults.maxDate instanceof Date&&(tp_inst._defaults.maxDateTime=new Date(tp_inst._defaults.maxDate.getTime())),void 0!==tp_inst._defaults.maxDateTime&&tp_inst._defaults.maxDateTime instanceof Date&&(tp_inst._defaults.maxDate=new Date(tp_inst._defaults.maxDateTime.getTime())),tp_inst.$input.bind("focus",function(){tp_inst._onFocus()}),tp_inst},_addTimePicker:function(e){var t=this.$altInput&&this._defaults.altFieldTimeOnly?this.$input.val()+" "+this.$altInput.val():this.$input.val();this.timeDefined=this._parseTime(t),this._limitMinMaxDateTime(e,!1),this._injectTimePicker()},_parseTime:function(e,t){if(this.inst||(this.inst=$.datepicker._getInst(this.$input[0])),t||!this._defaults.timeOnly){var i=$.datepicker._get(this.inst,"dateFormat");try{var s=parseDateTimeInternal(i,this._defaults.timeFormat,e,$.datepicker._getFormatConfig(this.inst),this._defaults);if(!s.timeObj)return!1;$.extend(this,s.timeObj)}catch(a){return $.timepicker.log("Error parsing the date/time string: "+a+"\ndate/time string = "+e+"\ntimeFormat = "+this._defaults.timeFormat+"\ndateFormat = "+i),!1}return!0}var n=$.datepicker.parseTime(this._defaults.timeFormat,e,this._defaults);return n?($.extend(this,n),!0):!1},_injectTimePicker:function(){var e=this.inst.dpDiv,t=this.inst.settings,i=this,s="",a="",n=null,r={},l={},o=null,c=0,u=0;if(0===e.find("div.ui-timepicker-div").length&&t.showTimepicker){var m=' style="display:none;"',d='<div class="ui-timepicker-div'+(t.isRTL?" ui-timepicker-rtl":"")+'"><dl>'+'<dt class="ui_tpicker_time_label"'+(t.showTime?"":m)+">"+t.timeText+"</dt>"+'<dd class="ui_tpicker_time"'+(t.showTime?"":m)+"></dd>";for(c=0,u=this.units.length;u>c;c++){if(s=this.units[c],a=s.substr(0,1).toUpperCase()+s.substr(1),n=null!==t["show"+a]?t["show"+a]:this.support[s],r[s]=parseInt(t[s+"Max"]-(t[s+"Max"]-t[s+"Min"])%t["step"+a],10),l[s]=0,d+='<dt class="ui_tpicker_'+s+'_label"'+(n?"":m)+">"+t[s+"Text"]+"</dt>"+'<dd class="ui_tpicker_'+s+'"><div class="ui_tpicker_'+s+'_slider"'+(n?"":m)+"></div>",n&&t[s+"Grid"]>0){if(d+='<div style="padding-left: 1px"><table class="ui-tpicker-grid-label"><tr>',"hour"===s)for(var p=t[s+"Min"];r[s]>=p;p+=parseInt(t[s+"Grid"],10)){l[s]++;var h=$.datepicker.formatTime(this.support.ampm?"hht":"HH",{hour:p},t);d+='<td data-for="'+s+'">'+h+"</td>"}else for(var _=t[s+"Min"];r[s]>=_;_+=parseInt(t[s+"Grid"],10))l[s]++,d+='<td data-for="'+s+'">'+(10>_?"0":"")+_+"</td>";d+="</tr></table></div>"}d+="</dd>"}var f=null!==t.showTimezone?t.showTimezone:this.support.timezone;d+='<dt class="ui_tpicker_timezone_label"'+(f?"":m)+">"+t.timezoneText+"</dt>",d+='<dd class="ui_tpicker_timezone" '+(f?"":m)+"></dd>",d+="</dl></div>";var g=$(d);for(t.timeOnly===!0&&(g.prepend('<div class="ui-widget-header ui-helper-clearfix ui-corner-all"><div class="ui-datepicker-title">'+t.timeOnlyTitle+"</div>"+"</div>"),e.find(".ui-datepicker-header, .ui-datepicker-calendar").hide()),c=0,u=i.units.length;u>c;c++)s=i.units[c],a=s.substr(0,1).toUpperCase()+s.substr(1),n=null!==t["show"+a]?t["show"+a]:this.support[s],i[s+"_slider"]=i.control.create(i,g.find(".ui_tpicker_"+s+"_slider"),s,i[s],t[s+"Min"],r[s],t["step"+a]),n&&t[s+"Grid"]>0&&(o=100*l[s]*t[s+"Grid"]/(r[s]-t[s+"Min"]),g.find(".ui_tpicker_"+s+" table").css({width:o+"%",marginLeft:t.isRTL?"0":o/(-2*l[s])+"%",marginRight:t.isRTL?o/(-2*l[s])+"%":"0",borderCollapse:"collapse"}).find("td").click(function(){var e=$(this),t=e.html(),a=parseInt(t.replace(/[^0-9]/g),10),n=t.replace(/[^apm]/gi),r=e.data("for");"hour"===r&&(-1!==n.indexOf("p")&&12>a?a+=12:-1!==n.indexOf("a")&&12===a&&(a=0)),i.control.value(i,i[r+"_slider"],s,a),i._onTimeChange(),i._onSelectHandler()}).css({cursor:"pointer",width:100/l[s]+"%",textAlign:"center",overflow:"hidden"}));if(this.timezone_select=g.find(".ui_tpicker_timezone").append("<select></select>").find("select"),$.fn.append.apply(this.timezone_select,$.map(t.timezoneList,function(e){return $("<option />").val("object"==typeof e?e.value:e).text("object"==typeof e?e.label:e)})),this.timezone!==void 0&&null!==this.timezone&&""!==this.timezone){var M=-1*new Date(this.inst.selectedYear,this.inst.selectedMonth,this.inst.selectedDay,12).getTimezoneOffset();M===this.timezone?selectLocalTimezone(i):this.timezone_select.val(this.timezone)}else this.hour!==void 0&&null!==this.hour&&""!==this.hour?this.timezone_select.val(t.timezone):selectLocalTimezone(i);this.timezone_select.change(function(){i._onTimeChange(),i._onSelectHandler()});var v=e.find(".ui-datepicker-buttonpane");if(v.length?v.before(g):e.append(g),this.$timeObj=g.find(".ui_tpicker_time"),null!==this.inst){var k=this.timeDefined;this._onTimeChange(),this.timeDefined=k}if(this._defaults.addSliderAccess){var T=this._defaults.sliderAccessArgs,D=this._defaults.isRTL;T.isRTL=D,setTimeout(function(){if(0===g.find(".ui-slider-access").length){g.find(".ui-slider:visible").sliderAccess(T);var e=g.find(".ui-slider-access:eq(0)").outerWidth(!0);e&&g.find("table:visible").each(function(){var t=$(this),i=t.outerWidth(),s=(""+t.css(D?"marginRight":"marginLeft")).replace("%",""),a=i-e,n=s*a/i+"%",r={width:a,marginRight:0,marginLeft:0};r[D?"marginRight":"marginLeft"]=n,t.css(r)})}},10)}i._limitMinMaxDateTime(this.inst,!0)}},_limitMinMaxDateTime:function(e,t){var i=this._defaults,s=new Date(e.selectedYear,e.selectedMonth,e.selectedDay);if(this._defaults.showTimepicker){if(null!==$.datepicker._get(e,"minDateTime")&&void 0!==$.datepicker._get(e,"minDateTime")&&s){var a=$.datepicker._get(e,"minDateTime"),n=new Date(a.getFullYear(),a.getMonth(),a.getDate(),0,0,0,0);(null===this.hourMinOriginal||null===this.minuteMinOriginal||null===this.secondMinOriginal||null===this.millisecMinOriginal||null===this.microsecMinOriginal)&&(this.hourMinOriginal=i.hourMin,this.minuteMinOriginal=i.minuteMin,this.secondMinOriginal=i.secondMin,this.millisecMinOriginal=i.millisecMin,this.microsecMinOriginal=i.microsecMin),e.settings.timeOnly||n.getTime()===s.getTime()?(this._defaults.hourMin=a.getHours(),this.hour<=this._defaults.hourMin?(this.hour=this._defaults.hourMin,this._defaults.minuteMin=a.getMinutes(),this.minute<=this._defaults.minuteMin?(this.minute=this._defaults.minuteMin,this._defaults.secondMin=a.getSeconds(),this.second<=this._defaults.secondMin?(this.second=this._defaults.secondMin,this._defaults.millisecMin=a.getMilliseconds(),this.millisec<=this._defaults.millisecMin?(this.millisec=this._defaults.millisecMin,this._defaults.microsecMin=a.getMicroseconds()):(this.microsec<this._defaults.microsecMin&&(this.microsec=this._defaults.microsecMin),this._defaults.microsecMin=this.microsecMinOriginal)):(this._defaults.millisecMin=this.millisecMinOriginal,this._defaults.microsecMin=this.microsecMinOriginal)):(this._defaults.secondMin=this.secondMinOriginal,this._defaults.millisecMin=this.millisecMinOriginal,this._defaults.microsecMin=this.microsecMinOriginal)):(this._defaults.minuteMin=this.minuteMinOriginal,this._defaults.secondMin=this.secondMinOriginal,this._defaults.millisecMin=this.millisecMinOriginal,this._defaults.microsecMin=this.microsecMinOriginal)):(this._defaults.hourMin=this.hourMinOriginal,this._defaults.minuteMin=this.minuteMinOriginal,this._defaults.secondMin=this.secondMinOriginal,this._defaults.millisecMin=this.millisecMinOriginal,this._defaults.microsecMin=this.microsecMinOriginal)}if(null!==$.datepicker._get(e,"maxDateTime")&&void 0!==$.datepicker._get(e,"maxDateTime")&&s){var r=$.datepicker._get(e,"maxDateTime"),l=new Date(r.getFullYear(),r.getMonth(),r.getDate(),0,0,0,0);(null===this.hourMaxOriginal||null===this.minuteMaxOriginal||null===this.secondMaxOriginal||null===this.millisecMaxOriginal)&&(this.hourMaxOriginal=i.hourMax,this.minuteMaxOriginal=i.minuteMax,this.secondMaxOriginal=i.secondMax,this.millisecMaxOriginal=i.millisecMax,this.microsecMaxOriginal=i.microsecMax),e.settings.timeOnly||l.getTime()===s.getTime()?(this._defaults.hourMax=r.getHours(),this.hour>=this._defaults.hourMax?(this.hour=this._defaults.hourMax,this._defaults.minuteMax=r.getMinutes(),this.minute>=this._defaults.minuteMax?(this.minute=this._defaults.minuteMax,this._defaults.secondMax=r.getSeconds(),this.second>=this._defaults.secondMax?(this.second=this._defaults.secondMax,this._defaults.millisecMax=r.getMilliseconds(),this.millisec>=this._defaults.millisecMax?(this.millisec=this._defaults.millisecMax,this._defaults.microsecMax=r.getMicroseconds()):(this.microsec>this._defaults.microsecMax&&(this.microsec=this._defaults.microsecMax),this._defaults.microsecMax=this.microsecMaxOriginal)):(this._defaults.millisecMax=this.millisecMaxOriginal,this._defaults.microsecMax=this.microsecMaxOriginal)):(this._defaults.secondMax=this.secondMaxOriginal,this._defaults.millisecMax=this.millisecMaxOriginal,this._defaults.microsecMax=this.microsecMaxOriginal)):(this._defaults.minuteMax=this.minuteMaxOriginal,this._defaults.secondMax=this.secondMaxOriginal,this._defaults.millisecMax=this.millisecMaxOriginal,this._defaults.microsecMax=this.microsecMaxOriginal)):(this._defaults.hourMax=this.hourMaxOriginal,this._defaults.minuteMax=this.minuteMaxOriginal,this._defaults.secondMax=this.secondMaxOriginal,this._defaults.millisecMax=this.millisecMaxOriginal,this._defaults.microsecMax=this.microsecMaxOriginal)}if(void 0!==t&&t===!0){var o=parseInt(this._defaults.hourMax-(this._defaults.hourMax-this._defaults.hourMin)%this._defaults.stepHour,10),c=parseInt(this._defaults.minuteMax-(this._defaults.minuteMax-this._defaults.minuteMin)%this._defaults.stepMinute,10),u=parseInt(this._defaults.secondMax-(this._defaults.secondMax-this._defaults.secondMin)%this._defaults.stepSecond,10),m=parseInt(this._defaults.millisecMax-(this._defaults.millisecMax-this._defaults.millisecMin)%this._defaults.stepMillisec,10),d=parseInt(this._defaults.microsecMax-(this._defaults.microsecMax-this._defaults.microsecMin)%this._defaults.stepMicrosec,10);this.hour_slider&&(this.control.options(this,this.hour_slider,"hour",{min:this._defaults.hourMin,max:o}),this.control.value(this,this.hour_slider,"hour",this.hour-this.hour%this._defaults.stepHour)),this.minute_slider&&(this.control.options(this,this.minute_slider,"minute",{min:this._defaults.minuteMin,max:c}),this.control.value(this,this.minute_slider,"minute",this.minute-this.minute%this._defaults.stepMinute)),this.second_slider&&(this.control.options(this,this.second_slider,"second",{min:this._defaults.secondMin,max:u}),this.control.value(this,this.second_slider,"second",this.second-this.second%this._defaults.stepSecond)),this.millisec_slider&&(this.control.options(this,this.millisec_slider,"millisec",{min:this._defaults.millisecMin,max:m}),this.control.value(this,this.millisec_slider,"millisec",this.millisec-this.millisec%this._defaults.stepMillisec)),this.microsec_slider&&(this.control.options(this,this.microsec_slider,"microsec",{min:this._defaults.microsecMin,max:d}),this.control.value(this,this.microsec_slider,"microsec",this.microsec-this.microsec%this._defaults.stepMicrosec))}}},_onTimeChange:function(){if(this._defaults.showTimepicker){var e=this.hour_slider?this.control.value(this,this.hour_slider,"hour"):!1,t=this.minute_slider?this.control.value(this,this.minute_slider,"minute"):!1,i=this.second_slider?this.control.value(this,this.second_slider,"second"):!1,s=this.millisec_slider?this.control.value(this,this.millisec_slider,"millisec"):!1,a=this.microsec_slider?this.control.value(this,this.microsec_slider,"microsec"):!1,n=this.timezone_select?this.timezone_select.val():!1,r=this._defaults,l=r.pickerTimeFormat||r.timeFormat,o=r.pickerTimeSuffix||r.timeSuffix;"object"==typeof e&&(e=!1),"object"==typeof t&&(t=!1),"object"==typeof i&&(i=!1),"object"==typeof s&&(s=!1),"object"==typeof a&&(a=!1),"object"==typeof n&&(n=!1),e!==!1&&(e=parseInt(e,10)),t!==!1&&(t=parseInt(t,10)),i!==!1&&(i=parseInt(i,10)),s!==!1&&(s=parseInt(s,10)),a!==!1&&(a=parseInt(a,10)),n!==!1&&(n=parseInt(n,10));var c=r[12>e?"amNames":"pmNames"][0],u=e!==this.hour||t!==this.minute||i!==this.second||s!==this.millisec||a!==this.microsec||this.ampm.length>0&&12>e!=(-1!==$.inArray(this.ampm.toUpperCase(),this.amNames))||null!==this.timezone&&n!==this.timezone;u&&(e!==!1&&(this.hour=e),t!==!1&&(this.minute=t),i!==!1&&(this.second=i),s!==!1&&(this.millisec=s),a!==!1&&(this.microsec=a),n!==!1&&(this.timezone=n),this.inst||(this.inst=$.datepicker._getInst(this.$input[0])),this._limitMinMaxDateTime(this.inst,!0)),this.support.ampm&&(this.ampm=c),this.formattedTime=$.datepicker.formatTime(r.timeFormat,this,r),this.$timeObj&&(l===r.timeFormat?this.$timeObj.text(this.formattedTime+o):this.$timeObj.text($.datepicker.formatTime(l,this,r)+o)),this.timeDefined=!0,u&&this._updateDateTime()}},_onSelectHandler:function(){var e=this._defaults.onSelect||this.inst.settings.onSelect,t=this.$input?this.$input[0]:null;e&&t&&e.apply(t,[this.formattedDateTime,this])},_updateDateTime:function(e){e=this.inst||e;var t=e.currentYear>0?new Date(e.currentYear,e.currentMonth,e.currentDay):new Date(e.selectedYear,e.selectedMonth,e.selectedDay),i=$.datepicker._daylightSavingAdjust(t),s=$.datepicker._get(e,"dateFormat"),a=$.datepicker._getFormatConfig(e),n=null!==i&&this.timeDefined;this.formattedDate=$.datepicker.formatDate(s,null===i?new Date:i,a);var r=this.formattedDate;if(""===e.lastVal&&(e.currentYear=e.selectedYear,e.currentMonth=e.selectedMonth,e.currentDay=e.selectedDay),this._defaults.timeOnly===!0?r=this.formattedTime:this._defaults.timeOnly!==!0&&(this._defaults.alwaysSetTime||n)&&(r+=this._defaults.separator+this.formattedTime+this._defaults.timeSuffix),this.formattedDateTime=r,this._defaults.showTimepicker)if(this.$altInput&&this._defaults.timeOnly===!1&&this._defaults.altFieldTimeOnly===!0)this.$altInput.val(this.formattedTime),this.$input.val(this.formattedDate);else if(this.$altInput){this.$input.val(r);var l="",o=this._defaults.altSeparator?this._defaults.altSeparator:this._defaults.separator,c=this._defaults.altTimeSuffix?this._defaults.altTimeSuffix:this._defaults.timeSuffix;this._defaults.timeOnly||(l=this._defaults.altFormat?$.datepicker.formatDate(this._defaults.altFormat,null===i?new Date:i,a):this.formattedDate,l&&(l+=o)),l+=this._defaults.altTimeFormat?$.datepicker.formatTime(this._defaults.altTimeFormat,this,this._defaults)+c:this.formattedTime+c,this.$altInput.val(l)}else this.$input.val(r);else this.$input.val(this.formattedDate);this.$input.trigger("change")},_onFocus:function(){if(!this.$input.val()&&this._defaults.defaultValue){this.$input.val(this._defaults.defaultValue);var e=$.datepicker._getInst(this.$input.get(0)),t=$.datepicker._get(e,"timepicker");if(t&&t._defaults.timeOnly&&e.input.val()!==e.lastVal)try{$.datepicker._updateDatepicker(e)}catch(i){$.timepicker.log(i)}}},_controls:{slider:{create:function(e,t,i,s,a,n,r){var l=e._defaults.isRTL;return t.prop("slide",null).slider({orientation:"horizontal",value:l?-1*s:s,min:l?-1*n:a,max:l?-1*a:n,step:r,slide:function(t,s){e.control.value(e,$(this),i,l?-1*s.value:s.value),e._onTimeChange()},stop:function(){e._onSelectHandler()}})},options:function(e,t,i,s,a){if(e._defaults.isRTL){if("string"==typeof s)return"min"===s||"max"===s?void 0!==a?t.slider(s,-1*a):Math.abs(t.slider(s)):t.slider(s);var n=s.min,r=s.max;return s.min=s.max=null,void 0!==n&&(s.max=-1*n),void 0!==r&&(s.min=-1*r),t.slider(s)}return"string"==typeof s&&void 0!==a?t.slider(s,a):t.slider(s)},value:function(e,t,i,s){return e._defaults.isRTL?void 0!==s?t.slider("value",-1*s):Math.abs(t.slider("value")):void 0!==s?t.slider("value",s):t.slider("value")}},select:{create:function(e,t,i,s,a,n,r){for(var l='<select class="ui-timepicker-select" data-unit="'+i+'" data-min="'+a+'" data-max="'+n+'" data-step="'+r+'">',o=e._defaults.pickerTimeFormat||e._defaults.timeFormat,c=a;n>=c;c+=r)l+='<option value="'+c+'"'+(c===s?" selected":"")+">",l+="hour"===i?$.datepicker.formatTime($.trim(o.replace(/[^ht ]/gi,"")),{hour:c},e._defaults):"millisec"===i||"microsec"===i||c>=10?c:"0"+(""+c),l+="</option>";return l+="</select>",t.children("select").remove(),$(l).appendTo(t).change(function(){e._onTimeChange(),e._onSelectHandler()}),t},options:function(e,t,i,s,a){var n={},r=t.children("select");if("string"==typeof s){if(void 0===a)return r.data(s);n[s]=a}else n=s;return e.control.create(e,t,r.data("unit"),r.val(),n.min||r.data("min"),n.max||r.data("max"),n.step||r.data("step"))},value:function(e,t,i,s){var a=t.children("select");return void 0!==s?a.val(s):a.val()}}}}),$.fn.extend({timepicker:function(e){e=e||{};var t=Array.prototype.slice.call(arguments);return"object"==typeof e&&(t[0]=$.extend(e,{timeOnly:!0})),$(this).each(function(){$.fn.datetimepicker.apply($(this),t)})},datetimepicker:function(e){e=e||{};var t=arguments;return"string"==typeof e?"getDate"===e?$.fn.datepicker.apply($(this[0]),t):this.each(function(){var e=$(this);e.datepicker.apply(e,t)}):this.each(function(){var t=$(this);t.datepicker($.timepicker._newInst(t,e)._defaults)})}}),$.datepicker.parseDateTime=function(e,t,i,s,a){var n=parseDateTimeInternal(e,t,i,s,a);if(n.timeObj){var r=n.timeObj;n.date.setHours(r.hour,r.minute,r.second,r.millisec),n.date.setMicroseconds(r.microsec)}return n.date},$.datepicker.parseTime=function(e,t,i){var s=extendRemove(extendRemove({},$.timepicker._defaults),i||{});-1!==e.replace(/\'.*?\'/g,"").indexOf("Z");var a=function(e,t,i){var s,a=function(e,t){var i=[];return e&&$.merge(i,e),t&&$.merge(i,t),i=$.map(i,function(e){return e.replace(/[.*+?|()\[\]{}\\]/g,"\\$&")}),"("+i.join("|")+")?"},n=function(e){var t=e.toLowerCase().match(/(h{1,2}|m{1,2}|s{1,2}|l{1}|c{1}|t{1,2}|z|'.*?')/g),i={h:-1,m:-1,s:-1,l:-1,c:-1,t:-1,z:-1};if(t)for(var s=0;t.length>s;s++)-1===i[(""+t[s]).charAt(0)]&&(i[(""+t[s]).charAt(0)]=s+1);return i},r="^"+(""+e).replace(/([hH]{1,2}|mm?|ss?|[tT]{1,2}|[zZ]|[lc]|'.*?')/g,function(e){var t=e.length;switch(e.charAt(0).toLowerCase()){case"h":return 1===t?"(\\d?\\d)":"(\\d{"+t+"})";case"m":return 1===t?"(\\d?\\d)":"(\\d{"+t+"})";case"s":return 1===t?"(\\d?\\d)":"(\\d{"+t+"})";case"l":return"(\\d?\\d?\\d)";case"c":return"(\\d?\\d?\\d)";case"z":return"(z|[-+]\\d\\d:?\\d\\d|\\S+)?";case"t":return a(i.amNames,i.pmNames);default:return"("+e.replace(/\'/g,"").replace(/(\.|\$|\^|\\|\/|\(|\)|\[|\]|\?|\+|\*)/g,function(e){return"\\"+e})+")?"}}).replace(/\s/g,"\\s?")+i.timeSuffix+"$",l=n(e),o="";s=t.match(RegExp(r,"i"));var c={hour:0,minute:0,second:0,millisec:0,microsec:0};return s?(-1!==l.t&&(void 0===s[l.t]||0===s[l.t].length?(o="",c.ampm=""):(o=-1!==$.inArray(s[l.t].toUpperCase(),i.amNames)?"AM":"PM",c.ampm=i["AM"===o?"amNames":"pmNames"][0])),-1!==l.h&&(c.hour="AM"===o&&"12"===s[l.h]?0:"PM"===o&&"12"!==s[l.h]?parseInt(s[l.h],10)+12:Number(s[l.h])),-1!==l.m&&(c.minute=Number(s[l.m])),-1!==l.s&&(c.second=Number(s[l.s])),-1!==l.l&&(c.millisec=Number(s[l.l])),-1!==l.c&&(c.microsec=Number(s[l.c])),-1!==l.z&&void 0!==s[l.z]&&(c.timezone=$.timepicker.timezoneOffsetNumber(s[l.z])),c):!1},n=function(e,t,i){try{var s=new Date("2012-01-01 "+t);if(isNaN(s.getTime())&&(s=new Date("2012-01-01T"+t),isNaN(s.getTime())&&(s=new Date("01/01/2012 "+t),isNaN(s.getTime()))))throw"Unable to parse time with native Date: "+t;return{hour:s.getHours(),minute:s.getMinutes(),second:s.getSeconds(),millisec:s.getMilliseconds(),microsec:s.getMicroseconds(),timezone:-1*s.getTimezoneOffset()}}catch(n){try{return a(e,t,i)}catch(r){$.timepicker.log("Unable to parse \ntimeString: "+t+"\ntimeFormat: "+e)}}return!1};return"function"==typeof s.parse?s.parse(e,t,s):"loose"===s.parse?n(e,t,s):a(e,t,s)},$.datepicker.formatTime=function(e,t,i){i=i||{},i=$.extend({},$.timepicker._defaults,i),t=$.extend({hour:0,minute:0,second:0,millisec:0,microsec:0,timezone:null},t);var s=e,a=i.amNames[0],n=parseInt(t.hour,10);return n>11&&(a=i.pmNames[0]),s=s.replace(/(?:HH?|hh?|mm?|ss?|[tT]{1,2}|[zZ]|[lc]|'.*?')/g,function(e){switch(e){case"HH":return("0"+n).slice(-2);case"H":return n;case"hh":return("0"+convert24to12(n)).slice(-2);case"h":return convert24to12(n);case"mm":return("0"+t.minute).slice(-2);case"m":return t.minute;case"ss":return("0"+t.second).slice(-2);case"s":return t.second;case"l":return("00"+t.millisec).slice(-3);case"c":return("00"+t.microsec).slice(-3);case"z":return $.timepicker.timezoneOffsetString(null===t.timezone?i.timezone:t.timezone,!1);case"Z":return $.timepicker.timezoneOffsetString(null===t.timezone?i.timezone:t.timezone,!0);case"T":return a.charAt(0).toUpperCase();case"TT":return a.toUpperCase();case"t":return a.charAt(0).toLowerCase();case"tt":return a.toLowerCase();default:return e.replace(/'/g,"")}})},$.datepicker._base_selectDate=$.datepicker._selectDate,$.datepicker._selectDate=function(e,t){var i=this._getInst($(e)[0]),s=this._get(i,"timepicker");s?(s._limitMinMaxDateTime(i,!0),i.inline=i.stay_open=!0,this._base_selectDate(e,t),i.inline=i.stay_open=!1,this._notifyChange(i),this._updateDatepicker(i)):this._base_selectDate(e,t)},$.datepicker._base_updateDatepicker=$.datepicker._updateDatepicker,$.datepicker._updateDatepicker=function(e){var t=e.input[0];if(!($.datepicker._curInst&&$.datepicker._curInst!==e&&$.datepicker._datepickerShowing&&$.datepicker._lastInput!==t||"boolean"==typeof e.stay_open&&e.stay_open!==!1)){this._base_updateDatepicker(e);var i=this._get(e,"timepicker");i&&i._addTimePicker(e)}},$.datepicker._base_doKeyPress=$.datepicker._doKeyPress,$.datepicker._doKeyPress=function(e){var t=$.datepicker._getInst(e.target),i=$.datepicker._get(t,"timepicker");if(i&&$.datepicker._get(t,"constrainInput")){var s=i.support.ampm,a=null!==i._defaults.showTimezone?i._defaults.showTimezone:i.support.timezone,n=$.datepicker._possibleChars($.datepicker._get(t,"dateFormat")),r=(""+i._defaults.timeFormat).replace(/[hms]/g,"").replace(/TT/g,s?"APM":"").replace(/Tt/g,s?"AaPpMm":"").replace(/tT/g,s?"AaPpMm":"").replace(/T/g,s?"AP":"").replace(/tt/g,s?"apm":"").replace(/t/g,s?"ap":"")+" "+i._defaults.separator+i._defaults.timeSuffix+(a?i._defaults.timezoneList.join(""):"")+i._defaults.amNames.join("")+i._defaults.pmNames.join("")+n,l=String.fromCharCode(void 0===e.charCode?e.keyCode:e.charCode);return e.ctrlKey||" ">l||!n||r.indexOf(l)>-1}return $.datepicker._base_doKeyPress(e)},$.datepicker._base_updateAlternate=$.datepicker._updateAlternate,$.datepicker._updateAlternate=function(e){var t=this._get(e,"timepicker");if(t){var i=t._defaults.altField;if(i){var s=(t._defaults.altFormat||t._defaults.dateFormat,this._getDate(e)),a=$.datepicker._getFormatConfig(e),n="",r=t._defaults.altSeparator?t._defaults.altSeparator:t._defaults.separator,l=t._defaults.altTimeSuffix?t._defaults.altTimeSuffix:t._defaults.timeSuffix,o=null!==t._defaults.altTimeFormat?t._defaults.altTimeFormat:t._defaults.timeFormat;n+=$.datepicker.formatTime(o,t,t._defaults)+l,t._defaults.timeOnly||t._defaults.altFieldTimeOnly||null===s||(n=t._defaults.altFormat?$.datepicker.formatDate(t._defaults.altFormat,s,a)+r+n:t.formattedDate+r+n),$(i).val(n)}}else $.datepicker._base_updateAlternate(e)},$.datepicker._base_doKeyUp=$.datepicker._doKeyUp,$.datepicker._doKeyUp=function(e){var t=$.datepicker._getInst(e.target),i=$.datepicker._get(t,"timepicker");if(i&&i._defaults.timeOnly&&t.input.val()!==t.lastVal)try{$.datepicker._updateDatepicker(t)}catch(s){$.timepicker.log(s)}return $.datepicker._base_doKeyUp(e)},$.datepicker._base_gotoToday=$.datepicker._gotoToday,$.datepicker._gotoToday=function(e){var t=this._getInst($(e)[0]),i=t.dpDiv;this._base_gotoToday(e);var s=this._get(t,"timepicker");selectLocalTimezone(s);var a=new Date;this._setTime(t,a),$(".ui-datepicker-today",i).click()},$.datepicker._disableTimepickerDatepicker=function(e){var t=this._getInst(e);if(t){var i=this._get(t,"timepicker");$(e).datepicker("getDate"),i&&(t.settings.showTimepicker=!1,i._defaults.showTimepicker=!1,i._updateDateTime(t))}},$.datepicker._enableTimepickerDatepicker=function(e){var t=this._getInst(e);if(t){var i=this._get(t,"timepicker");$(e).datepicker("getDate"),i&&(t.settings.showTimepicker=!0,i._defaults.showTimepicker=!0,i._addTimePicker(t),i._updateDateTime(t))}},$.datepicker._setTime=function(e,t){var i=this._get(e,"timepicker");if(i){var s=i._defaults;i.hour=t?t.getHours():s.hour,i.minute=t?t.getMinutes():s.minute,i.second=t?t.getSeconds():s.second,i.millisec=t?t.getMilliseconds():s.millisec,i.microsec=t?t.getMicroseconds():s.microsec,i._limitMinMaxDateTime(e,!0),i._onTimeChange(),i._updateDateTime(e)}},$.datepicker._setTimeDatepicker=function(e,t,i){var s=this._getInst(e);if(s){var a=this._get(s,"timepicker");if(a){this._setDateFromField(s);var n;t&&("string"==typeof t?(a._parseTime(t,i),n=new Date,n.setHours(a.hour,a.minute,a.second,a.millisec),n.setMicroseconds(a.microsec)):(n=new Date(t.getTime()),n.setMicroseconds(t.getMicroseconds())),"Invalid Date"==""+n&&(n=void 0),this._setTime(s,n))}}},$.datepicker._base_setDateDatepicker=$.datepicker._setDateDatepicker,$.datepicker._setDateDatepicker=function(e,t){var i=this._getInst(e);if(i){"string"==typeof t&&(t=new Date(t),t.getTime()||$.timepicker.log("Error creating Date object from string."));var s,a=this._get(i,"timepicker");t instanceof Date?(s=new Date(t.getTime()),s.setMicroseconds(t.getMicroseconds())):s=t,a&&(a.support.timezone||null!==a._defaults.timezone||(a.timezone=-1*s.getTimezoneOffset()),t=$.timepicker.timezoneAdjust(t,a.timezone),s=$.timepicker.timezoneAdjust(s,a.timezone)),this._updateDatepicker(i),this._base_setDateDatepicker.apply(this,arguments),this._setTimeDatepicker(e,s,!0)}},$.datepicker._base_getDateDatepicker=$.datepicker._getDateDatepicker,$.datepicker._getDateDatepicker=function(e,t){var i=this._getInst(e);if(i){var s=this._get(i,"timepicker");if(s){void 0===i.lastVal&&this._setDateFromField(i,t);var a=this._getDate(i);return a&&s._parseTime($(e).val(),s.timeOnly)&&(a.setHours(s.hour,s.minute,s.second,s.millisec),a.setMicroseconds(s.microsec),null!=s.timezone&&(s.support.timezone||null!==s._defaults.timezone||(s.timezone=-1*a.getTimezoneOffset()),a=$.timepicker.timezoneAdjust(a,s.timezone))),a
}return this._base_getDateDatepicker(e,t)}},$.datepicker._base_parseDate=$.datepicker.parseDate,$.datepicker.parseDate=function(e,t,i){var s;try{s=this._base_parseDate(e,t,i)}catch(a){if(!(a.indexOf(":")>=0))throw a;s=this._base_parseDate(e,t.substring(0,t.length-(a.length-a.indexOf(":")-2)),i),$.timepicker.log("Error parsing the date string: "+a+"\ndate string = "+t+"\ndate format = "+e)}return s},$.datepicker._base_formatDate=$.datepicker._formatDate,$.datepicker._formatDate=function(e){var t=this._get(e,"timepicker");return t?(t._updateDateTime(e),t.$input.val()):this._base_formatDate(e)},$.datepicker._base_optionDatepicker=$.datepicker._optionDatepicker,$.datepicker._optionDatepicker=function(e,t,i){var s,a=this._getInst(e);if(!a)return null;var n=this._get(a,"timepicker");if(n){var r,l=null,o=null,c=null,u=n._defaults.evnts,m={};if("string"==typeof t){if("minDate"===t||"minDateTime"===t)l=i;else if("maxDate"===t||"maxDateTime"===t)o=i;else if("onSelect"===t)c=i;else if(u.hasOwnProperty(t)){if(i===void 0)return u[t];m[t]=i,s={}}}else if("object"==typeof t){t.minDate?l=t.minDate:t.minDateTime?l=t.minDateTime:t.maxDate?o=t.maxDate:t.maxDateTime&&(o=t.maxDateTime);for(r in u)u.hasOwnProperty(r)&&t[r]&&(m[r]=t[r])}for(r in m)m.hasOwnProperty(r)&&(u[r]=m[r],s||(s=$.extend({},t)),delete s[r]);if(s&&isEmptyObject(s))return;l?(l=0===l?new Date:new Date(l),n._defaults.minDate=l,n._defaults.minDateTime=l):o?(o=0===o?new Date:new Date(o),n._defaults.maxDate=o,n._defaults.maxDateTime=o):c&&(n._defaults.onSelect=c)}return void 0===i?this._base_optionDatepicker.call($.datepicker,e,t):this._base_optionDatepicker.call($.datepicker,e,s||t,i)};var isEmptyObject=function(e){var t;for(t in e)if(e.hasOwnProperty(t))return!1;return!0},extendRemove=function(e,t){$.extend(e,t);for(var i in t)(null===t[i]||void 0===t[i])&&(e[i]=t[i]);return e},detectSupport=function(e){var t=e.replace(/'.*?'/g,"").toLowerCase(),i=function(e,t){return-1!==e.indexOf(t)?!0:!1};return{hour:i(t,"h"),minute:i(t,"m"),second:i(t,"s"),millisec:i(t,"l"),microsec:i(t,"c"),timezone:i(t,"z"),ampm:i(t,"t")&&i(e,"h"),iso8601:i(e,"Z")}},convert24to12=function(e){return e%=12,0===e&&(e=12),e+""},computeEffectiveSetting=function(e,t){return e&&e[t]?e[t]:$.timepicker._defaults[t]},splitDateTime=function(e,t){var i=computeEffectiveSetting(t,"separator"),s=computeEffectiveSetting(t,"timeFormat"),a=s.split(i),n=a.length,r=e.split(i),l=r.length;return l>1?{dateString:r.splice(0,l-n).join(i),timeString:r.splice(0,n).join(i)}:{dateString:e,timeString:""}},parseDateTimeInternal=function(e,t,i,s,a){var n,r,l;if(r=splitDateTime(i,a),n=$.datepicker._base_parseDate(e,r.dateString,s),""===r.timeString)return{date:n};if(l=$.datepicker.parseTime(t,r.timeString,a),!l)throw"Wrong time format";return{date:n,timeObj:l}},selectLocalTimezone=function(e,t){if(e&&e.timezone_select){var i=t||new Date;e.timezone_select.val(-i.getTimezoneOffset())}};$.timepicker=new Timepicker,$.timepicker.timezoneOffsetString=function(e,t){if(isNaN(e)||e>840||-720>e)return e;var i=e,s=i%60,a=(i-s)/60,n=t?":":"",r=(i>=0?"+":"-")+("0"+Math.abs(a)).slice(-2)+n+("0"+Math.abs(s)).slice(-2);return"+00:00"===r?"Z":r},$.timepicker.timezoneOffsetNumber=function(e){var t=(""+e).replace(":","");return"Z"===t.toUpperCase()?0:/^(\-|\+)\d{4}$/.test(t)?("-"===t.substr(0,1)?-1:1)*(60*parseInt(t.substr(1,2),10)+parseInt(t.substr(3,2),10)):e},$.timepicker.timezoneAdjust=function(e,t){var i=$.timepicker.timezoneOffsetNumber(t);return isNaN(i)||e.setMinutes(e.getMinutes()+-e.getTimezoneOffset()-i),e},$.timepicker.timeRange=function(e,t,i){return $.timepicker.handleRange("timepicker",e,t,i)},$.timepicker.datetimeRange=function(e,t,i){$.timepicker.handleRange("datetimepicker",e,t,i)},$.timepicker.dateRange=function(e,t,i){$.timepicker.handleRange("datepicker",e,t,i)},$.timepicker.handleRange=function(e,t,i,s){function a(a,n){var r=t[e]("getDate"),l=i[e]("getDate"),o=a[e]("getDate");if(null!==r){var c=new Date(r.getTime()),u=new Date(r.getTime());c.setMilliseconds(c.getMilliseconds()+s.minInterval),u.setMilliseconds(u.getMilliseconds()+s.maxInterval),s.minInterval>0&&c>l?i[e]("setDate",c):s.maxInterval>0&&l>u?i[e]("setDate",u):r>l&&n[e]("setDate",o)}}function n(t,i,a){if(t.val()){var n=t[e].call(t,"getDate");null!==n&&s.minInterval>0&&("minDate"===a&&n.setMilliseconds(n.getMilliseconds()+s.minInterval),"maxDate"===a&&n.setMilliseconds(n.getMilliseconds()-s.minInterval)),n.getTime&&i[e].call(i,"option",a,n)}}return s=$.extend({},{minInterval:0,maxInterval:0,start:{},end:{}},s),$.fn[e].call(t,$.extend({onClose:function(){a($(this),i)},onSelect:function(){n($(this),i,"minDate")}},s,s.start)),$.fn[e].call(i,$.extend({onClose:function(){a($(this),t)},onSelect:function(){n($(this),t,"maxDate")}},s,s.end)),a(t,i),n(t,i,"minDate"),n(i,t,"maxDate"),$([t.get(0),i.get(0)])},$.timepicker.log=function(e){window.console&&window.console.log(e)},$.timepicker._util={_extendRemove:extendRemove,_isEmptyObject:isEmptyObject,_convert24to12:convert24to12,_detectSupport:detectSupport,_selectLocalTimezone:selectLocalTimezone,_computeEffectiveSetting:computeEffectiveSetting,_splitDateTime:splitDateTime,_parseDateTimeInternal:parseDateTimeInternal},Date.prototype.getMicroseconds||(Date.prototype.microseconds=0,Date.prototype.getMicroseconds=function(){return this.microseconds},Date.prototype.setMicroseconds=function(e){return this.setMilliseconds(this.getMilliseconds()+Math.floor(e/1e3)),this.microseconds=e%1e3,this}),$.timepicker.version="1.4.1"}})(jQuery);;
/* English/UK initialisation for the jQuery UI date picker plugin. */
/* Written by Stuart. */
jQuery(function($){
  $.datepicker.regional['en-GB'] = {
    closeText: 'Done',
    prevText: 'Prev',
    nextText: 'Next',
    currentText: 'Today',
    monthNames: ['January','February','March','April','May','June',
    'July','August','September','October','November','December'],
    monthNamesShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
    'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    dayNames: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
    dayNamesShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
    dayNamesMin: ['Su','Mo','Tu','We','Th','Fr','Sa'],
    weekHeader: 'Wk',
    dateFormat: 'yy/mm/dd',
    firstDay: 1,
    isRTL: false,
    showMonthAfterYear: false,
    yearSuffix: ''};
  $.datepicker.setDefaults($.datepicker.regional['en-GB']);
});
;
/*
 * jQuery UI Slider Access
 * By: Trent Richardson [http://trentrichardson.com]
 * Version 0.3
 * Last Modified: 10/20/2012
 * 
 * Copyright 2011 Trent Richardson
 * Dual licensed under the MIT and GPL licenses.
 * http://trentrichardson.com/Impromptu/GPL-LICENSE.txt
 * http://trentrichardson.com/Impromptu/MIT-LICENSE.txt
 * 
 */
 (function($){

	$.fn.extend({
		sliderAccess: function(options){
			options = options || {};
			options.touchonly = options.touchonly !== undefined? options.touchonly : true; // by default only show it if touch device

			if(options.touchonly === true && !("ontouchend" in document))
				return $(this);
				
			return $(this).each(function(i,obj){
						var $t = $(this),
							o = $.extend({},{ 
											where: 'after',
											step: $t.slider('option','step'), 
											upIcon: 'ui-icon-plus', 
											downIcon: 'ui-icon-minus',
											text: false,
											upText: '+',
											downText: '-',
											buttonset: true,
											buttonsetTag: 'span',
											isRTL: false
										}, options),
							$buttons = $('<'+ o.buttonsetTag +' class="ui-slider-access">'+
											'<button data-icon="'+ o.downIcon +'" data-step="'+ (o.isRTL? o.step : o.step*-1) +'">'+ o.downText +'</button>'+
											'<button data-icon="'+ o.upIcon +'" data-step="'+ (o.isRTL? o.step*-1 : o.step) +'">'+ o.upText +'</button>'+
										'</'+ o.buttonsetTag +'>');

						$buttons.children('button').each(function(j, jobj){
							var $jt = $(this);
							$jt.button({ 
											text: o.text, 
											icons: { primary: $jt.data('icon') }
										})
								.click(function(e){
											var step = $jt.data('step'),
												curr = $t.slider('value'),
												newval = curr += step*1,
												minval = $t.slider('option','min'),
												maxval = $t.slider('option','max'),
												slidee = $t.slider("option", "slide") || function(){},
												stope = $t.slider("option", "stop") || function(){};

											e.preventDefault();
											
											if(newval < minval || newval > maxval)
												return;
											
											$t.slider('value', newval);

											slidee.call($t, null, { value: newval });
											stope.call($t, null, { value: newval });
										});
						});
						
						// before or after					
						$t[o.where]($buttons);

						if(o.buttonset){
							$buttons.removeClass('ui-corner-right').removeClass('ui-corner-left').buttonset();
							$buttons.eq(0).addClass('ui-corner-left');
							$buttons.eq(1).addClass('ui-corner-right');
						}

						// adjust the width so we don't break the original layout
						var bOuterWidth = $buttons.css({
									marginLeft: ((o.where == 'after' && !o.isRTL) || (o.where == 'before' && o.isRTL)? 10:0), 
									marginRight: ((o.where == 'before' && !o.isRTL) || (o.where == 'after' && o.isRTL)? 10:0)
								}).outerWidth(true) + 5;
						var tOuterWidth = $t.outerWidth(true);
						$t.css('display','inline-block').width(tOuterWidth-bOuterWidth);
					});		
		}
	});

})(jQuery);;

// Google form AJAX
$( document ).ready(function() {







    $(".tabsanchor111").click(function() {
        $("#tabbutton1").click();
        scrollToAnchor('tabsanchor');
    });

    $(".tabsanchor222").click(function() {
        $("#tabbutton2").click();
        scrollToAnchor('tabsanchor');
    });

    $(".tabsanchor333").click(function() {
        $("#tabbutton3").click();
        scrollToAnchor('tabsanchor');
    });

    $(".tabsanchor444").click(function() {
        $("#tabbutton4").click();
        scrollToAnchor('tabsanchor');
    });

    $(".tabsanchor555").click(function() {
        $("#tabbutton5").click();
        scrollToAnchor('tabsanchor');
    });

    $(".tabsanchor666").click(function() {
        $("#tabbutton6").click();
        scrollToAnchor('tabsanchor');
    });

    $(".tabsanchor777").click(function() {
        $("#tabbutton7").click();
        scrollToAnchor('tabsanchor');
    });

  var url2 =  $(location).attr('href');
  var hash = url2.substring(url2.indexOf("#")+1);
  if (hash=='tab2link'){
    $("#tabbutton2").click();
    scrollToAnchor('tabsanchor');
  }
  if (hash=='tab3link'){
    $("#tabbutton3").click();
    scrollToAnchor('tabsanchor');
  }
    if (hash=='tab4link'){
    $("#tabbutton4").click();
    scrollToAnchor('tabsanchor');
  }
    if (hash=='tab1link'){
    $("#tabbutton1").click();
    scrollToAnchor('tabsanchor');
  }
    if (hash=='tab5link'){
    $("#tabbutton5").click();
    scrollToAnchor('tabsanchor');
  }
      if (hash=='tab6link'){
    $("#tabbutton6").click();
    scrollToAnchor('tabsanchor');
  }
      if (hash=='tab7link'){
    $("#tabbutton7").click();
    scrollToAnchor('tabsanchor');
  }
      if (hash=='tab8link'){
    $("#tabbutton8").click();
    scrollToAnchor('tabsanchor');
  }

    if (hash=='tab2'){
    $("#tabbutton2").click();
  }
  if (hash=='tab3'){
    $("#tabbutton3").click();
  }
    if (hash=='tab4'){
    $("#tabbutton4").click();
  }
    if (hash=='tab1'){
    $("#tabbutton1").click();
  }
    if (hash=='tab5'){
    $("#tabbutton5").click();
  }
      if (hash=='tab6'){
    $("#tabbutton6").click();
  }
      if (hash=='tab7'){
    $("#tabbutton7").click();
  }
      if (hash=='tab8'){
    $("#tabbutton8").click();
  }



if((hash.indexOf('wpcf7')>-1)&&(hash.indexOf('-o1')>-1)){
    $(".enquireclass:first").click(); 
    scrollToAnchor('tabsanchor'); 
}



if((hash.indexOf('wpcf7')>-1)&&(hash.indexOf('-o2')>-1)){
    $(".quoteclass:first").click(); 
    scrollToAnchor('tabsanchor'); 
}
 





	$('#subscribeform').submit(function(e) {
	    e.preventDefault();
	    $.ajax({
	        url: "https://docs.google.com/a/jtbap.com/forms/d/156gXL4746L64dL8pcMuAmzCZuus7gvohkUlx1a_UVOs/formResponse",
	        data: $(this).serialize(),
	        type: "POST",
	        dataType: "xml",
	        success: function(data) {
	            console.log('Submission successful');
	            $("#subscribeform").toggle();
	            $("#subscribemessage").html('<h4 class="green-text"><i class="fa fa-envelope" aria-hidden="true"></i> Thank you for subscribing with JTB!</h4>');
	        },
	        error: function(xhr, status, error) {
	        	if (error.length>0){
	        		console.log('Submission failed: ' + error);
		            $("#subscribemessage").html('Submission failed: ' + error+'<p>Try refresh the page, or contact us using the contact form</p>');
	        	}else{
		            console.log('Submission successful');
		            $("#subscribeform").toggle();
		            $("#subscribemessage").html('<h4 class="green-text"><i class="fa fa-envelope" aria-hidden="true"></i> You have subscribed to our newsletter</h4><p>Thank you</p>');
	        	}
	        }
	    });
	});
	$('.wpcf7-response-output').addClass('col-xs-12');
	$('.wpcf7-response-output').addClass('col-sm-12');
	return false;

 


});


function scrollToAnchor(aid){
    var aTag = $("a[name='"+ aid +"']");
    $('html,body').animate({scrollTop: aTag.offset().top},'slow');
}

;

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
	return true;
});
	//document.getElementsByClassName("close")[i].onclick = function(event) {}
//}

;
/*
Date.locale = {
    month_names: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
    short_names: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
};

var Explore = Explore || {};

Explore.Options = {
  earliestDeparture: 7,
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




$(function(){

  var today = new Date();
  var earliestDeparture = new Date(today.getFullYear(), today.getMonth(), today.getDate()+Explore.Options.earliestDeparture);
  var defaultReturn = new Date(earliestDeparture.getFullYear(), earliestDeparture.getMonth(), earliestDeparture.getDate()+Explore.Options.defaultReturn);

  //setup autocomplete
  $(".airport-autocomplete").each(function(index){
        var ida = $(this).hasClass('init-dep-apt');
        $(this).autocomplete({
            source: function( request, response ) {
                $.ajax({
                    url: "https://jtb.sabreexplore.com.au:443/citySearchJson.aj?sn=jtb&ida=" + ida,
                    dataType: "jsonp",
                    data: {
                        term: request.term
                    },
                    success: function( data ) {
                        response( $.map( data.query.results.result, function( item ) {
                            return {
                                label: item.display,
                                value: item.select
                            }
                        }));
                    }
                });
            },
            minLength: Explore.Options.minAutocompleteCharacters,
            open: function() {
                $( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
            },
            close: function() {
                $( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
            }
        });
    });

  //disable/enable
  $("input[name=returnOrOneWay]").change(function(){
     var x = $("#searchAir\\.segments1\\.departDate")
     if($(this).val() == 'R'){
        x.removeClass('disabled');
        $("input, select", x).prop('disabled', false);
     } else {
        x.addClass('disabled');
        $("input, select", x).prop('disabled', true);
     }
  });

  $("#explore-amenities").buttonset();
  //room guests
  $("#searchHotelV2\\.roomCount").change(function(){
     var count = $(this).val();
     var base = $("#searchHotelV2\\.segments0\\.guestCount");
     for(var i = 1; i < 5; i++){
        var elem = $("#searchHotelV2\\.segments" + i + "\\.guestCount");
        var prev = $("#searchHotelV2\\.segments" + (i-1) + "\\.guestCount");
        if(i < count && elem.size() == 0){
           elem = base.clone(); $("label", elem).html("Guests in room " + (i+1) + ":").attr('for','searchHotelV2.segments[' + i + '].guestCount'); elem.attr('id', 'searchHotelV2.segments' + i + '.guestCount');
           $("select",elem).attr('name','searchHotelV2.segments[' + i + '].guestCount');
           prev.after(elem);
        }else if(i >= count){
           elem.detach();
        }
     }
  });

  //setup validation, copy
  $("#explore-flights form, #explore-hotels form, #explore-cars form").submit(function(event){
     $(".explore-error").detach();
     $(".explore-validate-airport",this).each(function(){
         var val = $(this).val();
         if(val == null || !(val.length == 3 || /.*\([A-Z]{3}\)/.test(val) ) ){
            $(this).after("<div class='explore-error'>Please select an airport or enter an airport code</div>");
         }
      });
     $(".explore-validate-date", this).each(function(){
         if($(this).hasClass("disabled")) { return; }
         var selDate = new Date($(".year",this).val(), $(".month", this).val()-1, $(".day",this).val());
         if($(this).attr('dateafter')){
            var that = $("#" + $(this).attr('dateafter').replace(/\./g, "\\."));
            var thatDate = new Date($(".year",that).val(), $(".month", that).val()-1, $(".day",that).val());
            if(selDate < thatDate){
               $(this).after("<div class='explore-error'>Please select a date after " + thatDate.getDate() + "-" + Date.locale.short_names[thatDate.getMonth()] + "-" + thatDate.getFullYear() + "</div>");
            }
         }
         if(selDate < earliestDeparture){
            $(this).after("<div class='explore-error'>Please select a date after " + earliestDeparture.getDate() + "-" + Date.locale.short_names[earliestDeparture.getMonth()] + "-" + earliestDeparture.getFullYear() + "</div>");
         }
     });
     if($("input[name=returnOrOneWay]:checked").val() == 'R'){
        $("#searchAir\\.segments1\\.departCity").val($("#searchAir\\.segments0\\.arrivalCity").val());
        $("#searchAir\\.segments1\\.arrivalCity").val($("#searchAir\\.segments0\\.departCity").val());
        $("#searchAir\\.segments0\\.cabinIndicator").val($("#cabClassSelect").val());
        $("#searchAir\\.segments1\\.cabinIndicator").val($("#cabClassSelect").val());
     } else {
        $("#searchAir\\.segments1\\.departCity").val("");
        $("#searchAir\\.segments1\\.arrivalCity").val("");
        $("#searchAir\\.segments0\\.cabinIndicator").val($("#cabClassSelect").val());
        $("#searchAir\\.segments1\\.cabinIndicator").val("");
     }
     $(".explore-data-copy").each(function(){
        $(this).val($("#" + $(this).attr('idref').replace(/\./g, "\\.")).val());
     });

     //return false;
     return $(".explore-error", this).size() == 0;

   });


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
                $(".month", wrapper).val(date.getMonth());
                $(".year", wrapper).val(date.getFullYear());
            }
     });
  });
});
*/;
