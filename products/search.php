<?php
include('../config/db_connect.php');
include('../templates/header.php');

if (substr($uid, 0, 3) == 'ADM')
	include('../storage_connect.php');

// Check for toast message
if (isset($_SESSION['LASTACTION'])) {
	if ($_SESSION['LASTACTION'] == 'DELETEPDT') {
		echo "<script>M.toast({html: 'Successfully deleted product!'});</script>";
	}
	$_SESSION['LASTACTION'] = 'NONE';
}

// Store previously selected variables
$rangeCheck = $getSearchItem = $getSort = $getFilter = '';
$limit = TRUE;

// Pagination for all results
$currDir = "search.php";
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

// Add to cart 
if (isset($_GET['cart'])) {
	if ($uid) {
		$uid = mysqli_real_escape_string($conn, $uid);
		$id = mysqli_real_escape_string($conn, $_GET['cart']);

		// Check that cart item exists 
		$sql = "SELECT * FROM cart WHERE PDTID='$id' AND USERID='$uid'";
		$result = mysqli_query($conn, $sql);
		if (mysqli_num_rows($result) > 0) {
			// Increment product qty by 1
			$sql = "UPDATE cart SET CARTQTY=CARTQTY+1 WHERE PDTID='$id' AND USERID='$uid'";
		} else {
			// Add to db cart with qty of 1
			$sql = "INSERT INTO cart(PDTID, USERID, CARTQTY) VALUES('$id', '$uid', '1')";
		}

		if (mysqli_query($conn, $sql)) {
			echo "<script>M.toast({html: 'Successfully added to cart!'});</script>";
		} else {
			echo 'Query Error: ' . mysqli_error($conn);
		}
	}
}

// Admin delete 
if (isset($_GET['delete'])) {
	$product_id = mysqli_real_escape_string($conn, $_GET['delete']);
	$sql = "DELETE FROM product WHERE PDTID = '$product_id'";

	// Also delete from cloud storage
	$fileName = $_GET['file'];
	delete_object($bucketName, $fileName);

	// Checks if query is successful
	if (mysqli_query($conn, $sql)) {
		$_SESSION['LASTACTION'] = 'DELETEPDT';
	} else {
		echo 'Query Error' . mysqli_error($conn);
	}
}

// Set title
$title = 'PRODUCTS';
if ($getFilter !== '' && $getSearchItem == '') {

	if ($getFilter !== 'all')
		$title = str_replace('-', ' ', $getFilter);
}

// Get current link
$link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ?
	"https" : "http") . "://" . $_SERVER['HTTP_HOST'] .
	$_SERVER['REQUEST_URI'];
$linkCat = (strpos($link, '?') == TRUE) ? '&' : '?';


// Free memory of result and close connection
mysqli_free_result($result);
mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<h4 class="center grey-text"><?php echo $title; ?></h4>

<div class="sidebar sidebar-padding">
	<form id="sfform" name="sfform" method="get" action="search.php">
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

<div class="container" style="margin-left: 300px;">
	<div class="row">
		<?php foreach ($productList as $product) { ?>

			<div class="col s12 m4">
				<a href="product_details.php?id=<?php echo $product['PDTID']; ?>">
					<div class="card z-depth-0 small">

						<img src="<?php if ($product['IMAGE']) {
											echo $product['IMAGE'];
										} else {
											echo 'img/product_icon.svg';
										} ?>" class="product-icon circle">
						<div class="card-content center">
							<h6 class="black-text"> <?php echo htmlspecialchars($product['PDTNAME']); ?> <label> <?php echo htmlspecialchars($product['WEIGHT']); ?> </label></h6>

							<label> <?php echo htmlspecialchars($product['BRAND']); ?> </label>
							<br>

							<?php if ($product['PDTDISCNT'] > 0) { ?>
								<label>
									<strike> <?php echo htmlspecialchars('$' . $product['PDTPRICE']); ?> </strike>
								</label>
								&nbsp
								<label class="white-text discount-label">
									<?php echo htmlspecialchars('-' . $product['PDTDISCNT'] . '% OFF'); ?>
								</label>

							<?php } ?>

							<div class="black-text flow-text"><?php echo '$' . number_format(htmlspecialchars($product['PDTPRICE']) / 100 * htmlspecialchars(100 - $product['PDTDISCNT']), 2, '.', ''); ?></div>
				</a>
				<div class="card-action right-align">

					<?php if (substr($uid, 0, 3) == 'CUS' || substr($uid, 0, 3) == 'ANO') { ?>
						<a href="<?php echo $link . $linkCat . 'cart=' . $product['PDTID']; ?>">
							<div class="red-text"><i class="fa fa-shopping-cart"></i> Add to Cart</div>
						</a>
					<?php } else if (substr($uid, 0, 3) == 'ADM') {
							$fileName = explode("/", $product['IMAGE'])[4];
							$fileName = explode("?", $fileName)[0];
							?>

						<input type="hidden" name="url" value="<?php echo $product['IMAGE']; ?>">
						<a href="<?php echo $link  . $linkCat .  'delete=' . $product['PDTID'] . '&file=' . $fileName; ?>">
							<div class="red-text"><i class="fa fa-trash-alt"></i> DELETE</div>
						</a>
					<?php } ?>

				</div>

			</div>
	</div>
</div>
<?php } ?>
</div>

<?php
include("../templates/pagination_output_search.php");
include("../templates/footer.php");
?>
</div>

</html>