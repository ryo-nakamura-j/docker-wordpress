
$(window).ready(function() {
	Vue.use(VeeValidate);
	Vue.use(VueScrollMagnet);
	if ($("#tourplanRetailConfig").length > 0) {
		REI = new TourplanRetailInterface($("#tourplanRetailConfig").attr("serverurl"));
		CartInterface = new TourplanCart($("#tourplanRetailConfig").attr('carturl'));
	}
});