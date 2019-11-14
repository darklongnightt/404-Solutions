<?php
include("../config/db_connect.php");
include("../templates/header.php");

// Access Control Check
if (substr($uid, 0, 3) != 'ADM') {
    echo "<script type='text/javascript'>window.top.location='/index.php';</script>";
}

// Fetch all customers
$sql = "SELECT COUNT(*), customer.CLUSTER 
FROM customer JOIN orders ON customer.USERID = orders.USERID 
GROUP BY customer.CLUSTER";
$result = mysqli_query($conn, $sql);
$stats = mysqli_fetch_all($result, MYSQLI_ASSOC);
print_r($stats);

// Fetch products from category
$sql = "SELECT * FROM product WHERE CATEGORY='HOUSEHOLD CONSUMMABLES' OR CATEGORY='TOILETRIES' 
OR CATEGORY='TEXTILE' OR CATEGORY='FROZEN FOOD' OR CATEGORY='BAZAAR'";
$result = mysqli_query($conn, $sql);
$products = mysqli_fetch_all($result, MYSQLI_ASSOC);
$n = sizeof($products);

// Fetch orders -> 100k
$sql = "SELECT * FROM orders JOIN customer ON orders.USERID = customer.USERID 
WHERE customer.CLUSTER = 2 AND SUBSTRING(orders.PDTID, 1, 2) = 'FR'";
$result = mysqli_query($conn, $sql);
$orders = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Cluster 2 -> 3.9K tidbits
for ($i = 0; $i < 4600; $i++) {
    // Assoc random product to order
    $orderid = $orders[$i]['ORDERID'];
    $index = random_int(0, $n - 1);
    $product = $products[$index];

    // Get product id, quantity, price, discountprice, netprice
    $quantity = 1;
    $pid = $product['PDTID'];
    $price = number_format($product['PDTPRICE'] * $quantity, 2, '.', '');
    $discount = number_format($price / 100 * $product['PDTDISCNT'], 2, '.', '');
    $netprice = number_format($price - $discount, 2, '.', '');

    $sql = "UPDATE orders SET PDTID='$pid', ORDERQTY='$quantity', TTLPRICE='$price', 
    TTLDISCNTPRICE='$discount', NETPRICE='$netprice' WHERE ORDERID='$orderid'";
    if (!mysqli_query($conn, $sql)) {
        echo 'Query Error: ' . mysqli_error($conn);
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