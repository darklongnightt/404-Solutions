<?php
include("../config/db_connect.php");
include("../templates/header.php");

// Fetch product recommendations
$sql = "SELECT * FROM revenue_forecast ORDER BY CREATED_AT DESC";
$result = mysqli_query($conn, $sql);
$revenue = mysqli_fetch_all($result, MYSQLI_ASSOC)[0];

$sql = "SELECT COUNT(*) FROM product";
$result = mysqli_query($conn, $sql);
$product_count = mysqli_fetch_assoc($result)['COUNT(*)'];

$sql = "SELECT COUNT(*) FROM orders";
$result = mysqli_query($conn, $sql);
$orders_count = mysqli_fetch_assoc($result)['COUNT(*)'];

// Get today's date
date_default_timezone_set('Singapore');
$today = date('d-m-Y', time());

// Split table into past data and predicted data
$past_timesteps = array();
$past_profits = array();
$predicted_timesteps = array();
$predicted_profits = array();

$timesteps = explode('|', $revenue['TIMESTEP']);
$profits = explode('|', $revenue['REVENUE']);

// Set values for graph
$dataPoints = array();
$dataPoints1 = array();

for ($i = 0; $i < sizeof($timesteps); $i++) {
    if ($timesteps[$i] != '') {

        $formatted = date("F Y", strtotime($timesteps[$i]));
        $jsDate = strtotime($formatted) * 1000;
        $data = array("x" => $jsDate, "y" => $profits[$i]);

        if (date("Y-m", strtotime($timesteps[$i])) == date("Y-m", strtotime(("-1 months")))) {
            array_push($dataPoints1, $data);
        }

        if (date("Y-m", strtotime($timesteps[$i])) >= date("Y-m", strtotime($today))) {

            array_push($predicted_timesteps, $formatted);
            array_push($predicted_profits, $profits[$i]);

            // Set values for graph
            array_push($dataPoints1, $data);
        } else {
            array_push($past_timesteps, $formatted);
            array_push($past_profits, $profits[$i]);

            // Set values for graph
            array_push($dataPoints, $data);
        }
    }
}

// Free memory of result and close connection
mysqli_free_result($result);
mysqli_close($conn);
?>

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
                prefix: "$"
            },
            axisX: {
                valueFormatString: "YYYY-MMM"
            },
            data: [{
                    type: "line",
                    markerSize: 5,
                    lineColor: "#2196f3",
                    markerColor: "black",
                    xValueFormatString: "YYYY-MMM",
                    yValueFormatString: "$#,##0.##",
                    xValueType: "dateTime",
                    dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
                },
                {
                    type: "line",
                    markerSize: 5,
                    lineColor: "red",
                    markerColor: "black",
                    xValueFormatString: "YYYY-MMM",
                    yValueFormatString: "$#,##0.##",
                    xValueType: "dateTime",
                    dataPoints: <?php echo json_encode($dataPoints1, JSON_NUMERIC_CHECK); ?>
                }
            ]
        });

        chart.render();

    }
</script>

<style>
    .method-icon {
        width: 170px;
        height: auto;
    }

    .title-label {
        border-radius: 25px;
        background: grey;
        padding: 10px;
    }
</style>

<!DOCTYPE HTML>
<html>
<h4 class="grey-text center">Revenue Forecast Report</h4>
<div class="container">
    <div class="row">
        <div class="col m4 s8">
            <div class="card z-depth-0 small">
                <div class="card-content center">
                    <h4 class="red-text bold center">
                        <?php echo $product_count; ?>
                    </h4>
                    <div class="bold center"> Products </div>
                    <br>
                    <div class="divider"></div>
                    <h4 class="red-text bold center">
                        <?php echo $orders_count; ?>
                    </h4>
                    <div class="bold center"> Transactions </div>
                </div>
            </div>
        </div>

        <div class="col m4 s8">
            <div class="card z-depth-0 small">
                <div class="card-content center">
                    <h6 class="brand-text bold">Method: <?php echo $revenue['METHOD']; ?></h6>
                    <h6 class="brand-text bold">Output: Predicted Monthly Revenue (SGD)</h6>
                    <img src="../img/revenue1.png" class="method-icon">
                </div>
            </div>
        </div>

        <div class="col m4 s8">
            <div class="card z-depth-0 small">
                <div class="card-content">

                    <h6 class="bold brand-text">Algorithm Inputs</h6>
                    <ul>
                        <li style="list-style-type: initial; margin-left: 15px">Transactional Profits <br>(Net Price - Cost Price)</li>
                        <li style="list-style-type: initial; margin-left: 15px">Transactional Date Time</li>
                    </ul>

                </div>
            </div>
        </div>
    </div>

    <?php if ($revenue) : ?>

        <div class="row">
            <div class="col m12 s24">
                <div class="card z-depth-0 center">
                    <div class="card-content">

                        <div id="chartContainer" style="height: 400px; width: 100%;"></div>
                        <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>

                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col s12 m6">
                <div class="card z-depth-0">
                    <div class="card-content">
                        <h5 class="center bold blue-text">Past Revenue</h5>
                        <table class="responsive-table centered highlight">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                    for ($i = 0; $i < sizeof($past_profits); $i++) {
                                        echo '<tr>';
                                        echo "<td>$past_timesteps[$i]</td>";
                                        echo "<td>$past_profits[$i]</td>";
                                    }
                                    ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col s12 m6">
                <div class="card z-depth-0">
                    <div class="card-content">
                        <h5 class="center bold red-text">Forecasted Revenue</h5>
                        <table class="responsive-table centered highlight">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                    for ($i = 0; $i < sizeof($predicted_profits); $i++) {
                                        echo '<tr>';
                                        echo "<td>$predicted_timesteps[$i]</td>";
                                        echo "<td>$predicted_profits[$i]</td>";
                                    }
                                    ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    <?php else : ?>
        <div>No Revenue Forecast Report Generated!</div>
    <?php endif ?>

</div>

<?php

include("../templates/footer.php");
?>

</html>