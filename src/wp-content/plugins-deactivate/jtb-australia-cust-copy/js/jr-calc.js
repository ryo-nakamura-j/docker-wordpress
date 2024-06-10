
var pass_prices = ["393","626","801"]
var jpy = 9999999990.015; // loaded from PHP - WP page data #exchange_rate


var datablock = 'x@tSapporo@tHakodate@tAomori@tMorioka (Iwate)@tSendai (Miyagi)@tFukushima@tYamagata@tUtsunomiya(Tochigi)@tOmiya(Saitama)@tChiba@tMito(Ibaragi)@tTokyo@tYokohama (Kanagawa)@tKofu(Yamanashi)@tTakasaki(Gunma)@tNagano @tNiigata@tToyama@tKanazawa (Ishikawa)@tFukui@tShizuoka@tNagoya (Aichi)@tGifu@tTakayama (Gifu)@tTsu (Mie)@tOtsu(shiga)@tWakayama@tKyoto @tOsaka@tNara@tSannomiya (Hyogo)@tHimeji (Hyogo)@tOkayama@tHiroshima@tMatsue(Shimane)@tTottori@tYamaguchi@tTokushima@tTakamastu(Kagawa)@tMatsuyama(Ehime)@tKochi@tHakata(Fukuoka)@tSaga@tNagasaki@tKumamoto@tBeppu(Oita)@tMiyazaki@tKagoshimachuou(Kagoshima)@nSapporo@t@t8910@t13570@t18520@t22260@t23350@t22480@t25370@t26230@t28390@t29330@t27300@t27300@t28240@t28760@t30740@t32730@t35040@t35800@t36460@t31040@t34230@t34230@t36650@t36170@t36190@t37370@t36000@t36330@t36330@t36660@t37640@t38840@t40620@t40380@t40480@t42930@t40910@t39600@t41850@t41410@t44460@t45970@t46960@t48260@t46460@t52680@t51800@nHakodate@t@t@t7790@t13400@t18130@t19880@t19010@t21790@t22690@t23760@t25790@t23760@t23760@t25750@t25220@t26980@t29190@t31170@t32040@t33190@t27610@t30800@t30800@t31830@t32740@t32650@t33940@t32460@t32790@t32790@t33280@t34260@t35300@t37080@t37940@t36940@t39500@t37480@t36170@t38310@t37870@t40920@t42230@t43760@t44630@t42720@t48940@t48060@nAomori@t@t@t@t6050@t11420@t13060@t12190@t16070@t16930@t18000@t20030@t17670@t18000@t19500@t19460@t21310@t23430@t25410@t26170@t27960@t21740@t24930@t24930@t27690@t26870@t26890@t28070@t26700@t27030@t27030@t27360@t28340@t29540@t31320@t31850@t31020@t33630@t31280@t30300@t32550@t32110@t35160@t36470@t38000@t38760@t36960@t44430@t42300@nMorioka (Iwate)@t@t@t@t@t6790@t9110@t7890@t12730@t14250@t15560@t17480@t15010@t15560@t17480@t16890@t18870@t21520@t23060@t24260@t26050@t19300@t22490@t22820@t25130@t24430@t24780@t25960@t24590@t24810@t24810@t25140@t25900@t27100@t28880@t29740@t28910@t31190@t29170@t27860@t30440@t30000@t32720@t34250@t35560@t36320@t34740@t42320@t40080@nSendai (Miyagi)@t@t@t@t@t@t3210@t1166@t8040@t10870@t11960@t9250@t11410@t11960@t14430@t13620@t15950@t19040@t21240@t21460@t22260@t16270@t19680@t20010@t22320@t21620@t21860@t23150@t21670@t22000@t22000@t22330@t23090@t24510@t26400@t26930@t26100@t28380@t26360@t25050@t27630@t27190@t30130@t31440@t33080@t33840@t31930@t39510@t37470@nFukushima@t@t@t@t@t@t@t2720@t5720@t8040@t9240@t7150@t8580@t9240@t11920@t10900@t13320@t16740@t19270@t20030@t20290@t13750@t17710@t17930@t20350@t18780@t19890@t21070@t19700@t20030@t20030@t20580@t21010@t22540@t24320@t24850@t24020@t26410@t24280@t23080@t25550@t25110@t28160@t29470@t31000@t31760@t29960@t37430@t35300@nYamagata@t@t@t@t@t@t@t@t8000@t10100@t11190@t13660@t10640@t10980@t13100@t13180@t15600@t18470@t20670@t21540@t22800@t16030@t19440@t19440@t21750@t20290@t21290@t22580@t21100@t21430@t21430@t21760@t22850@t23940@t25830@t26690@t27490@t28140@t25790@t24810@t27060@t26620@t29560@t30870@t32510@t33270@t31360@t38940@t37010@nUtsunomiya(Tochigi)@t@t@t@t@t@t@t@t@t3210@t5150@t1694@t4490@t4820@t6630@t6380@t9350@t12770@t15300@t16390@t17740@t10210@t14390@t15050@t17600@t15570@t17450@t18630@t17260@t17480@t17480@t17810@t18570@t19990@t21880@t22410@t21580@t23860@t21840@t20530@t23110@t22670@t25720@t26920@t28560@t29320@t27410@t34990@t32750@nOmiya(Saitama)@t@t@t@t@t@t@t@t@t@t1100@t3560@t561@t935@t3890@t3210@t6050@t9800@t12330@t13640@t14790@t6270@t10780@t10780@t14630@t12720@t14060@t15460@t13870@t14420@t14420@t14960@t15400@t16930@t18710@t19240@t18570@t20800@t19000@t17750@t19940@t19500@t22550@t23890@t25520@t25520@t24350@t31820@t31090@nChiba@t@t@t@t@t@t@t@t@t@t@t2140@t649@t1100@t5320@t5150@t8580@t10890@t13310@t14730@t14680@t6270@t6270@t11330@t15940@t13050@t14060@t15460@t13870@t14420@t14420@t14750@t15730@t16930@t18710@t19570@t18570@t21020@t19000@t17690@t19940@t19500@t22550@t23860@t25390@t26150@t24350@t31820@t30000@nMito(Ibaragi)@t@t@t@t@t@t@t@t@t@t@t@t3890@t4220@t7670@t7620@t11160@t13360@t15890@t15890@t16810@t9610@t13790@t14120@t15290@t15730@t16520@t17700@t16330@t16550@t14970@t16880@t16390@t19060@t20950@t21810@t20720@t23260@t21240@t19930@t22180@t21740@t24790@t25990@t27630@t28390@t26480@t34060@t31820@nTokyo@t@t@t@t@t@t@t@t@t@t@t@t@t473@t3890@t4490@t7810@t10230@t12760@t14180@t14130@t5940@t10560@t11160@t15390@t12500@t13510@t15240@t13320@t13870@t13870@t14420@t15400@t16600@t18380@t19240@t18240@t20800@t18670@t17470@t19610@t19170@t22220@t23530@t25060@t25930@t24020@t31490@t29360@nYokohama (Kanagawa)@t@t@t@t@t@t@t@t@t@t@t@t@t@t3330@t4820@t8580@t10890@t13310@t14400@t13580@t5412@t10142@t10802@t13882@t11840@t12840@t14910@t12650@t13540@t13442@t14542@t15422@t16512@t18050@t18910@t18482@t19910@t18670@t17420@t19280@t19170@t21890@t23560@t24840@t25600@t23480@t31490@t29912@nKofu(Ymanashi)@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t7520@t4280@t13360@t9950@t11720@t14020@t4170@t12900@t13230@t16310@t14840@t15620@t17140@t15430@t15990@t15650@t16530@t17080@t18280@t20060@t20920@t20090@t21810@t20350@t19040@t20170@t19730@t21720@t25430@t26740@t27500@t23520@t33500@t31570@nTakasaki(Gunma)@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t4620@t7600@t10030@t11670@t13370@t10210@t14390@t15050@t12660@t16330@t17450@t18630@t17260@t17480@t17480@t17810@t18570@t19770@t21550@t22410@t21580@t23860@t21840@t20530@t23110@t22670@t25720@t26920@t28230@t29320@t27410@t34990@t32750@nNagano @t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t12220@t7040@t8920@t10950@t13530@t6930@t7590@t10000@t8870@t13540@t13090@t10340@t14090@t14700@t14640@t13190@t19470@t17170@t18910@t24020@t18810@t17840@t15930@t22810@t21500@t24870@t26510@t23750@t24510@t22030@t34040@t27940@nNiigata@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t8540@t9530@t11830@t15400@t19030@t19360@t19380@t20970@t21210@t22500@t21020@t21350@t21350@t21890@t22440@t23640@t25420@t26280@t25450@t27730@t25710@t24400@t26980@t26540@t29260@t30790@t32100@t32860@t31280@t37610@t36620@nToyama@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t2860@t4080@t17820@t10230@t6930@t2890@t11300@t8030@t10670@t8030@t8800@t8800@t9130@t10560@t13630@t16710@t17050@t15060@t19030@t16200@t14420@t18290@t17310@t20020@t21360@t23800@t23300@t21390@t30180@t26730@nKanazawa (Ishikawa)@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t2540@t11980@t7370@t6490@t5710@t8880@t6490@t9130@t6490@t7260@t7650@t7920@t9020@t12090@t15170@t16600@t13400@t16500@t16190@t13180@t17300@t16860@t18480@t19790@t21430@t22190@t19850@t27860@t25620@nFukui@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t10380@t5440@t4600@t8070@t6950@t3840@t8030@t4500@t5610@t5660@t6270@t8300@t10380@t13850@t15270@t12200@t15900@t14810@t10990@t15660@t15530@t17650@t18930@t20490@t20990@t19010@t26920@t24890@nShizuoka@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t5940@t6270@t10010@t7880@t10090@t12150@t9900@t10560@t10560@t11760@t12320@t13870@t16270@t16730@t15710@t18050@t16490@t14960@t17430@t16990@t19580@t21220@t22420@t23180@t21710@t23900@t27050@nNagoya (Aichi)@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t470@t5610@t1940@t5370@t7860@t5170@t5940@t6330@t6600@t8240@t10550@t13540@t14180@t11800@t15400@t13390@t11530@t15100@t14440@t17830@t19030@t20670@t21430@t18950@t21470@t24860@nGifu@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t2640@t2410@t5810@t8190@t5610@t6600@t6770@t8120@t8900@t10770@t13870@t14730@t12290@t16060@t14270@t12080@t15430@t14660@t18050@t19360@t20670@t21760@t19280@t21470@t25190@nTakayama (Gifu)@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t6630@t6370@t11110@t9350@t10010@t10010@t11640@t11980@t14180@t16400@t18140@t15590@t18040@t17070@t15270@t18840@t18400@t20140@t21780@t22980@t23740@t21700@t29740@t27610@nTsu (Mie)@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t6390@t4160@t6310@t3240@t2970@t3470@t6510@t12490@t15480@t16120@t10090@t16420@t15660@t13470@t13940@t16380@t17810@t21310@t22610@t22450@t20890@t22490@t25880@nOtsu(Shiga)@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t4060@t200@t990@t940@t2510@t5170@t6600@t10770@t11430@t8450@t13870@t10640@t9110@t12350@t11690@t14970@t17380@t18350@t18680@t17100@t24570@t22440@nWakayama@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t2860@t1270@t1610@t2530@t4400@t7040@t11100@t11770@t8020@t14240@t11310@t8950@t12690@t12030@t15300@t17130@t18630@t19340@t17590@t25060@t22930@nKyoto @t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t570@t1160@t1100@t4840@t7140@t10770@t11430@t7690@t13540@t10640@t8780@t12020@t11360@t15400@t17050@t18350@t19110@t16770@t25000@t22540@nOsaka@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t810@t410@t3280@t5610@t9890@t10010@t6590@t13030@t9110@t7250@t10600@t9770@t14750@t15950@t17590@t18350@t16440@t24020@t21780@nNara@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t1270@t4400@t6600@t10770@t10560@t7690@t13530@t9770@t8240@t11480@t10820@t14970@t16610@t17920@t18680@t17100@t24570@t22440@nSannomiya(Hyogo)@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t990@t5380@t9880@t9560@t5920@t12690@t8770@t6690@t10260@t9820@t14630@t16500@t17470@t18230@t16650@t24230@t21990@nHimeji (Hyogo)@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t3280@t7910@t7700@t3680@t11100@t6690@t5050@t8730@t7960@t13540@t15400@t16930@t17690@t15550@t23360@t21230@nOkayama@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t5610@t5610@t4470@t8900@t4100@t1550@t6420@t5540@t12100@t13630@t16040@t16470@t16470@t22360@t20230@nHiroshima@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t9130@t10460@t5170@t8890@t6810@t10380@t9940@t8570@t10100@t12180@t13050@t10590@t19930@t17470@nMatsue(Shimane)@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t3630@t6930@t9380@t6920@t11810@t11040@t15510@t17050@t18020@t15040@t12140@t18940@t19020@nTottori@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t12680@t8480@t6240@t11130@t10250@t15550@t19260@t20450@t19700@t17450@t21590@t24750@nYamaguchi@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t11740@t10210@t13450@t12790@t5610@t7250@t9660@t10420@t6970@t17850@t15260@nTokushima@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t1470@t8400@t4430@t14940@t16810@t17780@t18540@t16510@t23290@t22300@nTakamatsu(Kagawa)@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t5760@t4990@t13190@t14940@t16470@t17340@t14980@t23230@t21100@nMatsuyama(Ehime)@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t9390@t16100@t17410@t19050@t19810@t17450@t25480@t23240@nKochi@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t15770@t17310@t18610@t19370@t17010@t19530@t22800@nHakata(Fukuoka)@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t1970@t4270@t4700@t5840@t13120@t10110@nSaga@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t3420@t4240@t7400@t13430@t10420@nNagasaki@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t7080@t9940@t15930@t12790@nKumamoto@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t10600@t10100@t6540@nBeppu(Oita)@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t5590@t14880@nMiyazaki@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t@t3780@n';




