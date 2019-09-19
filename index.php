<?php
include('config/db_connect.php');
include('templates/header.php');

// Pagination for all results
$currDir = "index.php";
$query = "SELECT * FROM product";
include('templates/pagination_query.php');

// Getting data from table: product as associative array
$query = "SELECT * FROM product 
ORDER BY CREATED_AT DESC 
LIMIT $startingLimit , $resultsPerPage";
$result = mysqli_query($conn, $query);
$productList = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Free memory of result and close connection
mysqli_free_result($result);
mysqli_close($conn);
?>

<!DOCTYPE html>
<html>

<h4 class="center grey-text">Products</h4>
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