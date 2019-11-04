<?php
include("config/db_connect.php");
include('templates/header.php');

// Check for toast message
if (isset($_SESSION['LASTACTION'])) {
    if ($_SESSION['LASTACTION'] == 'REVIEWED') {
        echo "<script>M.toast({html: 'Successfully reviewed product!'});</script>";
    } else if ($_SESSION['LASTACTION'] == 'PAYCONFIRM') {
        echo "<script>M.toast({html: 'Paypal payment successful!'});</script>";
    } else if ($_SESSION['LASTACTION'] == 'PAYCANCEL') {
        echo "<script>M.toast({html: 'Paypal payment cancelled!'});</script>";
    } else if ($_SESSION['LASTACTION'] == 'CONFIRMDELIVERY') {
        echo "<script>M.toast({html: 'Delivery Confirmed!'});</script>";
    }

    $_SESSION['LASTACTION'] = 'NONE';
}

// Pagination for all results
$currDir = "my_orders.php";
$query = "SELECT * FROM orders, product 
WHERE orders.USERID = '$uid' AND product.PDTID = orders.PDTID";
include('templates/pagination_query.php');

// Retrieve orders from order and product table as assoc array
$sql = "SELECT * FROM orders, product 
WHERE orders.USERID = '$uid' AND product.PDTID = orders.PDTID
ORDER BY PCHASEDATE DESC
LIMIT $startingLimit, $resultsPerPage";

