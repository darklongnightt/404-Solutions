<?php
include("config/db_connect.php");
include("templates/header.php");
include_once 'paypal/config.php';

// Checks on message notification to display
if (isset($_SESSION['LASTACTION'])) {
  if ($_SESSION['LASTACTION'] == 'ADDRESS') {
    echo "<script>M.toast({html: 'Successfully created shipping gaddress!'});</script>";
    unset($_SESSION['LASTPAGE']);
  }

  $_SESSION['LASTACTION'] = 'NONE';
}

$totalPrice = $totalQty = 0;
$totalName = $tid = $token = $error = '';

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

if (isset($_GET['token'])) {
  $token = $_GET['token'];
}

// Check that pay button is pressed
if (isset($_POST['submit'])) {
  // Specify paypal store id
  $query = array();
  $query['cmd'] = '_xclick';
  $query['business'] = PAYPAL_ID;

  // Specify item details
  $query['item_name'] = $totalName;
  $query['amount'] = $totalPrice;
  $query['item_number'] = $totalQty;
  $query['charset'] = "utf-8";
  $query['currency_code'] = PAYPAL_CURRENCY;

  // Specify URLs
  $query['notify_url'] = PAYPAL_NOTIFY_URL;
  $query['rm'] = 2;
  $query['return'] = 'https://super-data-fyp.appspot.com/paypal/success.php?tid=' . $tid;
  $query['cancel_return'] = PAYPAL_CANCEL_URL;

  // Security validation: rebuild hash from the query for integrity check
  $rebuiltToken = hash('sha256', $query['item_name'] . $query['amount'] . $query['item_number'] . $_SESSION['SERVERSECRET']);
  if ($rebuiltToken == $token) {
    echo "<script type='text/javascript'>window.top.location='" . PAYPAL_URL . "?" . http_build_query($query) . "';</script>";
  } else {
    // Warn user on on attemp to later item price
    $error = "Warning: Malicious attempt to alter item details has been detected!";
    $_SESSION['BADATTEMPTS'] += 1;

    // Log user out if attempts more than 3 tries, preventing bruteforce for session server secret
    if ($_SESSION['BADATTEMPTS'] > 3) {
      echo "<script type='text/javascript'>window.top.location='/authentication/logout.php';</script>";
    }
  }
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
      <form class="EditForm" method="post" style="width: 100%;">

        <label class="brand-text bold">Shipping Address: </label>
        <input disabled value="<?php echo htmlspecialchars($address['ADDRESS1']); ?>" id="disabled" type="text" class="validate">
        <label>Postal Code: </label>
        <input disabled value="<?php echo htmlspecialchars($address['POSTALCD1']); ?>" id="disabled" type="text" class="validate">
        <label>Country: </label>
        <input disabled value="<?php echo htmlspecialchars($address['COUNTRY1']); ?>" id="disabled" type="text" class="validate">

        <div class="red-text center"><?php echo $error; ?></div>

        <button type="submit" name="submit" class="btn-small action-btn brand z-depth-0">
          Pay now
        </button>
      </form>
    </div>
  </div>

</div>

<?php include("templates/footer.php"); ?>

</html>