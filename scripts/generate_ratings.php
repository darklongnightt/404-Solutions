<?php
include("../config/db_connect.php");
include("../templates/header.php");

// Fetch all customers
$sql = "SELECT USERID FROM customer";
$result = mysqli_query($conn, $sql);
$customers = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Fetch all customers
$sql = "SELECT PDTID FROM product";
$result = mysqli_query($conn, $sql);
$products = mysqli_fetch_all($result, MYSQLI_ASSOC);


foreach ($products as $product) {
    // Loop through all product id
    $pid = $product['PDTID'];

    // Generate n number of reviews
    $reviewCount = random_int(6, 37);
    for ($i = 0; $i < $reviewCount; $i++) { }
}

// Randomly select a customer id for review
$custIndex = random_int(0, sizeof($customers) - 1);
$customerId = $customers[$custIndex]['USERID'];

// Randomly generate an rating type bad, average, good
$type = random_int(1, 100);
if ($type >= 1 && $type <= 60) {
    // Generate good rating (60%)
    $p = random_int(4, 5);
    $s = random_int(4, 5);
    $d = random_int(4, 5);
} else if ($type > 60 && $type < 90) {
    // Generate average rating (30%)
    $p = random_int(2, 4);
    $s = random_int(2, 4);
    $d = random_int(2, 4);
} else {
    // Generate bad rating (10%)
    $p = random_int(1, 3);
    $s = random_int(1, 3);
    $d = random_int(1, 3);
}

$reviewid = uniqid('RNR');

// Select random comment
$selectComment = random_int(0, 100);

$sql = "INSERT INTO review(REVIEWID, PDTID, USERID, PRATING, SRATING, DRATING, COMMENT) 
VALUES ('$reviewid', '$pid', '$customerId', '$p', '$s', '$d', '$selectComment')";

echo $sql;
mysqli_free_result($result);
mysqli_close($conn);
?>

<!DOCTYPE HTML>
<html>




<?php include("../templates/footer.php"); ?>

</html>