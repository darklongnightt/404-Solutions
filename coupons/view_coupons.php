<?php
include("../config/db_connect.php");
include("../templates/header.php");

// Access Control Check
if (substr($uid, 0, 3) != 'ADM') {
    echo "<script type='text/javascript'>window.top.location='/index.php';</script>";
}

// Check for toast message
if (isset($_SESSION['LASTACTION'])) {
    if ($_SESSION['LASTACTION'] == 'DELETECOUPON') {
        echo "<script>M.toast({html: 'Successfully deleted coupon!'});</script>";
    }
    $_SESSION['LASTACTION'] = 'NONE';
}

// Check if delete button is clicked
if (isset($_GET['delete'])) {
    $couponcode_delete = $_GET['delete'];
    $sql = "DELETE FROM coupon WHERE COUPONCODE='$couponcode_delete'";
    if (mysqli_query($conn, $sql)) {
        $_SESSION['LASTACTION'] = 'DELETECOUPON';
        echo "<script type='text/javascript'>window.top.location='view_coupons.php';</script>";
    } else {
        echo 'Query Error: ' . mysqli_error($conn);
    }
}

// Pagination for all results
$currDir = "view_coupons.php";
$query = "SELECT DISTINCT COUPONCODE, DISCOUNT, DESCRIPTION FROM coupon";
include('../templates/pagination_query.php');

// Get all distinct coupons
$sql = "SELECT DISTINCT COUPONCODE, DISCOUNT, DESCRIPTION FROM coupon 
LIMIT $startingLimit, $resultsPerPage";
$result = mysqli_query($conn, $sql);
$couponlist = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Compute coupon claimed rate
for ($i = 0; $i < sizeof($couponlist); $i++) {
    $code = $couponlist[$i]['COUPONCODE'];

    // Get number of claimed and not claimed for each distinct coupon
    $sql = "SELECT COUNT(*) FROM coupon WHERE COUPONCODE = '$code' AND CLAIMED='TRUE'";
    $result = mysqli_query($conn, $sql);
    $claimed = mysqli_fetch_assoc($result)['COUNT(*)'];

    $sql = "SELECT COUNT(*) FROM coupon WHERE COUPONCODE = '$code' AND CLAIMED='FALSE'";
    $result = mysqli_query($conn, $sql);
    $notClaimed = mysqli_fetch_assoc($result)['COUNT(*)'];

    $rate = round($claimed / ($claimed + $notClaimed) * 100, 2);
    $couponlist[$i]['RATE'] = $rate;
}

// Free memory of result and close connection
mysqli_free_result($result);
mysqli_close($conn);

?>

<!DOCTYPE HTML>
<html>
<div class="container">
    <h4 class="center grey-text">View Coupons</h4>

    <?php if ($couponlist) : ?>
        <ul class="collection">
            <?php foreach ($couponlist as $coupon) { ?>
                <li class="collection-item avatar">
                    <img src="../img/coupon.png" class="circle">

                    <div class="title bold"><?php echo htmlspecialchars($coupon['DESCRIPTION']); ?></div>
                    <div>Code: <?php echo htmlspecialchars($coupon['COUPONCODE']); ?></div>
                    <div> <?php echo htmlspecialchars($coupon['DISCOUNT']); ?>% OFF</div>

                    <div class="secondary-content flex">
                        <div class="bold">Claim Rate: <?php echo number_format($coupon['RATE'], 2, '.', ''); ?>%</div>
                        <a href="view_coupons.php?delete=<?php echo htmlspecialchars($coupon['COUPONCODE']); ?>">
                            <i class="material-icons black-text">close</i>
                        </a>
                    </div>

                </li>
            <?php } ?>
        </ul>
    <?php
        include("../templates/pagination_output.php");
    else :
        ?>
        <div class="center">
            <img src="../img/coupon.png" class="empty-cart">
        </div>
        <br>
        <br>
        <br>
        <h6 class="center">There is no coupon created!</h6>
        <a href="new_coupon.php">
            <div class="center">
                <button class="btn brand z-depth-0 empty-cart-btn">Create New Coupon</button>
            </div>
        </a>
    <?php endif ?>

</div>

<?php include("../templates/footer.php"); ?>

</html>