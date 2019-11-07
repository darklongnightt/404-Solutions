<?php
include("../config/db_connect.php");
include('../templates/header.php');

// Check for toast message
if (isset($_SESSION['LASTACTION'])) {
    if ($_SESSION['LASTACTION'] == 'UPDATESTOCK') {
        echo "<script>M.toast({html: 'Successfully updated product stock!'});</script>";
    }
    $_SESSION['LASTACTION'] = 'NONE';
}

// Get upcoming forecast values 
$month = $predictedQty = '';
if (isset($_POST['forecast'])) {
    $month = $_POST['forecast'];

    // Get demand forecasts
    $sql = "SELECT * FROM demand_forecast";
    $result = mysqli_query($conn, $sql);
    $demands = mysqli_fetch_all($result, MYSQLI_ASSOC);

    foreach ($demands as $demand) {
        $pid = $demand['PDTID'];
        $timesteps = explode('|', $demand['TIMESTEP']);
        $demands = explode('|', $demand['DEMAND']);

        for ($i = 0; $i < sizeof($timesteps); $i++) {
            if ($timesteps[$i] != '') {

                // Get predicted value on selected date
                $formatted = date("F Y", strtotime($timesteps[$i]));
                if (strtotime($formatted) == strtotime($month)) {
                    $predictedQty = $demands[$i];
                }
            }
        }

        // Update product forecast and threshold
        $forecast = $month . '|' . $predictedQty;
        $sql = "UPDATE product SET THRESHOLD='$predictedQty', FORECAST='$forecast' WHERE PDTID='$pid'";
        if (!mysqli_query($conn, $sql)) {
            echo 'Query Error: ' . mysqli_error($conn);
        }
    }
}

// Init forecast dates to pick from 
date_default_timezone_set("Singapore");
$today = date('Y-m-d ');
$pickDates = array();
for ($i = 0; $i < 6; $i++) {
    $today = date('F Y', strtotime($today . ' + 1 month'));
    array_push($pickDates, $today);
}

// Store previously selected variables
$rangeCheck = $getSearchItem = $getSort = $getFilter = $rFilter = $ext = '';
$limit = TRUE;

// Pagination for all results
$currDir = "inventory_management.php";
$query = 'SELECT *, ROUND(PDTPRICE/ 100 * (100 - PDTDISCNT),2) AS NETPRICE FROM product';

// Get all product categories for filtering
$getCat = "SELECT DISTINCT CATEGORY FROM product";
$catResult = mysqli_query($conn, $getCat);
$filterCat = mysqli_fetch_all($catResult, MYSQLI_ASSOC);

// Get min and max product price for range slider default
$getCatRange = "SELECT ROUND(MIN(PDTPRICE/ 100 * (100 - PDTDISCNT)),2) AS MINPRICE, ROUND(MAX(PDTPRICE/ 100 * (100 - PDTDISCNT)),2) AS MAXPRICE FROM product";
$result = mysqli_query($conn, $getCatRange);
$defaultRange = mysqli_fetch_assoc($result);
$minRange = $catMin = round($defaultRange['MINPRICE'], 2);
$maxRange = $catMax = round($defaultRange['MAXPRICE'], 2);

if (isset($_GET['submit'])) {
    // Get user's selection for sort, replace - with space for sql filter by category
    $getFilter = $_GET['Filter'];
    $rFilter = str_replace('-', ' ', $getFilter);
    $getSort = $_GET['sort'];

    // Get price range
    $getPriceR = str_replace('$', '', $_GET['priceRange']);
    $price = explode('-', $getPriceR);
    $minRange = round($price[0], 2);
    $maxRange = round($price[1], 2);

    //Get search
    $getSearchItem = $_GET['searchItem'];

    //Get range use check
    $rangeCheck = $_GET['check'];

    // Get user's selection for sort, replace - with space for sql filter by category
    $rFilter = str_replace('-', ' ', $getFilter);

    //If user uses search function
    if ($getSearchItem != null) {
        $query .= ' WHERE PDTNAME LIKE "%' . $getSearchItem .
            '%" OR CATEGORY LIKE "%' . $getSearchItem .
            '%" OR BRAND LIKE "%' . $getSearchItem .
            '%" OR PDTID LIKE "%' . $getSearchItem . '%"';

        $getCatRange .= ' WHERE PDTNAME LIKE "%' . $getSearchItem .
            '%" OR CATEGORY LIKE "%' . $getSearchItem .
            '%" OR BRAND LIKE "%' . $getSearchItem .
            '%" OR PDTID LIKE "%' . $getSearchItem . '%"';
    }

    // If user uses filter function
    if ($getFilter != "all") {
        $limit = FALSE;
        if ($getSearchItem != null) {
            // Price range by category
            $getCatRange .= ' AND CATEGORY = "' . $rFilter . '"';
            $query .= ' AND CATEGORY = "' . $rFilter . '"';
        } else {
            // Price range by category
            $getCatRange .= ' WHERE CATEGORY = "' . $rFilter . '"';
            $query .= ' WHERE CATEGORY = "' . $rFilter . '"';
        }
    }

    // Get price range for specific category
    $catResult = mysqli_query($conn, $getCatRange);
    $catPriceRange = mysqli_fetch_assoc($catResult);
    $catMin = round($catPriceRange['MINPRICE'], 2);
    $catMax = round($catPriceRange['MAXPRICE'], 2);

    // If user uses range
    if ($rangeCheck == 1) {
        //if minprice less than min category or more than max category
        if (($minRange < $catMin) || ($minRange > $catMax) || ($minRange == 0)) {
            $minRange = $catMin;
        }
        //if maxprice more than max category or less than min category
        if (($maxRange > $catMax) || ($maxRange < $catMin) || ($maxRange == 0)) {
            $maxRange = $catMax;
        }
    } else {
        $minRange = $catMin;
        $maxRange = $catMax;
    }

    $query .= ' HAVING NETPRICE BETWEEN ' . $minRange . ' AND ' . $maxRange;

    // If user use sort function
    if ($getSort != "default") {
        $query .= ' ORDER BY NETPRICE ASC'; // . $getSort;
    }
    // If user did not use sort function
    else {
        $query .= ' ORDER BY CREATED_AT DESC';
    }

    $ext = "&Filter=$rFilter&sort=$getSort&priceRange=$minRange-$maxRange&check=$rangeCheck&searchItem=$getSearchItem&submit=Search";
    $getFilter = str_replace(' ', '-', $rFilter);
} else {
    $query .= ' ORDER BY CREATED_AT DESC';
}

