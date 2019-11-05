<?php
include("config/db_connect.php");
include("templates/header.php");
include_once 'paypal/config.php';

if (isset($_SESSION['LASTACTION'])) {
  if ($_SESSION['LASTACTION'] == 'ADDRESS') {
    echo "<script>M.toast({html: 'Successfully created shipping gaddress!'});</script>";
    unset($_SESSION['LASTPAGE']);
  }

  $_SESSION['LASTACTION'] = 'NONE';
}

$totalPrice = $totalQty = 0;
$totalName = $tid = '';

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

if (isset($_GET['tid'])) {
  $tid = $_GET['tid'];
}

// Retrieve customer address
$sql = "SELECT * FROM address WHERE USERID='$uid'";
$result = mysqli_query($conn, $sql);
$address = mysqli_fetch_assoc($result);

// Redirect user to create shipping address
if (mysqli_num_rows($result) < 1) {
  // Get current link
  $link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ?
    "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
  $_SESSION['LASTPAGE'] = $link;
  echo "<script type='text/javascript'>window.top.location='/authentication/shipping_details.php';</script>";
}
?>

<style>
  .address-card {
    border-style: solid;
    border-width: 2px;
    border-radius: 10px;
    padding: 15px;
    background-color: white;
    margin: 15px;
  }
</style>

<!DOCTYPE HTML>
<html>

<div class="container">
  <h4 class="center grey-text">Verify Address</h4>

  <div class="row">
    <div class="col s12 m8 offset-m2">
      <form action="<?php echo PAYPAL_URL; ?>" class="EditForm" method="post" style="width: 100%;">

        <label class="brand-text bold">Shipping Address: </label>
        <input disabled value="<?php echo htmlspecialchars($address['ADDRESS1']); ?>" id="disabled" type="text" class="validate">
        <label>Postal Code: </label>
        <input disabled value="<?php echo htmlspecialchars($address['POSTALCD1']); ?>" id="disabled" type="text" class="validate">
        <label>Country: </label>
        <input disabled value="<?php echo htmlspecialchars($address['COUNTRY1']); ?>" id="disabled" type="text" class="validate">

        <input type="hidden" name="business" value="<?php echo PAYPAL_ID; ?>">
        <input type="hidden" name="cmd" value="_xclick">

        <!-- Specify details about the item that buyers will purchase. -->
        <input type="hidden" name="item_name" value="<?php echo $totalName; ?>">
        <input type="hidden" name="item_number" value="<?php echo $totalQty; ?>">
        <input type="hidden" name="amount" value="<?php echo $totalPrice; ?>">
        <input type="hidden" name="charset" value="utf-8">
        <input type="hidden" name="currency_code" value="<?php echo PAYPAL_CURRENCY; ?>">

        <!-- Specify URLs -->
        <input type="hidden" name="notify_url" value="<?php echo PAYPAL_NOTIFY_URL; ?>">
        <input type="hidden" name="rm" value="2">
        <input type="hidden" name="return" value="http://localhost:8090/paypal/success.php?tid=<?php echo $tid; ?>">
        <input type="hidden" name="cancel_return" value="<?php echo PAYPAL_CANCEL_URL; ?>">

        <div class="center">
          <input type="image" name="submit" style="width: 150px; margin-top: 15px;" src="https://www.paypalobjects.com/webstatic/en_US/i/btn/png/btn_buynow_107x26.png">
        </div>

      </form>
    </div>
  </div>

</div>

<?php include("templates/footer.php"); ?>

</html>