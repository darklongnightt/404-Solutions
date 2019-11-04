<?php
include("templates/header.php");
include("config/db_connect.php");

date_default_timezone_set("Singapore");
$phpDate = date("Y-m");
$jsDate = strtotime($phpDate) * 1000;

$dataPoints = array(
    array("x" => $jsDate, "y" => 1)
);

?>

<head>
</head>

<script>
    window.onload = function() {

        var chart = new CanvasJS.Chart("chartContainer", {
            animationEnabled: true,
            title: {
                text: "SuperData Monthly Revenue",
                fontFamily: "Montserrat"
            },
            axisY: {
                title: "Revenue (SGD)",
                valueFormatString: "#0,,.",
                suffix: "mn",
                prefix: "$"
            },
            axisX: {
                valueFormatString: "YYYY-MMMM"
            },
            data: [{
                type: "spline",
                markerSize: 5,
                xValueFormatString: "YYYY-MMMM",
                yValueFormatString: "$#,##0.##",
                xValueType: "dateTime",
                dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
            }]
        });

        chart.render();

    }
</script>

<!DOCTYPE HTML>
<html>

<div class="row">
    <div class="col m12 s24">
        <div class="card z-depth-0 center">
            <div class="card-content">
                <div id="chartContainer" style="height: 340px; width: 100%;"></div>
                <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>

            </div>
        </div>
    </div>
</div>

<?php include("templates/footer.php"); ?>

</html>