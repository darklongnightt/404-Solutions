<?php
include("../config/db_connect.php");
include('../templates/header.php');
if (substr($uid, 0, 3) == 'ADM')
    include('../storage_connect.php');

$id = $product = $message = '';
$count = $cartQty = 0;
$more = FALSE;

// Update recent views of specific customer
function updateRecentView($uid, $pid, $conn)
{
    $uid = mysqli_real_escape_string($conn, $uid);

    // Check if product is already in recent views
    $sql = "SELECT * FROM recent_views WHERE USERID='$uid' AND PDTID='$pid'";
    $result = mysqli_query($conn, $sql);
    $checkUnique = mysqli_num_rows($result);

    if ($checkUnique < 1) {
        $sql = "SELECT * FROM recent_views WHERE USERID='$uid'";
        $result = mysqli_query($conn, $sql);
        $num = mysqli_num_rows($result);

        // Only keeps top 6 rows per customer by either insert or update
        if ($num > 5) {
            $sql = "UPDATE recent_views SET VIEWED_AT=CURRENT_TIMESTAMP, PDTID='$pid' 
                WHERE USERID='$uid' AND VIEWED_AT=(SELECT MIN(VIEWED_AT) FROM recent_views WHERE USERID='$uid');";
        } else {
            $sql = "INSERT INTO recent_views(USERID, PDTID) VALUES('$uid', '$pid')";
        }

        // Execute the query
        if (!mysqli_query($conn, $sql)) {
            echo 'Query Error: ' . mysqli_error($conn);
        }
    }
}

// Add an item to cart
function addCart($conn, $id, $qty)
{
    $uid = $GLOBALS['uid'];
    if ($uid) {
        $uid = mysqli_real_escape_string($conn, $uid);
        $id = mysqli_real_escape_string($conn, $id);

        // Check that cart item exists 
        $sql = "SELECT * FROM cart WHERE PDTID='$id' AND USERID='$uid'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            // Increment product qty by 1
            $sql = "UPDATE cart SET CARTQTY=CARTQTY+'$qty' WHERE PDTID='$id' AND USERID='$uid'";
        } else {
            // Add to db cart
            $sql = "INSERT INTO cart(PDTID, USERID, CARTQTY) VALUES('$id', '$uid', '$qty')";
        }

        if (mysqli_query($conn, $sql)) {
            $GLOBALS['message'] = 'Successfully added product to cart!';
        } else {
            echo 'Query Error: ' . mysqli_error($conn);
        }
    }
}

// Checks if link contains product id
if (isset($_GET['id'])) {
    // To translate any possible user input before query the db
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // Update recent views table
    updateRecentView($uid, $id, $conn);

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

    // Checks if more recommendation is to be shown
    if (isset($_GET['limit'])) {
        $limit = $_GET['limit'];
    } else {
        $limit = 4;
        if ($limit < count($recommendation_list)) {
            $more = TRUE;
        }
    }
}

// Checks if delete button is clicked
if (isset($_POST['delete'])) {
    $product_id = mysqli_real_escape_string($conn, $_POST['product_id']);
    $sql = "DELETE FROM product WHERE PDTID = '$product_id'";

    // Also delete from cloud storage
    $fileName = explode("/", $product['IMAGE'])[4];
    $fileName = explode("?", $fileName)[0];
    delete_object($bucketName, $fileName);

    // Checks if query is successful
    if (mysqli_query($conn, $sql)) {
        echo "<script type='text/javascript'>window.top.location='/index.php';</script>";
    } else {
        echo 'Query Error' . mysqli_error($conn);
    }
}

// Checks if add to cart button is clicked
if (isset($_POST['cart'])) {
    if (empty($_POST['cartQty'])) {
        $cartQty = 1;
    } else {
        $cartQty = $_POST['cartQty'];
    }

    addCart($conn, $_POST['product_id'], $cartQty);
}

// Checks if recommended cart is clicked
if (isset($_GET['cart'])) {
    addCart($conn, $_GET['cart'], 1);
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
        // Redirect to login page
        echo "<script type='text/javascript'>window.top.location='/authentication/login.php';</script>";
    }
}

mysqli_free_result($result);
mysqli_close($conn);
?>

<style>
    .main-image {
        width: auto;
        max-height: 325px;
        margin: 25px;
    }

    .main-card {
        height: 400px;
    }
</style>

