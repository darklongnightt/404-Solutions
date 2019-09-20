<?php
include('config/db_connect.php');
include('templates/header.php');

// Pagination for all results
$currDir = "index.php";
$query = "SELECT * FROM product";
include('templates/pagination_query.php');

// Get category to filter products
$getCat = "SELECT DISTINCT CATEGORY FROM product";
$catResult = mysqli_query($conn, $getCat);
$filterCat = mysqli_fetch_all($catResult, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>

<h4 class="center grey-text">Products</h4>

<form id="sfform" method="post">
	<label> Sort By: </label>
	<select class="browser-default" name="selectS">
		<option value="default" selected>Default</option>
		<option value="PDTPRICE DESC">Price - High to Low </option>
		<option value="PDTPRICE ASC">Price - Low to High</option>
		<option value="PDTDISCNT ASC">Discount - Low to High</option>
		<option value="PDTDISCNT DESC">Discount - High to Low</option>
		<option value="PDTQTY DESC">Quantity - High to Low </option>
		<option value="PDTQTY ASC">Quantity - Low to High</option>
	</select>
	<label> Filter By Category: </label>
	<select class="browser-default" name="selectF">
		<option value="all">All</option>
		<?php

		foreach ($filterCat as $filtered) {
			echo "<option value=" . str_replace(' ', '-', $filtered['CATEGORY']) . ">" . $filtered['CATEGORY'] . "</option>";
		}
		?>
	</select>

	<input type="submit" name="submit" value="Go!" class="btn brand z-depth-0">
</form>

<?php
if ($_POST) {
	// get user's selection for sort
	$getFilter = htmlspecialchars($_POST['selectF']);
	// replace - with space for sql filter by category
	$rFilter = str_replace('-', ' ', $getFilter);
	$getSort = htmlspecialchars($_POST['selectS']);
	//if user uses filter function
	if ($getFilter != "all") {
		$query .= ' WHERE CATEGORY = "' . $rFilter . '"';
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
<div class="container">
	<div class="row">
		<?php foreach ($productList as $product) { ?>
			<div class="col s4 md2">
				<div class="card z-depth-0">
					<img src="img/product_icon.svg" class="product-icon">
					<div class="card-content center">
						<h6> <?php echo htmlspecialchars($product['PDTNAME']); ?> </h6>
						<div> <?php echo htmlspecialchars($product['WEIGHT']); ?> </div>
						<div> <?php echo htmlspecialchars('$' . $product['PDTPRICE']); ?> </div>
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