<?php
include('config/db_connect.php');
// Getting data from table: product_listing as associative array
$query = 'SELECT title, description, price, id FROM product_listing ORDER BY created_at DESC';
$result = mysqli_query($conn, $query);
$productList = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Free memory of result and close connection
mysqli_free_result($result);
mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<?php include("templates/header.php"); ?>

<h4 class="center grey-text">Products</h4>
<div class="container">
	<div class="row">
		<?php foreach ($productList as $product) { ?>
			<div class="col s6 md3">
				<div class="card z-depth-0">
					<img src="img/product_icon.svg" class="product-icon">
					<div class="card-content center">
						<h6> <?php echo htmlspecialchars($product['title']); ?> </h6>
						<div> <?php echo htmlspecialchars($product['description']); ?> </div>
						<div> <?php echo htmlspecialchars('$'.$product['price']); ?> </div>
						<div class="card-action right-align">
							<a href="product_details.php?id=<?php echo $product['id']; ?>" class="brand-text">more info</a>
						</div>
					</div>
				</div>
			</div>
		<?php } ?>
	</div>
</div>
<?php include("templates/footer.php"); ?>

</html>