$result = mysqli_query($conn, $sql);
$orderList = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Change status of an order entry to 'Confirmed Delivery'
if (isset($_GET['change_status'])) {
    $status = 'Confirmed Delivery';
    $orderId = mysqli_real_escape_string($conn, $_GET['change_status']);
    $sql = "UPDATE orders SET STATUS = '$status' WHERE ORDERID = '$orderId'";
    if (mysqli_query($conn, $sql)) {
        $_SESSION['LASTACTION'] = "CONFIRMDELIVERY";
        echo "<script type='text/javascript'>window.top.location='/my_orders.php';</script>";
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
    <h4 class="center grey-text">My Order History</h4>

    <?php if ($orderList) {
        foreach ($orderList as $order) { ?>
            <div class="col s8 md4">
                <div class="card order-card">
                    <div class="card-content center">
                        <a href="/products/product_details.php?id=<?php echo $order['PDTID']; ?>">
                            <img src="<?php if ($order['IMAGE']) {
                                                    echo $order['IMAGE'];
                                                } else {
                                                    echo 'img/product_icon.svg';
                                                } ?>" class="product-icon">
                            <h6 class="black-text"> <?php echo htmlspecialchars($order['PDTNAME'] . ' - ' . $order['WEIGHT']); ?> </h6>
                        </a>

                        <div class="flow-text"> <?php echo '$' . htmlspecialchars(number_format($order['NETPRICE'], 2, '.', '')); ?> </div>

                        <?php if ($order['PDTDISCNT'] > 0) { ?>
                            <div class="grey-text">
                                <strike><?php echo htmlspecialchars('$' . number_format($order['TTLPRICE'], 2, '.', '')); ?></strike>
                                <span class="discount-label white-text"><?php echo htmlspecialchars('-$' . $order['TTLDISCNTPRICE']); ?></span>
                            </div>
                        <?php } ?>

                        <div> <?php echo htmlspecialchars('Ordered Quantity: ' . $order['ORDERQTY']); ?> </div>

                        <div class="card z-depth-0">
                            <div class="card-content">
                                <ol class="tl">
                                    <li class="element">
                                        <p class="status"><i class="fa fa-credit-card" aria-hidden="true"></i></p>
                                        <?php if ($order['STATUS'] == 'Pending Payment') : ?>
                                            <span class="btn-floating pulse cyan active-point">
                                                <p class="description">Pending payment, please pay via paypal!</p>
                                            </span>
                                        <?php else : ?>
                                            <span class="point"></span>
                                        <?php endif ?>
                                    </li>

                                    <li class="element">
                                        <p class="status"><i class="fa fa-list-ul" aria-hidden="true"></i></p>
                                        <?php if ($order['STATUS'] == 'Confirmed Payment') : ?>
                                            <span class="btn-floating pulse cyan active-point">
                                                <p class="description">Payment is confirmed, we are currently processing your order!</p>
                                            </span>
                                        <?php else : ?>
                                            <span class="point"></span>
                                        <?php endif ?>
                                    </li>

                                    <li class="element">
                                        <p class="status"><i class="fa fa-truck" aria-hidden="true"></i></p>
                                        <?php if ($order['STATUS'] == 'Delivering') : ?>
                                            <span class="btn-floating pulse cyan active-point">
                                                <p class="description">Please be patient, your order is currently being delivered!</p>
                                            </span>
                                        <?php else : ?>
                                            <span class="point"></span>
                                        <?php endif ?>
                                    </li>

                                    <li class="element">
                                        <p class="status"><i class="fa fa-archive" aria-hidden="true" style="margin-left: 3px;"></i></p>
                                        <?php if ($order['STATUS'] == 'Confirmed Delivery') : ?>
                                            <span class="btn-floating pulse cyan active-point">
                                                <p class="description">Delivery is confirmed! Please leave us a feedback!</p>
                                            </span>
                                        <?php else : ?>
                                            <span class="point"></span>
                                        <?php endif ?>
                                    </li>

                                    <li class="element">
                                        <p class="status"><i class="fa fa-pencil-square-o" style="margin-left: 5px;" aria-hidden="true"></i></p>
                                        <?php if ($order['STATUS'] == 'Delivered & Reviewed') : ?>
                                            <span class="btn-floating pulse cyan active-point">
                                                <p class="description">Thank you! We value your feedback!</p>
                                            </span>
                                        <?php else : ?>
                                            <span class="point"></span>
                                        <?php endif ?>
                                    </li>

                                </ol>
                            </div>
                        </div>

                        <div class="card-action right-align">

                            <?php if ($order['STATUS'] == "Confirmed Delivery") { ?>
                                <a href="/products/rating_details.php?id=<?php echo $order['PDTID'] . "&order=" . $order['ORDERID']; ?>" class="btn-small brand" style="width: 200px;">Rate & Review</a>
                            <?php } else if ($order['STATUS'] == "Delivering") { ?>
                                <a href="my_orders.php?change_status=<?php echo $order['ORDERID']; ?>" class="btn-small brand" style="width: 200px;">Confirm Delivery</a>
                            <?php } else if ($order['STATUS'] == "Delivered & Reviewed") { ?>
                                <span class="grey-text">Finished Order</span>
                            <?php } else if ($order['STATUS'] == "Confirmed Payment") { ?>
                                <span class="grey-text">Processing Order</span>
                            <?php } else if ($order['STATUS'] == "Pending Payment") {
                                        // Navigate to payment page
                                        // Product name, quantity, sum price
                                        $payName = $order['PDTNAME'];
                                        $payPrice = number_format($order['NETPRICE'], 2, '.', '');
                                        $payQty = $order['ORDERQTY'];
                                        $tid = $order['TRANSACTIONID'];

                                        $location = "/template_pay.php?price=$payPrice&qty=$payQty&name=$payName&tid=$tid";
                                        ?>
                                <a href="<?php echo $location; ?>" class="btn-small brand" style="width: 200px;">Make Payment</a>
                            <?php } ?>

                            <span class="grey-text left"><?php echo htmlspecialchars('Transaction ID: ' . $order['TRANSACTIONID']); ?></span>
                        </div>
                    </div>
                </div>
            <?php }
                include("templates/pagination_output.php");
            } else { ?>
            <div class="center">
                <img src="img/empty_cart.png" class="empty-cart">
            </div>

            <br>
            <br>
            <br>
            <h6 class="center">Your order history is empty!</h6>
            <a href="/products/search.php">
                <div class="center">
                    <button class="btn brand z-depth-0 empty-cart-btn">Continue Browsing</button>
                </div>
            </a>
        <?php } ?>

        <?php
        include("templates/footer.php");
        ?>

</html>