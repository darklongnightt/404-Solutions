<?php
include("config/db_connect.php");
include('templates/header.php');

$id = $product = $message = '';

// Checks if link contains product id
if (isset($_GET['id'])) {
    // To translate any possible user input before query the db
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $sql = "SELECT * FROM product WHERE PDTID = '$id'";
    $result = mysqli_query($conn, $sql);

    // To fetch the result as a single associative array
    $product = mysqli_fetch_assoc($result);

    // Fetch product recommendations
    $sql = "SELECT * FROM product_recommendation WHERE PDTID = '$id'";
    $result = mysqli_query($conn, $sql);
    $recommendations = explode(' ', mysqli_fetch_assoc($result)['RECOMMENDATIONS']);

    // Fetch as product list
    $recommendation_list = array();
    foreach ($recommendations as $reco) {
        $sql = "SELECT * FROM product WHERE PDTID = '$reco'";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            $recommended_product = mysqli_fetch_assoc($result);
            if ($recommended_product)
                array_push($recommendation_list, $recommended_product);
        } else {
            echo 'Query Error: ' . mysqli_error($conn);
        }
    }
}

// Checks if delete button is clicked
if (isset($_POST['delete'])) {
    $id_to_delete = mysqli_real_escape_string($conn, $_POST['id_to_delete']);
    $sql = "DELETE FROM product WHERE PDTID = '$id_to_delete'";

    // Checks if query is successful
    if (mysqli_query($conn, $sql)) {
        header('Location: index.php');
    } else {
        echo 'Query Error' . mysqli_error($conn);
    }
}

// Checks if add to cart button is clicked
if (isset($_POST['cart'])) {
    if ($_SESSION['U_UID']) {
        $uid = mysqli_real_escape_string($conn, $_SESSION['U_UID']);
        $id = mysqli_real_escape_string($conn, $id);

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
            $message = 'Successfully added product to cart!';
        } else {
            echo 'Query Error: ' . mysqli_error($conn);
        }
    } else {
        // Temporary stores cart items as cookie / session
        // For now redirect to login page
        header('Location: /authentication/login.php');
    }
}

// Checks if add to favourite button is clicked
if (isset($_POST['favourite'])) {
    if ($_SESSION['U_UID']) {
        $uid = mysqli_real_escape_string($conn, $_SESSION['U_UID']);
        $id = mysqli_real_escape_string($conn, $id);

        // Check that fave item exists 
        $sql = "SELECT * FROM favourite WHERE PDTID='$id' AND USERID='$uid'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) < 1) {
            // Add to fave in db
            $sql = "INSERT INTO favourite(PDTID, USERID) VALUES('$id', '$uid')";
        }

        if (mysqli_query($conn, $sql)) {
            $message = 'Successfully added product to favourite!';
        } else {
            echo 'Query Error: ' . mysqli_error($conn);
        }
    } else {
        // Temporary stores cart items as cookie / session
        // For now redirect to login page
        header('Location: /authentication/login.php');
    }
}

mysqli_free_result($result);
mysqli_close($conn);
?>

<html>
<?php if ($product) : ?>
    <div class="container">
        <div class="row">
            <div class="col s6 m3">
                <br>
                <br>
                <img src="img/product_icon.svg">
            </div>
            <div class="col s8 m4 offset-m1">
                <div class="card z-depth-0">
                    <div class="card-content">
                        <h4><?php echo htmlspecialchars($product['PDTNAME']) . ' - ' . htmlspecialchars($product['WEIGHT']); ?></h4>
                        <div class="divider"></div>
                        <h6><?php echo htmlspecialchars($product['DESCRIPTION']); ?></h6>
                        <br>
                        <p><?php echo 'Category: ' . htmlspecialchars($product['CATEGORY']); ?></p>
                        <p><?php echo 'Brand: ' . htmlspecialchars($product['BRAND']); ?></p>
                        <p><?php echo 'Weight: ' . htmlspecialchars($product['WEIGHT']); ?></p>
                        <p><?php echo 'Quantity Available: ' . htmlspecialchars($product['PDTQTY']); ?></p>
                        <p><?php echo 'Production Date: ' . date($product['CREATED_AT']); ?></p>
                    </div>
                </div>


            </div>
            <div class="col s6 m3">
                <div class="card z-depth-0">
                    <div class="card-content">
                        <h5>Price Tag</h5>

                        <?php if ($product['PDTDISCNT'] > 0) { ?>
                            <div> <?php echo htmlspecialchars('Price: $' . $product['PDTPRICE']); ?>
                                <label class="red-text">
                                    <?php echo htmlspecialchars(' -' . $product['PDTDISCNT'] . '% OFF'); ?>
                                </label>
                            </div>
                            <div><?php echo 'Savings: -$' . number_format(htmlspecialchars($product['PDTPRICE']) / 100 * htmlspecialchars($product['PDTDISCNT']), 2, '.', ''); ?></div>
                        <?php } ?>

                        <div class="bold"><?php echo 'Net Price: $' . number_format(htmlspecialchars($product['PDTPRICE']) / 100 * (100 - htmlspecialchars($product['PDTDISCNT'])), 2, '.', ''); ?></div>

                        <div class="divider"></div>
                        <br>

                        <form action="product_details.php?id=<?php echo $id; ?>" method="POST">
                            <input type="hidden" name="id_to_delete" value="<?php echo $product['PDTID']; ?>" />
                            <?php if ($uid) { ?>
                                <?php if (substr($uid, 0, 3) == 'CUS') { ?>
                                    <input type="submit" name="cart" value="+cart" class="btn orange z-depth-0" />
                                    <input type="submit" name="favourite" value="+favourite" class="btn red z-depth-0" />
                                <?php } else if (substr($uid, 0, 3) == 'ADM') { ?>
                                    <input type="submit" name="delete" value="delete" class="btn brand z-depth-0" />
                                <?php } ?>
                            <?php } ?>
                        </form>

                    </div>
                </div>
            </div>
        </div>

        <div class="row center grey-text">
            <?php echo $message; ?>
        </div>

        <div class="row center">
            <h6>People Who Bought This Also Bought</h6>
            <?php foreach ($recommendation_list as $recommendation) { ?>
                <div class="col s2 md1">
                    <div class="card z-depth-0 small">
                        <img src="img/product_icon.svg" class="product-icon">
                        <div class="card-content center">
                            <h6> <?php echo htmlspecialchars($recommendation['PDTNAME']); ?> <label> <?php echo htmlspecialchars($product['WEIGHT']); ?> </label></h6>

                            <?php if ($recommendation['PDTDISCNT'] > 0) { ?>
                                <label> <?php echo htmlspecialchars('$' . $recommendation['PDTPRICE']); ?> </label>
                                <label class="red-text">
                                    <?php echo htmlspecialchars(' -' . $recommendation['PDTDISCNT'] . '% OFF'); ?>
                                </label>

                            <?php } ?>

                            <div class="black-text"><?php echo '$' . number_format(htmlspecialchars($recommendation['PDTPRICE']) / 100 * htmlspecialchars(100 - $product['PDTDISCNT']), 2, '.', ''); ?></div>

                            <div class="card-action right-align">
                                <a href="product_details.php?id=<?php echo $recommendation['PDTID']; ?>" class="brand-text">more info</a>
                            </div>

                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
<?php else : ?>
    <h4 class="center">Error 404: No such product exists!</h4>
<?php endif; ?>

<?php include("templates/footer.php"); ?>

</html>