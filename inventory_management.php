<?php
include("config/db_connect.php");
include('templates/header.php');

// Store previously selected variables
$getSearchItem = $getSearchR = $getSort = $getFilter = '';
$limit = TRUE;

// Pagination for all results
$currDir = "inventory_management.php";
$query = 'SELECT * FROM product';
include('templates/pagination_query.php');

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

    // If user selection misfit min and max for specific category
    if (($minRange == 0) || ($maxRange == 0) || ($minRange < $catMin) || ($maxRange > $catMax) || ($minRange > $catMax) || ($maxRange < $catMin)) {
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

    $ext = "&Filter=$rFilter&sort=$getSort&priceRange=$minRange-$maxRange&searchItem=$getSearchItem&search=Search";
    $getFilter = str_replace(' ', '-', $rFilter);
} else {
    $query .= ' ORDER BY CREATED_AT DESC';
}

if (!$limit) {
    $startingLimit = 0;
}
// Pagination for results
include('templates/pagination_query.php');
$query .= "\nLIMIT $startingLimit , $resultsPerPage";

// Getting data from table: product as associative array
$result = mysqli_query($conn, $query);
$productList = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Update product quantity
$updateqty = $pdtid = '';
if (isset($_POST['submit'])) {
    if (!empty($_POST['updateqty'])) {
        $updateqty = mysqli_real_escape_string($conn, $_POST['updateqty']);
        $pdtid = mysqli_real_escape_string($conn, $_POST['updateid']);

        $sql = "UPDATE product SET PDTQTY = '$updateqty' WHERE PDTID = '$pdtid'";
        if (mysqli_query($conn, $sql)) {
            header("Location: inventory_management.php");
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
<script>
    var priceMin = <?php echo json_encode($catMin); ?>;
    var priceMax = <?php echo json_encode($catMax); ?>;
    var postMin = <?php echo json_encode($minRange); ?>;
    var postMax = <?php echo json_encode($maxRange); ?>;

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
                        <form class="secondary-content flex no-pad" method="POST" action="inventory_management.php">
                            <label>Update Quantity: </label>
                            <input type="number" name="updateqty" value="<?php echo htmlspecialchars($product['PDTQTY']); ?>">
                            <input type="hidden" name="updateid" value="<?php echo $product['PDTID']; ?>" />
                            <input type="submit" name="submit" value="update" class="btn-small brand z-depth-0">
                        </form>

                        <span class="title black-text"><?php echo htmlspecialchars($product['PDTNAME'] . ' - ' . $product['WEIGHT']); ?></span>
                        <div class="black-text"><?php echo htmlspecialchars($product['BRAND']) . ' | ' . htmlspecialchars($product['CATEGORY']); ?></div>
                        <?php if ($product['PDTQTY'] <= $product['THRESHOLD']) { ?>
                            <div class="red-text bold"><?php echo htmlspecialchars('Quantity: ' . htmlspecialchars($product['PDTQTY'])); ?></div>
                        <?php } else { ?>
                            <div class="black-text"><?php echo htmlspecialchars('Quantity: ' . htmlspecialchars($product['PDTQTY'])); ?></div>
                        <?php } ?>
                        <span class="black-text"><?php echo htmlspecialchars(htmlspecialchars($product['PDTID'])); ?></span>

                    </li>
                <?php } ?>

            </ul>
            <?php
            include("templates/pagination_output_search.php");
            include("templates/footer.php");
            ?>
        </div>
    </div>
</div>

</html>