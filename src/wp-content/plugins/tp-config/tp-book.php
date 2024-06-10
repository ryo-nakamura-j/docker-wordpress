<?php

require_once('tp-cart.php');

/* ********************************************************************************* */
/*                                   PAYMENT PAGES                                   */
/* ********************************************************************************* */

function tp_book_rail() {
	$cart = tpGetCartArray();
	$code = $_GET["code"];
	$adults = $_GET["adults"];
	$children = $_GET["children"];

	$productDetails = json_decode(tpProductDetailsByCode($code, date('Y-m-d'), 1, 1, null));
	$productInfo = json_decode($productDetails->products[0]->info)->Option->OptGeneral;
	$quantity = $adults . 'A' . $children . 'C';

	if (is_null($cart['servicelines'])) {
		$cart['servicelines'] = array();
	}

	$rail_product = array(
		'productid' => intval($productDetails->products[0]->productid),
		'rateid' => 'Default',
		'date' => date('Y-m-d'),
		'scu' => 1,
		'qty' => $quantity,
		'servicetype' => 'Rail',
		'productname' => $productDetails->products[0]->name,
		'availability' => $productDetails->products[0]->availability[0]->Availability,
		'currency' => get_option('tp_currency'),
		'productcode' => $productDetails->products[0]->code,
		'suppliercode' => $productDetails->supplier->code,
		'adultages' => $productInfo->Adult_From . (($productInfo->Adult_To == 999) ? '+' : ('-' . $productInfo->Adult_To)),
		'childages' => $productInfo->Child_From . '-' . $productInfo->Child_To
	);
	
	array_push($cart['servicelines'], $rail_product);

	tpSetCart(json_encode($cart));
	wp_redirect(site_url() . '/' .  get_option('tp_itinerary_url'));
	exit();
}
?>
