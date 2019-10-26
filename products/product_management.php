<?php
include("../config/db_connect.php");
include('../templates/header.php');

// Store previously selected variables
$rangeCheck = $getSearchItem = $getSearchR = $getSort = $getFilter = '';
$limit = TRUE;

// Pagination for all results
$currDir = "product_management.php";
$query = 'SELECT * FROM product';
include('../templates/pagination_query.php');

// Get all product categories for filtering
$getCat = "SELECT DISTINCT CATEGORY FROM product";
$catResult = mysqli_query($conn, $getCat);
$filterCat = mysqli_fetch_all($catResult, MYSQLI_ASSOC);

// Get min and max product price for range slider default
$getCatRange = "SELECT MIN(PDTPRICE) AS MINPRICE, MAX(PDTPRICE) AS MAXPRICE FROM product";
$result = mysqli_query($conn, $getCatRange);
$defaultRange = mysqli_fetch_assoc($result);
$minRange = $catMin = (float) $defaultRange['MINPRICE'];
$maxRange = $catMax = (float) $defaultRange['MAXPRICE'];

// Getting data from table: product as associative array
$query = "SELECT * FROM product";

if (isset($_GET['search'])) {
    $getFilter = $_GET['Filter'];
    $getSort = $_GET['sort'];
    // Get price range
    $getPriceR = str_replace('$', '', $_GET['priceRange']);
    $price = explode('-', $getPriceR);
    $minRange = (float) $price[0];
    $maxRange = (float) $price[1];

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
            '%" OR PDTID LIKE "%' . $getSearchItem . '%" AND';

        $getCatRange .= ' WHERE PDTNAME LIKE "%' . $getSearchItem .
            '%" OR CATEGORY LIKE "%' . $getSearchItem .
            '%" OR BRAND LIKE "%' . $getSearchItem .
            '%" OR PDTID LIKE "%' . $getSearchItem . '%"';
    } else
        $query .= ' WHERE';

    // If user uses filter function
    if ($getFilter != "all") {
        $limit = FALSE;
        if ($getSearchItem != null) {
            // Price range by category
            $getCatRange .= ' AND CATEGORY = "' . $rFilter . '"';
        } else
            // Price range by category
            $getCatRange .= ' WHERE CATEGORY = "' . $rFilter . '"';
        $query .= ' CATEGORY = "' . $rFilter . '" AND';
    }

    // Get price range for specific category
    $result = mysqli_query($conn, $getCatRange);
    $catPriceRange = mysqli_fetch_assoc($result);
    $catMin = (float) $catPriceRange['MINPRICE'];
    $catMax = (float) $catPriceRange['MAXPRICE'];

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
    $query .= ' PDTPRICE >="' . $minRange . '" AND PDTPRICE <= "' . $maxRange . '"';

    // If user use sort function
    if ($getSort != "default") {
        $query .= ' ORDER BY ' . $getSort;
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

// Update product details
$updateqty = $pdtid = '';
if (isset($_POST['submit'])) {
    $updateqty = mysqli_real_escape_string($conn, $_POST['updateqty']);
    $pdtid = mysqli_real_escape_string($conn, $_POST['updateid']);
    $pdtBrand = mysqli_real_escape_string($conn, $_POST['pdtBrand']);
    $pdtCat = mysqli_real_escape_string($conn, $_POST['pdtCategory']);
    $pdtDesc = mysqli_real_escape_string($conn, $_POST['pdtDescription']);
    $pdtCstPrice = mysqli_real_escape_string($conn, $_POST['pdtCostPrice']);
    $pdtPrice = mysqli_real_escape_string($conn, $_POST['pdtPrice']);
    $pdtDiscount = mysqli_real_escape_string($conn, $_POST['pdtDiscount']);
    $pdtThreshold = mysqli_real_escape_string($conn, $_POST['pdtThreshold']);
    $pdtName = mysqli_real_escape_string($conn, $_POST['pdtName']);
    $pdtWeight = mysqli_real_escape_string($conn, $_POST['pdtWeight']);

    $sql = "UPDATE product SET PDTNAME = '$pdtName', WEIGHT = '$pdtWeight', BRAND = '$pdtBrand', 
				CATEGORY = '$pdtCat', DESCRIPTION = '$pdtDesc', CSTPRICE = ROUND($pdtCstPrice,2), PDTPRICE = ROUND($pdtPrice,2),
				PDTDISCNT = ROUND($pdtDiscount,2), THRESHOLD = '$pdtThreshold', PDTQTY = '$updateqty' WHERE PDTID = '$pdtid'";

    if (mysqli_query($conn, $sql)) {
        echo "<script type='text/javascript'>window.top.location='product_management.php';</script>";
    } else {
        echo 'Query Error: ' . mysqli_error($conn);
    }
}

// Free memory of result and close connection
mysqli_free_result($result);
mysqli_close($conn);
?>

<style>
    label {
        width: 100px;
        display: inline-block;
    }
</style>

<!DOCTYPE HTML>
<html>
<h4 class="center grey-text">Product Management</h4>
<div class="sidebar sidebar-padding">
    <form id="sfform" name="sfform" method="get" action="product_management.php">

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
            <option value="PDTPRICE DESC" <?php if ($getSort == 'PDTPRICE DESC') echo 'selected' ?>>Price - High to Low </option>
            <option value="PDTPRICE ASC" <?php if ($getSort == 'PDTPRICE ASC') echo 'selected' ?>>Price - Low to High</option>
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

        <div class="center">
            <input type="submit" name="search" value="Search" class="btn brand z-depth-0">
        </div>
    </form>
</div>

<div class="container">
    <div class="row">
        <div class="col s22 m11 offset-m1">

            <ul class="collection">
                <?php foreach ($productList as $product) { ?>
                    <li class="collection-item avatar">
                        <a href="product_details.php?id=<?php echo htmlspecialchars($product['PDTID']) ?>">

                            <img src="<?php if ($product['IMAGE']) {
                                                echo $product['IMAGE'];
                                            } else {
                                                echo 'img/product_icon1.svg';
                                            } ?>" class="circle">
                        </a>
                        <form method="POST">
                            <div>
                                <label>Product Name: </label>
                                <input type="text" name="pdtName" <?php echo "value='" . htmlspecialchars($product['PDTNAME']) . "'"; ?> style="width:290px;height:30px">
                            </div>


                            <div>
                                <label>Weight: </label>
                                <input type="text" name="pdtWeight" <?php echo "value='" . htmlspecialchars($product['WEIGHT']) . "'"; ?> style="width:110px;height:30px">
                            </div>

                            <div>
                                <label> Brand: </label>
                                <input type="text" name="pdtBrand" <?php echo "value='" .  htmlspecialchars($product['BRAND']) . "'"; ?> style="width:100px;height:30px">
                            </div>

                            <div>
                                <label>Category: </label>
                                <input type="text" name="pdtCategory" <?php echo "value='" . htmlspecialchars($product['CATEGORY']) . "'"; ?> style="width:230px;height:30px">
                            </div>

                            <label> Description: </label>
                            <input type="text" name="pdtDescription" value="<?php echo htmlspecialchars($product['DESCRIPTION']); ?>" style="width:370px;height:30px">

                            <?php if ($product['PDTQTY'] <= $product['THRESHOLD']) { ?>
                                <div class="red-text bold"><?php echo htmlspecialchars('Quantity: ' . htmlspecialchars($product['PDTQTY'])); ?></div>
                            <?php } else { ?>
                                <div class="black-text"><?php echo htmlspecialchars('Quantity: ' . htmlspecialchars($product['PDTQTY'])); ?></div>
                            <?php } ?>
                            <span class="black-text"><?php echo htmlspecialchars(htmlspecialchars($product['PDTID'])); ?></span>
                            <div align="center">
                                <input type="submit" name="submit" value="update" class="btn-small brand z-depth-0">
                            </div>
                            <div class="secondary-content flex no-pad">
                                <div>
                                    <label>Quantity: </label>
                                    <input type="number" name="updateqty" value="<?php echo htmlspecialchars($product['PDTQTY']); ?>" style="width:180px;height:30px">
                                    <input type="hidden" name="updateid" value="<?php echo $product['PDTID']; ?>" />
                                    <br>
                                    <label>Threshold: </label>
                                    <input type="number" name="pdtThreshold" value="<?php echo htmlspecialchars($product['THRESHOLD']); ?>" style="width:220px;height:30px">
                                    <br>
                                    <label>Cost Price: </label>
                                    <input type="number" name="pdtCostPrice" value="<?php echo htmlspecialchars($product['CSTPRICE']); ?>" step="any" style="width:220px;height:30px">
                                    <br>
                                    <label>Product Price: </label>
                                    <input type="number" name="pdtPrice" value="<?php echo htmlspecialchars($product['PDTPRICE']); ?>" step="any" style="width:200px;height:30px">
                                    <br>
                                    <label>Discount: </label>
                                    <input type="number" name="pdtDiscount" value="<?php echo htmlspecialchars($product['PDTDISCNT']); ?>" step="any" style="width:230px;height:30px">
                                </div>
                            </div>

                        </form>
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