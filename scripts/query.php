<?php
include("../config/db_connect.php");
include("../templates/header.php");

// Access Control Check
if (substr($uid, 0, 3) != 'ADM') {
    echo "<script type='text/javascript'>window.top.location='/index.php';</script>";
}

// Fetch all customers
$sql = "ALTER TABLE address MODIFY COLUMN POSTALCD2 VARCHAR (16);";
$result = mysqli_query($conn, $sql);
if($result) {
    echo 'yes';
} else {
    echo 'no';
}
//$orders = mysqli_fetch_all($result, MYSQLI_ASSOC);

//print_r($orders);

mysqli_free_result($result);
mysqli_close($conn);
?>

<!DOCTYPE HTML>
<html>

<h4 class="center grey-text">Query Finished</h4>

<?php include("../templates/footer.php"); ?>

</html>