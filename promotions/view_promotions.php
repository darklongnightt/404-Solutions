<?php
include("../config/db_connect.php");
include("../templates/header.php");
include('../storage_connect.php');

// Check for toast message
if (isset($_SESSION['LASTACTION'])) {
    if ($_SESSION['LASTACTION'] == 'DELETEPROMO') {
        echo "<script>M.toast({html: 'Successfully deleted promotion!'});</script>";
    }
    $_SESSION['LASTACTION'] = 'NONE';
}

// Check if delete button is clicked
if (isset($_GET['delete'])) {
    $promotioncode_delete = $_GET['delete'];
    $sql = "DELETE FROM promotion WHERE PROMOCODE='$promotioncode_delete'";
    if (mysqli_query($conn, $sql)) {
        $_SESSION['LASTACTION'] = 'DELETEPROMO';
        echo "<script type='text/javascript'>window.top.location='view_promotions.php';</script>";
    } else {
        echo 'Query Error: ' . mysqli_error($conn);
    }
}

// Pagination for all results
$currDir = "view_promotions.php";
$query = "SELECT * FROM promotion";
include('../templates/pagination_query.php');

// Get all distinct promotions
$sql = "SELECT * FROM promotion 
LIMIT $startingLimit, $resultsPerPage";
$result = mysqli_query($conn, $sql);
$promotionlist = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Free memory of result and close connection
mysqli_free_result($result);
mysqli_close($conn);

?>

<head>
    <link rel="stylesheet" href="../css/promotion.css" type="text/css">
</head>

<!DOCTYPE HTML>
<html>
<div class="container">
    <h4 class="center grey-text">View Promotions</h4>

    <?php if ($promotionlist) : ?>
        <ul class="collection">
            <?php foreach ($promotionlist as $promotion) { ?>
                <li class="collection-item avatar">
                    <img src="<?php echo $promotion['IMAGE']; ?>" class="grey-text promo-icon">

                    <div class="promo-text">
                        <div class="bold"><?php echo htmlspecialchars($promotion['DESCRIPTION']); ?></div>
                        <div>Code: <?php echo htmlspecialchars($promotion['PROMOCODE']); ?></div>
                        <div>Category: <?php echo htmlspecialchars($promotion['CATEGORY']); ?> <span class="white-text discount-label"><?php echo ' -' . $promotion['DISCOUNT'] . '% OFF'; ?></span> </div>
                        <div>Expiry: <?php echo htmlspecialchars($promotion['DATETO']); ?></div>
                    </div>

                    <div class="secondary-content flex">
                        <a href="view_promotions.php?delete=<?php echo htmlspecialchars($promotion['PROMOCODE']); ?>">
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
        <h6 class="center">There is no promotion created!</h6>
        <a href="new_promotion.php">
            <div class="center">
                <button class="btn brand z-depth-0 empty-cart-btn">Create New Promotion</button>
            </div>
        </a>
    <?php endif ?>

</div>

<?php include("../templates/footer.php"); ?>

</html>