var calcdata = [];
var array2 = [];

array1 = datablock.split('@n');

for (var i = 0 ; i <  array1.length ; i++) {
	array2.push( array1[i].split('@t')  ) ;
	//console.log( array1[i] );
}

var labels1 = [];
var labels2 = [];

for (var i = 0   ; i <  array2.length ; i++) {
	for (var c =  0 ; c < array2[i].length ; c++) {
		//array2[c]
		if(i == 0){
			labels1.push( array2[i][c] );
			console.log('array2-cell_'+ array2[i][c] );
		}else{
			break;
		}
	}
	if(  i == 0 ){
		continue;
	}
	labels2.push( array2[i][0]  ) ;
	console.log( 'array2-row_'+ array2[i][0]);

}


//load array 

for (var i = 0   ; i <  array2.length ; i++) {
	if(i==0){continue;}
	for (var c =  0 ; c < array2[i].length ; c++) {
		console.log('cell___'+ array2[i][c] );
		if((array2[i][0] == "x") ||(array2[0][c] == "x") ||(array2[i][c] == 0) ||(array2[i][c] == null)||(array2[i][c] == false) ||(array2[i][c] == undefined ) ){
			continue;
		}
		var break2 = false; 
		for(var j=0; j< calcdata.length ; j++ ){
			var temp_name = array2[0][c] + " - " + array2[i][0];
			var temp_name2 = array2[i][0] + " - " + array2[0][c];
			if( (calcdata[j][0] == temp_name)|| (calcdata[j][0] == temp_name2) ){
				break2 = true;
			}

		}
					if( ! break2){
				calcdata.push([ array2[0][c] + " - " + array2[i][0], String(array2[i][c]), "0"   ]);
				//console.log( array2[0][c] + " - " + array2[i][0] + " >> " +  String(array2[i][c]) + " >> " +  "0"     );
			}


	}
}







