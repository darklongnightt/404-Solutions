<?php
include("config/db_connect.php");
include('templates/header.php');
$uid = mysqli_real_escape_string($conn, $_SESSION['U_UID']);

if (isset($_SESSION['LASTACTION'])) {
    switch ($_SESSION['LASTACTION']) {
        case 'REMOVEFAVE':
            echo "<script>M.toast({html: 'Successfully removed from favourites!'});</script>";
            break;
    }

    $_SESSION['LASTACTION'] = "NONE";
}

// Pagination for all results
$currDir = "my_favourites.php";
$query = "SELECT * FROM product, favourite
WHERE product.PDTID = favourite.PDTID AND favourite.USERID = '$uid'";
include('templates/pagination_query.php');

// Get results from favourites with retrieved limits
$query = "SELECT * FROM product, favourite
WHERE product.PDTID = favourite.PDTID AND favourite.USERID = '$uid'
LIMIT $startingLimit, $resultsPerPage";

$result = mysqli_query($conn, $query);
$faveList = mysqli_fetch_all($result, MYSQLI_ASSOC);

// To add an item to cart
if (isset($_GET['addcart'])) {
    $id = mysqli_real_escape_string($conn, $_GET['addcart']);

    // Check that cart item exists 
    $sql = "SELECT * FROM cart WHERE PDTID='$id' AND USERID='$uid'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        // Increment product qty by 1
        $sql = "UPDATE cart SET CARTQTY=CARTQTY+1 WHERE PDTID='$id' AND USERID='$uid'";
    } else {
        // Add to db cart with qty of 1
        $sql = "INSERT INTO cart(PDTID, USERID, CARTQTY) VALUES('$id', '$uid', '1')";
    }

    if (mysqli_query($conn, $sql)) {
        echo "<script>M.toast({html: 'Successfully added to cart!'});</script>";
    } else {
        echo 'Query Error: ' . mysqli_error($conn);
    }
}

// To remove an item from favourite
if (isset($_GET['remove'])) {
    $removePdt = mysqli_real_escape_string($conn, $_GET['remove']);
    $sql = "DELETE FROM favourite WHERE PDTID = '$removePdt' AND USERID = '$uid'";
    if (mysqli_query($conn, $sql)) {
        $_SESSION['LASTACTION'] = 'REMOVEFAVE';
        echo "<script type='text/javascript'>window.top.location='my_favourites.php';</script>";
    } else {
        echo 'Query Error: ' . mysqli_error($conn);
    }
}



// Free memory of result and close connection
mysqli_free_result($result);
mysqli_close($conn);
?>

    <!DOCTYPE HTML>
    <html>

    <div class="container">
        <h4 class="center grey-text">My Favourites</h4>
        <?php if ($faveList) {
            foreach ($faveList as $product) {
                $totalPrice =  $product['PDTPRICE'];
                $totalDiscount = round($totalPrice / 100 * $product['PDTDISCNT'], 2);
                $netPrice = round($totalPrice - $totalDiscount, 2);
                ?>

                <div class="card z-depth-0 order-card">
                    <a href="/products/product_details.php?id=<?php echo $product['PDTID']; ?>">
                        <img src="<?php if ($product['IMAGE']) {
                                                echo $product['IMAGE'];
                                            } else {
                                                echo 'img/product_icon.svg';
                                            } ?>" class="product-icon circle"> </a>
                    <div class="card-content center">
                        <h6> <?php echo htmlspecialchars($product['PDTNAME'] . ' - ' . $product['WEIGHT']); ?> </h6>

                        <?php if ($product['PDTDISCNT'] > 0) { ?>
                            <div class="grey-text">
                                <strike><?php echo htmlspecialchars('$' . number_format($totalPrice, 2, '.', '')); ?></strike>
                                <span class="red-text">
                                    <?php echo htmlspecialchars('-' . $product['PDTDISCNT'] . '%'); ?>
                                </span>
                            </div>
                        <?php } ?>

                        <div class="flow-text"> <?php echo htmlspecialchars('$' . number_format($netPrice, 2, '.', '')); ?> </div>

                        <div class="card-action right-align">
                            <a href="my_favourites.php?remove=<?php echo $product['PDTID']; ?>" class="brand-text">
                                <i class="fa fa-trash" aria-hidden="true"></i> Remove
                            </a>
                            <a href="my_favourites.php?addcart=<?php echo $product['PDTID']; ?>" class="red-text">
                                <i class="fa fa-shopping-cart"></i> Cart
                            </a>
                        </div>
                    </div>
                </div>
            <?php }
                include("templates/pagination_output.php");
            } else { ?>
            <div class="center big-icon">
                <i class="fa fa-heart-o" aria-hidden="true"></i>
            </div>

            <h6 class="center">Your favourite list is empty!</h6>
            <a href="/products/search.php">
                <div class="center">
                    <button class="btn brand z-depth-0 empty-cart-btn">Continue Browsing</button>
                </div>
            </a>
        <?php } ?>
    </div>


    <?php
    include("templates/footer.php");
    ?>

    </html>