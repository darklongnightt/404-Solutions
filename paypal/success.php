<?php
// Include configuration file 
include_once 'config.php';
include("../config/db_connect.php");
include("../templates/header.php");
$uid = mysqli_real_escape_string($conn, $_SESSION['U_UID']);

$currDir = "paypal/success.php";

// If transaction data is available in the URL 
if (!empty($_GET['tid']) && !empty($_GET['item_number']) && !empty($_GET['tx']) && !empty($_GET['amt']) && !empty($_GET['cc']) && !empty($_GET['st'])) {
	// Get transaction information from URL 
	$item_number = $_GET['item_number'];
	$tid = $_GET['tid'];
	$txn_id = $_GET['tx'];
	$payment_gross = $_GET['amt'];
	$currency_code = $_GET['cc'];
	$payment_status = $_GET['st'];
}


if (!empty($_GET['tid'])) {

	// Change status of an order entry to 'Confirmed Payment'
	$status = 'Confirmed Payment';
	$sql = "UPDATE orders SET STATUS = '$status' WHERE TRANSACTIONID = '$tid'";
	if (mysqli_query($conn, $sql)) {
		sleep(7);
		$_SESSION['LASTACTION'] = 'PAYCONFIRM';
		echo "<script type='text/javascript'>window.top.location='/my_orders.php';</script>";
	} else {
		echo 'Query Error: ' . mysqli_error($conn);
	}
} else { ?>
	<h1 class="paypalerror">Order ID invalid</h1>
<?php
}

// Free memory of result and close connection
mysqli_close($conn);
?>

<link rel="stylesheet" type="text/css" href="/css/paypal_style.css">

<div class="container">
	<div class="error-container white" style="padding: 25px; margin: 25px;">
		<h1 class="paypalsuccess">Your Payment has been Successful</h1>

		<h4>Payment Information</h4>

		<div class="grey-text">Order ID:</div>
		<div style="font-size: 18px;"><?php echo $tid; ?></div>

		<div class="grey-text">Paypal Transaction ID:</div>
		<div style="font-size: 18px;"><?php echo $txn_id; ?></div>

		<div class="grey-text">Paid Amount:</div>
		<div style="font-size: 18px;"><?php echo $payment_gross; ?></div>

		<div class="grey-text">Payment Status:</div>
		<div style="font-size: 18px;"><?php echo $payment_status; ?></div>



	</div>
</div>