for (var i = 1  ; i <  array2.length ; i++) {
	for (var c =  1 ; c < array2[i].length ; c++) {
		//array2[c]
		labels1.push( array2[i][c] );
		//console.log('cell_'+ array2[i][c] );
		
	}
	if(  i == 0 ){
		continue;
	}
	labels2.push( array2[i][0]  ) ;
	//console.log( 'row_'+ array2[i][0]);
}

 

var calcdata2 = [

['Tokyo - Osaka', "14000","500"],
['Osaka - Kyoto', "500","0"],
['Nara - Osaka', "800","0"],
['Nara - Tokyo', "14200","500"],
['Nara - Kyoto', "700","0"],
//['Nara - Hiroshima', "12000","2000"],
['xxx','100','100']
];



var location_list=[];


var number_of_items =0;
var total_cost=0; 

//jQuery( document ).ready(function() {
jQuery(window).load(function() {

//addcalc

//alert('123');

setTimeout(function(){ init_calc(); }, 5000);

});

function init_calc(){
	 init_selection(); init_jr_prices();
}


function init_selection(){
	for (var i = 0; i< calcdata.length ; i++) {
	var temp = calcdata[i][0].split(" - ");
	if(   location_list.includes( temp[0]) || temp[0]=="xxx"|| temp[0]=="undefined"|| temp[0]==undefined ){
		
	}else{
		location_list[location_list.length]=temp[0];
	}
	if(   location_list.includes( temp[1]) || temp[1]=="xxx"|| temp[1]=="undefined"|| temp[1]==undefined ){
		
	}else{
		location_list[location_list.length]=temp[1];
	}
}
var selec = '<select name="station1" id="station1" onchange="selec2();">';
selec += '<option value="x">Starting Location</option>';
//	location_list;
for (var i =0; i< location_list.length  ; i++) {
	
	selec += '<option value="'+location_list[i]+'">'+location_list[i]+'</option>';
}
selec += '</select>';
	document.getElementById("selection").innerHTML = selec;
}

