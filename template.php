<?php
include("config/db_connect.php");
include("templates/header.php");
$link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ?
    "https" : "http") . "://" . $_SERVER['HTTP_HOST'] .
    $_SERVER['REQUEST_URI'];

$a = 50;
$b = 25;
$c = 88;
?>

<style>
    .graph-cont {
        width: calc(100% - 40px);
        width: 100%;
        max-width: 800px;
        margin: 0 auto;
        z-index: 1;
    }

    .bar {
        height: 20px;
        width: 200px;
        margin: 0 auto 10px auto;
        line-height: 20px;
        font-size: 16px;
        color: black;
        padding: 0 0 0 10px;
        position: relative;
        display: inline-block;
    }

    .bar::before {
        content: '';
        width: 100%;
        position: absolute;
        left: 0;
        height: 20px;
        top: 0;
        z-index: -2;
        background: #ecf0f1;
    }

    .bar::after {
        content: '';
        background: gold;
        height: 20px;
        transition: 0.7s;
        display: block;
        width: 100%;
        -webkit-animation: bar-before 1 1.8s;
        position: absolute;
        top: 0;
        left: 0;
        z-index: -1;
    }

    @-webkit-keyframes bar-before {
        0% {
            width: 0px;
        }

        100% {
            width: 100%;
        }
    }

    .bar1::after {
        max-width: <?php echo $a; ?>%;
    }

    .bar2::after {
        max-width: <?php echo $b; ?>%;
    }

    .bar3::after {
        max-width: <?php echo $c; ?>%;
    }
</style>

<!DOCTYPE HTML>
<html>

<div class="card" style="z-index: -3;">
    4<span class="bar bar1" style="width: 200px; ">5: 10%</span>4
    <div class="bar bar2">4: 10%</div>
    <div class="bar bar3">3: 10%</div>
</div>


<?php include("templates/footer.php"); ?>

</html>