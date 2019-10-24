<?php
include('config/db_connect.php');
include('templates/header.php');

$name = 'Guest';
$cluster = 0;

// Add an item to cart
function addCart($conn, $id, $qty)
{
    $uid = $GLOBALS['uid'];
    if ($uid) {
        $uid = mysqli_real_escape_string($conn, $uid);
        $id = mysqli_real_escape_string($conn, $id);

        // Check that cart item exists 
        $sql = "SELECT * FROM cart WHERE PDTID='$id' AND USERID='$uid'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            // Increment product qty by 1
            $sql = "UPDATE cart SET CARTQTY=CARTQTY+'$qty' WHERE PDTID='$id' AND USERID='$uid'";
        } else {
            // Add to db cart
            $sql = "INSERT INTO cart(PDTID, USERID, CARTQTY) VALUES('$id', '$uid', '$qty')";
        }

        if (mysqli_query($conn, $sql)) {
            $GLOBALS['message'] = 'Successfully added product to cart!';
        } else {
            echo 'Query Error: ' . mysqli_error($conn);
        }
    }
}

// Checks if recommended cart is clicked
if (isset($_GET['cart'])) {
    addCart($conn, $_GET['cart'], 1);
}

// Checks if user is logged in
if (isset($_SESSION['U_UID'])) {
    $name = $_SESSION['U_FIRSTNAME'] . ' ' . $_SESSION['U_LASTNAME'];
    $cluster = $_SESSION['U_CLUSTER'];
} else {
    $cluster = 1;
}

// Get all recent views for this user
$uid = mysqli_real_escape_string($conn, $uid);
$sql = "SELECT * FROM recent_views JOIN product ON recent_views.PDTID = product.PDTID
WHERE USERID='$uid' ORDER BY VIEWED_AT DESC";
$result = mysqli_query($conn, $sql);
$recent_views = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Get all cluster recommendations for this user
$sql = "SELECT * FROM cluster_recommendation JOIN product ON cluster_recommendation.PDTID = product.PDTID 
WHERE CLUSTER='$cluster' ORDER BY FREQUENCY DESC";
$result = mysqli_query($conn, $sql);
$cluster_recommendations = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Get top 12 most popular products from database
$sql = "SELECT * FROM top_products ORDER BY FREQUENCY DESC";
$result = mysqli_query($conn, $sql);
$top_products = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Select random items from each category
$sql = "SELECT DISTINCT CATEGORY FROM product";
$result = mysqli_query($conn, $sql);
$categories = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Free memory of result and close connection
mysqli_free_result($result);
mysqli_close($conn);
?>

<head>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</head>

<!DOCTYPE HTML>
<html>
<script>
    $(document).ready(function() {
        $('.slider').slider({
            height: 230
        });
    });
</script>