function selec2(){
	var x = document.getElementById("station1").selectedIndex;
	if( 	document.getElementById("station1")[x].value != "x" ){
		 //document.getElementById("selection2").innerHTML = "1";
		 init_selection2(document.getElementsByTagName("option")[x].value);
	}else{
		 document.getElementById("selection2").innerHTML = "";
		 document.getElementById("selection3").innerHTML = "";//clear button
	}
//var selection = 1;
}


function init_selection2(x){
var selec = '<select name="station2" id="station2" onchange="selec3();">';
selec += '<option value="x">Destination</option>';
//	location_list;
for (var i = 0 ; i< location_list.length ; i++) {
	if(location_list[i] == x){
		continue;
	}
	selec += '<option value="'+location_list[i]+'">'+location_list[i]+'</option>';
}
selec += '</select>';
	document.getElementById("selection2").innerHTML = selec;
}


function init_jr_prices(){
	  pass_prices[0] = document.getElementById("a7d1").value;//@@@
	 pass_prices[1] = document.getElementById("a14d1").value;
	 pass_prices[2] = document.getElementById("a21d1").value;
	 jpy = document.getElementById("exchange_rate").value;
}

function selec3(){
	var x = document.getElementById("station2").selectedIndex;
	if( 	document.getElementById("station2")[x].value != "x" ){
		 //document.getElementById("selection3").innerHTML = "1";
		 make_button();
		 //init_selection2(document.getElementsByTagName("option")[x].value);
		 //make button to add the things and init the drop-down - add price
	}else{
		 document.getElementById("selection3").innerHTML = "";
	}
//var selection = 1;
}