<html>
<?php if ($product) : ?>
    <div class="container">
        <div class="row">
            <div class="col s6 m3">
                <br>
                <div class="center">
                    <img src="<?php if ($product['IMAGE']) {
                                        echo $product['IMAGE'];
                                    } else {
                                        echo '/img/product_icon.svg';
                                    } ?>" class="grey main-image">
                </div>
            </div>
            <div class="col s8 m4 offset-m1">
                <div class="card z-depth-0 main-card">
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
                        <label><?php echo htmlspecialchars($product['PDTID']); ?></label>
                    </div>
                </div>


            </div>
            <div class="col s6 m3">
                <div class="card z-depth-0 main-card">
                    <div class="card-content">
                        <h5><img src="/img/price_tag.svg" class="tag-icon"> Price Tag</h5>
                        <br>
                        <div class="divider"></div>

                        <?php if ($product['PDTDISCNT'] > 0) { ?>
                            <div> <?php echo htmlspecialchars('Price: $' . $product['PDTPRICE']); ?>
                                <label class="white-text discount-label">
                                    <?php echo htmlspecialchars(' -' . $product['PDTDISCNT'] . '% OFF'); ?>
                                </label>
                            </div>
                            <div><?php echo 'Savings: -$' . number_format(htmlspecialchars($product['PDTPRICE']) / 100 * htmlspecialchars($product['PDTDISCNT']), 2, '.', ''); ?></div>
                        <?php } ?>

                        <div class="bold"><?php echo 'Net Price: $' . number_format(htmlspecialchars($product['PDTPRICE']) / 100 * (100 - htmlspecialchars($product['PDTDISCNT'])), 2, '.', ''); ?></div>

                        <div class="divider"></div>
                        <br>

                        <form action="product_details.php?id=<?php echo $id; ?>" method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $product['PDTID']; ?>" />

                            <label>Quantity: </label>
                            <select class="browser-default" name="cartQty">
                                <?php for ($i = 1; $i <= 10 && $i <= $product['PDTQTY']; $i++) { ?>
                                    <option value="<?php echo $i; ?>"> <?php echo $i; ?> </option>
                                <?php } ?>
                            </select>

                            <br>

                            <?php if (substr($uid, 0, 3) == 'CUS' || substr($uid, 0, 3) == 'ANO') { ?>
                                <button type="submit" name="cart" class="btn orange z-depth-0" style="width: 90px;">
                                    <i class='fa fa-cart-plus' aria-hidden='true'></i> Cart
                                </button>
                                <span>&nbsp</span>
                                <button type="submit" name="favourite" class="btn red z-depth-0" style="width: 90px;">
                                    <i class="fa fa-heart" aria-hidden="true"></i>
                                </button>
                            <?php } else if (substr($uid, 0, 3) == 'ADM') { ?>
                                <button type="submit" name="delete" class="btn brand z-depth-0" style="width: 195px;">
                                    <i class="fa fa-trash-o" aria-hidden="true"></i> Delete
                                </button>
                            <?php } ?>

                        </form>

                    </div>
                </div>
            </div>
        </div>

        <div class="row center grey-text">
            <?php echo $message; ?>
        </div>

        <?php if ($recommendation_list) { ?>
            <div class="row">
                <h6 class="left">&nbsp&nbsp <u>People Who Bought This Also Bought</u></h6>
                <?php if ($more) { ?>
                    <a href="product_details.php?id=<?php echo $id . '&limit=' . count($recommendation_list); ?>">
                        <li class="waves-effect right red-text">
                            <i class="material-icons">chevron_right</i>
                        </li>
                        <li class="waves-effect right red-text">See All </li>
                    </a>
                <?php } ?>
            </div>
        <?php } ?>

        <div class="row center">
            <?php foreach ($recommendation_list as $recommendation) {
                    $count += 1;
                    if ($count > $limit) {
                        break;
                    }
                    ?>
                <div class="col s3 md2">
                    <a href="product_details.php?id=<?php echo $recommendation['PDTID']; ?>">
                        <div class="card z-depth-0 small">

                            <img src="<?php if ($recommendation['IMAGE']) {
                                                    echo $recommendation['IMAGE'];
                                                } else {
                                                    echo 'img/product_icon.svg';
                                                } ?>" class="product-icon">
                            <div class="card-content center">
                                <h6 class="black-text"> <?php echo htmlspecialchars($recommendation['PDTNAME']); ?> <label> <?php echo htmlspecialchars($recommendation['WEIGHT']); ?> </label></h6>
                                <?php if ($recommendation['PDTDISCNT'] > 0) { ?>

                                    <label>
                                        <strike> <?php echo htmlspecialchars('$' . $recommendation['PDTPRICE']); ?> </strike>
                                        &nbsp
                                    </label>
                                    <label class="white-text discount-label">
                                        <?php echo htmlspecialchars('-' . $recommendation['PDTDISCNT'] . '% OFF'); ?>
                                    </label>

                                <?php } ?>


                                <div class="black-text flow-text">
                                    <?php echo '$' . number_format(htmlspecialchars($recommendation['PDTPRICE']) / 100 * htmlspecialchars(100 - $recommendation['PDTDISCNT']), 2, '.', ''); ?>
                                </div>

                    </a>
                    <?php if (substr($uid, 0, 3) == 'CUS' || substr($uid, 0, 3) == 'ANO') { ?>

                        <div class="card-action right-align">
                            <a href="product_details.php?id=<?php echo $product['PDTID'] . '&cart=' . $recommendation['PDTID']; ?>">
                                <div class="red-text"><i class="fa fa-shopping-cart"></i> Cart</div>
                            </a>
                        </div>

                    <?php } ?>
                </div>
        </div>
    </div>
<?php } ?>
</div>
</div>
<?php else : ?>
    <h4 class="center">Error 404: No such product exists!</h4>
<?php endif; ?>

<?php include("../templates/footer.php"); ?>

</html>