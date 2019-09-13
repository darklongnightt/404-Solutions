<?php
include('config/db_connect.php');
include('templates/header.php');

$pdtname = $desc = $brand = $category = $pdtqty = $pdtprice = $cstprice = $discount = $checkResult = $pdtid = '';
$errors = array('pdtname'=>'', 'desc'=>'', 'brand'=>'', 'category'=>'', 'pdtqty'=>'', 'pdtprice'=>'', 'cstprice'=>'', 'discount'=>'');

//Checks if button of name="submit" is clicked
if (isset($_POST['submit'])){
	//Gets data from the POST request 
	if (empty($_POST['pdtname'])) {
		$errors['pdtname'] = 'Product name is required!';
	} else {
		$pdtname = $_POST['pdtname'];
		if (!preg_match('/^[a-zA-Z\s]+$/', $pdtname)) {
			$errors['pdtname'] = 'Product name must be letters and spaces only!';
		}
	}

	if (empty($_POST['desc'])) {
		$errors['desc'] = 'Product description is required!';
	} else {
		$desc = $_POST['desc'];

	}

	if (empty($_POST['brand'])) {
		$errors['brand'] = 'Product brand is required!';
	} else {
		$brand = $_POST['brand'];
		
	}

	if (empty($_POST['category'])) {
		$errors['category'] = 'Product category is required!';
	} else {
		$category = $_POST['category'];
		
	}

	if (empty($_POST['pdtqty'])) {
		$errors['pdtqty'] = 'Product quantity is required!';
	} else {
		$pdtqty = $_POST['pdtqty'];
		
	}

	if (empty($_POST['pdtprice'])) {
		$errors['pdtprice'] = 'Product price is required!';
	} else {
		$pdtprice = $_POST['pdtprice'];
	}

	if (empty($_POST['cstprice'])) {
		$errors['cstprice'] = 'Cost price is required!';
	} else {
		$cstprice = $_POST['cstprice'];
	}

	if (empty($_POST['discount'])) {
		$discount = 0;
	} else {
		$discount = $_POST['discount'];
	}
	
	// Checks if form is error free
	if (!array_filter($errors)) {
		// Formatting string for db security
		$pdtname = mysqli_real_escape_string($conn, $_POST['pdtname']);
		$desc = mysqli_real_escape_string($conn, $_POST['desc']);
		$brand = mysqli_real_escape_string($conn, $_POST['brand']);
		$category = mysqli_real_escape_string($conn, $_POST['category']);

		// Generate unique uid for the product
        $unique = true;
        do {
            $pdtid = uniqid('PDT', true);
            $sql = "SELECT * FROM product WHERE PDTID = $pdtid";
            $result = mysqli_query($conn, $sql);
            $checkResult = mysqli_num_rows($result);
            if ($checkResult > 0) {
                $unique = false;
            }
        } while (!$unique);
		
		// Inserts data to db and redirects user to homepage
		$sql = "INSERT INTO product(PDTID, PDTNAME, DESCRIPTION, BRAND, CATEGORY, PDTQTY, CSTPRICE, PDTPRICE, PDTDISCNT) 
		VALUES('$pdtid', '$pdtname', '$desc', '$brand', '$category', '$pdtqty', '$cstprice', '$pdtprice', '$discount')";
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

<section class="container grey-text">
	<h4 class="center">New Product</h4>
	<form action="list_product.php" class="white" method="POST">
		<label>Product Name: </label>
		<input type="text" name="pdtname" value="<?php echo htmlspecialchars($pdtname); ?>">
		<div class="red-text"><?php echo htmlspecialchars($errors['pdtname']); ?></div>

		<label>Description: </label>
		<input type="text" name="desc" value="<?php echo htmlspecialchars($desc); ?>">
		<div class="red-text"><?php echo htmlspecialchars($errors['desc']); ?></div>

		<label>Brand: </label>
		<input type="text" name="brand" value="<?php echo htmlspecialchars($brand); ?>">
		<div class="red-text"><?php echo htmlspecialchars($errors['brand']); ?></div>

		<label>Category: </label>
		<input type="text" name="category" value="<?php echo htmlspecialchars($category); ?>">
		<div class="red-text"><?php echo htmlspecialchars($errors['category']); ?></div>

		<label>Quantity Available: </label>
		<input type="number" name="pdtqty" value="<?php echo htmlspecialchars($pdtqty); ?>">
		<div class="red-text"><?php echo htmlspecialchars($errors['pdtqty']); ?></div>

		<label>Product Price: </label>
		<input type="number" name="pdtprice" min="0" value="<?php echo htmlspecialchars($pdtprice); ?>" step=".01">
		<div class="red-text"><?php echo htmlspecialchars($errors['pdtprice']); ?></div>

		<label>Cost Price: </label>
		<input type="number" name="cstprice" min="0" value="<?php echo htmlspecialchars($cstprice); ?>" step=".01">
		<div class="red-text"><?php echo htmlspecialchars($errors['cstprice']); ?></div>

		<label>Discount: </label>
		<input type="number" name="discount" min="0" value="<?php echo htmlspecialchars($discount); ?>" step=".01">
		<div class="red-text"><?php echo htmlspecialchars($errors['discount']); ?></div>

		<div class="center">
			<input type="submit" name="submit" type="submit" class="btn brand z-depth-0">
		</div>
	</form>
</section>

<?php include("templates/footer.php"); ?>

</html>