function make_button(){
	var text = "";
	var data =  make_div();
	text += "<button onClick='addItem(\""+data[0]+"@@@"+ data[1]+"\")' >";
	
	text += "Add the trip: " +data[0]+" - " + data[1];
	text += "</button>";
	document.getElementById("selection3").innerHTML = text;
}

function make_div(){
	var text = [];
	var e = document.getElementById("station1") ;
text[text.length] = e.options[e.selectedIndex].value;
 
	var e22 = document.getElementById("station2") ;
text[text.length] = e22.options[e22.selectedIndex].value;
	return text;
}







function addItem(x){
	var data = x.split("@@@");
	var temp1 = data[0];
var temp2 = data[1];
for (var i = 0 ; i < calcdata.length ; i++) {
	if((calcdata[i][0] == (temp1+" - "+temp2))||(calcdata[i][0] == (temp2+" - "+temp1))){
		 var ts = Math.round((new Date()).getTime() / 1000); 
		ts = ts.toString(32);
		rand =  Math.floor((Math.random() * 88) + 11);
		var btn = document.createElement("div"); 
		btn.setAttribute("id", ts+rand);
		btn.setAttribute("class", "travel_item");
//btn.innerHTML = calcdata[i][0]+" - "+calcdata[i][1]+" - "+calcdata[i][2]+" // <br />"+ts+rand+"<br />"+ temp1+" - "+temp2 +"<br /><br /><button onClick='delete_field(\""+ts+rand+"\");remove_from_total("+calcdata[i][1]+","+calcdata[i][2]+");' >Close</button><br /><br /><br />";
var item_data = "<strong>"+temp1+"</strong> to <strong>"+temp2+"</strong> - ";
var discrepency3 = "";
if( (parseInt(calcdata[i][2])*jpy) != 0  ){
	discrepency3 = "$" +  rmv_decimal(((parseInt(calcdata[i][1])*jpy) - (parseInt(calcdata[i][2])*jpy)).toFixed(2) )+ " - ";
}
item_data += discrepency3 + "$" +  rmv_decimal( (parseInt(calcdata[i][1])*jpy).toFixed(2) ) ;
item_data += "<a onClick='delete_field(\""+ts+rand+"\");remove_from_total("+calcdata[i][1]+","+calcdata[i][2]+");' >Remove <i class='material-icons'>close</i></a>";

btn.innerHTML = item_data;
add_to_total((parseInt(calcdata[i][1]) ),(parseInt(calcdata[i][2]) ));
	calcblock.appendChild(btn);
		break;
	}
}

document.getElementById("selection3").innerHTML = "";//clear button
document.getElementById("selection2").innerHTML = "";//clear selec-2
init_selection();
}


