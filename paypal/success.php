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
?>
<link rel="stylesheet" type="text/css" href="/css/paypal_style.css">

<div class="container">
	<div class="error-container">
		<h1 class="paypalsuccess">Your Payment has been Successful</h1>

			<h4>Payment Information</h4>

			<p><b>Order ID:</b> <?php echo $tid; ?></p>
			<p><b>Paypal Transaction ID:</b> <?php echo $txn_id; ?></p>
			<p><b>Paid Amount:</b> <?php echo $payment_gross; ?></p>
			<p><b>Payment Status:</b> <?php echo $payment_status; ?></p>
			
		 <?php 
        		if (!empty($_GET['tid'])) 
        		{

        			// Change status of an order entry to 'Confirmed Payment'
        			$status = 'Confirmed Payment';
        			$sql = "UPDATE orders SET STATUS = '$status' WHERE TRANSACTIONID = '$tid'";
				    if (mysqli_query($conn, $sql)) {
				        //header("Refresh:0");
				        sleep(3);
				        echo "<script type='text/javascript'>window.top.location='/my_orders.php';</script>";
				    } else {
				        echo 'Query Error: ' . mysqli_error($conn);
				    }
				} 
				else { ?>
			<h1 class="paypalerror">Order ID invalid</h1>
		<?php 
			} 
	// Free memory of result and close connection
	mysqli_close($conn);
	?>

    </div>
</div>