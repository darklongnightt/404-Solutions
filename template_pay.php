<?php
include("config/db_connect.php");
include("templates/header.php");
include_once 'paypal/config.php';

$totalPrice = $totalQty = 0;
$totalName = '';

// Checks if payment var in url is set
if (isset($_GET['price'])) {
    $totalPrice = $_GET['price'];
}

if (isset($_GET['qty'])) {
    $totalQty = $_GET['qty'];
}

if (isset($_GET['name'])) {
    $totalName = $_GET['name'];
}

echo 'Name: ' . $totalName . '<br>';
echo 'Total Net Price: ' . $totalPrice . '<br>';
echo 'Total Qty: ' . $totalQty . '<br>';
?>

<!DOCTYPE HTML>
<html>
<div class="container">
    <img class="center product-icon" src="https://storage.cloud.google.com/super-data-fyp.appspot.com/BA01579.jpg?cloudshell=false">
    <h4 class="center">Billing Address</h4>
	<form class="EditForm" action="<?php echo PAYPAL_URL; ?>" method="post">
        <label for="adr"><i class="fa fa-address-card-o"></i> Address</label>
            <input type="text" id="adr" name="address" placeholder="542 W. 15th Street">
        <label for="city"><i class="fa fa-institution"></i> City</label>
            <input type="text" id="city" name="city" placeholder="New York">

            <div class="row">
              <div class="col-50">
                <label for="state">State</label>
                <input type="text" id="state" name="state" placeholder="NY">
              </div>
              <div class="col-50">
                <label for="zip">Zip</label>
                <input type="text" id="zip" name="zip" placeholder="10001">
              </div>
              <br>
	        
			<input type="hidden" name="business" value="<?php echo PAYPAL_ID; ?>">


			<input type="hidden" name="cmd" value="_xclick">
			<!-- Specify details about the item that buyers will purchase. -->
			<input type="hidden" name="item_name" value="<?php echo $totalName;?>">
			<input type="hidden" name="item_number" value="<?php echo $totalQty;?>">
			<input type="hidden" name="amount" value="<?php echo $totalPrice;?>">
      <input type="hidden" name="charset" value="utf-8">
			<input type="hidden" name="currency_code" value="<?php echo PAYPAL_CURRENCY; ?>">
			<!-- Specify URLs -->
			<input type="hidden" name="notify_url" value="<?php echo PAYPAL_NOTIFY_URL; ?>">
			<input type="hidden" name="return" value="<?php echo PAYPAL_RETURN_URL; ?>">
			<input type="hidden" name="cancel_return" value="<?php echo PAYPAL_CANCEL_URL; ?>">
			

				<div class="center">
				 <input type="image" name="submit" border="0" src="https://www.paypalobjects.com/webstatic/en_US/i/btn/png/btn_buynow_107x26.png">
				</div>
			</div>
	</form>
</div>

<?php include("templates/footer.php"); ?>

</html>