function delete_field(x){
	document.getElementById(x).remove();
}
function add_to_total(x,y){
	//add values - total cost + fluctuation 
	//total1
	//total2
	document.getElementById("total1").value = parseInt(document.getElementById("total1").value) + parseInt(x) ;
	document.getElementById("total2").value = parseInt(document.getElementById("total2").value) + parseInt(y) ;
var discrepency2 =   rmv_decimal(((parseInt(document.getElementById("total1").value) - parseInt(document.getElementById("total2").value))*jpy).toFixed(2) )+  " - $" ;
if( ((parseInt(document.getElementById("total2").value))*jpy) == 0  ){
	discrepency2="";
}
	document.getElementById("running_total").innerHTML = "Total cost: $"+  discrepency2 +  rmv_decimal((parseInt(document.getElementById("total1").value)*jpy).toFixed(2));

	check_pass_value();
}
function remove_from_total(x,y){
	//remove - delete pass - total cost + fluctuation 
	//total1
	//total2
	document.getElementById("total1").value = parseInt(document.getElementById("total1").value) - parseInt(x) ;
	document.getElementById("total2").value = parseInt(document.getElementById("total2").value) - parseInt(y) ;
	var discrepency2 = rmv_decimal(( ((parseInt(document.getElementById("total1").value) - parseInt(document.getElementById("total2").value))*jpy) ).toFixed(2)) + " - $"  ;
		if( ((parseInt(document.getElementById("total2").value))*jpy)  == 0){
		discrepency2="";
	}
	document.getElementById("running_total").innerHTML = "Total cost: $"+  discrepency2+  rmv_decimal((parseInt(document.getElementById("total1").value) *jpy ).toFixed(2));

	if((document.getElementById("running_total").innerHTML == "Total cost: 0 - 0") || (document.getElementById("running_total").innerHTML == "Total cost: $0 - $0")){
          document.getElementById("running_total").innerHTML = "";
	}
	check_pass_value();
}


