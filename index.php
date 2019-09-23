<?php
include('config/db_connect.php');
include('templates/header.php');

// Store previously selected variables
$getSort = $getFilter = '';

// Pagination for all results
$currDir = "index.php";
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
		$query .= ' WHERE CATEGORY = "' . $rFilter . '" AND';

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
$query .= "\nLIMIT $startingLimit , $resultsPerPage";

// Getting data from table: product as associative array
$result = mysqli_query($conn, $query);
$productList = mysqli_fetch_all($result, MYSQLI_ASSOC);

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
<div class="sidebar">
	<form id="sfform" name="sfform" method="post">
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
		<input type="submit" name="submit" value="Search" class="btn brand z-depth-0">
	</form>
</div>

<div class="container">
	<div class="row">
		<?php foreach ($productList as $product) { ?>
			<div class="col s4 md2">
				<div class="card z-depth-0">
					<img src="img/product_icon.svg" class="product-icon">
					<div class="card-content center">
						<h6> <?php echo htmlspecialchars($product['PDTNAME'] . ' - ' . $product['WEIGHT']); ?> </h6>
						<div> <?php echo htmlspecialchars('$' . $product['PDTPRICE']); ?>
							<label><?php if ($product['PDTDISCNT'] > 0) {
											echo htmlspecialchars(' -' . $product['PDTDISCNT'] . '% OFF');
										}
										?>
							</label>
						</div>
						<div class="card-action right-align">
							<a href="product_details.php?id=<?php echo $product['PDTID']; ?>" class="brand-text">more info</a>
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