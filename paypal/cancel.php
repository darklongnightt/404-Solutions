<?php
include("../config/db_connect.php");
include("../templates/header.php");

$_SESSION['LASTACTION'] = 'PAYCANCEL';
echo "<script type='text/javascript'>window.top.location='/my_orders.php';</script>";
?>
<!DOCTYPE HTML>
<html>
<link rel="stylesheet" type="text/css" href="/css/paypal_style.css">
<div class="container">
	<div class="error-container">
		<h1 class="paypalerror">Your PayPal Transaction has been Canceled</h1>
	</div>
</div>

<?php include("../templates/footer.php"); ?>

</html>