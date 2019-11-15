<?php
include('../config/db_connect.php');
include('../templates/header.php');
include("../storage_connect.php");

// Access Control Check
if (substr($uid, 0, 3) != 'ADM') {
    echo "<script type='text/javascript'>window.top.location='/index.php';</script>";
}

$pdtname = $desc = $brand = $category = $pdtqty = $pdtprice = $cstprice = $discount = $checkResult = $pdtid = $weight = $url = $fileName = $tmpFilePath = '';
$errors = array('pdtname' => '', 'weight' => '', 'desc' => '', 'brand' => '', 'category' => '', 'pdtqty' => '', 'pdtprice' => '', 'cstprice' => '', 'discount' => '', 'image' => '');

// Fetch all distinct categories
$sql = "SELECT DISTINCT(CATEGORY) FROM product";
$result = mysqli_query($conn, $sql);
$categories = mysqli_fetch_all($result, MYSQLI_ASSOC);

//Checks if button of name="submit" is clicked
if (isset($_POST['submit'])) {

	if ($_FILES["fileToUpload"]["size"] !== 0) {
		// Checks if file is an image
		$imageCheck = getimagesize($_FILES["fileToUpload"]["tmp_name"]);

		if (!$imageCheck) {
			$errors['image'] = "Invalid image file selected!";
		} else {
			// Get the file name and temp file path
			$fileName = $_FILES['fileToUpload']['name'];
			$tmpFilePath = $_FILES['fileToUpload']['tmp_name'];
		}
	} else {
		$errors['image'] = "Product image is required!";
	}

	//Gets data from the POST request for error checking
	if (empty($_POST['pdtname'])) {
		$errors['pdtname'] = 'Product name is required!';
	} else {
		$pdtname = $_POST['pdtname'];
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

	if (empty($_POST['weight'])) {
		$errors['weight'] = 'Product weight is required!';
	} else {
		$weight = $_POST['weight'];
	}

	// Checks if form is error free
	if (!array_filter($errors)) {
		// Formatting string for db security
		$pdtname = mysqli_real_escape_string($conn, $_POST['pdtname']);
		$desc = mysqli_real_escape_string($conn, $_POST['desc']);
		$brand = mysqli_real_escape_string($conn, $_POST['brand']);
		$category = mysqli_real_escape_string($conn, $_POST['category']);
		$weight = mysqli_real_escape_string($conn, $_POST['weight']);

		// Generate unique uid for the product
		$unique = true;
		do {
			$pdtid = uniqid('PDT');
			$sql = "SELECT * FROM product WHERE PDTID = '$pdtid'";
			$result = mysqli_query($conn, $sql);
			$checkResult = mysqli_num_rows($result);
			if ($checkResult > 0) {
				$unique = false;
			}
		} while (!$unique);

		// Resizing image to 300 x 300
		$pic_error = @image_resize($tmpFilePath, $tmpFilePath, 300, 300);

		// Upload to google cloud storage
		upload_object($bucketName, $fileName, $tmpFilePath);

		// Create url for the uploaded image
		$url = "https://storage.cloud.google.com/" . $bucketName . "/" . $fileName . "?cloudshell=false";
		$url = mysqli_real_escape_string($conn, $url);

		// Inserts data to db and redirects user to homepage
		$sql = "INSERT INTO product(PDTID, PDTNAME, WEIGHT, DESCRIPTION, BRAND, CATEGORY, PDTQTY, CSTPRICE, PDTPRICE, PDTDISCNT, IMAGE) 
		VALUES('$pdtid', '$pdtname', '$weight', '$desc', '$brand', '$category', '$pdtqty', '$cstprice', '$pdtprice', '$discount', '$url')";
		if (mysqli_query($conn, $sql)) {
			$_SESSION['LASTACTION'] = 'NEWPDT';
			echo "<script type='text/javascript'>window.top.location='/index.php';</script>";
		} else {
			echo 'Query Error: ' . mysqli_error($conn);
		}
	}
}
?>

<!DOCTYPE html>
<html>

<script>
	function triggerClick(e) {
		displayImage(e);
	}

	function displayImage(e) {
		if (e.files[0]) {
			var reader = new FileReader();
			reader.onload = function(e) {
				document.querySelector('#preview').setAttribute('src', e.target.result);
			}
			reader.readAsDataURL(e.files[0]);
		}
	}
</script>

<section class="container">
	<h4 class="center grey-text">New Product</h4>
	<form action="new_product.php" enctype="multipart/form-data" class="EditForm" method="POST">

		<div class="center">
			<label for="imageUpload"> <img src="/img/upload_placeholder1.png" id="preview" onclick="triggerClick()" style="width: 200px; margin: 20px; border-style: dotted; border-radius: 5px;"> </label>
			<input type="file" name="fileToUpload" id="imageUpload" onchange="displayImage(this)" style="display: none;">
		</div>
		<div class="red-text center"><?php echo htmlspecialchars($errors['image']); ?></div>
		<br>

		<label>Product Category: </label>
		<select class="browser-default" name="category">
			<?php

			foreach ($categories as $cat) {
				echo "<option value=" . $cat['CATEGORY'];
				if ($category == $cat['CATEGORY']) {
					echo " selected";
				}
				echo ">" . $cat['CATEGORY'] . "</option>";
			}
			?>
		</select>
		<div class="red-text"><?php echo htmlspecialchars($errors['category']); ?></div>

		<label>Product Name: </label>
		<input type="text" name="pdtname" value="<?php echo htmlspecialchars($pdtname); ?>">
		<div class="red-text"><?php echo htmlspecialchars($errors['pdtname']); ?></div>

		<label>Weight: </label>
		<input type="text" name="weight" value="<?php echo htmlspecialchars($weight); ?>">
		<div class="red-text"><?php echo htmlspecialchars($errors['weight']); ?></div>

		<label>Description: </label>
		<input type="text" name="desc" value="<?php echo htmlspecialchars($desc); ?>">
		<div class="red-text"><?php echo htmlspecialchars($errors['desc']); ?></div>

		<label>Brand: </label>
		<input type="text" name="brand" value="<?php echo htmlspecialchars($brand); ?>">
		<div class="red-text"><?php echo htmlspecialchars($errors['brand']); ?></div>

		<label>Quantity Available: </label>
		<input type="number" name="pdtqty" value="<?php echo htmlspecialchars($pdtqty); ?>">
		<div class="red-text"><?php echo htmlspecialchars($errors['pdtqty']); ?></div>

		<label>Selling Price (SGD): </label>
		<input type="number" name="pdtprice" min="0" value="<?php echo htmlspecialchars($pdtprice); ?>" step=".01">
		<div class="red-text"><?php echo htmlspecialchars($errors['pdtprice']); ?></div>

		<label>Cost Price (SGD): </label>
		<input type="number" name="cstprice" min="0" value="<?php echo htmlspecialchars($cstprice); ?>" step=".01">
		<div class="red-text"><?php echo htmlspecialchars($errors['cstprice']); ?></div>

		<label>Discount (%): </label>
		<input type="number" name="discount" min="0" max="99" value="<?php echo htmlspecialchars($discount); ?>" step="1">
		<div class="red-text"><?php echo htmlspecialchars($errors['discount']); ?></div>

		<div class="center">
			<input type="submit" name="submit" type="submit" class="btn brand z-depth-0 action-btn">
		</div>
	</form>
</section>

<?php include("../templates/footer.php"); ?>

</html>