<?php
include('config/db_connect.php');
include('templates/header.php');
$uid = mysqli_real_escape_string($conn, $_SESSION['U_UID']);

// Pagination for all results
$currDir = "cart.php";
$query = "SELECT * FROM product, cart
WHERE product.PDTID = cart.PDTID AND cart.USERID = '$uid'";
include('templates/pagination_query.php');

$totalPrice = $totalDiscount = $netPrice = 0;
$sumSubTotal = $sumSavings = $sumTotal = $totalQty = 0;
$transactionId = '';

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
        header('Location: cart.php');
    } else {
        echo 'Query Error: ' . mysqli_error($conn);
    }
}

// To checkout all products from cart
if (isset($_POST['checkout']) && $cartList) {
    // Generate a transaction id for the series of orders
    $unique = true;
    do {
        $transactionId = uniqid('ORD', true);
        $sql = "SELECT * FROM orders WHERE TRANSACTIONID = '$transactionId'";
        $result = mysqli_query($conn, $sql);
        $checkResult = mysqli_num_rows($result);
        if ($checkResult > 0) {
            $unique = false;
        }
    } while (!$unique);


    // Add an order entry for each product in cart
    foreach ($cartList as $product) {
        $totalPrice =  mysqli_real_escape_string($conn, $product['PDTPRICE'] * $product['CARTQTY']);
        $totalDiscount = mysqli_real_escape_string($conn, round($totalPrice / 100 * $product['PDTDISCNT'], 2));
        $netPrice = mysqli_real_escape_string($conn, round($totalPrice - $totalDiscount, 2));;
        $pdtId = mysqli_real_escape_string($conn, $product['PDTID']);
        $orderQty = mysqli_real_escape_string($conn, $product['CARTQTY']);
        $deliveryStatus = mysqli_real_escape_string($conn, 'Delivering');
        $deliveryDate = mysqli_real_escape_string($conn, date('Y-m-d', strtotime(date('Y-m-d') . ' + 3 days')));
        $payType = mysqli_real_escape_string($conn, $_POST['payment']);

        // Insert into orders table
        $sql = "INSERT INTO orders(TRANSACTIONID, PDTID, USERID, ORDERQTY, 
        DELVRYSTS, PMENTTYPE, TTLPRICE, TTLDISCNTPRICE, NETPRICE, DELVRYDATE)
        VALUES('$transactionId', '$pdtId', '$uid', '$orderQty', '$deliveryStatus', 
        '$payType', '$totalPrice', '$totalDiscount', '$netPrice', '$deliveryDate')";

        // Check if insert statement returns an error
        if (mysqli_query($conn, $sql)) {
            // Empty cart
            $sql = "DELETE FROM cart WHERE USERID='$uid'";
            if (mysqli_query($conn, $sql)) {
                header('Location: cart.php');
            } else {
                echo 'Query Error: ' . mysqli_error($conn);
            }
        } else {
            echo 'Query Error: ' . mysqli_error($conn);
        }
    }
}

// Free memory of result and close connection
mysqli_free_result($result);
mysqli_close($conn);
?>

<!DOCTYPE html>
<html>

<h4 class="center grey-text">My Shopping Cart</h4>
<div class="container">
    <div class="row">
        <div class="col s14 m8">
            <?php if ($cartList) {
                foreach ($cartList as $product) {
                    $unitPrice = round($product['PDTPRICE'], 2);
                    $unitDiscount = round($unitPrice / 100 * $product['PDTDISCNT'], 2);
                    $netUnit = round($unitPrice - $unitDiscount, 2);

                    $totalPrice = $product['PDTPRICE'] * $product['CARTQTY'];
                    $totalDiscount = round($totalPrice / 100 * $product['PDTDISCNT'], 2);
                    $netPrice = round($totalPrice - $totalDiscount, 2);

                    $sumSubTotal += $totalPrice;
                    $sumSavings += $totalDiscount;
                    $sumTotal += $netPrice;

                    $totalQty += $product['CARTQTY'];
                    ?>

                    <div class="card z-depth-0">
                        <a href="product_details.php?id=<?php echo $product['PDTID']; ?>">
                            <img src="img/product_icon.svg" class="product-icon"> </a>
                        <div class="card-content center">
                            <h6> <?php echo htmlspecialchars($product['PDTNAME']); ?> </h6>
                            <div> <?php echo htmlspecialchars('Unit Price: $' . number_format($netUnit, 2, '.', '')); ?> </div>

                            <?php if ($product['PDTDISCNT'] > 0) { ?>
                                <div class="grey-text">
                                    <strike><?php echo htmlspecialchars('$' . number_format($unitPrice, 2, '.', '')); ?></strike>
                                    <?php echo htmlspecialchars('-' . $product['PDTDISCNT'] . '%'); ?>
                                </div>
                            <?php } ?>

                            <div> <?php echo htmlspecialchars('Quantity: ' . $product['CARTQTY']); ?> </div>
                            <div class="card-action right-align">
                                <a href="cart.php?remove=<?php echo $product['PDTID']; ?>" class="brand-text">Remove</a>
                            </div>
                        </div>
                    </div>
                <?php }
                } else { ?>
                <h4 class="center">Shopping cart is empty!</h4>
            <?php } ?>
        </div>

        <div class="col s6 m3 offset-m1">
            <div class="card z-depth-0">
                <div class="card-content">
                    <h5>Order Summary</h5>
                    <div>Subtotal: $<?php echo htmlspecialchars(number_format($sumSubTotal, 2, '.', '')); ?> </div>
                    <div>Savings: -$<?php echo htmlspecialchars(number_format($sumSavings, 2, '.', '')); ?></div>
                    <div class="divider"></div>
                    <strong>
                        <div>Total: $<?php echo htmlspecialchars(number_format($sumTotal, 2, '.', '')); ?> </div>
                    </strong>

                    <div class="divider"></div>
                    <label>Payment: </label>
                    <select class="browser-default" name="payment" form="checkout">
                        <option value="MasterCard">MasterCard</option>
                        <option value="PayPal">PayPal</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                    </select>

                    <form action="cart.php" method="POST" class="center" id="checkout">
                        <input type="submit" name="checkout" value="Checkout(<?php echo $totalQty; ?>)" class="btn brand z-depth-0" />
                    </form>
                </div>
            </div>
        </div>


    </div>
</div>

<?php
include("templates/pagination_output.php");
include("templates/footer.php");
?>

</html>