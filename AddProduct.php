<?php
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
		if (!preg_match('/^[0-9]+$/', $price)) {
			$errors['price'] = 'Price must be in numbers only!';
		}
	}
	
	// Checks if form is error free
	if (!array_filter($errors)) {
		//Saves data to db and redirects user to homepage
		header('Location: index.php');
	}
}
?>

<!DOCTYPE html>
<html>
<?php include("templates/header.php"); ?>

<section class="container grey-text">
	<h4 class="center">New Product</h4>
	<form action="AddProduct.php" class="white" method="POST">
		<label>Title: </label>
		<input type="text" name="title" value="<?php echo htmlspecialchars($title); ?>">
		<div class="red-text"><?php echo htmlspecialchars($errors['title']); ?></div>

		<label>Description: </label>
		<input type="text" name="desc" value="<?php echo htmlspecialchars($desc); ?>">
		<div class="red-text"><?php echo htmlspecialchars($errors['desc']); ?></div>

		<label>Price: </label>
		<input type="text" name="price" value="<?php echo htmlspecialchars($price); ?>">
		<div class="red-text"><?php echo htmlspecialchars($errors['price']); ?></div>

		<div class="center">
			<input type="submit" name="submit" type="submit" class="btn brand z-depth-0">
		</div>
	</form>
</section>

<?php include("templates/footer.php"); ?>

</html>