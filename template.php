<?php
include("templates/header.php");
include("config/db_connect.php");

date_default_timezone_set("Singapore");
$phpDate = date("Y-m");
$jsDate = strtotime($phpDate) * 1000;

$dataPoints = array(
    array("x" => $jsDate, "y" => 1)
);

if (!empty($_GET['oid'])) {
    echo "got it!";
} else {
    echo "not it!";
}


?>

<!DOCTYPE HTML>
<html>

<div class="container black">
    <div class="row">
        <div class="col m4 s6">
            <div class="card z-depth-0 center">
                <div class="card-content">
                    <div class="center">
                        <img src="/img/avatar-bg.png" class="product-icon">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include("templates/footer.php"); ?>
</div>

</html>