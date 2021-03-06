<?php
include('config/db_connect.php');
include('templates/header.php');

if (isset($_SESSION['LASTACTION'])) {
    switch ($_SESSION['LASTACTION']) {
        case 'REMOVECART':
            echo "<script>M.toast({html: 'Successfully removed from cart!'});</script>";
            break;

        case 'LOGIN':
            echo "<script>M.toast({html: 'You are successfully logged in!'});</script>";
            break;

        case 'ADDRESS':
            echo "<script>M.toast({html: 'You are successfully registered!'});</script>";
            break;
    }

    $_SESSION['LASTACTION'] = "NONE";
}

// Pagination for all results
$currDir = "cart.php";
$query = "SELECT * FROM product, cart
WHERE product.PDTID = cart.PDTID AND cart.USERID = '$uid'";
include('templates/pagination_query.php');

$totalPrice = $totalDiscount = $netPrice = $appliedDiscount = 0;
$sumSubTotal = $sumSavings = $sumTotal = $totalQty = 0;
$transactionId = $couponcode = $description = $couponCategory = '';
$errors = array('discountcode' => '');
$coupon = array();

// Init variables for paypal
$payName = '';
$payPrice = $payQty = 0;

// Getting data from table: all elements from product, cartqty from cart associated with the same product
$sql = "SELECT * FROM product, cart
WHERE product.PDTID = cart.PDTID AND cart.USERID = '$uid'
LIMIT $startingLimit, $resultsPerPage";

// Fetch all as assoc array
$result = mysqli_query($conn, $sql);
$cartList = mysqli_fetch_all($result, MYSQLI_ASSOC);

// To remove a single product from cart
if (isset($_GET['remove'])) {
    $removePdt = mysqli_real_escape_string($conn, $_GET['remove']);
    $sql = "DELETE FROM cart WHERE PDTID = '$removePdt' AND USERID = '$uid'";
    if (mysqli_query($conn, $sql)) {
        $_SESSION['LASTACTION'] = 'REMOVECART';
        echo "<script type='text/javascript'>window.top.location='cart.php';</script>";
    } else {
        echo 'Query Error: ' . mysqli_error($conn);
    }
}

if (isset($_POST['applydiscount'])) {
    $couponcode = $_POST['discountcode'];
    $location = "cart.php?discount=$couponcode";
    echo "<script type='text/javascript'>window.top.location='$location';</script>";
}

if (isset($_GET['discount'])) {
    // Getting data from table: coupon AND promotion
    $couponcode = $_GET['discount'];

    $sql = "SELECT * FROM coupon WHERE USERID='$uid' AND COUPONCODE='$couponcode'";
    $result = mysqli_query($conn, $sql);
    $coupon = mysqli_fetch_assoc($result);

    $sql = "SELECT * FROM promotion WHERE PROMOCODE='$couponcode'";
    $result = mysqli_query($conn, $sql);
    $promotion = mysqli_fetch_assoc($result);

    // Check if coupon exists or is already claimed
    if (!$coupon && !$promotion) {
        $errors['discountcode'] = "Invalid code entered!";
    } else if ($coupon) {

        if ($coupon['CLAIMED'] == 'TRUE') {
            $errors['discountcode'] = "Sorry, this coupon is already claimed!";
        }

        // If error free, apply coupon to cart
        if (!array_filter($errors)) {
            $appliedDiscount = $coupon['DISCOUNT'];
            $description = $coupon['DESCRIPTION'];
            $couponCategory = 'ALL';
            echo "<script>M.toast({html: 'Successfully applied discount!'});</script>";
        }
    } else if ($promotion) {

        // If error free, apply coupon to cart
        if (!array_filter($errors)) {
            $appliedDiscount = $promotion['DISCOUNT'];
            $description = $promotion['DESCRIPTION'];
            $couponCategory = $promotion['CATEGORY'];
            echo "<script>M.toast({html: 'Successfully applied discount!'});</script>";
        }
    }
}