<div class="container">
    <div class="section">
        <div class="row">
            <div class="col s24 m12">
                <div class="slider">
                    <ul class="slides">
                        <li>
                            <img src="/img/banner1.jpg">
                            <div class="caption center-align">
                                <h3 class="black-text bold">Discount Banner 1</h3>
                                <h5 class="light grey-text text-lighten-3">Here's our small slogan.</h5>
                            </div>
                        </li>
                        <li>
                            <img src="/img/banner2.jpg">
                            <div class="caption left-align">
                                <h3 class="black-text bold">Discount Banner 2</h3>
                                <h5 class="light grey-text text-lighten-3">Here's our small slogan.</h5>
                            </div>
                        </li>
                        <li>
                            <img src="/img/banner3.jpg">
                            <div class="caption right-align">
                                <h3 class="black-text bold">Discount Banner 3</h3>
                                <h5 class="light grey-text text-lighten-3">Here's our small slogan.</h5>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col s16 m8">
            <div class="card z-depth-0" style="height: 350px;">
                <br>
                <h6 class="brand-text bold center">Most Popular Products</h6>

                <?php foreach ($top_products as $product) { ?>
                    <a href="/products/product_details.php?id=<?php echo $product['PDTID']; ?>">
                        <span class="img-container" style="margin-left: 10px;">
                            <img src="<?php if ($product['IMAGE']) {
                                                echo $product['IMAGE'];
                                            } else {
                                                echo 'img/product_icon.svg';
                                            } ?>" class="recent-icon">

                            <div class="white-text discount-label">
                                <?php echo '$' . number_format(htmlspecialchars($product['PDTPRICE']) / 100 * htmlspecialchars(100 - $product['PDTDISCOUNT']), 2, '.', ''); ?>
                            </div>
                        </span>
                    </a>
                <?php } ?>
            </div>
        </div>

        <div class="col s8 m4">
            <div class="card z-depth-0" style="height: 350px;">
                <div class="card-content center">
                    <h6 class="white-text bold welcome-label">Welcome, <?php echo $name . '!' ?> </h6>
                </div>
                <div class="bold center">Recently Viewed</div>
                <?php foreach ($recent_views as $product) { ?>
                    <a href="/products/product_details.php?id=<?php echo $product['PDTID']; ?>">
                        <span class="img-container">
                            <img src="<?php if ($product['IMAGE']) {
                                                echo $product['IMAGE'];
                                            } else {
                                                echo 'img/product_icon.svg';
                                            } ?>" class="recent-icon">
                        </span>
                    </a>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="row">
        <h5 class="brand-text bold">&nbsp&nbspShop By Category</h5>
        <?php foreach ($categories as $category) { ?>
            <a href="products/search.php?Filter=<?php echo str_replace(' ', '-', $category['CATEGORY']); ?>&sort=default&priceRange=%240+-+%2410000&check=&searchItem=&submit=Search">
                <div class="col s3 md2">
                    <div class="card z-depth-0 category-card">
                        <img src="img/category/<?php echo $category['CATEGORY'] . '.jpg'; ?>" class="category-icon">

                        <div class="middle">
                            <h5 class="category-text bold"><?php echo $category['CATEGORY']; ?></h5>
                        </div>
                    </div>
                </div>
            </a>

        <?php } ?>
    </div>

    <div class="row"></div>

    <?php if ($uid && $cluster > 0) { ?>
        <div class="row">
            <h5 class="brand-text bold">&nbsp&nbspRecommended For You</h5>
            <?php for ($i = 0; $i < 12; $i++) {
                    $recommendation = $cluster_recommendations[$i];
                    ?>
                <div class="col s3 md2">
                    <a href="/products/product_details.php?id=<?php echo $recommendation['PDTID']; ?>">
                        <div class="card z-depth-0 small">

                            <img src="<?php if ($recommendation['IMAGE']) {
                                                    echo $recommendation['IMAGE'];
                                                } else {
                                                    echo 'img/product_icon.svg';
                                                } ?>" class="product-icon circle">
                            <div class="card-content center">
                                <h6 class="black-text"> <?php echo htmlspecialchars($recommendation['PDTNAME']); ?> <label> <?php echo htmlspecialchars($recommendation['WEIGHT']); ?> </label></h6>
                                <?php if ($recommendation['PDTDISCNT'] > 0) { ?>

                                    <label>
                                        <strike> <?php echo htmlspecialchars('$' . $recommendation['PDTPRICE']); ?> </strike>
                                    </label>
                                    &nbsp
                                    <label class="white-text discount-label">
                                        <?php echo htmlspecialchars('-' . $recommendation['PDTDISCNT'] . '% OFF'); ?>
                                    </label>

                                <?php } ?>


                                <div class="black-text flow-text">
                                    <?php echo '$' . number_format(htmlspecialchars($recommendation['PDTPRICE']) / 100 * htmlspecialchars(100 - $recommendation['PDTDISCNT']), 2, '.', ''); ?>
                                </div>

                    </a>

                    <div class="card-action right-align">
                        <?php if (substr($uid, 0, 3) == 'CUS' || substr($uid, 0, 3) == 'ANO') { ?>
                            <a href="index.php?<?php echo 'cart=' . $recommendation['PDTID']; ?>">
                                <div class="red-text"><i class="fa fa-shopping-cart"></i> Cart</div>
                            </a>
                        <?php } ?>
                    </div>
                </div>
        </div>
</div>
<?php } ?>


</div>
<?php } ?>
</div>

</div>
<?php include("templates/footer.php"); ?>

</html>