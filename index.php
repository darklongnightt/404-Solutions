<?php
include('config/db_connect.php');
include('templates/header.php');

// Store previously selected variables
$getSort = $getFilter = '';

// Pagination for all results
$currDir = "index.php";
$query = 'SELECT * FROM product';
include('templates/pagination_query.php');

// Get category to filter products
$getCat = "SELECT DISTINCT CATEGORY FROM product";
$catResult = mysqli_query($conn, $getCat);
$filterCat = mysqli_fetch_all($catResult, MYSQLI_ASSOC);

if ($_POST) {
	// get user's selection for sort
	$getFilter = htmlspecialchars($_POST['selectF']);
	// replace - with space for sql filter by category
	$rFilter = str_replace('-', ' ', $getFilter);
	$getSort = htmlspecialchars($_POST['selectS']);
	$getPriceR = str_replace('$', '', $_POST['priceR']);
	$price = explode('-', $getPriceR);
	// get min and max price
	$pMin = $price[0];
	$pMax = $price[1];
	$query .= ' WHERE PDTPRICE >="' . $pMin . '" AND PDTPRICE <= "' . $pMax . '"';
	//if user uses filter function
	if ($getFilter != "all") {
		$query .= ' AND CATEGORY = "' . $rFilter . '"';
	}

	//if user use sort function
	if ($getSort != "default") {
		$query .= ' ORDER BY ' . $getSort;
	}
	// if user did not use sort function
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
	$(function() {
		$("#pRange").slider({
			range: true,
			min: 0,
			max: 20,
			values: [0, 20],
			slide: function(event, ui) {
				$("#range").val("$" + ui.values[0] + " - $" + ui.values[1]);
			}
		});
		$("#range").val("$" + $("#pRange").slider("values", 0) +
			" - $" + $("#pRange").slider("values", 1));
	});
</script>
<div class="sidebar">
	<form id="sfform" method="post">

		<h6 class="grey-text">Category</h6>
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
			<option value="default" <?php if($getSort == '') echo 'selected'?> >Default</option>
			<option value="PDTPRICE DESC" <?php if($getSort == 'PDTPRICE DESC') echo 'selected'?> >Price - High to Low </option>
			<option value="PDTPRICE ASC" <?php if($getSort == 'PDTPRICE ASC') echo 'selected'?>>Price - Low to High</option>
			<option value="PDTDISCNT ASC" <?php if($getSort == 'PDTDISCNT ASC') echo 'selected'?>>Discount - Low to High</option>
			<option value="PDTDISCNT DESC" <?php if($getSort == 'PDTDISCNT DESC') echo 'selected'?>>Discount - High to Low</option>
			<option value="PDTQTY DESC" <?php if($getSort == 'PDTQTY DESC') echo 'selected'?>>Quantity - High to Low </option>
			<option value="PDTQTY ASC" <?php if($getSort == 'PDTQTY ASC') echo 'selected'?>>Quantity - Low to High</option>
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