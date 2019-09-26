<?php
include("config/db_connect.php");
include('templates/header.php');
$uid = mysqli_real_escape_string($conn, $_SESSION['U_UID']);

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

// Change status of an order entry to delivered
if (isset($_GET['change_status'])) {
    $status = mysqli_real_escape_string($conn, 'Delivered');
    $orderId = mysqli_real_escape_string($conn, $_GET['change_status']);
    $sql = "UPDATE orders SET DELVRYSTS = '$status' WHERE ORDERID = '$orderId'";
    if (mysqli_query($conn, $sql)) {
        header("Location: my_orders.php");
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

<h4 class="center grey-text">My Orders</h4>
<div class="container">
    <?php if ($orderList) {
        foreach ($orderList as $order) { ?>
            <div class="col s8 md4">
                <div class="card z-depth-0">
                    <div class="card-content center">
                        <a href="product_details.php?id=<?php echo $order['PDTID']; ?>">
                            <h6 class="black-text"> <?php echo htmlspecialchars($order['PDTNAME'] . ' - ' . $order['WEIGHT']); ?> </h6>
                        </a>

                        <div> <?php echo htmlspecialchars('Net Total Price: $' . number_format($order['NETPRICE'], 2, '.', '')); ?> </div>

                        <?php if ($order['PDTDISCNT'] > 0) { ?>
                            <div class="grey-text">
                                <strike><?php echo htmlspecialchars('$' . number_format($order['TTLPRICE'], 2, '.', '')); ?></strike>
                                <?php echo htmlspecialchars('-' . $order['TTLDISCNTPRICE'] . '%'); ?>
                            </div>
                        <?php } ?>

                        <div> <?php echo htmlspecialchars('Ordered Quantity: ' . $order['ORDERQTY']); ?> </div>

                        <?php if ($order['DELVRYSTS'] == "Delivered") { ?>
                            <strong>
                                <span class="green-text lighten-2"><?php echo htmlspecialchars($order['DELVRYSTS']); ?></span>
                            </strong>
                        <?php } else { ?>
                            <strong>
                                <span class="orange-text lighten"><?php echo htmlspecialchars($order['DELVRYSTS']); ?></span>
                            </strong>
                        <?php } ?>

                        <div class="card-action right-align">
                            <?php if ($order['DELVRYSTS'] == "Delivered") { ?>
                            <strong>
                                <span class="green-text lighten-2">
                                    <a href="rating_details.php?id=<?php echo $order['ORDERID']; ?>"class="brand-text">Rate & Review</a>
                                </span>
                            </strong>
                            <?php }?>
                            <span class="brand-text left"><?php echo htmlspecialchars('Transaction ID: ' . $order['TRANSACTIONID']); ?></span>
                            <a href="my_orders.php?change_status=<?php echo $order['ORDERID']; ?>" class="brand-text">Confirm Delivery</a>
                        </div>
                    </div>
                </div>
            <?php }
            } else { ?>
            <h4 class="center">Order list is empty!</h4>
        <?php } ?>

        <?php 
        include("templates/pagination_output.php");
        include("templates/footer.php"); 
        ?>

</html>