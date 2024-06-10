<?php

require_once('tp-cart.php');

switch (get_option('tp_payment_gateway'))
{
  case 'eway': require_once('tp-eway.php'); break;
  case 'pbb': require_once('tp-pbb.php'); break;
  case 'dps': require_once('tp-dps.php'); break;
  case 'cfb': require_once('tp-cfb.php'); break;
  case 'axes': require_once('tp-axes.php'); break;
  case 'aeon': require_once('tp-aeon.php'); break;
  case 'paynamics': require_once('tp-paynamics.php'); break;
  case 'ipay88': require_once('tp-ipay88.php'); break;
  case 'stripe': require_once('tp-stripe.php'); break;
  case 'credomatic': require_once('tp-credomatic.php'); break;
  case 'paydollar': require_once('tp-paydollar.php'); break;
  default: break;
}

/* ********************************************************************************* */
/*                                   PAYMENT PAGES                                   */
/* ********************************************************************************* */

function tp_payment_success_page()
{
	switch (get_option('tp_payment_gateway'))
	{
		case 'axes':  tpAxesPaymentSuccess(); break;
        case 'cfb':  tpCFBPaymentSuccess(); break;
		default: break;
	}
}

function tp_payment_failed_page()
{
	switch (get_option('tp_payment_gateway'))
	{
		case 'axes':  tpAxesPaymentFailed(); break;
		default: break;
	}
}

function tp_payment_response_page()
{
	switch (get_option('tp_payment_gateway'))
	{
		case 'dps':  tpDPSPaymentResponse(); break;
		case 'cfb':  tpCFBPaymentResponse(); break;
		case 'axes': tpAxesPaymentResponse(); break;
		case 'pbb':  tpPBBPaymentResponse(); break;
		case 'aeon':  tpAEONPaymentResponse(); break;
		case 'paynamics': tpPaynamicsPaymentResponse(); break;
		case 'ipay88': tpIPay88PaymentResponse(); break;
		case 'credomatic':  tpCredomaticPaymentResponse(); break;
		case 'eway': 
			$eWayPGW = new eWayPaymentGateway();
			$eWayPGW -> processingRedirect();
			break;
		case 'paydollar': tpPayDollarPaymentResponse(); break;
		default: break;
	}
}