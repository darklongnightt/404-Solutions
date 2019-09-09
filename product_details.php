<?php
include("config/db_connect.php");

// Checks if link contains product id
if (isset($_GET['id'])) {
    // To translate any possible user input before query the db
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $sql = "SELECT * FROM product WHERE PDTID = $id";
    $result = mysqli_query($conn, $sql);

    // To fetch the result as a single associative array
    $product = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    mysqli_close($conn);
}

// Checks if delete button is clicked
if (isset($_POST['delete'])) {
    $id_to_delete = mysqli_real_escape_string($conn, $_POST['id_to_delete']);
    $sql = "DELETE FROM product WHERE PDTID = $id_to_delete";

    // Checks if query is successful
    if(mysqli_query($conn, $sql)) {
        header('Location: index.php');
    } else {
        echo 'Query Error' . mysqli_error($conn);
    }
}
?>

<html>
<?php include("templates/header.php"); ?>
<?php if ($product) : ?>
    <div class="container center">
        <h4><?php echo htmlspecialchars($product['PDTNAME']); ?></h4>
        <p><?php echo 'Description: ' . htmlspecialchars($product['DESCRIPTION']); ?></p>
        <p><?php echo 'Category: ' . htmlspecialchars($product['CATEGORY']); ?></p>
        <p><?php echo 'Brand: ' . htmlspecialchars($product['BRAND']); ?></p>


        <p><?php echo 'Product Price: $' . htmlspecialchars($product['PDTPRICE']); ?></p>
        <p><?php echo 'Cost Price: $' . htmlspecialchars($product['CSTPRICE']); ?></p>
        <p><?php echo 'Product Discount: ' . htmlspecialchars($product['PDTDISCNT']) . '%'; ?></p>
        <p><?php echo 'Discounted Price: $' . round(htmlspecialchars($product['PDTPRICE']) / 100 * (100 - htmlspecialchars($product['PDTDISCNT'])), 2); ?></p>
        <p><?php echo 'Listed At: ' . date($product['CREATED_AT']); ?></p>

        <form action="product_details.php" method="POST">
            <input type="hidden" name="id_to_delete" value="<?php echo $product['PDTID']; ?>" />
            <input type="submit" name="delete" value="delete" class="btn brand z-depth-0" />
        </form>
    </div>
<?php else : ?>
    <h4 class="center">Error: No such product exists!</h4>
<?php endif; ?>

<?php include("templates/footer.php"); ?>

</html>