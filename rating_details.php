<?php 
include("config/db_connect.php");
include('templates/header.php');

$uid = mysqli_real_escape_string($conn, $_SESSION['U_UID']);
$rtdesc ='';
$errors = array('rtname' => '', 'productrating' => '','delirating' => '','srating' => '' );

//Checks if button of name="submit" is clicked
if (isset($_POST['submit'])) {
	//Gets data from the POST request 
	if (empty($_POST['rtname'])) {
		$errors['rtname'] = 'Product name is required!';
	} else {
		$rtname = $_POST['rtname'];
	}

	if (empty($_POST['productrating'])) {
		$errors['productrating'] = 'Rating is required!';
	} else {
		$productrating=$_POST['productrating'];
	}
	
	if (empty($_POST['delirating'])) {
		$errors['delirating'] = 'Rating is required!';
	} else {
		$delirating=$_POST['delirating'];
	}
	
	if (empty($_POST['srating'])) {
		$errors['srating'] = 'Rating is required!';
	} else {
		$srating=$_POST['srating'];
	}
	// if (!array_filter($errors)) {
	// 	// Formatting string for db security
	// 	$rating_productid = mysqli_real_escape_string($conn, $_POST['rating_productid']);
	// 	$rating_orderid = mysqli_real_escape_string($conn, $_POST['rating_orderid']);
		
	// 	$productrating = mysqli_real_escape_string($conn, $_POST['productrating']);
	// 	$delirating = mysqli_real_escape_string($conn, $_POST['delirating']);
	// 	$srating = mysqli_real_escape_string($conn, $_POST['srating']);

	// 	$rtdesc = mysqli_real_escape_string($conn, $_POST['rtdesc']);
	// 	// Inserts data to db and redirects user to homepage
	// 	$sql = "INSERT INTO rating(RPRO_ID,RO_ID,PRODUCTRATING,DELIRATING,SRATING,RTDESC) VALUES('$rating_productid','$rating_orderid','$productrating','$delirating','$srating','$rtdesc')";
	// 	if (mysqli_query($conn, $sql)) {
	// 	 	header('Location: index.php');
	// 	} else {
	// 	 	echo 'Query Error: ' . mysqli_error($conn);
	// 	}
	// }
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
<head>
	<link rel="stylesheet" type="text/css" href="css/rating_style.css">
	<script type="text/javascript">
  
   function change(id)
   {
      var cname=document.getElementById(id).className;
      var ab=document.getElementById(id+"_hidden").value;
      document.getElementById(cname+"rating").value=ab;

      for(var i=ab;i>=1;i--)
      {
         document.getElementById(cname+i).src="img/star2.png";
      }
      var id=parseInt(ab)+1;
      for(var j=id;j<=5;j++)
      {
         document.getElementById(cname+j).src="img/star1.png";
      }
   }

	</script>
</head>
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
			<!-- <label>Product Name: </label>
			<input type="text" name="rtname" value="<?php echo htmlspecialchars($rtname); ?>">
			<div class="red-text"><?php echo htmlspecialchars($errors['rtname']); ?></div> -->

			<div class ="div">
				<p>Product</p>
				<input type="hidden" id="product1_hidden" value="1">
				<img src="img/star1.png" onmouseup="change(this.id);" id="product1" class="product">
				<input type="hidden" id="product2_hidden" value="2">
				<img src="img/star1.png" onmouseup="change(this.id);" id="product2" class="product">
				<input type="hidden" id="product3_hidden" value="3">
				<img src="img/star1.png" onmouseup="change(this.id);" id="product3" class="product">
				<input type="hidden" id="product4_hidden" value="4">
				<img src="img/star1.png" onmouseup="change(this.id);" id="product4" class="product">
				<input type="hidden" id="product5_hidden" value="5">
				<img src="img/star1.png" onmouseup="change(this.id);" id="product5" class="product">	
			</div>
			<div class="center red-text"><?php echo htmlspecialchars($errors['productrating']); ?></div> 
			<div class ="div">
				<p>Delivery</p>
				<input type="hidden" id="deli1_hidden" value="1">
				<img src="img/star1.png" onmouseup="change(this.id);" id="deli1" class="deli">
				<input type="hidden" id="deli2_hidden" value="2">
				<img src="img/star1.png" onmouseup="change(this.id);" id="deli2" class="deli">
				<input type="hidden" id="deli3_hidden" value="3">
				<img src="img/star1.png" onmouseup="change(this.id);" id="deli3" class="deli">
				<input type="hidden" id="deli4_hidden" value="4">
				<img src="img/star1.png" onmouseup="change(this.id);" id="deli4" class="deli">
				<input type="hidden" id="deli5_hidden" value="5">
				<img src="img/star1.png" onmouseup="change(this.id);" id="deli5" class="deli">	
			</div>
			<div class="center red-text"><?php echo htmlspecialchars($errors['delirating']); ?></div> 
			<div class ="div">
				<p>Service</p>
				<input type="hidden" id="s1_hidden" value="1">
				<img src="img/star1.png" onmouseup="change(this.id);" id="s1" class="s">
				<input type="hidden" id="s2_hidden" value="2">
				<img src="img/star1.png" onmouseup="change(this.id);" id="s2" class="s">
				<input type="hidden" id="s3_hidden" value="3">
				<img src="img/star1.png" onmouseup="change(this.id);" id="s3" class="s">
				<input type="hidden" id="s4_hidden" value="4">
				<img src="img/star1.png" onmouseup="change(this.id);" id="s4" class="s">
				<input type="hidden" id="s5_hidden" value="5">
				<img src="img/star1.png" onmouseup="change(this.id);" id="s5" class="s">
			</div>
			<div class="center red-text"><?php echo htmlspecialchars($errors['srating']); ?></div> 
			<p>Comments: </p>
			<textarea name="rtdesc" rows="10" cols="40" placeholder="How was the product? Comment on the good and bad sides now !" class="ColHeight"><?php echo htmlspecialchars($rtdesc); ?></textarea>
				
				<div class="center">
					<br>
					<input type="hidden" name="rating_productid" id="rating_productid" value= <?php echo$product['PDTID']; ?> >
					<input type="hidden" name="rating_orderid" id="rating_orderid" value= <?php echo$product['ORDERID']; ?> >
					<input type="hidden" name="productrating" id="productrating" value="0">
					<input type="hidden" name="delirating" id="delirating" value="0">
					<input type="hidden" name="srating" id="srating" value="0">
					<input type="submit" name="submit" type="submit" class="btn brand z-depth-0">
				</div>
		</form>
	</div>

<?php else : ?>
    <h4 class="center">Error 404: No product ordered!</h4>
<?php endif; ?>

<?php include("templates/footer.php"); ?>
</html>