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
                <span class="title bold grey-text"><?php echo htmlspecialchars($product['PDTNAME']); ?></span>

                <div class="grey-text"><?php echo htmlspecialchars($product['BRAND']) . ' | ' . htmlspecialchars($product['CATEGORY']); ?></div>
                <div class="grey-text"><?php echo htmlspecialchars('Quantity: ' . htmlspecialchars($product['PDTQTY'])); ?></div>
                <span class="grey-text"><?php echo htmlspecialchars(htmlspecialchars($product['PDTID'])); ?></span>

                <a href="edit_product.php" class="secondary-content brand-text">Edit Product</a>
            </li>
        <?php } ?>

    </ul>
</div>

<?php 
include("templates/pagination_output.php");
include("templates/footer.php"); 
?>

</html>