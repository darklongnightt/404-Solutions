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
	if ($rangeCheck == 1){
		//if minprice less than min category or more than max category
		if (($minRange < $catMin) || ($minRange > $catMax) || ($minRange == 0)) {
			$minRange = $catMin;
		}
		//if maxprice more than max category or less than min category
		if (($maxRange > $catMax) || ($maxRange < $catMin) || ($maxRange == 0)){
			$maxRange = $catMax;	
		}
	}
	else {
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

// Update product quantity
$updateqty = $pdtid = '';
if (isset($_POST['submit'])) {
    if (!empty($_POST['updateqty'])) {
		$updateqty = mysqli_real_escape_string($conn, $_POST['updateqty']);
        $pdtid = mysqli_real_escape_string($conn, $_POST['updateid']);
		$nameNweight = $_POST['nameNweight'];
		$brandNcat = $_POST['brandNcat'];
		$pdtDesc = $_POST['pdtDescription'];
		$pdtCstPrice = $_POST['pdtCostPrice'];
		$pdtPrice = $_POST['pdtPrice'];
		$pdtDiscount = $_POST['pdtDiscount'];

		$nameNweight = explode('-',$nameNweight);
		$pdtName = $nameNweight[0];
		$pdtWeight = $nameNweight[1];
		$brandNcat = explode('|',$brandNcat);
		$pdtBrand = $brandNcat[0];
		$pdtCat = $brandNcat[1];	

        $sql = "UPDATE product SET PDTNAME = '$pdtName', WEIGHT = '$pdtWeight', BRAND = '$pdtBrand', 
				CATEGORY = '$pdtCat', DESCRIPTION = '$pdtDesc', CSTPRICE = ROUND($pdtCstPrice,2), PDTPRICE = ROUND($pdtPrice,2),
				PDTDISCNT = ROUND($pdtDiscount,2), PDTQTY = '$updateqty' WHERE PDTID = '$pdtid'";
        if (mysqli_query($conn, $sql)) {
            header("Location: product_management.php");
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
<h4 class="center grey-text">Product Management</h4>
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
		<script>
			var priceMin = <?php echo json_encode($catMin); ?>;
			var priceMax = <?php echo json_encode($catMax); ?>;
			var postMin = <?php echo json_encode($minRange); ?>;
			var postMax = <?php echo json_encode($maxRange); ?>;
			var rangeCheck = <?php echo json_encode($rangeCheck); ?>;
			
			//if user click on range slider
			function clicked(){
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
							<span class="title black-text">
								<input type="text" name="nameNweight" <?php echo "value='" . htmlspecialchars($product['PDTNAME'] . ' - ' . $product['WEIGHT']) . "'"; ?> style="width:250px;height:30px">
							</span>
							<div class="black-text" >	
								<label> Brand | Category </label>
								<input type="text" name="brandNcat" <?php echo "value='" .  htmlspecialchars($product['BRAND']) . ' | ' . htmlspecialchars($product['CATEGORY']) . "'"; ?> style="width:180px;height:30px">
							</div>
							<label> Description: </label>
							<input type="text" name="pdtDescription" value="<?php echo htmlspecialchars($product['DESCRIPTION']); ?>"style="width:190px;height:30px">
							<?php if ($product['PDTQTY'] <= $product['THRESHOLD']) { ?>
								<div class="red-text bold"><?php echo htmlspecialchars('Quantity: ' . htmlspecialchars($product['PDTQTY'])); ?></div>
							<?php } else { ?>
								<div class="black-text"><?php echo htmlspecialchars('Quantity: ' . htmlspecialchars($product['PDTQTY'])); ?></div>
							<?php } ?>
							<span class="black-text"><?php echo htmlspecialchars(htmlspecialchars($product['PDTID'])); ?></span>
							<div class="secondary-content flex no-pad">
								<label>Update Quantity: </label>
								<input type="number" name="updateqty" value="<?php echo htmlspecialchars($product['PDTQTY']); ?>">
								<input type="hidden" name="updateid" value="<?php echo $product['PDTID']; ?>" />
							</div>	
							<div class="secondary-content flex no-pad" style="padding:60px">
								<label>Cost Price: </label> 
								<input type="number" name="pdtCostPrice" value="<?php echo htmlspecialchars($product['CSTPRICE']); ?>" style="width:180px;height:30px">
							</div>
							<div class="secondary-content flex no-pad" style="padding:100px">
								<label>Product Price: </label>
								<input type="number" name="pdtPrice" value="<?php echo htmlspecialchars($product['PDTPRICE']); ?>" style="width:180px;height:30px">
							</div>
							<div class="secondary-content flex no-pad" style="padding:140px">
								<label>Discount: </label> 
								<input type="number" name="pdtDiscount" value="<?php echo htmlspecialchars($product['PDTDISCNT']); ?>" style="width:180px;height:30px">
							</div>
							<br>
							<input type="submit" name="submit" value="update" class="btn-small brand z-depth-0">
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