<?php
include("config/db_connect.php");
include('templates/header.php');

// Pagination for all results
$currDir = "inventory_management.php";
$query = 'SELECT PDTNAME, CATEGORY, BRAND, PDTID, PDTQTY
FROM product';
include('templates/pagination_query.php');

// Getting data from table: product as associative array
$query = "SELECT PDTNAME, CATEGORY, BRAND, PDTID, PDTQTY
FROM product ORDER BY CREATED_AT DESC 
LIMIT $startingLimit , $resultsPerPage";
$result = mysqli_query($conn, $query);
$productList = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Update product quantity
$updateqty = $pdtid = '';
if (isset($_POST['submit'])) {
    if (!empty($_POST['updateqty'])) {
        $updateqty = mysqli_real_escape_string($conn, $_POST['updateqty']);
        $pdtid = mysqli_real_escape_string($conn, $_POST['updateid']);

        $sql = "UPDATE product SET PDTQTY = '$updateqty' WHERE PDTID = '$pdtid'";
        if (mysqli_query($conn, $sql)) {
            header("Location: inventory_management.php");
        } else {
            echo 'Query Error: ' . mysqli_error($conn);
        }
    }
}

// Free memory of result and close connection
mysqli_free_result($result);
mysqli_close($conn);

?>

<!DOCTYPE HTML>
<html>
<h4 class="center grey-text">Inventory Management</h4>

<div class="container">
    <ul class="collection">

        <?php foreach ($productList as $product) { ?>
            <li class="collection-item avatar">
                <img src="img/product_icon1.svg" alt="" class="circle">
                <form class="secondary-content flex no-pad" method="POST" action="inventory_management.php">
                    <label>Update Quantity: </label>
                    <input type="number" name="updateqty" value="<?php echo htmlspecialchars($product['PDTQTY']); ?>">
                    <input type="hidden" name="updateid" value="<?php echo $product['PDTID']; ?>" />
                    <input type="submit" name="submit" value="update" class="btn-small brand z-depth-0">
                </form>

                <span class="title bold grey-text"><?php echo htmlspecialchars($product['PDTNAME']); ?></span>
                <div class="grey-text"><?php echo htmlspecialchars($product['BRAND']) . ' | ' . htmlspecialchars($product['CATEGORY']); ?></div>
                <div class="grey-text"><?php echo htmlspecialchars('Quantity: ' . htmlspecialchars($product['PDTQTY'])); ?></div>
                <span class="grey-text"><?php echo htmlspecialchars(htmlspecialchars($product['PDTID'])); ?></span>




            </li>
        <?php } ?>

    </ul>
</div>

<?php
include("templates/pagination_output.php");
include("templates/footer.php");
?>

</html>