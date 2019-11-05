<?php
include("../config/db_connect.php");
include("../templates/header.php");

// Fetch all customers
$sql = "SELECT * FROM product";
$result = mysqli_query($conn, $sql);
$products = mysqli_fetch_all($result, MYSQLI_ASSOC);
$n = sizeof($products);
echo $n;

for ($i = 0; $i < $n; $i++) {
    $deleteIndex = random_int(0, $n - 3);
    $pid = $products[$deleteIndex]['PDTID'];
    $qty = random_int(44, 344);
    $sql = "UPDATE product SET PDTQTY='$qty' WHERE PDTID = '$pid'";

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

<h4 class="center grey-text">Successfully tuned products data in database!</h4>

<?php include("../templates/footer.php"); ?>

</html>