<?php
include("../config/db_connect.php");
include('../templates/header.php');

$review = $reviewid = $orderid = '';
$productrating = $delirating = $srating = 0;
$errors = array('review' => '', 'rating' => '');

// Checks if link contains product id
if (isset($_GET['id'])) {
	$id = mysqli_real_escape_string($conn, $_GET['id']);

	// Retrieve product as assoc array
	$sql = "SELECT * FROM product WHERE PDTID = '$id'";
	$result = mysqli_query($conn, $sql);
	$product = mysqli_fetch_assoc($result);
}

// Checks if link contains order id
if (isset($_GET['order'])) {
	$orderid = mysqli_real_escape_string($conn, $_GET['order']);
}

//Checks if button of name="submit" is clicked
if (isset($_POST['submit'])) {
	//Gets data from the POST request 

	if (empty($_POST['productrating']) || empty($_POST['delirating']) || empty($_POST['srating'])) {
		$errors['rating'] = 'All ratings are required!';
	} else {
		$productrating = $_POST['productrating'];
		$delirating = $_POST['delirating'];
		$srating = $_POST['srating'];
	}

	if (empty($_POST['review'])) {
		$errors['review'] = 'Please leave us a review!';
	} else {
		$review = $_POST['review'];
	}

	if (!array_filter($errors)) {
		// Formatting string for db security
		$uid = mysqli_real_escape_string($conn, $_SESSION['U_UID']);
		$pdtid = mysqli_real_escape_string($conn, $_POST['rating_productid']);
		$productrating = mysqli_real_escape_string($conn, $_POST['productrating']);
		$delirating = mysqli_real_escape_string($conn, $_POST['delirating']);
		$srating = mysqli_real_escape_string($conn, $_POST['srating']);
		$review = mysqli_real_escape_string($conn, $_POST['review']);

		// Generate unique uid for the rating
		$unique = true;
		do {
			$reviewid = uniqid('RNR');
			$sql = "SELECT * FROM review WHERE REVIEWID = '$reviewid'";
			$result = mysqli_query($conn, $sql);
			$checkResult = mysqli_num_rows($result);
			if ($checkResult > 0) {
				$unique = false;
			}
		} while (!$unique);

		// Insert review into the db
		$sql = "INSERT INTO review(REVIEWID, USERID, PDTID, PRATING, DRATING, SRATING, COMMENT)
		VALUES('$reviewid', '$uid', '$pdtid', '$productrating', '$delirating', '$srating', '$review')";

		if (mysqli_query($conn, $sql)) {
			// Update order status to "REVIEWED"
			$status = 'Delivered & Reviewed';
			$sql = "UPDATE orders SET STATUS='$status' WHERE ORDERID='$orderid'";

			if (mysqli_query($conn, $sql)) {
				$_SESSION['LASTACTION'] = 'REVIEWED';
				echo "<script type='text/javascript'>window.top.location='/my_orders.php';</script>";
			} else {
				echo 'Query Error: ' . mysqli_error($conn);
			}
		} else {
			echo 'Query Error: ' . mysqli_error($conn);
		}
	}
}

mysqli_free_result($result);
mysqli_close($conn);
?>

<!DOCTYPE HTML>
<html>

<head>
	<link rel="stylesheet" type="text/css" href="/css/rating_style.css">
	<script type="text/javascript">
		function change(id) {
			var cname = document.getElementById(id).className;
			var ab = document.getElementById(id + "_hidden").value;
			document.getElementById(cname + "rating").value = ab;

			for (var i = ab; i >= 1; i--) {
				document.getElementById(cname + i).src = "/img/star2.png";
			}
			var id = parseInt(ab) + 1;
			for (var j = id; j <= 5; j++) {
				document.getElementById(cname + j).src = "/img/star1.png";
			}
		}
	</script>
</head>

