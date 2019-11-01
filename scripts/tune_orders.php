<?php
include("../config/db_connect.php");
include("../templates/header.php");

// Fetch all customers
$sql = "SELECT COUNT(*), customer.CLUSTER 
FROM customer JOIN orders ON customer.USERID = orders.USERID 
GROUP BY customer.CLUSTER";
$result = mysqli_query($conn, $sql);
$stats = mysqli_fetch_all($result, MYSQLI_ASSOC);
print_r($stats);

// Fetch all customers
$sql = "SELECT COUNT(*), CLUSTER FROM customer
GROUP BY CLUSTER";
$result = mysqli_query($conn, $sql);
$stats = mysqli_fetch_all($result, MYSQLI_ASSOC);
echo '<br>';
print_r($stats);

// Fetch all customers
$sql = "SELECT customer.CLUSTER, customer.USERID, orders.ORDERID, orders.TRANSACTIONID 
FROM customer JOIN orders ON customer.USERID = orders.USERID 
WHERE customer.CLUSTER = 1";
$result = mysqli_query($conn, $sql);
$orders = mysqli_fetch_all($result, MYSQLI_ASSOC);
$n = sizeof($orders);
echo $n;

for ($i = 0; $i < 2000; $i++) {
    $deleteIndex = random_int(0, $n - 3);
    $oid = $orders[$deleteIndex]['ORDERID'];
    $sql = "DELETE FROM orders WHERE ORDERID = '$oid'";
    if (!mysqli_query($conn, $sql)) {
        echo "Query Error: " . mysqli_error($conn);
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