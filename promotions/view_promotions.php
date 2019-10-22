<?php
include("../config/db_connect.php");
include("../templates/header.php");

// Check if delete button is clicked
if (isset($_GET['delete'])) {
    $promotioncode_delete = $_GET['delete'];
    $sql = "DELETE FROM promotion WHERE PROMOCODE='$promotioncode_delete'";
    if (mysqli_query($conn, $sql)) {
        header("Location: view_promotions.php");
    } else {
        echo 'Query Error: ' . mysqli_error($conn);
    }
}

// Pagination for all results
$currDir = "view_promotions.php";
$query = "SELECT DISTINCT PROMOCODE, DISCOUNT, DESCRIPTION FROM promotion";
include('../templates/pagination_query.php');

// Get all distinct promotions
$sql = "SELECT DISTINCT PROMOCODE, DISCOUNT, DESCRIPTION FROM promotion 
LIMIT $startingLimit, $resultsPerPage";
$result = mysqli_query($conn, $sql);
$promotionlist = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Free memory of result and close connection
mysqli_free_result($result);
mysqli_close($conn);

?>

<!DOCTYPE HTML>
<html>
<div class="container">
    <h4 class="center grey-text">View Promotions</h4>

    <?php if ($promotionlist) : ?>
        <ul class="collection">
            <?php foreach ($promotionlist as $promotion) { ?>
                <li class="collection-item avatar">
                    <img src="../img/promotion.png" class="circle">

                    <div class="title"><?php echo htmlspecialchars($promotion['DESCRIPTION']); ?></div>
                    <div>Code: <?php echo htmlspecialchars($promotion['PROMOCODE']); ?></div>
                    <div> <?php echo htmlspecialchars($promotion['DISCOUNT']); ?>% OFF</div>

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
            <img src="../img/promotion.png" class="empty-cart">
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