if (!$limit) {
    $startingLimit = 0;
}

// Pagination for results
include('../templates/pagination_query.php');
$query .= "\nLIMIT $startingLimit , $resultsPerPage";

// Getting data from table: product as associative array
$result = mysqli_query($conn, $query);
$productList = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Update product quantity
$updateqty = $pdtid = '';
if (isset($_POST['qtybutton'])) {
    if (!empty($_POST['updateqty'])) {
        $updateqty = mysqli_real_escape_string($conn, $_POST['updateqty']);
        $pdtid = mysqli_real_escape_string($conn, $_POST['updateid']);

        $sql = "UPDATE product SET PDTQTY = '$updateqty' WHERE PDTID = '$pdtid'";
        if (mysqli_query($conn, $sql)) {
            // Get current link
            $link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ?
                "https" : "http") . "://" . $_SERVER['HTTP_HOST'] .
                $_SERVER['REQUEST_URI'];

            $_SESSION['LASTACTION'] = 'UPDATESTOCK';
            echo "<script type='text/javascript'>window.top.location='$link';</script>";
        } else {
            echo 'Query Error: ' . mysqli_error($conn);
        }
    }
}

// Free memory of result and close connection
mysqli_free_result($result);
mysqli_close($conn);
?>

<!DOCTYPE HTML>
<html>
<h4 class="center grey-text">Inventory Management</h4>

<div class="sidebar sidebar-padding">
    <form id="sfform" name="sfform" method="get" action="inventory_management.php">
        <h6 class="grey-text">Category</h6>
        <select class="browser-default" name="Filter">
            <option value="all">All</option>
            <?php

            foreach ($filterCat as $filtered) {
                echo "<option value=" . str_replace(' ', '-', $filtered['CATEGORY']);
                if ($getFilter == str_replace(' ', '-', $filtered['CATEGORY'])) {
                    echo " selected";
                }
                echo ">" . $filtered['CATEGORY'] . "</option>";
            }
            ?>
        </select>
        <br>
        <h6 class="grey-text">Sort</h6>
        <select class="browser-default" name="sort">
            <option value="default" <?php if ($getSort == '') echo 'selected' ?>>Default</option>
            <option value="NETPRICE DESC" <?php if ($getSort == 'NETPRICE DESC') echo 'selected' ?>>Price - High to Low </option>
            <option value="NETPRICE ASC" <?php if ($getSort == 'NETPRICE ASC') echo 'selected' ?>>Price - Low to High</option>
            <option value="PDTDISCNT ASC" <?php if ($getSort == 'PDTDISCNT ASC') echo 'selected' ?>>Discount - Low to High</option>
            <option value="PDTDISCNT DESC" <?php if ($getSort == 'PDTDISCNT DESC') echo 'selected' ?>>Discount - High to Low</option>
            <option value="PDTQTY DESC" <?php if ($getSort == 'PDTQTY DESC') echo 'selected' ?>>Quantity - High to Low </option>
            <option value="PDTQTY ASC" <?php if ($getSort == 'PDTQTY ASC') echo 'selected' ?>>Quantity - Low to High</option>
        </select>
        <br>
        <h6 class="grey-text">Price Range</h6>
        <input type="text" name="priceRange" id="range" readonly>
        <div id="pRange"></div>
        <script>
            var priceMin = <?php echo json_encode($catMin); ?>;
            var priceMax = <?php echo json_encode($catMax); ?>;
            var postMin = <?php echo json_encode($minRange); ?>;
            var postMax = <?php echo json_encode($maxRange); ?>;
            var rangeCheck = <?php echo json_encode($rangeCheck); ?>;

            //if user click on range slider
            function clicked() {
                document.getElementById('testrange').value = 1;
            }
            document.getElementById('pRange').addEventListener("mousedown", clicked);

            $(function() {
                $("#pRange").slider({
                    range: true,
                    min: priceMin,
                    max: priceMax,
                    values: [postMin, postMax],
                    slide: function(event, ui) {
                        $("#range").val("$" + ui.values[0] + " - $" + ui.values[1]);
                    }
                });

                $("#range").val("$" + $("#pRange").slider("values", 0) +
                    " - $" + $("#pRange").slider("values", 1));
            });
        </script>
        <input type="text" name="check" id="testrange" <?php if ($rangeCheck != '') echo " value = '" . $rangeCheck . "'"; ?> hidden>
        <br>

        <h6 class="grey-text"> Search </h6>
        <input type="search" name="searchItem" <?php if ($getSearchItem != '') echo " value = '" . $getSearchItem . "'"; ?>>
        <button type="submit" name="submit" class="btn brand z-depth-0 btn-small" style="width: 230px;">Search</button>

    </form>
