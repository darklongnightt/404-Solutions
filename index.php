<?php
include('config/db_connect.php');
include('templates/header.php');

if (substr($uid, 0, 3) == 'ADM')
	include('storage_connect.php');

// Store previously selected variables
$getSort = $getFilter = '';
$limit = TRUE;

// Pagination for all results
$currDir = "index.php";
$query = "SELECT * FROM product";
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


if (isset($_POST['submit'])) {
	// Get user's selection for sort, replace - with space for sql filter by category
	$getFilter = $_POST['selectF'];
	$rFilter = str_replace('-', ' ', $getFilter);
	$getSort = $_POST['selectS'];

	// Get price range
	$getPriceR = str_replace('$', '', $_POST['priceR']);
	$price = explode('-', $getPriceR);
	$minRange = (float) $price[0];
	$maxRange = (float) $price[1];

	// If user uses filter function
	if ($getFilter != "all") {
		$limit = FALSE;
		$query = "SELECT * FROM product WHERE CATEGORY='$rFilter' AND";
		// Price range by category
		$getCatRange .= ' WHERE CATEGORY = "' . $rFilter . '"';
	} else
		$query .= ' WHERE';

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
	// Pagination for results
	include('templates/pagination_query.php');
} else {
	$query .= ' ORDER BY CREATED_AT DESC';
}

if (!$limit) {
	$startingLimit = 0;
}
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
			$message = 'Successfully added product to cart!';
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
		header('Location: index.php');
	} else {
		echo 'Query Error' . mysqli_error($conn);
	}
}

// Free memory of result and close connection
mysqli_free_result($result);
mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<h4 class="center grey-text">Products</h4>
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
	<form id="sfform" name="sfform" method="post" action="index.php">
		<h6 class="grey-text">Category</h6>
		<p id="testrange"></p>
		<select class="browser-default" name="selectF">
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
		<select class="browser-default" name="selectS">
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
		<input type="text" name="priceR" id="range" readonly>
		<div id="pRange"></div>
		<br>
		<div class="center">
			<input type="submit" name="submit" value="Search" class="btn brand z-depth-0">
		</div>
	</form>
</div>

<div class="container">
	<div class="row">
		<?php foreach ($productList as $product) { ?>
			<div class="col s4 md2">
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
						<a href="index.php?cart=<?php echo $product['PDTID']; ?>">
							<div class="red-text"><i class="fa fa-shopping-cart"></i> Add to Cart</div>
						</a>
					<?php } else if (substr($uid, 0, 3) == 'ADM') {
							$fileName = explode("/", $product['IMAGE'])[4];
							$fileName = explode("?", $fileName)[0];
							?>

						<input type="hidden" name="url" value="<?php echo $product['IMAGE']; ?>">
						<a href="index.php?delete=<?php echo $product['PDTID'] . '&file=' . $fileName; ?>">
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
include("templates/pagination_output.php");
include("templates/footer.php");
?>
</div>

</html>