<?php if ($product) : ?>
	<div class="container EditPadding">
		<div class="col s8 m4">
			<div class="card z-depth-0">
				<div class="card-content center">
					<img src="<?php if ($product['IMAGE']) {
										echo $product['IMAGE'];
									} else {
										echo '/img/product_icon.svg';
									} ?>" class="icon">

					<a href="product_details.php?id=<?php echo $product['PDTID']; ?>">
						<h6 class="black-text"> <?php echo htmlspecialchars($product['PDTNAME'] . ' - ' . $product['WEIGHT']); ?> </h6>
					</a>

					<div class="grey-text"> <?php echo htmlspecialchars($product['BRAND']); ?> </div>
				</div>
			</div>
		</div>

		<form action="rating_details.php?id=<?php echo $product['PDTID'] . "&order=" . $orderid; ?>" class="EditForm" method="POST">

			<div class="rating">
				<p class="word">Product</p>
				<input type="hidden" id="product1_hidden" value="1">
				<img src="/img/star1.png" onmouseup="change(this.id);" id="product1" class="product">
				<input type="hidden" id="product2_hidden" value="2">
				<img src="/img/star1.png" onmouseup="change(this.id);" id="product2" class="product">
				<input type="hidden" id="product3_hidden" value="3">
				<img src="/img/star1.png" onmouseup="change(this.id);" id="product3" class="product">
				<input type="hidden" id="product4_hidden" value="4">
				<img src="/img/star1.png" onmouseup="change(this.id);" id="product4" class="product">
				<input type="hidden" id="product5_hidden" value="5">
				<img src="/img/star1.png" onmouseup="change(this.id);" id="product5" class="product">
			</div>

			<div class="rating">
				<p class="word">Delivery</p>
				<input type="hidden" id="deli1_hidden" value="1">
				<img src="/img/star1.png" onmouseup="change(this.id);" id="deli1" class="deli">
				<input type="hidden" id="deli2_hidden" value="2">
				<img src="/img/star1.png" onmouseup="change(this.id);" id="deli2" class="deli">
				<input type="hidden" id="deli3_hidden" value="3">
				<img src="/img/star1.png" onmouseup="change(this.id);" id="deli3" class="deli">
				<input type="hidden" id="deli4_hidden" value="4">
				<img src="/img/star1.png" onmouseup="change(this.id);" id="deli4" class="deli">
				<input type="hidden" id="deli5_hidden" value="5">
				<img src="/img/star1.png" onmouseup="change(this.id);" id="deli5" class="deli">
			</div>

			<div class="rating">
				<p class="word">Service</p>
				<input type="hidden" id="s1_hidden" value="1">
				<img src="/img/star1.png" onmouseup="change(this.id);" id="s1" class="s">
				<input type="hidden" id="s2_hidden" value="2">
				<img src="/img/star1.png" onmouseup="change(this.id);" id="s2" class="s">
				<input type="hidden" id="s3_hidden" value="3">
				<img src="/img/star1.png" onmouseup="change(this.id);" id="s3" class="s">
				<input type="hidden" id="s4_hidden" value="4">
				<img src="/img/star1.png" onmouseup="change(this.id);" id="s4" class="s">
				<input type="hidden" id="s5_hidden" value="5">
				<img src="/img/star1.png" onmouseup="change(this.id);" id="s5" class="s">
			</div>

			<br>
			<div class="red-text"><?php echo htmlspecialchars($errors['rating']); ?></div>

			<p class="word">Comment: </p>
			<textarea name="review" placeholder="How was the product? Leave us a feedback!" class="ColHeight"><?php echo htmlspecialchars($review); ?></textarea>

			<div class="red-text"><?php echo htmlspecialchars($errors['review']); ?></div>

			<div class="center">
				<br>
				<input type="hidden" name="rating_productid" id="rating_productid" value=<?php echo $product['PDTID']; ?>>
				<input type="hidden" name="productrating" id="productrating" value="0">
				<input type="hidden" name="delirating" id="delirating" value="0">
				<input type="hidden" name="srating" id="srating" value="0">
				<input type="submit" name="submit" type="submit" class="btn brand z-depth-0">
			</div>
		</form>
	</div>

<?php else : ?>
	<h4 class="center">Error 404: No such product!</h4>
<?php endif; ?>

<?php include("../templates/footer.php"); ?>

</html>