</div>

<div class="container" style="margin-left: 250px;">

    <div class="row">
        <div class="col m11 s12 offset-m1">
            <div class="card z-depth-0">
                <div class="card-content">

                    <form action="inventory_management.php" method="POST" style="margin-bottom: 5px;">
                        <h5><i class="fa fa-bar-chart" aria-hidden="true"></i> Set Threshold = Forecasted Quantity</h5>
                        <select class="browser-default" name="forecast">
                            <option value="" disabled selected>Select Month</option>
                            <?php foreach ($pickDates as $date) { ?>
                                <option value="<?php echo  $date; ?>" <?php if ($month == $date) echo ' selected'; ?>><?php echo  $date; ?></option>
                            <?php } ?>
                        </select>

                        <button type="submit" name="setforecast" value="submit" class="btn z-depth-0 brand center" style="margin-top: 15px; width: 20%;">
                            Apply
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col s22 m11 offset-m1">

            <ul class="collection">
                <?php foreach ($productList as $product) { ?>
                    <li class="collection-item avatar">

                        <div class="row">

                            <div class="col m4 s4">
                                <a href="product_details.php?id=<?php echo htmlspecialchars($product['PDTID']) ?>">

                                    <img src="<?php if ($product['IMAGE']) {
                                                        echo $product['IMAGE'];
                                                    } else {
                                                        echo 'img/product_icon1.svg';
                                                    } ?>" class="circle">
                                </a>

                                <div class="title black-text"><?php echo htmlspecialchars($product['PDTNAME'] . ' - ' . $product['WEIGHT']); ?></div>
                                <?php if ($product['PDTQTY'] <= $product['THRESHOLD']) { ?>
                                    <div class="red-text bold">Available: <span class="flow-text"><?php echo $product['PDTQTY']; ?></span></div>
                                <?php } else { ?>
                                    <div class="black-text">Available: <span class="flow-text"><?php echo $product['PDTQTY']; ?></span></div>
                                <?php } ?>
                                <div class="grey-text"><?php echo htmlspecialchars($product['BRAND']) . ' | ' . htmlspecialchars($product['CATEGORY']); ?></div>
                                <span class="grey-text"><?php echo htmlspecialchars(htmlspecialchars($product['PDTID'])); ?></span>
                            </div>

                            <div class="col m4 s4">
                                <?php if ($product['FORECAST']) {
                                        $prediction = explode('|', $product['FORECAST']);
                                        ?>
                                    <h6><i class="fa fa-eye" aria-hidden="true"></i> Forecast</h6>
                                    <div>
                                        <?php echo $prediction[0]; ?>
                                    </div>
                                    <div class="flow-text blue-text" style="margin: 10 0 0 50;">
                                        <?php echo $prediction[1]; ?>
                                    </div>
                                <?php } ?>
                            </div>

                            <div class="col m4 s4">
                                <form method="POST" action="inventory_management.php">
                                    <label>Update Quantity: </label>
                                    <input type="number" name="updateqty" value="<?php echo htmlspecialchars($product['PDTQTY']); ?>">
                                    <input type="hidden" name="updateid" value="<?php echo $product['PDTID']; ?>" />
                                    <input type="submit" name="qtybutton" value="update" class="btn-small brand z-depth-0" style="width: 100%;">
                                </form>
                            </div>
                        </div>
                    </li>
                <?php } ?>

            </ul>
            <?php
            include("../templates/pagination_output_search.php");
            include("../templates/footer.php");
            ?>
        </div>
    </div>
</div>

</html>