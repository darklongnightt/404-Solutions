<?php
// PayPal configuration 
define('PAYPAL_ID', 'sb-szic6332813@business.example.com');
define('PAYPAL_SANDBOX', TRUE); //TRUE or FALSE 

define('PAYPAL_RETURN_URL', 'https://super-data-fyp.appspot.com/paypal/success.php');
define('PAYPAL_CANCEL_URL', 'https://super-data-fyp.appspot.com/paypal/cancel.php');
define('PAYPAL_NOTIFY_URL', 'https://super-data-fyp.appspot.com/paypal/ipn.php');
define('PAYPAL_CURRENCY', 'SGD');

// Change not required 
define('PAYPAL_URL', (PAYPAL_SANDBOX == true) ? "https://www.sandbox.paypal.com/cgi-bin/webscr" : "https://www.paypal.com/cgi-bin/webscr");
