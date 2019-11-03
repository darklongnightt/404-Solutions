<?php
include("templates/header.php");
include("config/db_connect.php");

$dataPoints = array(
    array("label" => "Chrome", "y" => 64.02),
    array("label" => "Firefox", "y" => 12.55),
    array("label" => "IE", "y" => 8.47),
    array("label" => "Safari", "y" => 6.08),
    array("label" => "Edge", "y" => 4.29),
    array("label" => "Others", "y" => 4.59)
);

?>

<head>
    <link rel="stylesheet" href="/css/timeline.css" type="text/css">
</head>

<!DOCTYPE HTML>
<html>

<div class="row">
    <div class="col m6 s12">

        <div class="card z-depth-0">
            <div class="card-content">
                <ol class="tl">
                    <li class="element">
                        <p class="status"><i class="fa fa-shopping-cart" aria-hidden="true"></i></p>
                        <span class="point">
                        </span>

                    </li>
                    <li class="element">
                        <p class="status"><i class="fa fa-credit-card" aria-hidden="true"></i></p>
                        <span class="active-point">
                            <p class="description">Confirmed Payment</p>
                        </span>
                    </li>
                    <li class="element">
                        <p class="status"><i class="fa fa-truck" aria-hidden="true"></i></p>
                        <span class="point">
                        </span>
                    </li>
                    <li class="element">
                        <p class="status"><i class="fa fa-archive" aria-hidden="true"></i>
                        </p>
                        <span class="point">
                        </span>
                    </li>
                    <li class="element">
                        <p class="status"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></p>
                        <span class="point">
                        </span>
                    </li>
                </ol>
            </div>
        </div>



    </div>
</div>

<?php include("templates/footer.php"); ?>

</html>