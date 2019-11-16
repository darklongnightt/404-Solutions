<?php
include("../config/db_connect.php");
include("../templates/header.php");

// Access Control Check
if (substr($uid, 0, 3) != 'ADM') {
    echo "<script type='text/javascript'>window.top.location='/index.php';</script>";
}

// Fetch all customers
$sql = "SELECT * FROM promotion";
$result = mysqli_query($conn, $sql);
$products = mysqli_fetch_all($result, MYSQLI_ASSOC);
$n = sizeof($products);
echo $n;

// For each product, reformat the image url for public view
foreach ($products as $product) {
    $pid = $product['PROMOCODE'];
    $img = $product['IMAGE'];

    $url = str_replace("https://storage.cloud.google.com", "https://storage.googleapis.com", $img);
    $url = str_replace("?cloudshell=false", "", $url);
    echo $url  . '<br>';

    $sql = "UPDATE promotion SET IMAGE='$url' WHERE PROMOCODE = '$pid'";
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