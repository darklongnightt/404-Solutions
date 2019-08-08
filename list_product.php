<?php
include('config/db_connect.php');

$title = $desc = $price = '';
$errors = array('title'=>'', 'desc'=>'', 'price'=>'');

//Checks if button of name="submit" is clicked
if (isset($_POST['submit'])){
	//Gets data from the POST request 
	if (empty($_POST['title'])) {
		$errors['title'] = 'Product title is required!';
	} else {
		$title = $_POST['title'];
		if (!preg_match('/^[a-zA-Z\s]+$/', $title)) {
			$errors['title'] = 'Title must be letters and spaces only!';
		}
	}

	if (empty($_POST['desc'])) {
		$errors['desc'] = 'Product description is required!';
	} else {
		$desc = $_POST['desc'];
		
	}

	if (empty($_POST['price'])) {
		$errors['price'] = 'Product price is required!';
	} else {
		$price = $_POST['price'];
	}
	
	// Checks if form is error free
	if (!array_filter($errors)) {
		// Formatting string for db security
		$title = mysqli_real_escape_string($conn, $_POST['title']);
		$desc = mysqli_real_escape_string($conn, $_POST['desc']);
		
		// Inserts data to db and redirects user to homepage
		$sql = "INSERT INTO product_listing(title, description, price) 
		VALUES('$title', '$desc', '$price')";
		if(mysqli_query($conn, $sql)) {
			header('Location: index.php');
		} else {
			echo 'Query Error: '.mysqli_error($conn);
		}
	}
}
?>

<!DOCTYPE html>
<html>
<?php include("templates/header.php"); ?>

<section class="container grey-text">
	<h4 class="center">New Product</h4>
	<form action="list_product.php" class="white" method="POST">
		<label>Title: </label>
		<input type="text" name="title" value="<?php echo htmlspecialchars($title); ?>">
		<div class="red-text"><?php echo htmlspecialchars($errors['title']); ?></div>

		<label>Description: </label>
		<input type="text" name="desc" value="<?php echo htmlspecialchars($desc); ?>">
		<div class="red-text"><?php echo htmlspecialchars($errors['desc']); ?></div>

		<label>Price: </label>
		<input type="number" name="price" min="0" value="<?php echo htmlspecialchars($price); ?>" step=".01">
		<div class="red-text"><?php echo htmlspecialchars($errors['price']); ?></div>

		<div class="center">
			<input type="submit" name="submit" type="submit" class="btn brand z-depth-0">
		</div>
	</form>
</section>

<?php include("templates/footer.php"); ?>

</html>