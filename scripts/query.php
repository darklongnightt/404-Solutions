<?php
include("../config/db_connect.php");
include("../templates/header.php");

// Fetch all customers
$sql = "SELECT * FROM orders WHERE USERID='$uid' ORDER BY PCHASEDATE DESC";
$result = mysqli_query($conn, $sql);
$orders = mysqli_fetch_all($result, MYSQLI_ASSOC);

print_r($orders);

mysqli_free_result($result);
mysqli_close($conn);
?>

<!DOCTYPE HTML>
<html>

<h4 class="center grey-text">Query Finished</h4>

<?php include("../templates/footer.php"); ?>

</html>