<?php
include("config/db_connect.php");
include("templates/header.php");
$link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ?
    "https" : "http") . "://" . $_SERVER['HTTP_HOST'] .
    $_SERVER['REQUEST_URI'];
?>

<!DOCTYPE HTML>
<html>




<?php include("templates/footer.php"); ?>
</html>