<?php 
include("config/db_connect.php");
include('templates/header.php');

$uid = mysqli_real_escape_string($conn, $_SESSION['U_UID']);
$id = $rtname = $rtdesc ='';
$errors = array('rtname' => '', 'rtdesc' => '');

//Checks if button of name="submit" is clicked
if (isset($_POST['submit'])) {
	//Gets data from the POST request 
	if (empty($_POST['rtname'])) {
		$errors['rtname'] = 'Product name is required!';
	} else {
		$rtname = $_POST['rtname'];
	}

	if (empty($_POST['rtdesc'])) {
		$errors['rtdesc'] = 'Product name is required!';
	} else {
		$rtdesc = $_POST['rtdesc'];
	}
	if (!array_filter($errors)) {
		// Formatting string for db security
		$rtname = mysqli_real_escape_string($conn, $_POST['rtname']);
		$rtdesc = mysqli_real_escape_string($conn, $_POST['rtdesc']);

		// Inserts data to db and redirects user to homepage
		// $sql = "INSERT INTO rating(RTNAME,  RTDESC) 
		// VALUES('$rtname', '$rtdesc')";
		// if (mysqli_query($conn, $sql)) {
		// 	header('Location: index.php');
		// } else {
		// 	echo 'Query Error: ' . mysqli_error($conn);
		// }
	}
}
// Checks if link contains product id
if (isset($_GET['id'])) {

	//Order id to find product that got ordered
    $id = mysqli_real_escape_string($conn, $_GET['id']);
	// Retrieve orders from order and product table as assoc array
	$sql = "SELECT * FROM orders, product 
	WHERE orders.ORDERID = '$id' AND product.PDTID = orders.PDTID 
	ORDER BY PCHASEDATE DESC";
    $result = mysqli_query($conn, $sql) or die ("No product in order");
    if(mysqli_num_rows($result)>0)
    {
    	// To fetch the result as a single associative array
    	$product = mysqli_fetch_assoc($result);
	}
}
mysqli_close($conn);
?>

<!DOCTYPE HTML>
<html>

<?php if ($product) : ?>
	<div class="container EditPadding">
		<div class="col s8 md4">
			<div class="card z-depth-0">
				<div class="card-content center">
					<a href="product_details.php?id=<?php echo $product['PDTID']; ?>">
						<h6 class="black-text"> <?php echo htmlspecialchars($product['PDTNAME'] . ' - ' . $product['WEIGHT']); ?> </h6>
					</a>

					<div> <?php echo htmlspecialchars('Net Total Price: $' . number_format($product['NETPRICE'], 2, '.', '')); ?> </div>

					<?php if ($product['PDTDISCNT'] > 0) { ?>
						<div class="grey-text">
							<strike><?php echo htmlspecialchars('$' . number_format($product['TTLPRICE'], 2, '.', '')); ?></strike>
							<?php echo htmlspecialchars('-' . $product['TTLDISCNTPRICE'] . '%'); ?>
						</div>
					<?php } ?>

					<div> <?php echo htmlspecialchars('Ordered Quantity: ' . $product['ORDERQTY']); ?> </div>
				</div>
			</div>
		</div>

		<form action="rating_details.php?id=<?php echo $product['ORDERID']; ?>" class="EditForm" method="POST">
			<label>Product Name: </label>
			<input type="text" name="rtname" value="<?php echo htmlspecialchars($rtname); ?>">
			<div class="red-text"><?php echo htmlspecialchars($errors['rtname']); ?></div>

			<label>Comments: </label>
			<textarea name="rtdesc" rows="10" cols="40" class="ColHeight"><?php echo htmlspecialchars($rtdesc); ?></textarea>
			<div class="red-text"><?php echo htmlspecialchars($errors['rtdesc']); ?></div>
				<div class="center">
					<br>
					<input type="submit" name="submit" type="submit" class="btn brand z-depth-0">
				</div>
		</form>
	</div>

<?php else : ?>
    <h4 class="center">Error 404: No product ordered!</h4>
<?php endif; ?>

<?php include("templates/footer.php"); ?>
</html>