// To checkout all products from cart
if (isset($_POST['checkout']) && $cartList) {
    if (substr($uid, 0, 3) !== 'ANO') {
        // Generate a transaction id for the series of orders
        $unique = true;
        do {
            $transactionId = uniqid('ORD');
            $sql = "SELECT * FROM orders WHERE TRANSACTIONID = '$transactionId'";
            $result = mysqli_query($conn, $sql);
            $checkResult = mysqli_num_rows($result);
            if ($checkResult > 0) {
                $unique = false;
            }
        } while (!$unique);

        // Check if coupon exists or is already claimed
        if (mysqli_num_rows($result) < 1) {
            $errors['discountcode'] = "Invalid code entered!";
        } else if ($coupon['CLAIMED'] == 'TRUE') {
            $errors['discountcode'] = "Sorry, this coupon is already claimed!";
        }

        // Add an order entry for each product in cart
        foreach ($cartList as $product) {
            $totalPrice =  mysqli_real_escape_string($conn, $product['PDTPRICE'] * $product['CARTQTY']);
            if ($appliedDiscount > 0) {
                $totalDiscount = round($totalPrice / 100 * $appliedDiscount, 2);
            } else {
                $totalDiscount = round($totalPrice / 100 * $product['PDTDISCNT'], 2);
            }
            $netPrice = mysqli_real_escape_string($conn, round($totalPrice - $totalDiscount, 2));;
            $pdtId = mysqli_real_escape_string($conn, $product['PDTID']);
            $orderQty = mysqli_real_escape_string($conn, $product['CARTQTY']);
            $deliveryDate = mysqli_real_escape_string($conn, date('Y-m-d h:i:s', strtotime(date('Y-m-d h:i:s') . ' + 5 days')));
            $payType = mysqli_real_escape_string($conn, $_POST['payment']);

            // Compute variables needed in paypal page
            $payName .= $product['PDTNAME'] . ', ';
            $payQty += $orderQty;
            $payPrice += $netPrice;

            // Insert into orders table
            $sql = "INSERT INTO orders(TRANSACTIONID, PDTID, USERID, ORDERQTY, PMENTTYPE, TTLPRICE, TTLDISCNTPRICE, NETPRICE, DELVRYDATE)
            VALUES('$transactionId', '$pdtId', '$uid', '$orderQty', '$payType', '$totalPrice', '$totalDiscount', '$netPrice', '$deliveryDate')";

            // Check if insert statement returns an error
            if (mysqli_query($conn, $sql)) {
                // Empty cart
                $sql = "DELETE FROM cart WHERE USERID='$uid'";
                if (mysqli_query($conn, $sql)) {
                    // Update product qty in products table
                    $sql = "UPDATE product SET PDTQTY=PDTQTY-'$orderQty' WHERE PDTID = '$pdtId'";
                    if (!mysqli_query($conn, $sql)) {
                        echo 'Query Error: ' . mysqli_error($conn);
                    }
                } else {
                    echo 'Query Error: ' . mysqli_error($conn);
                }
            } else {
                echo 'Query Error: ' . mysqli_error($conn);
            }
        }

        // Update coupon to be claimed
        if ($coupon) {
            $couponid = $coupon['COUPONID'];
            $sql = "UPDATE coupon SET CLAIMED='TRUE' WHERE COUPONID = '$couponid'";
            if (!mysqli_query($conn, $sql)) {
                echo 'Query Error: ' . mysqli_error($conn);
            }
        }

        // Navigate to payment page
        // Product name, quantity, sum price
        $payName = substr_replace($payName, "", -2);
        $payPrice = number_format($payPrice, 2, '.', '');

        // Calculate hash for payment integrity check using server secret
        $token = hash('sha256', $payName . $payPrice . $payQty . $_SESSION['SERVERSECRET']);

        $location = "/template_pay.php?price=$payPrice&qty=$payQty&name=$payName&tid=$transactionId&token=$token";
        echo "<script type='text/javascript'>window.top.location='$location';</script>";
    } else {
        $_SESSION['LASTPAGE'] = '/cart.php';
        echo "<script type='text/javascript'>window.top.location='/authentication/login.php';</script>";
    }
}

