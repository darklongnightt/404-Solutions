<?php
include("../config/db_connect.php");
include("../templates/header.php");

// Fetch all customers
$sql = "SELECT USERID FROM customer WHERE CLUSTER <> 1";
$result = mysqli_query($conn, $sql);
$cusid = mysqli_fetch_all($result, MYSQLI_ASSOC);
$csize = sizeof($cusid);

// Fetch products from category
$sql = "SELECT * FROM product WHERE CATEGORY <> 'BEVERAGE'";
$result = mysqli_query($conn, $sql);
$products = mysqli_fetch_all($result, MYSQLI_ASSOC);
$psize = sizeof($products);

date_default_timezone_set("Singapore");
$start = '2017-01-16 13:38:00';
$add = date('Y-m-d h:i:s', strtotime($start . ' + 1 day'));
$end =  date("Y-m-d h:i:s");

while (strtotime($add) < strtotime($end)) {
    $add = date('Y-m-d h:i:s', strtotime($add . ' + 1 day'));
    $cindex = random_int(0, $csize - 1);
    $pindex = random_int(0, $psize - 1);
    $cid = $cusid[$cindex]['USERID'];
    $product = $products[$pindex];
    $transactionId = uniqid('ORD');

    // Get product id, quantity, price, discountprice, netprice
    $quantity = random_int(1, 3);
    $pid = $product['PDTID'];
    $price = number_format($product['PDTPRICE'] * $quantity, 2, '.', '');
    $discount = number_format($price / 100 * $product['PDTDISCNT'], 2, '.', '');
    $netprice = number_format($price - $discount, 2, '.', '');

    // Insert into orders table
    $sql = "INSERT INTO orders(TRANSACTIONID, PDTID, USERID, ORDERQTY, PMENTTYPE, TTLPRICE, TTLDISCNTPRICE, NETPRICE, PCHASEDATE, DELVRYDATE)
VALUES('$transactionId', '$pid', '$cid', '$quantity', 'PayPal', '$price', '$discount', '$netprice', '$add', '$add')";
    // Check if insert statement returns an error
    if (!mysqli_query($conn, $sql)) {
        echo 'Error: ' . mysqli_error($conn);
        exit();
    }
}




mysqli_free_result($result);
mysqli_close($conn);
?>

<!DOCTYPE HTML>
<html>

<h4 class="center grey-text">Successfully tuned orders data in database!</h4>

<?php include("../templates/footer.php"); ?>

</html>