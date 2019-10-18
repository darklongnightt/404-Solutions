<?php
include("config/db_connect.php");
include("templates/header.php");

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
</div>

<?php include("templates/footer.php"); ?>

</html>