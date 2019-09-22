<?php
include("config/db_connect.php");
include('templates/header.php');

$id = $product = '';

// Checks if link contains product id
if (isset($_GET['id'])) {
    // To translate any possible user input before query the db
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $sql = "SELECT * FROM product WHERE PDTID = '$id'";
    $result = mysqli_query($conn, $sql);

    // To fetch the result as a single associative array
    $product = mysqli_fetch_assoc($result);
}

// Checks if delete button is clicked
if (isset($_POST['delete'])) {
    $id_to_delete = mysqli_real_escape_string($conn, $_POST['id_to_delete']);
    $sql = "DELETE FROM product WHERE PDTID = $id_to_delete";

    // Checks if query is successful
    if (mysqli_query($conn, $sql)) {
        header('Location: index.php');
    } else {
        echo 'Query Error' . mysqli_error($conn);
    }
}

// Checks if add to cart button is clicked
if (isset($_POST['cart'])) {
    if ($_SESSION['U_UID']) {
        $uid = mysqli_real_escape_string($conn, $_SESSION['U_UID']);
        $id = mysqli_real_escape_string($conn, $id);

        // Check that cart item exists 
        $sql = "SELECT * FROM cart WHERE PDTID='$id' AND USERID='$uid'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            // Increment product qty by 1
            $sql = "UPDATE cart SET CARTQTY=CARTQTY+1 WHERE PDTID='$id' AND USERID='$uid'";
        } else {
            // Add to db cart with qty of 1
            $sql = "INSERT INTO cart(PDTID, USERID, CARTQTY) VALUES('$id', '$uid', '1')";
        }

        if (mysqli_query($conn, $sql)) {
            echo 'Successfully added product to cart!';
        } else {
            echo 'Query Error: ' . mysqli_error($conn);
        }
    } else {
        // Temporary stores cart items as cookie / session
        // For now redirect to login page
        header('Location: /authentication/login.php');
    }
}

// Checks if add to favourite button is clicked
if (isset($_POST['favourite'])) {
    if ($_SESSION['U_UID']) {
        $uid = mysqli_real_escape_string($conn, $_SESSION['U_UID']);
        $id = mysqli_real_escape_string($conn, $id);

        // Check that fave item exists 
        $sql = "SELECT * FROM favourite WHERE PDTID='$id' AND USERID='$uid'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) < 1) {
            // Add to fave in db
            $sql = "INSERT INTO favourite(PDTID, USERID) VALUES('$id', '$uid')";
        }

        if (mysqli_query($conn, $sql)) {
            echo 'Successfully added product to favourite!';
        } else {
            echo 'Query Error: ' . mysqli_error($conn);
        }
    } else {
        // Temporary stores cart items as cookie / session
        // For now redirect to login page
        header('Location: /authentication/login.php');
    }
}

mysqli_free_result($result);
mysqli_close($conn);
?>

<html>
<?php if ($product) : ?>
    <div class="container center">
        <h4><?php echo htmlspecialchars($product['PDTNAME']) . ' - ' . htmlspecialchars($product['WEIGHT']); ?></h4>
        <p><?php echo 'Description: ' . htmlspecialchars($product['DESCRIPTION']); ?></p>
        <p><?php echo 'Category: ' . htmlspecialchars($product['CATEGORY']); ?></p>
        <p><?php echo 'Brand: ' . htmlspecialchars($product['BRAND']); ?></p>

        <p><?php echo 'Product Price: $' . htmlspecialchars($product['PDTPRICE']); ?></p>
        <p><?php echo 'Cost Price: $' . htmlspecialchars($product['CSTPRICE']); ?></p>
        <p><?php echo 'Product Discount: ' . htmlspecialchars($product['PDTDISCNT']) . '%'; ?></p>
        <p><?php echo 'Discounted Price: $' . round(htmlspecialchars($product['PDTPRICE']) / 100 * (100 - htmlspecialchars($product['PDTDISCNT'])), 2); ?></p>
        <p><?php echo 'Listed At: ' . date($product['CREATED_AT']); ?></p>

        <form action="product_details.php?id=<?php echo $id; ?>" method="POST">
            <input type="hidden" name="id_to_delete" value="<?php echo $product['PDTID']; ?>" />

            <?php if ($uid) { ?>
                <?php if (substr($uid, 0, 3) == 'CUS') { ?>
                    <input type="submit" name="cart" value="+cart" class="btn brand z-depth-0" />
                    <input type="submit" name="favourite" value="+favourite" class="btn brand z-depth-0" />
                <?php } else if (substr($uid, 0, 3) == 'ADM') { ?>
                    <input type="submit" name="delete" value="delete" class="btn brand z-depth-0" />
                <?php } ?>
            <?php } ?>
        </form>
    </div>
<?php else : ?>
    <h4 class="center">Error: No such product exists!</h4>
<?php endif; ?>

<?php include("templates/footer.php"); ?>

</html>