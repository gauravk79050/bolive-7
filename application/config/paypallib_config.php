<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

// ------------------------------------------------------------------------
// Ppal (Paypal IPN Class)
// ------------------------------------------------------------------------

// If (and where) to log ipn to file
$config['paypal_lib_ipn_log_file'] = BASEPATH . 'logs/paypal_ipn.log';
$config['paypal_lib_ipn_log'] = TRUE;

// Where are the buttons located at 
$config['paypal_lib_button_path'] = 'buttons';

// What is the default currency?
$config['paypal_lib_currency_code'] = 'EUR';

$config['paypal_lib_environment'] = 'live'; //live or testing

// Paypal email id
$config['paypal_lib_email_address'] = 'info@onlinebestelsysteem.net';

// Email ID where purchase report would go
$config['paypal_lib_receiver_email_address'] = 'info@onlinebestelsysteem.net';

?>
