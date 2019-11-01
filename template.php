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
<!DOCTYPE HTML>
<html>

<head>

    <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>

</head>

<script>
    window.onload = function() {

        var chart = new CanvasJS.Chart("chartContainer", {
            animationEnabled: true,
            title: {
                text: "Usage Share of Desktop Browsers"
            },
            subtitles: [{
                text: "November 2017"
            }],
            data: [{
                type: "pie",
                yValueFormatString: "#,##0.00\"%\"",
                indexLabel: "{label} ({y})",
                dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
            }]
        });
        chart.render();
    }
</script>

<body>
    <div class="container center">
        <div class="card z-depth-0 white">
            <div class="card-content">

                <div id="chartContainer" style="height: 370px; width: 50%;"></div>
            </div>
        </div>
    </div>
</body>

<?php include("templates/footer.php"); ?>

</html>