function addtokyo(){
var temp1 = 'Tokyo';
var temp2 = "kyoto";
for (var i = 0 ; i < calcdata.length ; i++) {
	if((calcdata[i][0] = (temp1+" - "+temp2))||(calcdata[i][0] = (temp2+" - "+temp1))){
		 var ts = Math.round((new Date()).getTime() / 1000); 
		ts = ts.toString(32);
		rand =  Math.floor((Math.random() * 88) + 11);
		var btn = document.createElement("div"); 
		var data = "";
		btn.innerHTML = calcdata[i][1]+"_123<br />"+ts+rand+"<br />"+data+"<br /><br /><br />";   
		calcblock.appendChild(btn);
		break;
	}
}
}


function check_pass_value(){
	//pass_prices = ["393","626","801"]
	//document.getElementById("total1").value
	//document.getElementById("total2").value

	var price_min = ( (parseInt(document.getElementById("total1").value) - parseInt(document.getElementById("total2").value))*jpy).toFixed(2) ;
	var price_max =  ( parseInt(document.getElementById("total1").value) *jpy).toFixed(2) ;
	var jr1 = parseInt(pass_prices[0]);
	var jr2 = parseInt(pass_prices[1]);
	var jr3 = parseInt(pass_prices[2]);
	if( price_max < jr1 ){//worth getting 3 JR passes? - NO
		worth_no(7);worth_no(14);worth_no(21);
	}else if((price_max > jr3) && (price_min > jr3)){
		worth_yes(7);worth_yes(14);worth_yes(21);
	}else if((price_max > jr3) && (price_min < jr3)){
		worth_yes(7);worth_yes(14);worth_maybe(21);
	}else if((price_max > jr2) && (price_min > jr2)){
		worth_yes(7);worth_yes(14);worth_no(21);
	}else if((price_max > jr2) && (price_min < jr2)){
		worth_yes(7);worth_maybe(14);worth_no(21);
	}else if((price_max > jr1) && (price_min > jr1)){
		worth_yes(7);worth_no(14);worth_no(21);
	}else if((price_max > jr1) && (price_min < jr1)){
		worth_maybe(7);worth_no(14);worth_no(21);
	}
//a7day
//a14day
//a21day
}


function worth_no(x){
	var id = "#a"+x+"day";
	var id2 = "#a"+x+"d2";
	$(id).css("color", "#000000");
	$(id2).html( "");
}
function worth_yes(x){
	var id = "#a"+x+"day";
	var id2 = "#a"+x+"d2";
	$(id).css("color", "#46a049");
	$(id2).html( "Pass is worth getting");
}
function worth_maybe(x){
	var id = "#a"+x+"day";
	var id2 = "#a"+x+"d2";
	$(id).css("color", "#ff5825");
	$(id2).html( "Pass may be worth getting");
}

function rmv_decimal(x){
	//remove decimal
	x = x.toString();
	if( x.substr(x.length-3, 3) == ".00" ){
		return x.substr(0,x.length-3);
	} 
	return x.substr(0,x.length-3)+"<small>"+x.substr(x.length-3, 3)+"</small>";
}