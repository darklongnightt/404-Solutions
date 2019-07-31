<?php 
	$conn = mysqli_connect('localhost', 'xavier', 'pw1234', 'product_listing');
	if (!$conn) {
		echo 'Connection Error: '.mysqli_connect_error();
	}
 ?>
 
<!DOCTYPE html>
<html>
	<?php include("templates/header.php"); ?>

	<?php include("templates/footer.php"); ?>
</html>