// Free memory of result and close connection
mysqli_free_result($result);
mysqli_close($conn);
?>

    <!DOCTYPE html>
    <html>

    <div class="container">
        <h4 class="center grey-text">My Shopping Cart</h4>
        <div class="row">
            <div class="col s12 m8">
                <?php if ($cartList) {
                    foreach ($cartList as $product) {
                        $unitPrice = round($product['PDTPRICE'], 2);
                        $unitDiscount = round($unitPrice / 100 * $product['PDTDISCNT'], 2);
                        $netUnit = round($unitPrice - $unitDiscount, 2);

                        $totalPrice = $product['PDTPRICE'] * $product['CARTQTY'];

                        if ($appliedDiscount > 0 && ($couponCategory == 'ALL' || $couponCategory == $product['CATEGORY'])) {
                            $totalDiscount = round($totalPrice / 100 * $appliedDiscount, 2);
                        } else {
                            $totalDiscount = round($totalPrice / 100 * $product['PDTDISCNT'], 2);
                        }

                        $netPrice = round($totalPrice - $totalDiscount, 2);

                        $sumSubTotal += $totalPrice;
                        $sumSavings += $totalDiscount;
                        $sumTotal += $netPrice;

                        $totalQty += $product['CARTQTY'];
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

                                <div>
                                    <span class="grey-text">Unit Price: </span>
                                    <span class="flow-text">
                                        <?php echo '$' . number_format($netUnit, 2, '.', ''); ?>
                                    </span>
                                </div>

                                <?php if ($product['PDTDISCNT'] > 0) { ?>
                                    <span class="grey-text">
                                        <strike><?php echo htmlspecialchars('$' . number_format($unitPrice, 2, '.', '')); ?></strike>
                                    </span>
                                    <span class="white-text discount-label"> <?php echo htmlspecialchars(' -' . $product['PDTDISCNT'] . '%'); ?> </span>
                                <?php } ?>

                                <div> <?php echo htmlspecialchars('Quantity: ' . $product['CARTQTY']); ?> </div>
                                <div class="card-action right-align">
                                    <a href="cart.php?remove=<?php echo $product['PDTID']; ?>" class="brand-text">
                                        <i class="fa fa-trash" aria-hidden="true"></i> Remove
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php

                        }
                        include("templates/pagination_output.php");
                    } else { ?>

                    <div class="center big-icon">
                        <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                    </div>
                    <h6 class="center">Your shopping cart is empty!</h6>
                    <a href="/products/search.php">
                        <div class="center">
                            <button class="btn brand z-depth-0 empty-cart-btn">Continue Browsing</button>
                        </div>
                    </a>

                <?php } ?>
            </div>

            <div class="col s12 m3 offset-m1">
                <div class="card z-depth-0 bill-card">
                    <div class="card-content">
                        <h5>Order Summary</h5>
                        <div>Subtotal: $<?php echo htmlspecialchars(number_format($sumSubTotal, 2, '.', '')); ?> </div>

                        <?php if ($sumSavings > 0) { ?>
                            <div class="red-text">Savings: -$
                                <?php echo htmlspecialchars(number_format($sumSavings, 2, '.', '')); ?>
                            </div>
                        <?php } ?>

                        <?php if ($appliedDiscount > 0) { ?>
                            <div class="red-text">
                                <?php echo '"' . htmlspecialchars($description) . '" applied! <br>' . $appliedDiscount . '% OFF ' . $couponCategory; ?>
                            </div>
                        <?php } ?>
                        <div class="divider"></div>

                        <h6 class="bold">Total: $<?php echo htmlspecialchars(number_format($sumTotal, 2, '.', '')); ?> </h6>

                        <div class="divider"></div>
                        <label>Payment: </label>
                        <select class="browser-default" name="payment" form="checkout">
                            <option value="PayPal">PayPal</option>
                        </select>

                        <br>
                        <form action="cart.php<?php if ($appliedDiscount > 0) echo '?discount=' . $couponcode; ?>" method="POST" class="center" id="checkout">
                            <input type="submit" name="checkout" value="Checkout(<?php echo $totalQty; ?>)" class="btn red z-depth-0" style="width: 100%;" />
                        </form>
                    </div>
                </div>

                <div class="card z-depth-0 bill-card">
                    <div class="card-content">
                        <form action="cart.php" method="POST" id="applydiscount">
                            <h6><i class="fa fa-tag" aria-hidden="true"></i>&nbspCoupon:</h6>
                            <input type="text" name="discountcode" placeholder="Discount Code" />
                            <div class="red-text"><?php echo htmlspecialchars($errors['discountcode']); ?></div>

                            <div class="center">
                                <input type="submit" name="applydiscount" value="APPLY" class="btn brand z-depth-0" style="width: 100%;" />
                            </div>
                        </form>

                    </div>
                </div>

            </div>

        </div>
    </div>

    <?php
    include("templates/footer.php");
    ?>

    </html>