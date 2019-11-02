<?php
include("../config/db_connect.php");
include("../templates/header.php");

// Fetch all reviews
$sql = "SELECT DISTINCT(PDTID) FROM review";
$result = mysqli_query($conn, $sql);
$ratings = mysqli_fetch_all($result, MYSQLI_ASSOC);

foreach ($ratings as $rating) {
    // Get category for this product
    $pid = $rating['PDTID'];
    $sql = "SELECT CATEGORY FROM product WHERE PDTID='$pid'";
    $result = mysqli_query($conn, $sql);
    $category = mysqli_fetch_assoc($result)['CATEGORY'];

    // Update reviews
    $sql = "UPDATE review SET CATEGORY='$category' WHERE PDTID='$pid'";
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

<h4 class="center grey-text">Successfully tuned reviews data in database!</h4>

<?php include("../templates/footer.php"); ?>

</html>