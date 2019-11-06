<?php
include("../config/db_connect.php");
include("../templates/header.php");

// Get product id from link or form submit
$pid = $pdt = '';
if (isset($_POST['product'])) {
    $pid = $_POST['product'];
} else if (isset($_GET['pid'])) {
    $pid = $_GET['pid'];
}

// Fetch demand forecast
$sql = "SELECT * FROM demand_forecast WHERE PDTID='$pid' ORDER BY CREATED_AT DESC";
$result = mysqli_query($conn, $sql);
$demand = mysqli_fetch_assoc($result);

$sql = "SELECT COUNT(*) FROM orders";
$result = mysqli_query($conn, $sql);
$orders_count = mysqli_fetch_assoc($result)['COUNT(*)'];

// Fetch all products 
$sql = "SELECT * FROM product";
$result = mysqli_query($conn, $sql);
$products = mysqli_fetch_all($result, MYSQLI_ASSOC);
$product_count = sizeof($products);
foreach ($products as $product) {
    if ($product['PDTID'] == $pid) {
        $pdt = $product;
    }
}

// Get today's date
date_default_timezone_set('Singapore');
$today = date('d-m-Y', time());

// Split table into past data and predicted data
$past_timesteps = array();
$past_demands = array();
$predicted_timesteps = array();
$predicted_demands = array();

$timesteps = explode('|', $demand['TIMESTEP']);
$demands = explode('|', $demand['DEMAND']);

// Set values for graph
$dataPoints = array();
$dataPoints1 = array();

for ($i = 0; $i < sizeof($timesteps); $i++) {
    if ($timesteps[$i] != '') {

        $formatted = date("F Y", strtotime($timesteps[$i]));
        $jsDate = strtotime($formatted) * 1000;
        $data = array("x" => $jsDate, "y" => $demands[$i]);

        if (date("Y-m", strtotime($timesteps[$i])) == date("Y-m", strtotime(("-1 months")))) {
            array_push($dataPoints1, $data);
        }

        if (date("Y-m", strtotime($timesteps[$i])) >= date("Y-m", strtotime($today))) {

            array_push($predicted_timesteps, $formatted);
            array_push($predicted_demands, $demands[$i]);

            // Set values for graph
            array_push($dataPoints1, $data);
        } else {
            array_push($past_timesteps, $formatted);
            array_push($past_demands, $demands[$i]);

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
                text: "Product Monthly Demand",
                fontFamily: "Montserrat"
            },
            axisY: {
                title: "Demand (QTY)",
                prefix: ""
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
                    yValueFormatString: "#,##0.##",
                    xValueType: "dateTime",
                    dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
                },
                {
                    type: "line",
                    markerSize: 5,
                    lineColor: "red",
                    markerColor: "black",
                    xValueFormatString: "YYYY-MMM",
                    yValueFormatString: "#,##0.##",
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
        margin-top: 25px;
    }

    .title-label {
        border-radius: 25px;
        background: grey;
        padding: 10px;
    }

    .pdt-icon {
        max-height: 100px;
        width: auto;
        max-width: 150px;
        margin-top: 10px;
    }

    .taller-card {
        height: 320px;
    }
</style>

<!DOCTYPE HTML>
<html>
<h4 class="grey-text center">Demand Forecast Report</h4>
<div class="container">
    <div class="row">
        <div class="col m4 s8">
            <div class="card z-depth-0 taller-card">
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
            <div class="card z-depth-0 taller-card">
                <div class="card-content center">
                    <h6 class="brand-text bold">Method: <?php echo $demand['METHOD']; ?></h6>
                    <h6 class="brand-text bold">Output: Predicted Monthly Demand (QTY)</h6>
                    <img src="../img/demand_forecast.png" class="method-icon">
                </div>
            </div>
        </div>

        <div class="col m4 s8">
            <div class="card z-depth-0 taller-card">
                <div class="card-content">

                    <h6 class="bold brand-text">Algorithm Inputs</h6>
                    <ul>
                        <li style="list-style-type: initial; margin-left: 15px">Transactional Quantity</li>
                        <li style="list-style-type: initial; margin-left: 15px">Transactional Date Time</li>
                        <li style="list-style-type: initial; margin-left: 15px">Select Product: </li>

                        <form action="demand_report.php" method="POST" style="margin-bottom: 5px;">
                            <select class="browser-default" name="product" onchange='this.form.submit()'>
                                <?php foreach ($products as $product) {
                                    $name = $product['PDTNAME'];
                                    $id = $product['PDTID'];
                                    ?>

                                    <option value="<?php echo  $id; ?>" <?php if ($pid == $id) echo ' selected'; ?>><?php echo  htmlspecialchars($name); ?></option>
                                <?php } ?>
                            </select>
                            <noscript><input type="submit" value="submit"></noscript>
                        </form>

                        <?php if ($pdt) { ?>
                            <div class="center">
                                <a href="/products/product_details.php?id=<?php echo $pdt['PDTID']; ?>">
                                    <img src="<?php echo $pdt['IMAGE']; ?>" class="pdt-icon">
                                </a>
                            </div>
                        <?php } ?>

                    </ul>

                </div>
            </div>
        </div>
    </div>

    <?php if ($predicted_timesteps) : ?>

        <div class="row">
            <div class="col m12 s24">
                <div class="card center">
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
                        <h5 class="center bold blue-text">Past Sales Quantity</h5>
                        <table class="responsive-table centered highlight">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Demand</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                    for ($i = 0; $i < sizeof($past_demands); $i++) {
                                        echo '<tr>';
                                        echo "<td>$past_timesteps[$i]</td>";
                                        echo "<td>$past_demands[$i]</td>";
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
                        <h5 class="center bold red-text">Forecasted Demand</h5>
                        <table class="responsive-table centered highlight">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Demand</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                    for ($i = 0; $i < sizeof($predicted_demands); $i++) {
                                        echo '<tr>';
                                        echo "<td>$predicted_timesteps[$i]</td>";
                                        echo "<td>$predicted_demands[$i]</td>";
                                    }
                                    ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    <?php else : ?>
        <div class="center bold red-text">No Demand Forecast Report Available For This Product!</div>
    <?php endif ?>

</div>

<?php

include("../templates/footer.php");
?>

</html>