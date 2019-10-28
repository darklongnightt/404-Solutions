<?php
include("../config/db_connect.php");
include("../templates/header.php");

// Checks if generate button is clicked
if (isset($_GET['generate'])) {
    // Get top 12 most popular products from database
    $sql = "SELECT COUNT(orders.PDTID) AS FREQ, orders.PDTID, product.PDTNAME, product.PDTPRICE, product.PDTDISCNT, product.IMAGE, product.WEIGHT, product.BRAND, product.CATEGORY
    FROM orders JOIN product ON orders.PDTID = product.PDTID
    GROUP BY orders.PDTID ORDER BY FREQ DESC LIMIT 0, 12";
    $result = mysqli_query($conn, $sql);
    $top_products = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // Truncate the top_products table
    $sql = "TRUNCATE TABLE top_products";
    if (!mysqli_query($conn, $sql)) {
        echo 'Query Error: ' . mysqli_error($conn);
    }

    // Insert these products into top_products table
    foreach ($top_products as $product) {
        $PDTNAME = $product['PDTNAME'];
        $PDTPRICE = $product['PDTPRICE'];
        $PDTDISCNT = $product['PDTDISCNT'];
        $PDTID = $product['PDTID'];
        $WEIGHT = $product['WEIGHT'];
        $CATEGORY = $product['CATEGORY'];
        $BRAND = $product['BRAND'];
        $FREQ = $product['FREQ'];
        $IMAGE = $product['IMAGE'];

        $sql = "INSERT INTO top_products(PDTNAME, PDTPRICE, PDTDISCOUNT, PDTID, WEIGHT, CATEGORY, BRAND, FREQUENCY, IMAGE)
        VALUES('$PDTNAME', '$PDTPRICE', '$PDTDISCNT', '$PDTID', '$WEIGHT', '$CATEGORY', '$BRAND', '$FREQ', '$IMAGE')";
        if (!mysqli_query($conn, $sql)) {
            echo 'Query Error: ' . mysqli_error($conn);
        }
    }
}

// Get all top products 
$sql = "SELECT * FROM top_products ORDER BY FREQUENCY DESC";
$result = mysqli_query($conn, $sql);
$products = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Free memory of result and close connection
mysqli_free_result($result);
mysqli_close($conn);

?>

<!DOCTYPE HTML>
<html>
<h4 class="grey-text center">Most Popular Products</h4>
<div class="container center">
    <?php if ($products) : ?>
        <div class="row">
            <div class="col m11 s22">
                <div class="card z-depth-0">
                    <div class="card-content">
                        <table class="striped responsive-table">
                            <thead>
                                <tr>
                                    <?php
                                        echo '<th>Ranking</th>';

                                        echo '<th>Product</th>';

                                        echo '<th>Name</th>';

                                        echo '<th>Price</th>';

                                        echo '<th>Transaction Frequency</th>';
                                        ?>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                    for ($i = 1; $i <= sizeof($products); $i++) {
                                        echo '<tr>';
                                        echo '<td>' . $i . '</td>';
                                        echo '<td><img src="' . $products[$i - 1]['IMAGE'] . '" class="product-icon center"></td>';

                                        echo '<td>' . $products[$i - 1]['PDTNAME'] . '</td>';

                                        echo '<td>' . $products[$i - 1]['PDTPRICE'] . '&nbsp&nbsp';
                                        if ($products[$i - 1]['PDTDISCOUNT'] > 0) {
                                            echo '<label class="white-text discount-label">- ' . $products[$i - 1]['PDTDISCOUNT'] . '% OFF </label>';
                                        }
                                        echo '</td>';

                                        echo '<td>' . $products[$i - 1]['FREQUENCY'] . '</td>';

                                        echo '</tr>';
                                    }
                                    ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


    <?php else : ?>
        <div>No Top Products Generated!</div>
    <?php endif ?>

    <a href="generate_top_products.php?generate=true">
        <button name="submit" class=" btn brand z-depth-0">Refresh Top Products</button>
    </a>

</div>

<?php include("../templates/footer.php"); ?>

</html>