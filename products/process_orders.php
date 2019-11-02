<?php
include("../templates/header.php");
include("../config/db_connect.php");

// Check that submit button is pressed
if (isset($_POST['submit'])) {
    // Get order id and set status to 'Delivering'
    $oid = $_POST['orderid'];
    $sql = "UPDATE orders SET STATUS='Delivering' WHERE ORDERID='$oid'";
    if (mysqli_query($conn, $sql)) {
        $_SESSION['LASTACTION'] = "DELIVER";
    } else {
        echo "Query Error: " . mysqli_error($conn);
    }
}

if (isset($_SESSION['LASTACTION'])) {
    if ($_SESSION['LASTACTION'] == "DELIVER") {
        echo "<script>M.toast({html: 'Order set to delivering!'});</script>";
    }

    $_SESSION['LASTACTION'] = "NONE";
}

// Pagination for all results
$currDir = "process_orders.php";
$query = "SELECT * FROM orders JOIN product on ORDERS.PDTID = product.PDTID WHERE STATUS='Confirmed Payment'";
include('../templates/pagination_query.php');

// Retrieve orders that are confirmed payment
$sql = "SELECT * FROM orders JOIN product on ORDERS.PDTID = product.PDTID WHERE STATUS='Confirmed Payment'
ORDER BY PCHASEDATE DESC LIMIT $startingLimit, $resultsPerPage";
$result = mysqli_query($conn, $sql);
$orders = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Free memory of result and close connection
mysqli_free_result($result);
mysqli_close($conn);
?>
<!DOCTYPE HTML>
<html>

<body>
    <div class="container">
        <h4 class="center grey-text">Process Orders</h4>
        <?php if ($orders) : ?>
            <div class="row">
                <div class="col s20 m10 offset-m1">
                    <ul class="collection">
                        <?php foreach ($orders as $order) { ?>
                            <li class="collection-item avatar">
                                <a href="product_details.php?id=<?php echo htmlspecialchars($order['PDTID']) ?>">
                                    <img src="<?php if ($order['IMAGE']) {
                                                            echo $order['IMAGE'];
                                                        } else {
                                                            echo 'img/product_icon1.svg';
                                                        } ?>" class="circle">
                                </a>

                                <form class="secondary-content flex no-pad" method="POST">
                                    <input type="hidden" name="orderid" value="<?php echo $order['ORDERID']; ?>" />

                                    <button class="btn-floating btn-large brand waves-effect waves-light z-depth-0" type="submit" name="submit" style="margin-top: 30px;">
                                        <i class="material-icons right">send</i>
                                    </button>
                                </form>

                                <span class="title black-text"><?php echo htmlspecialchars($order['PDTNAME'] . ' ' . $order['WEIGHT']); ?></span>
                                <span class="red-text bold"> <?php echo ' X ' . $order['ORDERQTY']; ?></span>
                                <div class="black-text flow-text" style="margin-top: 10px; margin-bottom: 10px;"><?php echo '$' . $order['NETPRICE']; ?> </div>
                                <div class="green-text"> <?php echo ' ' . strtoupper($order['STATUS']); ?></div>
                                <div class="grey-text"><?php echo htmlspecialchars($order['BRAND']) . ' | ' . htmlspecialchars($order['CATEGORY']); ?></div>


                                <span class="grey-text"><?php echo htmlspecialchars($order['TRANSACTIONID']); ?></span>

                            </li>
                        <?php } ?>

                    </ul>
                    <?php
                        include("../templates/pagination_output.php");
                        ?>
                </div>
            </div>
        <?php else : ?>
            <div class="center">
                <img src="../img/empty_cart.png" class="empty-cart">
            </div>

            <br>
            <br>
            <br>
            <h6 class="center">No orders to process!</h6>
            <a href="/products/inventory_management.php">
                <div class="center">
                    <button class="btn brand z-depth-0 empty-cart-btn">Manage Inventory</button>
                </div>
            </a>
        <?php endif ?>
        <?php include("../templates/footer.php"); ?>

    </div>
</body>


</html>