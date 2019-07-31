<?php
// Connecting to mySQL database
$conn = mysqli_connect('localhost', 'xavier', 'pw1234', 'super_data');
if (!$conn) {
	echo 'Connection Error: ' . mysqli_connect_error();
}

// Getting data from table: product_listing as associative array
$query = 'SELECT title, description, price FROM product_listing ORDER BY created_at DESC';
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
					<div class="card-content center">
						<h6> <?php echo htmlspecialchars($product['title']); ?> </h6>
						<div> <?php echo htmlspecialchars($product['description']); ?> </div>
						<div> <?php echo htmlspecialchars('$'.$product['price']); ?> </div>
						<div class="card-action right-align">
							<a href="#" class="brand-text">more info</a>
						</div>
					</div>
				</div>
			</div>
		<?php } ?>
	</div>
</div>
<?php include("templates/footer.php"); ?>

</html>